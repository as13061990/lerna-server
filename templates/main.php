<?php
function getCode($code) {
	if (strlen((string) $code) === 1) {
		$code = '000' . $code;
	} else if (strlen((string) $code) === 2) {
		$code = '00' . $code;
	} else if (strlen((string) $code) === 3) {
		$code = '0' . $code;
	}
	return 'LernaTG' . $code;
}
?>
<div class="container">
	<h2 class="mb-3">Общая статистика</h2>
	
	<div class="col-12 mt-3">
		<div class="mt-3">Общее количество уникальных пользователей, запустивших чат-бот - <b><?= count($users) ?></b></div>
		<div class="mt-3">Общее количество уникальных пользователей, запустивших Web-App - <b><?= $webApp ?></b></div>
		<div class="mt-3">Общее количество открытий Web-App’а - <b><?= $main['open_app'] ?></b></div>
		<div class="mt-3">Общее количество уникальных прохождений тестирования в Web-App’е - <b><?= $results ?></b></div>
		<div class="mt-3">Общее количество прохождений тестирования в Web-App’е - <b><?= $allResults ?></b></div>
		<div class="mt-3">Общее количество пользователей, запустивших чат-бот по реферальной ссылке - <b><?= $main['referral'] ?></b></div>
		
	</div>
	
	<h2 class="my-3">Пользователи</h2>
	<div class="col-12 mt-3">
		<table class="sort">
			<thead>
				<tr>
					<td>id</td>
					<td>юзернейм</td>
					<td>промокод</td>
					<td>время получения</td>
				</tr>
			</thead>
			<tbody id="tbody">
			<? foreach ($users as $user) { ?>
				<tr>
					<td><?= $user['id'] ?></td>
					<td><?= $user['username'] ?></td>
					<td><?= $user['promo'] == 0 ? '' : getCode($user['promo']) ?></td>
					<td><span class="unixtime"><?= (int) $user['time_promo'] ?></span><span><?= $user['time_promo'] != 0 ? date('Y.m.d H:i:s', $user['time_promo']) : '' ?></span></td>
				</tr>
			<? } ?>
			</tbody>
		</table>
	</div>
</div>