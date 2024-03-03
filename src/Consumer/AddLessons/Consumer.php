<?php

namespace App\Consumer\AddLessons;

use App\Consumer\AddTasks\Input\Message;
use App\Entity\Course;
use App\Repository\CourseRepository;
use App\Service\LessonService;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Consumer implements ConsumerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface     $validator,
        private readonly LessonService          $lessonService,
        private readonly CourseRepository       $courseRepository
    )
    {
    }

    public function execute(AMQPMessage $msg): int
    {
        try {
            $message = Message::createFromQueue($msg->getBody());
            $errors = $this->validator->validate($message);
            if ($errors->count() > 0) {
                return $this->reject((string)$errors);
            }
        } catch (JsonException $err) {
            return $this->reject($err->getMessage());
        }

        try {
            $this->lessonService->addLessons($message->getCourseId(), $message->getLessonName(), $message->getCount());
            $course = $this->courseRepository->find($message->getCourseId());
            if (!($course instanceof Course)) {
                return $this->reject(sprintf('Course ID %s was not found', $message->getCourseId()));
            }
        } catch (\Throwable $err) {
            return $this->reject($err->getMessage());
        } finally {
            $this->entityManager->clear();
            $this->entityManager->getConnection()->close();
        }

        return self::MSG_ACK;
    }

    private function reject(string $error): int
    {
        echo "Incorrect message: $error";

        return self::MSG_REJECT;
    }
}