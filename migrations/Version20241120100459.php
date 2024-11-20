<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241120100459 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, user_type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE client ADD user_id INT NOT NULL, DROP username, DROP email, DROP passworld');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7440455A76ED395 ON client (user_id)');
        $this->addSql('ALTER TABLE fleuriste DROP FOREIGN KEY FK_2955524E4DE7DC5C');
        $this->addSql('DROP INDEX UNIQ_2955524E4DE7DC5C ON fleuriste');
        $this->addSql('ALTER TABLE fleuriste DROP nom, CHANGE adresse_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE fleuriste ADD CONSTRAINT FK_2955524EA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2955524EA76ED395 ON fleuriste (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455A76ED395');
        $this->addSql('ALTER TABLE fleuriste DROP FOREIGN KEY FK_2955524EA76ED395');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP INDEX UNIQ_C7440455A76ED395 ON client');
        $this->addSql('ALTER TABLE client ADD username VARCHAR(255) NOT NULL, ADD email VARCHAR(255) NOT NULL, ADD passworld VARCHAR(255) NOT NULL, DROP user_id');
        $this->addSql('DROP INDEX UNIQ_2955524EA76ED395 ON fleuriste');
        $this->addSql('ALTER TABLE fleuriste ADD nom VARCHAR(255) NOT NULL, CHANGE user_id adresse_id INT NOT NULL');
        $this->addSql('ALTER TABLE fleuriste ADD CONSTRAINT FK_2955524E4DE7DC5C FOREIGN KEY (adresse_id) REFERENCES adresse (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2955524E4DE7DC5C ON fleuriste (adresse_id)');
    }
}
