<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


use Carbon\Carbon;

use App\Models\Payment;

use App\Http\Requests\PaymentRequest;
use Illuminate\Support\Facades\Auth;


class PaymentController extends Controller
{
    public function paymentNow(PaymentRequest $request)
    {
        $user = Auth::user();
        Log::info('User attempting payment', ['$request-' => $request]);
        $payload = $request->validated();
        $payload['user_id'] = $user->id;
        if(isset($payload['stall_rental_id'])) {
            $payload['stall_rental_id'] = (int) $payload['stall_rental_id'] ?? null;
        } 

        if(isset($payload['table_rental_id'])) {
            $payload['table_rental_id'] = (int) $payload['table_rental_id'] ?? null;
        } 

        if(isset($payload['volantes_id'])) {
            $payload['volantes_id'] = (int) $payload['volantes_id'] ?? null;
        }
        $this->addBusinessPermit($payload);

        return redirect()->back()->with('success', 'Rental application paid.');
    }

    public function rentalPayment(PaymentRequest $request)
    {

        // $user = Auth::user();

        $payload = $request->validated();
        $payload['user_id'] = 5;

        $payment = $this->addBusinessPermit($payload);

        return response()->json([
            'message' => $payment
                ? 'Table rental application paid for this quarter.'
                : 'Payment failed.',
            'data'    => $payload,
        ], $payment ? 201 : 400);
    }

    private function addBusinessPermit(array $payload) 
    {
        Log::info('Payload: ', $payload);
        $file = isset($payload['receipt']) ? $payload['receipt'] : null;

        if (!$file || !method_exists($file, 'getClientOriginalExtension')) {
            return null; // File not uploaded or invalid
        }

        $uuidName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        // ✅ Move file immediately to public/uploads
        $file->move(public_path('uploads'), $uuidName);

        $path = 'uploads/' . $uuidName;
        $date = Carbon::now()->format('Y-m-d');

        return Payment::create([
            'user_id' => $payload['user_id'] ?? null,
            'stall_rental_id' => $payload['stall_rental_id'] ?? null,
            'volantes_id' => $payload['volantes_id'] ?? null,
            'table_rental_id' => $payload['table_rental_id'] ?? null,
            'receipt' => 'receipt-'.$date.'-'.$uuidName,
            'date' => $date,
            'amount' => $payload['amount'] ?? null,
            'reference_number' => $payload['reference_number'] ?? null,
            'status' => $file ? 1 : 2, // 1 - Paid, 2 - Pending, 3 - Failed
        ]);
    }
}
