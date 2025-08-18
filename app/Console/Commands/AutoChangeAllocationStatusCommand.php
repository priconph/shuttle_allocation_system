<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AllocationService;
use Carbon\Carbon;
use App\Models\Allocations;

class AutoChangeAllocationStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    // protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    // public function handle()
    // {
    //     return 0;
    // }

    // public function handle(AllocationService $allocationService)
    // {
    //     $allocations = \App\Models\Allocations::where('request_status', 0)->get();

    //     foreach ($allocations as $alloc) {
    //         $allocationService->changeStatus($alloc->control_number, $alloc->request_status);
    //     }

    //     $this->info("Allocation statuses updated successfully.");
    // }

    protected $signature = 'allocations:auto-close';
    protected $description = 'Change allocation status automatically based on time';

    public function handle()
    {
        $today = Carbon::today()->toDateString();
        // Find allocations that have ended and are not yet status 2
        $affected = Allocations::whereDate('alloc_date_end', '<', $today)
            ->where('request_status', '!=', 2)
            ->update([
                'request_status' => 2,
                'updated_at' => now()
            ]);

        $this->info("$affected allocation(s) closed.");
    }
}
