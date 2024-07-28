<?php

namespace Ijdb\Entity;

use Ninja\DatabaseTable;

class Joke {
    public int $id;
    public int $authorid; //made a mistake in the db and created it with lower case!!!!
    public string $jokedate;
    public string $joketext;

    private ?object $author;

    public function __construct(
        private DatabaseTable $authorsTable, 
        private DatabaseTable $jokeCategoriesTable
    ) {
    }

    public function getAuthor() {
        if (empty($this->author)) {
            $this->author = $this->authorsTable->find('id', $this->authorid)[0];
        }
        
        return $this->author;
    }

    public function addCategory($categoryId) {
        $jokeCat = ['jokeId' => $this->id, 'categoryId' => $categoryId];
      
        $this->jokeCategoriesTable->save($jokeCat);
    }

    public function clearCategories() {
        $this->jokeCategoriesTable->delete('jokeId', $this->id);
    }

    public function hasCategory($categoryId) {
        $jokeCategories = $this->jokeCategoriesTable->find('jokeId', $this->id);
      
        foreach ($jokeCategories as $jokeCategory) {
          if ($jokeCategory->categoryId == $categoryId) {
            return true;
          }
        }
    }
}