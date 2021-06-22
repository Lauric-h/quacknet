<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210622124238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_quack (tag_id INT NOT NULL, quack_id INT NOT NULL, INDEX IDX_A1385669BAD26311 (tag_id), INDEX IDX_A1385669D3950CA9 (quack_id), PRIMARY KEY(tag_id, quack_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tag_quack ADD CONSTRAINT FK_A1385669BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_quack ADD CONSTRAINT FK_A1385669D3950CA9 FOREIGN KEY (quack_id) REFERENCES quack (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tag_quack DROP FOREIGN KEY FK_A1385669BAD26311');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_quack');
    }
}
