<?php

namespace App\Controller;

use App\Client\LocalisationClient;
use App\Services\UserService;
use SoapClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index(
        Request $request,
        UserService $userService
    ) {
        $ip = $request->getClientIp();

        return $this->render(
            'home/index.html.twig',
            [
                'ip' => $ip,
                'country' => $userService->getLocalisation($ip)
            ]
        );
    }
}
