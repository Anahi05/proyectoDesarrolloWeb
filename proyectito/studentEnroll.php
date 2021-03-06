<?php
include("include/header.php");
include("include/leftNav.php");
?>

<!--Either shows the registration form, or adds the user to the database -->
<div class="row">
	<div class="column middle">
	<?php
    include("studentCheck.php");
    if (isset($_POST['course'])) {
        addEnrollmentToDatabase();
    } else {
        showForm();
    }
    ?>
	</div>
</div>

<?php
function showForm()
    {
        //selects courses where user has not enrolled and displays them
        $userID = $_SESSION['userID'];
        $conn = mysqli_connect('localhost', 'root', '', 'classDatabase');
        $sql = "SELECT courseID, courseName FROM course
			WHERE courseID NOT IN (SELECT courseID FROM studentTaking WHERE userID=$userID)";
        $resource = mysqli_query($conn, $sql);
        if (mysqli_num_rows($resource)<1) {
            echo "There are no courses for you to enroll on";
        } else {
            //displays all the potential courses to take
            echo "<form name='enroll' method='post' action='studentEnroll.php'>";
            while ($currentCourse = mysqli_fetch_array($resource)) {
                echo "<input type='checkbox' name='course[]' value='$currentCourse[courseID]' />
			  $currentCourse[courseName] <br>";
            }
            echo"<input type='submit' onclick='submit' />
			</form>";
        }
        mysqli_close($conn);
    }


function addEnrollmentToDatabase()
{
    //adds enrollment information
    $course = $_POST['course'];
    $userID = $_SESSION['userID'];
    $today = date("Ymd");

    $conn = mysqli_connect('localhost', 'root', '', 'classDatabase');
    foreach ($course as $currentCourse) {
        $sql = "INSERT INTO studentTaking (courseID, userID, dateRegistered, authorized)
				VALUES ('$currentCourse', '$userID', '$today', 0)";
        //check if added successfully
        if (mysqli_query($conn, $sql)) {
            echo "<p style='color:green'>Successfully Registered</p>";
        } else {
            echo"<p style='color:red'>Failed to register for course<br/> ";
            echo(mysqli_error($conn));
            echo "<br/>Contact Network Admin</p>";
            mysqli_close($conn);
            die();
        }
    }
    echo "<a href='studentEnroll.php'>Click here to enroll on another course</a>
		   <br><a href='studentHome.php'>Click here to return to the student home page</a>";
    mysqli_close($conn);
}

include("include/footer.php");
?>
