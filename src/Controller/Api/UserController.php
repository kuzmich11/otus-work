<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\UserService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: 'api/user')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    private const DEFAULT_PAGE = 0;
    private const DEFAULT_PER_PAGE = 20;

    public function __construct(
        private readonly UserService $service
    )
    {
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function get(int $id): Response
    {
        $user = $this->service->getById($id);
        return new JsonResponse($user?->toArray());
    }

    #[Route(path: '', methods: 'GET')]
    public function getAll(Request $request): Response
    {
        $params = $request->query->all();
        $students = $this->service->getAll(
            $params['page'] ?? self::DEFAULT_PAGE,
            $params['perPage'] ?? self::DEFAULT_PER_PAGE);
        return new JsonResponse(empty($students)
            ? []
            : array_map(static fn(User $student) => $student->toArray(), $students));
    }

    /**
     * @throws Exception
     */
    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: 'PATCH')]
    public function update(int $id, Request $request): Response
    {
        $params = $request->query->all();
        $studentId = $id ? $this->service->update($id, $params) : null;

        [$data, $code] = $studentId === null ?
            [['success' => false], Response::HTTP_BAD_REQUEST] :
            [['success' => true, 'studentId' => $studentId], Response::HTTP_OK];
        return new JsonResponse($data, $code);
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: 'DELETE')]
    public function delete(int $id): Response
    {
        $result = $this->service->deleteStudent($id);

        return new JsonResponse(['success' => $result], $result ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }
}