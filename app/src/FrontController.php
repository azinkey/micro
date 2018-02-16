<?php

namespace App;

use Slim\Views\Twig;
use Slim\Router;
use Slim\Flash\Messages as FlashMessages;
use App\Book;

final class FrontController {

    private $view;
    private $router;
    private $flash;

    public function __construct(Twig $view, Router $router, FlashMessages $flash) {
        $this->view = $view;
        $this->router = $router;
        $this->flash = $flash;
    }

    public function index($request, $response) {
        return $this->view->render($response, 'theme-1/book/list.twig', [
                    'books' => Book::all()
        ]);
    }

}
