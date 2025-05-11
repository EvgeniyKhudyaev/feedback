<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250511193809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client_user (id SERIAL NOT NULL, uuid UUID NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(20) DEFAULT NULL, telegram VARCHAR(100) DEFAULT NULL, status VARCHAR(20) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C0F152BD17F50A6 ON client_user (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C0F152BE7927C74 ON client_user (email)');
        $this->addSql('CREATE TABLE service (id SERIAL NOT NULL, service_type_id INT NOT NULL, uuid UUID NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, status VARCHAR(50) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E19D9AD2D17F50A6 ON service (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E19D9AD25E237E06 ON service (name)');
        $this->addSql('CREATE INDEX IDX_E19D9AD2AC8DE0F ON service (service_type_id)');
        $this->addSql('CREATE TABLE service_history (id SERIAL NOT NULL, service_id INT NOT NULL, creator_id INT NOT NULL, state_id INT NOT NULL, uuid UUID NOT NULL, note TEXT NOT NULL, status VARCHAR(50) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E83E22D7D17F50A6 ON service_history (uuid)');
        $this->addSql('CREATE INDEX IDX_E83E22D7ED5CA9E6 ON service_history (service_id)');
        $this->addSql('CREATE INDEX IDX_E83E22D761220EA6 ON service_history (creator_id)');
        $this->addSql('CREATE INDEX IDX_E83E22D75D83CC1 ON service_history (state_id)');
        $this->addSql('CREATE TABLE service_state (id SERIAL NOT NULL, uuid UUID NOT NULL, name VARCHAR(50) NOT NULL, description TEXT NOT NULL, status VARCHAR(50) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AE13F1CD17F50A6 ON service_state (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AE13F1C5E237E06 ON service_state (name)');
        $this->addSql('CREATE TABLE service_type (id SERIAL NOT NULL, uuid UUID NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, status VARCHAR(50) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_429DE3C5D17F50A6 ON service_type (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_429DE3C55E237E06 ON service_type (name)');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, uuid UUID NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, avatar VARCHAR(512) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, telegram VARCHAR(100) DEFAULT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(50) NOT NULL, status VARCHAR(20) NOT NULL, is_verified BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649D17F50A6 ON "user" (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2AC8DE0F FOREIGN KEY (service_type_id) REFERENCES service_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE service_history ADD CONSTRAINT FK_E83E22D7ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE service_history ADD CONSTRAINT FK_E83E22D761220EA6 FOREIGN KEY (creator_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE service_history ADD CONSTRAINT FK_E83E22D75D83CC1 FOREIGN KEY (state_id) REFERENCES service_state (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service DROP CONSTRAINT FK_E19D9AD2AC8DE0F');
        $this->addSql('ALTER TABLE service_history DROP CONSTRAINT FK_E83E22D7ED5CA9E6');
        $this->addSql('ALTER TABLE service_history DROP CONSTRAINT FK_E83E22D761220EA6');
        $this->addSql('ALTER TABLE service_history DROP CONSTRAINT FK_E83E22D75D83CC1');
        $this->addSql('DROP TABLE client_user');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE service_history');
        $this->addSql('DROP TABLE service_state');
        $this->addSql('DROP TABLE service_type');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
