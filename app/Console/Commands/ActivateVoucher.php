<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;

class ActivateVoucher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:activate-voucher';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate vouchers based on start date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Use a transaction to ensure atomicity
            DB::transaction(function () {
                $vouchers = Voucher::where('start_date', '<=', now())
                    ->where('active', false)
                    ->update(['active' => true]);

                $this->info($vouchers . ' vouchers activated successfully.');
            });
        } catch (\Exception $e) {
            $this->error('Error activating vouchers: ' . $e->getMessage());
        }
    }
}

