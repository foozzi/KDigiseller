# KDigiseller
Digiseller module for Kohana 3.3 (API Wrapper)

## Установка
Создать каталог в modules с именем digiseller, далее:

``
git clone git@github.com:foozzi/KDigiseller.git .
``

В application/bootstrap.php прописать модуль:

``
'digiseller' => MODPATH.'digiseller'
``

Модуль установлен!
## Использование 

```php
$Digiseller = Digiseller::factory();
$config = Kohana::$config->load('digiseller')->as_array(); 
$sign = $Digiseller->sign($_GET['uniquecode']);
$xml_data = $Digiseller->answer_check_code($config['id_seller'], $uniquecode, $sign); 
var_dump($xml_data);
```
