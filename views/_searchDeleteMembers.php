<?php // Created by Emelie Wallin
include '../dbconnection.php';

$con = OpenCon();
$q = $_GET['q'];

//get result for search
$sql = "SELECT id, first_name, last_name FROM all_members WHERE first_name like '%$q%' OR last_name like '%$q%' order by first_name";

$result = mysqli_query($con, $sql);

$members = "null";
if ($result->num_rows > 0) {
	$members = $result->fetch_all(MYSQLI_ASSOC);
}

CloseCon($con);

if($members != "null") {
	foreach ($members as $member){
		?>
		<li id="<?php echo $member['id']; ?>" href="#" class="list-group-item list-group-item-action" style="cursor:pointer;" ><?=$member['first_name'], ' ', $member['last_name']?></li>	
		<?php }
		}

else { ?>
	<lable><em>No search objects...</em></lable>	
	<?php }
?>
