<header class="main-header">
	<a href="<?php echo base_url(); ?>" class="logo">
		<span class="logo-mini"><i class="fa fa-home"></i></span>
		<span class="logo-lg"><b>Admin</b>SLIP</span>
	</a>
	<nav class="navbar navbar-static-top">
		<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</a>

		<div class="navbar-custom-menu">
			<ul class="nav navbar-nav">
				<li class="dropdown user user-menu">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<img src="<?php echo base_url(); ?>assets/dist/img/avatar5.png" class="user-image" alt="User Image">
						<span class="hidden-xs"><?php echo $this->session->userdata('name') ?></span>
					</a>
					<ul class="dropdown-menu">
						<li class="user-header">
							<img src="<?php echo base_url(); ?>assets/dist/img/avatar5.png" class="img-circle" alt="User Image">
							<p>
								<?php echo $this->session->userdata('department') ?>
								<!-- <a href="<?php echo base_url(); ?>login/logout" class="btn btn-default btn-flat">Sign out</a> -->
							</p>
						</li>
						<li class="user-footer">
							<!-- <div class="pull-left">
								<a href="<?php echo site_url('profile')?>" class="btn btn-default btn-flat">Profile</a>
							</div> -->
							<center>
								<a href="<?php echo base_url(); ?>login/logout" class="btn btn-default btn-flat">Sign out</a>
							</center>
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</nav>
</header>