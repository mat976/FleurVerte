<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260511060237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE action_log (id SERIAL NOT NULL, user_id INT DEFAULT NULL, action VARCHAR(255) NOT NULL, ip VARCHAR(45) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, details TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B2C5F685A76ED395 ON action_log (user_id)');
        $this->addSql('CREATE TABLE commande (id SERIAL NOT NULL, user_id INT NOT NULL, statut VARCHAR(50) NOT NULL, total NUMERIC(10, 2) NOT NULL, date_commande TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_livraison TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, adresse_livraison TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6EEAA67DA76ED395 ON commande (user_id)');
        $this->addSql('CREATE TABLE ligne_commande (id SERIAL NOT NULL, commande_id INT NOT NULL, fleur_id INT DEFAULT NULL, designation VARCHAR(255) NOT NULL, quantite INT NOT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3170B74B82EA2E54 ON ligne_commande (commande_id)');
        $this->addSql('CREATE INDEX IDX_3170B74BE8DD5A7 ON ligne_commande (fleur_id)');
        $this->addSql('ALTER TABLE action_log ADD CONSTRAINT FK_B2C5F685A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ligne_commande ADD CONSTRAINT FK_3170B74B82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ligne_commande ADD CONSTRAINT FK_3170B74BE8DD5A7 FOREIGN KEY (fleur_id) REFERENCES fleur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commentaire ADD visible BOOLEAN NOT NULL DEFAULT TRUE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE action_log DROP CONSTRAINT FK_B2C5F685A76ED395');
        $this->addSql('ALTER TABLE commande DROP CONSTRAINT FK_6EEAA67DA76ED395');
        $this->addSql('ALTER TABLE ligne_commande DROP CONSTRAINT FK_3170B74B82EA2E54');
        $this->addSql('ALTER TABLE ligne_commande DROP CONSTRAINT FK_3170B74BE8DD5A7');
        $this->addSql('DROP TABLE action_log');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE ligne_commande');
        $this->addSql('ALTER TABLE commentaire DROP COLUMN visible');
    }
}
