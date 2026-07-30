<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;

final class AuthController extends BaseController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            redirect('dashboard');
        }

        $this->render('login', ['title' => 'Anmelden']);
    }

    public function login(): void
    {
        if (Auth::check()) {
            redirect('dashboard');
        }

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            flash('error', 'Bitte geben Sie E-Mail-Adresse und Passwort ein.');
            redirect('login');
        }

        if (!Auth::attempt($email, $password)) {
            flash('error', 'Anmeldung fehlgeschlagen. Bitte prüfen Sie die Zugangsdaten.');
            redirect('login');
        }

        $this->audit('login', 'session');
        flash('success', 'Willkommen im Bibliothksystem.');
        redirect('dashboard');
    }

    public function logout(): void
    {
        Auth::requireLogin();
        $this->audit('logout', 'session');
        Auth::logout();
        flash('success', 'Sie wurden sicher abgemeldet.');
        redirect('login');
    }
}

