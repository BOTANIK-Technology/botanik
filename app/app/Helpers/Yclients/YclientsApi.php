<?php

namespace App\Helpers\Yclients;

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
        $params = [
            'company_id' => $this->getCompanyID(),
        ];

        return $this->request("clients/" . $this->getCompanyID(),
            $params,
            self::METHOD_GET,
            $this->getUserToken()
        );
    }

    /**
     * Получить список клиентов
     *
     * @return array
     * @throws YclientsException
     */
    public function getStaff(): array
    {
        $params = [
            'company_id' => $this->getCompanyID(),
        ];

        return $this->request("company/" . $this->getCompanyID() . '/staff',
            $params,
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
        $params = [
            'company_id' => $this->getCompanyID(),
        ];

        return $this->request("service_categories/" . $this->getCompanyID(),
            $params,
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
        $params = [
            'company_id' => $this->getCompanyID(),
        ];

        return $this->request("services/" . $this->getCompanyID(),
            $params,
            self::METHOD_GET,
            $this->getUserToken()
        );
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
