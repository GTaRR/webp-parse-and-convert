# WebPParseAndConvert
Конвертирование изображений в WebP через парсинг переданного HTML страницы.
## Установка через Composer
```php
composer require gtarr/webp-parse-and-convert
```
## Установка без Composer
https://php-download.com/package/gtarr/webp-parse-and-convert
## Использование
```php
$rootDir = $_SERVER['DOCUMENT_ROOT'];

require $rootDir . '/vendor/autoload.php';

use WebPParseAndConvert\WebPParseAndConvert;

$options = [
   "formats" => [  
      '.jpg', '.jpeg',  
      //'.png' // со старым php-расширением GD не работает  
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
   ] 
];

$converter = new WebPParseAndConvert(  
   $content,  // HTML страницы
   $rootDir,  // корень сайта
   $options   // необязательные параметры
);  

$content = $converter->execute();
```
