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

final class PostController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'app_post')]
    public function index(Request $request): Response
    {
        $post = new Post();
        $posts = $this->em->getRepository(Post::class)->findAllPosts($this->getUser());
        $form = $this->createForm(PostType::class, $post);

        // Si el usuario no está logado redirige a la página de login
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'posts' => $posts
        ]);
    }
}
