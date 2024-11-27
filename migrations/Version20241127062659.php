<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241127062659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Restructure fleuriste and user tables';
    }

    public function up(Schema $schema): void
    {
        // Désactiver les vérifications de clés étrangères
        $this->addSql('SET FOREIGN_KEY_CHECKS=0');

        // Supprimer les contraintes et indices
        $this->addSql('ALTER TABLE fleuriste DROP FOREIGN KEY FK_2955524EA76ED395');
        $this->addSql('DROP INDEX IDX_2955524EA76ED395 ON fleuriste');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677 ON user');

        // Modifier les tables
        $this->addSql('ALTER TABLE fleuriste DROP user_id');
        $this->addSql('ALTER TABLE user CHANGE username user_type VARCHAR(255) NOT NULL');

        // Ajouter une nouvelle colonne user_id à fleuriste
        $this->addSql('ALTER TABLE fleuriste ADD user_id INT NOT NULL');

        // Recréer l'index et la contrainte de clé étrangère
        $this->addSql('CREATE INDEX IDX_2955524EA76ED395 ON fleuriste (user_id)');
        $this->addSql('ALTER TABLE fleuriste ADD CONSTRAINT FK_2955524EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');

        // Réactiver les vérifications de clés étrangères
        $this->addSql('SET FOREIGN_KEY_CHECKS=1');
    }
    public function down(Schema $schema): void
    {
        // Supprimer la contrainte et l'index
        $this->addSql('ALTER TABLE fleuriste DROP FOREIGN KEY FK_2955524EA76ED395');
        $this->addSql('DROP INDEX IDX_2955524EA76ED395 ON fleuriste');

        // Restaurer les tables à leur état précédent
        $this->addSql('ALTER TABLE fleuriste DROP user_id');
        $this->addSql('ALTER TABLE fleuriste ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE user_type username VARCHAR(255) NOT NULL');

        // Recréer les indices et contraintes
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
        $this->addSql('CREATE INDEX IDX_2955524EA76ED395 ON fleuriste (user_id)');
        $this->addSql('ALTER TABLE fleuriste ADD CONSTRAINT FK_2955524EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }
}
