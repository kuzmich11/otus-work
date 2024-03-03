<?php

namespace App\Service;

use App\Manager\UserManager;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthService
{
    public function __construct(
        private readonly UserRepository              $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly JWTEncoderInterface         $jwtEncoder,
        private readonly int                         $tokenTTL,
    )
    {
    }

    public function isCredentialsValid(string $login, string $password): bool
    {
        $user = $this->userRepository->findOneBy(['login' => $login]);
        if ($user === null) {
            return false;
        }

        return $this->passwordHasher->isPasswordValid($user, $password);
    }

    /**
     * @throws JWTEncodeFailureException
     */
    public function getToken(string $login): string
    {
        $user = $this->userRepository->findOneBy(['login' => $login]);
        $roles = $user ? $user->getRoles() : [];
        $tokenData = [
            'username' => $login,
            'roles' => $roles,
            'exp' => time() + $this->tokenTTL,
        ];

        return $this->jwtEncoder->encode($tokenData);
    }
}