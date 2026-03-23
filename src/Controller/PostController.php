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

        return $this->render('post/index.html.twig', [
            'form' => $form->createView(),
            'posts' => $posts
        ]);
    }
}
