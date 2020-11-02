<?php //Isabella has created this page. ?>

<?php
//Check if user is logged in. If so, forward to index.
if(isset($_SESSION['usr']))
{
	header("Location: /Index.php");	
}

//datavariables, empty at start
$firstName = $lastName  = $phone = $email = $biography = $user = $password = "";

$title = "* Select title";
$area = "* Select research area";

//error message variables for required fields
$fname_err = $lname_err = $email_err = $title_err = $area_err = $user_err = $password_err = "";

//styling variables for required fields
$fn_valid = $ln_valid = $em_valid = $un_valid = $pw_valid = $t_valid = $a_valid = "";

//variable that checks if data is ok to upload
$upload = 0;

//variables that checks if image is ok to upload
$uploadIMG = 1;
$image_info = "Please select a file for your profile picture. The file must be in jpg/jpeg-format.";
$image = "";

//session variables to see if application is succesfully completed or not and filled in
$_SESSION['has_applied'] = false;

$db = OpenCon();

//retrieve input and check input with function:
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) 
{

  if(empty($_POST["firstName"]))
  {
      $fname_err = "First name is required";
      $fn_valid = "is-invalid";
  }
  else
  {
      $fn_valid = "is-valid";
      $firstName = check_input($_POST["firstName"]);
      $upload++;
  }

  if(empty($_POST["lastName"]))
  {
      $lname_err = "Last name is required";
      $ln_valid = "is-invalid";
  }
  else
  {
       $ln_valid = "is-valid";
       $lastName = check_input($_POST["lastName"]);
       $upload++;
  }

  if(empty($_POST["title"]) ||  $_POST["title"] == "* Select title")
  {
      $title_err = "Title is required";
      $t_valid = "is-invalid";
  }
  else
  {
    $t_valid = "is-valid";
      $title = $_POST["title"];
      $upload++;
  }

  if(empty($_POST["area"]) || $_POST["area"] == "* Select research area")
  {
      $area_err = "Research area is required";
      $a_valid = "is-invalid";
  }
  else
  {   
	  $a_valid = "is-valid";
      $area = $_POST["area"];
      $upload++;
  }

  if(empty($_POST["email"]))
  {
      $email_err = "Email is required";
      $em_valid = "is-invalid";
  }
  else
  {
      $email = check_input($_POST["email"]);

      $sql = "select email from members where email='$email' union select email from new_members where email='$email'";
	  $result = mysqli_query($db, $sql);
		
      if (mysqli_num_rows($result) >=1)			
      {
            $email_err = "Email is already registered";
            $em_valid = "is-invalid";
      }
      else
      {
            $em_valid = "is-valid";
            $upload++;
	  }
  }
  
  if(empty($_POST["user"]))
  {
      $user_err = "Username is required";
      $un_valid = "is-invalid";
  }
  else
  {
      $user = check_input($_POST["user"]);

      $sql = "SELECT login FROM users WHERE login = '$user'";
	  $result = mysqli_query($db, $sql);
		
      if (mysqli_num_rows($result) >=1)			
      {
            $user_err = "Username is already taken";
            $un_valid = "is-invalid";
      }
      else
      {
            $un_valid = "is-valid";
            $upload++;
	  }
  }

  if(empty($_POST["password"]))
  {
      $password_err = "Password is required";
      $pw_valid = "is-invalid";
  }
  else
  {
      $pw_valid = "is-valid";
      $password = check_input($_POST["password"]);
      $upload++;
  }

  $phone = check_input($_POST["phone"]);
  $biography = check_input($_POST["biography"]);
}

//function that checks the input and removes blankspaces, backslashes etc and validates the code in prevention of hacking scripts.
function check_input($data) 
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

//Retrieve titles and areas to choose between from database:
$sql = "SELECT type FROM titles";
$result = mysqli_query($db, $sql);
$titles = $result->fetch_all(MYSQLI_ASSOC);

$sql = "SELECT type FROM areas";
$result = mysqli_query($db, $sql);
$areas = $result->fetch_all(MYSQLI_ASSOC);


//Retrieve fileinput
if (isset($_POST['submit']) && !empty($_FILES['image']['name']) && !empty($_POST["user"])) 
{
    $folder = "uploads/";
    $image = $user . basename($_FILES["image"]["name"]);
    $target = $folder . $image;
    $imageFileType = strtolower(pathinfo($target,PATHINFO_EXTENSION));
    $check = getimagesize($_FILES["image"]["tmp_name"]);

    // Check file size
    if ($_FILES['image']['size'] > 500000) 
    {
    $image_info = "Sorry, your file is too large.";
    $uploadIMG = 0;
    }

    // Allow only jpg and jpeg as file formats
    if($imageFileType != "jpg" && $imageFileType != "jpeg") 
    {
        $image_info = "Sorry, only JPG & JPEG files are allowed.";
        $uploadIMG = 0;
    }

    // Check if $uploadIMG is set to 0 by an error, else upload image to server
    if ($uploadIMG == 1) 
    {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target))
        {
            $uploadIMG = 1;
        } 
    } 

}

//check if data and file are ok, then upload everything to database and update page:
if($uploadIMG == 1 && $upload == 7)
{
    //Cryptates the password before sending to database
    $md5_password = md5($password);
    $sql = "CALL registerMember('$firstName', '$lastName', '$phone', '$email', '$title', '$area', '$biography', '$user', '$md5_password', '$image')";
    
    mysqli_query($db, $sql);

    $email_receiver = "ihn19002@gmail.com";
    $textHead = "Your application has been sent";
    $textBody = "Hello! Your application has been received. Our administator will process it shortly and let you know if you have been accepted or not. Kind regards, The Computer Club";

    mail($email_receiver, $textHead, $textBody);
    $firstName = $lastName  = $phone = $email = $title = $area = $biography = $user = $password = "";
    $_SESSION['has_applied'] = true;
}

CloseCon($db);

//Send email when application is done

?>

<!--Page that displays when application is successful-->
<div id="default_page">
    <?php if($_SESSION['has_applied']):
    $_SESSION['clear'] = true;
    ?>
    <form>
        <div class="container-fluid">
            <br><br>
			<h2 style="text-align:center">Application successful!</h2><br>
			<p style="text-align:center">A confirmation email has been sent to your inbox.<br> Our administration will notify you if and when your application has been accepted.</p><br>
			<br>
		</div>
    </form>
    <?php else: ?>
                 
    <!--Page with application form-->
    <form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" id="registerForm" >
    <?php if(isset($_SESSION['clear']) && $_SESSION['clear'] == true):
             $firstName = $lastName  = $phone = $email = $title = $area = $biography = $user = $password = $email_err = "";
             $fn_valid = $ln_valid = $em_valid = $un_valid = $pw_valid = $t_valid = $a_valid = "";
             $title = "* Select title";
             $area = "* Select research area";
             $_SESSION['clear'] = false;
         endif; ?>
        <div class="d-flex justify-content-center">
        <h4>Application for membership</h4></div>
            <div class="d-flex justify-content-center">
                <p style="text-align: center">Please fill in this form to become a member of our Computer Club.<br> Our administration will notify you if and when your application has been accepted.</p>
            </div>
            <div class="container">
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <input type="text" placeholder="* First name" class="form-control <?php echo $fn_valid ?>" id="firstName" name="firstName" value="<?php echo $firstName ?>">
                            <div><span class="required"><?php echo $fname_err;?></span></div><br>
                        </div>
                        <div class="col">
                            <input type="text" placeholder="* Last name" class="form-control <?php echo $ln_valid ?>" id="lastName" name="lastName" value="<?php echo $lastName ?>">
                            <div><span class="required"><?php echo $lname_err;?></span></div><br>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <input type="text" placeholder="Phone" class="form-control" id="phone" name="phone" value="<?php echo $phone ?>">
                            <label for="phone"></label>
                        </div>
                        <div class="col">
                            <input type="email" placeholder="* Email" class="form-control <?php echo $em_valid ?>" id="email" name="email" value="<?php echo $email ?>">
                            <div><span class="required"><?php echo $email_err; if(!empty($email_reg)) echo $email_reg;?></span></div><br>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <input type="text" placeholder="* Username" class="form-control <?php echo $un_valid ?>" id="user" name="user" value="<?php echo $user ?>">
                            <div><span class="required"><?php echo $user_err;?></span></div>
                        </div>
                        <div class="col">
                            <input type="text" placeholder="* Password" class="form-control <?php echo $pw_valid ?>" id="password" name="password" value="<?php echo $password ?>">
                            <div><span class="required"><?php echo $password_err;?></span></div><br>
                        </div>
                    </div>
                </div>

                <div class="container">
                    <div class="row">
                        <div class="col">
                            <?php if ($titles): ?>
                                <select class="form-control <?php echo $t_valid ?>" id="title" name="title"><option value="<?php echo $title; ?>"><?php echo $title; ?></option>
                                    <?php foreach($titles as $row): ?><option>
                                        <?= $row['type'] ?>
                                    <?php endforeach ?></option></select>
                            <?php endif ?>
                            <div><span class="required"><?php echo $title_err;?></span></div>
                        </div>
                        <div class="col">
                            <?php if ($areas): ?>
                                <select class="form-control <?php echo $a_valid ?>" id="area" name="area"><option value="<?php echo $area; ?>"><?php echo $area; ?></option>
                                    <?php foreach($areas as $row): ?><option>
                                        <?= $row['type'] ?>
                                    <?php endforeach ?></option></select>
                            <?php endif ?>
                            <div><span class="required"><?php echo $area_err;?></span></div>
                        </div>
                    </div>
                </div>
                <div class="container">
                <br>
                    <p>Biography:</p>
                        <textarea class="form-control" id="biography" name="biography" rows="4" placeholder="Please write a few words about yourself."></textarea>
                </div>
                <br>
                <div class="container">
                    <p><?php echo $image_info; ?></p>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="image" id="image">
                            <label class="custom-file-label" for="image">Choose file</label>
                        </div>
                </div>
                <br>
                <div class="d-flex justify-content-center">
                   <button type="submit" class="btn btn-info" name="submit" value="Submit">Submit <i class='fa fa-check'></i></button>
                </div>
            </div>
        </div>
    </form>
    <?php endif; ?>
</div>

<?php // script to style the file selector ?>
<script>
$(".custom-file-input").on("change", function() {
  var fileName = $(this).val().split("\\").pop();
  $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});
</script>