<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221130094832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX search_index_description ON thing');
        $this->addSql('DROP INDEX search_index ON thing');
        $this->addSql('DROP INDEX search_index_name ON thing');
        $this->addSql('ALTER TABLE thing CHANGE description description VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE thing CHANGE description description LONGTEXT NOT NULL');
        $this->addSql('CREATE INDEX search_index_description ON thing (description(100))');
        $this->addSql('CREATE INDEX search_index ON thing (name, description(100))');
        $this->addSql('CREATE INDEX search_index_name ON thing (name)');
    }
}
