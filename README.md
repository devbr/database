# Simple database node

[![Latest Stable Version](https://poser.pugx.org/devbr/website/v/stable)](https://packagist.org/packages/devbr/database)
[![Latest Unstable Version](https://poser.pugx.org/devbr/website/v/unstable)](https://packagist.org/packages/devbr/database)
[![License](https://poser.pugx.org/devbr/website/license)](https://packagist.org/packages/devbr/database)
[![Total Downloads](https://poser.pugx.org/devbr/website/downloads)](https://packagist.org/packages/devbr/database)
[![Monthly Downloads](https://poser.pugx.org/devbr/website/d/monthly)](https://packagist.org/packages/devbr/database)

To install:

```shell
Composer require devbr/database
```

## Uso

Uma simples utilização do Database:

```php
$cfg = [];

$db = new Devbr\Database($cfg);
print_r($db->query('SELECT * FROM TABELA'));
```

Se você estiver usando o 'devbr/website' como base do seu projeto é possível extender a configuração na classe 'Config\Database', ficando mais simples o uso:

```php
$db = new Devbr\Database;

$db->query('SELECT * FROM TABELA');

print_r($db->result());

//Passando arqumentos:
$args = [':id'=>23];

$db->query('SELECT * FROM TABELA WHERE ID=:id', $args);

print_r($db->result());
```
