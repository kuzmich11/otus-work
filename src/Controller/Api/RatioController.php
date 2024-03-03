<?php

namespace App\Controller\Api;

use App\Entity\Ratio;
use App\Service\RatioService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[Route(path: 'api/ratio')]
#[IsGranted('ROLE_TEACHER')]
class RatioController
{
    private const DEFAULT_PAGE = 0;
    private const DEFAULT_PER_PAGE = 20;
    public function __construct(
        private readonly RatioService $service
    )
    {
    }

    /**
     * @throws Exception
     */
    #[Route(path: '', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $taskId = (int)$request->request->get('taskId');
        $skillId = (int)$request->request->get('skillId');
        $ratio = (int)$request->request->get('ratio');

        $ratioId = $this->service->create($taskId, $skillId, $ratio);
        [$data, $code] = $ratioId === null ?
            [['success' => false], Response::HTTP_BAD_REQUEST] :
            [['success' => true, 'ratioId' => $ratioId], Response::HTTP_OK];
        return new JsonResponse($data, $code);
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function get(int $id): Response
    {
        $course = $this->service->getById($id);
        return new JsonResponse($course?->toArray());
    }

    #[Route(path: '', methods: 'GET')]
    public function getAll(Request $request): Response
    {
        $page = $request->query->get('page');
        $perPage = $request->query->get('perPage');
        $ratios = $this->service->getAll(
            $page ?? self::DEFAULT_PAGE,
            $perPage ?? self::DEFAULT_PER_PAGE);

        return new JsonResponse(empty($ratios)
            ? []
            : array_map(static fn(Ratio $ratio) => $ratio->toArray(), $ratios));
    }

    #[Route(path: '', methods: 'PATCH')]
    public function update(Request $request): Response
    {
        $id = (int)$request->query->get('id');
        $taskId = (int)$request->query->get('taskId');
        $skillId = (int)$request->query->get('skillId');
        $ratio = (int)$request->query->get('ratio');

        $ratioId = $this->service->update($id, $taskId, $skillId, $ratio);

        [$data, $code] = $ratioId === null ?
            [['success' => false], Response::HTTP_BAD_REQUEST] :
            [['success' => true, 'courseId' => $ratioId], Response::HTTP_OK];
        return new JsonResponse($data, $code);
    }

    #[Route(path: '/{id}',requirements: ['id' => '\d+'], methods: 'DELETE')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): Response
    {
        $result = $this->service->deleteRatio($id);

        return new JsonResponse(
            ['success' => $result],
            $result ? Response::HTTP_OK : Response::HTTP_NOT_FOUND
        );
    }
}