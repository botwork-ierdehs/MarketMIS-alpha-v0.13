<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ApprovalPermitController extends Controller
{
    /**
     * Store a new approval record for a permit.
     */
    public function storeApproval($permitId, $departmentId, $approverId, $status = 1)
    {
        DB::table('approval_permits')->insert([
            'permit_id'     => $permitId,
            'department_id' => $departmentId,
            'approver_id'   => $approverId,
            'status'        => $status, // 1 = Approved, 2 = Pending, 3 = Failed
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return true;
    }
}
