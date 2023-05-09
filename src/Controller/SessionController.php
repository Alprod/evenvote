<?php

namespace App\Controller;

use App\Entity\Session;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{
    #[Route('/', name: 'app_session')]
    public function index(SessionRepository $sessionRepo): Response
    {
        return $this->render('session/index.html.twig', [
            'sessions' => $sessionRepo->findBy([],['createdAt' => 'ASC']),
        ]);
    }

	#[Route('/session/{id}', name: 'app_session_detail')]
	public function detail(Session $session): Response
	{
		return $this->render('session/detail.html.twig', [
			'session' => $session
		]);
	}

}
