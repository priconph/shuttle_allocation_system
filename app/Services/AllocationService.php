<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Allocations;

class AllocationService
{
    /**
     * Change allocation status.
     *
     * @param string $controlNo
     * @param int    $deleteRequestStatus
     * @return array
     */
    // public function changeStatus($controlNo, $deleteRequestStatus){
    //     DB::beginTransaction();
    //     try {
    //         if ($deleteRequestStatus == 0) {
    //             $change_status_to = 1;
    //         } elseif ($deleteRequestStatus == 1) {
    //             $change_status_to = 0;
    //         } else {
    //             // Run only when setting status to "finished"
    //             $change_status_to = 2;
    //         }

    //         Allocations::where('control_number', $controlNo)
    //             ->update([
    //                 'request_status' => $change_status_to,
    //                 'updated_at' => now()
    //             ]);

    //         DB::commit();
    //         return ['hasError' => 0];
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return ['hasError' => 1, 'exceptionError' => $e->getMessage()];
    //     }
    // }
}
