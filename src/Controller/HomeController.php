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
        $ip = '151.230.64.78';//$request->getClientIp();

        $country = $userService->getLocalisation($ip);

        var_dump($country->getId());

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
