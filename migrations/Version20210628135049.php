<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210628135049 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE report');
        $this->addSql('ALTER TABLE quack ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE positive positive INT NOT NULL, CHANGE negative negative INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, quack_id INT NOT NULL, treated TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_C42F7784D3950CA9 (quack_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784D3950CA9 FOREIGN KEY (quack_id) REFERENCES quack (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE quack DROP updated_at, CHANGE positive positive INT DEFAULT 0 NOT NULL, CHANGE negative negative INT DEFAULT 0 NOT NULL');
    }
}
