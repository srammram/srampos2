<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>
	
/*ABDHUR RAHMAN*/
.inventory_header {padding: 0px 30px;}
.inventory_header h3 {color: #3c3c3c; font-size: 25px;}
.inventory_header hr {border-top: 1px solid #b4bcc3;}

.room-service {padding: 40px 15px;}
.room-service .room_service_detail{border: 1px solid #b7b7b7; border-radius: 15px; position: relative; float: left; width: 100%; text-decoration: none;padding: 15px 20px 10px;}
.room-service .room_service_detail img {margin-top: -50px;}
.room-service .room_service_detail h4 {text-align: right; margin-top: -10px; font-size: 35px; color: #575757;}
.room-service .room_service_detail p {text-align: right; font-size: 23px; color: #a3a3a3; font-weight: normal; margin: 0px;	transition: all 0.25s ease-in-out;}
.room_service_detail:hover p{color: #f79404;}

.house-keeping {padding: 40px 15px;}
.house-keeping .house_keeping_detail{border: 1px solid #b7b7b7; border-radius: 15px; position: relative; float: left; width: 100%; text-decoration: none;padding: 15px 20px 10px;}
.house-keeping .house_keeping_detail img {margin-top: -50px;}
.house-keeping .house_keeping_detail h4 {text-align: right; margin-top: -10px; font-size: 35px; color: #575757;}
.house-keeping .house_keeping_detail p {text-align: right; font-size: 23px; color: #a3a3a3; font-weight: normal; margin: 0px; transition: all 0.25s ease-in-out;}
.house_keeping_detail:hover p {color: #68c26c;}

.restaurent_inven {padding: 40px 15px;}
.restaurent_inven .restaurent_detail{border: 1px solid #b7b7b7; border-radius: 15px; position: relative; float: left; width: 100%; text-decoration: none;padding: 15px 20px 10px;}
.restaurent_inven .restaurent_detail img {margin-top: -50px;}
.restaurent_inven .restaurent_detail h4 {text-align: right; margin-top: -10px; font-size: 35px; color: #575757;}
.restaurent_inven .restaurent_detail p {text-align: right; font-size: 23px; color: #a3a3a3; font-weight: normal; margin: 0px; transition: all 0.25s ease-in-out;}
.restaurent_detail:hover p {color: #f77d75;}

.bar_inven {padding: 40px 15px;}
.bar_inven .bar_detail{border: 1px solid #b7b7b7; border-radius: 15px; position: relative; float: left; width: 100%; text-decoration: none;padding: 15px 20px 10px;}
.bar_inven .bar_detail img {margin-top: -50px;}
.bar_inven .bar_detail h4 {text-align: right; margin-top: -10px; font-size: 35px; color: #575757;}
.bar_inven .bar_detail p {text-align: right; font-size: 23px; color: #a3a3a3; font-weight: normal; margin: 0px; transition: all 0.25s ease-in-out;}
.bar_detail:hover p {color: #6abfb8;}


.meterial_inven {padding: 40px 15px;}
.meterial_inven .meterial_detail{border: 1px solid #b7b7b7; border-radius: 15px; position: relative; float: left; width: 100%; text-decoration: none;padding: 15px 20px 10px;}
.meterial_inven .meterial_detail img {margin-top: -50px;}
.meterial_inven .meterial_detail h4 {text-align: right; margin-top: -10px; font-size: 35px; color: #575757;}
.meterial_inven .meterial_detail p {text-align: right; font-size: 23px; color: #a3a3a3; font-weight: normal; margin: 0px; transition: all 0.25s ease-in-out;}
.meterial_detail:hover p {color: #686fc2;}


.purchase_inven {padding: 40px 15px;}
.purchase_inven .purchase_detail{border: 1px solid #b7b7b7; border-radius: 15px; position: relative; float: left; width: 100%; text-decoration: none;padding: 15px 20px 10px;}
.purchase_inven .purchase_detail img {margin-top: -50px;}
.purchase_inven .purchase_detail h4 {text-align: right; margin-top: -10px; font-size: 35px; color: #575757;}
.purchase_inven .purchase_detail p {text-align: right; font-size: 23px; color: #a3a3a3; font-weight: normal; margin: 0px; transition: all 0.25s ease-in-out;}
.purchase_detail:hover p {color: #9f739c;}


.store_inventory h3 {font-size: 25px; color: #3c3c3c;}
.sale_inventory h3 {font-size: 25px; color: #3c3c3c;}
.store_inventory hr, .sale_inventory hr{border-top: 1px solid #b4bcc3;}
.store_inventory ul, .sale_inventory ul {list-style: none;}
.store_inventory li {padding: 25px 0px;}
.store_inventory li a {text-decoration: none; border: 1px solid #3cb878; padding: 15px 0px 15px 18px; border-radius: 6px; color: #424242; font-size: 23px; transition: all 0.25s ease-in-out;}
.store_inventory li a span {color: #fff; background-color: #3cb878; padding: 15px 15px; border-top-right-radius: 4px; border-bottom-right-radius: 4px; border-top-left-radius: 20px; border-bottom-left-radius: 20px; margin-left: 7px; transition: all 0.25s ease-in-out;}
.store_inventory li a:hover {color: #3cb878; border-color: #424242;}
.store_inventory li a:hover span{color: #3cb878; background-color: #424242;}

.sale_inventory li {padding: 12px 0px;}
.sale_inventory li a {color: #a3a3a3; font-size: 20px; text-decoration: none; transition: all 0.25s ease-in-out; padding: 15px 0px;}
.sale_inventory li a span {padding-right: 15px;}
.sale_inventory li a span img {width: 60px; transition: all 0.25s ease-in-out;}

.sale_inventory li a:hover {color: #3cb878;}
.sale_inventory li a:hover img {-webkit-filter: drop-shadow(1px 5px 8px rgba(0,0,0,0.5)); filter: drop-shadow(1px 5px 8px rgba(0,0,0,0.5));}
</style>


<div class="col-sm-11 col-xs-12">
                        <div class="row">
                            <div class="col-lg-12 inventory_header">
                                <h3>Inventory</h3>
                                <hr>
                            </div>
                        </div>
							<div class="row">
                            	<div class="col-lg-12 col-xs-12">
                            		<div class="col-lg-4 room-service">
                            			<a href="#" class="room_service_detail">
											<img src="<?=$assets?>/images/room_service.png">
											<h4>58</h4>
											<p>Room service</p>
                            			</a>
                            		</div>
                            		<div class="col-lg-4 house-keeping">
                            			<a href="#" class="house_keeping_detail">
											<img src="<?=$assets?>/images/housekeeping.png">
											<h4>58</h4>
											<p>House keeping</p>
                            			</a>
                            		</div>
                            		<div class="col-lg-4 restaurent_inven">
                            			<a href="#" class="restaurent_detail">
											<img src="<?=$assets?>/images/restaurent.png">
											<h4>58</h4>
											<p>Restaurant</p>
                            			</a>
                            		</div>
                            		
                            		<div class="col-lg-4 bar_inven">
                            			<a href="#" class="bar_detail">
											<img src="<?=$assets?>/images/bar_inventory.png">
											<h4>58</h4>
											<p>Bar</p>
                            			</a>
                            		</div>
                            		
                            		<div class="col-lg-4 meterial_inven">
                            			<a href="#" class="meterial_detail">
											<img src="<?=$assets?>/images/meterial.png">
											<h4>58</h4>
											<p>Material Request</p>
                            			</a>
                            		</div>
                            		
                            		<div class="col-lg-4 purchase_inven">
                            			<a href="#" class="purchase_detail">
											<img src="<?=$assets?>/images/purchase.png">
											<h4>58</h4>
											<p>Purchase</p>
                            			</a>
                            		</div>
                            	</div>
                            </div>
							
							
							<div class="row">
								<div class="col-lg-12 col-xs-12">
									<div class="col-lg-6 store_inventory">
										<h3>Store Inventry</h3>
										<hr>
										
										<ul>
											<li><a href="#">Store name 1 <span>05</span></a></li>
											<li><a href="#">Store name 2 <span>05</span></a></li>
											<li><a href="#">Store name 3 <span>05</span></a></li>
										</ul>
									</div>
									<div class="col-lg-6 sale_inventory">
										<h3>Sale Inventry</h3>
										<hr>
										
										<ul>
											<li><a href="#"><span><img src="<?=$assets?>/images/res_sale_inventory.png"></span> Rerstaurant sale inventry</a></li>
											<li><a href="#"><span><img src="<?=$assets?>/images/bar_sale_inventory.png"></span> Bar sale inventry</a></li>
											<li><a href="#"><span><img src="<?=$assets?>/images/overall_inventory.png"></span> Overall inventry</a></li>
										</ul>
									</div>
								</div>
							</div>
	
</div>