# WebPParseAndConvert
Парсинг HTML страницы и конвертирование изображений в WebP через библиотеку [WebP Convert](https://github.com/rosell-dk/webp-convert).

## Проверка возможности конвертирования в WebP на сервере
```php
var_dump(function_exists('imagewebp')); // bool(true) - можно
```
Если на сервере нет `Imagick`, то конвертирование работать будет, но качество получившегося изображения не будет соответствовать качеству оригинального изображения (установится в 75 для jpg и 85 для png)

## Установка через Composer
```bash
composer require gtarr/webp-parse-and-convert
```
## Установка без Composer
Скачать [отсюда](https://php-download.com/package/gtarr/webp-parse-and-convert) и загрузить папку `vendor` на сайт
## Использование
1. По умолчанию
```php
$content = '<html>...<img src="">...</html>'; // HTML страницы
$rootDir = $_SERVER['DOCUMENT_ROOT'];         // корень сайта

require $rootDir . '/vendor/autoload.php';

use WebPParseAndConvert\WebPParseAndConvert;

$converter = new WebPParseAndConvert($content, $rootDir);  

$content = $converter->execute();
```
2. C доп. опциями (в примере представлены значения по умолчанию)
```php
$options = [
   "formats" => ['jpg', 'jpeg', 'png'],
   "patterns" => [
      [
         'pattern' => '/<img[^>]+src=("[^"]*")[^>]*>/i',
         'exclude' => ['"', './']
      ],
      [
         'pattern' => '/background-image:.+url\(([^"]+)\)/i',
         'exclude' => ["'", "./"]
      ],
   ],
   "devices" => ['iphone', 'ipod', 'ipad', 'macintosh', 'mac os', 'Edge', 'MSIE', 'Trident'],
   "converterOptions" => [],
   "debug" => false
];

$converter = new WebPParseAndConvert($content, $rootDir, $options); 
```
## Опции
Опция      | Тип | Описание
:---------:|:---:|:---------
'formats' | Array | Форматы изображений, которые будут найдены на странице и конвертированы в webp 
'patterns' | Array | Регулярные выражения, по которым будет производиться поиск, имеет 2 параметра:
'patterns' => [['pattern' => '...']] | String | 1. Само регулярное выражение
'patterns' => [['exclude'  => []]] | Array | 2. Подстроки, которые нужно исключить из обработки после нахождения строки по регулярному выражению, например, кавычки. В исключение также автоматически добавляется адрес сайта с протоколом для обработки абсолютных путей изображений.
'devices' | Array | Список устройств, для которых по User-agent будут выводиться оригинальные изображения в форматах jpg, jpeg, png
'converterOptions' | Array | Опции, передаваемые в вызов `WebPConvert::convert()` библиотеки [WebP Convert](https://github.com/rosell-dk/webp-convert)
'debug' | Boolean | Использование встроенного логгера библиотеки [WebP Convert](https://github.com/rosell-dk/webp-convert)
'useApi' | Boolean | Использования для конвертирования внешнего сервиса основанного на [webp-convert-cloud-service](https://github.com/rosell-dk/webp-convert-cloud-service)
'api' | Array | Параметры для подключения к внешнему сервису
'api' => ['key' => '...'] | String | API ключ внешнего облачного сервиса
'api' => ['url' => '...'] | String | URL внешнего облачного сервиса

## Решение проблем
При ошибке `PNG file skipped. GD is configured not to convert PNGs` необходимо отключить обработку PNG изображений, для этого нужно в опцию `'formats'` передать только `['jpg', 'jpeg']`.  
**С версии 0.1.0 это исключение перехватывается**
## Примеры для CMS
* [1С-Битрикс](https://github.com/GTaRR/WebPParseAndConvert/wiki/1C-Bitrix)
* [ModX](https://github.com/GTaRR/WebPParseAndConvert/wiki/ModX)
* [Без CMS](https://github.com/GTaRR/WebPParseAndConvert/wiki/%D0%91%D0%B5%D0%B7-CMS)
## Если нет поддержки WebP на сервере сайта
Можно использовать [webp-convert-cloud-service](https://github.com/rosell-dk/webp-convert-cloud-service). Создать на другом сервере, на котором есть поддержка конвертирования в WebP облачный сервис и обращаться к нему по API. Для работы такого варианта необходимо в `$options` передать два параметра `useApi` и `api`:
```php
$options = array(
    'useApi' => true,
    'api' => array(
       'key' => 'some API key',
       'url' => 'http://example.com/wpc.php'
    )
);
```
В таком случае конвертирование через облачный сервис будет происходить только при указании в URL страницы GET параметра '?webpconvert=Y'.
