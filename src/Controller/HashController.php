<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HashController extends AbstractController
{
    /**
     * @Route("/hash", name="app_hash")
     */
    public function index(Request $request): JsonResponse
    {
        $message = $request->get('message');

        $attempts = 0;
        do {
            $key = $this->generateKey();
            $hash = md5($message . $key);

            $attempts++;

        } while (substr($hash, 0, 4) !== "0000");

        return $this->json([
            'hash' => $hash,
            'key' => $key,
            'attempts' => $attempts,
        ]);
    }

    private function generateKey(): string
    {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($permitted_chars), 0, 8);
    }
}
