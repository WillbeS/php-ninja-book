<?php

namespace Ijdb\Controllers;

use Ninja\DatabaseTable;

class Author
{
    const BASE_URL = "sandbox/ninja-book/public/";

    public function __construct(
        private DatabaseTable $authorsTable
    ) {
    }

    public function index() {
        echo " Here";
        die;
        
    }

    public function list() {
        $authors = $this->authorsTable->findAll();
      
        return [
            'template' => 'authorlist.html.php',
            'title' => 'Author List',
            'variables' => [
                'authors' => $authors,
                'baseUrl' => self::BASE_URL,
          ]
        ];
    }

    public function registrationForm() {
        // echo "Registration form";
        // die;
        return [
            'template' => 'register.html.php',
            'title' => 'Register an account'
         ];
    }

    public function registrationFormSubmit() {
        $author = $_POST['author'];

        // Validation rules
        $errors = [];

        if (empty($author['name'])) {
            $errors[] = 'Name cannot be blank';
        }

        if (empty($author['email'])) {
            $errors[] = 'Email cannot be blank';
        }
        else if (filter_var($author['email'], FILTER_VALIDATE_EMAIL) == false) {
            $errors[] = 'Invalid email address';
        } else {
            $author['email'] = strtolower($author['email']);
            if (count($this->authorsTable->find('email', $author['email'])) > 0) {
                $errors[] = 'That email address is already registered';
            }
        }

        if (empty($author['password'])) {
            $errors[] = 'Password cannot be blank';
        } 
       
        // End of validation rules //////////////////

        if (count($errors) === 0) {
            // Hash the password before saving it in the database
            $author['password'] = password_hash($author['password'], PASSWORD_DEFAULT);

            // When submitted, the $author variable now contains a lowercase value for email
            // and a hashed password
            $this->authorsTable->save($author);
        
            header('location: /' . self::BASE_URL .'author/success');
        }
        else {
            return [
                'template' => 'register.html.php',
                'title' => 'Register an account',
                'variables' => [
                    'errors' => $errors,
                    'author' => $author
                ]
            ];
        }
    }

    public function success() {
        return [
            'template' => 'registersuccess.html.php',
            'title' => 'Registration Successful'
        ];
    }

    public function permissions(int $id) {
        $author = $this->authorsTable->find('id', $id)[0];

        $reflected = new \ReflectionClass('\Ijdb\Entity\Author');
        $constants = $reflected->getConstants();

        return [
            'template' => 'permissions.html.php',
            'title' => 'Edit Permissions',
            'variables' => [
                'author' => $author,
                'permissions' => $constants
            ]   
        ];
    }

    public function permissionsSubmit(int $id) {
        $author = [
          'id' =>$id,
          'permissions' => array_sum($_POST['permissions'] ?? [])
        ];
      
        $this->authorsTable->save($author);
      
        header('location: /' . self::BASE_URL .'author/list');
      }
}