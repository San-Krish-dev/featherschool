<?php

include('ajaxconfig.php');

$column = array(
    'studentname',
    'rollnumber',
     
    'checks', 
    'status',
    'finalresult'
);

$query = "SELECT * FROM sturollbacktwo WHERE 1" ;
if(isset($_POST['search'])!="");
{
if (isset($_POST['search'])) {

	if($_POST['search']=="Active")
{
	$query .="and status=0 ";
	
}
else if($_POST['search']=="Inactive")
{
	$query .="and status=1 ";
}
else{	
   $query .= "
 OR studentname LIKE '%".$_POST['search']."%'
 OR rollnumber LIKE '%".$_POST['search']."%'

 OR checks LIKE '%".$_POST['search']."%'
 OR finalresult LIKE '%".$_POST['search']."%'
 ";
}
}
}

if (isset($_POST['order'])) {
    $query .= 'ORDER BY ' . $column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'] . ' ';
} else {
    $query .= ' ';
}

$query1 = '';

if (isset($_POST['length']) != -1) {
    $query1 = 'LIMIT ' . isset($_POST['start']) . ', ' . isset($_POST['length']);
}

$statement = $connect->prepare($query);

$statement->execute();

$number_filter_row = $statement->rowCount();

$statement = $connect->prepare($query . $query1);

$statement->execute();

$result = $statement->fetchAll();

$data = array();

foreach ($result as $row) {
    $sub_array   = array();
    $sub_array[] = $row['studentname'];
    $sub_array[] = $row['rollnumber'];   
    
    $sub_array[] = $row['checks'];
    $sub_array[] = $row['finalresult'];
    $status = $row['status'];
    if($status==1)
	{
	$sub_array[]="<span style='width: 144px;'><span class='kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill'>Inactive</span></span>";
	}
	else
	{
    $sub_array[]="<span style='width: 144px;'><span class='kt-badge  kt-badge--success kt-badge--inline kt-badge--pill'>Active</span></span>";
	}
	$id          = $row['stuid'];
	
	$action="<a href='studentrollback&upd=$id' title='Edit details'><button type='button' class='btn btn-icon btn-sm' title='Edit'><i class='fa fa-edit'></i></button></a>
	<a href='studentrollback&del=$id' title='Edit details'><button type='button' class='btn btn-icon btn-sm js-sweetalert' title='Delete' data-type='confirm'><i class='fa fa-trash-o text-danger'></i></button></a>";

	
	$sub_array[] = $action;
    $data[]      = $sub_array;
}

function count_all_data($connect)
{
    $query     = "SELECT * FROM sturollbacktwo WHERE 1" ;
    $statement = $connect->prepare($query);
    $statement->execute();
    return $statement->rowCount();
}

$output = array(
    'draw' => intval($_POST['draw']),
    'recordsTotal' => count_all_data($connect),
    'recordsFiltered' => $number_filter_row,
    'data' => $data
);

echo json_encode($output);

?>