<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $search = $request->query('q');

        $query = Transaction::with(['user', 'plan', 'invitation'])->latest();

        if ($status && in_array($status, ['paid', 'pending', 'failed', 'expired'])) {
            $query->where('status', $status);
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', Carbon::parse($startDate));
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', Carbon::parse($endDate));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('email', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->paginate(20)->withQueryString();

        $stats = [
            'total_amount' => Transaction::where('status', 'paid')->sum('amount'),
            'total_count' => Transaction::count(),
            'paid_count' => Transaction::where('status', 'paid')->count(),
            'pending_count' => Transaction::where('status', 'pending')->count(),
        ];

        return view('admin.transactions.index', compact('transactions', 'stats', 'status', 'startDate', 'endDate', 'search'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $status = $request->query('status');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = Transaction::with(['user', 'plan', 'invitation'])->latest();

        if ($status && in_array($status, ['paid', 'pending', 'failed', 'expired'])) {
            $query->where('status', $status);
        }
        if ($startDate) {
            $query->whereDate('created_at', '>=', Carbon::parse($startDate));
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', Carbon::parse($endDate));
        }

        $transactions = $query->get();
        $filename = "transaksi-kawinan-" . date('Ymd-His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($transactions) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($out, ['Order ID', 'Pelanggan', 'Email', 'Paket', 'Nominal (Rp)', 'Status', 'Gateway', 'Tanggal Transaksi', 'Tanggal Bayar', 'Undangan Slug']);

            foreach ($transactions as $t) {
                fputcsv($out, [
                    $t->order_id,
                    $t->user->name ?? '-',
                    $t->user->email ?? '-',
                    $t->plan->name ?? '-',
                    $t->amount,
                    strtoupper($t->status),
                    strtoupper($t->gateway),
                    $t->created_at->format('Y-m-d H:i:s'),
                    $t->paid_at ? $t->paid_at->format('Y-m-d H:i:s') : '-',
                    $t->invitation->slug ?? '-',
                ]);
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
