<h1 align="center">🚀 Table</h1>

**table** - небольшая библиотека для работы с таблицами строк на PHP 7.4+

## Установка

```
composer require krypt0nn/table
```

## Пример работы

### Создание таблицы

```php
<?php

use Table\Table;

$table = new Table (['id', 'name'], [
    [0, 'Hello'],
    [1, 'from'],
    [2, 'Russia!']
]);
```

```php
<?php

use Table\Table;

$table = new Table;

$table->columns (['id', 'name'])->items ([
    [0, 'Hello'],
    [1, 'from'],
    [2, 'Russia!']
]);
```

### Получение заголовков, элементов и их количества:

```php
<?php

print_r ($table->columns ());

print_r ($table->items ());

echo $table->size ();
```

```
Array
(
    [0] => id
    [1] => name
)
```

```
Array
(
    [0] => Array
        (
            [0] => 0
            [1] => Hello
        )

    [1] => Array
        (
            [0] => 1
            [1] => from
        )

    [2] => Array
        (
            [0] => 2
            [1] => Russia!
        )

)
```

```
3
```

### Вывод части элементов:

```php
<?php

$table->foreach (function ($item)
{
    echo $item[1] .' ';
});
```

```
Hello from Russia!
```

### Фильтрация элементов:

```php
<?php

$table->where (function ($item)
{
    return $item[1] == 'hello';
});
```

### Вывод массива элементов:

```php
<?php

print_r ($table->get ());
```

```
Array
(
    [0] => Array
        (
            [id] => 0      
            [name] => Hello
        )

    [1] => Array
        (
            [id] => 1
            [name] => from
        )

    [2] => Array
        (
            [id] => 2
            [name] => Russia!
        )

)
```

### Добавление элементов:

```php
<?php

$table->push ([3, 'Alalalalala']);

$table->merge ([
    [4, 'Ololo'],
    [5. 'Olo lo'],
    [6, 'Lo']
]);
```

### Кодирование и декодирование таблицы:

```php
<?php

$table->delimiter = "\r\n";

file_put_contents ('table', $table->encode ());

$table = (new Table)->decode (file_get_contents ('table'));
```

table:

```
2
id
name
0
Hello
1
from
2
Russia!
```

Автор: [Подвирный Никита](https://vk.com/technomindlp)
