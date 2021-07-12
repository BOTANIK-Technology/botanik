<?php

namespace App\Helpers\Yclients;

use App\Models\Address;
use App\Models\Api;
use App\Models\Catalog;
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

class Yclients
{

    /** @var YclientsApi $api */
    public YclientsApi $api;

    /**
     * Yclients constructor.
     * @throws YclientsException
     */
    function __construct()
    {
        $config = self::getConfig();
        $this->api = new YclientsApi(
            $config['company_id'],
            $config['login'],
            $config['password'],
            $config['partner_token']);
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

            // Синхронизация типов (категорий) услуг
            $services_types = $this->servicesTypesSync();

            // Синхронизация услуг
            $services = $this->servicesSync();

            // Синхронизация записей
            $records = $this->recordsSync();

            // Синхронизация каталогов
            $categories = $this->categoriesSync();

            // Синхронизация товаров
            $products = $this->productsSync();

        } catch (YclientsException $e) {
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
            "records"           => $records,
            "categories"        => $categories,
            "products"          => $products,
        ];
    }

     /**
     * @return array
     */
    public static function getConfig (): array
    {
        $data = self::getData();
        return json_decode($data->config, JSON_OBJECT_AS_ARRAY);
    }

    /**
     * @return Api|Builder|Model|object|null
     */
    public static function getData ()
    {
        return Api::where('slug', 'yclients')->firstOrFail();
    }

    /**
     * @return array
     * @throws YclientsException
     */
    private function clientsSync(): array
    {
        $clients = $this->api->getClients();
        if($clients["success"] === true) {
            $ext_clients = (array)$clients["data"];
            $actions = $this->compareClients($ext_clients);
            return $this->doClients($actions);
        } else {
            throw new YclientsException("Ошибка доступа к API");
        }
    }

    /**
     * @return array
     * @throws YclientsException
     */
    private function staffSync(): array
    {
        $staff = $this->api->getStaff();
        if($staff["success"] === true) {
            $ext_staff = (array)$staff["data"];
            $actions = $this->compareStaff($ext_staff);
            return $this->doStaff($actions);
        } else {
            throw new YclientsException("Ошибка доступа к API");
        }
    }

    /**
     * @return array
     * @throws YclientsException
     */
    private function servicesTypesSync(): array
    {
        $services = $this->api->getServicesTypes();
        if($services["success"] === true) {
            $ext_types = (array)$services["data"];
            $actions = $this->compareTypesServices($ext_types);
            return $this->doTypes($actions);
        } else {
            throw new YclientsException("Ошибка доступа к API");
        }
    }

    /**
     * @return array
     * @throws YclientsException
     */
    private function servicesSync(): array
    {
        $services = $this->api->getServices();
        if($services["success"] === true) {
            $ext_services = (array)$services["data"];
            $actions = $this->compareServices($ext_services);
            return $this->doServices($actions);
        } else {
            throw new YclientsException("Ошибка доступа к API");
        }
    }

    /**
     * @return array
     * @throws YclientsException
     */
    private function recordsSync(): array
    {
        $records = $this->api->getRecords();
        if($records["success"] === true) {
            $ext_records = $records["data"];
            $actions = $this->compareRecords($ext_records);
            return $this->doRecords($actions);
        } else {
            throw new YclientsException("Ошибка доступа к API");
        }
    }

    /**
     * @return array
     * @throws YclientsException
     */
    private function categoriesSync(): array
    {
        $categories = $this->api->getCategories();
        if($categories["success"] === true) {
            $ext_categories = $categories["data"];
            $actions = $this->compareCategories($ext_categories);

            // Not supported
            $this->doCategories($actions);
            return $actions;
        } else {
            throw new YclientsException("Ошибка доступа к API");
        }
    }

    /**
     * @return array
     * @throws YclientsException
     */
    private function productsSync(): array
    {
        $products = $this->api->getProducts();
        if($products["success"] === true) {
            $ext_products = $products["data"];
            $actions = $this->compareProducts($ext_products);
            return $this->doProducts($actions);
        } else {
            throw new YclientsException("Ошибка доступа к API");
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
            ->whereNull('yclients_id')
            ->whereNull('beauty_id')
            ->get()
            ->toArray();

        foreach ($ext_clients as $ext_client) {
            $count = TelegramUser::query()
                ->where('yclients_id', $ext_client['id'])
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
            ->whereNull('yclients_id')
            ->whereNull('beauty_id')
            ->get()
            ->toArray();

        foreach ($ext_staffs as $ext_staff) {
            $count = User::query()
                ->where('yclients_id', $ext_staff['id'])
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
                ->where('yclients_id', $type['id'])
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
                ->where('yclients_id', $ext_service['id'])
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
            ->where('created_at', '>', Carbon::now()->format('Y-m-d 00:00'))
            ->get()
            ->toArray();

        foreach ($ext_records as $ext_record) {
            $count = Record::query()
                ->where('yclients_id', $ext_record['id'])
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
     * @param array $ext_categories
     * @return array
     */
    private function compareCategories(array $ext_categories): array
    {
        return [
            "create" => [],
            "update" => [],
            "upload" => []
        ];
    }

    /**
     * @param array $ext_products
     * @return array
     */
    private function compareProducts(array $ext_products): array
    {
        $create = [];
        $update = [];
        $upload = Catalog::query()
            ->whereNull('yclients_id')
            ->whereNull('beauty_id')
            ->get()
            ->toArray();

        foreach ($ext_products as $ext_product) {
            $count = Catalog::query()
                ->where('yclients_id', $ext_product['good_id'])
                ->count();
            if($count > 0) {
                $update[] = $ext_product;
            } else {
                $create[] = $ext_product;
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
     * @throws YclientsException
     */
    private function doClients(array $action): array
    {
        // Insert
        $create = [];
        foreach ($action['create'] as $client) {
            $client_entity = new TelegramUser([
                'yclients_id'   => $client["id"],
                'first_name'    => $client['name'],
                'last_name'     => '',
                'phone'    => $client['phone'],
                'email'    => $client['email'],
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
                'phone'    => $client['phone'],
            ];

            if(!empty($client['email'])) $fields["email"] = $client['email'];

            TelegramUser::query()
                ->where('yclients_id', $client['id'])
                ->update($fields);
            $update[] = $client;
        }

        // Upload
        $upload = [];
        if(count($action["upload"]) > 0) {
            $upload = $this->api->addClients($action["upload"]);
        }

        return [
            "create" => count($create),
            "update" => count($update),
            "upload" => count($upload)
        ];
    }

    /**
     * @param array $action
     * @return array
     * @throws YclientsException
     */
    private function doStaff(array $action): array
    {
        // Insert
        $create = [];
        foreach ($action['create'] as $client) {

            $pass = $this->generatePass();

            $staff_entity = new User([
                'yclients_id'   => $client["id"],
                'name'          => $client['name'],
                'status'        => 1,
                'password'      => Hash::make($pass)
            ]);
            $staff_entity->save();
            $create[] = $client;
        }

        // Update
        $update = [];
        foreach ($action['update'] as $client) {
            User::query()
                ->where('yclients_id', $client['id'])
                ->update([
                    'name'    => $client['name'],
                ]);
            $update[] = $client;
        }

        // Upload
        $upload = [];
        if(count($action["upload"]) > 0) {
            $upload = $this->api->addStaff($action["upload"]);
        }

        return [
            "create" => count($create),
            "update" => count($update),
            "upload" => count($upload)
        ];

    }

    /**
     * @param array $action
     * @return array
     * @throws YclientsException
     */
    private function doTypes(array $action): array
    {
        // Insert
        $create = [];
        foreach ($action['create'] as $type) {
            $type_entity = new TypeService([
                'yclients_id'   => $type["id"],
                'type'          => $type['title']
            ]);
            $type_entity->save();
            $create[] = $type;
        }

        // Update
        $update = [];
        foreach ($action['update'] as $type) {
            TypeService::query()
                ->where('yclients_id', $type['id'])
                ->update([
                    'type'    => $type['title'],
                ]);
            $update[] = $type;
        }

        // Upload
        $upload = [];
        if(count($action["upload"]) > 0) {
            $upload = $this->api->addTypes($action["upload"]);
        }

        return [
            "create" => count($create),
            "update" => count($update),
            "upload" => count($upload)
        ];
    }

    /**
     * @param array $action
     * @return array
     * @throws YclientsException
     */
    private function doServices(array $action): array
    {
        // Insert
        $create = [];
        foreach ($action['create'] as $service) {

            $type = TypeService::getByYClientsId($service['category_id']);
            $service_entity = new Service([
                'yclients_id'       => $service["id"],
                'type_service_id'   => $type->id,
                'name'              => $service['title'] . " (импорт yclients)",
                'price'             => $service['price_min'],
                'cash_pay'          => 1,
                'bonus_pay'         => 1,
                'online_pay'        => 1
            ]);
            $service_entity->save();
            $create[] = $service;

            if(isset($service["staff"]) && count($service["staff"]) > 0) {
                foreach ($service["staff"] as $staff) {
                    $row = User::query()->where('yclients_id', $staff["id"])->get('id')->first()->toArray();

                    DB::table('users_services')->insert([
                        'user_id' => $row['id'],
                        'service_id' => $service_entity->id
                    ]);
                }
            }
        }

        // Update
        $update = [];
        foreach ($action['update'] as $service) {
            Service::query()
                ->where('yclients_id', $service['id'])
                ->update([
                    'name'          => $service['title'],
                    'price'         => $service['price_min'],
                ]);
            $update[] = $service;
        }

        // Upload
        $upload = [];
        if(count($action["upload"]) > 0) {
            $upload = $this->api->addServices($action["upload"]);
        }


        return [
            "create" => count($create),
            "update" => count($update),
            "upload" => count($upload)
        ];
    }

    /**
     * @param array $action
     * @return array
     * @throws YclientsException
     */
    private function doRecords(array $action): array
    {
        // Insert
        $create = [];
        foreach ($action['create'] as $record) {

            if(!isset($record["services"]) || count($record["services"]) == 0) {
                continue;
            }

            $telegram_user = TelegramUser::getByYClientsId($record["client"]["id"]);
            $service = Service::getByYClientsId($record["services"][0]['id']);
            $staff = User::getByYClientsId($record["staff"]["id"]);

            if(!is_null($telegram_user) && !is_null($service) && !is_null($staff)) {
                $address_text = "Адрес услуги " . $record["services"][0]["title"] . " (импортпорт из YClients)";
                $address = Address::query()->where('address', $address_text)->first();
                if(is_null($address)) {
                    $address = new Address([
                        "address" => $address_text
                    ]);
                    $address->save();
                }

                $record_entity = new Record([
                    'yclients_id'      => $record["id"],
                    'telegram_user_id' => $telegram_user->id,
                    'service_id'       => $service->id,
                    'address_id'       => $address->id,
                    'user_id'          => $staff->id,
                    'status'           => $record["confirmed"],
                    'date'             => Carbon::parse($record['date'])->format('Y-m-d'),
                    'time'             => Carbon::parse($record['date'])->format('H:i'),
                ]);
                $record_entity->save();
                $create[] = $record;
            }
        }

        // Update
        $update = [];
        foreach ($action['update'] as $record) {
            Record::query()
                ->where('yclients_id', $record['id'])
                ->update([
                    'status' => $record['confirmed'],
                    'date'   => Carbon::parse($record['date'])->format('Y-m-d'),
                    'time'   => Carbon::parse($record['date'])->format('H:i'),
                ]);
            $update[] = $record;
        }

        // Upload
        $upload = [];
        if(count($action["upload"]) > 0) {
            $upload = $this->api->addRecords($action["upload"]);
        }


        return [
            "create" => count($create),
            "update" => count($update),
            "upload" => count($upload)
        ];
    }

    /**
     * @param array $action
     */
    private function doCategories(array $action): void
    {
        // We not have categories
    }

    /**
     * @param array $action
     * @return array
     * @throws YclientsException
     */
    private function doProducts(array $action): array
    {
        // Insert
        $create = [];
        foreach ($action['create'] as $product) {
            $product_entity = new Catalog([
                'yclients_id'   => $product["good_id"],
                'title'         => $product["title"],
                'text'          => $product["label"],
                'img'           => "",
                'price'         => $product["cost"],
                'count'         => 0,
                'article'       => $product["article"],
            ]);
            $product_entity->save();
            $create[] = $product;
        }

        // Update
        $update = [];
        foreach ($action['update'] as $product) {
            Catalog::query()
                ->where('yclients_id', $product['good_id'])
                ->update([
                    'title'         => $product["title"],
                    'text'          => $product["label"],
                    'img'           => "",
                    'price'         => $product["cost"],
                    'count'         => 0,
                    'article'       => $product["article"],
                ]);
            $update[] = $product;
        }

        // Upload
        $upload = [];
        if(count($action["upload"]) > 0) {
            $upload = $this->api->addProducts($action["upload"]);
        }

        return [
            "create" => count($create),
            "update" => count($update),
            "upload" => count($upload)
        ];
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

}
