<?php

namespace App\Service;

use App\Entity\Driver;
use App\Entity\User;
use App\Factory\DriverFactory;
use App\Repository\DriverRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class DriverCreator implements DriverCreatorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DriverFactory $driverFactory,
        private DriverParser $driverParser,
        private DriverRepository $driverRepository,
        private Security $security
    )
    {
    }

    public function create(string $file): Driver
    {
        $data = $this->driverParser->parse($file);
        $driverInfo = $data['driverInfo'];

        $driver = $this->driverRepository->findByCardId($driverInfo['id']);

        if(!$driver){
            /** @var User $user */
            $user = $this->security->getUser();

            $driver = $this->driverFactory->createDriver(
                $driverInfo['name'],
                $driverInfo['carNumber'],
                $driverInfo['id'],
                $user,
            );

            $this->entityManager->persist($driver);
            $this->entityManager->flush();
        }

        return $driver;
    }
}
