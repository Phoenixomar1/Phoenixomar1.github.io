<?php
require_once "pdo.php";
require_once "util.php";
session_start();

if ( ! isset($_SESSION['user_id']) ) {
    die('ACCESS DENIED');
    return;
}

if ( ! isset($_GET['profile_id']) ) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :pid");
$stmt->execute(array(":pid" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Could not load profile';
    header('Location: index.php');
    return;
}

if ( $row['user_id'] != $_SESSION['user_id'] ) {
    $_SESSION['error'] = 'You do not own this profile';
    header('Location: index.php');
    return;
}

if ( isset($_POST['delete']) ) {
    $stmt = $pdo->prepare('DELETE FROM Profile WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_POST['profile_id']));
    $_SESSION['success'] = 'Profile deleted';
    header('Location: index.php');
    return;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>John Doe's Profile Delete</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Delete Profile</h1>
<p>First Name: <?= htmlentities($row['first_name']) ?></p>
<p>Last Name: <?= htmlentities($row['last_name']) ?></p>
<form method="post">
<input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
<input type="submit" name="delete" value="Delete">
<input type="submit" name="cancel" value="Cancel">
</form>
</div>
</body>
</html>