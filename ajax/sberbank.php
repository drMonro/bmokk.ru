<?
	if (!session_id()) {
		session_start();
	}

	if (!isset($_POST['f']) || !isset($_POST['s']) || !isset($_POST['l'])) {
		exit ('Нет данных!');
	}

	if (isset($_SESSION['user_right'])) {
		if ($_SESSION['user_right'] != 'admin') {
			exit('Error right!');
		}
	} else {
			exit('Error session!');
	}

	include_once ($_SERVER['DOCUMENT_ROOT'].'/config.php');

		/* Данные в выгрузках */
		$file = $_POST['f'];
		$search = $_POST['s'];
		$limit = intval($_POST['l']);
		$sber = [];
		$i = 0;

		if ($limit < 1 ) {
			$limit = 1;
		} elseif ($limit > 200 ) {
			$limit = 200;
		}

		$query = "SELECT ls, address, kvartira, summa, month, fio FROM sberbank WHERE file_n LIKE '%".basename($file)."' AND ("
				. " ls LIKE '%".$search."%' "
				. " OR address LIKE '%".$search."%' "
				. " OR summa LIKE '%".$search."%' "
				. " OR fio LIKE '%".$search."%' "
				. " ) LIMIT ".$limit;

		if (!($statement = $db->prepare($query))) {
			exit('Ошибка запроса (select sberbank)');
		}
		$statement->execute();
		$statement->bind_result($ls, $address, $kvartira, $summa, $month, $fio);

		while ($statement->fetch()) {
			$sber[$i]['ls'] = $ls;
			$sber[$i]['address'] = $address;
			$sber[$i]['kvartira'] = $kvartira;
			$sber[$i]['summa'] = $summa;
			$sber[$i]['month'] = $month;
			$sber[$i]['fio'] = $fio;
			$i++;
		}

		$statement->close();

$string_ret  = '';


$string_ret .= '<table class="table table-hover">
					<thead>
					  <tr>
						<th scope="col" style="width: 50px;">л/с</th>
						<th scope="col">Адрес</th>
						<th scope="col" style="width: 60px;">Квартира</th>
						<th scope="col" style="width: 100px;">Сумма</th>
						<th scope="col"style="width: 100px;">Месяц</th>
					  </tr>
					</thead>
					<tbody>';


	foreach ($sber as $s) {
		$string_ret .= "
			<tr>
				<td>{$s['ls']}</td>
				<td>{$s['address']}<br><b>{$s['fio']}</b></td>
				<td>{$s['kvartira']}</td>
				<td><span ".((intval(str_replace(',', '.', $s['summa'])) < 0 || intval(str_replace(',', '.', $s['summa'])) == 0) ? 'style="color: red"': '')." >{$s['summa']}</span></td>
				<td>{$s['month']}</td>
			</tr>
		";
	}

	$string_ret .= '</tbody>
					</table>';

echo $string_ret;