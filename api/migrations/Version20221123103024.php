<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221123103024 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expense ADD user_id INT NOT NULL, ADD owner_id INT NOT NULL, ADD thing_id INT NOT NULL, ADD amount INT NOT NULL, ADD status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA67E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6C36906A7 FOREIGN KEY (thing_id) REFERENCES thing (id)');
        $this->addSql('CREATE INDEX IDX_2D3A8DA6A76ED395 ON expense (user_id)');
        $this->addSql('CREATE INDEX IDX_2D3A8DA67E3C61F9 ON expense (owner_id)');
        $this->addSql('CREATE INDEX IDX_2D3A8DA6C36906A7 ON expense (thing_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA6A76ED395');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA67E3C61F9');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA6C36906A7');
        $this->addSql('DROP INDEX IDX_2D3A8DA6A76ED395 ON expense');
        $this->addSql('DROP INDEX IDX_2D3A8DA67E3C61F9 ON expense');
        $this->addSql('DROP INDEX IDX_2D3A8DA6C36906A7 ON expense');
        $this->addSql('ALTER TABLE expense DROP user_id, DROP owner_id, DROP thing_id, DROP amount, DROP status');
    }
}
