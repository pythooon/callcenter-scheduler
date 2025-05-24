<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250523090425 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE agent (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE agent_queue (agent_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', queue_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', INDEX IDX_4653571B3414710B (agent_id), INDEX IDX_4653571B477B5BAE (queue_id), PRIMARY KEY(agent_id, queue_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE call_history (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', agent_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', queue_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', date DATETIME NOT NULL, calls_count INT NOT NULL, INDEX IDX_F21FE97E3414710B (agent_id), INDEX IDX_F21FE97E477B5BAE (queue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE efficiency (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', agent_id BINARY(16) DEFAULT NULL COMMENT '(DC2Type:uuid)', queue_id BINARY(16) DEFAULT NULL COMMENT '(DC2Type:uuid)', score DOUBLE PRECISION NOT NULL, INDEX IDX_C06D87493414710B (agent_id), INDEX IDX_C06D8749477B5BAE (queue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE predictions (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', queue_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', date DATE NOT NULL, time TIME NOT NULL, occupancy INT NOT NULL, INDEX IDX_8E87BCE6477B5BAE (queue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE queue (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE shift (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', agent_id BINARY(16) DEFAULT NULL COMMENT '(DC2Type:uuid)', queue_id BINARY(16) DEFAULT NULL COMMENT '(DC2Type:uuid)', start DATETIME NOT NULL, end DATETIME NOT NULL, INDEX IDX_A50B3B453414710B (agent_id), INDEX IDX_A50B3B45477B5BAE (queue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE agent_queue ADD CONSTRAINT FK_4653571B3414710B FOREIGN KEY (agent_id) REFERENCES agent (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE agent_queue ADD CONSTRAINT FK_4653571B477B5BAE FOREIGN KEY (queue_id) REFERENCES queue (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE call_history ADD CONSTRAINT FK_F21FE97E3414710B FOREIGN KEY (agent_id) REFERENCES agent (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE call_history ADD CONSTRAINT FK_F21FE97E477B5BAE FOREIGN KEY (queue_id) REFERENCES queue (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE efficiency ADD CONSTRAINT FK_C06D87493414710B FOREIGN KEY (agent_id) REFERENCES agent (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE efficiency ADD CONSTRAINT FK_C06D8749477B5BAE FOREIGN KEY (queue_id) REFERENCES queue (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE predictions ADD CONSTRAINT FK_8E87BCE6477B5BAE FOREIGN KEY (queue_id) REFERENCES queue (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shift ADD CONSTRAINT FK_A50B3B453414710B FOREIGN KEY (agent_id) REFERENCES agent (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shift ADD CONSTRAINT FK_A50B3B45477B5BAE FOREIGN KEY (queue_id) REFERENCES queue (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE agent_queue DROP FOREIGN KEY FK_4653571B3414710B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE agent_queue DROP FOREIGN KEY FK_4653571B477B5BAE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE call_history DROP FOREIGN KEY FK_F21FE97E3414710B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE call_history DROP FOREIGN KEY FK_F21FE97E477B5BAE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE efficiency DROP FOREIGN KEY FK_C06D87493414710B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE efficiency DROP FOREIGN KEY FK_C06D8749477B5BAE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE predictions DROP FOREIGN KEY FK_8E87BCE6477B5BAE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shift DROP FOREIGN KEY FK_A50B3B453414710B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shift DROP FOREIGN KEY FK_A50B3B45477B5BAE
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE agent
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE agent_queue
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE call_history
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE efficiency
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE predictions
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE queue
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE shift
        SQL);
    }
}
