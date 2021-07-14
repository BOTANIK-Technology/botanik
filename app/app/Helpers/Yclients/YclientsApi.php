<?php

namespace App\Helpers\Yclients;

use App\Models\Catalog;
use App\Models\Record;
use App\Models\Service;
use App\Models\TelegramUser;
use App\Models\TypeService;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * @see http://docs.yclients.apiary.io
 */
class YclientsApi
{
    /*
     * URL для RestAPI
     */
    const URL = 'https://api.yclients.com/api/v1';

    /*
     * Методы используемые в API
     */
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    /**
     * Данные аутентификации
     *
     * @var array @auth
     * @access private
     */
    private array $auth;

    /**
     * Company Id
     *
     * @var int
     */
    private int $companyId;

    /**
     * Токен доступа для авторизации партнёра
     *
     * @var string
     * @access private
     */
    private string $tokenPartner;

    /**
     * @param string $login
     * @param string $password
     * @param string $tokenPartner
     * @access public
     * @throws YclientsException
     */
    public function __construct(int $company_id, string $login, string $password, string $tokenPartner)
    {
        $this->setCompanyID($company_id);
        $this->setTokenPartner($tokenPartner);
        $this->auth = $this->getAuth($login, $password);
    }

    /**
     * Установка ID компании
     *
     * @param int $company_id
     * @return YclientsApi
     */
    public function setCompanyID(int $company_id): YclientsApi
    {
        $this->companyId = $company_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyID(): string
    {
        return $this->companyId;
    }

    /**
     * Утановка токена можно сделать отдельно т.к. есть запросы не
     * требующие авторизации партнёра
     *
     * @param string $tokenPartner
     * @return self
     * @access public
     */
    public function setTokenPartner(string $tokenPartner): YclientsApi
    {
        $this->tokenPartner = $tokenPartner;

        return $this;
    }

    /**
     * @return string
     */
    public function getTokenPartner(): string
    {
        return $this->tokenPartner;
    }

    /**
     * Получаем токен пользователя по логину-паролю
     *
     * @param string $login
     * @param string $password
     * @return array
     * @throws YclientsException
     * @access public
     * @see http://docs.yclients.apiary.io/#reference/0/0/0
     */
    public function getAuth(string $login, string $password): array
    {
        $this->auth = $this->request('auth', [
            'login' => $login,
            'password' => $password,
        ], self::METHOD_POST);

        return $this->auth;
    }

    public function getUserToken(): string
    {
        if(isset($this->auth['success']) && $this->auth['success'] == true) {
            return $this->auth['data']['user_token'];
        } else {
            return "";
        }
    }

    /**
     * Получить список клиентов
     *
     * @return array
     * @throws YclientsException
     */
    public function getClients(): array
    {
        return $this->request("clients/" . $this->getCompanyID(),
            [],
            self::METHOD_GET,
            $this->getUserToken()
        );
    }

    /**
     * Получить список специалистов
     *
     * @return array
     * @throws YclientsException
     */
    public function getStaff(): array
    {
        return $this->request("company/" . $this->getCompanyID() . '/staff',
            [],
            self::METHOD_GET,
            $this->getUserToken()
        );
    }

    /**
     * Получить расписание специалиста
     *
     * @param int $staff_id
     * @return array
     * @throws YclientsException
     */
    public function getStaffSchedule(int $staff_id): array
    {
        $now = Carbon::now();
        $from = $now->startOfWeek()->format('Y-m-d');
        $to = $now->endOfWeek()->format('Y-m-d');

        return $this->request("schedule/" . $this->getCompanyID() . '/' .$staff_id . '/' . $from . "/" . $to,
            [],
            self::METHOD_GET,
            $this->getUserToken()
        );
    }

    /**
     * Получить список типов услуг
     *
     * @return array
     * @throws YclientsException
     */
    public function getServicesTypes(): array
    {
        return $this->request("service_categories/" . $this->getCompanyID(),
            [],
            self::METHOD_GET,
            $this->getUserToken()
        );
    }

    /**
     * Получить список услуг
     *
     * @return array
     * @throws YclientsException
     */
    public function getServices(): array
    {
        return $this->request("services/" . $this->getCompanyID(),
            [],
            self::METHOD_GET,
            $this->getUserToken()
        );
    }

    /**
     * Получить список записей
     *
     * @return array
     * @throws YclientsException
     */
    public function getRecords(): array
    {
        return $this->request("records/" . $this->getCompanyID(),
            [
                "start_date" => Carbon::now()->format("Y-m-d")
            ],
            self::METHOD_GET,
            $this->getUserToken()
        );
    }

    /**
     * Получить список категорий товаров
     *
     * @return array
     * @throws YclientsException
     */
    public function getCategories(): array
    {
        return $this->request("goods_categories/" . $this->getCompanyID(),
            [],
            self::METHOD_GET,
            $this->getUserToken()
        );
    }

    /**
     * Получить список товаров
     *
     * @return array
     * @throws YclientsException
     */
    public function getProducts(): array
    {
        return $this->request("goods/" . $this->getCompanyID(),
            [],
            self::METHOD_GET,
            $this->getUserToken()
        );
    }

    /**
     * @param array $clients
     * @return array
     * @throws YclientsException
     */
    public function addClients(array $clients): array
    {
        $success = [];
        foreach ($clients as $client) {
            $res = $this->request("clients/" . $this->getCompanyID(),
                [
                    'name' => $client['first_name'],
                    'phone' => $client['phone'],
                    'email' => empty($client['email']) ?? null,
                    'sex_id' => $client['sex'],
                ],
                self::METHOD_POST,
                $this->getUserToken()
            );

            if($res["success"] == true) {
                $id = $res["data"]["id"];
                TelegramUser::query()
                    ->where('id', $client['id'])
                    ->update(['yclients_id' => $id]);
                $success[] = $client;
            }
        }

        return $success;
    }

    /**
     * @param array $staffs
     * @return array
     * @throws YclientsException
     */
    public function addStaff(array $staffs): array
    {
        $success = [];
        foreach ($staffs as $staff) {
            $res = $this->request("staff/" . $this->getCompanyID(),
                [
                    'name' => $staff['name'],
                    'specialization' => "Не указана",
                ],
                self::METHOD_POST,
                $this->getUserToken()
            );

            if($res["success"] == true) {
                $id = $res["data"]["id"];
                User::query()
                    ->where('id', $staff['id'])
                    ->update(['yclients_id' => $id]);
                $success[] = $staff;
            }
        }

        return $success;
    }

    /**
     * @param array $types
     * @return array
     * @throws YclientsException
     */
    public function addTypes(array $types): array
    {
        $success = [];
        foreach ($types as $type) {
            $res = $this->request("service_categories/" . $this->getCompanyID(),
                [
                    'title' => $type['type'],
                ],
                self::METHOD_POST,
                $this->getUserToken()
            );

            if($res["success"] == true) {
                $id = $res["data"]["id"];
                TypeService::query()
                    ->where('id', $type['id'])
                    ->update(['yclients_id' => $id]);
                $success[] = $type;
            }
        }

        return $success;
    }

    /**
     * @param array $services
     * @return array
     * @throws YclientsException
     */
    public function addServices(array $services): array
    {
        $success = [];
        foreach ($services as $service) {

            $yclients_type = TypeService::query()->
                where('id', $service['type_service_id'])->
                get('yclients_id')->
                first()->
                toArray();

            $res = $this->request("services/" . $this->getCompanyID(),
                [
                    'title' => $service['name'],
                    'category_id' => $yclients_type['yclients_id'],
                    'price_min' => $service['price'],
                ],
                self::METHOD_POST,
                $this->getUserToken()
            );

            if($res["success"] == true) {
                $id = $res["data"]["id"];
                Service::query()
                    ->where('id', $service['id'])
                    ->update(['yclients_id' => $id]);
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
     * @throws YclientsException
     */
    public function addRecords(array $records): array
    {
        $success = [];
        foreach ($records as $record) {

            if(is_null($record['user_id'])) continue;

            $yclients_staff = User::query()->
                where('id', $record['user_id'])->
                get('yclients_id')->
                first()->
                toArray();

            $yclients_services = Service::query()->
                where('id', $record['service_id'])->
                get()->
                toArray();


            $staff_id = $yclients_staff['yclients_id'];

            $services = [];
            foreach ($yclients_services as $service) {
                $services[] = [
                    "id" => $service["yclients_id"],
                    "first_cost" => $service["price"],
                    "cost" => $service["price"],
                ];
            }

            $telegram_user = TelegramUser::query()->
                where('id', $record['telegram_user_id'])
                ->get()
                ->first()
                ->toArray();

            $client = [
                'phone' => $telegram_user['phone'],
                'name'  => $telegram_user['first_name'],
                'email' => $telegram_user['email']
            ];

            $datetime = Carbon::parse($record['date'] . $record['time'])->format('Y-m-d H:i');

            $schedule = $this->request("schedule/" . $this->getCompanyID() ."/" . $staff_id . "/",
                [
                    'date'      => Carbon::parse($record['date'])->format('Y-m-d'),
                    'is_working' => true,
                ],
                self::METHOD_PUT,
                $this->getUserToken()
            );

            if(["success"] === false) {
                Log::debug("Ошибка YClients (addSchedule): ", $schedule);
            }

            $res = $this->request("records/" . $this->getCompanyID(),
                [
                    'staff_id'      => $staff_id,
                    'services'      => $services,
                    'client'        => $client,
                    'datetime'      => $datetime,
                    'seance_length' => 3600,
                    'save_if_busy'  => true,
                    'send_sms'      => false,
                ],
                self::METHOD_POST,
                $this->getUserToken()
            );


            if($res["success"] === true) {
                $id = $res["data"]["id"];
                Record::query()
                    ->where('id', $record['id'])
                    ->update(['yclients_id' => $id]);
                $success[] = $record;
            } else {
                Log::debug("Ошибка YClients (addRecord): ", $res["meta"]);
            }
        }

        return $success;
    }

    /**
     * @param array $products
     * @return array
     * @throws YclientsException
     */
    public function addProducts(array $products): array
    {
        // For now this method not allowed
        return $products;

        foreach ($products as $product) {
            $res = $this->request("goods/" . $this->getCompanyID(),
                [
                    "title" => $product["title"],
                    "value" => $product["title"],
                    "label" => $product["text"],
                    "article" => $product["article"],
                    "category" => "",
                    "category_id" => 0,
                    "cost" => $product["price"],
                ],
                self::METHOD_POST,
                $this->getUserToken()
            );

            if($res["success"] == true) {
                $id = $res["data"]["id"];
                Catalog::query()
                    ->where('id', $product['id'])
                    ->update(['yclients_id' => $id]);
            } else {
                Log::debug("Error upload product: ", $res);
            }
        }

        return $products;
    }



    /**
     * Подготовка запроса
     *
     * @param string $url
     * @param array $parameters
     * @param string $method
     * @param bool|string $auth - если true, то авторизация партнёрская
     *                            если string, то авторизация пользовательская
     * @return array
     * @throws YclientsException
     * @access protected
     */
    protected function request(string $url, $parameters = [], $method = 'GET', $auth = true): array
    {
        $headers = [
            'Accept: application/vnd.yclients.v2+json',
            'Content-Type: application/json'
        ];

        if ($auth) {
            if (!$this->tokenPartner) {
                throw new YclientsException('Не указан токен партнёра');
            }

            $headers[] = 'Authorization: Bearer ' . $this->tokenPartner . (is_string($auth) ? ', User ' . $auth : '');
        }

        return $this->requestCurl($url, $parameters, $method, $headers);
    }

    /**
     * Выполнение непосредственно запроса с помощью curl
     *
     * @param string $url
     * @param array $parameters
     * @param string $method
     * @param array $headers
     * @param integer $timeout
     * @return array
     * @throws YclientsException
     * @access protected
     */
    protected function requestCurl(string $url, $parameters = [], $method = 'GET', $headers = [], $timeout = 30): array
    {
        $ch = curl_init();

        if (count($parameters)) {
            if ($method === self::METHOD_GET) {
                $url .= '?' . http_build_query($parameters);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
            }
        }

        if ($method === self::METHOD_POST) {
            curl_setopt($ch, CURLOPT_POST, true);
        } elseif ($method === self::METHOD_PUT) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, self::METHOD_PUT);
        } elseif ($method === self::METHOD_DELETE) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, self::METHOD_DELETE);
        }

        curl_setopt($ch, CURLOPT_URL, self::URL . '/' . $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HEADER, false);

        if (count($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);

        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($errno) {
            throw new YclientsException('Запрос произвести не удалось: ' . $error, $errno);
        }

        return json_decode($response, true);
    }

}
