# WebPParseAndConvert
Конвертирование изображений в WebP через парсинг переданного HTML страницы.
## Установка через Composer
```php
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
   "formats" => ['.jpg', '.jpeg', '.png'],
   "patterns" => [
      [
         'pattern' => '<img[^>]+src=("[^"]*")[^>]+>',
         'exclude' => ['"', './']
      ],
      [
         'pattern' => '/background-image:.+url\(([^"]+)\)/i',
         'exclude' => ["'", "./"]
      ],
   ],
   "devices" => ['iphone', 'ipod', 'ipad', 'macintosh', 'mac os', 'Edge', 'MSIE']
];

$converter = new WebPParseAndConvert($content, $rootDir, $options); 
```
## Опции
Опция      | Тип | Описание
:---------:|:---:|:--------:
'formats' | Array | Форматы изображений, которые будут найдены на странице и конвертированы в webp 
'patterns' | Array | Регулярные выражения, по которым будет производиться поиск, имеет 2 параметра
'patterns' => ['pattern'] | String | Само регулярное выражение
'patterns' => ['exclude'] | String | Подстроки, которые нужно исключить из обработки после нахождения строки по регулярному выражению, например, кавычки
'devices' | Array | Список устройств, для которых будут выводиться оригинальные изображения в форматах jpg, jpeg, png

## Решение проблем
При ошибке `PNG file skipped. GD is configured not to convert PNGs` необходимо отключить обработку PNG изображений, для этого нужно в опцию `'formats'` передать только `['.jpg.', '.jpeg']`
## Примеры для CMS
* [1С-Битрикс](https://github.com/GTaRR/WebPParseAndConvert/wiki/1C-Bitrix)
* [ModX](https://github.com/GTaRR/WebPParseAndConvert/wiki/ModX)
* [Без CMS](https://github.com/GTaRR/WebPParseAndConvert/wiki/%D0%91%D0%B5%D0%B7-CMS)
