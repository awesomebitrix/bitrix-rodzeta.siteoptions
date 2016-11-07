<?php
/***********************************************************************************************
 * rodzeta.siteoptions - Users site options
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

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

$tabControl = new \CAdminTabControl("tabControl", array(
  array(
		"DIV" => "edit2",
		"TAB" => Loc::getMessage("RODZETA_SITEOPTIONS_DATA_TAB_SET"),
		"TITLE" => Loc::getMessage("RODZETA_SITEOPTIONS_DATA_TAB_TITLE_SET", array(
			"#FILE#" => FILE_OPTIONS
		)),
  ),
  array(
		"DIV" => "edit1",
		"TAB" => Loc::getMessage("RODZETA_SITEOPTIONS_MAIN_TAB_SET"),
		"TITLE" => Loc::getMessage("RODZETA_SITEOPTIONS_MAIN_TAB_TITLE_SET"),
  ),
));

?>

<?php

if ($request->isPost() && check_bitrix_sessid()) {
	if (!empty($save) || !empty($restore)) {
		Option::set("rodzeta.siteoptions", "iblock_id", (int)$request->getPost("iblock_id"));

		CreateCache($request->getPost("site_options"));

		\CAdminMessage::showMessage(array(
	    "MESSAGE" => Loc::getMessage("RODZETA_SITEOPTIONS_OPTIONS_SAVED"),
	    "TYPE" => "OK",
	  ));
	}	/*else if ($request->getPost("clear") != "") {


		CAdminMessage::showMessage(array(
	    "MESSAGE" => Loc::getMessage("RODZETA_SITEOPTIONS_OPTIONS_RESETED"),
	    "TYPE" => "OK",
	  ));
	} */
}

$tabControl->begin();

?>

<form method="post" action="<?= sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID) ?>" type="get">
	<?= bitrix_sessid_post() ?>

	<?php $tabControl->beginNextTab() ?>

	<tr>
		<td colspan="2">
			<table width="100%">
				<tbody>
					<?php
					$i = 0;
					foreach (AppendValues(Options(), 10, array(false, null, null)) as $optionCode => $optionValue) {
						$i++;
						if (empty($optionValue[0])) {
							continue;
						}
					?>
						<tr>
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
		<td colspan="2">Настройки для пользовательских опций из инфоблока</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Инфоблок</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<?= GetIBlockDropDownListEx(
				Option::get("rodzeta.siteoptions", "iblock_id", 1),
				"iblock_type_id",
				"iblock_id",
				array(
					"MIN_PERMISSION" => "R",
				),
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

<?php

$tabControl->end();
