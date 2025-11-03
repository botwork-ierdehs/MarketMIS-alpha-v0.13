<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Permits;
use App\Http\Requests\PermitRequest;
use Illuminate\Support\Facades\Log;

class PermitController extends Controller
{
    public function countPerStatus(){
        $statuses = [
            0 => 'Pending',
            1 => 'Approved',
            2 => 'Rejected',
            3 => 'Expired',
            4 => 'Terminated',
        ];

        $rawCounts = Permits::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status') // returns [status => total]
            ->toArray();

        $finalCounts = [];
            foreach ($statuses as $key => $label) {
                $finalCounts[] = [
                    'label' => $label,
                    'status' => $key,
                    'total' => $rawCounts[$key] ?? 0,
                ];
            }
        // Final Output
        return $finalCounts;
    }

    public function addBusinessPermit(array $request) {
          Log::info('Login attempt', [
            'request' => $request 
        ]);
        return Permits::create($request);
    }

    public function generatePermitNumber(int $length = 8): string {
    // Characters to choose from (A-Z uppercase)
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $permitNumber = '';
        
        for ($i = 0; $i < $length; $i++) {
            $permitNumber .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        return $permitNumber;
    }

    public function update(array $payload, $permit)
    {
        Log::info('update', [
            'payload' => $payload,
            'permits' => $permit, 
        ]);
        return Permits::where('id', $permit)->update($payload);
    }
}
