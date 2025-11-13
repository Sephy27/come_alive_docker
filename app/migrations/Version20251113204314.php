<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251113204314 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make message column NOT NULL safely';
    }

    public function up(Schema $schema): void
    {
        // 1) S'assurer qu'aucune ligne ne contient encore NULL
        $this->addSql("UPDATE trial_request SET message = '' WHERE message IS NULL");

        // 2) Appliquer la contrainte NOT NULL
        $this->addSql('ALTER TABLE trial_request CHANGE message message LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // Revenir en arrière si nécessaire
        $this->addSql('ALTER TABLE trial_request CHANGE message message LONGTEXT DEFAULT NULL');
    }
}

