<?php

declare(strict_types=1);

namespace App\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200323044550 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE cities (id VARCHAR(6) NOT NULL, name VARCHAR(100) NOT NULL, country VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cities_users (city_id VARCHAR(6) NOT NULL, user_id VARCHAR(6) NOT NULL, INDEX IDX_6A104E888BAC62AF (city_id), INDEX IDX_6A104E88A76ED395 (user_id), PRIMARY KEY(city_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id VARCHAR(6) NOT NULL, name VARCHAR(100) NOT NULL, phone VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cities_users ADD CONSTRAINT FK_6A104E888BAC62AF FOREIGN KEY (city_id) REFERENCES cities (id)');
        $this->addSql('ALTER TABLE cities_users ADD CONSTRAINT FK_6A104E88A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cities_users DROP FOREIGN KEY FK_6A104E888BAC62AF');
        $this->addSql('ALTER TABLE cities_users DROP FOREIGN KEY FK_6A104E88A76ED395');
        $this->addSql('DROP TABLE cities');
        $this->addSql('DROP TABLE cities_users');
        $this->addSql('DROP TABLE users');
    }
}
