<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210601114359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91E3569D950');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91E3569D950 FOREIGN KEY (featured_image_id) REFERENCES image (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91E3569D950');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91E3569D950 FOREIGN KEY (featured_image_id) REFERENCES image (id)');
    }
}
