import './bootstrap.js';

// Back-to-top: safe + perf-friendly
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.querySelector('.back-to-top');
  if (!btn) return;

  // état initial
  btn.style.display = window.scrollY > 300 ? 'flex' : 'none';

  // écouteur scroll (passive)
  window.addEventListener(
    'scroll',
    () => {
      // micro-throttle via requestAnimationFrame
      if (btn.__raf__) return;
      btn.__raf__ = requestAnimationFrame(() => {
        btn.style.display = window.scrollY > 300 ? 'flex' : 'none';
        btn.__raf__ = null;
      });
    },
    { passive: true }
  );
});



/*--- JS : intercepter, envoyer en fetch, afficher toast--- */
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('footer-contact');
  if (!form) return;

  const btn   = document.getElementById('footer-contact-submit');
  const toastOkEl    = document.getElementById('contactToast');
  const toastErrEl   = document.getElementById('contactToastError');
  const toastOkMsg   = document.getElementById('contactToastMessage');
  const toastErrMsg  = document.getElementById('contactToastErrorMessage');

  const Toast = window.bootstrap?.Toast;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    btn.disabled = true;
    btn.innerHTML = 'Envoi…';

    try {
      const res = await fetch(form.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: new FormData(form)
      });

      const data = await res.json().catch(() => ({}));

      if (!res.ok || !data.ok) {
        throw new Error(data.message || 'Erreur lors de l’envoi');
      }

      // Succès
      if (Toast && toastOkEl) {
        toastOkMsg.textContent = data.message || 'Message envoyé ✔️';
        new Toast(toastOkEl, { delay: 3500 }).show();
      }
      form.reset();

    } catch (err) {
      if (Toast && toastErrEl) {
        toastErrMsg.textContent = err.message || 'Oups, un problème est survenu.';
        new Toast(toastErrEl, { delay: 4500 }).show();
      }
    } finally {
      btn.disabled = false;
      btn.innerHTML = 'Envoyer';
    }
  });
});

//--------planning modal----------------
(function attachCourseModal(){
  const CAL = document.querySelector('.week-calendar');
  if (!CAL) return;

  // --- mapping "activité" -> {titre, desc, img}
  const MAP = {
    'STEP': {
      titre:'Step',
      desc:"Cardio chorégraphié, fun et mémorisation. Débutant à confirmé.",
      img:'/images/step.jpg'
    },
    'CARDIOLAND': {
      titre:'Cardioland',
      desc:"Brûle-calories, endurance et énergie collective.",
      img:'/images/cardioland01.jpg'
    },
    'HIIT': {
      titre:'HIIT',
      desc:"Intervalles haute intensité, efficacité maximale.",
      img:'/images/hiit.jpg'
    },
    'FUNCTIONAL TRAINING': {
      titre:'Functional Training',
      desc:"Force, tonicité et souffle avec des mouvements efficaces.",
      img:'/images/functional.jpg'
    },
    'HYBRID TRAINING': {
      titre:'Hybrid Training',
      desc:"Course + ateliers fonctionnels. On se surpasse !",
      img:'/images/hybrid.jpg'
    },
    'SOUPLESSE': {
      // alias vers Souplesse Yoga Pilates
      titre:'Souplesse Yoga Pilates',
      desc:"Mobilité, détente musculaire, posture et muscles profonds.",
      img:'/images/souplesse.jpg'
    },
    'SOUPLESSE YOGA PILATE': { // au cas où
      titre:'Souplesse Yoga Pilates',
      desc:"Mobilité, détente musculaire, posture et muscles profonds.",
      img:'/images/souplesse.jpg'
    },
    'GYM SENIOR': {
      titre:'Gym Sénior',
      desc:"Équilibre, mobilité, force : séances adaptées et sécurisées.",
      img:'/images/gym.jpg'
    },
    'MARCHE NORDIQUE': {
      titre:'Marche Nordique / Running',
      desc:"Cardio en plein air, technique et progression en groupe.",
      img:'/images/running.jpg'
    },
    'RUNNING': {
      titre:'Marche Nordique / Running',
      desc:"Cardio en plein air, technique et progression en groupe.",
      img:'/images/running.jpg'
    },
    'STADE': {
      titre:'Stade',
      desc:"Foulée, agilité, vitesse : travail technique varié.",
      img:'/images/stade.jpg'
    },
    "MOVE N'DANCE": {
      titre:"Move n’ Dance",
      desc:"Choré simples, fun et cardio.",
      img:'/images/move.jpg'
    },
    'FIT DANCE': {
      titre:'Fit Dance',
      desc:"On construit la choré pendant le cours.",
      img:'/images/fitdance.jpg'
    },
    'HEELS': {
      titre:'Heels',
      desc:'Danse sur talons, stylée et assumée.',
      img:'/images/heels.jpg'
    },
    'JUMP': {
      titre:'Jump',
      desc:"Mini-trampoline : cardio fun, corps tonique, sans impact.",
      img:'/images/cardioland.jpg'
    },
    'SANGLES': {
      titre:'Sangles',
      desc:"TRX : force, stabilité, posture – tout niveau.",
      img:'/images/sangles.jpg'
    },
    'FLYING POLE': {
      titre:'Flying pole dance',
      desc:"Pole suspendue : activité unique pour se révéler.",
      img:'/images/flying.jpg'
    },
    'YOGA': {
      titre:'Yoga',
      desc:'Souplesse, respiration et recentrage.',
      img:'/images/souplesse.jpg'
    },
    'STEP DÉBUTANT': {
      titre:'Step',
      desc:"Version accessible pour apprendre sans stress.",
      img:'/images/step.jpg'
    }
  };

  const DAY_FR = {mon:'lundi', tue:'mardi', wed:'mercredi', thu:'jeudi', fri:'vendredi', sat:'samedi', sun:'dimanche'};

  function normTitle(s){
    return (s||'').normalize('NFD').replace(/[\u0300-\u036f]/g,'') // supprime accents
             .toUpperCase().trim();
  }

  function openModalFromEvent(el){
    const col = el.closest('.day-col');
    const dayKey = col?.dataset.day || 'mon';
    const dayLbl = DAY_FR[dayKey] || dayKey;

    const time = el.querySelector('.time')?.textContent?.trim() || '';
    const rawTitle = el.querySelector('.title')?.textContent?.trim() || '';
    const key = normTitle(rawTitle);

    const meta = MAP[key] || {
      titre: rawTitle || 'Cours',
      desc: "Pour ce cours, la description détaillée sera prochainement ajoutée.",
      img:  '/images/functional.jpg'
    };

    // Alimente la modale
    const $title = document.getElementById('cm-title');
    const $desc  = document.getElementById('cm-desc');
    const $img   = document.getElementById('cm-img');
    const $when  = document.getElementById('cm-when');
    const $cta   = document.getElementById('cm-cta');

    $title.textContent = meta.titre;
    $desc.textContent  = meta.desc;
    $img.style.backgroundImage = `url('${meta.img}')`;
    $when.textContent  = `${dayLbl} • ${time}`;

    // Lien vers la page d'essai pré-remplie
    // On tente d’extraire "HH:MM – HH:MM"
    const m = time.match(/(\d{2}:\d{2})\s*[\-–]\s*(\d{2}:\d{2})/);
    const start = m ? m[1] : '';
    const end   = m ? m[2] : '';

    const url = new URL('{{ path("seance_essai") }}', window.location.origin);
    // pas de date ici (planning hebdo), l’utilisateur choisira — tu peux aussi calculer la prochaine occurrence si tu veux
    url.searchParams.set('title', meta.titre);
    if (start) url.searchParams.set('start', start);
    if (end)   url.searchParams.set('end', end);
    $cta.href = url.toString();

    // Ouvre la modale
    new bootstrap.Modal(document.getElementById('courseModal')).show();
  }

  // Délégation : un seul écouteur pour toutes les cartes
  CAL.addEventListener('click', (ev) => {
    const card = ev.target.closest('.event');
    if (!card) return;
    openModalFromEvent(card);
  });

  // Optionnel : hint au clavier
  CAL.addEventListener('keydown', (ev) => {
    if (ev.key !== 'Enter' && ev.key !== ' ') return;
    const card = ev.target.closest('.event');
    if (!card) return;
    ev.preventDefault();
    openModalFromEvent(card);
  });

  // Accessibilité : rendre les events focusables
  CAL.querySelectorAll('.event').forEach(e => e.setAttribute('tabindex','0'));

  // Hotwire/Turbo : si tu re-rends la page
  window.addEventListener?.('turbo:render', () => {
    CAL.querySelectorAll('.event').forEach(e => e.setAttribute('tabindex','0'));
  });
})();









