<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250627055001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fleur_tag (fleur_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_20E1648EE8DD5A7 (fleur_id), INDEX IDX_20E1648EBAD26311 (tag_id), PRIMARY KEY(fleur_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, couleur VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fleur_tag ADD CONSTRAINT FK_20E1648EE8DD5A7 FOREIGN KEY (fleur_id) REFERENCES fleur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fleur_tag ADD CONSTRAINT FK_20E1648EBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fleur_tag DROP FOREIGN KEY FK_20E1648EE8DD5A7');
        $this->addSql('ALTER TABLE fleur_tag DROP FOREIGN KEY FK_20E1648EBAD26311');
        $this->addSql('DROP TABLE fleur_tag');
        $this->addSql('DROP TABLE tag');
    }
}
