<?php

namespace App\Controller;

use App\Command\CommandBusInterface;
use App\Command\UploadDriver\UploadDriverCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Webmozart\Assert\Assert;

class DriverUploadController extends AbstractController
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    #[Route('/api/driver/upload', name: 'app_driver_create', methods: ["POST"])]
    public function index(Request $request): JsonResponse
    {
        try {
            $params = $request->toArray();
            $this->validateParams($params);

            $command = new UploadDriverCommand($params['file']);
            $result = $this->commandBus->execute($command);

            return $this->json($result);
        }catch (\Exception $exception){
            return $this->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    private function validateParams(array $params): void
    {
        Assert::string($params['file']);
    }
}
