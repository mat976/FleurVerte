<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Fix sequences PostgreSQL après chargement des fixtures
 */
final class Version20260505090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Reset des sequences pour eviter les conflits d\'ID lors des inscriptions';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("SELECT setval('user_id_seq', (SELECT MAX(id) + 1 FROM \"user\"), false)");
        $this->addSql("SELECT setval('fleuriste_id_seq', (SELECT MAX(id) + 1 FROM fleuriste), false)");
        $this->addSql("SELECT setval('fleur_id_seq', (SELECT MAX(id) + 1 FROM fleur), false)");
        $this->addSql("SELECT setval('tag_id_seq', (SELECT MAX(id) + 1 FROM tag), false)");
        $this->addSql("SELECT setval('client_id_seq', (SELECT MAX(id) + 1 FROM client), false)");
        $this->addSql("SELECT setval('adresse_id_seq', (SELECT MAX(id) + 1 FROM adresse), false)");
        $this->addSql("SELECT setval('commentaire_id_seq', (SELECT MAX(id) + 1 FROM commentaire), false)");
        $this->addSql("SELECT setval('conversation_id_seq', (SELECT MAX(id) + 1 FROM conversation), false)");
        $this->addSql("SELECT setval('message_id_seq', (SELECT MAX(id) + 1 FROM message), false)");
    }

    public function down(Schema $schema): void
    {
        // Pas de rollback nécessaire pour les séquences
    }
}
