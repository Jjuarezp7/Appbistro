<?php
$hash = '$2y$10$wHq3xX7QfY9cQ9xZpQ5eUuQZyXz5vZJfQ0ZpQ5eUuQZyXz5vZJfQ0';
$password = 'admin123';

var_dump(password_verify($password, $hash));