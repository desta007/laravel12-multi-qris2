<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BcaQrisService;
use Illuminate\Support\Str;

class QrisController extends Controller
{
    protected $bcaQrisService;

    public function __construct(BcaQrisService $bcaQrisService)
    {
        $this->bcaQrisService = $bcaQrisService;
    }

    /**
     * Menampilkan halaman untuk generate QRIS
     */
    public function showForm()
    {
        return view('qris.form'); // Kita akan buat view ini nanti
    }

    /**
     * Proses generate QRIS dan menampilkan hasilnya
     */
    public function generate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $amount = (float) $request->input('amount');
        // Transaction ID harus unik untuk setiap request
        $transactionId = 'INV-' . time() . '-' . Str::random(5);

        $result = $this->bcaQrisService->generateQris($amount, $transactionId);

        if ($result['success']) {
            // Ambil konten QR dari response BCA
            $qrContent = $result['data']['qrContent'];
            return view('qris.show', compact('qrContent', 'amount'));
        } else {
            // Tampilkan error jika gagal
            return back()->withErrors(['api_error' => 'Gagal membuat QRIS: ' . json_encode($result['error'])]);
        }
    }
}
