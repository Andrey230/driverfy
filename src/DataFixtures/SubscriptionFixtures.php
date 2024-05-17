<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Subscription;

class SubscriptionFixtures extends Fixture
{
    private array $subscriptions = [
        Subscription::BASE_SUBSCRIPTION => [
            'maxDrivers' => Subscription::BASE_MAX_DRIVERS,
            'price' => Subscription::BASE_PRICE,
        ],
        Subscription::BEGINNER_SUBSCRIPTION => [
            'maxDrivers' => Subscription::BEGINNER_MAX_DRIVERS,
            'price' => Subscription::BEGINNER_PRICE,
        ],
        Subscription::PRO_SUBSCRIPTION => [
            'maxDrivers' => Subscription::PRO_MAX_DRIVERS,
            'price' => Subscription::PRO_PRICE,
        ],
    ];


    public function load(ObjectManager $manager)
    {
        foreach ($this->subscriptions as $name => $data){
            $subscription = new Subscription();
            $subscription->setName($name);
            $subscription->setMaxDrivers($data['maxDrivers']);
            $subscription->setPrice($data['price']);

            $manager->persist($subscription);
        }

        $manager->flush();
    }
}
