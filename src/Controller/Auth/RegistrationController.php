<?php

namespace App\Controller\Auth;

use App\Client\StatsdAPIClient;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/auth/registration')]
class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly UserService $service,
        private readonly StatsdAPIClient $statsdAPIClient,
    )
    {
    }

    #[Route(path: '', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $this->statsdAPIClient->increment('create_user');
        $params = $request->request->all();
        if (!isset($params['email']) || !isset($params['login']) || !isset($params['password'])) {
            return new JsonResponse([
                [
                    'success' => false,
                    'desc' => 'Не верно заданны логин, пароль или email'
                ],
                Response::HTTP_BAD_REQUEST
            ]);
        }
        $userId = $this->service->create($params);
        [$data, $code] = $userId === null ?
            [['success' => false], Response::HTTP_BAD_REQUEST] :
            [['success' => true, 'userId' => $userId], Response::HTTP_OK];
        return new JsonResponse($data, $code);
    }
}