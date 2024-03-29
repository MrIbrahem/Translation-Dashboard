<?php
//---
if (isset($_POST['del'])) {
	for($i = 0; $i < count($_POST['del']); $i++ ) {
		$del	= $_POST['del'][$i];
		//---
		if ($del != '') {
			$qua2 = "DELETE FROM categories WHERE id = ?";
			execute_query($qua2, [$del]);
		};
	};
};
//---
$default_cat = $_POST['default_cat'] ?? '';
//---
if (isset($_POST['cats'])) {
	for($i = 0; $i < count($_POST['cats']); $i++ ){
		$cats= $_POST['cats'][$i];
		$dis = $_POST['dis'][$i];
		$ido = $_POST['id'][$i];
		$ido = (isset($ido)) ? $ido : '';
		$dep = $_POST['dep'][$i];
		//---
		// $def = $_POST['def'][$i];
		$def = ($default_cat == $cats) ? 1 : 0;
		//---
		$qua = "INSERT INTO categories (category, display, depth, def) SELECT ?, ?, ?, ?
		WHERE NOT EXISTS (SELECT 1 FROM categories WHERE category = ?)";
		$params = [$cats, $dis, $dep, $def, $cats];
		//---
		if ($ido != '') {
			$qua = "UPDATE categories 
			SET 
				display = ?,
				category = ?,
				depth = ?,
				def = ?
			WHERE 
				id = ?
			";
			$params = [$dis, $cats, $dep, $def, $ido];
		};
		//---
		if (isset($_REQUEST['test'])) {
			echo "<br>$qua<br>";
		};
		//---
		execute_query($qua, $params);
	};
	if ($_REQUEST['test'] == 'dd') {
		exit;
	};
};
//---
if (isset($_POST['cat'])) {
	for($i = 0; $i < count($_POST['cat']); $i++ ){
		$cat = $_POST['cat'][$i];
		$dis = $_POST['dis'][$i];
		$def = $_POST['def'][$i];
		$ido = $_POST['id'][$i];
		$ido = (isset($ido)) ? $ido : '';
		$dep = $_POST['dep'][$i];
		//---
		$qua = "INSERT INTO categories (category, display, depth, def) 
			SELECT ?, ?, ?, ?
		WHERE NOT EXISTS (SELECT 1 FROM categories WHERE category = ?)";
		//---
		$params = [$cat, $dis, $dep, $def, $cat];
		//---
		if ($ido != '') {
			$qua = "UPDATE categories 
			SET 
				display = ?,
				category = ?,
				depth = ?,
				def = ?
			WHERE 
				id = ?
			";
			$params = [$dis, $cat, $dep, $def, $ido];
		};
		execute_query($qua, $params);
	};
};
//---
?>