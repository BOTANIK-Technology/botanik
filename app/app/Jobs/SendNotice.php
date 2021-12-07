<?php

namespace App\Jobs;

use App\Facades\ConnectService;
use App\Models\Notice;
use App\Models\Role;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;
use Illuminate\Support\Facades\Log;


class SendNotice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notices = [];
    protected $db;

    /**
     * Create a new job instance.
     *
     * @param string $db
     * @param array $notices
     *
     * $notices is array of App\Models\Notice $fillable arrays.
     *
     * Example:
     *     $notices = [
     *         [
     *             'user_id'    => null|int,
     *             'role_id'    => null|int,
     *             'role_slug'  => null|string,
     *             'address_id' => null|int,
     *             'created_at' => null|date,
     *             'updated_at' => null|date,
     *             'seen'       => bool|default false,
     *             'message'    => string
     *         ], ...
     *     ]
     *
     */
    public function __construct(string $db, array $notices)
    {
        if (empty($notices))
            return;

        $this->db = $db;
        $this->notices = $notices;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!ConnectService::dbConnect($this->db))
            return;
        foreach ($this->notices as $notice) {

            if (isset($notice['role_slug'])) {
                $notice['role_id'] = Role::where('slug', $notice['role_slug'])->first()->id;
                unset($notice['role_slug']);
            }
            /** @var Notice $res */
            $res = Notice::create($notice);
        }
    }

    /**
     * The job failed to process.
     *
     * @param Exception $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        Log::error($exception->getMessage() . ' * File: ' . $exception->getFile() . ' Line: ' . $exception->getLine() . ' * Database: ' . $this->db);
    }
}
