<?php

display_topline_menu("navbar-default"); ?>

	
<div class="container" id="main_container">
	
	<div id="content" class="row">
		<?php 
			display_conditional_login();
		?>
		<?php 
		
		include("content.php"); ?>
		<div class="clearfix"></div>
	</div>

	<?php //Footer
	display_footer(); ?>
	
	
	
</div><!-- /.container -->