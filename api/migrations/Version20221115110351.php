<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221115110351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE thing ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE thing ADD CONSTRAINT FK_5B4C2C837E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5B4C2C837E3C61F9 ON thing (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE thing DROP FOREIGN KEY FK_5B4C2C837E3C61F9');
        $this->addSql('DROP INDEX IDX_5B4C2C837E3C61F9 ON thing');
        $this->addSql('ALTER TABLE thing DROP owner_id');
    }
}
