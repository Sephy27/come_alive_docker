<#
.SYNOPSIS
  Déploiement/rollback Symfony (prod) – Windows/PowerShell + AssetMapper.

.DESCRIPTION
  - Action "deploy" : pull git, composer install --no-dev, migrations, asset-map:compile,
    création d'un dossier release horodaté, bascule de "current" -> nouvelle release, tag git optionnel.
  - Action "rollback" : revient à la release précédente (sans toucher la BDD).

.PARAMETERS
  -ProjectPath   : racine du projet (composer.json)
  -PhpPath       : chemin vers php.exe (si pas dans PATH)
  -ComposerPath  : chemin vers composer (si pas dans PATH)
  -GitBranch     : branche à déployer
  -Tag           : crée un tag git (ex: v1.2.3) au moment du déploiement
  -Action        : "deploy" (défaut) | "rollback"
  -KeepReleases  : nombre de releases à conserver (défaut: 5)

.NOTES
  Arborescence (après 1er déploiement) :
    project/
      releases/
        2025-11-02_21-05-30/   <-- release horodatée
        ...
      current -> releases/2025-11-02_21-05-30   (symlink/liaison)
#>

param(
  [string]$ProjectPath  = "D:\projets\come_alive\app",
  [string]$PhpPath      = "php",
  [string]$ComposerPath = "composer",
  [string]$GitBranch    = "main",
  [string]$Tag          = "",
  [ValidateSet("deploy","rollback")] [string]$Action = "deploy",
  [int]$KeepReleases = 5
)

$ErrorActionPreference = "Stop"

function Info($m){ Write-Host "• $m" -ForegroundColor Cyan }
function Ok($m){ Write-Host "✔ $m" -ForegroundColor Green }
function Warn($m){ Write-Host "! $m" -ForegroundColor Yellow }
function Err($m){ Write-Host "✖ $m" -ForegroundColor Red }

if (!(Test-Path $ProjectPath)){ Err "ProjectPath introuvable: $ProjectPath"; exit 1 }
Set-Location $ProjectPath

$releasesDir = Join-Path $ProjectPath "releases"
$currentLink = Join-Path $ProjectPath "current"

# Utilitaires
function Ensure-Dir($path){ if (!(Test-Path $path)){ New-Item -ItemType Directory -Path $path | Out-Null } }
function Resolve-Releases(){
  if (!(Test-Path $releasesDir)){ @() ; return }
  Get-ChildItem -Path $releasesDir -Directory | Sort-Object Name
}

function New-Symlink($link,$target){
  # Sur Windows, New-Item -ItemType SymbolicLink nécessite élévation. On tente, sinon copie fallback.
  try{
    if (Test-Path $link){ Remove-Item -Force $link }
    New-Item -ItemType SymbolicLink -Path $link -Target $target | Out-Null
    return $true
  } catch {
    Warn "Symbolic link impossible (droits). Fallback: copie du webroot (public/) vers current."
    if (Test-Path $link){ Remove-Item -Recurse -Force $link }
    Copy-Item -Recurse -Force $target $link
    return $false
  }
}

switch ($Action) {
  "rollback" {
    Info "Rollback vers la release précédente…"
    $releases = Resolve-Releases
    if ($releases.Count -lt 2){ Err "Pas de release précédente trouvée."; exit 1 }
    $prev = $releases[$releases.Count - 2]
    $ok = New-Symlink -link $currentLink -target $prev.FullName
    Ok ("Rollback vers: " + $prev.Name + ( $ok ? " (symlink)" : " (copie)" ))
    exit 0
  }

  "deploy" {
    # 1) Prépare dossiers
    Ensure-Dir $releasesDir
    $stamp = (Get-Date).ToString("yyyy-MM-dd_HH-mm-ss")
    $releasePath = Join-Path $releasesDir $stamp
    Ensure-Dir $releasePath

    # 2) Git up-to-date (dans le repo principal)
    if (Test-Path ".git") {
      Info "Récupération du code (branche $GitBranch)…"
      git fetch origin
      git checkout $GitBranch
      git pull origin $GitBranch --rebase
      Ok "Code à jour"
    } else {
      Warn "Pas de repo git détecté — on déploie l'état local"
    }

    # 3) Copier sources -> dossier release (hors vendor/var/public/assets)
    Info "Copie du code vers la release: $stamp"
    robocopy . $releasePath /MIR /XD ".git" "var" "vendor" "public\assets" | Out-Null
    # Robocopy code 1 = "copied some files" -> OK
    Ok "Code copié"

    # 4) Composer install (prod) DANS la release
    Set-Location $releasePath
    Info "Composer install (prod)…"
    & $ComposerPath install --no-dev --optimize-autoloader --prefer-dist
    Ok "Composer OK"

    # 5) APP_ENV=prod
    Info "APP_ENV=prod (process)"
    [System.Environment]::SetEnvironmentVariable("APP_ENV","prod","Process")
    $env:APP_ENV = "prod"

    # 6) Migrations
    Info "Migrations Doctrine…"
    & $PhpPath bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
    Ok "Migrations OK"

    # 7) Assets (AssetMapper) – en prod ON COMPILE !
    Info "Compilation assets (AssetMapper)…"
    if (Test-Path "public\assets") {
      attrib -r -h -s .\public\assets\* /s 2>$null | Out-Null
      Remove-Item -Recurse -Force .\public\assets
    }
    & $PhpPath bin/console asset-map:compile
    Ok "Assets compilés"

    # 8) Cache prod
    Info "Cache: clear + warmup (prod)…"
    & $PhpPath bin/console cache:clear --env=prod
    & $PhpPath bin/console cache:warmup --env=prod
    Ok "Cache prod OK"

    # 9) Bascule "current" vers nouvelle release
    Set-Location $ProjectPath
    $ok = New-Symlink -link $currentLink -target $releasePath
    Ok ("Bascule vers release " + $stamp + ( $ok ? " (symlink)" : " (copie)" ))

    # 10) Tag git optionnel
    if ($Tag -ne "") {
      try {
        Info "Création du tag git: $Tag"
        git tag $Tag
        git push origin $Tag
        Ok "Tag $Tag poussé"
      } catch {
        Warn "Impossible de créer/pousser le tag: $Tag ($($_.Exception.Message))"
      }
    }

    # 11) Keep last N releases
    $releases = Resolve-Releases
    if ($releases.Count -gt $KeepReleases){
      $toRemove = $releases | Select-Object -First ($releases.Count - $KeepReleases)
      foreach($r in $toRemove){
        Info "Suppression ancienne release: $($r.Name)"
        Remove-Item -Recurse -Force $r.FullName
      }
      Ok "Rotation des releases OK (garde $KeepReleases)"
    }

    Ok "Déploiement terminé 🎉"
  }
}
