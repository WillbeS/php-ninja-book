<?php

namespace Ijdb\Controllers;

use Ninja\Authentication;

class Login {
    const BASE_URL = "sandbox/ninja-book/public/";

    public function __construct(private Authentication $authentication) {

    }

    public function login() {

        return [
            'template' => 'loginform.html.php',
            'title' => 'Log in',
            'variables' => [
                    'baseUrl' => self::BASE_URL
            ]
        ];
    }

    public function loginSubmit() {
        $success = $this->authentication->login($_POST['email'], $_POST['password']);

        if ($success) {
            return ['template' => 'loginSuccess.html.php',
                    'title' => 'Log In Successful'
                   ];
        }
        else {
            return [
                'template' => 'loginForm.html.php',
                'title' => 'Log in',
                'variables' => [
                    'errorMessage' => true,
                    'baseUrl' => self::BASE_URL
                ]
            ];
        } 
    }

    public function logout() {
        $this->authentication->logout();
        header('location: /' . self::BASE_URL);
    }
        
}