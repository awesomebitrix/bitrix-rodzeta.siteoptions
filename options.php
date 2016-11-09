<?php
/*******************************************************************************
 * rodzeta.siteoptions - Users site options
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Siteoptions;

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

if (!$USER->isAdmin()) {
	$APPLICATION->authForm("ACCESS DENIED");
}

Loader::includeModule("iblock");

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

Loc::loadMessages(__FILE__);

$tabControl = new \CAdminTabControl("tabControl", [
  [
		"DIV" => "edit2",
		"TAB" => Loc::getMessage("RODZETA_SITEOPTIONS_DATA_TAB_SET"),
		"TITLE" => Loc::getMessage("RODZETA_SITEOPTIONS_DATA_TAB_TITLE_SET", [
			"#FILE#" => FILE_OPTIONS
		]),
  ],
  [
		"DIV" => "edit1",
		"TAB" => Loc::getMessage("RODZETA_SITEOPTIONS_MAIN_TAB_SET"),
		"TITLE" => Loc::getMessage("RODZETA_SITEOPTIONS_MAIN_TAB_TITLE_SET"),
  ],
]);

?>

<?php

if ($request->isPost() && check_bitrix_sessid()) {
	if (!empty($save) || !empty($restore)) {
		Option::set("rodzeta.main", "iblock_services",
			(int)$request->getPost("iblock_services"));

		CreateCache($request->getPost("site_options"), Loc::getMessage("RODZETA_SITEOPTIONS_CATEGORY_SNIPPETS"));

		\CAdminMessage::showMessage([
	    "MESSAGE" => Loc::getMessage("RODZETA_SITEOPTIONS_OPTIONS_SAVED"),
	    "TYPE" => "OK",
	  ]);
	}
}

$tabControl->begin();

?>

<form method="post" action="<?= sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID) ?>" type="get">
	<?= bitrix_sessid_post() ?>

	<?php $tabControl->beginNextTab() ?>

	<tr>
		<td colspan="2">
			<table width="100%" class="rodzeta-siteoptions-table">
				<tbody>
					<?php
					$i = 0;
					foreach (AppendValues(Options(), 5, [true, null, null]) as $optionCode => $optionValue) {
						if (empty($optionValue[0])) {
							continue;
						}
						$i++;
					?>
						<tr data-idx="<?= $i ?>">
							<td>
								<input type="text" placeholder="Код опции"
									name="site_options[<?= $i ?>][CODE]"
									value="<?= htmlspecialcharsex(substr($optionCode, 1, -1)) ?>"
									style="width:96%;">
							</td>
							<td>
								<input type="text" placeholder="Значение опции"
									name="site_options[<?= $i ?>][VALUE]"
									value="<?= htmlspecialcharsex($optionValue[1]) ?>"
									style="width:96%;">
							</td>
							<td>
								<input type="text" placeholder="Название"
									name="site_options[<?= $i ?>][NAME]"
									value="<?= htmlspecialcharsex($optionValue[2]) ?>"
									style="width:96%;">
							</td>
						</tr>
					<?php } ?>

				</tbody>
			</table>
		</td>
	</tr>

	<?php $tabControl->beginNextTab() ?>

	<tr class="heading">
		<td colspan="2">Хранение пользовательских опций</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Инфоблок</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<?= GetIBlockDropDownListEx(
				Option::get("rodzeta.main", "iblock_services", 0),
				"iblock_type_id",
				"iblock_services",
				[
					"MIN_PERMISSION" => "R",
				],
				"",
				""
			) ?>
		</td>
	</tr>

	<?php
	 $tabControl->buttons();
  ?>

  <input class="adm-btn-save" type="submit" name="save" value="Применить настройки">

</form>

<script>

BX.ready(function () {
	"use strict";

	function makeAutoAppend($table) {

		function bindEvents($row) {
			for (let $input of $row.querySelectorAll('input[type="text"]')) {
				$input.addEventListener("change", function (event) {
					let $tr = event.target.closest("tr");
					let $trLast = $table.rows[$table.rows.length - 1];
					if ($tr != $trLast) {
						return;
					}
					$table.insertRow(-1);
					$trLast = $table.rows[$table.rows.length - 1];
					$trLast.innerHTML = $tr.innerHTML;
					let idx = parseInt($tr.getAttribute("data-idx")) + 1;
					$trLast.setAttribute("data-idx", idx);
					for (let $input of $trLast.querySelectorAll('input[type="text"]')) {
						$input.setAttribute("name", $input.getAttribute("name").replace(/([a-zA-Z0-9])\[\d+\]/, "$1[" + idx + "]"));
					}
					bindEvents($trLast);
				});
			}
		}

		for (let $row of document.querySelectorAll(".rodzeta-siteoptions-table tr")) {
			bindEvents($row);
		}
	}

	makeAutoAppend(document.querySelector(".rodzeta-siteoptions-table"));

});


</script>

<?php

$tabControl->end();
