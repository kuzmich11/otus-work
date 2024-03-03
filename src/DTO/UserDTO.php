<?php

namespace App\DTO;

use App\Entity\Course;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class UserDTO
{
    public function __construct(
        #[Assert\Length(max: 32)]
        public ?string $name = null,

        #[Assert\Length(max: 32)]
        public ?string $lastName = null,

        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email = '',

        #[Assert\NotBlank]
        #[Assert\Length(max: 32)]
        public string $login = '',

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $password = '',

//        #[Assert\Type('array')]
//        public array $courses = [],
    ) {
    }

    public static function fromEntity(User $user): UserDTO
    {
        return new self(...[
            'name' => $user->getName(),
            'lastName' => $user->getLastname(),
            'email' => $user->getEmail(),
            'login' => $user->getLogin(),
            'password' => $user->getPassword(),
//            'courses' => array_map(
//                static function (Course $course) {
//                    return [
//                        'id' => $course->getId(),
//                        'courseName' => $course->getCourseName()
//                    ];
//                },
//                $user->getCourses()->toArray()
//            )
        ]);
    }
}