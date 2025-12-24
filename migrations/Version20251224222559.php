<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251224222559 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Add columns with default values for existing users
        $this->addSql('ALTER TABLE "user" ADD full_name VARCHAR(255) DEFAULT \'User\' NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD phone_number VARCHAR(20) DEFAULT \'+440000000000\' NOT NULL');

        // Remove the defaults so new users must provide these values
        $this->addSql('ALTER TABLE "user" ALTER COLUMN full_name DROP DEFAULT');
        $this->addSql('ALTER TABLE "user" ALTER COLUMN phone_number DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP full_name');
        $this->addSql('ALTER TABLE "user" DROP phone_number');
    }
}
