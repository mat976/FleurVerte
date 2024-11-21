<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241120113007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse ADD num_rue INT NOT NULL, ADD code_post INT NOT NULL, DROP NumRue, DROP CodePost, CHANGE NomRue nom_rue VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX adresse_id ON client');
        $this->addSql('ALTER TABLE client ADD user_id INT NOT NULL, DROP nom, DROP prenom, DROP adresse_id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7440455A76ED395 ON client (user_id)');
        $this->addSql('DROP INDEX UNIQ_2955524E4DE7DC5C ON fleuriste');
        $this->addSql('ALTER TABLE fleuriste DROP adresse_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse ADD NumRue INT NOT NULL, ADD CodePost INT NOT NULL, DROP num_rue, DROP code_post, CHANGE nom_rue NomRue VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455A76ED395');
        $this->addSql('DROP INDEX UNIQ_C7440455A76ED395 ON client');
        $this->addSql('ALTER TABLE client ADD nom VARCHAR(255) NOT NULL, ADD prenom VARCHAR(255) NOT NULL, ADD adresse_id INT DEFAULT NULL, DROP user_id');
        $this->addSql('CREATE UNIQUE INDEX adresse_id ON client (adresse_id)');
        $this->addSql('ALTER TABLE fleuriste ADD adresse_id INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2955524E4DE7DC5C ON fleuriste (adresse_id)');
    }
}
