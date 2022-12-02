<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221123162000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE income (id INT AUTO_INCREMENT NOT NULL, thing_id INT NOT NULL, user_id INT NOT NULL, expense_id INT NOT NULL, amount INT NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_3FA862D0C36906A7 (thing_id), INDEX IDX_3FA862D0A76ED395 (user_id), UNIQUE INDEX UNIQ_3FA862D0F395DB7B (expense_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE income ADD CONSTRAINT FK_3FA862D0C36906A7 FOREIGN KEY (thing_id) REFERENCES thing (id)');
        $this->addSql('ALTER TABLE income ADD CONSTRAINT FK_3FA862D0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE income ADD CONSTRAINT FK_3FA862D0F395DB7B FOREIGN KEY (expense_id) REFERENCES expense (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE income DROP FOREIGN KEY FK_3FA862D0C36906A7');
        $this->addSql('ALTER TABLE income DROP FOREIGN KEY FK_3FA862D0A76ED395');
        $this->addSql('ALTER TABLE income DROP FOREIGN KEY FK_3FA862D0F395DB7B');
        $this->addSql('DROP TABLE income');
    }
}
