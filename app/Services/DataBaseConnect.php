<?php

namespace App\Services;

use App\Models\Root\Business;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DataBaseConnect
{
    /**
     * Check business.
     *
     * @param  string $slug
     * @return mixed
     */
    public function isBusiness (string $slug)
    {
        $business = session()->get('business', false);
        if (!$business) {
            if ($this->isDefaultConnect()) {
                $business = Business::where('slug', $slug)->first();
                if ($business) {
                    if (Auth::check())
                    {
                        session()->put(['business' => $business]);
                    }
                    return $business;
                }
                return false;
            }
            else {
                Log::info('Function $this->isDefaultConnect() returned FALSE in App\Services\DataBaseConnect::isBusiness. Business slug: '.$slug);
            }
        }
        return $business;
    }

    /**
     * Connect to $db_name DB.
     *
     * @param  string $db_name
     * @return boolean
     */
    public function dbConnect (string $db_name): bool
    {
        try {
            Config::set('database.connections.mysql.database', $db_name);
            DB::purge('mysql');
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Return true if app connect to env database
     * else return false
     *
     * @return bool
     */
    public function isDefaultConnect () : bool
    {
        if (DB::connection()->getConfig()['database'] == getenv('DB_DATABASE'))
            return true;
        return false;
    }

    /**
     * Return true if database $db_name exist
     * else return false
     *
     * @param string $db_name
     * @return bool
     */
    public function isExist (string $db_name) : bool
    {
        $result = $this->dbConnect($db_name);
        if ( $this->setDefaultConnect() )
            return $result;
        else
            return false;
    }

    /**
     * @return bool
     */
    public function setDefaultConnect () : bool
    {
        return $this->dbConnect(getenv('DB_DATABASE'));
    }


    /**
     * @return bool
     */
    public function prepareJob () : bool
    {
        if (!$this->isDefaultConnect())
            return $this->setDefaultConnect();
        return true;
    }

}
