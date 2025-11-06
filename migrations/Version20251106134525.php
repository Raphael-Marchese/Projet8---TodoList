<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251106134525 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add roles on users and set ROLE_USER by default on existing users';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            UPDATE user
    SET roles = JSON_ARRAY('ROLE_USER')
    WHERE roles IS NULL OR roles = '' OR roles = 'N;'
");
        $this->addSql('ALTER TABLE user MODIFY roles JSON NOT NULL');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP roles');
    }
}
