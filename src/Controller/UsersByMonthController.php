<?php

namespace App\Controller;

use App\Query\QueryBusInterface;
use App\Query\UsersByMonth\UsersByMonthQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Webmozart\Assert\Assert;

class UsersByMonthController extends AbstractController
{
    public function __construct(private QueryBusInterface $queryBus)
    {
    }

    #[Route('/api/users/month/{month}', name: 'app_users_month', methods: ["GET"])]
    public function index(string $month): JsonResponse
    {
        try {
            Assert::notEmpty($month);

            $query = new UsersByMonthQuery($month);
            $result = $this->queryBus->execute($query);

            return $this->json($result);
        }catch (\Exception $exception){
            return $this->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
