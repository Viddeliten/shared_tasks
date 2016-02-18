<?php

function category_receive()
{
	if(isset($_POST['save_category']))
	{
		$sql="";
		if(isset($_POST['category_id']))
		{
			//Check that user has ownership here
			if(!category_get_user_privilege($_SESSION['user_id'], $_POST['category_id']))
				add_error((sprintf(_("Access denied (%s)"), 341053)));
			else
				$sql="UPDATE task_category SET 
				`name`='".sql_safe($_POST['name'])."',
				`description`='".sql_safe($_POST['description'])."'
				WHERE id=".sql_safe($_POST['category_id']).";";
		}
		else
			$sql="INSERT INTO task_category SET 
			creator='".sql_safe($_SESSION['user_id'])."',
			`name`='".sql_safe($_POST['name'])."',
			`description`='".sql_safe($_POST['description'])."';";
			
		if($sql!="")
		{
			message_try_mysql($sql,531220, _("Category saved"));
			// preprint($sql);
		}
	}
}

function category_add_empty()
{
	$sql="INSERT INTO task_category SET
	`creator`='".sql_safe($_SESSION['user_id'])."';";
	if(mysql_query($sql))
		return mysql_insert_id();
	else
		message_print_error(mysql_error());
	return FALSE;
}

function category_get($user_id, $category_id=NULL)
{
	$return=array();
	$sql="SELECT 
		task_category.id,
		task_category.creator,
		IFNULL(task_category.name,'"._("Untitled category")."') as name,
		task_category.description,
		task_category.assignment_length
	FROM task_category 
	LEFT JOIN task_user_category ON task_user_category.task_category_id=task_category.id
	WHERE ".($category_id!==NULL ? "task_category.id=".sql_safe($category_id)." AND " : "")."
	(creator=".sql_safe($user_id)."	OR task_user_category.user_id=".sql_safe($user_id).");";
	if($cc=mysql_query($sql))
	{
		while($c=mysql_fetch_assoc($cc))
		{
			$return[]=$c;
		}
	}
	return $return;
}

function category_get_edit_url($category_id)
{
	return SITE_URL."/?p=category&amp;s=edit&amp;id=".$category_id;
}

function category_get_edit_button($category_id)
{
	return '<a href="'.category_get_edit_url($category_id).'" class="btn small helpmarker" title="'._("Edit this category").'"><span class="glyphicon glyphicon-edit"></span></a>';
}

function category_display_edit()
{
	if(login_check_logged_in_mini()<1)
	{
		message_print_error(_("You need to be logged in to edit categories."));
		return false;
	}
	if(isset($_REQUEST['id']))
	{
		echo "<h1>"._("Editing category")."</h2>";
		category_display_form();
	}
}

function category_display_form()
{	
	if(isset($_POST['category_id']))
		$category_id=$_POST['category_id'];
	else if(isset($_GET['id']))
		$category_id=$_GET['id'];
	else
		$category_id=NULL;
	
	if($category_id!==NULL)
	{
		//Get existing category
		$category=category_get($_SESSION['user_id'], $category_id);
	}
	
	//get all available categories
	$categories=category_get($_SESSION['user_id']);
	
	echo '<form method="post" class="form-horizontal" '.(!isset($category[0]) ? ' action="'.SITE_URL.'/?p=mytasks"' :"").'>
		'.(isset($category[0]['id']) ? '<input type="hidden" name="category_id" value="'.$category[0]['id'].'">' : "").'
		<div class="form-group">
			<label for="name_text" class="col-sm-2 control-label">
				'._("Name").'
			</label>
			<div class="col-sm-10">
				<input id="name_text" class="form-control" type="text" name="name" 
						value="'.(isset($category[0]['name']) ? $category[0]['name'] : "").'"
						placeholder="'._("Enter category name").'" required="required">
			</div>
		</div>
		<div class="form-group">
			<label for="description_textarea" class="col-sm-2 control-label">
				'._("Description").'
			</label>
			<div class="col-sm-10">
				<textarea id="description_textarea" class="form-control" name="description" placeholder="'._("Enter category description").'">'.
					(isset($category[0]['description']) ? $category[0]['description'] : "").
				'</textarea>
			</div>
		</div>

		<div class="col-sm-10 col-sm-offset-2">
			<input type="submit" class="btn" name="save_category" value="'._("Save").'">
		</div>
	</form>';
}

function category_get_user_privilege($user_id, $task_id)
{
	$category=category_get($user_id, $task_id);
	if(!empty($category))
		return TRUE;
	return FALSE;
}
?>