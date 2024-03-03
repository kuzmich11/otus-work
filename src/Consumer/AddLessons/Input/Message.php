<?php

namespace App\Consumer\AddTasks\Input;


use Symfony\Component\Validator\Constraints as Assert;

class Message
{
    #[Assert\Type('numeric')]
    private int $courseId;

    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    private string $lessonName;

    #[Assert\Type('numeric')]
    private int $count;

    public static function createFromQueue(string $messageBody): self
    {
        $message = json_decode($messageBody, true, 512, JSON_THROW_ON_ERROR);
        $result = new self();
        $result->courseId = $message['courseId'];
        $result->lessonName = $message['lessonName'];
        $result->count = $message['count'];

        return $result;
    }

    public function getCourseId(): int
    {
        return $this->courseId;
    }

    public function getLessonName(): string
    {
        return $this->lessonName;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}