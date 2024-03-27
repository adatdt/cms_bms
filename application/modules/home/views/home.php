<!-- <div class="content">
	<div class="panel"> 
		<div class="panel-flat">
			<h4 class="text-center">
				<img src="http://103.107.68.163/logo.jpg" alt="" style="padding-top: 100px">
				<img src="<?php echo base_url(); ?>assets/images/dua.jpeg" alt="" style="max-height: 900px;max-width: 900px;">
			</h4>
		</div>
	</div>
</div> -->
<style>

.carousel-control.left{
	background-image: linear-gradient(to right, rgba(0, 0, 0, 0.0001) 0%, rgba(0, 0, 0, 0.0001) 100%);
}
.carousel-control.right{
	background-image: linear-gradient(to right,  rgba(0, 0, 0, 0.0001) 0%, rgba(0, 0, 0, 0.0001) 100%);
}
.carousel-indicators li {
  width: 30px;
  height: 10px;
  border-radius:0%;
}

.carousel-indicators .active {
  width: 30px;
  height: 11px;
  border-radius:0%;
  background-color: orange;
  border-color:orange;
}
/*.carousel-control *:before{
	color:orange;
}
*/
</style>



<div class="panel"> 
<!-- <div class="panel-flat" style="padding:3px"> -->
			<!-- 	<div class="col-md-8 col-md-offset-2"> -->
	<!-- <div id="myCarousel" class="carousel slide" data-ride="carousel"> -->
	<!-- inicator -->
	<!-- <ol class="carousel-indicators">
		<li data-target="#myCarousel" data-slide-to=0 class="active" ></li>
		<li data-target="#myCarousel" data-slide-to=1 ></li>
		<li data-target="#myCarousel" data-slide-to=2 ></li>
		<li data-target="#myCarousel" data-slide-to=3 ></li>
		<li data-target="#myCarousel" data-slide-to=4 ></li>
		<li data-target="#myCarousel" data-slide-to=5 ></li>
		<li data-target="#myCarousel" data-slide-to=6 ></li>
	</ol> -->

	<!-- declarasi carousel -->
<!-- 	<div class="carousel-inner" roll="listbox">
		<div class="item active">
			<img src="<?php echo base_url(); ?>assets/images/slide/dua.jpeg" style="height:410px; width:100% ">
			<div class="carousel-caption">
			</div>
		</div>

		<div class="item ">
			<img src="<?php echo base_url(); ?>assets/images/slide/img1.jpg" style="height:410px; width:100% ">
			<div class="carousel-caption">

			</div>
		</div>

		<div class="item ">
			<img src="<?php echo base_url(); ?>assets/images/slide/img2.jpg" style="height:410px; width:100% ">
			<div class="carousel-caption">
			</div>
		</div>

		<div class="item ">
			<img src="<?php echo base_url(); ?>assets/images/slide/img3.jpg" style="height:410px; width:100% ">
			<div class="carousel-caption">
			</div>
		</div>

		<div class="item ">
			<img src="<?php echo base_url(); ?>assets/images/slide/img5.jpg" style="height:410px; width:100% ">
			<div class="carousel-caption">
			</div>
		</div>

		<div class="item ">
			<img src="<?php echo base_url(); ?>assets/images/slide/img6.jpg" style="height:410px; width:100% ">
			<div class="carousel-caption">
			</div>
		</div>

		<div class="item ">
			<img src="<?php echo base_url(); ?>assets/images/slide/img4.jpeg" style="height:410px; width:100% ">
			<div class="carousel-caption">
			</div>
		</div>
	</div> -->
		<!-- <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
			<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
			<span class="sr-only">Previous</span>
		</a>
		<a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
			<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
			<span class="sr-only">Next</span>
		</a>
	</div>	 -->
<!-- </div> -->
</div>

<script type="text/javascript">

	$(document).ready(function(){
		$('.carousel').carousel({
  		interval:3000
		});
	});
</script>