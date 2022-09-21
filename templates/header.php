<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="/public/favicon.ico" rel="icon" type="image/x-icon">
	<link href="/public/css/bootstrap.min.css" rel="stylesheet">
	<link href="/public/css/mdb.min.css" rel="stylesheet">
	<link href="/public/css/style.css" rel="stylesheet">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
	<title>Lerna</title>
	<style>
		table.sort {
			border-spacing: 0.1em;
			margin-bottom: 1em;
			margin-top: 1em;
			font-size: 12px;
		}
		table.sort td {
			border: 1px solid #CCCCCC;
			padding: 0.3em 1em
		}
		table.sort thead td {
			cursor: pointer;
			cursor: hand;
			font-weight: bold;
			text-align: center;
			vertical-align: middle
		}
		table.sort tbody td {
			text-align: left;
		}
		table.sort thead td.curcol {
			background-color: #999999;
			color: #FFFFFF
		}
		thead tr td {
			background: #CCCCCC;
			-moz-user-select: none;
			-webkit-user-select: none;
			-ms-user-select: none;
			-o-user-select: none;
			user-select: none;
		}
		a { color: #000; }
		.new-status { background: #ff9494; }
		.done-status { background: #98e38d; }
		.in-process { background: #87cefa; }
		.unixtime { display: none }
	</style>
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-dark fixed-top black scrolling-navbar">
		<div class="container">
			<a href="/" class="navbar-brand">
			<img src="/public/images/logo.png" width="30" height="30" alt="logo"> <strong> Lerna</strong></a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#basicExampleNav" aria-controls="basicExampleNav" aria-expanded="false" aria-label="Toggle Navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="basicExampleNav">
				<!-- <ul class="navbar-nav mr-auto smooth-scroll">
					<li class="nav-item">
						<a href="/link" class="nav-link waves-effect waves-light">Другой раздел</a>
					</li>
				</ul> -->
			</div>
		</div>
	</nav>
	<div style="width:100px; height: 100px;"></div>