<?php

function task_display_edit()
{
	echo "<h1>"._("Edit tasks")."</h2>";
	
	//Button to get to "add new task"-page
	task_display_add_button();
	
	//List all tasks we are allowed to edit, with edit-button
	
}

function task_display_add_button()
{
	echo '<a href="'.task_add_url().'" class="btn">'._("New task +").'</a>';
}
function task_add_url()
{
	return SITE_URL."/?p=task&amp;s=add";
}

function task_display_add()
{
	if(login_check_logged_in_mini()<1)
	{
		message_print_error(_("You need to be logged in to add tasks."));
		return false;
	}
	echo "<h1>"._("Adding task")."</h2>";
	task_display_form();
}

function task_display_form($task_id=NULL)
{
	if(isset($_POST['save_task']))
	{
		if($_POST['task_category_id']=="NULL")
			$task_category_id=category_add_empty();
		else
			$task_category_id=$_POST['task_category_id'];
		
		if($task_category_id)
		{
			$sql="INSERT INTO task SET 
			creator='".sql_safe($_SESSION['user_id'])."',
			`task_category_id`='".sql_safe($task_category_id)."',
			`name`='".sql_safe($_POST['name'])."',
			`description`='".sql_safe($_POST['description'])."';";
			message_try_mysql($sql,531220, _("Task saved"), TRUE);
		}
	}
	
	if($task_id!==NULL)
	{
		//Get existing task
	}
	
	//task_category_id, name, description
	
	//get all available categories
	$categories=category_get($_SESSION['user_id']);
	
	echo '<form method="post">
		<label for="task_category_id_select">'._("Category").'</label>
		<select name="task_category_id" id="task_category_id_select">';
	if(!empty($categories))
	{
		foreach($categories as $cat)
		{
			echo '<option value="'.$cat['id'].'">'.$cat['name'].'</option>';
		}
	}
	echo '
			<option value="NULL"> - '._("New (untitled) category").' - </option>
		</select>
		
		<label for="name_text">'._("Name").'</label>
		<input id="name_text" type="text" name="name" value="" placeholder="'._("Enter task name").'" required="required">

		<label for="description_textarea">'._("Description").'</label>
		<textarea id="description_textarea" name="description" placeholder="'._("Enter task description").'"></textarea>
		
		<input type="submit" class="btn" name="save_task" value="'._("Save").'">
	</form>';
}

?>