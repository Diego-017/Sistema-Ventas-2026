<?php
namespace App\Http\Controllers;

abstract class Controller
{
    protected function isAdmin(): bool
    {
        return (session('user')['rol'] ?? '') === 'admin';
    }

    protected function requireAdmin(): void
    {
        if (!$this->isAdmin()) abort(403, 'Acceso solo para administradores.');
    }

    protected function userId(): int
    {
        return (int) session('user_id');
    }
}
