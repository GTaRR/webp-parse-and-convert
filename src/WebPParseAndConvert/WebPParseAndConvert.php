<?php

namespace WebPParseAndConvert;

use WebPConvert\WebPConvert;
use WebPConvert\Loggers\EchoLogger;

class WebPParseAndConvert
{
    private $content;
    private $rootDir;
    private $images;
    private $formats = array(
        'jpg',
        'jpeg',
        'png'
    );
    private $patterns = array(
        array(
            'pattern' => '/<img[^>]+src=("[^"]*")[^>]*>/i',
            'exclude' => array('"', './')
        ),
        array(
            'pattern' => '/background-image:.+url\(([^"]+)\)/i',
            'exclude' => array("'", "./")
        ),
//        array(
//            'pattern' => '/background:.+url\(([^"]+)\)/i',
//            'exclude' => array("'","./")
//        ),
//        array(
//            'pattern' => '/data-src=\"([^"]+)\"/i',
//            'exclude' => array("'","./")
//        ),
    );
    private $notSupportDevice = array(
        'iphone',
        'ipod',
        'ipad',
        'macintosh',
        'mac os',
        'Edge',
        'MSIE',
        'Trident'
    );
    private $options = false;
    private $debug = false;

    /**
     * @param   string  $content - HTML загружаемой страницы
     * @param   string  $rootDir - Корень сайта в файловой системе
     * @param   array   $options - Дополнительные опции
     * @return  string  &$content
     */
    public function __construct($content, $rootDir, $options = array())
    {
        if (!isset($content) || empty($content)) return false;

        $this->content = $content;
        $this->rootDir = ($rootDir) ? $rootDir : $_SERVER['DOCUMENT_ROOT'];

        if (isset($options['formats']) && is_array($options['formats']))
            $this->formats = $options['formats'];
        if (isset($options['patterns']) && is_array($options['patterns']))
            $this->patterns = $options['patterns'];
        if (isset($options['devices']) && is_array($options['devices']))
            $this->notSupportDevice = $options['devices'];
        if (isset($options['converterOptions']) && is_array($options['converterOptions']))
            $this->options = $options['converterOptions'];
        if (isset($options['debug']) && (!!$options['debug']))
            $this->debug = $options['debug'];

        return true;
    }

    /**
     * Получение адреса корня сайта
     *
     * @return  string
     */
    private function getProtocolAndHostName(){
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        return $protocol . $_SERVER['HTTP_HOST'];
    }

    /**
     * Парсинг изображений на странице
     *
     * @param   string  $pattern - Регулярное выражение для поиска изображений
     * @param   string  $content - HTML загружаемой страницы
     * @param   array   $exclude - Исключаемые строки
     * @return  array   $images  - Массив адресов изображений
     */
    private function parseImgByPattern($pattern, $content, $exclude = array())
    {
        $images = array();

        preg_match_all($pattern, $content, $result);

        if (count($result)) {
            foreach ($result[1] as $img) {
                if (is_array($exclude) && count($exclude) > 0) {
                    $exclude[] = $this->getProtocolAndHostName();

                    foreach ($exclude as $search){
                        $img = str_replace($search, "", $img);
                    }
                }

                $standardFormats = array('jpg','jpeg','png');
                foreach ($standardFormats as $format) {
                    if (pathinfo(strtolower($img), PATHINFO_EXTENSION) == $format)
                        $images[] = $img;
                }
            }
        }

        return $images;
    }

    /**
     * Конвертирование массива изображений в WebP по адресу [path/name.*].webp
     * и сохранением исходных файлов [path/name.*]
     *
     * @param   string  $content - HTML загружаемой страницы
     * @param   array   $images  - Массив адресов изображений
     * @return  string  $content
     */
    private function convertImages($content, $images)
    {
        foreach ($images as $img_src_rel)
        {
            if ((!$img_src_rel) || (!file_exists($this->rootDir . $img_src_rel)))
                continue;

            if (!file_exists($this->rootDir . $img_src_rel . '.webp'))
            {
                $img_src_abs = $this->rootDir . $img_src_rel;
                $destination = $this->rootDir . $img_src_rel . '.webp';

                // во избежании ошибок обработки png картинок с расширениями .jpg/.jpeg
                if (!in_array('.png', $this->formats)
                    && strpos(strtolower($img_src_abs), '.png') === false
                    && mime_content_type($img_src_abs) === 'image/png') continue;

                // 2 проверки на формат для возможности подстановки загрженного вручную
                // WebP избражения из PNG в проверке на наличие файла
                $isSupportFormat = false;
                foreach ($this->formats as $format) {
                    if (pathinfo(strtolower($img_src_rel), PATHINFO_EXTENSION) == $format)
                        $isSupportFormat = true;
                }
                if (!$isSupportFormat) continue;

                $isConvert = false;
                try {
                    if ($this->options && $this->debug) {
                        if (WebPConvert::convert($img_src_abs, $destination, $this->options, new EchoLogger()))
                            $isConvert = true;
                    } elseif ($this->options) {
                        if (WebPConvert::convert($img_src_abs, $destination, $this->options))
                            $isConvert = true;
                    } elseif ($this->debug) {
                        if (WebPConvert::convert($img_src_abs, $destination, array(), new EchoLogger()))
                            $isConvert = true;
                    } else {
                        if (WebPConvert::convert($img_src_abs, $destination))
                            $isConvert = true;
                    }
                } catch (\WebPConvert\Converters\Exceptions\ConversionDeclinedException $e) {
                    continue;
                }

                if ($isConvert) {
                    $img_dest = $img_src_rel . '.webp';
                } else {
                    $img_dest = $img_src_rel;
                }
            } else {
                $img_dest = $img_src_rel . '.webp';
            }

            $content = str_replace($img_src_rel, $img_dest, $content);
        }

        return $content;
    }

    /**
     * @return  string  $content - Итоговый контент страницы
     */
    public function execute()
    {
        $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);

        foreach ($this->notSupportDevice as $val) {
            if (stripos($userAgent, $val) !== false) return $this->content;
        }

        $this->images = array();

        if (count($this->patterns) > 0) {
            foreach ($this->patterns as $pattern) {
                $this->images = array_merge(
                    $this->images,
                    $this->parseImgByPattern($pattern['pattern'], $this->content, $pattern['exclude'])
                );
            }
        }

        $this->images = array_unique($this->images);

        if (count($this->images))
            return $this->convertImages($this->content, $this->images);
        else
            return $this->content;
    }
}
