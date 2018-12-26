<?php
//include_once "securityheader.php";
session_start();
$audit_execution = false; // True to save Insert/Update/Delete SQL statements in the ExecutionLog tables
$audit_select = false; // True to record select statements in the ExecutionLog table
$audit_history = false; // True to record all Insert/Update point in time records in <tablename>_history
include_once "ActionsDBConfig.php";

$tablename = "Mail";
$keyfield = "id";
$searchfields = Array("Name");
// Structure
//   [0] = FieldName
//   [1] = Field Type; P = Post, "" = Fixed Values: as per [2] - unquoted
//   [2] = Field Source
$insertfields = Array(Array("Name", "P", "Name"),Array("eMail","P","eMail"),Array("Message","P","Message"));
$updatefields = Array(Array("Name", "P", "Name"),Array("eMail","P","eMail"),Array("Message","P","Message"));
$filterfields = Array("");
// Structure = Value(ID), DisplayText(DataFieldName)
$optionfields = Array('id','Name');
$defaultsort = 'Name asc';


// Custom Validation Functions
function Validate($field, $value)
{
  // Throw an exception if field does not confrm to validation rules. Will not be called for Fixed value fields
  // If no validation required then dont add a rule
  /*if ($field == "Age")
  {
		if (!is_numeric($value))
  		  { throw new Exception("Age needs to be an Integer (".$value.")"); }
  }*/
}
// PostInsert, PreUpdate, PostUpdate, PreDelete - Use to create audit records if needed
// On Pre* function Throw Exception to stop execution
/*
function PreInsert()
{

}
function PreUpdate($id)
{

}
*/
function PostInsert($id)
{
	$msg = getSingleRow("select * from mail where id = $id");
	mail ( "cairnswm@gmail.com" , "Message from Tour Divide ToDoN" , $msg["Message"] . "\nFrom: ".$msg["Name"]."\neMail: ".$msg["eMail"] );
}
/*
function PostUpdate($id)
{

}                 */
function Action($action, $values)
{
	global $jTableResult;
	if ($action == "toggle")
	{
		if (isset($values["id"]))
		{ 
			$id = $values["id"];
			query("update ToDo set Status = 1 - Status where id = ".$id);
			$data = getSingleRow("select * from ToDo where id = ".$id);
			$jTableResult["Result"] = "OK";
			$jTableResult["Record"] = $data;
		}
		else
		{
			// Raise Error
			$jTableResult["Result"] = "ERROR";
			$jTableResult["Message"] = "No id recieved";
		}
	}
}

// End Custom Functions

include_once "TableActions.php";

?>