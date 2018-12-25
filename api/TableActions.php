<?php
// Valid actions
// list
// filter
// create
// update
// delete
// options
// getbyid

if (isset($_SESSION["userid"]))
{ $userid = $_SESSION["userid"]; }
else if (isset($_SESSION["user_id"]))
{ $userid = $_SESSION["user_id"]; }
else
{
  // TODO: Use Access token in header to get User id from user logins table
  $userid = "reporting";
}
// TODO: Record last action made by user
function History($table, $key, $id)
{
   global $mysqli, $userid;
   // Add Audit Record
   $hsql = "Insert into ".$table."_history
                 select 0,".$table.".*,'".$userid."',null
                 from ".$table." where ".$key." = ".$id.";";
   $result = $mysqli->query($hsql);
   return $hsql;
}
function executionlog($table, $action, $sql)
{
  global $mysqli, $userid;
  $asql = $mysqli->real_escape_string($sql);
  $asql = "INSERT INTO executionlog (SQLStatement, TableName, Action, ExecutedBy) VALUES ('".$asql."','".$table."','".$action."','".$userid."')";
  $result = $mysqli->query($asql);
  return $asql;
}
function SearchField($searchfield,$search,$searchtype)
{
  if ($searchtype == 'eq')   // Logic for EQUAL fields
  {
    if ($search == "null") { $search = ""; }
    if (!isset($search)) { $search = ""; }
    if ($search != "") { $res = $searchfield." = '".$search."' "; }
    else { $res = $searchfield." like '%".$search."%' "; }
  }
  else  if ($searchtype == 'yr')  // logic for YEARf ields - should be same as for dates.
  {
    if ($search == "null") { $search = ""; }
    if (!isset($search)) { $search = ""; }
    if ($search != "") { $res = $searchfield." = '".$search."' "; }
    else { $res = " 1 = 1 "; }
  }
  else

  {
    $res = $searchfield." like '%".$search."%' ";
  }
  return $res;
}

// ------------- Check user Token -------------
function PreLoad($Action, $POST)
{
	if (!isset($_SESSION["role_id"]))
	{
		if (isset($_SERVER["HTTP_TOKEN"]))
		{
			$token = $_SERVER["HTTP_TOKEN"];
			// Verify from user table the users rights etc
			$result = query("select u.USERNAME, u.NAME, u.SURNAME, USER_ID, USER_ROLES_ID from userlogins ul, users u where access_token = '$token' and ul.username = u.username");
			while($row = $result->fetch_assoc())
			{
				$_SESSION['username'] = $row["USERNAME"];
				$_SESSION['user_id'] = $row["USER_ID"];
				$_SESSION['role_id'] = $row["USER_ROLES_ID"];
				$_SESSION['fullname'] = $row["NAME"]." ".$row["SURNAME"];
			}	
		}
	}
}

try
{
        dbconnect();		
        $jTableResult = array();
		//$jTableResult["SERVER"] = $_SERVER;
        //$jTableResult["Action"] = $_GET["action"];
        $sql = '';
        $where = '';
        $limit = '';
        $order = '';

	//Getting records (listAction)
	$action = "";
	if (isset($_GET["action"])) { $action = $_GET["action"]; } else { $action = "list"; }
	if (function_exists("PreLoad")) { PreLoad($action, $_POST); }
    if (!in_array($action, ["list","filter","create","update","delete","options","getbyid"]))
	{
		if (function_exists("Action")) { Action($action, $_POST); }
	}
	if($action == "list")
	{
		//Get records from database
		$search = Array();
                for ($i=0;$i<count($searchfields);$i++)
                {
                  if (isset($_POST[$searchfields[$i]]))
                  {
                    $search[$searchfields[$i]] = $_POST[$searchfields[$i]];
                  }
                  else if (isset($_GET[$searchfields[$i]]))
                  {
                    $search[$searchfields[$i]] = $_GET[$searchfields[$i]];
                  }
                  else
                  {
                    $search[$searchfields[$i]] = "";
                  }
                }
                if (isset($_GET["jtSorting"])) { $sort = $_GET["jtSorting"]; }  else { (isset($defaultsort) ? $sort = $defaultsort : $sort = ""); }
                if (isset($_GET["jtStartIndex"])) { $start = $_GET["jtStartIndex"]; } else { $start = ""; }
                if (isset($_GET["jtPageSize"])) { $pagesize = $_GET["jtPageSize"]; } else { $pagesize = ""; }
		if (empty($start)) { $start = 0; }
		if (empty($pagesize)) { $pagesize = 10; }


		$sql = "SELECT * FROM $tablename ";
		// Check if search needed
		if (!empty($search))
                { $where .= "where ".SearchField($searchfields[0],$search[$searchfields[0]],isset($searchtype) ? $searchtype[0] : "");
                  if (count($searchfields) > 1)
                  {
                    for ($i=1;$i<count($searchfields);$i++)
                    {
                        $where .= "and ".SearchField($searchfields[$i],$search[$searchfields[$i]],isset($searchtype) ? $searchtype[$i] : "");
                    }
                  }
                }

		// check if sorting needed
                if (!empty($sort)) { $order .= " ORDER BY " . $sort; } else { $order .= "ORDER BY ". $defaultsort; }
                $limit .= " LIMIT " . $start . "," . $pagesize . ";";

//        	if ($audit_select) { $asql = executionlog($tablename, 'Test', $sql.$where.$order.$limit); $jTableResult['Audit'] = $asql; }
//        	echo $sql;

		//Get record count
		$countsql = "SELECT COUNT(*) AS RecordCount FROM $tablename ";
		//echo $sql;
        if ($audit_select) {executionlog($tablename, 'Count Select', $countsql.$where); }
		$result = $mysqli->query($countsql.$where);
		$row = $result->fetch_assoc();
		$recordCount = $row['RecordCount'];


		//Add all records to an array
       	if ($audit_select) { $asql = executionlog($tablename, 'Select', $sql.$where.$order.$limit); $jTableResult['Audit'] = $asql; }
		//echo $sql.$where.$order.$limit;
		$result = $mysqli->query($sql.$where.$order.$limit);
		$rows = array();
		while($row = $result->fetch_assoc())
		{
		    $rows[] = $row;
		}

		//Return result to jTable
		$jTableResult['Result'] = "OK";
		$jTableResult['TotalRecordCount'] = $recordCount;
		$jTableResult['Records'] = $rows;
	}
	//Getting records (filterAction) Used for sub tables
	if($action == "filter")
	{
		//Get records from database
		$search = Array();
		//$search[$searchfields[0]] = $_POST[$searchfields[0]];
                for ($i=0;$i<count($filterfields);$i++)
                {
                  if (isset($_GET[$filterfields[$i]]))
                  {
                    $search[$filterfields[$i]] = $_GET[$filterfields[$i]];
                  }
                }
                if (isset($_GET["jtSorting"])) { $sort = $_GET["jtSorting"]; } else { $sort = ""; }
                if (isset($_GET["jtStartIndex"])) { $start = $_GET["jtStartIndex"]; } else { $start = ""; }
                if (isset($_GET["jtPageSize"])) { $pagesize = $_GET["jtPageSize"]; } else { $pagesize = ""; }
		if (empty($start)) { $start = 0; }
		if (empty($pagesize)) { $pagesize = 10; }

		$sql = "SELECT * FROM $tablename ";
		// Check if search needed
		if (!empty($search))
		{ $where .= "where ";
		  $c = 0;
                  foreach ($search as $key => $value)
                  {
                      if ($c > 0) { $where .= " and "; }
                      $where .= $key." = '$value' ";
                      $c++;
                  }
                }
		// check if sorting needed
                if (!empty($sort)) { $order .= " ORDER BY " . $sort; }
                $limit .= " LIMIT " . $start . "," . $pagesize . ";";

		//Get record count
		$countsql = "SELECT COUNT(*) AS RecordCount FROM $tablename ";
		//echo $countsql.$where;
		$result = $mysqli->query($countsql.$where);
		$row = $result->fetch_assoc();
		$recordCount = $row['RecordCount'];

                // Get Records
        	$result = $mysqli->query($sql.$where.$order.$limit);

        	if ($audit_select) { $asql = executionlog($tablename, 'Select', $sql.$where.$order.$limit); $jTableResult['Audit'] = $asql; }

		//Add all records to an array
		$rows = array();
		while($row = $result->fetch_assoc())
		{
		    $rows[] = $row;
		}

		//Return result to jTable
		$jTableResult['Result'] = "OK";
		$jTableResult['TotalRecordCount'] = $recordCount;
		$jTableResult['Records'] = $rows;
		//$jTableResult['SQL'] = $sql;
	}
	//Creating a new record (createAction)
	else if($action == "create")
	{
		if (function_exists("PreInsert"))
		{
                  PreInsert();
                }
                // Prepare Fields
		$fields = "";
                $values = "";
		for ($i=0;$i<count($insertfields);$i++)
		{ 
                  if (isset($_POST[$insertfields[$i][2]]))
  		  {
                    if (strlen($fields) > 0) { $fields .= ", "; }
                    $fields .= $insertfields[$i][0];
                    if (strlen($values) > 0) { $values .= ", "; }
      		    if ($insertfields[$i][1] == "P") {
                            if (function_exists("Validate"))
                            {
                              Validate($insertfields[$i][0],$_POST[$insertfields[$i][2]]);
                            }
                            $values .= "'".$_POST[$insertfields[$i][2]]."'"; }
    		    if ($insertfields[$i][1] == "") { $values .= $insertfields[$i][2]; }
		  }
                }
		//Insert record into database
		$sql = "INSERT INTO $tablename (".$fields.") VALUES (".$values.")";
		$result = $mysqli->query($sql);

		//Get last inserted record (to return to jTable)
		$asql = "SELECT * FROM $tablename WHERE $keyfield = LAST_INSERT_ID();";
		$result = $mysqli->query($asql);
		$row = $result->fetch_assoc();

                $jTableResult['SQL'] = $sql;

                if ($audit_execution) { executionlog($tablename, 'Insert', $sql); $jTableResult['Audit'] = $asql; }

                if ($result === false)
                {
                  throw new Exception(mysqli_error($mysqli));
                }

		//Return result to jTable
		$jTableResult['Result'] = "OK";
		$jTableResult['Record'] = $row;
		$jTableResult['Post'] = $_POST;

                if ($audit_history) { $jTableResult['historysql'] = History($tablename, $keyfield, $row[$keyfield]); }

		if (function_exists("PostInsert"))
		{
                  PostInsert($row[$keyfield]);
                }
                //var_dump($jTableResult);

	}
	//Updating a record (updateAction)
	else if($action == "update")
	{
		if (function_exists("PreUpdate"))
		{
		    PreUpdate($_POST[$keyfield]);
		}

                // Prepare Fields
                $fields = "";
		for ($i=0;$i<count($updatefields);$i++)
		{
			if (isset($_POST[$updatefields[$i][2]]))
			{
				if (strlen($fields) > 0) { $fields .= ", "; }
				$fields .= $updatefields[$i][0]." = ";
				if ($updatefields[$i][1] == "P") { //Validate($updatefields[$i][0],$_POST[$updatefields[$i][2]]);
				   $fields .= "'".$_POST[$updatefields[$i][2]]."'"; }
				if ($updatefields[$i][1] == "") { $fields .= $updatefields[$i][2]; }
		    }
		}

		//Update record in database
		//$result = $mysqli->query("UPDATE $tablename SET Name = '" . $_POST["Name"] . "', Age = " . $_POST["Age"] . " WHERE $keyfield = " . $_POST[$keyfield] . ";");
		$sql = "UPDATE $tablename SET ".$fields." WHERE $keyfield = " . $_POST[$keyfield] . ";";
		$asql = $mysqli->real_escape_string($sql);
		$jTableResult['sql'] = $asql;
		if ($audit_execution) { executionlog($tablename, 'Update', $sql); $jTableResult['Audit'] = $asql; }

		$result = $mysqli->query($sql);

		if ($result === false)
		{
		  throw new Exception(mysqli_error($mysqli));
		}
                
		//Get updated record (to return to jTable)
		$result = $mysqli->query("SELECT * FROM $tablename WHERE $keyfield = " . $_POST[$keyfield] . ";");
		$row = $result->fetch_assoc();

		if ($audit_history) { $jTableResult['historysql'] = History($tablename, $keyfield, $_POST[$keyfield]); }

		if (function_exists("PostUpdate"))
		{
		  PostUpdate($_POST[$keyfield]);
		}

		//Return result to jTable
		$jTableResult['Result'] = "OK";
		$jTableResult['Record'] = $row;
	}
	//Deleting a record (deleteAction)
	else if($action == "delete")
	{
		if (function_exists("PreDelete"))
		{
                  PreDelete($_POST[$keyfield]);
                }
		//Delete from database
		$sql = "DELETE FROM $tablename WHERE $keyfield = " . $_POST[$keyfield] . ";";
		$result = $mysqli->query($sql);

                if ($audit_execution) { executionlog($tablename, 'Delete', $sql); $jTableResult['Audit'] = $asql; }

		if (function_exists("PostDelete"))
		{
                  PostDelete($_POST[$keyfield]);
                }
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
	}
	else if($action == "options")
	{
                $sql .= "SELECT ".$optionfields[0]." Value, ".$optionfields[1]." DisplayText from ".$tablename;
                if (isset($defaultsort)) { $sql .= " order by ".$defaultsort; }
                $sql .= ";";
                //echo $sql;
		$result = $mysqli->query($sql);

		if ($audit_select) { $asql = executionlog($tablename, 'Options', $sql); $jTableResult['Audit'] = $asql; }

		//Add all records to an array
		$rows = array();
		while($row = $result->fetch_assoc())
		{
		    $rows[] = $row;
		}

		//Return result to jTable
		$jTableResult['Result'] = "OK";
		$jTableResult['Options'] = $rows;
	}
	else if($action == "getbyid")
	{
		//Get last inserted record (to return to jTable)
		$sql = "SELECT * FROM $tablename WHERE $keyfield = " . $_GET[$keyfield] . ";";
		//echo $sql;
		if ($audit_select) { $asql = executionlog($tablename, 'GetByID', $sql); $jTableResult['Audit'] = $sql; }
		$result = $mysqli->query($sql);
		$row = $result->fetch_assoc();


		//Return result to jTable
		$jTableResult['Result'] = "OK";
		$jTableResult['Record'] = $row;
		//$jTableResult['sql'] = $sql;
	}

	//Close database connection
	dbclose();
}
catch(Exception $ex)
{
    //Return error message

	$jTableResult['Result'] = "ERROR";
	$jTableResult['Message'] = $ex->getMessage();
}
	print json_encode($jTableResult);

?>