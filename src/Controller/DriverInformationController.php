<?php

namespace App\Controller;

use App\Query\DriverInformation\DriverInformationQuery;
use App\Query\QueryBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DriverInformationController extends AbstractController
{
    public function __construct(private QueryBusInterface $queryBus)
    {
    }

    #[Route('/api/driver/{id}', name: 'app_driver_information', methods: ["GET"])]
    public function index(string $id,Request $request): JsonResponse
    {
        try {
            $command = new DriverInformationQuery($id);
            $result = $this->queryBus->execute($command);

            return $this->json($result);
        }catch (\Exception $exception){
            return $this->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
