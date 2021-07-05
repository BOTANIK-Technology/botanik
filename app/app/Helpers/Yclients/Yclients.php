<?php

namespace App\Helpers\Yclients;

use App\Models\Api;
use App\Models\Service;
use App\Models\TelegramUser;
use App\Models\TypeService;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

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
            Log::debug("Clients: ", $clients);

            // Синхронизация специалистов
            $staff = $this->staffSync();
            Log::debug("Staff: ", $staff);

            // Синхронизация типов (категорий) услуг
            $services = $this->servicesTypesSync();
            Log::debug("Types Services: ", $services);

            // Синхронизация услуг
            $services = $this->servicesSync();
            Log::debug("Services: ", $services);

            // Синхронизация записей
//            $records = $this->recordsSync();
//            Log::debug("Records: ", $services);

            // Синхронизация каталогов

            // Синхронизация товаров

        } catch (YclientsException $e) {
            return [
                "result"    => "fail",
                "code"      => $e->getCode(),
                "message"   => $e->getMessage(),
            ];
        }
        return [
            "result"    => "success",
            "clients"   => $clients
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
        if($clients["success"] === true && count($clients["data"]) > 0) {
            $ext_clients = (array)$clients["data"];
            $clients = TelegramUser::all()->toArray();
            return $this->compareClients($ext_clients, $clients);
        }
        return [];
    }

    /**
     * @return array
     * @throws YclientsException
     */
    private function staffSync(): array
    {
        $staff = $this->api->getStaff();
        if($staff["success"] === true && count($staff["data"]) > 0) {
            $ext_staff = (array)$staff["data"];
            $staff = User::query()
                ->leftJoin('users_services', 'users.id', '=', 'users_services.user_id', )
                ->get()
                ->toArray();
            return $this->compareStaff($ext_staff, $staff);
        }
        return [];
    }

    /**
     * @return array
     * @throws YclientsException
     */
    private function servicesTypesSync(): array
    {
        $services = $this->api->getServicesTypes();
        if($services["success"] === true && count($services["data"]) > 0) {
            $ext_types = (array)$services["data"];
            $types = TypeService::all()->toArray();
            return $this->compareTypesServices($ext_types, $types);
        }
        return [];
    }

    /**
     * @return array
     * @throws YclientsException
     */
    private function servicesSync(): array
    {
        $services = $this->api->getServices();
        if($services["success"] === true && count($services["data"]) > 0) {
            $ext_services = (array)$services["data"];
            $services = Service::all()->toArray();
            return $this->compareServices($ext_services, $services);
        }
        return [];
    }

    /**
     * @return array
     * @throws YclientsException
     */
    private function recordsSync(): array
    {
        $records = $this->api->getRecords();
        if($records["success"] === true && count($records["data"]) > 0) {
            return $records["data"];
        }
        return [];
    }

    /**
     * @param array $clients_ext
     * @param array $clients
     * @return array
     */
    private function compareClients(array $clients_ext, array $clients): array
    {
        // TODO: Make compare
        return $clients_ext;
        return [];
    }

    /**
     * @param array $staff_ext
     * @param array $staff
     * @return array
     */
    private function compareStaff(array $staff_ext, array $staff): array
    {
        // TODO: Make staff
        return $staff_ext;
        return [];
    }

    /**
     * @param array $types_ext
     * @param array $types
     * @return array
     */
    private function compareTypesServices(array $types_ext, array $types): array
    {
        // TODO: Make staff
        return $types_ext;
        return [];
    }

    /**
     * @param array $services_ext
     * @param array $services
     * @return array
     */
    private function compareServices(array $services_ext, array $services): array
    {
        // TODO: Make staff
        return $services_ext;
        return [];
    }


}
