# WebPParseAndConvert
Конвертирование изображений в WebP через парсинг переданного HTML страницы.
## Установка
```composer require gtarr/webp-parse-and-convert```
## Использование
```php
$rootDir = $_SERVER['DOCUMENT_ROOT'];
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
   $content,  // контент страницы
   $rootDir,  // корень сайта
   $options   // необязательные параметры
);  

$content = $converter->execute();
```
