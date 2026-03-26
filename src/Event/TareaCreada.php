<?php
namespace App\Event;

use App\Entity\Post;

class TareaCreada
{
    public function __construct(private Post $post) {}

    // Pilla los datos de la tarea
    public function getPost(): Post
    {
        return $this->post;
    }
}
