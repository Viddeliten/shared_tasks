<?php

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
?>