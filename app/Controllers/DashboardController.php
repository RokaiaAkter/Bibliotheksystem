<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Repositories\BookRepository;
use App\Repositories\LoanRepository;
use App\Repositories\PhoneRepository;
use App\Repositories\TicketRepository;
use App\Repositories\UserRepository;

final class DashboardController extends BaseController
{
    public function index(): void
    {
        Auth::requirePermission('dashboard.view');

        $books = new BookRepository($this->database);
        $loans = new LoanRepository($this->database);
        $tickets = new TicketRepository($this->database);
        $users = new UserRepository($this->database);
        $phones = new PhoneRepository($this->database);

        $this->render('dashboard', [
            'title' => 'Dashboard',
            'bookCounts' => $books->counts(),
            'activeLoans' => $loans->countActive(),
            'ticketCounts' => $tickets->counts(),
            'activeUsers' => $users->countActive(),
            'activePhones' => $phones->countActive(),
            'recentAudits' => $this->audit->recent(8),
        ]);
    }
}

