<?php

namespace UxmanSarwar;

use Psr\Http\Message\ResponseInterface;

class Http
{
    // public static string $user_agent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36";
    // public static string $user_agent;
    public static string $proxy;
    public static bool $use_proxy;

    public static string $cookie_path;

    private static \GuzzleHttp\Client $client;
    private static \GuzzleHttp\Cookie\FileCookieJar $cookie_jar;


    /**
     * @singleton 
     * setup default options for client
     */
    public static function client(string $cookie_path = '', string $proxy = '', bool $use_proxy = false): \GuzzleHttp\Client
    {
        self::$cookie_path = $cookie_path;
        self::$proxy       = $proxy;
        self::$use_proxy   = $use_proxy;
        if (isset(self::$client))
            return self::$client;

        $opt = [

            \GuzzleHttp\RequestOptions::COOKIES => self::cookieJar(),
            \GuzzleHttp\RequestOptions::VERSION => '2.0',
            \GuzzleHttp\RequestOptions::DECODE_CONTENT => true,
            \GuzzleHttp\RequestOptions::ALLOW_REDIRECTS => [
                'max'             => 5,
                'strict'          => false,
                'referer'         => true,
                'protocols'       => ['http', 'https'],
                'track_redirects' => true
            ],
            \GuzzleHttp\RequestOptions::HEADERS => [
                'user-agent'  => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
            ]
        ];
        isset(self::$use_proxy) && self::$use_proxy ? $opt[\GuzzleHttp\RequestOptions::PROXY]   = self::$proxy      : '';
        !empty(self::cookieFilePath())              ? $opt[\GuzzleHttp\RequestOptions::COOKIES] = self::cookieJar() : '';
        

        self::$client = new \GuzzleHttp\Client($opt);
        return self::$client;
    }

    public static function request(string $method = 'GET', string $url, array $option = []): array
    {

        $html = [];
        if (isset(self::$client))
            try {
                $req = self::client()->request(strtoupper($method), $url, $option);
                $html['status'] = $req->getStatusCode();
                $html['header'] = $req->getHeaders();
                $html['content'] = $req->getBody()->getContents();
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                $html['errors']['request_exception'] = $e->getMessage();
                if ($e->hasResponse()) {
                    $response = $e->getResponse();
                    $html['header'] = $response->getHeaders();
                    $html['status'] = $response->getStatusCode();
                    $html['content'] = (string)$response->getBody();
                }
            } catch (\Exception $e) {
                $html['errors']['exception'] = $e->getMessage();
            } finally {
                return $html;
            }
        return ['errors' => ['Please initialize client 1st!']];
    }

    public static function get(string $url, array $option = []): array
    {
        return self::request('GET', $url, $option);
    }

    public static function post(string $url, array $option = []): array
    {
        return self::request('POST', $url, $option);
    }


    /**
     * @singleton 
     */
    private static function cookieJar(): \GuzzleHttp\Cookie\FileCookieJar
    {
        return isset(self::$cookie_jar) ? self::$cookie_jar : self::$cookie_jar = new \GuzzleHttp\Cookie\FileCookieJar(self::cookieFilePath(), true);
    }

    private static function cookieFilePath(): string
    {
        if (self::dirFailSave(self::$cookie_path))
            return self::$cookie_path . DIRECTORY_SEPARATOR . 'cookies.json';
        return '';
    }

    private static function dirFailSave($path): bool
    {
        if (empty($path))
            return false;
        if (!is_dir($path))
            return mkdir($path, 0755, true);
        return true;
    }
}
