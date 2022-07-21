<?php
	spl_autoload_extensions('.php');
	spl_autoload_register('load');

	function load($className) {
		$extension = spl_autoload_extensions();
		if (is_file($_SERVER["DOCUMENT_ROOT"] . "/" . str_replace('\\', '/', $className . $extension))) {
			require_once($_SERVER["DOCUMENT_ROOT"] . "/" .  str_replace('\\', '/', $className . $extension));
		} else {
			// Auto load das bibliotecas
			if (is_file($_SERVER["DOCUMENT_ROOT"] . "/libraries/". str_replace('\\', '/', $className . $extension))) {
				require_once($_SERVER["DOCUMENT_ROOT"] . "/libraries/" . str_replace('\\', '/', $className . $extension));
			}
		}
	}
