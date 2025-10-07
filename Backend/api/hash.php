<?php
$clave = "123456"; // Cambiá esto por la contraseña que quieras hashear
$hash = password_hash($clave, PASSWORD_DEFAULT);
echo "<h3>🔐 Hash generado para '$clave'</h3>";
echo "<code>$hash</code>";