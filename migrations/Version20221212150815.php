<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221212150815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD COLUMN slug VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE licence ADD COLUMN slug VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__article AS SELECT id, author_id, licence_id, name, description, content, created_at, updated_at, img FROM article');
        $this->addSql('DROP TABLE article');
        $this->addSql('CREATE TABLE article (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, licence_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, content CLOB NOT NULL, created_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , img CLOB DEFAULT NULL, CONSTRAINT FK_23A0E66F675F31B FOREIGN KEY (author_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_23A0E6626EF07C9 FOREIGN KEY (licence_id) REFERENCES licence (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO article (id, author_id, licence_id, name, description, content, created_at, updated_at, img) SELECT id, author_id, licence_id, name, description, content, created_at, updated_at, img FROM __temp__article');
        $this->addSql('DROP TABLE __temp__article');
        $this->addSql('CREATE INDEX IDX_23A0E66F675F31B ON article (author_id)');
        $this->addSql('CREATE INDEX IDX_23A0E6626EF07C9 ON article (licence_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__licence AS SELECT id, name, created_at, updated_at, img FROM licence');
        $this->addSql('DROP TABLE licence');
        $this->addSql('CREATE TABLE licence (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , img CLOB DEFAULT NULL)');
        $this->addSql('INSERT INTO licence (id, name, created_at, updated_at, img) SELECT id, name, created_at, updated_at, img FROM __temp__licence');
        $this->addSql('DROP TABLE __temp__licence');
    }
}
