<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Inertia\Inertia;

class DownloadsController extends Controller
{
    public function index($token)
    {
        $appointment = Appointment::where('token', $token)->firstOrFail();

        if ($appointment->status !== 'confirmed' && $appointment->payment_status !== 'paid') {
            return redirect('/')->with('error', 'Tu cita aún no está confirmada o pagada.');
        }

        return Inertia::render('Shop/Downloads', [
            'appointment' => $appointment->load('service'),
            'files' => [
                ['name' => 'Manual de Bienvenida.pdf', 'url' => '#'],
                ['name' => 'Guía de Terapia.pdf', 'url' => '#'],
                ['name' => 'Video Introductorio.mp4', 'url' => '#'],
            ]
        ]);
    }
}
