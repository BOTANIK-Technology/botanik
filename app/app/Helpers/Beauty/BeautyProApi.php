<?php

namespace App\Helpers\Beauty;

use App\Models\Api;
use App\Models\Record;
use App\Models\Service;
use App\Models\TelegramUser;
use App\Models\TypeService;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class BeautyProApi
{
    const BASE_URI = 'https://api.aihelps.com/v1/';

    private Client $guzzle;
    private ?string $token;

    /**
     * BeautyProApi constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->token = null;

        $params = [
            'base_uri' => self::BASE_URI,
            'timeout'  => 2.0,
        ];

        $this->guzzle = new Client($params);
        $response = $this->getBearer($config['application_id'], $config['application_secret'], $config['database_code']);
        if(isset($response['access_token'])) {
            $token = $response['access_token'];

            $this->token = $token;
            $params['headers'] = [
                'Authorization' => 'Bearer ' . $token,
            ];
            $this->guzzle = new Client($params);
        }

        if (
            isset($config['expires_at']) &&
            isset($config['access_token']) &&
            Carbon::parse($config['access_token'])->greaterThan(Carbon::now())
        )
            return $config['access_token'];

        if (
            !isset($config['application_id']) ||
            !isset($config['application_secret']) ||
            !isset($config['database_code'])
        )
            return [
                'errors' => [
                    __('Заполните настройки для API интеграции.')
                ]
            ];

        if (isset($response['status']) && ($response['status'] != 'pending' || $response['status'] != 'refused')) {
            $model = Api::where('slug', 'beauty')->first();
            $data = array_merge($config, [
                'access_token' => $response['access_token'],
                'expires_at' => $response['expires_at']
            ]);

            $model->updateConfig($data);
        }

    }

    /**
     * @param string $application_id
     * @param string $application_secret
     * @param int $database_code
     * @return array
     */
    public function getBearer (string $application_id, string $application_secret, int $database_code): array
    {
        return $this->request(
            'auth/database',
            ['query' => [
                    'application_id' => $application_id,
                    'application_secret' => $application_secret,
                    'database_code' => $database_code
                ]
            ]
        );
    }

    /**
     * @return array
     */
    public function getClients(): array
    {
        $params = [
            "query" => [
                "fields" => implode(",", [
                    "name",
                    "firstname",
                    "lastname",
                    "phone",
                    "email"
                ]),
                "archive" => "false"
            ]
        ];
        return $this->request('clients', $params);
    }

    /**
     * @return string|null
     */
    public function getOwnerPositionId():?string
    {
        $params = [
            "query" => [
                "fields" => implode(",", [
                    "name",
                    "role",
                    "permissions",
                    "parent"
                ])
            ]
        ];
        $res = $this->request('positions', $params);
        foreach ($res as $item) {
            if($item["role"] == 'owner') {
                return  $item["id"];
            }
        }
        return null;
    }


    /**
     * @return string|null
     */
    public function getProfessionalPositionId():?string
    {
        $params = [
            "query" => [
                "fields" => implode(",", [
                    "name",
                    "role",
                    "permissions",
                    "parent"
                ])
            ]
        ];
        $res = $this->request('positions', $params);
        foreach ($res as $item) {
            if($item["role"] == 'professional') {
                return  $item["id"];
            }
        }
        return null;
    }


    /**
     * @return array
     */
    public function getStaff(): array
    {
        $params = [
            "query" => [
                "fields" => implode(",", [
                    "name",
                    "firstname",
                    "phone",
                    "email",
                    "roles",
                    "archive",
                    "public",
                    "permissions"
                ])
            ]
        ];
        return $this->request('employees', $params);
    }

    /**
     * @return array
     */
    public function getSchedules(): array
    {
        $now = Carbon::now();
        $from = $now->startOfWeek()->format('Y-m-d\TH:i:00.000\Z');
        $to = $now->endOfWeek()->format('Y-m-d\TH:i:00.000\Z');

        $params = [
            "query" => [
                "from"  => $from,
                "to"    => $to
            ]
        ];

        return $this->request("schedule", $params);
    }

    /**
     * @return array
     */
    public function getServicesTypes(): array
    {
        $params = [
            "query" => [
                "fields" => implode(",", [
                    "name",
                    "archive"
                ])
            ],
            "body" => json_encode([
                "archive" => "false"
            ])
        ];
        return $this->request('services/categories', $params);
    }

    /**
     * @return array
     */
    public function getServices(): array
    {
        $params = [
            "query" => [
                "fields" => implode(",", [
                    "name",
                    "category",
                    "price",
                    "archive"
                ]),
                "archive" => "false"
            ]
        ];
        return $this->request('services', $params);
    }

    /**
     * @return array
     */
    public function getRecords(): array
    {
        $params = [
            "query" => [
                "fields" => implode(",", [
                    "start",
                    "professional",
                    "client",
                    "service",
                    "price",
                    "state"
                ])
            ]
        ];
        return $this->request('appointments/services', $params);
    }

    /**
     * @param array $clients
     * @return array
     */
    public function addClients(array $clients): array
    {
        $success = [];
        foreach ($clients as $client) {
            $params = [
                'query' => [
                    'fields' => implode(",", [
                        "firstname",
                        "lastname",
                        "phone",
                        "email"
                    ])
                ],
                'body' => json_encode([
                    'firstname' => $client['first_name'],
                    'lastname' => "",
                    'phone' => $client['phone'],
                    'email' => $client['email'],
                ])
            ];
            $res = $this->request("clients", $params, 'POST');
            if(!isset($res["errors"])) {
                $id = $res["id"];
                TelegramUser::query()
                    ->where('id', $client['id'])
                    ->update(['beauty_id' => $id]);
                $success[] = $client;
            }
        }

        return $success;
    }

    /**
     * @param array $staffs
     * @return array
     */
    public function addStaff(array $staffs): array
    {
        $owner_position_id = $this->getOwnerPositionId();
        $professional_position_id = $this->getProfessionalPositionId();

        $success = [];
        foreach ($staffs as $staff) {
            $params = [
                'query' => [
                    'fields' => implode(",", [
                        "firstname",
                        "middlename",
                        "lastname",
                        "phone",
                        "email",
                        "roles",
                    ]),
                ],
                'body' => json_encode([
                    'firstname' => $staff['name'],
                    'lastname' => "",
                    'middlename' => "",
                    'phone' => $staff['phone'],
                    'email' => $staff['email'],
                    'positions' => [
                        $owner_position_id,
                        $professional_position_id,
                    ]
                ])
            ];
            $res = $this->request("employees", $params, 'POST');

            if(!isset($res["errors"])) {
                $id = $res["id"];
                User::query()
                    ->where('id', $staff['id'])
                    ->update(['beauty_id' => $id]);
                $success[] = $staff;
            }
        }

        return $success;
    }

    /**
     * @param array $staffs
     * @return array
     */
    public function deleteStaffs(array $staffs): array
    {
       $out = [];
       foreach ($staffs as $staff_id) {
           $res = $this->request("employees/" . $staff_id, [], 'DELETE');
           if(!isset($res["errors"])) {
               User::where("beauty_id", $staff_id)->delete();
               $out[] = $res;
           } else {
               Log::debug("Ошибка удаления сотрудника: " . $staff_id, $res);
           }
       }
       return $out;
    }

    /**
     * @param array $types
     * @return array
     */
    public function addTypes(array $types): array
    {
        $success = [];
        foreach ($types as $type) {
            $params = [
                "query" => [
                    "fields" => implode(",", [
                        "name",
                        "parent",
                        "picture"
                    ])
                ],
                "body" => json_encode([
                    "name"    => $type["type"],
                    "parent"  => null,
                    "picture" => null
                ])
            ];
            $res = $this->request("services/categories", $params, "POST");

            if(!isset($res["errors"])) {
                $id = $res["id"];
                TypeService::query()
                    ->where('id', $type['id'])
                    ->update(['beauty_id' => $id]);
                $success[] = $type;
            }
        }

        return $success;
    }

    /**
     * @param array $services
     * @return array
     */
    public function addServices(array $services): array
    {
        $success = [];
        foreach ($services as $service) {

            $beauty_type = TypeService::query()->
                where('id', $service['type_service_id'])->
                get('beauty_id')->
                first()->
                toArray();

            $params = [
                "query" => [
                    "fields" => implode(",", [
                        "name",
                        "category",
                        "price"
                    ])
                ],
                "body" => json_encode([
                    "name" => $service["name"],
                    "category" => $beauty_type["beauty_id"],
                    //TODO: How to send price ?
                    //"price" => $service["price"]
                ])
            ];

            $res = $this->request("services", $params, "POST");

            if(!isset($res["errors"])) {
                $id = $res["id"];
                Service::query()
                    ->where('id', $service['id'])
                    ->update(['beauty_id' => $id]);
                $success[] = $service;
            } else {
                Log::debug("Add service: ", $res);
            }
        }

        return $success;
    }

    /**
     * @param array $records
     * @return array
     */
    public function addRecords(array $records): array
    {
        $success = [];
        foreach ($records as $record) {
            if(is_null($record["user_id"])) continue;

            $beauty_staff = null;
            $staff_id = null;
            if(!is_null($record['user_id'])) {
                $beauty_staff = User::query()->
                    where('id', $record['user_id'])->
                    get('beauty_id')->
                    first()->
                    toArray();
                $staff_id = $beauty_staff['beauty_id'];
            }

            $service = Service::query()->
                    where('id', $record['service_id'])->
                    first()->
                    toArray();

            $telegram_user = TelegramUser::query()
                ->where('id', $record['telegram_user_id'])
                ->get()
                ->first()
                ->toArray();

            $datetime = Carbon::parse($record['date'] . $record['time'])->format('Y-m-d\TH:i:00.000\Z');

            $params = [
                "query" => [
                    "force" => "serviceTeeth"
                ],
                "body" => json_encode([
                    "date"    => Carbon::parse($record['date'])->format('Y-m-d'),
                    "services" => [(object)[
                        "start"         => $datetime,
                        "professional"  => $staff_id,
                        "service"       => $service["beauty_id"],
                        "duration"      => $service["price"]
                    ]],
                    "client"  => $telegram_user["beauty_id"],
                    "clientsModule" => true,
                    "state" => "planned",
                ])
            ];

            $res = $this->request("appointments", $params, "POST");

            if(!isset($res["errors"])) {
                $id = $res["id"];
                Record::query()
                    ->where('id', $record['id'])
                    ->update(['beauty_id' => $id]);
                $success[] = $record;
            } else {
                Log::debug("Ошибка BeautyProApi (addRecord): ", $res);
            }
        }

        return $success;
    }

    protected function exception (GuzzleException $e): array
    {
        return [
            'errors' =>
                json_decode(
                    $e->getResponse()->getBody()->getContents(),
                    JSON_OBJECT_AS_ARRAY
                )
        ];
    }

    /**
     * @param string $url
     * @param array $parameters
     * @param string $method
     * @return array|null
     */
    protected function request(string $url, array $parameters = [], string $method = 'GET'): ?array
    {
        try {
            $response = $this->guzzle->request(
                $method,
                $url,
                $parameters
            );

            $response = $response->getBody()->getContents();
            return json_decode($response, JSON_OBJECT_AS_ARRAY);
        } catch (GuzzleException $e) {
            return $this->exception($e);
        }
    }
}
