<?php
/***********************************************************************************************
 * rodzeta.siteoptions - Users site options
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

namespace Rodzeta\Siteoptions;

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

require __DIR__ . "/.init.php";

use Bitrix\Main\EventManager;

EventManager::getInstance()->addEventHandler("main", "OnBeforeProlog", function () {
	if (\CSite::InDir("/bitrix/")) {
		return;
	}
	$GLOBALS["rodzeta.siteoptions"] = Options();
});

EventManager::getInstance()->addEventHandler("main", "OnEndBufferContent", function (&$content) {
	if (\CSite::InDir("/bitrix/")) {
		return;
	}
	global $APPLICATION;
	if ($APPLICATION->GetPublicShowMode() != "view") {
		return;
	}

	// predefined site options
	$GLOBALS["rodzeta.siteoptions"]["#CURRENT_YEAR#"] = [false, date("Y"), ""];
	$GLOBALS["rodzeta.siteoptions"]["#CURRENT_MONTH#"] = [false, date("m"), ""];
	$GLOBALS["rodzeta.siteoptions"]["#CURRENT_DAY#"] = [false, date("d"), ""];
	$GLOBALS["rodzeta.siteoptions"]["#CURRENT_DATE#"] = [false, date("d.m.Y"), ""];

	// replace options in page content
	$content = str_replace(
		array_keys($GLOBALS["rodzeta.siteoptions"]),
		array_map(
			function ($v) {
				return $v[1];
			},
			array_values($GLOBALS["rodzeta.siteoptions"])
		),
		$content
	);
});