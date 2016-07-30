<!DOCTYPE html>
<html lang="ru">
	<head>
		<meta charset="UTF-8">
		<title>Заказ печати</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
		<link rel="stylesheet" type="text/css" href="css/main.css" />
	</head>
	<body>
		<div class="container">
		<div id="loading" style="width: 1000px; height: 1000px; z-index: 999999; background-color: #000;opacity: .5;position: absolute; display: none;" ></div>

			<div class="row">
				<div class="col-md-12">
					<h2>Оформление заказа</h2>
					<hr />
				</div>
			</div>
			<form id="order_form" enctype="multipart/form-data">
				<div class="row">
					<div class="col-md-12">
						<h3>Параметры печати</h3>
					</div>
					<div class="col-md-3 col-sm-6 col-xs-12">
						<div class="form-group">
							<label for="format">Формат</label>
							<select name="format" class="form-control" id="format">
								<option value="10 x 15">10 x 15</option>
								<option value="15 x 21">15 x 21</option>
								<option value="20 x 30">20 x 30</option>
								<option value="30 x 40">30 x 40</option>
							</select>
						</div>
					</div>
					<div class="col-md-3 col-sm-6 col-xs-12">
						<div class="form-group">
							<label for="paper">Бумага</label>
							<select name="paper" class="form-control" id="paper">
								<option value="Глянцевая">Глянцевая</option>
								<option value="Матовая">Матовая</option>
								<option value="Тисненая">Тисненая</option>
							</select>
						</div>
					</div>
					<div class="col-md-3 col-sm-6 col-xs-12">
						<div class="form-group">
							<label for="brim">Поля</label>
							<select name="brim" class="form-control">
								<option value="Без полей">Без полей</option>
								<option value="С полями">С полями</option>
							</select>
						</div>
					</div>
					<div class="col-md-3 col-sm-6 col-xs-12">
						<div class="form-group">
							<label for="quantity">Количество</label>
							<input type="number" min = "1" name="quantity" class="form-control" id="quantity">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<h3>Информация о покупателе</h3>
					</div>
					<div class="col-md-4 col-sm-6 col-xs-12">
						<div class="form-group">
							<label for="name">Имя</label>
							<input type="text" name="name" id="" class="form-control" required pattern="^[А-Яа-яЁё\s]+$">
						</div>
					</div>
					<div class="col-md-4 col-sm-6 col-xs-12">
						<div class="form-group">
							<label for="phone">Телефон</label>
							<input type="text" name="phone" id="phone" class="form-control" required>
						</div>
					</div>
					<div class="col-md-4 col-sm-6 col-xs-12">
						<div class="form-group">
							<label for="address">Адрес доставки</label>
							<input type="text" name="address" id="" class="form-control">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-xs-12">
						<div class="form-group">
							<label for="format">Примечание</label>
							<textarea name="note" class="form-control"></textarea>
						</div>
					</div>
					<div class="col-md-6 col-xs-12">
						<div class="form-group">
							<label for="file[]">Файлы</label>
			 				<input type="hidden" name="MAX_FILE_SIZE" value="50000" />
			   				<input type="file" name="file[]"  multiple required accept="image/*" />
			   			</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3 col-xs-12">
						<p id="result"></p>
					</div>
					<div class="col-md-9 col-xs-12 order_buttons">
						<input type="submit" value="Отправить" class="btn btn-success">
					</div>
				</div>
	 		</form> 
		<div class="out"></div>
		</div>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		<script type="text/javascript" src="js/jquery.maskedinput.min.js"></script>
		<script type="text/javascript" src="js/serialize.js"></script>
		<script type="text/javascript" src="js/main.js"></script>
	</body>
</html>
