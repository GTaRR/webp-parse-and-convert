# WebPParseAndConvert
Конвертирование изображений в WebP через парсинг переданного HTML страницы.
## Использование
```php
$rootDir = $_SERVER['DOCUMENT_ROOT'];
$options = array(
   "formats" => [  
      '.jpg', '.jpeg',  
      //'.png' // со старым php-расширением GD не работает  
   ],  
   "patterns" => [  
      [  
         'pattern' => '<img[^>]+src=("[^"]*")[^>]+>',
         'exclude' => array('"', './')  
      ],  
      [  
         'pattern' => '/background-image:.+url\(([^"]+)\)/i',
         'exclude' => array("'", "./")  
      ],  
   ] 
);

$converter = new WebPParseAndConvert(  
   $content,  // контент страницы
   $rootDir,  // корень сайта
   // $options // необязательные параметры
);  

$content = $converter->execute();
```
