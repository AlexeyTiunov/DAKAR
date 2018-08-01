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
					<span class="slogan">Магазин - (044) 258 47 47 					Автосервис - (044) 258 48 48
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

					<div class="beta-comp">
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
				<a class="visible-xs beta-menu-toggle pull-right" href="#"><span class='beta-menu-toggle-text'>Меню</span> <i class="fa fa-bars"></i></a>
				<div class="visible-xs clearfix"></div>
				<nav class="main-menu">
					<ul class="l-inline ov">
						<li><a href="index.php">Главная</a>
							<!--<ul class="sub-menu">
								<li><a href="home_10.php">Home Portfolio</a></li>
								<li><a href="home_4.php">Company Intro 1</a></li>
								<li><a href="home_5.php">Company Intro 2</a></li>
								<li><a href="home_7.php">Company Intro 3</a></li>
								<li><a href="home_6.php">Home Classic 1</a></li>
								<li><a href="home_8.php">Home Classic 2</a></li>
								<li><a href="home_9.php">Home Classic 3</a></li>
								<li><a href="index.php">Home Shop 1</a></li>
								<li><a href="home_2.php">Home Shop 2</a></li>
								<li><a href="home_3.php">Home Shop 3</a></li>
							</ul>-->
						</li>
						<li><a href="#">О нас</a>
							<ul class="sub-menu">
								<li><a href="about_1.php">About 1</a></li>
								<li><a href="about_2.php">About 2</a></li>
								<li><a href="testimonials.php">Testimonials</a></li>
								<li><a href="text_page.php">Text Page</a></li>
								<li><a href="typography.php">Typography</a></li>
								<li><a href="accordion_toggles.php">Accordion and Toggles</a></li>
								<li><a href="buttons.php">Buttons</a></li>
								<li><a href="custom_callout_box.php">Custom Callout Box</a></li>
								<li><a href="404.php">Page 404</a></li>
								<li><a href="under_construction.php">Coming Soon</a></li>
							</ul>
						</li>
						<li><a href="features.php"></a></li>
						<li><a href="#">Доставка и оплата</a>
							<!--<ul class="sub-menu">
								<li><a href="portfolio_type_a.php">Portfolio type A</a></li>
								<li><a href="#">Portfolio type B</a>
									<ul class="sub-menu">
										
										<li><a href="portfolio_3col.php">Portfolio B - 3 Columns</a></li>
										<li><a href="portfolio_4col.php">Portfolio B - 4 Columns</a></li>
									</ul>
								</li>
								<li><a href="portfolio_single.php">Portfolio Item</a></li>
							</ul>-->
						</li>
						<li><a href="#">Каталог з/ч</a>
							<!--<ul class="sub-menu">
								<li><a href="blog_fullwidth_1col.php">Blog Full Width</a>
									<ul class="sub-menu">
										<li><a href="blog_fullwidth_2col.php">Blog Full Width - 2 Columns</a></li>
										<li><a href="blog_fullwidth_3col.php">Blog Full Width - 3 Columns</a></li>
										<li><a href="blog_fullwidth_4col.php">Blog Full Width - 4 Columns</a></li>
									</ul>
								</li>
								<li><a href="#">Blog Type A</a>
									<ul class="sub-menu">
										<li><a href="blog_with_sidebarleft_type_a.php">Blog A - Left Sidebar</a></li>
										<li><a href="blog_with_sidebarright_type_a.php">Blog A - Right Sidebar</a></li>
									</ul>
								</li>
								<li><a href="blog_with_sidebarleft_type_b.php">Blog Type B</a></li>
								<li><a href="#">Blog Type C</a>
									<ul class="sub-menu">
										<li><a href="blog_with_sidebarleft_type_c.php">Blog C - Left Sidebar</a></li>
										<li><a href="blog_with_sidebarleft_type_c_2cols.php">Blog C - 2 Columns</a></li>
									</ul>
								</li>
								<li><a href="blog_with_sidebarleft_type_d.php">Blog Type D</a></li>
								<li><a href="#">Blog Type E</a>
									<ul class="sub-menu">
										<li><a href="blog_with_sidebarleft_type_e.php">Blog E - Left Sidebar</a></li>
										<li><a href="blog_with_2sidebars_type_e.php">Blog E - 2 Sidebars</a></li>
									</ul>
								</li>
								<li><a href="#">Blog Single Post</a>
									<ul class="sub-menu">
										<li><a href="single_type_image.php">Single Post Image</a></li>
										<li><a href="single_type_gallery.php">Single Post Gallery</a></li>
										<li><a href="single_type_video.php">Single Post Video</a></li>
										<li><a href="single_type_audio.php">Single Post Audio</a></li>
										<li><a href="single_type_slideshow.php">Single Post Slideshow</a></li>
										<li><a href="single_type_quote.php">Single Post Quote</a></li>
									</ul>
								</li>
							</ul>-->
						</li>
						<li><a href="#">Автосервис</a>
							<ul class="sub-menu">
								
								<li><a href="home_2.php">Home Shop 2</a></li>
								<li><a href="home_3.php">Home Shop 3</a></li>
								<li><a href="checkout.php">Checkout</a></li>
								<li><a href="pricing.php">Pricing</a></li>
								<li><a href="shopping_cart.php">Shopping Cart</a></li>
								<li><a href="product.php">Product</a></li>
							</ul>
						</li>
						<li><a href="contacts.php">Контакты</a></li>
					</ul>
					<div class="clearfix"></div>
				</nav>
			</div> <!-- .container -->
		</div> <!-- .header-bottom -->
	</div> <!-- #header -->
	
	<div class="inner-header">
		<div class="container">
			<div class="pull-left">
				<h6 class="inner-title">Поиск запчастей</h6>
			</div>
			<div class="pull-right">
				<div class="beta-breadcrumb font-large">
					<a href="index.php">Главная</a> / <span>Результаты поиска</span>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="space10">&nbsp;</div>

		<div id="content">
			<div class="col-sm-2 aside">
					<div class="widget">
						<h3 class="widget-title">Поиск по авто</h3>
						<div class="widget-body">
							<ul class="list-unstyled">
								<li class="hidden-xs">
									<select name="Бренд">
										<option value="ru">Toyota</option>
										<option value="ro">Lexus</option>
									</select>
								</li>
								<li class="hidden-xs">
									<select name="Модель">
										<option value="ru">Toyota</option>
										<option value="ro">Lexus</option>
									</select>
								</li>
								<li class="hidden-xs">
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
							<input type="c" name="">
								
								<li>
									
									<label for="colors-white"><span></span> White <span>(789)</span></label>
								</li>
							</ul>
						</div>
					</div> <!-- colors widget -->

					 <!-- price range widget -->

					<div class="widget">
						<h3 class="widget-title">Best Sellers</h3>
						<div class="widget-body">
							<div class="beta-sales beta-lists">
								<div class="media beta-sales-item">
									<a class="pull-left" href="product.php"><img src="assets/dest/images/products/sales/1.png" alt=""></a>
									<div class="media-body">
										Sample Woman Top
										<span class="beta-sales-price">$34.55</span>
									</div>
								</div>
							</div>
						</div>
					</div> <!-- best sellers widget -->

					 <!-- twitter feeds widget -->

					 <!-- tags cloud widget -->
				</div> <!-- .aside -->
			<div class="col-sm-10">
				<!-- Shop Products Table -->
				<table class="shop_table beta-shopping-cart-table" cellspacing="0">
					<thead>
						<tr>
							<th class="product-photo">Фото</th>
							<th class="product-brand">Бренд</th>
							<th class="product-number">Артикул</th>
							<th class="product-name">Название</th>
							<th class="product-quantity">К-во</th>
							<th class="product-price">Цена</th>
							<th class="product-remove">Действие</th>
						</tr>
					</thead>
					<tbody>
					
						<tr class="cart_item">
							<td class="product-photo">
								<div class="media">
									<img class="pull-left" src="assets/dest/images/shoping1.jpg" alt="">
								</div>
							</td>
							<td class="product-brand"><span class="amount">Toyota</span></td>

							<td class="product-number">0445633568</td>

							<td class="product-name">Запчасть</td>

							<td class="product-quantity">3</td>

							<td class="product-price"><span class="amount">$235.00</span></td>

							<td class="product-remove">
							<select name="product-qty" id="product-qty"><option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
								</select>
								<a href="#" class="remove" title="Remove this item"><i class="fa fa-shopping-cart"></i></a>
							</td>
						</tr>
						<tr class="cart_item">
							<td class="product-photo">
								<div class="media">
									<img class="pull-left" src="assets/dest/images/shoping1.jpg" alt="">
								</div>
							</td>
							<td class="product-brand"><span class="amount">Toyota</span></td>

							<td class="product-number">0445633568</td>

							<td class="product-name">Запчасть</td>

							<td class="product-quantity">3</td>

							<td class="product-price"><span class="amount">$235.00</span></td>

							<td class="product-remove">
							<select name="product-qty" id="product-qty"><option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
								</select>
								<a href="#" class="remove" title="Remove this item"><i class="fa fa-shopping-cart"></i></a>
							</td>
						</tr>
						<tr class="cart_item">
							<td class="product-photo">
								<div class="media">
									<img class="pull-left" src="assets/dest/images/shoping1.jpg" alt="">
								</div>
							</td>
							<td class="product-brand"><span class="amount">Toyota</span></td>

							<td class="product-number">0445633568</td>

							<td class="product-name">Запчасть</td>

							<td class="product-quantity">3</td>

							<td class="product-price"><span class="amount">$235.00</span></td>

							<td class="product-remove">
							<select name="product-qty" id="product-qty"><option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
								</select>
								<a href="#" class="remove" title="Remove this item"><i class="fa fa-shopping-cart"></i></a>
							</td>
						</tr>

					</tbody>

					<tfoot>
						<tr>
							<td colspan="6" class="actions">

								<div class="coupon">
									<label for="coupon_code">Coupon</label> 
									<input type="text" name="coupon_code" value="" placeholder="Coupon code"> 
									<button type="submit" class="beta-btn primary" name="apply_coupon">Apply Coupon <i class="fa fa-chevron-right"></i></button>
								</div>
								
								<button type="submit" class="beta-btn primary" name="update_cart">Update Cart <i class="fa fa-chevron-right"></i></button> 
								<button type="submit" class="beta-btn primary" name="proceed">Proceed to Checkout <i class="fa fa-chevron-right"></i></button>
							</td>
						</tr>
					</tfoot>
				</table>
				<!-- End of Shop Table Products -->
			</div>

			<div class="clearfix"></div>

		</div> <!-- #content -->


	<div id="footer">
		<div class="container">
			<div class="row">
				<div class="col-sm-3">
					<div class="widget">
						<h4 class="widget-title">Instagram Feed</h4>
						<div id="beta-instagram-feed"><div></div></div>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="widget">
						<h4 class="widget-title">Information</h4>
						<div>
							<ul>
								<li><a href="blog_fullwidth_3col.php"><i class="fa fa-chevron-right"></i> Web Design</a></li>
								<li><a href="blog_fullwidth_3col.php"><i class="fa fa-chevron-right"></i> Web development</a></li>
								<li><a href="blog_fullwidth_3col.php"><i class="fa fa-chevron-right"></i> Marketing</a></li>
								<li><a href="blog_fullwidth_3col.php"><i class="fa fa-chevron-right"></i> Tips</a></li>
								<li><a href="blog_fullwidth_3col.php"><i class="fa fa-chevron-right"></i> Resources</a></li>
								<li><a href="blog_fullwidth_3col.php"><i class="fa fa-chevron-right"></i> Illustrations</a></li>
							</ul>
						</div>
					</div>
				</div>
				<div class="col-sm-4">
				<div class="col-sm-10">
					<div class="widget">
						<h4 class="widget-title">Contact Us</h4>
						<div>
							<div class="contact-info">
								<i class="fa fa-map-marker"></i>
								<p>30 South Park Avenue San Francisco, CA 94108 Phone: +78 123 456 78</p>
								<p>Nemo enim ipsam voluptatem quia voluptas sit asnatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione.</p>
							</div>
						</div>
					</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="widget">
						<h4 class="widget-title">Newsletter Subscribe</h4>
						<form action="#" method="post">
							<input type="email" name="your_email">
							<button class="pull-right" type="submit">Subscribe <i class="fa fa-chevron-right"></i></button>
						</form>
					</div>
				</div>
			</div> <!-- .row -->
		</div> <!-- .container -->
	</div> <!-- #footer -->
	<div class="copyright">
		<div class="container">
			<p class="pull-left">Privacy policy. (&copy;) 2014</p>
			<p class="pull-right pay-options">
				<a href="#"><img src="assets/dest/images/pay/master.jpg" alt="" /></a>
				<a href="#"><img src="assets/dest/images/pay/pay.jpg" alt="" /></a>
				<a href="#"><img src="assets/dest/images/pay/visa.jpg" alt="" /></a>
				<a href="#"><img src="assets/dest/images/pay/paypal.jpg" alt="" /></a>
			</p>
			<div class="clearfix"></div>
		</div> <!-- .container -->
	</div> <!-- .copyright -->
	

	<!-- include js files -->
	<script src="assets/dest/js/jquery.js"></script>
	<script src="assets/dest/vendors/jqueryui/jquery-ui-1.10.4.custom.min.js"></script>
	<script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
	<script src="assets/dest/vendors/bxslider/jquery.bxslider.min.js"></script>
	<script src="assets/dest/vendors/colorbox/jquery.colorbox-min.js"></script>
	<script src="assets/dest/vendors/animo/Animo.js"></script>
	<script src="assets/dest/vendors/dug/dug.js"></script>
	<script src="assets/dest/js/scripts.min.js"></script>
	<!--customjs-->
	<script type="text/javascript">
    $(function() {
        // this will get the full URL at the address bar
        var url = window.location.href;

        // passes on every "a" tag
        $(".main-menu a").each(function() {
            // checks if its the same on the address bar
            if (url == (this.href)) {
                $(this).closest("li").addClass("active");
				$(this).parents('li').addClass('parent-active');
            }
        });
    });   


</script>
<script>
	 jQuery(document).ready(function($) {
                'use strict';
				
// color box

//color
      jQuery('#style-selector').animate({
      left: '-213px'
    });

    jQuery('#style-selector a.close').click(function(e){
      e.preventDefault();
      var div = jQuery('#style-selector');
      if (div.css('left') === '-213px') {
        jQuery('#style-selector').animate({
          left: '0'
        });
        jQuery(this).removeClass('icon-angle-left');
        jQuery(this).addClass('icon-angle-right');
      } else {
        jQuery('#style-selector').animate({
          left: '-213px'
        });
        jQuery(this).removeClass('icon-angle-right');
        jQuery(this).addClass('icon-angle-left');
      }
    });
				});
	</script>
</body>
</html>