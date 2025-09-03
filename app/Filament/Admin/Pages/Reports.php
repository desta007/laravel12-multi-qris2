<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Qris;
use Carbon\Carbon;

class Reports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.reports';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 1;

    public $startDate;
    public $endDate;
    public $qrisId;
    public $transactions = [];
    public $totalAmount = 0;
    public $totalFee = 0;
    public $totalCount = 0;
    public $qrisList = [];

    public function mount()
    {
        // Ensure only admin and master_admin can access reports
        if (!Auth::user()->hasAnyRole(['master_admin', 'admin'])) {
            abort(403, 'Unauthorized access');
        }

        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->qrisList = Qris::all();
        $this->loadTransactions();
    }

    public function loadTransactions()
    {
        $query = Transaction::with(['user', 'qris'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate]);
            
        if ($this->qrisId) {
            $query->where('qris_id', $this->qrisId);
        }
        
        $this->transactions = $query->orderBy('created_at', 'desc')->get();
        
        $this->totalAmount = $this->transactions->sum('amount');
        $this->totalFee = $this->transactions->sum('fee');
        $this->totalCount = $this->transactions->count();
    }

    public function filter()
    {
        $this->loadTransactions();
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasAnyRole(['master_admin', 'admin']);
    }
}