<?php
/***********************************************************************************************
 * rodzeta.siteoptions - Users site options
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class rodzeta_siteoptions extends CModule {

	var $MODULE_ID = "rodzeta.siteoptions"; // FIX for bitrix rules

	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $MODULE_GROUP_RIGHTS;
	public $PARTNER_NAME;
	public $PARTNER_URI;

	//public $MODULE_GROUP_RIGHTS = 'N';
	//public $NEED_MAIN_VERSION = '';
	//public $NEED_MODULES = array();

	function __construct() {
		$this->MODULE_ID = "rodzeta.siteoptions"; // NEED for showing module in /bitrix/admin/partner_modules.php?lang=ru

		$arModuleVersion = array();
		include __DIR__ . "/version.php";

		if (!empty($arModuleVersion["VERSION"])) {
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}

		$this->MODULE_NAME = Loc::getMessage("RODZETA_SITEOPTIONS_MODULE_NAME");
		$this->MODULE_DESCRIPTION = Loc::getMessage("RODZETA_SITEOPTIONS_MODULE_DESCRIPTION");
		$this->MODULE_GROUP_RIGHTS = "N";

		$this->PARTNER_NAME = "Rodzeta";
		$this->PARTNER_URI = "http://rodzeta.ru/";
	}

	function InstallFiles() {
		/*
    // copy example if not exists
		$fname = $_SERVER["DOCUMENT_ROOT"] . "/upload/" . $this->MODULE_ID . ".csv";
		if (!file_exists($fname)) {
			copy($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/examples/" . $this->MODULE_ID . ".csv", $fname);
		}
		*/
		return true;
	}

	function UnInstallFiles() {
		//...

		return true;
	}

	function DoInstall() {
		ModuleManager::registerModule($this->MODULE_ID);
		RegisterModuleDependences("main", "OnPageStart", $this->MODULE_ID);
		$this->InstallFiles();
	}

	function DoUninstall() {
		$this->UnInstallFiles();
		UnRegisterModuleDependences("main", "OnPageStart", $this->MODULE_ID);
		ModuleManager::unregisterModule($this->MODULE_ID);
	}

}
