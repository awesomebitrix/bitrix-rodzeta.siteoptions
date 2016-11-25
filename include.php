<?php
/*******************************************************************************
 * rodzeta.siteoptions - Users site options
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Siteoptions;

defined("B_PROLOG_INCLUDED") and (B_PROLOG_INCLUDED === true) or die();

require __DIR__ . "/.init.php";

use Bitrix\Main\EventManager;

EventManager::getInstance()->addEventHandler("main", "OnPanelCreate", function () {
	// TODO заменить на определение доступа к редактированию конента
	if (!$GLOBALS["USER"]->IsAdmin()) {
	  return;
	}

	$link = "javascript:" . $GLOBALS["APPLICATION"]->GetPopupLink([
		"URL" => URL_ADMIN,
		"PARAMS" => [
			"resizable" => true,
			//"width" => 780,
			//"height" => 570,
			//"min_width" => 400,
			//"min_height" => 200,
			"buttons" => "[BX.CDialog.prototype.btnClose]"
		]
	]);
  $GLOBALS["APPLICATION"]->AddPanelButton([
		"HREF" => $link,
		"ICON"  => "bx-panel-site-structure-icon",
		//"SRC" => URL_ADMIN . "/icon.gif",
		"TEXT"  => "Опции сайта",
		"ALT" => "Опции сайта",
		"MAIN_SORT" => 2000,
		"SORT"      => 10
	]);
});

EventManager::getInstance()->addEventHandler("main", "OnBeforeProlog", function () {
	if (\CSite::InDir("/bitrix/")) {
		return;
	}
	list($currentHost, $currentUrl, $currentParams, $optionsKey, $defaultOptions) =
		UrlInfo($_SERVER["REQUEST_URI"]);
	foreach (Select($optionsKey)[0] as $k => $v) {
		// set value from current url options
		$defaultOptions[0][$k][1] = $v[1];
	}
	$GLOBALS["rodzeta.siteoptions"] = $defaultOptions[0];
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