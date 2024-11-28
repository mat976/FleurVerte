<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241128080826 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse ADD fleuriste_id INT NOT NULL, ADD code_postal VARCHAR(10) NOT NULL, ADD ville VARCHAR(255) NOT NULL, ADD complement VARCHAR(255) DEFAULT NULL, ADD principale TINYINT(1) NOT NULL, DROP num_rue, DROP code_post, CHANGE nom_rue rue VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE adresse ADD CONSTRAINT FK_C35F08168FC9FF7E FOREIGN KEY (fleuriste_id) REFERENCES fleuriste (id)');
        $this->addSql('CREATE INDEX IDX_C35F08168FC9FF7E ON adresse (fleuriste_id)');
        $this->addSql('ALTER TABLE fleuriste DROP FOREIGN KEY FK_2955524E4DE7DC5C');
        $this->addSql('DROP INDEX UNIQ_2955524E4DE7DC5C ON fleuriste');
        $this->addSql('ALTER TABLE fleuriste DROP adresse_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse DROP FOREIGN KEY FK_C35F08168FC9FF7E');
        $this->addSql('DROP INDEX IDX_C35F08168FC9FF7E ON adresse');
        $this->addSql('ALTER TABLE adresse ADD nom_rue VARCHAR(255) NOT NULL, ADD code_post INT NOT NULL, DROP rue, DROP code_postal, DROP ville, DROP complement, DROP principale, CHANGE fleuriste_id num_rue INT NOT NULL');
        $this->addSql('ALTER TABLE fleuriste ADD adresse_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fleuriste ADD CONSTRAINT FK_2955524E4DE7DC5C FOREIGN KEY (adresse_id) REFERENCES adresse (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2955524E4DE7DC5C ON fleuriste (adresse_id)');
    }
}
