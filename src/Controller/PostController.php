<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PostController extends AbstractController
{
    private $em;

    /**
     * @param $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'app_post')]
    public function index(Request $request): Response
    {
        $post = new Post();
        $posts = $this->em->getRepository(Post::class)->findAllPosts();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Añadir los cambios
            $this->em->persist($post);
            //Guardar cambios en BD
            $this->em->flush();
            //Redirigir a la página principal
            return $this->redirectToRoute('app_post');
        }
        // Mostrar la página con el formulario
       return $this->render('post/index.html.twig', [
            'form' => $form->createView(),
            'posts' => $posts
        ]);
    }

    //Edita un post existente /edit/{id}
    #[Route('/edit/{id}', name: 'app_post_edit')]
    public function edit(Post $post, Request $request): Response
    {
        // Crear formulario con los datos del post
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

    //Elimina un post /delete/{id}
    #[Route('/delete/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Post $post, Request $request): Response
    {
        // Validar token CSRF para evitar eliminaciones no autorizadas
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->get('_token'))) {
            // Marcar el post para eliminar
            $this->em->remove($post);
            $this->em->flush();
        }
        return $this->redirectToRoute('app_post');
    }

    //Alterna el estado completado/incompleto de un post /toggle/{id}
    #[Route('/toggle/{id}', name: 'app_post_toggle')]
    public function toggle(Post $post): Response
    {
        // Cambiar el estado done al opuesto
        $post->setDone(!$post->isDone());
        $this->em->flush();
        return $this->redirectToRoute('app_post');
    }
}
