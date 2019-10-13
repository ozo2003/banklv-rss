<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201012211954 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO exchange (title, url) VALUES ('bank', 'https://www.bank.lv/vk/ecb_rss.xml')");
        $this->addSql("INSERT INTO exchange (title, url) VALUES ('fxex', 'https://eur.fxexchangerate.com/rss.xml')");
        $this->addSql("INSERT INTO exchange (title, url) VALUES ('float', 'http://www.floatrates.com/daily/eur.xml')");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM exchange WHERE title IN ('bank', 'fxex', 'float')");
    }
}
