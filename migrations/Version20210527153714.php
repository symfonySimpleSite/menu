<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210527153714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE page (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, keywords VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, preview LONGTEXT NOT NULL, body LONGTEXT DEFAULT NULL, type SMALLINT NOT NULL, status SMALLINT NOT NULL, image VARCHAR(255) DEFAULT NULL, created_date DATETIME NOT NULL, INDEX IDX_140AB620727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB620727ACA70 FOREIGN KEY (parent_id) REFERENCES page (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB620727ACA70');
        $this->addSql('DROP TABLE page');
    }
}
