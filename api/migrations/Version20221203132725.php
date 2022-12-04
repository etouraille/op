<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221203132725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expense DROP INDEX UNIQ_2D3A8DA6B83297E7, ADD INDEX IDX_2D3A8DA6B83297E7 (reservation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expense DROP INDEX IDX_2D3A8DA6B83297E7, ADD UNIQUE INDEX UNIQ_2D3A8DA6B83297E7 (reservation_id)');
    }
}
