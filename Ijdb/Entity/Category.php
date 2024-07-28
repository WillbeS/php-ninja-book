<?php

namespace Ijdb\Entity;

use Ninja\DatabaseTable;

class Category {
    public $id;
    public $name;

    public function __construct(
        private ?DatabaseTable $jokesTable, 
        private ?DatabaseTable $jokeCategoriesTable
    ) {
    }

    public function getJokes(int $limit = 0, int $offset = 0) {
        $jokeCategories = $this->jokeCategoriesTable->find('categoryId',  $this->id, null, $limit, $offset);

        $jokes = [];

        foreach ($jokeCategories as $jokeCategory) {
            $joke = $this->jokesTable->find('id', $jokeCategory->jokeId)[0] ?? null;
            if ($joke) {
                $jokes[] = $joke;
            }
        }

        usort($jokes, [$this, 'sortJokes']);

        return $jokes;
    }

    public function getNumJokes() {
        return $this->jokeCategoriesTable->total('categoryId', $this->id);
    }

    private function sortJokes($a, $b) {
        $aDate = new \DateTime($a->jokedate);
        $bDate = new \DateTime($b->jokedate);

        if ($aDate->getTimestamp() == $bDate->getTimestamp()) {
            return 0;
        }

        return $aDate->getTimestamp() > $bDate->getTimestamp() ? -1 : 1;
    }
}