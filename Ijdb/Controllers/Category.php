<?php

namespace Ijdb\Controllers;

use Ninja\DatabaseTable;

class Category {

    const BASE_URL = "sandbox/ninja-book/public/";

    public function __construct(private DatabaseTable $categoriesTable) {

    }

    public function list() {
        return ['template' => 'categories.html.php',
          'title' => 'Joke Categories',
          'variables' => [
            'categories' => $this->categoriesTable->findAll(),
            'baseUrl' => self::BASE_URL,
          ]
        ];
    }

    public function edit(?string $id = null) {

        if (isset($id)) {
          $category = $this->categoriesTable->find('id', $id)[0];
        }
      
        return ['template' => 'editcategory.html.php',
          'title' =>  'Edit Category',
          'variables' => [
            'category' => $category ?? null
          ]
        ];
    }

    public function editSubmit() {
        $category = $_POST['category'];
      
        $this->categoriesTable->save($category);

        header('location: /' . self::BASE_URL . 'category/list' );
    }
}