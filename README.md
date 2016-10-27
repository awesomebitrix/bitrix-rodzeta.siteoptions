
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
        Номер телефона: <?= $GLOBALS["rodzeta.siteoptions"]["#PHONE#"] ?><br>
        Адрес: <?= $GLOBALS["rodzeta.siteoptions"]["#ADDRESS#"] ?><br>
        E-mail: <a href="mailto:<?= $GLOBALS["rodzeta.siteoptions"]["#EMAIL#"] ?>"><?= $GLOBALS["rodzeta.siteoptions"]["#EMAIL#"] ?></a>
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

### Пример подмены опции - номера телефона в зависимости от города

Добавить свой обработчик в php_interface/init.php или модуль
   
    use Bitrix\Main\EventManager;
    
    EventManager::getInstance()->addEventHandler("main", "OnProlog", function () {
        if (CSite::InDir("/bitrix/")) {
            return;
        }
        /* example data
        $_REQUEST["city"] = "Москва";
        $GLOBALS["RODZETA"]["DATA_BY_CITY"] = array(
            "Москва" => array(
                    "PHONE" => "1234567",
                    "MANAGER" => "Мэнеджер Имя Фамилия",
            )
        );
        */
        if (!empty($_REQUEST["city"]) &&
                !empty($GLOBALS["RODZETA"]["DATA_BY_CITY"][$_REQUEST["city"]])) {
            $content = $GLOBALS["RODZETA"]["DATA_BY_CITY"][$_REQUEST["city"]];
            if (!empty($content["PHONE"])) {
                $GLOBALS["rodzeta.siteoptions"]["#PHONE#"] = $content["PHONE"];
            }
        }
    });

## Описание техподдержки и контактных данных

Тех. поддержка и кастомизация оказывается на платной основе, e-mail: rivetweb@yandex.ru

Багрепорты и предложения на https://github.com/rivetweb/bitrix-rodzeta.siteoptions/issues

Пул реквесты на https://github.com/rivetweb/bitrix-rodzeta.siteoptions/pulls

## Ссылка на демо-версию

http://villa-mia.ru/
