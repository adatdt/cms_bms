<aside class="main-sidebar">
	<section class="sidebar">
		<div class="user-panel">
			<div class="pull-left image">
				<img src="<?php echo base_url(); ?>assets/dist/img/avatar5.png" class="img-circle" alt="User Image">
			</div>
			<div class="pull-left info" style="padding-top: 10px">
				<smal><?php echo $this->session->userdata('name') ?></smal><br>
				<small><?php echo $this->session->userdata('department') ?></small>
			</div>
		</div>
		<!-- <form action="#" method="get" class="sidebar-form">
			<div class="input-group">
				<input type="text" name="q" class="form-control" placeholder="Search...">
				<span class="input-group-btn">
					<button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
					</button>
				</span>
			</div>
		</form> -->
		<ul class="sidebar-menu" data-widget="tree">
			<li class="header">MAIN NAVIGATION</li>
			<?php if ($_SESSION['level'] == '1') { ?>
			<li class="">
				<a href="<?php echo site_url('dashboard'); ?>">
					<i class="fa fa-dashboard"></i> <span>Dashboard</span>
				</a>
			</li>
			<?php } ?>

			<li class="treeview">
				<a href="#">
					<i class="fa fa-ellipsis-h"></i> <span>List Data</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu">
					<li><a href="<?php echo site_url('salary') ?>"><i class="fa fa-money"></i>Salary</a></li>
					<li><a href="<?php echo site_url('employee') ?>"><i class="fa fa-user"></i>Employee</a></li>
				</ul>
			</li>

			<?php if ($_SESSION['level'] == '1') { ?>
			<li class="treeview">
				<a href="#">
					<i class="fa fa-plus"></i> <span>Input Data</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu">
					<li><a href="<?php echo site_url('salary/add') ?>"><i class="fa fa-money"></i>Salary</a></li>
					<li><a href="<?php echo site_url('employee/add') ?>"><i class="fa fa-user"></i>Employee</a></li>
				</ul>
			</li>
			<li class="treeview">
				<a href="#">
					<i class="fa fa-database"></i> <span>Data Master</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu">
					<li><a href="<?php echo site_url('salary/add') ?>"><i class="fa fa-ellipsis-h"></i>Deparment</a></li>
					<li><a href="<?php echo site_url('employee/add') ?>"><i class="fa fa-ellipsis-h"></i>Job Level</a></li>
					<li><a href="<?php echo site_url('employee/add') ?>"><i class="fa fa-ellipsis-h"></i>Position</a></li>
					<li><a href="<?php echo site_url('employee/add') ?>"><i class="fa fa-ellipsis-h"></i>Alokasi Gaji</a></li>
					<li><a href="<?php echo site_url('employee/add') ?>"><i class="fa fa-ellipsis-h"></i>Alokasi Gaji Project</a></li>
					<li><a href="<?php echo site_url('employee/add') ?>"><i class="fa fa-ellipsis-h"></i>Function</a></li>
				</ul>
			</li>
			<?php } ?>
		</ul>
	</section>
</aside>