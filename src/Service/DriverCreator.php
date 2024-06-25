<?php

namespace App\Service;

use App\Entity\Driver;
use App\Entity\User;
use App\Factory\DriverFactory;
use App\Repository\DriverRepository;
use App\Service\MonthActivityCreator\MonthActivityCreatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class DriverCreator implements DriverCreatorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DriverFactory $driverFactory,
        private DriverParser $driverParser,
        private DriverRepository $driverRepository,
        private Security $security,
        private MonthActivityCreatorInterface $monthActivityCreator,
    )
    {
    }

    public function create(string $file): Driver
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $options = $user->getOptions();

        $data = $this->driverParser->parse($file, $options['full_day_start'], $options['full_day_end']);
        $driverInfo = $data['driverInfo'];

        $driver = $this->driverRepository->findByCardId($driverInfo['id'], $user->getId());

        if(!$driver){
            $driver = $this->driverFactory->createDriver(
                $driverInfo['name'],
                $driverInfo['carNumber'],
                $driverInfo['id'],
                $user,
            );

            $this->entityManager->persist($driver);
        }

        foreach ($data['months'] as $month => $monthData){
            $this->monthActivityCreator->create($driver, $month, $monthData);
        }
        $this->entityManager->flush();


        return $driver;
    }
}
