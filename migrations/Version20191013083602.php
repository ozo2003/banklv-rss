<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191013083602 extends AbstractMigration
{
    /**
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE rate (id INT AUTO_INCREMENT NOT NULL, currency_id INT DEFAULT NULL, exchange_id INT DEFAULT NULL, published_at DATETIME NOT NULL, rate DOUBLE PRECISION NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX IDX_DFEC3F3938248176 (currency_id), INDEX IDX_DFEC3F3968AFD1A0 (exchange_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exchange (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE currency (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(5) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rate ADD CONSTRAINT FK_DFEC3F3938248176 FOREIGN KEY (currency_id) REFERENCES currency (id)');
        $this->addSql('ALTER TABLE rate ADD CONSTRAINT FK_DFEC3F3968AFD1A0 FOREIGN KEY (exchange_id) REFERENCES exchange (id)');
    }

    /**
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE rate DROP FOREIGN KEY FK_DFEC3F3968AFD1A0');
        $this->addSql('ALTER TABLE rate DROP FOREIGN KEY FK_DFEC3F3938248176');
        $this->addSql('DROP TABLE rate');
        $this->addSql('DROP TABLE exchange');
        $this->addSql('DROP TABLE currency');
    }
}
