<?php

namespace App\Query\GetUser;

use App\Entity\User;
use App\Query\QueryHandlerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class GetUserQueryHandler implements QueryHandlerInterface
{
    public function __construct(private Security $security)
    {
    }

    public function __invoke(GetUserQuery $getUserQuery)
    {
        /** @var User $user */
        $user = $this->security->getUser();

        return $this->getResult($user);
    }

    private function getResult(User $user): array
    {
        $data = [
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'subscription' => $user->getSubscriptionId()->getName(),
        ];

        return $data;
    }
}
