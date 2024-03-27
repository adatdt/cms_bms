<?php
getSession();
$access_url    = checkUrlAccess($menu,$this->uri->uri_string());

if($access_url == 0){
	show_404();
	return false;
}
?>
<!DOCTYPE html>
<html lang="en">
<?php $this->load->view('common/head'); ?>

<style>
	/*.navbar-default .navbar-nav > li > a
	{
		color:white;
	}
@media (max-width: 768px){
	.navbar-default .navbar-nav .open .dropdown-menu > li > a
	{
		color:white;
	}
}*/
</style>
<body>

	<div class="navbar navbar-ap2 ">
		<div class="navbar-header" >
			<a class="navbar-brand" href="<?php echo base_url() ?>">
				<!--<img style=""  src="<?php echo base_url(); ?>assets/images/favico2.png" alt="AP 2"></a>-->

			<ul class="nav navbar-nav visible-xs-block">
				<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
				<li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
			</ul>

		</div>

		<div class="navbar-collapse collapse" id="navbar-mobile">
			<ul class="nav navbar-nav">
				<li><a class="sidebar-control sidebar-main-toggle hidden-xs" ><i class="icon-paragraph-justify3"></i></a></li>
			</ul>

			<ul class="nav navbar-nav navbar-right heading" >
				<li class="dropdown dropdown-user ">
					<a class="dropdown-toggle " data-toggle="dropdown">
						<!-- <img src="<?php echo base_url(); ?>assets/images/placeholder.jpg" alt=""> -->
						<span >
							<?php echo $this->session->userdata('full_name');?>
						</span>
						<i class="caret" ></i>
					</a>

					<ul class="dropdown-menu dropdown-menu-right ">
						<li>
							<a href="<?php echo site_url('profile')?>" ><i class="icon-user pull-right "></i>Profile</a>
							<a href="<?php echo base_url(); ?>login/logout"><i class="icon-switch2 pull-right "></i> Logout</a>
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</div>

	<div class="page-container">
		<div class="page-content">
			<?php $this->load->view('common/sidebar'); ?>
			<div class="content-wrapper">
				<!-- <div class="page-header page-header-default">
					<div class="breadcrumb-line"><a class="breadcrumb-elements-toggle"><i class="icon-menu-open"></i></a>
						<ul class="breadcrumb">
							<li><a href="<?php echo base_url(); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
							<li class="active"><?php echo $title ?></li>
						</ul>
					</div>
				</div> -->
				<div class="content" style="padding-top: 10px;">
					<?php $this->load->view($content); ?>
					<?php $this->load->view('common/footer'); ?>
				</div>
			</div>
		</div>
	</div>

</div>
</body>
</html>