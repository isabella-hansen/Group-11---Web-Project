<?php //Created by Emelie Wallin
include '../dbconnection.php';

$con = OpenCon();
$q = $_GET['q'];
$title = $_GET['title'];

//get search result based on diffrent inputs
if( $q == '' && $title == 'All titles'){
	$sql = "SELECT * FROM all_members order by first_name";
}

elseif($title == 'All titles') {
$sql = "SELECT * FROM all_members WHERE first_name like '%$q%' OR last_name like '%$q%' order by first_name";
}

elseif ( $title == 'All titles'){
	$sql = "SELECT * FROM all_members WHERE first_name like '%$q%' OR last_name like '%$q%' order by first_name";
}

else{
	$sql = "SELECT * FROM all_members WHERE title = '$title' and (first_name like '%$q%' OR last_name like '%$q%') order by first_name";
}


$result = mysqli_query($con, $sql);
CloseCon($con);

//print result
if($result->num_rows > 0){
	while($row = mysqli_fetch_array($result)){
		$id = $row['id'];
		?>
		<tr class="pointer" id=<?php echo $id ?> >
			<td><?=$row['first_name']?></td>
			<td><?=$row['last_name']?></td>
			<td><?=$row['title']?></td>
		</tr>
		<?php }
		}
	else { ?>
		<tr>
			<td>No result</td>
		</tr>
	<?php }
?>
