<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240124171846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE score_skill DROP CONSTRAINT fk__score_skill_student_id');
        $this->addSql('ALTER TABLE score_task DROP CONSTRAINT fk__score_task_student_id');
        $this->addSql('DROP SEQUENCE course_student_course_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE student_id_seq CASCADE');
        $this->addSql('CREATE TABLE course_users (course_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(course_id, user_id))');
        $this->addSql('CREATE INDEX IDX_FD28A201591CC992 ON course_users (course_id)');
        $this->addSql('CREATE INDEX IDX_FD28A201A76ED395 ON course_users (user_id)');
        $this->addSql('CREATE TABLE users (id SERIAL NOT NULL, name VARCHAR(32) DEFAULT NULL, lastname VARCHAR(32) DEFAULT NULL, login VARCHAR(32) NOT NULL, password VARCHAR(120) NOT NULL, email VARCHAR(45) NOT NULL, roles JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx__users_name ON users (name)');
        $this->addSql('CREATE INDEX idx__users_lastname ON users (lastname)');
        $this->addSql('CREATE INDEX idx__users_created_at ON users (created_at)');
        $this->addSql('CREATE INDEX idx__users_updated_at ON users (updated_at)');
        $this->addSql('CREATE UNIQUE INDEX idx__users_login ON users (login)');
        $this->addSql('CREATE UNIQUE INDEX idx__users_email ON users (email)');
        $this->addSql('ALTER TABLE course_users ADD CONSTRAINT FK_FD28A201591CC992 FOREIGN KEY (course_id) REFERENCES course (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE course_users ADD CONSTRAINT FK_FD28A201A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE course_student DROP CONSTRAINT fk__course_student_course_id');
        $this->addSql('ALTER TABLE course_student DROP CONSTRAINT fk__course_student_student_id');
        $this->addSql('DROP TABLE course_student');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP INDEX idx__score_skill_student_skill');
        $this->addSql('DROP INDEX idx__score_skill_student_id');
        $this->addSql('ALTER TABLE score_skill RENAME COLUMN student_id TO user_id');
        $this->addSql('ALTER TABLE score_skill ADD CONSTRAINT FK_E77128FFA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx__score_skill_user_id ON score_skill (user_id)');
        $this->addSql('CREATE UNIQUE INDEX idx__score_skill_user_skill ON score_skill (user_id, skill_id)');
        $this->addSql('DROP INDEX idx__score_task_student_task');
        $this->addSql('DROP INDEX idx__score_task_student_id');
        $this->addSql('ALTER TABLE score_task RENAME COLUMN student_id TO user_id');
        $this->addSql('ALTER TABLE score_task ADD CONSTRAINT FK_78665A53A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx__score_task_user_id ON score_task (user_id)');
        $this->addSql('CREATE UNIQUE INDEX idx__score_task_user_task ON score_task (user_id, task_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE score_skill DROP CONSTRAINT FK_E77128FFA76ED395');
        $this->addSql('ALTER TABLE score_task DROP CONSTRAINT FK_78665A53A76ED395');
        $this->addSql('CREATE SEQUENCE course_student_course_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE student_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE course_student (course_id SERIAL NOT NULL, student_id INT NOT NULL, PRIMARY KEY(course_id, student_id))');
        $this->addSql('CREATE INDEX idx_bfe0aadfcb944f1a ON course_student (student_id)');
        $this->addSql('CREATE INDEX idx_bfe0aadf591cc992 ON course_student (course_id)');
        $this->addSql('CREATE TABLE student (id SERIAL NOT NULL, name VARCHAR(32) DEFAULT NULL, lastname VARCHAR(32) DEFAULT NULL, email VARCHAR(45) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX idx__student_email ON student (email)');
        $this->addSql('CREATE INDEX idx__student_updated_at ON student (updated_at)');
        $this->addSql('CREATE INDEX idx__student_created_at ON student (created_at)');
        $this->addSql('CREATE INDEX idx__student_lastname ON student (lastname)');
        $this->addSql('CREATE INDEX idx__student_name ON student (name)');
        $this->addSql('ALTER TABLE course_student ADD CONSTRAINT fk__course_student_course_id FOREIGN KEY (course_id) REFERENCES course (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE course_student ADD CONSTRAINT fk__course_student_student_id FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE course_users DROP CONSTRAINT FK_FD28A201591CC992');
        $this->addSql('ALTER TABLE course_users DROP CONSTRAINT FK_FD28A201A76ED395');
        $this->addSql('DROP TABLE course_users');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP INDEX idx__score_skill_user_id');
        $this->addSql('DROP INDEX idx__score_skill_user_skill');
        $this->addSql('ALTER TABLE score_skill RENAME COLUMN user_id TO student_id');
        $this->addSql('ALTER TABLE score_skill ADD CONSTRAINT fk__score_skill_student_id FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX idx__score_skill_student_skill ON score_skill (student_id, skill_id)');
        $this->addSql('CREATE INDEX idx__score_skill_student_id ON score_skill (student_id)');
        $this->addSql('DROP INDEX idx__score_task_user_id');
        $this->addSql('DROP INDEX idx__score_task_user_task');
        $this->addSql('ALTER TABLE score_task RENAME COLUMN user_id TO student_id');
        $this->addSql('ALTER TABLE score_task ADD CONSTRAINT fk__score_task_student_id FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX idx__score_task_student_task ON score_task (student_id, task_id)');
        $this->addSql('CREATE INDEX idx__score_task_student_id ON score_task (student_id)');
    }
}
