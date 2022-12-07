<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221207141552 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE compensation (id INT AUTO_INCREMENT NOT NULL, thing_id INT NOT NULL, user_id INT NOT NULL, rate DOUBLE PRECISION NOT NULL, INDEX IDX_B2DD12DAC36906A7 (thing_id), INDEX IDX_B2DD12DAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE compensation ADD CONSTRAINT FK_B2DD12DAC36906A7 FOREIGN KEY (thing_id) REFERENCES thing (id)');
        $this->addSql('ALTER TABLE compensation ADD CONSTRAINT FK_B2DD12DAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compensation DROP FOREIGN KEY FK_B2DD12DAC36906A7');
        $this->addSql('ALTER TABLE compensation DROP FOREIGN KEY FK_B2DD12DAA76ED395');
        $this->addSql('DROP TABLE compensation');
    }
}
