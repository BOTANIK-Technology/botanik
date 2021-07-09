<?php

namespace App\Helpers\Beauty;

use App\Models\Api;
use App\Models\TelegramUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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

            // Синхронизация типов (категорий) услуг
            //$services_types = $this->servicesTypesSync();

            // Синхронизация услуг
            //$services = $this->servicesSync();

            // Синхронизация записей
            //$records = $this->recordsSync();

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
            "services_types"    => ['create' => 0, 'update' => 0, 'upload' => 0], //$services_types,
            "services"          => ['create' => 0, 'update' => 0, 'upload' => 0], //$services,
            "records"           => ['create' => 0, 'update' => 0, 'upload' => 0], //$records,
            "categories"        => ['create' => 0, 'update' => 0, 'upload' => 0], //$categories,
            "products"          => ['create' => 0, 'update' => 0, 'upload' => 0], //$products,
        ];
    }

    /**
     * @return array
     */
    private function clientsSync(): array
    {
        $clients = $this->api->getClients();

        if($clients["success"] === true) {
            $ext_clients = (array)$clients["data"];
            $actions = $this->compareClients($ext_clients);
            return $this->doClients($actions);
        }
        return [];
    }

    /**
     * @return array
     */
    private function staffSync(): array
    {
        $ext_staff = $this->api->getStaff();
        if(!isset($ext_staff["error"])) {
            $actions = $this->compareStaff($ext_staff);
            return $this->doStaff($actions);
        }
        return [];
    }

    /**
     * @return array
     */
    private function servicesTypesSync(): array
    {
        $services = $this->api->getServicesTypes();
        if($services["success"] === true) {
            $ext_types = (array)$services["data"];
            $actions = $this->compareTypesServices($ext_types);
            return $this->doTypes($actions);
        }
        return [];
    }

    /**
     * @return array
     */
    private function servicesSync(): array
    {
        $services = $this->api->getServices();
        if($services["success"] === true) {
            $ext_services = (array)$services["data"];
            $actions = $this->compareServices($ext_services);
            return $this->doServices($actions);
        }
        return [];
    }

    /**
     * @return array
     */
    private function recordsSync(): array
    {
        $records = $this->api->getRecords();
        if($records["success"] === true) {
            $ext_records = $records["data"];
            $actions = $this->compareRecords($ext_records);
            return $this->doRecords($actions);
        }
        return [];
    }

    /**
     * @return array
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
        }
        return [];
    }

    /**
     * @return array
     */
    private function productsSync(): array
    {
        $products = $this->api->getProducts();
        if($products["success"] === true) {
            $ext_products = $products["data"];
            $actions = $this->compareProducts($ext_products);
            return $this->doProducts($actions);
        }
        return [];
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
                ->where('beauty_id', $client['id'])
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
    */
    private function doStaff(array $action): array
    {
        // Insert
        $create = [];
        foreach ($action['create'] as $client) {

            $pass = $this->generatePass();

            $staff_entity = new User([
                'beauty_id'   => $client["id"],
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
                ->where('beauty_id', $client['id'])
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
}
