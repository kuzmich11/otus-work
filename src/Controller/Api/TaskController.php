<?php

namespace App\Controller\Api;

use App\Entity\Task;
use App\Service\TaskService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[Route(path: 'api/task')]
#[IsGranted('ROLE_STUDENT')]
class TaskController
{
    private const DEFAULT_PAGE = 0;
    private const DEFAULT_PER_PAGE = 20;
    public function __construct(
        private readonly TaskService $service
    )
    {
    }

    /**
     * @throws Exception
     */
    #[Route(path: '', methods: ['POST'])]
    #[IsGranted('ROLE_TEACHER')]
    public function create(Request $request): JsonResponse
    {
        $task = (string)$request->request->get('task');
        $lessonId = $request->request->get('lessonId');
        $taskId = $lessonId ? $this->service->create($task, $lessonId) : null;
        [$data, $code] = $taskId === null ?
            [['success' => false], Response::HTTP_BAD_REQUEST] :
            [['success' => true, 'taskId' => $taskId], Response::HTTP_OK];
        return new JsonResponse($data, $code);
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function get(int $id): Response
    {
        $task = $this->service->getById($id);
        return new JsonResponse($task?->toArray());
    }

    #[Route(path: '', methods: 'GET')]
    public function getAll(Request $request): Response
    {
        $page = $request->query->get('page');
        $perPage = $request->query->get('perPage');
        $tasks = $this->service->getAll($page ?? self::DEFAULT_PAGE, $perPage ?? self::DEFAULT_PER_PAGE);

        return new JsonResponse(empty($tasks) ? [] : array_map(static fn(Task $task) => $task->toArray(), $tasks));
    }

    #[Route(path: '', methods: 'PATCH')]
    #[IsGranted('ROLE_TEACHER')]
    public function update(Request $request): Response
    {
        $id = $request->query->get('id');
        $taskName = $request->query->get('task');
        $lessonId = $request->request->get('lessonId');

        $taskId = $this->service->update($id, $taskName, $lessonId);

        [$data, $code] = $taskId === null ?
            [['success' => false], Response::HTTP_BAD_REQUEST] :
            [['success' => true, 'taskId' => $taskId], Response::HTTP_OK];
        return new JsonResponse($data, $code);
    }

    #[Route(path: '/{id}',requirements: ['id' => '\d+'], methods: 'DELETE')]
    #[IsGranted('ROLE_TEACHER')]
    public function delete(int $id): Response
    {
        $result = $this->service->deleteUser($id);

        return new JsonResponse(['success' => $result], $result ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }
}