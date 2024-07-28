<?php

namespace Ijdb\Controllers;

use Ijdb\Entity\Author;
use Ninja\Authentication;
use Ninja\DatabaseTable;

class Joke
{
    const BASE_URL = "sandbox/ninja-book/public/";

    const LIST_LIMIT = 2;

    public function __construct(
        private DatabaseTable $jokesTable, 
        private DatabaseTable $authorsTable,
        private DatabaseTable $categoriesTable,
        private Authentication $authentication
    ) {
    }

    public function home() {
        $title = 'Internet Joke Database';

        return ['template' => 'home.html.php', 'title' => $title];
    }

    public function list(mixed $categoryId = null, int $page = 1) {
        $offset = self::LIST_LIMIT * ($page - 1);

        if (is_numeric($categoryId)) {
            $category = $this->categoriesTable->find('id', $categoryId)[0];
            $jokes = $category->getJokes(self::LIST_LIMIT, $offset);
            $totalJokes = $category->getNumJokes();
        }
        else {
            $jokes = $this->jokesTable->findAll('jokedate DESC', self::LIST_LIMIT, $offset);
            $totalJokes = $this->jokesTable->total();
        }

        $user = $this->authentication->getUser();

        return [
            'template' => 'jokes.html.php', 
            'title' => 'Joke list', 
            'variables' => [
                'totalJokes' => $totalJokes,
                'jokes' => $jokes,
                'baseUrl' => self::BASE_URL,
                'user' => $user,
                'categories' => $this->categoriesTable->findAll(),
                'currentPage' => $page,
                'categoryId' => $categoryId,
            ],
        ];
    }

    public function deleteSubmit() {
        $author = $this->authentication->getUser();

        $joke = $this->jokesTable->find('id', $_POST['id'])[0];

        if ($joke->authorid != $author->id && !$author->hasPermission(Author::DELETE_JOKE)) {
            return;
        }

        $this->jokesTable->delete('id', $_POST['id']);

        header('location: list');
    }

    public function editSubmit($id = null) {
        $author = $this->authentication->getUser();

        if (!empty($id)) {
            $joke = $this->jokesTable->find('id', $id)[0];
      
            if ($joke->authorid != $author->id && !$author->hasPermission(Author::EDIT_JOKE)) {
             return;
            }
        }


        $joke = $_POST['joke'];
        $joke['jokedate'] = new \DateTime();
       
        $jokeEntity = $author->addJoke($joke);

        $jokeEntity->clearCategories();

        foreach ($_POST['category'] as $categoryId) {
            $jokeEntity->addCategory($categoryId);
        }

        header('location: /' . self::BASE_URL . 'joke/list' );
    }

    public function edit($id = null) {
        $author = $this->authentication->getUser();
        $categories = $this->categoriesTable->findAll();

        if (isset($id)) {
            $joke = $this->jokesTable->find("id", $id)[0] ?? null;
        } else {
            $joke = null;
        }

        $title = 'Edit joke';

        return [
            'template' => 'editjoke.html.php', 
            'title' => $title,
            'variables' => [
                'joke' => $joke,
                'user' => $author,
                'categories' => $categories,
            ],
        ];
    }
}