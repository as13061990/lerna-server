<div class="container">
	<h2 class="mb-3">Общая статистика</h2>
	
	<div class="col-12 mt-3">
		<div class="mt-3">Общее количество уникальных пользователей, которые запустили чат-бот - <b><?= count($users) ?></b></div>
	</div>
	
	<h2 class="my-3">Пользователи</h2>
	<div class="col-12 mt-3">
		<table class="sort">
			<thead>
				<tr>
					<td>id</td>
					<td>юзернейм</td>
					<td>результат</td>
					<!-- <td>время</td> -->
				</tr>
			</thead>
			<tbody id="tbody">
			<? foreach ($users as $user) { ?>
				<tr>
					<td><?= $user['id'] ?></td>
					<td><?= $user['username'] ?></td>
					<!-- <td><span class="unixtime"><?= (int) $user['time'] ?></span><span><?= $user['time'] !== null ? date('Y.m.d H:i:s', $user['time']) : '' ?></span></td> -->
				</tr>
			<? } ?>
			</tbody>
		</table>
	</div>
</div>