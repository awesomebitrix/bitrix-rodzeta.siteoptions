<?php
/***********************************************************************************************
 * rodzeta.siteoptions - Users site options
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;

if (!$USER->isAdmin()) {
	$APPLICATION->authForm("ACCESS DENIED");
}

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

Loc::loadMessages(__FILE__);

$tabControl = new CAdminTabControl("tabControl", array(
  array(
		"DIV" => "edit1",
		"TAB" => Loc::getMessage("RODZETA_SITEOPTIONS_MAIN_TAB_SET"),
		"TITLE" => Loc::getMessage("RODZETA_SITEOPTIONS_MAIN_TAB_TITLE_SET"),
  ),
  array(
		"DIV" => "edit2",
		"TAB" => Loc::getMessage("RODZETA_SITEOPTIONS_DATA_TAB_SET"),
		"TITLE" => Loc::getMessage("RODZETA_SITEOPTIONS_DATA_TAB_TITLE_SET", array(
			"#FILE#" => \Rodzeta\Siteoptions\_FILE_OPTIONS_CSV)
		),
  ),
));

?>

<?php /*
<?= BeginNote() ?>
<p>
	<b>Как работает</b>
	<ul>
		<li>загрузите или создайте файл <b><a href="<?= \Rodzeta\Siteoptions\Utils::SRC_NAME ?>">rodzeta.siteoptions.csv</a></b> в папке /upload/ с помощью
			<a target="_blank" href="/bitrix/admin/fileman_file_edit.php?path=<?=
					urlencode(\Rodzeta\Siteoptions\Utils::SRC_NAME) ?>">стандартного файлового менеджера</a>;
		<li>после изменений в файле rodzeta.siteoptions.csv или редактирования элементов в заданном разделе опций - нажмите в настройке модуля кнопку "Применить настройки";
	</ul>
</p>
<?= EndNote() ?>
*/ ?>

<?php

if ($request->isPost() && check_bitrix_sessid()) {
	if (!empty($save) || !empty($restore)) {
		Option::set("rodzeta.siteoptions", "iblock_id", (int)$request->getPost("iblock_id"));
		Option::set("rodzeta.siteoptions", "section_code", $request->getPost("section_code"));

		\Rodzeta\Siteoptions\SaveToCsv($request->getPost("site_options"));
		\Rodzeta\Siteoptions\CreateCache();

		CAdminMessage::showMessage(array(
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

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Код раздела</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="section_code" type="text" value="<?= Option::get("rodzeta.siteoptions", "section_code", "RODZETA_SITE") ?>" disabled>
			<input name="section_code" type="hidden" value="RODZETA_SITE">
		</td>
	</tr>

	<?php $tabControl->beginNextTab() ?>

	<tr>
		<td colspan="2">
			<table width="100%">
				<tbody>
					<?php
					$i = 0;
					foreach (\Rodzeta\Siteoptions\OptionsFromCsv() as $optionCode => $optionValue) {
						$i++;
					?>
						<tr>
							<td>
								<input type="text" placeholder="Код опции"
									name="site_options[<?= $i ?>][0]"
									value="<?= htmlspecialcharsex($optionCode) ?>"
									style="width:96%;">
							</td>
							<td>
								<input type="text" placeholder="Значение опции"
									name="site_options[<?= $i ?>][1]"
									value="<?= htmlspecialcharsex($optionValue) ?>"
									style="width:96%;">
							</td>
						</tr>
					<?php } ?>

					<?php foreach (range(1, 20) as $n) {
						$i++;
					?>
						<tr>
							<td>
								<input type="text" placeholder="Код опции"
									name="site_options[<?= $i ?>][0]"
									value=""
									style="width:96%;">
							</td>
							<td>
								<input type="text" placeholder="Значение опции"
									name="site_options[<?= $i ?>][1]"
									value=""
									style="width:96%;">
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</td>
	</tr>

	<?php
	 $tabControl->buttons();
  ?>

  <input class="adm-btn-save" type="submit" name="save" value="Применить настройки">

</form>

<?php

$tabControl->end();
