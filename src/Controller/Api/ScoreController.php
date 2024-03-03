<?php

namespace App\Controller\Api;

use App\Entity\Course;
use App\Entity\ScoreSkill;
use App\Entity\ScoreTask;
use App\Service\ScoreService;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[Route(path: 'api/score')]
#[IsGranted('ROLE_STUDENT')]
class ScoreController
{
    private const DEFAULT_PAGE = 0;
    private const DEFAULT_PER_PAGE = 20;
    public function __construct(
        private readonly ScoreService $service
    )
    {
    }

    /**
     * @throws Exception
     */
    #[Route(path: '', methods: ['POST'])]
    #[IsGranted('ROLE_TEACHER')]
    public function create(Request $request): Response
    {
        $taskId = (int)$request->request->get('taskId');
        $studentId = (int)$request->request->get('studentId');
        $score = (int)$request->request->get('score');
        $completedAt = new DateTime($request->request->get('completedAt'));
        $scoreId = $this->service->create($taskId, $studentId, $score, $completedAt);
        [$data, $code] = $scoreId === null ?
            [['success' => false], Response::HTTP_BAD_REQUEST] :
            [['success' => true, 'scoreId' => $scoreId], Response::HTTP_OK];
        return new JsonResponse($data, $code);
    }

    #[Route(path: '/task', methods: 'GET')]
    public function getScoreTask(Request $request): Response
    {
        $page = $request->query->get('page');
        $perPage = $request->query->get('perPage');
        $scores = $this->service->getScoreTask(
            $page ?? self::DEFAULT_PAGE,
            $perPage ?? self::DEFAULT_PER_PAGE);

        return new JsonResponse(empty($scores)
            ? []
            : array_map(static fn(ScoreTask $score) => $score->toArray(), $scores));
    }

    #[Route(path: '/skill', methods: 'GET')]
    public function getScoreSkill(Request $request): Response
    {
        $page = $request->query->get('page');
        $perPage = $request->query->get('perPage');
        $scores = $this->service->getScoreSkill(
            $page ?? self::DEFAULT_PAGE,
            $perPage ?? self::DEFAULT_PER_PAGE);

        return new JsonResponse(empty($scores)
            ? []
            : array_map(static fn(ScoreSkill $score) => $score->toArray(), $scores));
    }

    /**
     * @throws Exception
     */
    #[Route(path: '', methods: 'PATCH')]
    #[IsGranted('ROLE_TEACHER')]
    public function update(Request $request): Response
    {
        $studentId = (int)$request->query->get('studentId');
        $taskId = (int)$request->query->get('taskId');
        $score = (int)$request->query->get('score');
        $completedAt = new DateTime($request->query->get('completedAt'));

        $scoreId = $this->service->update($studentId, $taskId, $score, $completedAt);

        [$data, $code] = $scoreId === null ?
            [['success' => false], Response::HTTP_BAD_REQUEST] :
            [['success' => true, 'scoreId' => $scoreId], Response::HTTP_OK];
        return new JsonResponse($data, $code);
    }

    #[Route(path: '', methods: 'DELETE')]
    #[IsGranted('ROLE_TEACHER')]
    public function delete(Request $request): Response
    {
        $studentId = (int)$request->query->get('studentId');
        $taskId = (int)$request->query->get('taskId');
        $result = $this->service->deleteCourse($studentId, $taskId);

        return new JsonResponse(['success' => $result], $result ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }
}