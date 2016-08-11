﻿
# Модуль Пользовательские опции сайта

## Описание решения

Данный модуль позволяет быстро модифицировать и переносить основные данные - такие как: номер телефона, адрес, режим работы, ссылки на группы в соц. сетях и т.п. на всех страницах сайта. 

### Особенности

- можно вставлять в любое место используемое для вывода на сайте: содержимое полей инфоблоков, содержимое страницы, файлы шаблонов и т.п.;
- все данные хранятся в одном месте - в файле csv или инфоблоке, что удобно для обмена и редактирования и версионирования;
- не создает нагрузку на БД, все значения опций кешируются в php-файле, что позволяет использовать opcache.

## Описание установки и настройки решения

- загрузите или создайте файл rodzeta.siteoptions.csv в папке /upload/ с помощью стандартного файлового менеджера Bitrix или по FTP;
- формат файла: две колонки - "код", "значение", разделитель табуляция;
- дополнительно, в настройках возможно задать инфоблок и раздел в котором хранятся опции (актуально для хранения значений html и изображений);
- после изменений в файле rodzeta.siteoptions.csv или редактирования элементов в заданном разделе опций - нажмите в настройке модуля кнопку "Применить настройки";
- замена значений в выводе страницы производится по событию OnEndBufferContent.

### Предустановленные опции сайта

    #CURRENT_YEAR# - текущий год 
    #CURRENT_MONTH# - текущий месяц 
    #CURRENT_DAY# - текущий день 
    #CURRENT_DATE# - текущая дата
    
### Пример использования в шаблонах, в выводе страницы или любых редактируемых текстовых или html полях 

    <div>
        Номер телефона: #PHONE#<br>
        Адрес: #ADDRESS#<br>
        E-mail: <a href="#EMAIL#">#EMAIL#</a>
    </div>

### Пример использования в php-коде
    
    <div>
        Номер телефона: <?= $GLOBALS["RODZETA"]["SITE"]["#PHONE#"] ?><br>
        Адрес: <?= $GLOBALS["RODZETA"]["SITE"]["#ADDRESS#"] ?><br>
        E-mail: <a href="mailto:<?= $GLOBALS["RODZETA"]["SITE"]["#EMAIL#"] ?>"><?= $GLOBALS["RODZETA"]["SITE"]["#EMAIL#"] ?></a>
    </div>

### Пример опций из инфоблока

Для элемента инфоблока с кодом HOME создаются опции, которые можно использовать для хранения заданного типа значений:

    #HOME_NAME#
    #HOME_PREVIEW_TEXT#
    #HOME_DETAIL_TEXT#
    #HOME_PREVIEW_PICTURE_SRC#
    #HOME_PREVIEW_PICTURE_DESCRIPTION#
    #HOME_PREVIEW_PICTURE#
    #HOME_DETAIL_PICTURE_SRC#
    #HOME_DETAIL_PICTURE_DESCRIPTION#
    #HOME_DETAIL_PICTURE#

## Описание техподдержки и контактных данных

Тех. поддержка и кастомизация оказывается на платной основе, e-mail: rivetweb@yandex.ru

Багрепорты и предложения на https://github.com/rivetweb/bitrix-rodzeta.siteoptions/issues

Пул реквесты на https://github.com/rivetweb/bitrix-rodzeta.siteoptions/pulls

## Ссылка на демо-версию

http://villa-mia.ru/
