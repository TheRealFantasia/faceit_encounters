<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200506141606 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE cached_match (id INT AUTO_INCREMENT NOT NULL, guid VARCHAR(255) NOT NULL, team1 VARCHAR(255) NOT NULL, team2 VARCHAR(255) NOT NULL, winner VARCHAR(5) NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE match_players (match_id INT NOT NULL, player_id INT NOT NULL, INDEX IDX_51E81CC92ABEACD6 (match_id), UNIQUE INDEX UNIQ_51E81CC999E6F5DF (player_id), PRIMARY KEY(match_id, player_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE match_players ADD CONSTRAINT FK_51E81CC92ABEACD6 FOREIGN KEY (match_id) REFERENCES cached_match (id)');
        $this->addSql('ALTER TABLE match_players ADD CONSTRAINT FK_51E81CC999E6F5DF FOREIGN KEY (player_id) REFERENCES cached_name (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE match_players DROP FOREIGN KEY FK_51E81CC92ABEACD6');
        $this->addSql('DROP TABLE cached_match');
        $this->addSql('DROP TABLE match_players');
    }
}
