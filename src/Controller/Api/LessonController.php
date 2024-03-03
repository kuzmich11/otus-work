<?php

namespace App\Controller\Api;

use App\Entity\Lesson;
use App\Service\AsyncService;
use App\Service\LessonService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[Route(path: 'api/lesson')]
#[IsGranted('ROLE_STUDENT')]
class LessonController
{
    private const DEFAULT_PAGE = 0;
    private const DEFAULT_PER_PAGE = 20;
    public function __construct(
        private readonly LessonService $service,
        private readonly AsyncService $asyncService,
    )
    {
    }

    /**
     * @throws Exception
     */
    #[Route(path: '', methods: ['POST'])]
    #[IsGranted('ROLE_METHODIST')]
    public function create(Request $request): JsonResponse
    {
        $name = (string)$request->request->get('lessonName');
        $courseId = $request->request->get('courseId');
        $lesson = $courseId ? $this->service->create($name, $courseId) : null;
        [$data, $code] = $lesson === null ?
            [['success' => false], Response::HTTP_BAD_REQUEST] :
            [['success' => true, 'lessonId' => $lesson->getId()], Response::HTTP_OK];
        return new JsonResponse($data, $code);
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function get(int $id): Response
    {
        $lesson = $this->service->getById($id);
        return new JsonResponse($lesson?->toArray());
    }

    #[Route(path: '', methods: 'GET')]
    #[IsGranted('ROLE_TEACHER')]
    public function getAll(Request $request): Response
    {
        $page = $request->query->get('page');
        $perPage = $request->query->get('perPage');
        $lessons = $this->service->getAll(
            $page ?? self::DEFAULT_PAGE,
            $perPage ?? self::DEFAULT_PER_PAGE);

        return new JsonResponse(empty($lessons)
            ? []
            : array_map(static fn(Lesson $lesson) => $lesson->toArray(), $lessons));
    }

    #[Route(path: '', methods: 'PATCH')]
    #[IsGranted('ROLE_METHODIST')]
    public function update(Request $request): Response
    {
        $id = $request->query->get('id');
        $name = $request->query->get('lessonName');
        $courseId = $request->request->get('courseId');

        $lesson = $this->service->update($id, $name, $courseId);

        [$data, $code] = $lesson === null ?
            [['success' => false], Response::HTTP_BAD_REQUEST] :
            [['success' => true, 'lessonId' => $lesson->getId()], Response::HTTP_OK];
        return new JsonResponse($data, $code);
    }

    #[Route(path: '/{id}',requirements: ['id' => '\d+'], methods: 'DELETE')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): Response
    {
        $result = $this->service->delete($id);

        return new JsonResponse(['success' => $result], $result ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }

    #[Route(path: '/add',methods: 'POST')]
    public function addFromQueue(Request $request): JsonResponse
    {
        $lessonName = $request->request->get('lessonName');
        $courseId = $request->request->get('courseId');
        $count = $request->request->get('count');
        $async = $request->request->get('async');

        if (0 === $async) {
            $createdLessons = $courseId ? $this->service->addLessons($courseId, $lessonName, $count) : null;
            $response = ['created' => $createdLessons];
        } else {
            $message = $this->service->getLessonsMessages($courseId, $lessonName, $count);
            $result = $this->asyncService->publishMultipleToExchange(AsyncService::ADD_LESSON, $message);
            $response = ['success' => $result];
        }

        return new JsonResponse($response);
    }
}