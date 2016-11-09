<?php
/*******************************************************************************
 * rodzeta.siteoptions - Users site options
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Siteoptions;

define(__NAMESPACE__ . "\APP", __DIR__ . "/");
define(__NAMESPACE__ . "\LIB", __DIR__  . "/lib/");
define(__NAMESPACE__ . "\FILE_OPTIONS", "/upload/.rodzeta.siteoptions.php");

require LIB . "encoding/php-array.php";

use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

function CreateCache($siteOptions, $snippetsCategory) {
	Loader::includeModule("iblock");

	$basePath = $_SERVER["DOCUMENT_ROOT"];
	$iblockId = Option::get("rodzeta.siteoptions", "iblock_id", 2);

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

	$options = [];
	foreach ($siteOptions as $v) {
		$v["CODE"] = trim($v["CODE"]);
		$v["VALUE"] = trim($v["VALUE"]);
		$v["NAME"] = trim($v["NAME"]);
		if ($v["CODE"] == "") {
			continue;
		}
		$options["#" . $v["CODE"] . "#"] = [true, $v["VALUE"], $v["NAME"]];
	}

	// create snippets
	$snippetsPath = $basePath .  "/bitrix/templates/.default/snippets";;
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

	\Encoding\PhpArray\Write($basePath . FILE_OPTIONS, $options);
}

function Options() {
	return include $_SERVER["DOCUMENT_ROOT"] . FILE_OPTIONS;
}

function AppendValues($data, $n, $v) {
	for ($i = 0; $i < $n; $i++) {
		$data[] = $v;
	}
	return $data;
}
