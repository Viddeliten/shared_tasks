<?php

/********************************/
/*		Available pages			*/
/********************************/
//Slugs need to be untranslated or coder will have hell in case of many languages in translation!
define('CUSTOM_PAGES_ARRAY',serialize(array(
	//name	=>	content
	_("Edit")							=>	array(	"slug"	=>	"edit",
													"req_user_level"	=>	5,
													"subpages"		=>	array(
																				_("Tasks")	=>	array(	"slug"	=>	"tasks",
																											"req_user_level"	=>	5	//This will be seen by logged in people
																										),
																				_("Categories")	=>	array(	"slug"	=>	"cat",
																											"req_user_level"	=>	2	//This will be seen by admins
																										)
																			)
												),
	_("Admin")					=>	array(	"slug"				=>	"admin", //This will only affect the admin-menu! Subpages will be added to the regular admin menu.
													// "req_user_level"	=>	5,		// For this condition to be true, slug needs to be exactly "admin". req_user_level is not nessessary
													"subpages"		=>	array(
																			)
												)
)));

/********************************/
/*		Extra settings			*/
/********************************/
// define('CUSTOM_SETTINGS',serialize(array(
	// "flattr"	=>	array(	"some_object" => _("Some object"))
	// )));
?>