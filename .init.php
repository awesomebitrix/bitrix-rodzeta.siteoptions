<?php
/***********************************************************************************************
 * rodzeta.siteoptions - Users site options
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

namespace Rodzeta\Siteoptions;

define(__NAMESPACE__ . "\APP", __DIR__ . "/");
define(__NAMESPACE__ . "\LIB", __DIR__  . "/lib/");
define(__NAMESPACE__ . "\FILE_OPTIONS", "/upload/.rodzeta.siteoptions.php");

require LIB . "encoding/php-array.php";

use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

function CreateCache($siteOptions) {
	Loader::includeModule("iblock");

	$basePath = $_SERVER["DOCUMENT_ROOT"];
	$options = array();
	foreach ($siteOptions as $v) {
		$options["#" . $v["CODE"] . "#"] = array($v["MAIN"], $v["VALUE"], $v["NAME"]);
	}

	// init from infoblock section
	$sectionCode = Option::get("rodzeta.siteoptions", "section_code", "RODZETA_SITE");
	if ($sectionCode != "") {
		$res = \CIBlockElement::GetList(
			array("SORT" => "ASC"),
			array(
				"IBLOCK_ID" => Option::get("rodzeta.siteoptions", "iblock_id", 1),
				"SECTION_CODE" => $sectionCode,
				"ACTIVE" => "Y"
			),
			false,
			false,
			array() // fields
		);
		while ($row = $res->GetNextElement()) {
			$item = $row->GetFields();
			foreach (array("NAME", "PREVIEW_TEXT", "DETAIL_TEXT") as $code) {
				$options["#" . $item["CODE"] . "_" . $code . "#"] = array(false, $item[$code], "");
			}
			foreach (array("PREVIEW_PICTURE", "DETAIL_PICTURE") as $code) {
				$img = \CFile::GetFileArray($item[$code]);
				$options["#" . $item["CODE"] . "_" . $code . "_SRC" . "#"] =
					array(false, $img["SRC"], "");
				$options["#" . $item["CODE"] . "_" . $code . "_DESCRIPTION" . "#"] =
					array(false, $img["DESCRIPTION"], "");
				$options["#" . $item["CODE"] . "_" . $code . "#"] =
					array(false, '<img src="' . $img["SRC"] . '" alt="'
							. htmlspecialchars($img["DESCRIPTION"]) . '">', "");
			}
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
