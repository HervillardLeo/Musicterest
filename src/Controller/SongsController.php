<?php

namespace App\Controller;

use App\Entity\Song;
use App\Repository\SongRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

class SongsController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SongRepository $songRepository): Response
    {
        $songs = $songRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('songs/index.html.twig', ['songs' => $songs]);
    }

    #[Route('/songs/{id<\d+>}', name: 'app_songs_show')]
    public function show(Song $song): Response
    {
        return $this->render('songs/show.html.twig', ['song' => $song]);
    }
}
