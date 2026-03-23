<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TareasController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'app_tareas')]
    public function index(): Response
    {
        return $this->render('post/index.html.twig', [
            'controller_name' => 'TareasController',
        ]);
    }

    #[Route('post/crear', name: 'app_tareas_create')]
    public function createPost(Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($post);
            $this->em->flush();
        }

        return $this->redirectToRoute('app_post');
    }

    #[Route('/post/edit/{id}', name: 'app_tareas_edit')]
    public function editPost(Post $post, Request $request): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            return $this->redirectToRoute('app_post');
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post
        ]);
    }

    #[Route('/post/delete/{id}', name: 'app_tareas_delete')]
    public function deletePost(Post $post, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->get('_token'))) {
            $this->em->remove($post);
            $this->em->flush();
        }
        return $this->redirectToRoute('app_post');
    }

    #[Route('/post/toggle/{id}', name: 'app_tareas_toggle')]
    public function togglePost(Post $post): Response
    {
        $post->setDone(!$post->isDone());
        $this->em->flush();
        return $this->redirectToRoute('app_post');
    }
}
