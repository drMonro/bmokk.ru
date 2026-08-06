<?
	if (!session_id()) {
		session_start();
	}
//exit('!!!');
	if (!isset($_POST['f']) ) {
		exit ('Нет данных!');
	}

	$filename = $_SERVER['DOCUMENT_ROOT'].'/upload/'.$_POST['f'];

	if (!file_exists($filename)) {
		exit("Файл не существует!");
	}

	if (isset($_SESSION['user_right'])) {
		if ($_SESSION['user_right'] != 'admin') {
			exit('Error right!');
		}
	} else {
			exit('Error session!');
	}

	include_once ($_SERVER['DOCUMENT_ROOT'].'/config.php');
	$file_n = $_POST['f'];

	/* Удаляем записи связанные с этим файлом выгрузки*/
		$query = "DELETE FROM sberbank WHERE file_n LIKE '%".$file_n."'";
		if (!($statement = $db->prepare($query))) {
			exit('Ошибка запроса (DELETE)');
		}
		$statement->execute();
		$statement->close();

		$fp = @fopen($filename, "r");
		$id = null;
		if ($fp) {
			while (($buffer = fgets($fp)) !== false) {

				$ls = $code_1 = $code_2 = $kvartira = $address = $summa = $month = $fio = '';

/*
				if (strpos($buffer, ';;,;')){

					$buffer = str_replace(';;,;', ';', $buffer);
					$ls = $d[0];
					$fio = $d[1];
					$address = $d[2];
					$month = substr($d[3],0,2).'/'.substr($d[3],-2);
					$summa = $d[4];

				} else {
*/
					$d = explode(";", $buffer);
					$ls = $d[0];
					$code_1 = $d[1];
						$d2 = explode(",", $d[2]); /* код, квартира*/
					$code_2 = $d2[0];
					$kvartira = $d2[1];
						$d3 = explode(",", $d[3]); /* адрес с фамилией краткий, 0 - нас/пункт, 1 - улица, 2 - фио*/
					$fio = $d3[2];
					$address = $d[4]; /* полный адрес */
					$month = substr($d[5],0,2).'/'.substr($d[5],-2); /*дата из нового формата 0722 в 07/22 как в бд*/
					$summa = $d[6]; /* сумма начислений за месяц */
			/*	} */


					$query = "INSERT INTO sberbank (id, ls, code_1, code_2, kvartira, address, summa, file_n, month, fio) VALUES (?,?,?,?,?,?,?,?,?,?)";
					if (!($statement = $db->prepare($query))) {
						exit('Ошибка запроса (insert)');
					}
					$statement->bind_param("isssssssss", $id, $ls, $code_1, $code_2, $kvartira, $address, $summa, $file_n, $month, $fio);
					$statement->execute();
					$statement->close();

				//echo $buffer;
			}
			if (!feof($fp)) {
				exit ("Ошибка: fgets() неожиданно потерпел неудачу");
			}
			fclose($fp);
			//запишем данные о файле и месяце
				$id = null;
				$query = "INSERT INTO month_list (id, file_name, month) VALUES (?,?,?)";
				if (!($statement2 = $db->prepare($query))) {
					exit('Ошибка запроса (insert)');
				}
				$statement2->bind_param("iss", $id, $file_n, $month);
				$statement2->execute();
				$statement2->close();
			echo '777';
		} else {
			exit('Не удалось открыть файл '.$filename);
		}