<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  header("location: login.php");
  exit;
}

// Print authorizations
// print_r($_SESSION['authorizations']);

// Include config file
require_once 'config.php';

//Define variables and initialize with empty values
$type = $comments = "";
$type_err = $comments_err = "";

//Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

  //Validate type
  if(empty(trim($_POST["type"]))){
    $type_err = "Please select a type from the list.";
  } else{
    $type = trim($_POST["type"]);
  }

  //Validate comment
  if(empty(trim($_POST["comments"]))){
    $comments_err = "Please enter your comments.";
  } elseif(strlen(trim($_POST["comments"])) < 6){
    $comments_err = "Your comments do not appear to be valid.";
  } else{
    $comments = trim($_POST["comments"]);
  }

  // Check input errors before storing in database
  if(empty($type_err) && empty($comments_err)){
    // Prepare an insert statement
    $sql = "INSERT INTO tbl_feedback (username, ipaddress, feedbacktype, comments) VALUES (?, ?, ?, ?)";

    if($stmt = $mysqli->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("ssss", $param_username, $param_ipaddress, $param_type, $param_comments);

        // Set parameters
        $param_username = $_SESSION['username'];
        $param_ipaddress = $_SERVER['REMOTE_ADDR'];
        $param_type = $type;
        $param_comments = $comments;

        // Attempt to execute the prepared statement
        if($stmt->execute()){
            // Success
            $query_result = "<p>Got it, thanks for your feedback!</p>";
        } else{
            $query_result = "<p>Oops.. Could not execute query. Please try again later.</p>";
        }
    }

    // Close statement
    $stmt->close();

    //Reset all variables to empty so the page shows empty again
    $type = $comments = "";
  } else{
    echo "Something went really wrong. Please try again later.";
  }

  // Close connection
  $mysqli->close();
}
?>

<!DOCTYPE html>
<html>
<head>
  <?php include 'head.php'; ?>
</head>
<body>

<?php include 'navbar.php';?>

<div class="container-fluid" style="margin-top:20px">
  <div class="row">
    <div class="col-sm-3">
      <?php include 'aboutme.php';?>
      <?php include 'aboutyou.php';?>
      <?php include 'popular.php';?>
    </div>
    <div class="col-sm-9">
      <h1><i class="far fa-hand-spock"></i> HI <b><?php echo strtoupper(htmlspecialchars($_SESSION['username'])); ?></b>!</h1>
      <h5>In peace to you I come</h5>
      <h2><i class="fas fa-ban"></i> IN ORDER TO AVOID ANY CLAIMS..</h2>
      <h5>A list of things that make up this website</h5>
      <p>This never-ending project started officially in <a href="https://products.office.com/en/access" target="_blank">MS Access</a>. Teaching myself pretty much everything through online tutorials, below you find a list of functionalities, languages, programmes and features all combined for my, euhm your, entertainment.</p>
      <p>One word beforehand, I am definitely not the most technical skilled person! I just like to learn new things and mess around with code programming until I get frustrated and throw everything away. There are a billion people on this planet that can do things faster and better. I am in it for the fun.</p>
      <p>So here we go..</p>
      <p>Basic stuff first: this website uses a combination of HTML, PHP, MySQL and CSS. For me this is complex enough, and provides me with all that I currently need as far as functionality goes. There are some javascript functions included, but they are mainly taken from templates and I am still learning how to write that code. All the other languages I learnt from <a href="https://www.w3schools.com/" target="_blank">W3 Schools</a> and <a href="https://stackoverflow.com/" target="_blank">Stack Overflow</a>. </p>
      <p>With regards to external sources that I use. They are:
        <ul>
          <li>The font (Karla) is taken from <a href="https://fonts.google.com/specimen/Karla" target="_blank">Google Fonts</a>, integrated through CSS. It's simple, clearly readable and looks nice.</li>
          <li>The icons I use everywhere are taken from <a href="https://fontawesome.com" target="_blank">Font Awesome</a>, also through CSS. The interface is very easy to use, and since all icons are font-based they remain sharp even when scaled.</li>
          <li>The entire two column layout is actually one of the basic templates from <a href="https://www.w3schools.com/css/css_website_layout.asp" target="_blank">W3 School</a>. I chose this layout as it is pretty straigtforward and minimalistic.</li>
          <li>Colorscheme is made up by myself. As I am colorblind the palette might not be the best ever. At least I am able to see the different colors so I don't care, hehe.</li>
          <li><a href="http://jqueryui.com/" target="_blank">JQuery</a> is used for form manipulations, easy to integrate and a sh*tload of functions that I can use.</li>
          <li>I used <a href="https://www.highcharts.com/" target="_blank">Highcharts</a> in the past for table formatting. As I started all over with the current layout I am considering customizing my own table layout. It worked for me in the past but at the moment not a priority.</li>
          <li><a href="https://developers.google.com/chart/" target="_blank">Google Charts</a> everywhere! Pretty much all database integrations are focused on data visualizations. Still learning to work with the required format but over time only this channel will be used.</li>
          <li>Everything is built in <a href="https://atom.io/" target="_blank">Atom</a> in a DEV environment. This is a Windows based machine with locally installed servers for testing.</li>
          <li>When a functionality is finished, I use <a href="https://github.com/" target="_blank">GitHub</a> integration for versioning and push the functionality to the PROD environment.</li>
          <li>The PROD environment is a Linux based (<a href="https://www.ubuntu.com/" target="_blank">Ubuntu</a>) server running a <a href="https://www.nginx.com/" target="_blank">Nginx</a> webserver.</li>
        </ul>
      </p>
      <?php if(in_array("2", $_SESSION['authorizations'])){ ?>
      <h2><i class="fas fa-headset"></i> CONTACT FORM</h2>
      <h5>Leave me a comment</h5>
      <?php echo $query_result; ?>
      <p>Please fill in all fields and click Submit. Muchas gracias!</p>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="max-width:500px">
        <div class="input-container">
          <i class="fas fa-balance-scale icon"></i>
          <select class="input-field" name="type" value="<?php echo $type; ?>">
            <option value="Feedback">Feedback</option>
            <option value="Improvement">Improvement</option>
            <option value="Question">Question</option>
            <option value="Other">Other</option>
          </select><?php echo $type_err; ?>
        </div>
        <div class="input-container">
          <i class="far fa-calendar-alt icon"></i>
          <textarea class="input-field" name="comments" placeholder="Comments" maxlength="1000" cols="25" rows="6"></textarea><?php echo $comments_err; ?>
        </div>

        <button type="submit">Submit</button>
      </form>
    <?php } ?>
    </div>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
