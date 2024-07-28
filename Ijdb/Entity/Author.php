<?php

namespace Ijdb\Entity;

use Ninja\DatabaseTable;

class Author 
{
  const EDIT_JOKE = 1;
  const DELETE_JOKE = 2;
  const LIST_CATEGORIES = 4;
  const EDIT_CATEGORY = 8;
  const DELETE_CATEGORY = 16;
  const EDIT_USER_ACCESS = 32;

  public int $id;

  public string $name;

  public string $email;

  public string $password;

  public int $permissions;

  public function __construct(private DatabaseTable $jokesTable) {

  }
  
  public function getJokes() {
    return $this->jokesTable->find('authorId', $this->id);
  }

  public function addJoke(array $joke) {
    // set the `authorId` in the new joke to the id stored in this instance
    $joke['authorId'] = $this->id;

    return $this->jokesTable->save($joke); //this should return a joke entity
  }

  public function hasPermission(int $permission) {
    return $this->permissions & $permission;
  }
}