<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241127063444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change user_id column in fleuriste table';
    }

    public function up(Schema $schema): void
    {
        // Supprime d'abord la contrainte de clé étrangère
        $this->addSql('ALTER TABLE fleuriste DROP FOREIGN KEY FK_2955524EA76ED395');

        // Supprime l'index de la colonne user_id s'il existe
        $this->addSql('DROP INDEX IDX_2955524EA76ED395 ON fleuriste');

        // Modifie la colonne user_id
        $this->addSql('ALTER TABLE fleuriste CHANGE user_id user_id INT DEFAULT NULL');

        // Recrée l'index
        $this->addSql('CREATE INDEX IDX_2955524EA76ED395 ON fleuriste (user_id)');

        // Recrée la contrainte de clé étrangère
        $this->addSql('ALTER TABLE fleuriste ADD CONSTRAINT FK_2955524EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // Supprime d'abord la contrainte de clé étrangère
        $this->addSql('ALTER TABLE fleuriste DROP FOREIGN KEY FK_2955524EA76ED395');

        // Supprime l'index
        $this->addSql('DROP INDEX IDX_2955524EA76ED395 ON fleuriste');

        // Restaure la colonne user_id à son état précédent
        $this->addSql('ALTER TABLE fleuriste CHANGE user_id user_id INT NOT NULL');

        // Recrée l'index
        $this->addSql('CREATE INDEX IDX_2955524EA76ED395 ON fleuriste (user_id)');

        // Recrée la contrainte de clé étrangère
        $this->addSql('ALTER TABLE fleuriste ADD CONSTRAINT FK_2955524EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }
}
