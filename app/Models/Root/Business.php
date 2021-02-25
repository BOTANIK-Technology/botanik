<?php

namespace App\Models\Root;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Log;
use Exception;

/**
 * App\Models\Root\Business
 *
 * @property int $id
 * @property string $name
 * @property string|null $bot_name
 * @property int $status
 * @property int $package_id
 * @property string $db_name
 * @property string $slug
 * @property string|null $img
 * @property string $token
 * @property string $pay_token
 * @property int $catalog
 * @property int $owner_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Root\Owner|null $owner
 * @property-read \App\Models\Root\Package $package
 * @method static \Illuminate\Database\Eloquent\Builder|Business newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Business newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Business query()
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereBotName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereCatalog($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereDbName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business wherePayToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Business extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function package ()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function owner ()
    {
        return $this->hasOne(Owner::class, 'id', 'owner_id');
    }

    /**
     * Toggle business status.
     *
     * @param $id
     * @return array|Business
     */
    public static function changeStatus ($id)
    {
        if (!$obj = self::find($id))
            return ['error' => 'Business with id="'.$id.'" not find in the table'];

        $obj->status ? $obj->status = false : $obj->status = true;

        try {
            $obj->save();
            return $obj;
        }
        catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * @return array|bool
     */
    public function getChart ()
    {
        if (!\ConnectService::dbConnect($this->db_name))
            return false;

        $array = [
            'records'   => \App\Models\Record::count() ?? 0,
            'total'     => \App\Models\Report::allTimeTotal(),
            'info'      => \App\Models\Information::count() ?? 0,
            'feedback'  => \App\Models\FeedBack::count() ?? 0,
            'complaint' => \App\Models\FeedBack::where('stars', '<', 4)->count() ?? 0,
            'reviews'   => \App\Models\Review::count() ?? 0,
            'users'     => \App\Models\User::count() - 1,
            'admins'    => \App\Models\Role::where('slug', 'admin')->first()->users->count() ?? 0,
            'services'  => \App\Models\Service::count() ?? 0,
            'addrs'     => \App\Models\Address::count() ?? 0,
        ];

        $array['complaint'] += \App\Models\Review::where('stars', '<', 4)->count() ?? 0;

        \ConnectService::setDefaultConnect();
        return $array;
    }

    /**
     * Delete the business from the database.
     * Delete the business database.
     * Delete the business logo's from storage/app/public/logos/.
     * Delete owner model.
     * Unset webhook.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function delete()
    {
        if ( \ConnectService::isExist($this->db_name) ) {
            try {
                \DB::statement("DROP DATABASE `{$this->db_name}`");
            }
            catch (Exception $e) {
                Log::error($e->getMessage().' ** A level - delete database '.$this->db_name);
                return false;
            }
        }

        $fs = \Storage::disk('public');
        $fs->delete('/'.$this->img);

        $this->owner()->delete();

        $this->deleteWebhook();

        $this->mergeAttributesFromClassCasts();

        if (is_null($this->getKeyName())) {
            throw new Exception('No primary key defined on model.');
        }

        if (! $this->exists) {
            return false;
        }

        if ($this->fireModelEvent('deleting') === false) {
            return false;
        }

        $this->touchOwners();
        $this->performDeleteOnModel();
        $this->fireModelEvent('deleted', false);

        return true;
    }

    /**
     * @param string $path
     */
    public function changeLogo (string $path)
    {
        $fs = \Storage::disk('public');
        $fs->delete('/'.$this->img);
        $this->img = $path;
        $this->save();
    }

    /**
     * Deploy the business system
     *
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deploy ()
    {
        // Create a new database
        try {
            \Artisan::call('make:database', ['dbname' => $this->db_name]);
        }
        catch (Exception $e) {
            Log::error($e->getMessage().' *** Cannot create "'.$this->db_name.'" database');
            return false;
        }

        $db_name = $this->db_name;
        $name = $this->owner->fio;
        $email = $this->owner->email;
        $password = $this->owner->password;
        $slug = $this->slug;
        $business_name = $this->name;

        // Connect to created database
        if ( ! \ConnectService::dbConnect($db_name) )
            return false;

        // Do migrate
        try {
            \Artisan::call('migrate', ['--force' => true]);
        }
        catch (Exception $e) {
            Log::error($e->getMessage().' *** Cannot migrate "'.$db_name.'" database');
            return false;
        }

        // Do seed
        try {
            \Artisan::call('db:seed');
        } catch (Exception $e) {
            Log::error($e->getMessage().' *** Cannot seed "'.$db_name.'" database');
            return false;
        }

        // Create owner account
        $role = \App\Models\Role::where('slug', 'owner')->first();
        $owner = \App\Models\User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => bcrypt($password),
        ]);

        if (!$owner) {
            Log::error(' *** Cannot create user in "'.$db_name.'" database');
            return false;
        }

        $owner->roles()->attach($role);

        try {
            \Mail::send('emails.user-create', ['login' => $email, 'password' => $password, 'slug' => $slug], function ($message) use ($name, $email, $business_name) {
                $message->to($email, $name)->subject(__('Доступ к BOTANIK - '.$business_name));
            });
        } catch (Exception $e) {
            Log::warning($e->getMessage().' *** Cannot send auth data to owner. "'.$db_name.'" database');
        }

        \ConnectService::setDefaultConnect();
        return $this->setWebhook();
    }

    /**
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setWebhook ()
    {
        $base_url = \URL::to('/');
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request(
                'POST',
                $base_url.'/api/telegram/'.$this->slug.'/admin',
                [
                    'json' => [
                        'gess_key' => getenv('APP_KEY'),
                        'call' => 'setWebhook',
                        'params' => [
                            'url' => $base_url.'/api/telegram/'.$this->slug
                        ]
                    ]
                ]
            );

            return $response->getBody()->getContents();

        } catch (Exception $e) {

            return $e->getMessage();

        }
    }

    public function deleteWebhook ()
    {
        $base_url = \URL::to('/');
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request(
                'POST',
                $base_url.'/api/telegram/'.$this->slug.'/admin',
                [
                    'json' => [
                        'gess_key' => getenv('APP_KEY'),
                        'call' => 'deleteWebhook'
                    ]
                ]
            );

            return $response->getBody()->getContents();

        } catch (Exception $e) {

            return $e->getMessage();

        }
    }

    /**
     * @return bool|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getWebhookInfo ()
    {
        $base_url = \URL::to('/');
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request(
                'POST',
                $base_url.'/api/telegram/'.$this->slug.'/admin',
                [
                    'json' => [
                        'gess_key' => getenv('APP_KEY'),
                        'call' => 'getWebhookInfo',
                        'params' => [
                            'url' => $base_url.'/api/telegram/'.$this->slug
                        ]
                    ]
                ]
            );

            return $response->getBody()->getContents();

        } catch (Exception $e) {

            return $e->getMessage();

        }
    }
}
