<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221130094200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX search_index ON thing (name, description(100))');
        $this->addSql('CREATE INDEX search_index_name ON thing (name)');
        $this->addSql('CREATE INDEX search_index_description ON thing (description(100))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX search_index ON thing');
        $this->addSql('DROP INDEX search_index_name ON thing');
        $this->addSql('DROP INDEX search_index_description ON thing');
    }
}
