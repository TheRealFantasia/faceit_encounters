<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200506024321 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD guid VARCHAR(180) NOT NULL, ADD picture VARCHAR(255) NOT NULL, ADD locale VARCHAR(3) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6492B6FCFB2 ON user (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64916DB4F89 ON user (picture)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6494180C698 ON user (locale)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_8D93D6492B6FCFB2 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D64916DB4F89 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D6494180C698 ON user');
        $this->addSql('ALTER TABLE user DROP guid, DROP picture, DROP locale');
    }
}
