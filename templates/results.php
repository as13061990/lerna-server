<?php
function getCode($pro) {
	$portal = substr($pro, 2);
	return $portal === 'skillbox' ? 'LernaTelegramSB' : 'LernaTelegramGB';
}
function getProfession($pro, $professions) {
	$portal = substr($pro, 2);
	$vector = substr($pro, 1, 1);
	$index = substr($pro, 0, 1);
	return $professions[$portal][$vector][$index]['name'];
}
?>
<div class="container">
	<h3><a href="/" style="color:blue">назад</a></h3>
	<h2 class="mb-3">Тестирования</h2>

	<div class="col-12 mt-3">
		<table class="sort">
			<thead>
				<tr>
					<td>telegram_id </td>
					<td>telegram_username</td>
					<td>результат</td>
					<td>промокод</td>
					<td>время завершения</td>
				</tr>
			</thead>
			<tbody id="tbody">
			<? foreach ($results as $result) { ?>
				<tr>
					<td><?= $result['user_id'] ?></td>
					<td><?= $result['name'] ?></td>
					<td><?= getProfession($result['profession'], $professions) ?></td>
					<td><?= getCode($result['profession']) ?></td>
					<td><span class="unixtime"><?= strtotime($result['time']) ?></span><span><?= date('Y.m.d H:i:s', strtotime($result['time'])) ?></span></td>
				</tr>
			<? } ?>
			</tbody>
		</table>
	</div>
</div>