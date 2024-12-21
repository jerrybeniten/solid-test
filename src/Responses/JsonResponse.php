<?php

namespace App\Responses;

use App\Interfaces\ResponseInterface;

class JsonResponse implements ResponseInterface
{
    public function send(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
