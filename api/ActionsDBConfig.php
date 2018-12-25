<?php

global $mysqli;
$DBServer = "localhost";
$DBUser = "todon";
$DBPassword = "todon";
$DBSchema = "todon";

//Open database connection
function dbconnect()
{
  global $mysqli, $DBServer, $DBUser, $DBPassword, $DBSchema;
  $mysqli = new mysqli($DBServer,$DBUser,$DBPassword,$DBSchema);
  if ($mysqli->connect_errno) {
     $jTableResult = array();
     $jTableResult['Result'] = "ERROR";
     $jTableResult['Message'] = "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
     print json_encode($jTableResult);
     die("");
  }
}

function query($sql)
{
  //echo $sql."<p/>";
  global $mysqli;
  return $mysqli->query($sql);
}

// Close database connection
function dbclose()
{
  global $mysqli;
  mysqli_close($mysqli);
}

function lastid()
{
	global $mysqli;
	return mysqli_insert_id($mysqli);
}

function getSingleRow($sql)
{
  $q = query($sql);
  if (!$q) { $o = null; }
  else 
  {  $o = mysqli_fetch_assoc($q); }
  return $o;
}

function getRows($sql)
{
	$rows = array();
    $q = query($sql);
	while($row = mysqli_fetch_assoc($q)){
		array_push($rows, $row);
	}
	return $rows;
}

function find($sql, $dummy = false)  // Returns a single(first) field from a DB query
{
  $q = query($sql);
  	if(!$r = query($sql)){
		global $mysqli;
        die('There was an error running the query [' . $mysqli->error . '] { '. $sql.'}');
    }
  if (mysqli_num_rows($q) == 0) {
    if ($dummy)
      return $dummy;
    else
      return null;
  }
  $o = mysqli_fetch_array($q);
  return $o[0];
}

?>