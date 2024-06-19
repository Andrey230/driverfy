<?php

namespace App\Controller;

use App\Command\CommandBusInterface;
use App\Command\CreateUser\CreateUserCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Webmozart\Assert\Assert;

class UserCreateController extends AbstractController
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    #[Route('/api/users/create', name: 'app_user_create', methods: ["POST"])]
    public function index(Request $request): JsonResponse
    {
        $params = $request->toArray();
        try {
            $this->validateParams($params);
            $command = new CreateUserCommand(
                $params['email'],
                $params['password'],
                $params['name'],
                $params['fullDayStart'],
                $params['fullDayEnd']
            );
            $user = $this->commandBus->execute($command);
            return $this->json($user);
        }catch (\Exception $exception){
            return $this->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    private function validateParams(array $params): void
    {
        Assert::email($params['email']);
        Assert::string($params['password']);
        Assert::minLength($params['password'], 8);
        Assert::string($params['name']);
        Assert::notEmpty($params['fullDayStart']);
        Assert::notEmpty($params['fullDayEnd']);
        Assert::greaterThanEq($params['fullDayStart'], 0);
        Assert::lessThanEq($params['fullDayStart'], 1440);
        Assert::greaterThanEq($params['fullDayEnd'], 0);
        Assert::lessThanEq($params['fullDayEnd'], 1440);
    }
}
