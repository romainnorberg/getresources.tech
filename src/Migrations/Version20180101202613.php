<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180101202613 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE language ADD created_by_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', ADD site_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ADD name INT NOT NULL, ADD created DATETIME NOT NULL, CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE language ADD CONSTRAINT FK_D4DB71B5B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE language ADD CONSTRAINT FK_D4DB71B5F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('CREATE INDEX IDX_D4DB71B5B03A8386 ON language (created_by_id)');
        $this->addSql('CREATE INDEX IDX_D4DB71B5F6BD1646 ON language (site_id)');
        $this->addSql('ALTER TABLE site ADD is_highlight TINYINT(1) DEFAULT \'0\', CHANGE author author VARCHAR(255) DEFAULT NULL, CHANGE author_url author_url VARCHAR(255) DEFAULT NULL, CHANGE rate rate INT DEFAULT NULL, CHANGE hit hit INT DEFAULT NULL, CHANGE is_validated is_validated TINYINT(1) DEFAULT \'0\'');
        $this->addSql('ALTER TABLE source ADD created_by_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', ADD site_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ADD name INT NOT NULL, ADD created DATETIME NOT NULL, CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE source ADD CONSTRAINT FK_5F8A7F73B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE source ADD CONSTRAINT FK_5F8A7F73F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('CREATE INDEX IDX_5F8A7F73B03A8386 ON source (created_by_id)');
        $this->addSql('CREATE INDEX IDX_5F8A7F73F6BD1646 ON source (site_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE language DROP FOREIGN KEY FK_D4DB71B5B03A8386');
        $this->addSql('ALTER TABLE language DROP FOREIGN KEY FK_D4DB71B5F6BD1646');
        $this->addSql('DROP INDEX IDX_D4DB71B5B03A8386 ON language');
        $this->addSql('DROP INDEX IDX_D4DB71B5F6BD1646 ON language');
        $this->addSql('ALTER TABLE language DROP created_by_id, DROP site_id, DROP name, DROP created, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE site DROP is_highlight, CHANGE author author VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE author_url author_url VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE rate rate INT NOT NULL, CHANGE hit hit INT NOT NULL, CHANGE is_validated is_validated TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE source DROP FOREIGN KEY FK_5F8A7F73B03A8386');
        $this->addSql('ALTER TABLE source DROP FOREIGN KEY FK_5F8A7F73F6BD1646');
        $this->addSql('DROP INDEX IDX_5F8A7F73B03A8386 ON source');
        $this->addSql('DROP INDEX IDX_5F8A7F73F6BD1646 ON source');
        $this->addSql('ALTER TABLE source DROP created_by_id, DROP site_id, DROP name, DROP created, CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
