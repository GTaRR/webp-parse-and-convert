# WebPParseAndConvert
Конвертирование изображений в WebP через парсинг переданного HTML страницы.
## Установка через Composer
```php
composer require gtarr/webp-parse-and-convert
```
## Установка без Composer
Скачать [отсюда](https://php-download.com/package/gtarr/webp-parse-and-convert) и загрузить папку `vendor` на сайт
## Использование
1. Без доп. опций
```php
$content = '<html>...<img src="">...</html>';
$rootDir = $_SERVER['DOCUMENT_ROOT'];

require $rootDir . '/vendor/autoload.php';

use WebPParseAndConvert\WebPParseAndConvert;

$converter = new WebPParseAndConvert(  
   $content,  // HTML страницы
   $rootDir,  // корень сайта
// $options
);  

$content = $converter->execute();
```
2. C опциями (в примере значения по умолчанию)
```php
$options = [
   "formats" => [
         '.jpg', 
         '.jpeg',
         '.png' // на старом php расширении GD не работает, по умолчанию png обрабатывается
   ],
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
   "devices" => [
      'iphone',
      'ipod',
      'ipad',
      'macintosh',
      'mac os',
      'Edge',
      'MSIE'
   ]
];

$converter = new WebPParseAndConvert(  
   $content,
   $rootDir,
   $options
); 
```
## Опции
Имя           | Описание | Стандартное значение
:------------:|:--------:|:-------------------:
**'formats'** | форматы изображений, которые будут найдены на странице и конвертированы в webp |
**'patterns'** | регулярные выражения, по которым будет производиться поиск, имеет 2 параметра | 
'pattern' | само регулярное выражение |
'exclude' | подстроки, которые нужно исключить из обработки после нахождения строки по регулярному выражению, например, кавычки |
**'devices'** | список устройств, для которых будут выводиться оригинальные изображения в форматах jpg, jpeg, png | 

## Решение проблем
При ошибке `PNG file skipped. GD is configured not to convert PNGs` необходимо отключить обработку PNG изображений, для этого нужно в опции `'formats'` передать только `'.jpg.'` и `'.jpeg'`
## Примеры для CMS
* [1С-Битрикс](https://github.com/GTaRR/WebPParseAndConvert/wiki/1C-Bitrix)
* [ModX](https://github.com/GTaRR/WebPParseAndConvert/wiki/ModX)
