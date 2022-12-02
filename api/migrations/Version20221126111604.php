<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221126111604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE income_data (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, file VARCHAR(255) NOT NULL, amount DOUBLE PRECISION NOT NULL, date DATETIME NOT NULL, INDEX IDX_67B9E54DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE income_data ADD CONSTRAINT FK_67B9E54DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE expense ADD income_data_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA68FB7D745 FOREIGN KEY (income_data_id) REFERENCES income_data (id)');
        $this->addSql('CREATE INDEX IDX_2D3A8DA68FB7D745 ON expense (income_data_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA68FB7D745');
        $this->addSql('ALTER TABLE income_data DROP FOREIGN KEY FK_67B9E54DA76ED395');
        $this->addSql('DROP TABLE income_data');
        $this->addSql('DROP INDEX IDX_2D3A8DA68FB7D745 ON expense');
        $this->addSql('ALTER TABLE expense DROP income_data_id');
    }
}
