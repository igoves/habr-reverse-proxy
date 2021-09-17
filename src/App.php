<?php

namespace HabrReverseProxy;

use simplehtmldom\HtmlWeb;

final class App
{
    private $html;
    private $client;
    private $path;
    private $remote;
    private const PROXY_URL = 'https://habr.com/';
    private const WORD_LENGTH = 6;

    public function __construct($request)
    {
        $this->path = isset($request['path']) && $this->validateURL(self::PROXY_URL . $request['path']) ?
            self::PROXY_URL . $request['path'] : self::PROXY_URL . 'en/all/';

        if ($this->path === self::PROXY_URL . 'en/') {
            $this->path = self::PROXY_URL . 'en/all/';
        }

        if ($this->path === self::PROXY_URL . 'ru/') {
            $this->path = self::PROXY_URL . 'ru/all/';
        }

        $this->remote = isset($request['remote']) && $this->validateURL($request['remote']) ? $request['remote'] : '';

        $this->client = new HtmlWeb();
    }

    public function run(): string
    {
        $this->responseStaticContents();
        if ($this->getHttpResponseCode() === "404") {
            die('error page not found');
        }
        $this->showResponseImage($this->path);
        $this->html = $this->client->load($this->path);
        $this->modifyImages();
        $this->modifyContents();
        $this->injectContents();

        return $this->html;
    }

    private function validateURL($url): bool
    {
        return (bool)filter_var($url, FILTER_VALIDATE_URL);
    }

    public function getHttpResponseCode()
    {
        $headers = @get_headers($this->path);
        return $headers && strpos($headers[0], '200') ? substr($headers[0], 9, 3) : die('error with url');
    }

    public function addProtocol($url): string
    {
        $protocol = explode('//', $url);

        if (empty($protocol[0])) {
            return 'https:';
        }

        return '';
    }

    private function showResponseImage($url): void
    {
        $path_parts = pathinfo($url);
        if (isset($path_parts['extension']) && in_array($path_parts['extension'], ['png', 'jpg', 'jpeg', 'svg'])) {
            if ($path_parts['extension'] === 'svg') {
                $imginfo['mime'] = ' image/svg+xml';
            } else {
                $imginfo = getimagesize($url);
            }
            header("Content-type: {$imginfo['mime']}");
            readfile($url);
            die();
        }
    }

    private function showResponseHtml($remote): void
    {
        echo str_replace(self::PROXY_URL, 'http://' . $_SERVER['HTTP_HOST'] . '/', $this->getPage($remote));
        die();
    }

    private function modifyImages(): void
    {
        $i = 0;
        foreach ($this->html->find('img') as $element) {

            $property = 'data-src';
            $property2 = 'data-blurred';

            if (isset($element->$property) && !empty($element->$property)) {
                $this->html->find('img', $i)->src = $element->$property;
                $element->$property2 = null;
            } else {
                $this->html->find('img', $i)->src = '/?remote=' . $this->addProtocol($element->src) . $element->src;
            }

            $i++;
        }
    }

    private function modifyContents(): void
    {
        $i = 0;
        $class = '.article-formatted-body';
        foreach ($this->html->find($class) as $element) {

            $content = str_replace(['(', ')', '.', ';', ',', ':', '?'], '', strip_tags($element));
            $content = explode(' ', $content);

            $list = [];
            foreach ($content as $word) {
                if (mb_strlen(trim($word)) === self::WORD_LENGTH) {
                    $list['/\b' . $word . '\b/u'] = $word . '&trade;';
                }
            }

            $content_replaced = preg_replace(array_keys($list), array_values($list), $element);
            $this->html->find($class, $i)->outertext = $content_replaced;

            $i++;
        }
    }

    private function injectContents(): void
    {
        $additional_style = file_get_contents(dirname(__DIR__).'/src/assets/page-flow_page-flows.acb5aaf3.css');
        $inject = '<style>' . $additional_style . '</style>';
        $this->html->find('head', 0)->innertext = $inject . $this->html->find('head', 0)->innertext;
        $this->html->find('.tm-svg-img', 0)->innertext = file_get_contents(dirname(__DIR__).'/src/assets/logo.svg');
        $this->html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $this->html);
    }

    private function responseStaticContents(): void
    {
        if (empty($this->remote)) {
            return;
        }

        $this->remote = $this->addProtocol($this->remote) . $this->remote;
        $this->showResponseImage($this->remote);
        $this->showResponseHtml($this->remote);
    }

    private function getPage($url)
    {
        if (is_callable('curl_init')) {

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_USERAGENT,
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.46');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_REFERER, self::PROXY_URL);
            curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . 'cookie.txt');
            curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . 'cookie.txt');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_PROXY, '23.107.176.45:32180');
            // curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'user:password'); // Use if proxy have username and password
            // curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5); // If expected to call with specific PROXY type
            $output = curl_exec($ch);
            if (curl_errno($ch) > 0) {
                die('error ' . curl_error($ch));
            }
            curl_close($ch);
            return $output;

        }

        return file_get_contents($url);
    }


}
