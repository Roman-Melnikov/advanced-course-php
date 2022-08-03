<?php
use Melni\AdvancedCoursePhp\Blog\{User, Post, Comment};

function createUser($faker): User
{
    $id = (int)$faker->uuid();
    $name = $faker->firstName();
    $surname = $faker->lastName();

    return new User($id, $name, $surname);
}

function createPost($faker): Post
{
    $id = (int)$faker->uuid();
    $heading = $faker->title();
    $text = $faker->text();

    return new Post($id, createUser($faker), $heading, $text);
}

function createComment($faker): Comment
{
    $id = (int)$faker->uuid();
    $text = $faker->text();

    return new Comment($id, createUser($faker), createPost($faker), $text);
}
