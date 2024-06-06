<?php

namespace App\Query\GetUser;

use App\Entity\User;
use App\Query\QueryHandlerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class GetUserQueryHandler implements QueryHandlerInterface
{
    public function __construct(private readonly Security $security)
    {
    }

    public function __invoke(GetUserQuery $getUserQuery): array
    {
        /** @var User $user */
        $user = $this->security->getUser();

        return $this->getResult($user);
    }

    private function getResult(User $user): array
    {
        $drivers = [];
        foreach ($user->getDrivers() as $driver){
            $drivers[] = $driver->toArray();
        }

        return [
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'subscription' => $user->getSubscriptionId()->toArray(),
            'drivers' => $drivers,
        ];
    }
}
