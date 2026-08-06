<?
	if (!session_id()) {
		session_start();
	}

	if (!isset($_POST['s']) || !isset($_POST['l']) /*|| !isset($_POST['m'])*/) {
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
		$search = $_POST['s'];
		$limit = intval($_POST['l']);
		/*$month = $_POST['m'];*/
		$users = [];
		$i = 0;

		if ($limit < 1 ) {
			$limit = 1;
		} elseif ($limit > 1000 ) {
			$limit = 1000;
		}

			/* Зарегистрированные пользователи */
		$query = "SELECT u.id, u.email, u.description, u.user_date, l.ls, pr.pribor, p.pokazan, p.visibility, p.record_date FROM users AS u"
				. " LEFT JOIN ls AS l ON (l.user_id = u.id)"
				. " LEFT JOIN pribory AS pr ON (pr.ls = l.ls)"
				. " LEFT JOIN pokazania AS p ON (p.pribor = pr.pribor)"
				. " WHERE u.user_right = 0 AND ("
				. " l.ls LIKE '%".$search."%'"
				. " OR u.email LIKE '%".$search."%') ORDER BY l.ls AND p.visibility DESC LIMIT ".$limit;

		if (!($statement = $db->prepare($query))) {
			exit('Ошибка запроса (select)');
		}
		$statement->execute();
		$statement->bind_result($id, $email, $description, $user_date, $ls, $pribor, $pokazan, $visibility, $record_date);

		$users = [];
		$i = 0;

		while ($statement->fetch()) {
			$users[$i]['id'] = $id;
			$users[$i]['email'] = $email;
			$users[$i]['description'] = $description;
			$users[$i]['user_date'] = $user_date;
			$users[$i]['ls'] = $ls;
			$users[$i]['pribor'] = $pribor;
			$users[$i]['pokazan'] = $pokazan;
			$users[$i]['visibility'] = $visibility;
			$users[$i]['record_date'] = $record_date;
			$i++;
		}
		$statement->close();

$string_ret  = '';


$string_ret .= '<table class="table table-hover">
					<thead>
					  <tr>
						<th scope="col" style="width: 120px;">л/с</th>
						<th scope="col">e-mail</th>
						<th scope="col">Прибор учета</th>
						<th scope="col" style="width: 230px;">Последние показания</th>
						<th scope="col"style="width: 100px;">Действие</th>
					  </tr>
					</thead>
					<tbody>';


	foreach ($users as $u) {
		if ($u['ls'] < 1) {
			continue;
		}

		$pd =  $u['pokazan'] == null ? '':date("d.m.Y",strtotime($u['record_date']));
		$u['pokazan']  = $u['pokazan'] == null ? 'нет':$u['pokazan'];
		$v = $u['visibility'] ? 'on' : 'off';
		$s = $u['visibility'] ? 'не принято' : 'принято';
		$pk = $v == 'on' ? "<b>{$u['pokazan']}</b>" : $u['pokazan'];
		$d = $u['description'] == null ? '' : " {$u['description']}<br>";
		$act = strpos($u['email'],'@') ? "<i class=\"fa fa-envelope fa-2x\" aria-hidden=\"true\" title=\"Отправить письмо\" data-toggle=\"modal\" data-target=\"#modal-send\" data-email=\"{$u['email']}\"></i>" : "<a href=\"tel: {$u['email']}\"><i class=\"fa fa-phone fa-2x\" aria-hidden=\"true\" title=\"Позвонить\"></i></a>";
		$string_ret .= "
			<tr>
				<td>{$u['ls']}</td>
				<td>
					{$u['email']}
					<br>
					<span class=\"color_grey\">{$d}Зарегистрирован ".date("d.m.Y",strtotime($u['user_date']))."</span>
				</td>
				<td>
					<input id=\"pribor{$u['pribor']}\" class=\"input-pribor-number\" value=\"{$u['pribor']}\" style=\"width: 120px; font-size: 14px !important;\">
					<span><i class=\"fa fa-floppy-o fa-2x save-pribor\" aria-hidden=\"true\" style='cursor: pointer;' title=\"Сохранить\" onclick=\"change_pribor('{$u['pribor']}', '{$u['ls']}');\"></i></span>
				</td>

				<td>
					<span data-toggle=\"modal\" data-target=\"#modal-change\" data-pu=\"{$u['pribor']}\" data-pk=\"{$u['pokazan']}\" style='cursor: pointer'>{$pk}</span> <span class=\"logo color_grey\">{$pd}</span>
					<span class=\"actions\" onclick=\"change_pokazanie('{$u['pribor']}')\" id=\"act_chg_pok_{$u['pribor']}\" data-val=\"{$u['visibility']}\"> <i class=\"fa fa-toggle-{$v} fa-2x\" aria-hidden=\"true\" title=\"Показание {$s}\"></i></span>
				</td>
				<td>
					<span class=\"actions\">
						{$act}
					</span>
				</td>
			</tr>
		";
	}

	$string_ret .= '</tbody>
					</table>';

echo $string_ret;