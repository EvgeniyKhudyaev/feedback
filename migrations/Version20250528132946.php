<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250528132946 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "client_user" (id SERIAL NOT NULL, uuid UUID NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(20) DEFAULT NULL, telegram VARCHAR(100) DEFAULT NULL, status VARCHAR(20) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C0F152BD17F50A6 ON "client_user" (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C0F152BE7927C74 ON "client_user" (email)');
        $this->addSql('CREATE TABLE "feedback" (id SERIAL NOT NULL, uuid UUID NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, scope VARCHAR(50) NOT NULL, status VARCHAR(50) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D2294458D17F50A6 ON "feedback" (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D22944585E237E06 ON "feedback" (name)');
        $this->addSql('CREATE TABLE feedback_field (id SERIAL NOT NULL, feedback_id INT NOT NULL, uuid UUID NOT NULL, code VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, required BOOLEAN NOT NULL, sort_order INT NOT NULL, status VARCHAR(50) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CF54DC3DD249A887 ON feedback_field (feedback_id)');
        $this->addSql('CREATE TABLE feedback_field_answer (id SERIAL NOT NULL, field_id INT NOT NULL, responder_id INT NOT NULL, uuid UUID NOT NULL, value TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6C1B223B443707B0 ON feedback_field_answer (field_id)');
        $this->addSql('CREATE INDEX IDX_6C1B223B37395ADB ON feedback_field_answer (responder_id)');
        $this->addSql('CREATE TABLE feedback_field_answer_file (id SERIAL NOT NULL, file_id INT NOT NULL, answer_id INT NOT NULL, creator_id INT NOT NULL, status VARCHAR(50) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D44BC01393CB796C ON feedback_field_answer_file (file_id)');
        $this->addSql('CREATE INDEX IDX_D44BC013AA334807 ON feedback_field_answer_file (answer_id)');
        $this->addSql('CREATE INDEX IDX_D44BC01361220EA6 ON feedback_field_answer_file (creator_id)');
        $this->addSql('CREATE TABLE feedback_field_option (id SERIAL NOT NULL, field_id INT NOT NULL, uuid UUID NOT NULL, label VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, sort_order INT NOT NULL, status VARCHAR(50) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EC4068AE443707B0 ON feedback_field_option (field_id)');
        $this->addSql('CREATE TABLE feedback_manager (id SERIAL NOT NULL, feedback_id INT NOT NULL, editor_id INT NOT NULL, is_active BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6B7D051AD249A887 ON feedback_manager (feedback_id)');
        $this->addSql('CREATE INDEX IDX_6B7D051A6995AC4C ON feedback_manager (editor_id)');
        $this->addSql('CREATE TABLE feedback_target (id SERIAL NOT NULL, feedback_id INT NOT NULL, target_type VARCHAR(50) NOT NULL, target INT NOT NULL, is_active BOOLEAN DEFAULT true NOT NULL, created_at VARCHAR(255) NOT NULL, updated_at VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7B231BB2D249A887 ON feedback_target (feedback_id)');
        $this->addSql('CREATE TABLE file (id SERIAL NOT NULL, owner_id INT NOT NULL, uuid UUID NOT NULL, name VARCHAR(255) NOT NULL, path VARCHAR(1024) NOT NULL, mime_type VARCHAR(100) NOT NULL, size INT NOT NULL, extension VARCHAR(100) NOT NULL, hash VARCHAR(128) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8C9F3610D1B862B8 ON file (hash)');
        $this->addSql('CREATE INDEX IDX_8C9F36107E3C61F9 ON file (owner_id)');
        $this->addSql('CREATE TABLE message_log (id SERIAL NOT NULL, client_user_id INT DEFAULT NULL, feedback_id INT DEFAULT NULL, uuid UUID NOT NULL, type VARCHAR(50) NOT NULL, subject VARCHAR(255) NOT NULL, message TEXT NOT NULL, status VARCHAR(50) NOT NULL, error TEXT NOT NULL, sent_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A60AE229F55397E8 ON message_log (client_user_id)');
        $this->addSql('CREATE INDEX IDX_A60AE229D249A887 ON message_log (feedback_id)');
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
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, avatar_id INT DEFAULT NULL, uuid UUID NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(20) DEFAULT NULL, telegram VARCHAR(100) DEFAULT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(50) NOT NULL, status VARCHAR(20) NOT NULL, is_verified BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649D17F50A6 ON "user" (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE INDEX IDX_8D93D64986383B10 ON "user" (avatar_id)');
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
        $this->addSql('ALTER TABLE feedback_field ADD CONSTRAINT FK_CF54DC3DD249A887 FOREIGN KEY (feedback_id) REFERENCES "feedback" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback_field_answer ADD CONSTRAINT FK_6C1B223B443707B0 FOREIGN KEY (field_id) REFERENCES feedback_field (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback_field_answer ADD CONSTRAINT FK_6C1B223B37395ADB FOREIGN KEY (responder_id) REFERENCES "client_user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback_field_answer_file ADD CONSTRAINT FK_D44BC01393CB796C FOREIGN KEY (file_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback_field_answer_file ADD CONSTRAINT FK_D44BC013AA334807 FOREIGN KEY (answer_id) REFERENCES feedback_field_answer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback_field_answer_file ADD CONSTRAINT FK_D44BC01361220EA6 FOREIGN KEY (creator_id) REFERENCES "client_user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback_field_option ADD CONSTRAINT FK_EC4068AE443707B0 FOREIGN KEY (field_id) REFERENCES feedback_field (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback_manager ADD CONSTRAINT FK_6B7D051AD249A887 FOREIGN KEY (feedback_id) REFERENCES "feedback" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback_manager ADD CONSTRAINT FK_6B7D051A6995AC4C FOREIGN KEY (editor_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback_target ADD CONSTRAINT FK_7B231BB2D249A887 FOREIGN KEY (feedback_id) REFERENCES "feedback" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36107E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message_log ADD CONSTRAINT FK_A60AE229F55397E8 FOREIGN KEY (client_user_id) REFERENCES "client_user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message_log ADD CONSTRAINT FK_A60AE229D249A887 FOREIGN KEY (feedback_id) REFERENCES "feedback" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2AC8DE0F FOREIGN KEY (service_type_id) REFERENCES service_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE service_history ADD CONSTRAINT FK_E83E22D7ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE service_history ADD CONSTRAINT FK_E83E22D761220EA6 FOREIGN KEY (creator_id) REFERENCES "client_user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE service_history ADD CONSTRAINT FK_E83E22D75D83CC1 FOREIGN KEY (state_id) REFERENCES service_state (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D64986383B10 FOREIGN KEY (avatar_id) REFERENCES file (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE feedback_field DROP CONSTRAINT FK_CF54DC3DD249A887');
        $this->addSql('ALTER TABLE feedback_field_answer DROP CONSTRAINT FK_6C1B223B443707B0');
        $this->addSql('ALTER TABLE feedback_field_answer DROP CONSTRAINT FK_6C1B223B37395ADB');
        $this->addSql('ALTER TABLE feedback_field_answer_file DROP CONSTRAINT FK_D44BC01393CB796C');
        $this->addSql('ALTER TABLE feedback_field_answer_file DROP CONSTRAINT FK_D44BC013AA334807');
        $this->addSql('ALTER TABLE feedback_field_answer_file DROP CONSTRAINT FK_D44BC01361220EA6');
        $this->addSql('ALTER TABLE feedback_field_option DROP CONSTRAINT FK_EC4068AE443707B0');
        $this->addSql('ALTER TABLE feedback_manager DROP CONSTRAINT FK_6B7D051AD249A887');
        $this->addSql('ALTER TABLE feedback_manager DROP CONSTRAINT FK_6B7D051A6995AC4C');
        $this->addSql('ALTER TABLE feedback_target DROP CONSTRAINT FK_7B231BB2D249A887');
        $this->addSql('ALTER TABLE file DROP CONSTRAINT FK_8C9F36107E3C61F9');
        $this->addSql('ALTER TABLE message_log DROP CONSTRAINT FK_A60AE229F55397E8');
        $this->addSql('ALTER TABLE message_log DROP CONSTRAINT FK_A60AE229D249A887');
        $this->addSql('ALTER TABLE service DROP CONSTRAINT FK_E19D9AD2AC8DE0F');
        $this->addSql('ALTER TABLE service_history DROP CONSTRAINT FK_E83E22D7ED5CA9E6');
        $this->addSql('ALTER TABLE service_history DROP CONSTRAINT FK_E83E22D761220EA6');
        $this->addSql('ALTER TABLE service_history DROP CONSTRAINT FK_E83E22D75D83CC1');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D64986383B10');
        $this->addSql('DROP TABLE "client_user"');
        $this->addSql('DROP TABLE "feedback"');
        $this->addSql('DROP TABLE feedback_field');
        $this->addSql('DROP TABLE feedback_field_answer');
        $this->addSql('DROP TABLE feedback_field_answer_file');
        $this->addSql('DROP TABLE feedback_field_option');
        $this->addSql('DROP TABLE feedback_manager');
        $this->addSql('DROP TABLE feedback_target');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE message_log');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE service_history');
        $this->addSql('DROP TABLE service_state');
        $this->addSql('DROP TABLE service_type');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
