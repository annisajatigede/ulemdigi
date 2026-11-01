<?php

namespace App\Console\Commands;

use App\Models\Invitation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpireInvitationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invitations:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set status to expired for invitations that have passed their expires_at date';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = Carbon::now();

        $count = Invitation::where('status', '!=', 'expired')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now)
            ->update([
                'status' => 'expired',
            ]);

        $this->info("Berhasil memperbarui {$count} undangan yang telah melewati masa aktif menjadi 'expired'.");

        return Command::SUCCESS;
    }
}
