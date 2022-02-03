<?php

namespace App\Console\Commands;

use App\Facades\ConnectService;
use App\Models\Root\Business;
use Illuminate\Console\Command;

class MigrateRollbackDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:back_database {dbname?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback migration on business database';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $dbname = $this->argument('dbname');
            if(! $dbname) {
                ConnectService::dbConnect(getenv('DB_DATABASE'));
                $bases = Business::get()->pluck('db_name')->toArray();
            }
            else {
                $bases = [$dbname];
            }
            foreach ($bases as $db) {
                echo 'Rollback DB ' . $db . "\n";
                ConnectService::dbConnect($db);
                $this->call('migrate:rollback', ['--step' => 1]);
            }
        }
        catch (\Exception $e){
            $this->error($e->getMessage());
        }
    }
}
