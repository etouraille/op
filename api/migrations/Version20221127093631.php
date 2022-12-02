<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221127093631 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE coin (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, thing_id INT DEFAULT NULL, amount INT NOT NULL, reason INT NOT NULL, INDEX IDX_5569975D7E3C61F9 (owner_id), INDEX IDX_5569975DC36906A7 (thing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE coin ADD CONSTRAINT FK_5569975D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE coin ADD CONSTRAINT FK_5569975DC36906A7 FOREIGN KEY (thing_id) REFERENCES thing (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coin DROP FOREIGN KEY FK_5569975D7E3C61F9');
        $this->addSql('ALTER TABLE coin DROP FOREIGN KEY FK_5569975DC36906A7');
        $this->addSql('DROP TABLE coin');
    }
}
