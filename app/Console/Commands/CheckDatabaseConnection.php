<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class CheckDatabaseConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-database-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the configured database connection without modifying data';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            DB::connection()->getPdo();
            DB::select('SELECT 1');
            $this->info('Database connection successful ('.DB::connection()->getDriverName().').');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            // Avoid exposing credentials or the connection string in console output.
            $this->error('Database connection failed. Check the database configuration, TLS and network access.');

            return self::FAILURE;
        }
    }
}
