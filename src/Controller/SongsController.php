<?php

namespace App\Controller;

use App\Entity\Song;
use App\Form\SongType;
use App\Repository\SongRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class SongsController extends AbstractController
{

    #[Route('/', name: 'app_home', methods: 'GET')]
    public function index(SongRepository $songRepository): Response
    {
        $songs = $songRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('songs/index.html.twig', ['songs' => $songs]);
    }

    #[Route('/songs/{id<\d+>}', name: 'app_songs_show', methods: 'GET')]
    public function show(Song $song): Response
    {
        return $this->render('songs/show.html.twig', ['song' => $song]);
    }

    #[Route('/songs/create', name: 'app_songs_create', methods: 'GET|POST')]
    public function create(Request $request, EntityManagerInterface $em, UserRepository $userRepo): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('error', 'You need to log in first');
            return $this->redirectToRoute('app_login');
        }
        $song = new Song;
        $form = $this->createForm(SongType::class, $song);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $song->setUser($this->getUser());
            $em->persist($song);
            $em->flush();

            $this->addFlash('success', 'Song successfully added');

            return $this->redirectToRoute('app_home');
        }
        return $this->render('songs/create.html.twig', ['songAddForm' => $form->createView()]);
    }

    #[Route('/songs/{id<\d+>}/edit', name: 'app_songs_edit', methods: 'GET|POST')]
    public function edit(Song $song, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('error', 'You need to log in first');
            return $this->redirectToRoute('app_login');
        }
        if ($song->getUser() != $this->getUser()) {
            $this->addFlash('error', 'Access Forbidden');
            return $this->redirectToRoute('app_home');
        }

        $form = $form = $this->createForm(SongType::class, $song);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Song successfully updated');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('songs/edit.html.twig', ['song' => $song, 'songEditForm' => $form->createView()]);
    }

    #[Route('/songs/{id<\d+>}/delete', name: 'app_songs_delete', methods: 'GET|POST')]
    public function delete(Song $song, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('error', 'You need to log in first');
            return $this->redirectToRoute('app_login');
        }
        if ($song->getUser() != $this->getUser()) {
            $this->addFlash('error', 'Access Forbidden');
            return $this->redirectToRoute('app_home');
        }

        if ($this->isCsrfTokenValid('song_deletion_' . $song->getId(), $request->query->get('csrf_token'))) {
            $em->remove($song);
            $em->flush();
            $this->addFlash('info', 'Song successfully deleted');
        }


        return $this->redirectToRoute('app_home');
    }
}
