<?php

namespace App\Controller;

use App\Repository\SongRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SongsController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SongRepository $songRepository): Response
    {
        $songs = $songRepository->findAll();
        return $this->render('songs/index.html.twig', ['songs' => $songs]);
    }
}
