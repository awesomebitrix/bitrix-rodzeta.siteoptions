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
//use Bitrix\Main\Loader;

require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";
//require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";

// TODO заменить на определение доступа к редактированию контента
// 	if (!$USER->CanDoOperation("rodzeta.siteoptions"))
if (!$GLOBALS["USER"]->IsAdmin()) {
	//$APPLICATION->authForm("ACCESS DENIED");
  return;
}

//Loader::includeModule("iblock");
//Loc::loadMessages(__FILE__); // так не грузит языковые файлы
Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . ID . "/admin/" . ID . "/index.php");

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();
list($currentHost, $currentUrl, $currentParams, $optionsKey, $defaultOptions) =
	UrlInfo($_SERVER["HTTP_REFERER"]);
$usedParams = array_merge($defaultOptions[1], [
	"utm_term" => true,
]);

StorageInit();

$formSaved = check_bitrix_sessid() && $request->isPost();
if ($formSaved) {
	//Option::set("rodzeta.site", "iblock_services", (int)$request->getPost("iblock_services"));

	Update($optionsKey, $request->getPostList(), Loc::getMessage("RODZETA_SITEOPTIONS_CATEGORY_SNIPPETS"));

	/*
	\CAdminMessage::showMessage([
    "MESSAGE" => Loc::getMessage("RODZETA_SITEOPTIONS_OPTIONS_SAVED"),
    "TYPE" => "OK",
  ]);
  */
}

list($currentOptions, $optionParams) = Select($optionsKey);

?>

<form action="" method="post">
	<?= bitrix_sessid_post() ?>

	<table width="100%">

		<tr>
			<td colspan="2">

				<?php if ($optionsKey == KEY_DEFAULT) { ?>

					<div class="adm-detail-title">Привязываемые параметры</div>

					<table width="100%" class="rodzeta-siteoptions-params-table js-table-autoappendrows">
						<tbody>
							<?php
							$i = 0;
							foreach (AppendValues($usedParams, 1, null) as $optionCode => $optionValue) {
								$i++;
								if (empty($optionValue)) {
									$optionCode = "";
								}
							?>
								<tr data-idx="<?= $i ?>">
									<td>
										<input type="text" placeholder="Код параметра"
											name="site_options_param[<?= $i ?>][CODE]"
											value="<?= htmlspecialcharsex($optionCode) ?>"
											style="width:98%;">
									</td>
								</tr>
							<?php } ?>

						</tbody>
					</table>

					<br>
					<div class="adm-detail-title">Опции</div>

					<table width="100%" class="rodzeta-siteoptions-table js-table-autoappendrows">
						<tbody>
							<?php
							$i = 0;
							foreach (AppendValues($currentOptions, 5, [true, null, null]) as $optionCode => $optionValue) {
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
										<input type="text" placeholder="Значение по умолчанию"
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

				<?php } else { ?>

					<table width="100%" class="rodzeta-siteoptions-table">
						<tbody>
							<?php
							$i = 0;
							foreach (array_merge($defaultOptions[0], $currentOptions) as $optionCode => $optionValue) {
								if (!isset($currentOptions[$optionCode])) {
									$defaultValue = $optionValue[1];
									$optionValue[1] = "";
								}
								$i++;
							?>
								<tr data-idx="<?= $i ?>">
									<td class="adm-detail-content-cell-l">
										<b><?= $defaultOptions[0][$optionCode][2] ?></b>
										<input type="hidden"
											name="site_options[<?= $i ?>][CODE]"
											value="<?= htmlspecialcharsex(substr($optionCode, 1, -1)) ?>">
									</td>
									<td class="adm-detail-content-cell-r">
										<input type="text" placeholder="<?= htmlspecialcharsex($defaultValue) ?>"
											name="site_options[<?= $i ?>][VALUE]"
											value="<?= htmlspecialcharsex($optionValue[1]) ?>"
											style="width:96%;">
									</td>
								</tr>
							<?php } ?>

						</tbody>
					</table>

				<?php } ?>

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

<?php if ($formSaved) { ?>

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


<?php /* // NOTE external script not work
<script src="<?= URL_ADMIN ?>/init.js"></script>
*/ ?>
<script>

BX.ready(function () {
	"use strict";

	// init options
	//...

	// autoappend rows
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
					for (let $input of $trLast.querySelectorAll("input,select")) {
						let name = $input.getAttribute("name");
						if (name) {
							$input.setAttribute("name", name.replace(/([a-zA-Z0-9])\[\d+\]/, "$1[" + idx + "]"));
						}
					}
					bindEvents($trLast);
				});
			}
		}
		for (let $row of document.querySelectorAll(".js-table-autoappendrows tr")) {
			bindEvents($row);
		}
	}
	for (let $table of document.querySelectorAll(".js-table-autoappendrows")) {
		makeAutoAppend($table);
	}

});

</script>