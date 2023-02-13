<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class VersionXSeed extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('insert into public.user (id, email, roles, password, info) values (1, \'admin@admin.com\', \'["ROLE_USER","ROLE_ADMIN"]\', \'$2y$13$rFWSlytzgEEPOGJOEN5daOc7VcI5.7.VrWHzcX7NVtnYUrmSIp4.m\', \'{}\')');
        $this->addSql('ALTER SEQUENCE user_id_seq RESTART WITH 2;');
        $this->addSql('insert into public.dish_category (id, name) values (1, \'Завтрак\'),(2, \'Перекус\'),(3, \'Обед\'),(4, \'Полдник\'),(5, \'Ужин\')');
        $this->addSql('ALTER SEQUENCE dish_category_id_seq RESTART WITH 6;');
    }

    public function down(Schema $schema): void
    {

    }
}
