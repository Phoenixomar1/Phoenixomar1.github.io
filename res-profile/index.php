<?php
require_once "pdo.php";
require_once "util.php";

$stmt = $pdo->query("SELECT Profile.profile_id, Profile.first_name, 
                     Profile.last_name, Profile.headline, 
                     users.name, Profile.user_id 
                     FROM Profile JOIN users ON 
                     Profile.user_id = users.user_id");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Omar Yarane's Profile Database</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Omar Yarane's Resume Registry</h1>

<?php
flashMessages();

if ( ! isset($_SESSION['user_id']) ) {
    echo '<p><a href="login.php">Please log in</a></p>';
    if ( count($rows) > 0 ) {
        echo '<table border="1">';
        echo "<tr><th>Name</th><th>Headline</th></tr>";
        foreach ($rows as $row) {
            echo "<tr><td>";
            echo('<a href="view.php?profile_id='.$row['profile_id'].'">');
            echo(htmlentities($row['first_name']).' '.htmlentities($row['last_name']));
            echo("</a>");
            echo("</td><td>");
            echo(htmlentities($row['headline']));
            echo("</td></tr>\n");
        }
        echo "</table>";
    }
} else {
    echo '<p><a href="logout.php">Logout</a></p>';
    echo '<p><a href="add.php">Add New Entry</a></p>';
    
    if ( count($rows) > 0 ) {
        echo '<table border="1">';
        echo "<tr><th>Name</th><th>Headline</th><th>Action</th></tr>";
        foreach ($rows as $row) {
            echo "<tr><td>";
            echo('<a href="view.php?profile_id='.$row['profile_id'].'">');
            echo(htmlentities($row['first_name']).' '.htmlentities($row['last_name']));
            echo("</a>");
            echo("</td><td>");
            echo(htmlentities($row['headline']));
            echo("</td><td>");
            if ( $row['user_id'] == $_SESSION['user_id'] ) {
                echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
                echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
            }
            echo("</td></tr>\n");
        }
        echo "</table>";
    } else {
        echo '<p>No profiles found</p>';
    }
}
?>
</div>
</body>
</html>