<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;

class ActivateVoucher extends Command
{
    protected $signature = 'app:activate-voucher';
    protected $description = 'Activate or deactivate vouchers based on start and end dates';

    public function handle()
    {
        try {
           $vouchers = Voucher::get();
           foreach ($vouchers as $voucher){
                if ($voucher['start_date'] <= now() && $voucher['end_date'] >= now() && $voucher['active'] == false) {
                    $voucher->active = true;
                } else {
                    $voucher->active = false;
                }
                $voucher->save();
           }
           $this->info('Vouchers activate and deactivate successfully');
        } catch (\Exception $e) {
            $this->error('Error updating vouchers: ' . $e->getMessage());
        }
    }
}

