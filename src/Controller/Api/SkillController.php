<?php

namespace App\Controller\Api;

use App\Entity\Skill;
use App\Service\SkillService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[Route(path: 'api/skill')]
#[IsGranted('ROLE_STUDENT')]
class SkillController
{
    private const DEFAULT_PAGE = 0;
    private const DEFAULT_PER_PAGE = 20;
    public function __construct(
        private readonly SkillService $service
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
        $skillName = (string)$request->request->get('skill');
        $skillId = $this->service->create($skillName);
        [$data, $code] = $skillId === null ?
            [['success' => false], Response::HTTP_BAD_REQUEST] :
            [['success' => true, 'skillId' => $skillId], Response::HTTP_OK];
        return new JsonResponse($data, $code);
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function get(int $id): Response
    {
        $skill = $this->service->getById($id);
        return new JsonResponse($skill?->toArray());
    }

    #[Route(path: '', methods: 'GET')]
    public function getAll(Request $request): Response
    {
        $page = $request->query->get('page');
        $perPage = $request->query->get('perPage');
        $skills = $this->service->getAll(
            $page ?? self::DEFAULT_PAGE,
            $perPage ?? self::DEFAULT_PER_PAGE);

        return new JsonResponse(empty($skills)
            ? []
            : array_map(static fn(Skill $skill) => $skill->toArray(), $skills));
    }

    #[Route(path: '', methods: 'PATCH')]
    #[IsGranted('ROLE_TEACHER')]
    public function update(Request $request): Response
    {
        $id = (int)$request->query->get('id');
        $skillName = (string)$request->query->get('skill');

        $skillId = $this->service->update($id, $skillName);

        [$data, $code] = $skillId === null ?
            [['success' => false], Response::HTTP_BAD_REQUEST] :
            [['success' => true, 'skillId' => $skillId], Response::HTTP_OK];
        return new JsonResponse($data, $code);
    }

    #[Route(path: '/{id}',requirements: ['id' => '\d+'], methods: 'DELETE')]
    #[IsGranted('ROLE_METHODIST')]
    public function delete(int $id): Response
    {
        $result = $this->service->deleteSkill($id);

        return new JsonResponse(
            ['success' => $result],
            $result ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }
}