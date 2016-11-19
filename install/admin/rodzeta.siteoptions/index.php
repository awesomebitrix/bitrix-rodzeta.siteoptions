<?php
/*******************************************************************************
 * rodzeta.siteoptions - Users site options
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Siteoptions;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\IO\Path;

require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";
//require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";

// TODO заменить на определение доступа к редактированию конента
// 	if (!$USER->CanDoOperation("rodzeta.siteoptions"))
if (!$GLOBALS["USER"]->IsAdmin()) {
	//$APPLICATION->authForm("ACCESS DENIED");
  return;
}

Loader::includeModule("iblock");

//Loc::loadMessages(__FILE__); // так не грузит языковые файлы
Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . ID . "/admin/" . ID . "/index.php");

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

$formSaved = check_bitrix_sessid() && $_SERVER["REQUEST_METHOD"] == "POST";
if ($formSaved) {
	//Option::set("rodzeta.site", "iblock_services", (int)$request->getPost("iblock_services"));

	CreateCache($request->getPost("site_options"), Loc::getMessage("RODZETA_SITEOPTIONS_CATEGORY_SNIPPETS"));

	/*
	\CAdminMessage::showMessage([
    "MESSAGE" => Loc::getMessage("RODZETA_SITEOPTIONS_OPTIONS_SAVED"),
    "TYPE" => "OK",
  ]);
  */
}

?>

<form action="" method="post">
	<?= bitrix_sessid_post() ?>

	<table width="100%">

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

		<?php /* ?>

		<tr class="heading">
			<td colspan="2">Хранение пользовательских опций</td>
		</tr>

		<tr>
			<td class="adm-detail-content-cell-l" width="50%">
				<label>Инфоблок</label>
			</td>
			<td class="adm-detail-content-cell-r" width="50%">
				<?= GetIBlockDropDownListEx(
					Option::get("rodzeta.site", "iblock_services", 0),
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

		<?php */ ?>

	</table>

</form>

<?php if (!empty($formSaved)) { ?>

	<script>
		// close after submit
		top.BX.WindowManager.Get().AllowClose();
		top.BX.WindowManager.Get().Close();
	</script>

<?php } else { ?>

	<script>
		// add buttons for current windows
		BX.WindowManager.Get().SetButtons([
			BX.CDialog.prototype.btnSave,
			BX.CDialog.prototype.btnCancel
			//,BX.CDialog.prototype.btnClose
		]);
	</script>

<?php } ?>

<script>

// TODO use autoappendrows.js

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