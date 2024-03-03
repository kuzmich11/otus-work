<?php

namespace App\Controller\Admin;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Form\Type\CreateUserType;
use App\Form\Type\UpdateUserType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: 'admin/user')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $service,
        private readonly FormFactoryInterface $formFactory,
    )
    {
    }

    #[Route(path: '/create', name: 'create_user', methods: ['GET', 'POST'])]
    public function userCreateAction(Request $request): Response
    {
        $form = $this->formFactory->create(CreateUserType::class);
        $form->handleRequest($request);

        if ($sub = $form->isSubmitted() && $valid = $form->isValid()) {
            /** @var UserDTO $userDTO */
            $userDTO = $form->getData();

            $this->service->saveUserFromDTO($user ?? new User(), $userDTO);
        }

        return $this->render('formUser.html.twig', [
            'form' => $form,
            'user' => $user ?? null,
            'formName' => 'Создать пользователя'
        ]);
    }

    #[Route(path: '/update/{id}', name: 'update_user', methods: ['GET', 'PATCH'])]
    public function userUpdateAction(Request $request, ?int $id = null): Response
    {
        if ($id) {
            $user = $this->service->getById($id);
            $dto = UserDTO::fromEntity($user);
        }
        $form = $this->formFactory->create(UpdateUserType::class, $dto ?? null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserDTO $userDTO */
            $userDTO = $form->getData();

            $this->service->saveUserFromDTO($user ?? new User(), $userDTO);
        }

        return $this->render('formUser.html.twig', [
            'form' => $form,
            'user' => $user ?? null,
            'formName' => 'Обновить данные пользователя'
        ]);
    }
}