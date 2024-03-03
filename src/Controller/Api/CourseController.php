<?php

namespace App\Controller\Api;

use App\Entity\Course;
use App\Service\CourseService;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[Route(path: 'api/course')]
#[IsGranted('ROLE_USER')]
class CourseController
{
    private const DEFAULT_PAGE = 0;
    private const DEFAULT_PER_PAGE = 20;
    public function __construct(
        private readonly CourseService $service
    )
    {
    }

    /**
     * @throws Exception
     */
    #[Route(path: '', methods: ['POST'])]
    #[IsGranted('ROLE_METHODIST')]
    public function create(Request $request): Response
    {
        $name = (string)$request->request->get('courseName');
        $start = new DateTime($request->request->get('startedAt'));
        $end = new DateTime($request->request->get('finishedAt'));
        $courseId = $this->service->create($name, $start, $end);
        [$data, $code] = $courseId === null ?
            [['success' => false], Response::HTTP_BAD_REQUEST] :
            [['success' => true, 'courseId' => $courseId], Response::HTTP_OK];
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
        $courses = $this->service->getAll(
            $page ?? self::DEFAULT_PAGE,
            $perPage ?? self::DEFAULT_PER_PAGE);

        return new JsonResponse(['courses' => $courses]);
    }

    /**
     * @throws Exception
     */
    #[Route(path: '', methods: 'PATCH')]
    #[IsGranted('ROLE_METHODIST')]
    public function update(Request $request): Response
    {
        $id = $request->query->get('id');
        $name = $request->query->get('courseName');
        $start = new DateTime($request->query->get('startedAt'));
        $end = new DateTime($request->query->get('finishedAt'));

        $courseId = $this->service->update($id, $name, $start, $end);

        [$data, $code] = $courseId === null ?
            [['success' => false], Response::HTTP_BAD_REQUEST] :
            [['success' => true, 'courseId' => $courseId], Response::HTTP_OK];
        return new JsonResponse($data, $code);
    }

    #[Route(path: '/{id}',requirements: ['id' => '\d+'], methods: 'DELETE')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): Response
    {
        $result = $this->service->deleteCourse($id);

        return new JsonResponse(['success' => $result], $result ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }
}