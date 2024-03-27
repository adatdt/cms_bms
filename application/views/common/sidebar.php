<div class="sidebar sidebar-main sidebar-default">
	<div class="sidebar-content">
		<div class="sidebar-user">
			<div class="category-content">
				<div class="media">
					<a href="#" class="media-left"><img src="<?php echo base_url(); ?>assets/images/favico2.png" class="img-circle img-sm" alt=""></a>
					<div class="media-body">
						<span class="media-heading text-semibold">
							<?php echo $this->session->userdata('full_name');?>
						</span>
						<div class="text-size-mini text-muted" style="color: black">
							<i class="icon-vcard text-size-small"></i> &nbsp;
							<?php echo $this->session->userdata('group_name');?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="sidebar-category sidebar-category-visible">
			<div class="category-content no-padding" id="sidebar">
				<!-- <ul class="navigation navigation-main navigation-accordion"> -->
				<?php
					//echo "<ul class='navigation navigation-main navigation-accordion'><li class='navigation-header'><span>Main</span> <i class='icon-menu' title='Main pages'></i></li></ul>";
					$listMenu = listMenu($menu);
					echo $listMenu;
				 ?>
			</div>
		</div>
	</div>
</div>