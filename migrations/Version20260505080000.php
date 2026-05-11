<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Ajout des champs siret et statut à l'entité Fleuriste
 */
final class Version20260505080000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout colonnes siret (VARCHAR 14) et statut (VARCHAR 20) sur fleuriste';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE fleuriste ADD siret VARCHAR(14) DEFAULT NULL');
        $this->addSql('ALTER TABLE fleuriste ADD statut VARCHAR(20) NOT NULL DEFAULT \'en_attente\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE fleuriste DROP siret');
        $this->addSql('ALTER TABLE fleuriste DROP statut');
    }
}
