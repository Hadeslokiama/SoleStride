<?php
$plainPassword = 'Admin123!';
$hash = password_hash($plainPassword, PASSWORD_DEFAULT);
echo $hash . PHP_EOL;

// Verify it works before you trust it
var_dump(password_verify($plainPassword, $hash));