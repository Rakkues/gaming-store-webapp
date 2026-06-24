<?php

function securePasswordHash(string $password): string {
    return password_hash($password, PASSWORD_ARGON2ID);
}

function verifyPassword(string $entered, string $stored): bool {
    return password_verify($entered, $stored);
}

function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfValid(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}