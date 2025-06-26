<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration manuelle pour créer la table fleuriste_image
 */
final class Version20241130000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création de la table fleuriste_image manquante';
    }

    public function up(Schema $schema): void
    {
        // Création de la table fleuriste_image
        $this->addSql('CREATE TABLE fleuriste_image (id INT AUTO_INCREMENT NOT NULL, fleuriste_id INT NOT NULL, image_name VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', caption VARCHAR(255) DEFAULT NULL, INDEX IDX_D88DB9D78FC9FF7E (fleuriste_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fleuriste_image ADD CONSTRAINT FK_D88DB9D78FC9FF7E FOREIGN KEY (fleuriste_id) REFERENCES fleuriste (id)');
    }

    public function down(Schema $schema): void
    {
        // Suppression de la table fleuriste_image
        $this->addSql('ALTER TABLE fleuriste_image DROP FOREIGN KEY FK_D88DB9D78FC9FF7E');
        $this->addSql('DROP TABLE fleuriste_image');
    }
}