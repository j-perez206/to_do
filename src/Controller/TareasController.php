<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Event\TareaCreada;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class TareasController extends AbstractController
{
    private $em;
    private $dispatcher;

    // Además del entitymanager, se crea el dispatcher del evento
    public function __construct(EntityManagerInterface $em,
    EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $eventDispatcher;
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/tareas', name: 'app_tareas')]
    public function index(Request $request): Response
    {
        $post = new Post();
        $posts = $this->em->getRepository(Post::class)->findAllPosts($this->getUser());
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        return $this->render('tareas/index.html.twig', [
            'form' => $form->createView(),
            'posts' => $posts,
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
            // Asigna la tarea al usuario logeado
            $post->setUser($this->getUser());
            $this->em->flush();
            // Cada vez que haga flush, se ejecuta el dispatcher del evento
            $this->dispatcher->dispatch(new TareaCreada($post));
        }

        return $this->redirectToRoute('app_tareas');
    }

    #[Route('/post/edit/{id}', name: 'app_tareas_edit')]
    public function editPost(Post $post, Request $request): Response
    {
        //Deniega el acceso si quien intenta editar no es el dueño de la tarea
        $this->denyAccessUnlessGranted('POST_EDIT', $post);

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            return $this->redirectToRoute('app_tareas');
        }

        return $this->render('tareas/edit.html.twig', [
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
        return $this->redirectToRoute('app_tareas');
    }

    #[Route('/post/toggle/{id}', name: 'app_tareas_toggle')]
    public function togglePost(Post $post): Response
    {
        $post->setDone(!$post->isDone());
        $this->em->flush();
        return $this->redirectToRoute('app_tareas');
    }
}
