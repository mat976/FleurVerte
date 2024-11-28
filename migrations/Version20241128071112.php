<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241128071112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fleur CHANGE fleuriste_id fleuriste_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fleur ADD CONSTRAINT FK_3FFA9238FC9FF7E FOREIGN KEY (fleuriste_id) REFERENCES fleuriste (id)');
        $this->addSql('CREATE INDEX IDX_3FFA9238FC9FF7E ON fleur (fleuriste_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fleur DROP FOREIGN KEY FK_3FFA9238FC9FF7E');
        $this->addSql('DROP INDEX IDX_3FFA9238FC9FF7E ON fleur');
        $this->addSql('ALTER TABLE fleur CHANGE fleuriste_id fleuriste_id INT NOT NULL');
    }
}
