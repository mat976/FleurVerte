<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241128115654 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fleur ADD is_pinned TINYINT(1) DEFAULT 0 NOT NULL, DROP effets, DROP vues, DROP cbd, DROP created_at, DROP categories, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE stock stock INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fleur ADD effets JSON NOT NULL, ADD vues INT NOT NULL, ADD cbd DOUBLE PRECISION DEFAULT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD categories JSON NOT NULL, DROP is_pinned, CHANGE description description LONGTEXT NOT NULL, CHANGE stock stock INT NOT NULL');
    }
}
