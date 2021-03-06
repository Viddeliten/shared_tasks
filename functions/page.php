<?php
/*	custom_page_display should try to display a page based on page $_GET['p'] and subpage $_GET['s'] and return TRUE on success or FALSE on fail	*/
function custom_page_display()
{
	if(isset($_GET['p']))
	{
		if(!strcmp($_GET['p'],"about"))
		{
			page_display_about();
			return TRUE;
		}
		else if(!strcmp($_GET['p'],"mytasks"))
		{
			task_display_user_tasks($_SESSION['user_id']);
			return TRUE;
		}
		else if(!strcmp($_GET['p'],"task"))
		{
			if(isset($_GET['s']))
			{
				if(!strcmp($_GET['s'],"add"))
				{
					task_display_add();
					return TRUE;
				}
				else if(!strcmp($_GET['s'],"edit"))
				{
					task_display_edit_single();
					return TRUE;
				}
			}
		}
		else if(!strcmp($_GET['p'],"category"))
		{
			if(isset($_GET['s']))
			{
				if(!strcmp($_GET['s'],"edit"))
				{
					category_display_edit();
					return TRUE;
				}
			}
		}
		else if(!strcmp($_GET['p'],"stuff"))
		{
			if(isset($_GET['s']))
			{
				if(!strcmp($_GET['s'],"users"))
				{
					echo "<h1>"._("Active users")."</h1>";
					user_display_active_users(FALSE);
					return TRUE;
				}
			}
		}
	}
	return FALSE;
}

function page_display_about()
{
	?>
	<div class="row">
		<div class="col-md-12 start-container jumbotron">
			<h1><?php echo sprintf(_("About this site"), SITE_NAME); ?></h1>
			<p class="lead"><?php echo SELLING_TEXT; ?></p>
			<h2><?php echo _("This is more text"); ?></h2>
			<p><?php echo _("It's just here for testing purposes."); ?></p>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 start-container">
			<h2><?php echo _("More text outside the jumbotron"); ?></h2>
			<p><?php echo _("This can for example be an informative text."); ?></p>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<h2><?php echo _("Some useful information"); ?></h2>
			<p><?php echo _("This is a half column."); ?></p>
		</div>
		<div class="col-md-6">
			<h2><?php echo _("Other stuff"); ?></h2>
			<p><?php echo _("This can for example be an informative text."); ?></p>
		</div>
	</div>
<?php
}
?>