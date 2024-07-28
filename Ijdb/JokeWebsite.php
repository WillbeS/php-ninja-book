<?php

namespace Ijdb;

use Ijdb\Entity\Author;
use Ijdb\Entity\Category;
use Ijdb\Entity\Joke;
use Ninja\DatabaseTable;
use Ninja\Website;

class JokeWebsite implements Website {
    const BASE_URL = "sandbox/ninja-book/public/";

    private ?DatabaseTable $jokesTable;
    private ?DatabaseTable $authorsTable;
    private ?DatabaseTable $categoriesTable;
    private ?DatabaseTable $jokeCategoriesTable;
    private \Ninja\Authentication $authentication;

    public function __construct() {
        $pdo = new \PDO('mysql:host=localhost;dbname=ijdb;charset=utf8mb4', 'ijdbuser', 'mypassword');

        $this->jokesTable = new DatabaseTable(
            $pdo, 
            'joke', 
            'id',
            Joke::class,
            [&$this->authorsTable, &$this->jokeCategoriesTable] // use reference variable because the table is not yet created
        );

        $this->authorsTable = new DatabaseTable(
            $pdo, 
            'author', 
            'id', 
            // '\Ijdb\Entity\Author', 
            Author::class,
            [&$this->jokesTable] // the reference here is not really necessary but I'm going by the book
        );

        $this->categoriesTable = new DatabaseTable(
            $pdo, 
            'category', 
            'id',
            Category::class,
            [&$this->jokesTable, &$this->jokeCategoriesTable]
        );

        $this->jokeCategoriesTable = new DatabaseTable($pdo, 'joke_category', 'categoryId');

        $this->authentication = new \Ninja\Authentication($this->authorsTable, 'email', 'password');
    }

    public function getLayoutVariables(): array {
        return [
            'loggedIn' => $this->authentication->isLoggedIn()
        ];
    }

    public function getDefaultRoute() {
        return 'joke/home';
    }

    public function getController(string $controllerName): ?object {
        $controllers = [
            'joke' => new \Ijdb\Controllers\Joke(
                $this->jokesTable, 
                $this->authorsTable, 
                $this->categoriesTable, 
                $this->authentication
            ),
            'author' => new \Ijdb\Controllers\Author($this->authorsTable),
            'login' => new \Ijdb\Controllers\login($this->authentication),
            'category' => new \Ijdb\Controllers\Category($this->categoriesTable)
        ];
        
        return $controllers[$controllerName] ?? null;
    }

    public function checkLoginOld(string $uri): ?string {
        $restrictedPages = ['joke/edit', 'joke/delete'];

        if (in_array($uri, $restrictedPages) && !$this->authentication->isLoggedIn()) {
             header('location: /' . self::BASE_URL .'login/login');
            exit();
        }

        return $uri;
    }


    public function checkLogin(string $uri): ?string {
        $restrictedPages = [
                            'category/list' => Author::LIST_CATEGORIES,
                            'category/delete' => Author::DELETE_CATEGORY,
                            'category/edit' => Author::EDIT_CATEGORY,
                            'author/permissions' => Author::EDIT_USER_ACCESS,
                            'author/list' => Author::EDIT_USER_ACCESS,
                           ];
    
        if (isset($restrictedPages[$uri])) {
          if (!$this->authentication->isLoggedIn()
          || !$this->authentication->getUser()->hasPermission($restrictedPages[$uri])) {
            header('location: /' . self::BASE_URL .'login/login');
            exit();
          }
        }
    
        return $uri;
    
    }
}