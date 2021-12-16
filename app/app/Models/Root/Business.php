<?php

namespace App\Models\Root;

use App\Facades\ConnectService;
use App\Models\Address;
use App\Models\FeedBack;
use App\Models\Information;
use App\Models\Record;
use App\Models\Report;
use App\Models\Review;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Owner|null $owner
 * @property-read Package $package
 * @method static Builder|Business newModelQuery()
 * @method static Builder|Business newQuery()
 * @method static Builder|Business query()
 * @method static Builder|Business whereBotName($value)
 * @method static Builder|Business whereCatalog($value)
 * @method static Builder|Business whereCreatedAt($value)
 * @method static Builder|Business whereDbName($value)
 * @method static Builder|Business whereId($value)
 * @method static Builder|Business whereImg($value)
 * @method static Builder|Business whereName($value)
 * @method static Builder|Business whereOwnerId($value)
 * @method static Builder|Business wherePackageId($value)
 * @method static Builder|Business wherePayToken($value)
 * @method static Builder|Business whereSlug($value)
 * @method static Builder|Business whereStatus($value)
 * @method static Builder|Business whereToken($value)
 * @method static Builder|Business whereUpdatedAt($value)
 */
class Business extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function package (): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * @return HasOne
     */
    public function owner (): HasOne
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
        {
            return ['error' => 'Business with id="'.$id.'" not find in the table'];
        }

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
        if (!ConnectService::dbConnect($this->db_name))
            return false;

        $array = [
            'records'   => Record::count() ?? 0,
            'total'     => Report::allTimeTotal(),
            'info'      => Information::count() ?? 0,
            'feedback'  => FeedBack::count() ?? 0,
            'complaint' => FeedBack::where('stars', '<', 4)->count() ?? 0,
            'reviews'   => Review::count() ?? 0,
            'users'     => User::count() - 1,
            'admins'    => Role::where('slug', 'admin')->first()->users->count() ?? 0,
            'services'  => Service::count() ?? 0,
            'addrs'     => Address::count() ?? 0,
        ];

        $array['complaint'] += Review::where('stars', '<', 4)->count() ?? 0;

        ConnectService::setDefaultConnect();
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
     * @throws GuzzleException
     */
    public function delete(): bool
    {
        if ( ConnectService::isExist($this->db_name) ) {
            try {
                DB::statement("DROP DATABASE `{$this->db_name}`");
            }
            catch (Exception $e) {
                Log::error($e->getMessage().' ** A level - delete database '.$this->db_name);
                return false;
            }
        }

        $fs = Storage::disk('public');
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
        $fs = Storage::disk('public');
        $fs->delete('/'.$this->img);
        $this->img = $path;
        $this->save();
    }

    /**
     * Deploy the business system
     *
     * @return bool
     * @throws GuzzleException
     */
    public function deploy (): bool
    {
        // Create a new database
        try {
            $res = Artisan::call('make:database', ['dbname' => $this->db_name]);
            Log::debug('DB make', [$res]);
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
        if ( ! ConnectService::dbConnect($db_name) )
        {
            return false;
        }

        // Do migrate
        try {
            Artisan::call('migrate', ['--force' => true]);
        }
        catch (Exception $e) {
            Log::error($e->getMessage().' *** Cannot migrate "'.$db_name.'" database');
            return false;
        }

        // Do seed
        try {
            Artisan::call('db:seed');
        } catch (Exception $e) {
            Log::error($e->getMessage().' *** Cannot seed "'.$db_name.'" database');
            return false;
        }

        // Create owner account
        $role = Role::where('slug', 'owner')->first();
        $owner = User::create([
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
            /*Mail::send('emails.user-create', ['login' => $email, 'password' => $password, 'slug' => $slug], function ($message) use ($name, $email, $business_name) {
                $message->to($email, $name)->subject(__('Доступ к BOTANIK - '.$business_name));
            });*/

            $url = URL::to('/').'/'.$slug.'/login';
            $body = "Логин: <b>{$email}</b><br>Пароль: <b>{$password}</b><br>Ссылка на вход: {$url}";
            $transport = new Swift_SmtpTransport('localhost', 25);
            $mailer = new Swift_Mailer($transport);
            $message = (new Swift_Message('Доступ к BOTANIK' . $business_name))
                ->setFrom([env('MAIL_FROM_ADDRESS') => 'Some One'])
                ->setTo($email)
                ->setBody($body, 'text/html');

            $mailer->send($message);
        } catch (Exception $e) {
            Log::warning($e->getMessage().' *** Cannot send auth data to owner. "'.$db_name.'" database');
        }

        ConnectService::setDefaultConnect();
        return $this->setWebhook();
    }

    /**
     * @return bool
     * @throws GuzzleException
     */
    public function setWebhook (): bool
    {
        $base_url = URL::to('/');
        try {
            $client = new Client();
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

    /**
     * @return string
     * @throws GuzzleException
     */
    public function deleteWebhook (): string
    {
        $base_url = URL::to('/');
        try {
            $client = new Client();
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
     * @throws GuzzleException
     */
    public function getWebhookInfo ()
    {
        $base_url = URL::to('/');
        try {
            $client = new Client();
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
