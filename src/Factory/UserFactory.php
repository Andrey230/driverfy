<?php

namespace App\Factory;

use App\Entity\Subscription;
use App\Entity\User;
use App\Repository\SubscriptionRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactory
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private SubscriptionRepository $subscriptionRepository,
    )
    {
    }

    public function createBase(string $email, string $password, string $name, int $fullDayStart, int $fullDayEnd): User
    {
        $subscription = $this->subscriptionRepository->findByName(Subscription::BASE_SUBSCRIPTION);

        if(!$subscription){
            throw new NotFoundHttpException('subscription not found');
        }

        $user = new User();
        $user->setEmail($email);
        $user->setName($name);
        $user->setPassword($password, $this->passwordHasher);
        $user->setRoles(['ROLE_USER']);
        $user->setSubscriptionId($subscription);
        $user->setOptions([
            'full_day_start' => $fullDayStart,
            'full_day_end' => $fullDayEnd,
        ]);

        return $user;
    }
}
