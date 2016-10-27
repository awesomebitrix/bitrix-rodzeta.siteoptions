<?php
/***********************************************************************************************
 * rodzeta.siteoptions - Users site options
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\EventManager;

define("\Rodzeta\Siteoptions\_APP", __DIR__ . "/");
define("\Rodzeta\Siteoptions\_LIB", __DIR__  . "/lib/");
define("\Rodzeta\Siteoptions\_FILE_OPTIONS", "/upload/.rodzeta.siteoptions.php");
define("\Rodzeta\Siteoptions\_FILE_CSV", "/upload/.rodzeta.siteoptions.csv");

EventManager::getInstance()->addEventHandler("main", "OnBeforeProlog", function () {
	if (CSite::InDir("/bitrix/")) {
		return;
	}
	global $APPLICATION;
	$GLOBALS["RODZETA"]["SITE"] = \Rodzeta\Siteoptions\Utils::get();
});

EventManager::getInstance()->addEventHandler("main", "OnEndBufferContent", function (&$content) {
	if (CSite::InDir("/bitrix/")) {
		return;
	}
	global $APPLICATION;
	if ($APPLICATION->GetPublicShowMode() != "view") {
		return;
	}

	$options = &$GLOBALS["RODZETA"]["SITE"];

	// predefined site options
	$options["#CURRENT_YEAR#"] = date("Y");
	$options["#CURRENT_MONTH#"] = date("m");
	$options["#CURRENT_DAY#"] = date("d");
	$options["#CURRENT_DATE#"] = date("d.m.Y");

	$content = str_replace(array_keys($options), array_values($options), $content);
});