<?php
$input = "123456"; // lo que escribes en el login
$hash  = '$2y$10$NTR56hCgrCmD5r/fvUvGxOKb0KDz5OHmOrsBxdbQ286qFWmR67Uke'; // copia exacta de la BD

if (password_verify($input, $hash)) {
    echo "✅ Coincide";
} else {
    echo "❌ No coincide";
}