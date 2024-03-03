<?php

namespace App\DTO;

use JsonException;

class AddLessonsDTO
{
    private array $payload;

    public function __construct(int $courseId, string $lessonName, int $count)
    {
        $this->payload = ['courseId' => $courseId, 'lessonName' => $lessonName, 'count' => $count];
    }

    /**
     * @throws JsonException
     */
    public function toAMQPMessage(): string
    {
        return json_encode($this->payload, JSON_THROW_ON_ERROR);
    }
}