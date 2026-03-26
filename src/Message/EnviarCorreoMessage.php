<?php

namespace App\Message;

class EnviarCorreoMessage
{
    public function __construct(private string $textoTarea) {}

    // Esto es un puente que pasa los datos desde el subscriber al handler
    public function getTextoTarea(): string
    {
        return $this->textoTarea;
    }
}
