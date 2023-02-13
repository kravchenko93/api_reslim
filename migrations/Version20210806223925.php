<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210806223925 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE public.dish_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.dish_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE dish_ingredient_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE dish_step_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.ingredient_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reset_password_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_dish_choice_per_date_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_dish_excluded_per_date_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.user_subscription_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.yookassa_payment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE dish (id INT NOT NULL, dish_category_id INT DEFAULT NULL, name VARCHAR(180) NOT NULL, description TEXT NOT NULL, image VARCHAR(255) NOT NULL, hide BOOLEAN DEFAULT \'false\' NOT NULL, cooking_tools TEXT NOT NULL, weight INT NOT NULL, cooking_time TEXT NOT NULL, complexity TEXT DEFAULT NULL, proteins INT NOT NULL, fats INT NOT NULL, carbohydrates INT NOT NULL, vitamins JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957D8CB85E237E06 ON dish (name)');
        $this->addSql('CREATE INDEX IDX_957D8CB8C057AE07 ON dish (dish_category_id)');
        $this->addSql('COMMENT ON COLUMN dish.vitamins IS \'(DC2Type:json_array)\'');
        $this->addSql('CREATE TABLE dish_category (id INT NOT NULL, name VARCHAR(180) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1FB098AA5E237E06 ON dish_category (name)');
        $this->addSql('CREATE TABLE dish_ingredient (id INT NOT NULL, dish_id INT NOT NULL, ingredient_id INT NOT NULL, sort INT NOT NULL, quantity TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_77196056148EB0CB ON dish_ingredient (dish_id)');
        $this->addSql('CREATE INDEX IDX_77196056933FE08C ON dish_ingredient (ingredient_id)');
        $this->addSql('CREATE TABLE dish_step (id INT NOT NULL, dish_id INT NOT NULL, sort INT NOT NULL, text TEXT NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C9BB412D148EB0CB ON dish_step (dish_id)');
        $this->addSql('CREATE TABLE ingredient (id INT NOT NULL, name VARCHAR(180) NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6BAF78705E237E06 ON ingredient (name)');
        $this->addSql('CREATE TABLE log (id INT NOT NULL, user_id INT DEFAULT NULL, message TEXT NOT NULL, context TEXT NOT NULL, level SMALLINT NOT NULL, level_name VARCHAR(50) NOT NULL, channel VARCHAR(255) NOT NULL, extra TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, formatted TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8F3F68C5A76ED395 ON log (user_id)');
        $this->addSql('COMMENT ON COLUMN log.context IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN log.extra IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE reset_password_request (id INT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('COMMENT ON COLUMN reset_password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reset_password_request.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, info JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".roles IS \'(DC2Type:json_array)\'');
        $this->addSql('CREATE TABLE user_dish_choice_per_date (id INT NOT NULL, dish_id INT NOT NULL, user_id INT NOT NULL, date DATE NOT NULL, in_fact BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B47B120A148EB0CB ON user_dish_choice_per_date (dish_id)');
        $this->addSql('CREATE INDEX IDX_B47B120AA76ED395 ON user_dish_choice_per_date (user_id)');
        $this->addSql('CREATE UNIQUE INDEX user_dish_choice_per_date_unique ON user_dish_choice_per_date (dish_id, user_id, date)');
        $this->addSql('CREATE TABLE user_dish_excluded_per_date (id INT NOT NULL, dish_id INT NOT NULL, user_id INT NOT NULL, date DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BC21F011148EB0CB ON user_dish_excluded_per_date (dish_id)');
        $this->addSql('CREATE INDEX IDX_BC21F011A76ED395 ON user_dish_excluded_per_date (user_id)');
        $this->addSql('CREATE UNIQUE INDEX user_dish_excluded_per_date_unique ON user_dish_excluded_per_date (dish_id, user_id, date)');
        $this->addSql('CREATE TABLE user_dish_rating (dish_id INT NOT NULL, user_id INT NOT NULL, rating INT NOT NULL, PRIMARY KEY(dish_id, user_id))');
        $this->addSql('CREATE INDEX IDX_7995E8B8148EB0CB ON user_dish_rating (dish_id)');
        $this->addSql('CREATE INDEX IDX_7995E8B8A76ED395 ON user_dish_rating (user_id)');
        $this->addSql('CREATE TABLE user_subscription (id INT NOT NULL, user_id INT NOT NULL, type VARCHAR(255) NOT NULL, payment_type VARCHAR(255) NOT NULL, date_start TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_finish TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, paid BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_230A18D1A76ED395 ON user_subscription (user_id)');
        $this->addSql('CREATE TABLE yookassa_payment (id INT NOT NULL, user_subscription_id INT NOT NULL, yookassa_id VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, amount DOUBLE PRECISION NOT NULL, paid BOOLEAN NOT NULL, payment_method_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_35702B3EC4C05E9A ON yookassa_payment (yookassa_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_35702B3E88C4EB53 ON yookassa_payment (user_subscription_id)');
        $this->addSql('ALTER TABLE dish ADD CONSTRAINT FK_957D8CB8C057AE07 FOREIGN KEY (dish_category_id) REFERENCES dish_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dish_ingredient ADD CONSTRAINT FK_77196056148EB0CB FOREIGN KEY (dish_id) REFERENCES dish (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dish_ingredient ADD CONSTRAINT FK_77196056933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dish_step ADD CONSTRAINT FK_C9BB412D148EB0CB FOREIGN KEY (dish_id) REFERENCES dish (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE log ADD CONSTRAINT FK_8F3F68C5A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_dish_choice_per_date ADD CONSTRAINT FK_B47B120A148EB0CB FOREIGN KEY (dish_id) REFERENCES dish (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_dish_choice_per_date ADD CONSTRAINT FK_B47B120AA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_dish_excluded_per_date ADD CONSTRAINT FK_BC21F011148EB0CB FOREIGN KEY (dish_id) REFERENCES dish (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_dish_excluded_per_date ADD CONSTRAINT FK_BC21F011A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_dish_rating ADD CONSTRAINT FK_7995E8B8148EB0CB FOREIGN KEY (dish_id) REFERENCES dish (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_dish_rating ADD CONSTRAINT FK_7995E8B8A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_subscription ADD CONSTRAINT FK_230A18D1A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE yookassa_payment ADD CONSTRAINT FK_35702B3E88C4EB53 FOREIGN KEY (user_subscription_id) REFERENCES user_subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dish_ingredient DROP CONSTRAINT FK_77196056148EB0CB');
        $this->addSql('ALTER TABLE dish_step DROP CONSTRAINT FK_C9BB412D148EB0CB');
        $this->addSql('ALTER TABLE user_dish_choice_per_date DROP CONSTRAINT FK_B47B120A148EB0CB');
        $this->addSql('ALTER TABLE user_dish_excluded_per_date DROP CONSTRAINT FK_BC21F011148EB0CB');
        $this->addSql('ALTER TABLE user_dish_rating DROP CONSTRAINT FK_7995E8B8148EB0CB');
        $this->addSql('ALTER TABLE dish DROP CONSTRAINT FK_957D8CB8C057AE07');
        $this->addSql('ALTER TABLE dish_ingredient DROP CONSTRAINT FK_77196056933FE08C');
        $this->addSql('ALTER TABLE log DROP CONSTRAINT FK_8F3F68C5A76ED395');
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE user_dish_choice_per_date DROP CONSTRAINT FK_B47B120AA76ED395');
        $this->addSql('ALTER TABLE user_dish_excluded_per_date DROP CONSTRAINT FK_BC21F011A76ED395');
        $this->addSql('ALTER TABLE user_dish_rating DROP CONSTRAINT FK_7995E8B8A76ED395');
        $this->addSql('ALTER TABLE user_subscription DROP CONSTRAINT FK_230A18D1A76ED395');
        $this->addSql('ALTER TABLE yookassa_payment DROP CONSTRAINT FK_35702B3E88C4EB53');
        $this->addSql('DROP SEQUENCE public.dish_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.dish_category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE dish_ingredient_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE dish_step_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.ingredient_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE log_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reset_password_request_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.user_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_dish_choice_per_date_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_dish_excluded_per_date_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.user_subscription_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.yookassa_payment_id_seq CASCADE');
        $this->addSql('DROP TABLE dish');
        $this->addSql('DROP TABLE dish_category');
        $this->addSql('DROP TABLE dish_ingredient');
        $this->addSql('DROP TABLE dish_step');
        $this->addSql('DROP TABLE ingredient');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_dish_choice_per_date');
        $this->addSql('DROP TABLE user_dish_excluded_per_date');
        $this->addSql('DROP TABLE user_dish_rating');
        $this->addSql('DROP TABLE user_subscription');
        $this->addSql('DROP TABLE yookassa_payment');
    }
}
