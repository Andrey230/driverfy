<?php

namespace App\Command\CreateUser;

use App\Command\CommandHandlerInterface;
use App\Entity\User;
use App\Factory\UserFactory;
use Doctrine\ORM\EntityManagerInterface;

class CreateUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(private UserFactory $userFactory, private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(CreateUserCommand $createUserCommand): array
    {
        $user = $this->userFactory->create($createUserCommand->email, $createUserCommand->password);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return [
            'email' => $user->getEmail(),
        ];
    }
}
