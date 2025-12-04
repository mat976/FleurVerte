<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251204091122 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adresse (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, rue VARCHAR(255) NOT NULL, code_postal VARCHAR(10) NOT NULL, ville VARCHAR(255) NOT NULL, complement VARCHAR(255) DEFAULT NULL, principale TINYINT(1) NOT NULL, INDEX IDX_C35F0816A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cart_item (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, fleur_id INT NOT NULL, quantity INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F0FE2527A76ED395 (user_id), INDEX IDX_F0FE2527E8DD5A7 (fleur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_C7440455A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conversation (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, fleuriste_id INT NOT NULL, titre VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL, date_derniere_activite DATETIME NOT NULL, INDEX IDX_8A8E26E919EB6921 (client_id), INDEX IDX_8A8E26E98FC9FF7E (fleuriste_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fleur (id INT AUTO_INCREMENT NOT NULL, fleuriste_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, prix DOUBLE PRECISION NOT NULL, stock INT DEFAULT NULL, is_pinned TINYINT(1) DEFAULT 0 NOT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_3FFA9238FC9FF7E (fleuriste_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fleur_tag (fleur_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_20E1648EE8DD5A7 (fleur_id), INDEX IDX_20E1648EBAD26311 (tag_id), PRIMARY KEY(fleur_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fleuriste (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, nom VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, telephone VARCHAR(15) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, actif TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_2955524EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fleuriste_image (id INT AUTO_INCREMENT NOT NULL, fleuriste_id INT NOT NULL, image_name VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', caption VARCHAR(255) DEFAULT NULL, INDEX IDX_F3CB9D8B8FC9FF7E (fleuriste_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, expediteur_id INT NOT NULL, destinataire_id INT NOT NULL, conversation_id INT NOT NULL, contenu LONGTEXT NOT NULL, date_envoi DATETIME NOT NULL, est_lu TINYINT(1) NOT NULL, INDEX IDX_B6BD307F10335F61 (expediteur_id), INDEX IDX_B6BD307FA4F84F6E (destinataire_id), INDEX IDX_B6BD307F9AC0396 (conversation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, couleur VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, avatar_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE adresse ADD CONSTRAINT FK_C35F0816A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE2527A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE2527E8DD5A7 FOREIGN KEY (fleur_id) REFERENCES fleur (id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E919EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E98FC9FF7E FOREIGN KEY (fleuriste_id) REFERENCES fleuriste (id)');
        $this->addSql('ALTER TABLE fleur ADD CONSTRAINT FK_3FFA9238FC9FF7E FOREIGN KEY (fleuriste_id) REFERENCES fleuriste (id)');
        $this->addSql('ALTER TABLE fleur_tag ADD CONSTRAINT FK_20E1648EE8DD5A7 FOREIGN KEY (fleur_id) REFERENCES fleur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fleur_tag ADD CONSTRAINT FK_20E1648EBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fleuriste ADD CONSTRAINT FK_2955524EA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE fleuriste_image ADD CONSTRAINT FK_F3CB9D8B8FC9FF7E FOREIGN KEY (fleuriste_id) REFERENCES fleuriste (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F10335F61 FOREIGN KEY (expediteur_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA4F84F6E FOREIGN KEY (destinataire_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse DROP FOREIGN KEY FK_C35F0816A76ED395');
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE2527A76ED395');
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE2527E8DD5A7');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455A76ED395');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E919EB6921');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E98FC9FF7E');
        $this->addSql('ALTER TABLE fleur DROP FOREIGN KEY FK_3FFA9238FC9FF7E');
        $this->addSql('ALTER TABLE fleur_tag DROP FOREIGN KEY FK_20E1648EE8DD5A7');
        $this->addSql('ALTER TABLE fleur_tag DROP FOREIGN KEY FK_20E1648EBAD26311');
        $this->addSql('ALTER TABLE fleuriste DROP FOREIGN KEY FK_2955524EA76ED395');
        $this->addSql('ALTER TABLE fleuriste_image DROP FOREIGN KEY FK_F3CB9D8B8FC9FF7E');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F10335F61');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FA4F84F6E');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F9AC0396');
        $this->addSql('DROP TABLE adresse');
        $this->addSql('DROP TABLE cart_item');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE conversation');
        $this->addSql('DROP TABLE fleur');
        $this->addSql('DROP TABLE fleur_tag');
        $this->addSql('DROP TABLE fleuriste');
        $this->addSql('DROP TABLE fleuriste_image');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
