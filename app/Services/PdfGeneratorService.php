<?php

namespace App\Services;

use App\Models\ReservationItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfGeneratorService
{
    /**
     * Generate Client Booking Voucher PDF
     */
    public function generateClientVoucher(ReservationItem $item)
    {
        $data = [
            'item' => $item,
            'title' => 'Booking Voucher',
            'type' => 'client'
        ];

        // Ensure views exist
        if (!view()->exists('pdfs.voucher')) {
            throw new \Exception("View pdfs.voucher not found");
        }

        $pdf = Pdf::loadView('pdfs.voucher', $data);
        return $pdf->output(); // Returns binary PDF string
    }

    /**
     * Generate Provider Work Order PDF
     */
    public function generateProviderWorkOrder(ReservationItem $item)
    {
        $data = [
            'item' => $item,
            'title' => 'Service Order',
            'type' => 'provider'
        ];

        if (!view()->exists('pdfs.work_order')) {
            throw new \Exception("View pdfs.work_order not found");
        }

        $pdf = Pdf::loadView('pdfs.work_order', $data);
        return $pdf->output();
    }
}
