<?php

namespace App\Helpers\Beauty;

use App\Models\Address;
use App\Models\Api;
use App\Models\Record;
use App\Models\Service;
use App\Models\TelegramUser;
use App\Models\TypeService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class BeautyPro
{

    /** @var BeautyProApi $api */
    public BeautyProApi $api;


    /**
     * Beauty constructor.
     */
    function __construct()
    {
        $config = $this->getConfig();
        $this->api = new BeautyProApi($config);
    }

    /**
     * @return string[]
     */
    public function synchronize(): array
    {
        try {
            // Синхронизация клиентов
            $clients = $this->clientsSync();

            // Синхронизация специалистов
            $staff = $this->staffSync();

            // Синхронизация графиков специалистов
            $schedules= $this->schedulesSync();

            // Синхронизация типов (категорий) услуг
            $services_types = $this->servicesTypesSync();

            // Синхронизация услуг
            $services = $this->servicesSync();

            // Синхронизация записей
            $records = $this->recordsSync();

            // Синхронизация каталогов
            //$categories = $this->categoriesSync();

            // Синхронизация товаров
            //$products = $this->productsSync();

        } catch (BeautyProException $e) {
            return [
                "result"    => "fail",
                "code"      => $e->getCode(),
                "message"   => $e->getMessage(),
            ];
        }
        return [
            "result"            => "success",
            "clients"           => $clients,
            "staff"             => $staff,
            "services_types"    => $services_types,
            "services"          => $services,
            "records"           => ['create' => 0, 'update' => 0, 'upload' => 0], //$records,
            "categories"        => ['create' => 0, 'update' => 0, 'upload' => 0], //$categories,
            "products"          => ['create' => 0, 'update' => 0, 'upload' => 0], //$products,
        ];
    }

    /**
     * @return array
     * @throws BeautyProException
     */
    private function clientsSync(): array
    {
        $ext_clients = $this->api->getClients();
        if(!isset($ext_clients["errors"])) {
            $actions = $this->compareClients($ext_clients);
            return $this->doClients($actions);
        } else {
            throw new BeautyProException("Ошибка получения клиентов из Beauty Pro API");
        }

    }

    /**
     * @return array
     * @throws BeautyProException
     */
    private function staffSync(): array
    {
        $ext_staff = $this->api->getStaff();
        if(!isset($ext_staff["errors"])) {
            $actions = $this->compareStaff($ext_staff);
            return $this->doStaff($actions);
        } else {
            throw new BeautyProException("Ошибка получения сотрудников из Beauty Pro API");
        }
    }

    /**
     * @return array
     * @throws BeautyProException
     */
    private function schedulesSync(): array
    {
        $ext_schedules = $this->api->getSchedules();
        if(!isset($ext_schedules["errors"])) {
            $ext_schedules = $this->convertScheidules($ext_schedules);

            print_r($ext_schedules);die;

            $actions = $this->compareSchedules($ext_schedules);
            return $this->doScheduls($actions);
        } else {
            throw new BeautyProException("Ошибка получения графиков работы сотрудников из Beauty Pro API");
        }
    }

    /**
     * @return array
     * @throws BeautyProException
     */
    private function servicesTypesSync(): array
    {
        $types = $this->api->getServicesTypes();
        if(!isset($types["errors"])) {
            $actions = $this->compareTypesServices($types);
            return $this->doTypes($actions);
        } else {
            throw new BeautyProException("Ошибка получения типов услуг из Beauty Pro API");
        }
    }

    /**
     * @return array
     * @throws BeautyProException
     */
    private function servicesSync(): array
    {
        $services = $this->api->getServices();
        if(!isset($services["errors"])) {
            $actions = $this->compareServices($services);
            return $this->doServices($actions);
        } else {
            throw new BeautyProException("Ошибка получения услуг из Beauty Pro API");
        }
    }

    /**
     * @return array
     * @throws BeautyProException
     */
    private function recordsSync(): array
    {
        $records = $this->api->getRecords();
        if(!isset($services["errors"])) {
            $actions = $this->compareRecords($records);
            return $this->doRecords($actions);
        } else {
            throw new BeautyProException("Ошибка получения записей из Beauty Pro API");
        }
    }

    /**
     * @param array $ext_clients
     * @return array
     */
    private function compareClients(array $ext_clients): array
    {
        $create = [];
        $update = [];
        $upload = TelegramUser::query()
            ->whereNull('beauty_id')
            ->whereNull('yclients_id')
            ->get()
            ->toArray();

        foreach ($ext_clients as $ext_client) {
            $count = TelegramUser::query()
                ->where('beauty_id', $ext_client['id'])
                ->count();
            if($count > 0) {
                $update[] = $ext_client;
            } else {
                $create[] = $ext_client;
            }
        }

        return [
            "create" => $create,
            "update" => $update,
            "upload" => $upload
        ];
    }

    /**
     * @param array $ext_staffs
     * @return array
     */
    private function compareStaff(array $ext_staffs): array
    {
        $create = [];
        $update = [];
        $upload = User::query()
            ->whereNull('beauty_id')
            ->whereNull('yclients_id')
            ->get()
            ->toArray();

        foreach ($ext_staffs as $ext_staff) {
            $count = User::query()
                ->where('beauty_id', $ext_staff['id'])
                ->count();
            if($count > 0) {
                $update[] = $ext_staff;
            } else {
                $create[] = $ext_staff;
            }
        }

        return [
            "create" => $create,
            "update" => $update,
            "upload" => $upload
        ];

    }

    /**
     * @param array $ext_types
     * @return array
     */
    private function compareTypesServices(array $ext_types): array
    {
        $create = [];
        $update = [];
        $upload = TypeService::query()
            ->whereNull('yclients_id')
            ->whereNull('beauty_id')
            ->get()
            ->toArray();

        foreach ($ext_types as $type) {
            $count = TypeService::query()
                ->where('beauty_id', $type['id'])
                ->count();
            if($count > 0) {
                $update[] = $type;
            } else {
                $create[] = $type;
            }
        }

        return [
            "create" => $create,
            "update" => $update,
            "upload" => $upload
        ];
    }

    /**
     * @param array $ext_services
     * @return array
     */
    private function compareServices(array $ext_services): array
    {
        $create = [];
        $update = [];
        $upload = Service::query()
            ->whereNull('yclients_id')
            ->whereNull('beauty_id')
            ->get()
            ->toArray();

        foreach ($ext_services as $ext_service) {
            $count = Service::query()
                ->where('beauty_id', $ext_service['id'])
                ->count();
            if($count > 0) {
                $update[] = $ext_service;
            } else {
                $create[] = $ext_service;
            }
        }

        return [
            "create" => $create,
            "update" => $update,
            "upload" => $upload
        ];
    }

    /**
     * @param array $ext_records
     * @return array
     */
    private function compareRecords(array $ext_records): array
    {
        $create = [];
        $update = [];
        $upload = Record::query()
            ->whereNull('yclients_id')
            ->whereNull('beauty_id')
            ->get()
            ->toArray();

        foreach ($ext_records as $ext_record) {
            $count = Record::query()
                ->where('beauty_id', $ext_record['id'])
                ->count();
            if($count > 0) {
                $update[] = $ext_record;
            } else {
                $create[] = $ext_record;
            }
        }

        return [
            "create" => $create,
            "update" => $update,
            "upload" => $upload
        ];

    }


    /**
     * @param array $action
     * @return array
     */
    private function doClients(array $action): array
    {
        // Insert
        $create = [];
        foreach ($action['create'] as $client) {
            $client_entity = new TelegramUser([
                'beauty_id'   => $client["id"],
                'first_name'    => $client['name'],
                'last_name'     => '',
                'phone'    => isset($client['phone'][0]) ? $client['phone'][0] : "",
                'email'    => isset($client['email'][0]) ? $client['email'][0] : "",
                'middle_name'   => '',
                'status'        => 1
            ]);
            $client_entity->save();
            $create[] = $client_entity;
        }

        // Update
        $update = [];
        foreach ($action['update'] as $client) {
            $fields = [
                'first_name'    => $client['name'],
                'phone'    => isset($client['phone'][0]) ? $client['phone'][0] : "",
                'email'    => isset($client['email'][0]) ? $client['email'][0] : "",
            ];

            TelegramUser::query()
                ->where('beauty_id', $client['id'])
                ->update($fields);
            $update[] = $client;
        }

        // Upload
        $upload = [];
//        if(count($action["upload"]) > 0) {
//            $upload = $this->api->addClients($action["upload"]);
//        }

        return [
            "create" => count($create),
            "update" => count($update),
            "upload" => count($upload)
        ];
    }

    /**
     * @param array $action
     * @return array
    */
    private function doStaff(array $action): array
    {
        // сли нужно почистить сотрудников
        /*
        $delete = [
            "88d945e3-aa70-1529-625e-1e0146f641e4",
            "88d9460a-03ae-f3c6-3775-f92b432194db",
            "88d94605-0f6c-c829-3775-f92b62a9a44d",
            "88d945fc-14c4-d8c9-3e86-30737c6fbca0",
            "88d945fb-eae4-f1a8-0a89-712c29423f66",
            "88d945f7-a0b4-5ecb-21ea-e066602049fd",
            "88d9460a-03b7-5092-3e86-3073423d33b0",
        ];


        $res = $this->api->deleteStaffs($delete);

        */

        // Insert
        $create = [];
        foreach ($action['create'] as $client) {
            $pass = $this->generatePass();
            $staff_entity = new User([
                'beauty_id'   => $client["id"],
                'name'          => $client['name'],
                'phone'    => isset($client['phone'][0]) ? $client['phone'][0] : "",
                'email'    => isset($client['email'][0]) ? $client['email'][0] : "",
                'status'        => 1,
                'password'      => Hash::make($pass)
            ]);
            $staff_entity->save();

            DB::table('users_roles')->insert([
                "user_id" => $staff_entity->id,
                "role_id" => 3
            ]);

            $create[] = $client;
        }

        // Update
        $update = [];
        foreach ($action['update'] as $client) {
            User::query()
                ->where('beauty_id', $client['id'])
                ->update([
                    'name'    => $client['name'],
                    'phone'    => isset($client['phone'][0]) ? $client['phone'][0] : "",
                    'email'    => isset($client['email'][0]) ? $client['email'][0] : "",
                ]);
            $update[] = $client;
        }

        // Upload
        $upload = [];
//        if(count($action["upload"]) > 0) {
//            $upload = $this->api->addStaff($action["upload"]);
//        }

        return [
            "create" => count($create),
            "update" => count($update),
            "upload" => count($upload)
        ];

    }

    /**
     * @param array $action
     * @return array
     */
    private function doTypes(array $action): array
    {
        // Insert
        $create = [];
        foreach ($action['create'] as $type) {
            $type_entity = new TypeService([
                'beauty_id'   => $type["id"],
                'type'          => $type['name']
            ]);
            $type_entity->save();
            $create[] = $type;
        }

        // Update
        $update = [];
        foreach ($action['update'] as $type) {
            TypeService::query()
                ->where('beauty_id', $type['id'])
                ->update([
                    'type'    => $type['name'],
                ]);
            $update[] = $type;
        }

        // Upload
        $upload = [];
//        if(count($action["upload"]) > 0) {
//            $upload = $this->api->addTypes($action["upload"]);
//        }

        return [
            "create" => count($create),
            "update" => count($update),
            "upload" => count($upload)
        ];
    }

    /**
     * @param array $action
     * @return array
     */
    private function doServices(array $action): array
    {
        // Insert
        $create = [];
        foreach ($action['create'] as $service) {

            $type = TypeService::getByBeautyProId($service['category']);
            $service_entity = new Service([
                'beauty_id'       => $service["id"],
                'type_service_id'   => $type->id,
                'name'              => $service['name'] . " (импорт beauty pro)",
                'price'             => (isset($service['price']) && count($service["price"]) > 0) ?
                    $service["price"][array_key_first($service["price"])] : 0,
                'cash_pay'          => 1,
                'bonus_pay'         => 1,
                'online_pay'        => 1
            ]);
            $service_entity->save();
            $create[] = $service;
        }

        // Update
        $update = [];
        foreach ($action['update'] as $service) {
            Service::query()
                ->where('yclients_id', $service['id'])
                ->update([
                    'name'          => $service['name'],
                    'price'             => (isset($service['price']) && count($service["price"]) > 0) ?
                        $service["price"][array_key_first($service["price"])] : 0,
                ]);
            $update[] = $service;
        }

        // Upload
        $upload = [];
//        if(count($action["upload"]) > 0) {
//            $upload = $this->api->addServices($action["upload"]);
//        }


        return [
            "create" => count($create),
            "update" => count($update),
            "upload" => count($upload)
        ];
    }

    /**
     * @param array $action
     * @return array
     */
    private function doRecords(array $action): array
    {
        // Insert
        $create = [];
        foreach ($action['create'] as $record) {

            if(!isset($record["service"])) {
                continue;
            }

            $telegram_user = TelegramUser::getByBeautyId($record["client"]);
            $service = Service::getByBeautyId($record["service"]);
            $staff = !is_null($record["professional"]) ? User::getByBeautyId($record["professional"]) : null;

            if(!is_null($telegram_user) && !is_null($service)) {
                $address_text = "Адрес услуги " . $service->name . " (импортпорт из BeautyPro)";
                $address = Address::query()->where('address', $address_text)->first();
                if(is_null($address)) {
                    $address = new Address([
                        "address" => $address_text
                    ]);
                    $address->save();
                }

                $record_entity = new Record([
                    'beauty_id'        => $record["id"],
                    'telegram_user_id' => $telegram_user->id,
                    'service_id'       => $service->id,
                    'address_id'       => $address->id,
                    'user_id'          => !is_null($staff) ? $staff->id: null,
                    'status'           => $record["state"] == "planned" ? 1 : 0,
                    'date'             => Carbon::parse($record['start'])->format('Y-m-d'),
                    'time'             => Carbon::parse($record['start'])->format('H:i'),
                ]);
                $record_entity->save();
                $create[] = $record;
            }
        }

        // Update
        $update = [];
        foreach ($action['update'] as $record) {
            Record::query()
                ->where('beauty_id', $record['id'])
                ->update([
                    'status' => $record["state"] == "planned" ? 1 : 0,
                    'date'   => Carbon::parse($record['start'])->format('Y-m-d'),
                    'time'   => Carbon::parse($record['start'])->format('H:i'),
                ]);
            $update[] = $record;
        }

        // Upload
        $upload = [];
//        if(count($action["upload"]) > 0) {
//            $upload = $this->api->addRecords($action["upload"]);
//        }


        return [
            "create" => count($create),
            "update" => count($update),
            "upload" => count($upload)
        ];
    }



    /**
     * @return array
     */
    public function getConfig (): array
    {
        $data = $this->getData();
        return json_decode($data->config, JSON_OBJECT_AS_ARRAY);
    }

    /**
     * @return Api|Builder|Model|object|null
     */
    public function getData ()
    {
        return Api::where('slug', 'beauty')->firstOrFail();
    }

    /**
     * @return string
     */
    private function generatePass(): string
    {
        $chars = 'abcdefghiklmnopqrstvwxyz';
        $length = 6;
        $numChars = strlen($chars);
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, rand(1, $numChars) - 1, 1);
        }
        $array_mix = preg_split('//', $str, -1, PREG_SPLIT_NO_EMPTY);
        srand((float)microtime() * 1000000);
        shuffle($array_mix);
        return implode("", $array_mix);
    }

    /**
     * Convert to inner format
     * @param $api_schedules
     * @return array
     */
    private function convertSchedules($api_schedules): array
    {
        $schedules = [];
        foreach ($api_schedules as $schedule) {
            $schedules[] = [
                "id" => $schedule["professional"]
            ];
        }
    }
}
