<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Promo;
use Carbon\Carbon;

class DeleteExpiredPromos extends Command
{
    protected $signature = 'promo:delete-expired';
    protected $description = 'Delete promos that have passed their end_date';

    public function handle()
    {
        $expiredPromos = Promo::where('end_date', '<', Carbon::now())->delete();

        $this->info($expiredPromos . ' expired promos deleted.');
    }
}
