<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221121171412 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE thing DROP FOREIGN KEY FK_5B4C2C83C54C8C93');
        $this->addSql('DROP INDEX IDX_5B4C2C83C54C8C93 ON thing');
        $this->addSql('ALTER TABLE thing CHANGE type_id thing_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE thing ADD CONSTRAINT FK_5B4C2C83C36906A7 FOREIGN KEY (thing_id) REFERENCES thing_type (id)');
        $this->addSql('CREATE INDEX IDX_5B4C2C83C36906A7 ON thing (thing_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE thing DROP FOREIGN KEY FK_5B4C2C83C36906A7');
        $this->addSql('DROP INDEX IDX_5B4C2C83C36906A7 ON thing');
        $this->addSql('ALTER TABLE thing CHANGE thing_id type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE thing ADD CONSTRAINT FK_5B4C2C83C54C8C93 FOREIGN KEY (type_id) REFERENCES thing_type (id)');
        $this->addSql('CREATE INDEX IDX_5B4C2C83C54C8C93 ON thing (type_id)');
    }
}
