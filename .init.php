<?php
/***********************************************************************************************
 * rodzeta.siteoptions - Users site options
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

namespace Rodzeta\Siteoptions;

define(__NAMESPACE__ . "\_APP", __DIR__ . "/");
define(__NAMESPACE__ . "\_LIB", __DIR__  . "/lib/");
define(__NAMESPACE__ . "\_FILE_OPTIONS", "/upload/.rodzeta.siteoptions.php");
define(__NAMESPACE__ . "\_FILE_OPTIONS_CSV", "/upload/.rodzeta.siteoptions.csv");

use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

function OptionsFromCsv() {
	$basePath = $_SERVER["DOCUMENT_ROOT"];
	$options = array();
	$fcsv = fopen($basePath . _FILE_OPTIONS_CSV, "r");
	if ($fcsv === false) {
		return $options;
	}
	//$i = 0;
	while (($row = fgetcsv($fcsv, 4000, "\t")) !== false) {
		//$i++;
		//if ($i == 1) {
		//	continue;
		//}
		$row = array_map("trim", $row);
		if ($row[0] == "") {
			continue;
		}
		$options[$row[0]] = $row[1];
	}
	fclose($fcsv);
	return $options;
}

function SaveToCsv($options) {
	$basePath = $_SERVER["DOCUMENT_ROOT"];
	$fcsv = fopen($basePath . _FILE_OPTIONS_CSV, "w");
	if ($fcsv === false) {
		return;
	}
	foreach ($options as $row) {
		$row[0] = trim($row[0]);
		$row[1] = trim($row[1]);
		if ($row[0] == "" || $row[1] == "") {
			continue;
		}
		fputcsv($fcsv, array($row[0], $row[1]), "\t");
	}
	fclose($fcsv);
}

function CreateCache() {
	Loader::includeModule("iblock");

	$basePath = $_SERVER["DOCUMENT_ROOT"];
	$options = array();
	foreach (OptionsFromCsv() as $k => $v) {
		$options["#" . $k . "#"] = $v;
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
				$options["#" . $item["CODE"] . "_" . $code . "#"] = $item[$code];
			}
			foreach (array("PREVIEW_PICTURE", "DETAIL_PICTURE") as $code) {
				$img = \CFile::GetFileArray($item[$code]);
				$options["#" . $item["CODE"] . "_" . $code . "_SRC" . "#"] = $img["SRC"];
				$options["#" . $item["CODE"] . "_" . $code . "_DESCRIPTION" . "#"] = $img["DESCRIPTION"];
				$options["#" . $item["CODE"] . "_" . $code . "#"] =
					'<img src="' . $img["SRC"] . '" alt="' . htmlspecialchars($img["DESCRIPTION"]) . '">';
			}
		}
	}

	file_put_contents(
		$basePath . _FILE_OPTIONS,
		"<?php\nreturn " . var_export($options, true) . ";"
	);
}

function Options() {
	return include $_SERVER["DOCUMENT_ROOT"] . _FILE_OPTIONS;
}
