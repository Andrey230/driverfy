<?php

namespace App\Command\CreateUser;

use App\Command\CommandHandlerInterface;
use App\Entity\User;
use App\Factory\UserFactory;
use Doctrine\ORM\EntityManagerInterface;

class CreateUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly UserFactory $userFactory,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function __invoke(CreateUserCommand $createUserCommand): array
    {
        $user = $this->userFactory->createBase(
            $createUserCommand->email,
            $createUserCommand->password,
            $createUserCommand->name
        );
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return [
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'subscription' => $user->getSubscriptionId()->getName(),
        ];
    }
}
