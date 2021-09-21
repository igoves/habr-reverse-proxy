<?php

namespace HabrReverseProxy;

use simplehtmldom\HtmlDocument;

final class App
{
    private $path;
    private const PROXY_URL = 'https://habr.com/';
    private const WORD_LENGTH = 6;

    public function __construct($request)
    {
        if (isset($request['path']) && ($request['path'] === 'en/' || $request['path'] === 'ru/')) {
            header('Location: /' . $request['path'] . 'all/');
            die();
        }

        $this->path = isset($request['path']) && !empty($request['path']) ? self::PROXY_URL . $request['path'] : self::PROXY_URL;
    }

    public function run()
    {
        $result = $this->getPage($this->path);

        $this->checkHttpCode($result['info']);

        $html = new HtmlDocument();
        $html->load($result['response'])->plaintext;

        $this->modifyImages($html);
        $this->prepareContent($html);

        header('Content-Type: ' . $result['info']['content_type']);
        return preg_replace("/<script.*?\/script>/s", "", $html) ?: $html;
    }

    public function getPage($url): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        return [
            'response' => $response,
            'info' => $info,
        ];
    }

    private function checkHttpCode($info): void
    {
        $code = (string)$info['http_code'];
        $code = (int)$code[0];

        if ($code === 3) {
            header('Content-Type: ' . $info['content_type']);
            $info['redirect_url'] = str_replace(self::PROXY_URL, '', $info['redirect_url']);
            header('Location: ' . $info['redirect_url']);
            die();
        }

        if ($code === 4) {
            header("HTTP/1.0 404 Not Found");
            echo "error not found";
            die();
        }

    }

    private function modifyImages(HtmlDocument $html): void
    {
        foreach ($html->find('img') as $element) {
            $property = 'data-src';
            $property2 = 'data-blurred';
            if (isset($element->$property) && !empty($element->$property)) {
                $element->src = $element->$property;
                $element->$property2 = null;
            }
        }
    }

    private function searchWords(HtmlDocument $html): array
    {
        $content = str_replace([
            '(',
            ')',
            '.',
            ';',
            ',',
            ':',
            '?',
            '*',
            '"',
            '«',
            '»',
            '/',
            '+',
            '\'',
            '-',
            '—',
            '{',
            '}',
            '@',
            '#',
            '\n',
            '[',
            ']',
        ], '', strip_tags($html));
        $content = explode(' ', $content);
        $list = [];
        foreach ($content as $word) {
            $word = trim($word);
            if (mb_detect_encoding($word) === 'ASCII') {
                $word = mb_convert_encoding($word, "UTF-8");
            }
            if (iconv_strlen($word, 'UTF-8') === self::WORD_LENGTH) {
                $list['#(\b' . $word . '\b)#u'] = $word . '&trade;';
            }

        }
        return $list;
    }

    private function prepareContent(HtmlDocument $html): void
    {
        $list = $this->searchWords($html);

        foreach ($html->find('.tm-main-menu a') as $element) {
            $element->outertext = preg_replace(array_keys($list), array_values($list), $element);
        }

        foreach ($html->find('.tm-page__top a') as $element) {
            $element->outertext = preg_replace(array_keys($list), array_values($list), $element);
        }

        foreach ($html->find('.tm-articles-list__item') as $element) {
            $element->outertext = preg_replace(array_keys($list), array_values($list), $element);
        }

        foreach ($html->find('.tm-page-article__content') as $element) {
            $element->outertext = preg_replace(array_keys($list), array_values($list), $element);
            $element->outertext = preg_replace_callback(
                '/<code[^>]*>(.*?)<\/code>/si',
                static function ($matches) {
                    return htmlentities(str_replace('&trade;', '', $matches[1]));
                },
                $element->outertext
            );
            $element->outertext = str_replace(['<b><b></b>', '<b><s></b>', '<b><i></b>'], ['<b>&lt;b></b>', '<b>&lt;s></b>', '<b>&lt;i></b>'], $element->outertext);

        }

        foreach ($html->find('.tm-page-article__additional-blocks') as $element) {
            $element->outertext = preg_replace(array_keys($list), array_values($list), $element);
        }

        foreach ($html->find('.tm-comment__body-content') as $element) {
            $content_replaced = preg_replace(array_keys($list), array_values($list), $element);
            $element->outertext = $content_replaced;
        }

        foreach ($html->find('.tm-company-card__info') as $element) {
            $content_replaced = preg_replace(array_keys($list), array_values($list), $element);
            $element->outertext = $content_replaced;
        }

    }

}
