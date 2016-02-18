<?php

function task_receive()
{
	if(isset($_REQUEST['task_delete']))
	{
		//Check the user is logged in and see if it is the owner
		if(login_check_logged_in_mini()>0)
		{
			$task=task_get($_SESSION['user_id'],$_REQUEST['task_delete']);
			if(!empty($task))
			{
				$sql="DELETE FROM task WHERE id=".$task[0]['id'].";";
				message_try_mysql($sql, 152023, _("Task successfully removed"));
			}
		}
		else
			echo "no";
	}
	
	if(isset($_POST['save_task']))
	{
		if($_POST['task_category_id']=="NULL")
			$task_category_id=category_add_empty();
		else
			$task_category_id=$_POST['task_category_id'];
		
		if($task_category_id)
		{
			$sql="";
			if(isset($_POST['task_id']))
			{
				//Check that user has ownership here
				if(!task_get_user_privilege($_SESSION['user_id'], $_POST['task_id']))
					add_error((sprintf(_("Access denied (%s)"), 341053)));
				else
					$sql="UPDATE task SET 
					`task_category_id`='".sql_safe($task_category_id)."',
					`name`='".sql_safe($_POST['name'])."',
					`description`='".sql_safe($_POST['description'])."'
					WHERE id=".sql_safe($_POST['task_id']).";";
			}
			else
				$sql="INSERT INTO task SET 
				creator='".sql_safe($_SESSION['user_id'])."',
				`task_category_id`='".sql_safe($task_category_id)."',
				`name`='".sql_safe($_POST['name'])."',
				`description`='".sql_safe($_POST['description'])."';";
				
			if($sql!="")
			{
				message_try_mysql($sql,531220, _("Task saved"));
			}
		}
	}
}

function task_display_user_tasks($user_id)
{
	echo "<h1>"._("Tasks")."</h1>";
	
	//Button to get to "add new task"-page
	task_display_add_button();
	
	//List all tasks we are allowed to edit, with edit-button
	$tasks=task_get($user_id);
	if(!empty($tasks))
	{
		$category_id=$tasks[0]['task_category_id'];
		echo '<div class="category">';
		echo '<h2>'.$tasks[0]['category_name']." ".category_get_edit_button($category_id).'</h2>';
		echo '<p>'.$tasks[0]['category_description'].'</p>';
		echo '<div class="row">';
		$i=0;
		foreach($tasks as $key => $task)
		{
			if($category_id!==$task['task_category_id'])
			{
				echo '</div>';
				echo '</div>';
				echo '<div class="category">';
				echo '<h2>'.$task['category_name']." ".category_get_edit_button($task['task_category_id']).'</h2>';
				echo '<div class="row">';
				$category_id=$task['task_category_id'];
			}
			if($i>0 && $i%4==0)
				echo '</div><div class="row">';
			
			echo '<div class="col-md-3 task well">
				<h3>'.$task['name'].'</h3>
				<p class="author">'.sprintf(_("created by %s"),user_get_link($task['creator'])).' '.date("Y-m-d H:i", strtotime($task['created'])).'</p>
				<p>'.$task['description'].'</p>
				<div>
					'.task_get_delete_button($task['id']).'
					<a href="'.task_edit_url($task['id']).'" class="btn small helpmarker" title="'._("Edit this task").'"><span class="glyphicon glyphicon-edit"></span></a>
				</div>
			</div>';
		}
		echo '</div>';
		echo '</div>';
	}
	
}

function task_get_delete_button($task_id)
{
	return '<a onclick="return confirm(\''._("Do you really want to delete the task ".task_get_name($task_id)).'\');" href="'.task_delete_url($task_id).'" class="btn small btn-danger helpmarker" title="'._("Delete this task").'"><span class="glyphicon glyphicon-erase"></span></a>';
}

function task_get($user_id, $task_id=NULL)
{
	$return=array();
	$sql="SELECT 
		task.*,
		IFNULL(task_category.name,'"._("Untitled category")."') as category_name,
		task_category.description as category_description
	FROM task 
	LEFT JOIN task_category ON task_category.id=task.task_category_id
	LEFT JOIN task_user_category ON task_user_category.task_category_id=task.task_category_id
	WHERE ".($task_id!==NULL ? "task.id=".sql_safe($task_id)." AND " : "")."
	(task.creator=".sql_safe($user_id)."	OR task_user_category.user_id=".sql_safe($user_id).")
	ORDER BY task.task_category_id;";
	if($cc=mysql_query($sql))
	{
		while($c=mysql_fetch_assoc($cc))
		{
			$return[]=$c;
		}
	}
	return $return;
}

function task_get_user_privilege($user_id, $task_id)
{
	$task=task_get($user_id, $task_id);
	if(!empty($task))
		return TRUE;
	return FALSE;
}

function task_get_name($task_id)
{
	$sql="SELECT 
		name
	FROM task 
	WHERE id=".sql_safe($task_id).";";
	if($cc=mysql_query($sql))
	{
		if($c=mysql_fetch_assoc($cc))
		{
			return $c['name'];
		}
	}
	return NULL;
}

function task_display_add_button()
{
	echo '<a href="'.task_add_url().'" class="btn">'._("New task +").'</a>';
}
function task_add_url()
{
	return SITE_URL."/?p=task&amp;s=add";
}
function task_edit_url($task_id)
{
	return SITE_URL."/?p=task&amp;s=edit&amp;id=".$task_id;
}
function task_delete_url($task_id)
{
	return add_get_to_URL("task_delete", $task_id);
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
function task_display_edit_single()
{
	if(login_check_logged_in_mini()<1)
	{
		message_print_error(_("You need to be logged in to edit tasks."));
		return false;
	}
	if(isset($_REQUEST['id']))
	{
		echo "<h1>"._("Editing task")."</h2>";
		task_display_form();
	}
}

function task_display_form()
{	
	if(isset($_POST['task_id']))
		$task_id=$_POST['task_id'];
	else if(isset($_GET['id']))
		$task_id=$_GET['id'];
	else
		$task_id=NULL;
	
	if($task_id!==NULL)
	{
		//Get existing task
		$task=task_get($_SESSION['user_id'], $task_id);
		// preprint($task);
	}
	
	//get all available categories
	$categories=category_get($_SESSION['user_id']);
	
	echo '<form method="post" class="form-horizontal" '.(!isset($task[0]) ? ' action="'.SITE_URL.'/?p=mytasks"' :"").'>
		'.(isset($task[0]['id']) ? '<input type="hidden" name="task_id" value="'.$task[0]['id'].'">' : "").'
		<div class="form-group">
			<label for="task_category_id_select" class="col-sm-2 control-label">
				'._("Category").'
			</label>
			<div class="col-sm-10">
				<select name="task_category_id" id="task_category_id_select" class="form-control">';
	if(!empty($categories))
	{
		foreach($categories as $cat)
		{
			echo '<option value="'.$cat['id'].'" '.(isset($task[0]['task_category_id']) && $task[0]['task_category_id']==$cat['id'] ? 'selected="selected" ' : "").'>'.$cat['name'].'</option>';
		}
	}
	echo '
					<option value="NULL"> - '._("New (untitled) category").' - </option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="name_text" class="col-sm-2 control-label">
				'._("Name").'
			</label>
			<div class="col-sm-10">
				<input id="name_text" class="form-control" type="text" name="name" 
						value="'.(isset($task[0]['name']) ? $task[0]['name'] : "").'"
						placeholder="'._("Enter task name").'" required="required">
			</div>
		</div>
		<div class="form-group">
			<label for="description_textarea" class="col-sm-2 control-label">
				'._("Description").'
			</label>
			<div class="col-sm-10">
				<textarea id="description_textarea" class="form-control" name="description" placeholder="'._("Enter task description").'">'.
					(isset($task[0]['description']) ? $task[0]['description'] : "").
				'</textarea>
			</div>
		</div>

		<div class="col-sm-10 col-sm-offset-2">
			<input type="submit" class="btn" name="save_task" value="'._("Save").'">
		</div>
	</form>';
}

?>