<?php

namespace App\Controller;

use App\Entity\Song;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $song = new Song;
        $form = $this->createFormBuilder($song)
            ->add('title', TextType::class)
            ->add('artist', TextType::class)
            ->add('description', TextareaType::class)
            ->add('link', TextType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($song);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }
        return $this->render('songs/create.html.twig', ['songAddForm' => $form->createView()]);
    }

    #[Route('/songs/{id<\d+>}/edit', name: 'app_songs_edit', methods: 'GET|POST')]
    public function edit(Song $song, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createFormBuilder($song)
            ->add('title', TextType::class)
            ->add('artist', TextType::class)
            ->add('description', TextareaType::class)
            ->add('link', TextType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_home');
        }
        return $this->render('songs/edit.html.twig', ['song' => $song, 'songEditForm' => $form->createView()]);
    }
}
