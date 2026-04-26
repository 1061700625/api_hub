<?php

function start_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function current_admin(): ?array
{
    start_session();

    if (empty($_SESSION['admin_id'])) {
        return null;
    }

    $admin = db()->getAdminById((int)$_SESSION['admin_id']);
    if (!$admin) {
        return null;
    }
    unset($admin['password_hash']);
    return $admin;
}

function require_admin(): array
{
    $admin = current_admin();
    if (!$admin) {
        redirect('/admin/login');
    }
    return $admin;
}

function login_admin(string $username, string $password): bool
{
    start_session();

    $admin = db()->getAdminByUsername($username);
    if (!$admin || !password_verify($password, $admin['password_hash'])) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['admin_id'] = $admin['id'];
    return true;
}

function logout_admin(): void
{
    start_session();
    $_SESSION = [];
    session_destroy();
}
