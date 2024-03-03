<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Класс миграций основных сущностей
 */
final class Version20231213110905 extends AbstractMigration
{
    /**
     * Получить описание миграции
     */
    public function getDescription(): string
    {
        return 'Создает таблицы: course, course_student, lesson, ratio, score_skill, score_task, skill, student';
    }

    /**
     * @inheritdoc
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE course (id SERIAL NOT NULL, course_name VARCHAR(255) NOT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, finished_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx__course_name ON course (course_name)');
        $this->addSql('CREATE INDEX idx__course_created_at ON course (created_at)');
        $this->addSql('CREATE INDEX idx__course_updated_at ON course (updated_at)');
        $this->addSql('CREATE TABLE course_student (course_id SERIAL NOT NULL, student_id INT NOT NULL, PRIMARY KEY(course_id, student_id))');
        $this->addSql('CREATE INDEX IDX_BFE0AADF591CC992 ON course_student (course_id)');
        $this->addSql('CREATE INDEX IDX_BFE0AADFCB944F1A ON course_student (student_id)');
        $this->addSql('CREATE TABLE lesson (id SERIAL NOT NULL, course_id INT DEFAULT NULL, lesson_name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx__lesson_name ON lesson (lesson_name)');
        $this->addSql('CREATE INDEX idx__lesson_course ON lesson (course_id)');
        $this->addSql('CREATE INDEX idx__lesson_created_at ON lesson (created_at)');
        $this->addSql('CREATE INDEX idx__lesson_updated_at ON lesson (updated_at)');
        $this->addSql('CREATE TABLE ratio (id SERIAL NOT NULL, task_id INT DEFAULT NULL, skill_id INT DEFAULT NULL, ratio INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx__ratio_task_id ON ratio (task_id)');
        $this->addSql('CREATE INDEX idx__ratio_skill_id ON ratio (skill_id)');
        $this->addSql('CREATE UNIQUE INDEX idx__ratio_task_skill ON ratio (task_id, skill_id)');
        $this->addSql('CREATE TABLE score_skill (id SERIAL NOT NULL, student_id INT DEFAULT NULL, skill_id INT DEFAULT NULL, score INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx__score_skill_student_id ON score_skill (student_id)');
        $this->addSql('CREATE INDEX idx__score_skill_skill_id ON score_skill (skill_id)');
        $this->addSql('CREATE UNIQUE INDEX idx__score_skill_student_skill ON score_skill (student_id, skill_id)');
        $this->addSql('CREATE TABLE score_task (id SERIAL NOT NULL, student_id INT DEFAULT NULL, task_id INT DEFAULT NULL, score INT NOT NULL, completed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx__score_task_student_id ON score_task (student_id)');
        $this->addSql('CREATE INDEX idx__score_task_task_id ON score_task (task_id)');
        $this->addSql('CREATE UNIQUE INDEX idx__score_task_student_task ON score_task (student_id, task_id)');
        $this->addSql('CREATE TABLE skill (id SERIAL NOT NULL, skill VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx__skill_name ON skill (skill)');
        $this->addSql('CREATE TABLE student (id SERIAL NOT NULL, name VARCHAR(32) DEFAULT NULL, lastname VARCHAR(32) DEFAULT NULL, email VARCHAR(45) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx__student_name ON student (name)');
        $this->addSql('CREATE INDEX idx__student_lastname ON student (lastname)');
        $this->addSql('CREATE INDEX idx__student_email ON student (email)');
        $this->addSql('CREATE INDEX idx__student_created_at ON student (created_at)');
        $this->addSql('CREATE INDEX idx__student_updated_at ON student (updated_at)');
        $this->addSql('CREATE TABLE task (id SERIAL NOT NULL, lesson_id INT DEFAULT NULL, task VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx__task_name ON task (task)');
        $this->addSql('CREATE INDEX idx__task_lesson_id ON task (lesson_id)');
        $this->addSql('CREATE INDEX idx__task_created_at ON task (created_at)');
        $this->addSql('CREATE INDEX idx__task_updated_at ON task (updated_at)');
        $this->addSql('ALTER TABLE course_student ADD CONSTRAINT FK__course_student_course_id FOREIGN KEY (course_id) REFERENCES course (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE course_student ADD CONSTRAINT FK__course_student_student_id FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK__lesson_course_id FOREIGN KEY (course_id) REFERENCES course (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ratio ADD CONSTRAINT FK__ratio_task_id FOREIGN KEY (task_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ratio ADD CONSTRAINT FK__ratio_skill_id FOREIGN KEY (skill_id) REFERENCES skill (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE score_skill ADD CONSTRAINT FK__score_skill_student_id FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE score_skill ADD CONSTRAINT FK__score_skill_skill_id FOREIGN KEY (skill_id) REFERENCES skill (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE score_task ADD CONSTRAINT FK__score_task_student_id FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE score_task ADD CONSTRAINT FK__score_task_task_id FOREIGN KEY (task_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK__task_lesson_id FOREIGN KEY (lesson_id) REFERENCES lesson (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course_student DROP CONSTRAINT FK__course_student_course_id');
        $this->addSql('ALTER TABLE course_student DROP CONSTRAINT FK__course_student_student_id');
        $this->addSql('ALTER TABLE lesson DROP CONSTRAINT FK__lesson_course_id');
        $this->addSql('ALTER TABLE ratio DROP CONSTRAINT FK__ratio_task_id');
        $this->addSql('ALTER TABLE ratio DROP CONSTRAINT FK__ratio_skill_id');
        $this->addSql('ALTER TABLE score_skill DROP CONSTRAINT FK__score_skill_student_id');
        $this->addSql('ALTER TABLE score_skill DROP CONSTRAINT FK__score_skill_skill_id');
        $this->addSql('ALTER TABLE score_task DROP CONSTRAINT FK__score_task_student_id');
        $this->addSql('ALTER TABLE score_task DROP CONSTRAINT FK__score_task_task_id');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK__task_lesson_id');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE course_student');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('DROP TABLE ratio');
        $this->addSql('DROP TABLE score_skill');
        $this->addSql('DROP TABLE score_task');
        $this->addSql('DROP TABLE skill');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE task');
    }
}
