<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251106122258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add anonyme user to existing tasks';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            INSERT INTO `user` (username, password, email)
            SELECT 'anonyme', '', 'anonyme@example.com'
            WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE username = 'anonyme')
        ");

        $this->addSql("
            UPDATE task
            SET author_id = (SELECT id FROM `user` WHERE username = 'anonyme')
            WHERE author_id IS NULL
        ");
        $this->addSql('ALTER TABLE task MODIFY author_id INT NOT NULL');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task DROP author_id');
    }
}
