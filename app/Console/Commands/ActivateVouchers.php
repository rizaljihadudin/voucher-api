<?php

namespace App\Console\Commands;

use App\Models\Voucher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ActivateVouchers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activate:vouchers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate vouchers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info("Cron Job running at ". now());
        $aCount = Voucher::where('start_date', '<=', now())
            ->where('is_active', false)
            ->update(['is_active' => true]);

        Log::info('Vouchers activated successfully');
        $this->info("Activated {$aCount} vouchers.");
    }
}
