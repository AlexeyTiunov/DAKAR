<!doctype html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Dakar &mdash; service</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link href='http://fonts.googleapis.com/css?family=Dosis:300,400' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/dest/css/font-awesome.min.css">
	<link rel="stylesheet" href="assets/dest/rs-plugin/css/settings.css">
	<link rel="stylesheet" href="assets/dest/rs-plugin/css/responsive.css">
	<link rel="stylesheet" href="assets/dest/vendors/colorbox/example3/colorbox.css">
	<link rel="stylesheet" title="style" href="assets/dest/css/style.css">
	<link rel="stylesheet" href="assets/dest/css/animate.css">
	
</head>
<body>
	
	<div id="header">
		<div class="header-top">
			<div class="container">
				<div class="pull-left auto-width-left">
					<ul class="top-menu menu-beta l-inline">
						<li><a href="index.php"><i class="fa fa-home"></i> Главная</a></li>
						<li><a href="#"><i class="fa fa-sitemap"></i> Карта сайта</a></li>
					</ul>
				</div>
				<div class="pull-right auto-width-right">
					<ul class="top-details menu-beta l-inline">
						<li>
						<a href="#"><i class="fa fa-user"></i> Мой профиль</a>
						</li>
						<li class="hidden-xs">
							<select name="currency">
								<option value="usd">USD</option>
								<option value="eur">EUR</option>
								<option value="ron">UAH</option>
							</select>
						</li>
					</ul>
				</div>
				<div class="clearfix"></div>
			</div> <!-- .container -->
		</div> <!-- .header-top -->
		<div class="header-body">
			<div class="container beta-relative">
				<div class="pull-left">
					<a href="index.php" id="logo"><img src="assets/dest/images/logo.png" alt="" width="200em"></a>
					<span class="slogan">Магазин - (098) 622 23 80 					Автосервис - (068) 841 25 13 
					</span>
				</div>
				<div class="pull-right beta-components space-left ov">
					<div class="space10">&nbsp;</div>
					<div class="beta-comp">
						<form role="search" method="get" id="searchform" action="/">
					        <input type="text" value="" name="s" id="s" placeholder="Введите номер запчасти" />
					        <button class="fa fa-search" type="submit" id="searchsubmit"></button>
						</form>
					</div>

					<div class="beta-comp" id="basket">
						<div class="cart">
							<div class="beta-select"><i class="fa fa-shopping-cart"></i> Корзина (1) <i class="fa fa-chevron-down"></i></div>
							<div class="beta-dropdown cart-body">
								<div class="cart-item">
									<a class="cart-item-edit" href="#"><i class="fa fa-pencil"></i></a>
									<a class="cart-item-delete" href="#"><i class="fa fa-times"></i></a>
									<div class="media">
										<a class="pull-left" href="#"><img src="#" alt=""></a>
										<div class="media-body">
											<span class="cart-item-title">Колодки</span>
											<span class="cart-item-options">Артикул: 04465-35002; Бренд: TOYOTA</span>
											<span class="cart-item-amount">1*<span>$49.50</span></span>
										</div>
									</div>
								</div>

									<div class="cart-caption">
									<div class="cart-total text-right">Всего: <span class="cart-total-value">$49.50</span></div>
									<div class="clearfix"></div>

									<div class="center">
										<div class="space10">&nbsp;</div>
										<a href="checkout.php" class="beta-btn primary text-center">Оформить заказ <i class="fa fa-chevron-right"></i></a>
									</div>
								</div>
							</div>
						</div> <!-- .cart -->
					</div>
				</div>
				<div class="clearfix"></div>
			</div> <!-- .container -->
		</div> <!-- .header-body -->
		<div class="header-bottom">
			<div class="container">
				<a class="visible-xs beta-menu-toggle pull-right" href="#">
				<i class="fa fa-bars"></i></a>

				<div class="visible-xs clearfix"></div>
				<nav class="main-menu">
					<ul class="l-inline ov">
						<li><a href="index.php">Главная</a></li>
						<li><a href="about.php">О нас</a></li>
						<li><a href="delivery.php">Доставка и оплата</a></li>
						<li><a href="catalog.php">Каталог з/ч</a></li>
						<li><a href="calc.php">Калькулятор ТО</a></li>
						<li><a href="service.php">Автосервис</a></li>
						<li><a href="contacts.php">Контакты</a></li>
						<li><a href="cabinet.php">Личный кабинет</a></li>
					</ul>
					<div class="clearfix"></div>
				</nav>
			</div> <!-- .container -->
		</div> <!-- .header-bottom -->
	</div> <!-- #header -->
	<div class="space50">&nbsp;</div>
					<!-- .beta-products-list -->
				</div> <!-- .main-content -->

				<div class="col-sm-3 aside">
					<div class="widget">
						<h3 class="widget-title">Поиск по авто</h3>
						<div class="widget-body">
							<ul class="list-unstyled">
								<li class="hidden-xl">
									<select name="Бренд">
										<option value="ru">Toyota</option>
										<option value="ro">Lexus</option>
									</select>
								</li>
								<li class="hidden-xl">
									<select name="Модель">
										<option value="ru">Toyota</option>
										<option value="ro">Lexus</option>
									</select>
								</li>
								<li class="hidden-xl">
									<select name="Тип модели">
										<option value="ru">Toyota</option>
										<option value="ro">Lexus</option>
									</select>
								</li>
								
								
							</ul>
						</div>
					</div> <!-- brands widget -->

					<div class="widget">
						<h3 class="widget-title">Новости</h3>
						<div class="widget-body">
							<ul class="list-unstyled">
									<li>
									<label for="colors-white"><span>C 1 декабря 2016 года<br>
									начал работать наш обновлённый сайт </span></label>
								</li>
							</ul>
						</div>
					</div> 
					<div class="widget">
						<h3 class="widget-title">Популярное</h3>
						<div class="widget-body">
							<div class="beta-sales beta-lists">
								<div class="media beta-sales-item">
									<a class="pull-left" href="product.php"><img src="assets/dest/images/products/sales/1.png" alt=""></a>
									<div class="media-body">
										Запчасть
										<span class="beta-sales-price">$34.55</span>
									</div>
								</div>
							</div>
						</div>
					</div> 
				</div> <!-- .aside -->
	<div class="container">
		<div id="content">
			<div class="row">
				
		</div> <!-- #content -->
	</div> </div><!-- .container -->

<div id="footer">
<div class="container">
		
			<div class="row">
				<div class="col-sm-1">
					<div class="widget">
						<div>
							<ul>
								<li><a href="#"><img src="assets/dest/images/social/inst.png"></a></li>
								
								
							</ul>
						</div>
					</div>
				</div>
				<div class="col-sm-1">
					<div class="widget">
						<div>
							<ul>
	
								<li><a href="#"><img src="assets/dest/images/social/fb.png"></a></li>
								
								
							</ul>
						</div>
					</div>
				</div>
				<div class="col-sm-1">
					<div class="widget">
						<div>
							<ul>
	
								<li><a href="#"><img src="assets/dest/images/social/vk.png"></a></li>
								
								
							</ul>
						</div>
					</div>
				</div>
				<div class="col-sm-3">
				 <div class="col-sm-10">
					<div class="widget">
						<h4 class="widget-title">Контакты</h4>
						<div>
							<div class="contact-info">
								<i class="fa fa-map-marker"></i>
								<p>СТО</p>
								<p>068 841 25 13 </p>
								<p>Киев, ул.Жилянська 88</p>
							</div>
							
						</div>
					</div>
					</div>
				</div>
				<div class="col-sm-3">
				 <div class="col-sm-10">
					<div class="widget">
						
						<div>
							
							<div class="space20">&nbsp</div>
							<div class="contact-info">
								<i class="fa fa-map-marker"></i>
								<p>Магазин</p>
								<p>098 622 23 80</p>
								<p>095 877 88 01</p>
								<p>067 925 19 24</p>
								<p>Киев, ул.Садовая 70/110</p>
								<p>сектор А, магазин №13</p>
							</div>
							
						</div>
					</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="widget">
						<h4 class="widget-title">Подписка на новости</h4>
						<form action="#" method="post">
							<input type="email" name="your_email" placeholder="your_email@com">
							<button class="pull-right" type="submit">Подписаться<i class="fa fa-chevron-right"></i></button>
						</form>
					</div>
				</div>
			</div> <!-- .row -->
		</div>  <!-- #footer -->

	<!-- include js files -->
	<script src="assets/dest/js/jquery.js"></script>
	<script src="assets/dest/vendors/jqueryui/jquery-ui-1.10.4.custom.min.js"></script>
	<script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
	<script src="assets/dest/vendors/bxslider/jquery.bxslider.min.js"></script>
	<script src="assets/dest/vendors/colorbox/jquery.colorbox-min.js"></script>
	<script src="assets/dest/vendors/animo/Animo.js"></script>
	<script src="assets/dest/vendors/dug/dug.js"></script>
	<script src="assets/dest/js/scripts.min.js"></script>
	<script src="assets/dest/js/jquery.countTo.js"></script>
	<script src="assets/dest/js/waypoints.min.js"></script>
	<script src="assets/dest/js/wow.min.js"></script>
</body>
</html>