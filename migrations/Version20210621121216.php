<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210621121216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ducks ADD username VARCHAR(180) NOT NULL, ADD roles JSON NOT NULL, DROP duckname');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F3E591ADF85E0677 ON ducks (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_F3E591ADF85E0677 ON ducks');
        $this->addSql('ALTER TABLE ducks ADD duckname VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP username, DROP roles');
    }
}
