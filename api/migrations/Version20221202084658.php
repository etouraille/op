<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221202084658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE shop (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE thing ADD shop_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE thing ADD CONSTRAINT FK_5B4C2C834D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id)');
        $this->addSql('CREATE INDEX IDX_5B4C2C834D16C4DD ON thing (shop_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE thing DROP FOREIGN KEY FK_5B4C2C834D16C4DD');
        $this->addSql('DROP TABLE shop');
        $this->addSql('DROP INDEX IDX_5B4C2C834D16C4DD ON thing');
        $this->addSql('ALTER TABLE thing DROP shop_id');
    }
}
