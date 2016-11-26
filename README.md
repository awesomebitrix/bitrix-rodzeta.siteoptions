﻿
# Модуль Пользовательские опции сайта

## Описание решения

Данный модуль позволяет быстро модифицировать и переносить основные данные - такие как: номер телефона, адрес, режим работы, ссылки на группы в соц. сетях и т.п. на всех страницах сайта (можно вставлять в любое место используемое для вывода на сайте: содержимое полей инфоблоков, содержимое страницы, файлы шаблонов и т.п.).

Так же модуль позволяет привязывать свой набор опций (например метатеги и заголовок страницы) для произвольного урл и выбранных параметров. Например модуль пригодится в случае использования ЧПУ-фильтра (т.к. нет возможности задать данные в разделе) или для задания контента по UTM-меткам (упрощенный аналог Yagla).

**Модуль поддерживает разделение данных на уровне доменов, без необходимости настройки многосайтовости и покупки лицензий на дополнительные сайты.**

## Описание установки и настройки решения

Основной список опций и параметров задается с главной страницы (например http://rodzeta.ru). Опции c заданными названиями так же будут доступны как снипеты в визуальном редакторе.

Значения опций для конкретной страницы и параметров задаются на странице с данным урл (например http://rodzeta.ru/?utm_term=Уголок Потребителя).

### Предустановленные опции сайта

    #CURRENT_YEAR# - текущий год 
    #CURRENT_MONTH# - текущий месяц 
    #CURRENT_DAY# - текущий день 
    #CURRENT_DATE# - текущая дата
    
### Пример использования в шаблонах, в выводе страницы или любых редактируемых текстовых или html полях 

```
<div>
    Номер телефона: #PHONE#<br>
    Адрес: #ADDRESS#<br>
    E-mail: <a href="#EMAIL#">#EMAIL#</a>
</div>
```

### Пример использования в php-коде

```    
<div>
    Номер телефона: <?= $GLOBALS["rodzeta.siteoptions"]["#PHONE#"] ?><br>
    Адрес: <?= $GLOBALS["rodzeta.siteoptions"]["#ADDRESS#"] ?><br>
    E-mail: <a href="mailto:<?= $GLOBALS["rodzeta.siteoptions"]["#EMAIL#"] ?>"><?= $GLOBALS["rodzeta.siteoptions"]["#EMAIL#"] ?></a>
</div>
```

### Пример переопределения опции на свое значение - например подмена номера телефона в зависимости от города

Добавить свой обработчик в php_interface/init.php или модуль

```
use Bitrix\Main\EventManager;

EventManager::getInstance()->addEventHandler("main", "OnProlog", function () {
    if (\CSite::InDir("/bitrix/")) {
        return;
    }

    /* example data
    $_COOKIE["city"] = "Москва";
    $GLOBALS["RODZETA"]["DATA_BY_CITY"] = [
        "Москва" => [
                "PHONE" => "1234567",
                "MANAGER" => "Мэнеджер Имя Фамилия",
        ]
    ];
    */

    if (!empty($_COOKIE["city"]) &&
            !empty($GLOBALS["RODZETA"]["DATA_BY_CITY"][$_COOKIE["city"]])) {
        $content = $GLOBALS["RODZETA"]["DATA_BY_CITY"][$_COOKIE["city"]];
        if (!empty($content["PHONE"])) {
            $GLOBALS["rodzeta.siteoptions"]["#PHONE#"] = $content["PHONE"];
        }
    }
});
```

## Описание техподдержки и контактных данных

Тех. поддержка и кастомизация оказывается на платной основе, e-mail: rivetweb@yandex.ru

Багрепорты и предложения на https://github.com/rivetweb/bitrix-rodzeta.siteoptions/issues

Пул реквесты на https://github.com/rivetweb/bitrix-rodzeta.siteoptions/pulls

## Ссылка на демо-версию

http://villa-mia.ru/
