<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Класс миграций уникальных индексов
 */
final class Version20231213112139 extends AbstractMigration
{
    /**
     * Получить описание миграции
     */
    public function getDescription(): string
    {
        return 'Создает уникальные индексы';
    }

    /**
     * @inheritdoc
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX idx__course_name_start_finish ON course (course_name, started_at, finished_at)');
        $this->addSql('CREATE UNIQUE INDEX idx__lesson_name_course ON lesson (lesson_name, course_id)');
        $this->addSql('DROP INDEX idx__skill_name');
        $this->addSql('CREATE UNIQUE INDEX idx__skill_name ON skill (skill)');
        $this->addSql('DROP INDEX idx__student_email');
        $this->addSql('CREATE UNIQUE INDEX idx__student_email ON student (email)');
        $this->addSql('CREATE UNIQUE INDEX idx__task_lesson_task ON task (lesson_id, task)');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx__student_email');
        $this->addSql('CREATE INDEX idx__student_email ON student (email)');
        $this->addSql('DROP INDEX idx__lesson_name_course');
        $this->addSql('DROP INDEX idx__task_lesson_task');
        $this->addSql('DROP INDEX idx__course_name_start_finish');
        $this->addSql('DROP INDEX idx__skill_name');
        $this->addSql('CREATE INDEX idx__skill_name ON skill (skill)');
    }
}
