<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PendingTrade;
use Illuminate\Support\Facades\Log;

class PruneExpiredTrades extends Command
{

    protected $signature = 'trades:prune-expired';


    protected $description = 'Deletes pending trades that are older than their expiration time.';

    public function handle()
    {
        $this->info('Starting to prune expired trades...');

        $count = PendingTrade::where('expires_at', '<', now())->delete();

        $this->info("Done. Pruned {$count} expired trades.");

        Log::info("Scheduler: Pruned {$count} expired trades.");

        return Command::SUCCESS;
    }
}
