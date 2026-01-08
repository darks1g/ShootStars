<?php
require_once __DIR__ . '/backend/auth/TokenManager.php';

echo "Testing TokenManager...\n";
$tm = new TokenManager();
$email = "test@example.com";

echo "Creating token for $email...\n";
$token = $tm->createToken($email);
echo "Token: " . substr($token, 0, 10) . "...\n";

echo "Validating token...\n";
$validatedEmail = $tm->validateToken($token);

if ($validatedEmail === $email) {
    echo "SUCCESS: Token validated correctly.\n";
} else {
    echo "FAILURE: Token validation failed.\n";
}

echo "Removing token...\n";
$tm->removeToken($token);
if ($tm->validateToken($token) === false) {
    echo "SUCCESS: Token removed.\n";
} else {
    echo "FAILURE: Token still valid.\n";
}
