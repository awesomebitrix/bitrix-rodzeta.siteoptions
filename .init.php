<?php
/*******************************************************************************
 * rodzeta.siteoptions - Users site options
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Siteoptions;

define(__NAMESPACE__ . "\ID", "rodzeta.siteoptions");
define(__NAMESPACE__ . "\URL_ADMIN", "/bitrix/admin/" . ID . "/");
define(__NAMESPACE__ . "\APP", __DIR__ . "/");
define(__NAMESPACE__ . "\LIB", __DIR__  . "/lib/");
define(__NAMESPACE__ . "\FILE_OPTIONS", $_SERVER["DOCUMENT_ROOT"] . "/upload/" . $_SERVER["SERVER_NAME"] . "/." . ID);
define(__NAMESPACE__ . "\KEY_DEFAULT", "default");

require LIB . "encoding/php-array.php";

//use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

function CurrentUrl() {
	$currentUrl = parse_url($_SERVER["HTTP_REFERER"]);
	parse_str($currentUrl["query"], $params);
	$default = Select(KEY_DEFAULT);
	if ($currentUrl["path"] == "/" && empty($params)) {
		$key = KEY_DEFAULT;
	} else {
		// reset not defined params
		foreach ($params as $k => $v) {
			if (empty($default[1][$k])) {
				unset($params[$k]);
			}
		}
		ksort($params);
		$key = sha1($currentUrl["path"] . "?" . http_build_query($params));
	}
	return [$_SERVER["SERVER_NAME"], $currentUrl["path"], $params, $key, $default];
}

function StorageInit() {
	if (!is_dir(FILE_OPTIONS)) {
		mkdir(FILE_OPTIONS, 0700, true);
	}
}

function Update($key, $data, $snippetsCategory) {
	/*
	$iblockId = Option::get("rodzeta.site", "iblock_services", 0);
	if ((int)$iblockId == 0) {
		return;
	}

	Loader::includeModule("iblock");

	// create section RODZETA_SITE
	$res = \CIBlockSection::GetList(
		["SORT" => "ASC"],
		[
			"IBLOCK_ID" => $iblockId,
			"CODE" => "RODZETA_SITE",
		],
		true,
		["*"]
	);
	$sectionOptions = $res->GetNext();
	if (empty($sectionOptions["ID"])) {
		$iblockSection = new \CIBlockSection();
		$mainSectionId = $iblockSection->Add([
		  "IBLOCK_ID" => $iblockId,
		  "NAME" => "Пользовательские опции сайта",
		  "CODE" => "RODZETA_SITE",
		  "SORT" => 20000,
			"ACTIVE" => "Y",
	  ]);
	  if (!empty($mainSectionId)) {
	  	Option::set("rodzeta.siteoptions", "section_id", $mainSectionId);
	  }
	} else {
		$mainSectionId = $sectionOptions["ID"];
	}
	*/

	$options = [];
	if (!empty($data["site_options"])) {
		foreach ($data["site_options"] as $v) {
			$v["CODE"] = trim($v["CODE"]);
			$v["VALUE"] = trim($v["VALUE"]);
			$v["NAME"] = trim($v["NAME"]);
			if ($v["CODE"] == "") {
				continue;
			}
			if ($key != KEY_DEFAULT) {
				if ($v["VALUE"] == "") {
					continue;
				}
			}
			$options["#" . $v["CODE"] . "#"] = [true, $v["VALUE"], $v["NAME"]];
		}
	}

	$optionsParam = [];
	if (!empty($data["site_options_param"])) {
		foreach ($data["site_options_param"] as $v) {
			$v["CODE"] = trim($v["CODE"]);
			if ($v["CODE"] == "") {
				continue;
			}
			$optionsParam[$v["CODE"]] = true;
		}
	}

	// TODO snippets
	/*
	// create snippets
	$snippetsPath = $_SERVER["DOCUMENT_ROOT"] .  "/bitrix/templates/.default/snippets";;
	if (!is_dir($snippetsPath)) {
		mkdir($snippetsPath);
	}
	$snippetsCategoryPath = $snippetsPath . "/" . $snippetsCategory;
	if (!is_dir($snippetsCategoryPath)) {
		mkdir($snippetsCategoryPath);
	}
	if (is_dir($snippetsCategoryPath)) {
		$SNIPPETS = [];
		// read existing snippets to $SNIPPETS array
		if (file_exists($snippetsPath . "/.content.php")) {
			include $snippetsPath . "/.content.php";
		}
		foreach (array_merge($options, [
				"#CURRENT_YEAR#" => [false, "#CURRENT_YEAR#", "Текущий год"],
				"#CURRENT_MONTH#" => [false, "#CURRENT_MONTH#", "Текущий месяц"],
				"#CURRENT_DAY#" => [false, "#CURRENT_DAY#", "Текущий день"],
				"#CURRENT_DATE#" => [false, "#CURRENT_DATE#", "Текущая дата"],
			]) as $snippetContent => $snippetInfo) {
			if ($snippetInfo[2] == "") {
				continue;
			}
			$snippetFile = "snippet" . substr($snippetContent, 1, -1) . ".snp";
			$SNIPPETS[$snippetsCategory . "/" . $snippetFile] = ["title" => $snippetInfo[2]];
			file_put_contents($snippetsCategoryPath . "/" . $snippetFile, $snippetContent);
		}
		ksort($SNIPPETS);
		file_put_contents($snippetsPath . "/.content.php", '<?php
			if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
			$SNIPPETS = ' . var_export($SNIPPETS, true) . ';'
		);
	}
	*/

	/*
	// init from infoblock section
	$res = \CIBlockElement::GetList(
		["SORT" => "ASC"],
		[
			"IBLOCK_ID" => $iblockId,
			"SECTION_ID" => $mainSectionId,
			"ACTIVE" => "Y"
		],
		false,
		false,
		[] // fields
	);
	while ($row = $res->GetNextElement()) {
		$item = $row->GetFields();
		foreach (["NAME", "PREVIEW_TEXT", "DETAIL_TEXT"] as $code) {
			$options["#" . $item["CODE"] . "_" . $code . "#"] = [false, $item[$code], ""];
		}
		foreach (["PREVIEW_PICTURE", "DETAIL_PICTURE"] as $code) {
			$img = \CFile::GetFileArray($item[$code]);
			$options["#" . $item["CODE"] . "_" . $code . "_SRC" . "#"] =
				[false, $img["SRC"], ""];
			$options["#" . $item["CODE"] . "_" . $code . "_DESCRIPTION" . "#"] =
				[false, $img["DESCRIPTION"], ""];
			$options["#" . $item["CODE"] . "_" . $code . "#"] =
				[false, '<img src="' . $img["SRC"] . '" alt="'
						. htmlspecialchars($img["DESCRIPTION"]) . '">', ""];
		}
	}
	*/

	\Encoding\PhpArray\Write(FILE_OPTIONS . "/" . $key . ".php", [
		$options,
		$optionsParam
	]);
}

function Select($key) {
	$fname = FILE_OPTIONS . "/" . $key . ".php";
	return is_readable($fname)? include $fname : [[], []];
}

function AppendValues($data, $n, $v) {
	for ($i = 0; $i < $n; $i++) {
		$data[] = $v;
	}
	return $data;
}
