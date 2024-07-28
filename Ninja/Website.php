<?php

namespace Ninja;


interface Website
{
    public function getDefaultRoute();

    public function getLayoutVariables(): array;

    public function getController(string $controllerName): ?object;

    public function checkLogin(string $uri): ?string;
}