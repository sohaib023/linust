<?php
  session_start();

  // If the session vars aren't set, try to set them with a cookie
  if (!isset($_SESSION['user_id'])) {
    if (isset($_COOKIE['user_id']) && isset($_COOKIE['username'])) {
      $_SESSION['user_id'] = $_COOKIE['user_id'];
      $_SESSION['username'] = $_COOKIE['username'];
    }
  }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Linust - Edit Profile</title>
  
  <link rel="stylesheet" type="text/css" href="style_editprofile.css">
  <link rel="stylesheet" type="text/css" href="bootstrap.css">
  <link rel="stylesheet" type="text/css" href="cardio.css">
</head>

  <body style="background-image:url(b1.jpg);background-size:cover;">
    <h2 class=white jumbotron style="background-color:black;"><center><i>EDIT PROFILE </i></center></h2>
  <

<?php
  require_once('appvars.php');
  require_once('connectvars.php');

  // Make sure the user is logged in before going any further.
  if (!isset($_SESSION['user_id'])) {
    echo '<h4 class="white">Please <a href="login.php">log in</a> to access this page.</h4>';
    exit();
  }
  else {
  echo('<br><br><h4 class="white">You are logged in as ' . $_SESSION['username'] . '. <a href="logout.php">Log out</a></p>.');
  }

  // Connect to the database
  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  if (isset($_POST['submit'])) {
    // Grab the profile data from the POST
    $first_name = mysqli_real_escape_string($dbc, trim($_POST['firstname']));
    $last_name = mysqli_real_escape_string($dbc, trim($_POST['lastname']));
    $gender = mysqli_real_escape_string($dbc, trim($_POST['gender']));
    $birthdate = mysqli_real_escape_string($dbc, trim($_POST['birthdate']));
    $department = mysqli_real_escape_string($dbc, trim($_POST['department']));
    $program = mysqli_real_escape_string($dbc, trim($_POST['program']));
    $email = mysqli_real_escape_string($dbc, trim($_POST['email']));
    //$old_picture = mysqli_real_escape_string($dbc, trim($_POST['old_picture']));
    //$new_picture = mysqli_real_escape_string($dbc, trim($_FILES['new_picture']['name']));
    //$new_picture_type = $_FILES['new_picture']['type'];
    //$new_picture_size = $_FILES['new_picture']['size'];
   // list($new_picture_width, $new_picture_height) = getimagesize($_FILES['new_picture']['tmp_name']);
    $error = false;

    // Validate and move the uploaded picture file, if necessary
    if (!empty($new_picture)) {
      /*if ((($new_picture_type == 'image/gif') || ($new_picture_type == 'image/jpeg') || ($new_picture_type == 'image/pjpeg') ||
        ($new_picture_type == 'image/png')) && ($new_picture_size > 0) && ($new_picture_size <= MM_MAXFILESIZE) &&
        ($new_picture_width <= MM_MAXIMGWIDTH) && ($new_picture_height <= MM_MAXIMGHEIGHT)) {
        if ($_FILES['file']['error'] == 0) {
          // Move the file to the target upload folder
          $target = MM_UPLOADPATH . basename($new_picture);
          if (move_uploaded_file($_FILES['new_picture']['tmp_name'], $target)) {
            // The new picture file move was successful, now make sure any old picture is deleted
            if (!empty($old_picture) && ($old_picture != $new_picture)) {
              @unlink(MM_UPLOADPATH . $old_picture);
            }
          }
          else {
            // The new picture file move failed, so delete the temporary file and set the error flag
            @unlink($_FILES['new_picture']['tmp_name']);
            $error = true;
            echo '<p class="error">Sorry, there was a problem uploading your picture.</p>';
          }
        }
      }
      else {
        // The new picture file is not valid, so delete the temporary file and set the error flag
        @unlink($_FILES['new_picture']['tmp_name']);
        $error = true;
        echo '<p class="error">Your picture must be a GIF, JPEG, or PNG image file no greater than ' . (MM_MAXFILESIZE / 1024) .
          ' KB and ' . MM_MAXIMGWIDTH . 'x' . MM_MAXIMGHEIGHT . ' pixels in size.</p>';
      }*/
    }

    // Update the profile data in the database
    if (!$error) {
      if (!empty($first_name) && !empty($email)) {
        // Only set the picture column if there is a new picture
        if (!empty($program)) {
          $query = "UPDATE linust_f.users SET first_name = '$first_name', last_name = '$last_name', gender = '$gender', " .
            " birthdate = '$birthdate', department = '$department', program = '$program', email = '$email' WHERE user_id = '" . $_SESSION['user_id'] . "'";
        }
        else{
          $query = "UPDATE linust_f.users SET first_name = '$first_name', last_name = '$last_name', gender = '$gender', " .
            " birthdate = '$birthdate', department = '$department', email = '$email', program = '' WHERE user_id = '" . $_SESSION['user_id'] . "'";
        }
		
		if (!empty($department)) {
          $query = "UPDATE linust_f.users SET first_name = '$first_name', last_name = '$last_name', gender = '$gender', " .
            " birthdate = '$birthdate', department = '$department', program = '$program', email = '$email' WHERE user_id = '" . $_SESSION['user_id'] . "'";
        }
        else{
          $query = "UPDATE linust_f.users SET first_name = '$first_name', last_name = '$last_name', gender = '$gender', " .
            " birthdate = '$birthdate', program = '$program', email = '$email', department = '' WHERE user_id = '" . $_SESSION['user_id'] . "'";
        }
		
	
        mysqli_query($dbc, $query);

        // Confirm success with the user
        echo '<p>Your profile has been successfully updated. Would you like to <a href="viewprofile.php">view your profile</a>?</p>';

        mysqli_close($dbc);
        exit();
      }
      else {
        echo '<p class="error">You must enter all of the profile data.</p>';
      }
    }
  } // End of check for form submission
  else {
    // Grab the profile data from the database
    $query = "SELECT first_name, last_name, gender, birthdate, department, program,email, picture FROM linust_f.users WHERE user_id = '" . $_SESSION['user_id'] . "'";
    $data = mysqli_query($dbc, $query);
    $row = mysqli_fetch_array($data);

    if ($row != NULL) {
      $first_name = $row['first_name'];
      $last_name = $row['last_name'];
	  $email = $row['email'];
      $gender = $row['gender'];
      $birthdate = $row['birthdate'];
      $department = $row['department'];
      $program = $row['program'];
      $old_picture = $row['picture'];
    }
    else {
      echo '<p class="error">There was a problem accessing your profile.</p>';
    }
  }

  mysqli_close($dbc);
?>

  <form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MM_MAXFILESIZE; ?>" />
    <fieldset>
      <legend>Personal Information</legend>
      <label for="firstname">First name:</label>
      <input style="color:white;background-color:black;"type="text" id="firstname" name="firstname" value="<?php if (!empty($first_name)) echo $first_name; ?>" /><br />
      <label for="lastname">Last name:</label>
      <input style="color:white;background-color:black;"type="text" id="lastname" name="lastname" value="<?php if (!empty($last_name)) echo $last_name; ?>" /><br />
      <label for="lastname">Eamil:</label>
      <input style="color:white;background-color:black;"type="text" id="email" name="email" value="<?php if (!empty($email)) echo $email; ?>" /><br />
	  <label for="gender">Gender:</label>
      <select style="color:white;background-color:black;"id="gender" name="gender">
        <option style="color:white;background-color:black;"value="M" <?php if (!empty($gender) && $gender == 'M') echo 'selected = "selected"'; ?>>Male</option>
        <option style="color:white;background-color:black;"value="F" <?php if (!empty($gender) && $gender == 'F') echo 'selected = "selected"'; ?>>Female</option>
      </select><br />
      <label for="birthdate">Birthdate:</label>
      <input style="color:white;background-color:black;"type="text" id="birthdate" name="birthdate" value="<?php if (!empty($birthdate)) echo $birthdate; else echo 'YYYY-MM-DD'; ?>" /><br />
   
	  <label for="state">Department:</label>
      <input style="color:white;background-color:black;"type="text" id="department" name="department" value="<?php if (!empty($program)) echo $department; ?>" /><br />
      
	  <label for="state">Program:</label>
      <input style="color:white;background-color:black;"type="text" id="program" name="program" value="<?php if (!empty($program)) echo $program; ?>" /><br />
      
	  <?php /*
	  <input style="color:black;type="hidden" name="old_picture" value="<?php if (!empty($old_picture)) echo $old_picture; ?>" />
		<br>
      <label for="new_picture">Picture:</label>
</h4>
      <input type="file"  class="btn btn-warning" id="new_picture" name="new_picture" /><br>
      <?php if (!empty($old_picture)) {
        echo '<img class="profile" style="padding-left:50px;" src="' . MM_UPLOADPATH . $old_picture . '" alt="Profile Picture" />';
      } ?>
    </fieldset> */ ?>
    <br>
    <br>
<center><h3><input type="submit"class="btn btn-warning btn-block" style="padding-left:150px;" value="Save Profile" name="submit" /></h3></center>
  </form>
</body>
</html>
