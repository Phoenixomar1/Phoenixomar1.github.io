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

if ( isset($_POST['first_name']) && isset($_POST['last_name']) && 
     isset($_POST['email']) && isset($_POST['headline']) && 
     isset($_POST['summary']) ) {
    
    $msg = validateProfile();
    if ( is_string($msg) ) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
    }
    
    $stmt = $pdo->prepare('UPDATE Profile SET first_name = :fn,
        last_name = :ln, email = :em, headline = :he, summary = :su
        WHERE profile_id = :pid');
    $stmt->execute(array(
        ':pid' => $_POST['profile_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'])
    );
    $_SESSION['success'] = 'Profile updated';
    header('Location: index.php');
    return;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>John Doe's Profile Edit</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Editing Profile for <?= htmlentities($_SESSION['name']); ?></h1>
<?php flashMessages(); ?>
<form method="post">
<input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
<p>First Name:
<input type="text" name="first_name" value="<?= htmlentities($row['first_name']) ?>" size="60"/></p>
<p>Last Name:
<input type="text" name="last_name" value="<?= htmlentities($row['last_name']) ?>" size="60"/></p>
<p>Email:
<input type="text" name="email" value="<?= htmlentities($row['email']) ?>" size="30"/></p>
<p>Headline:<br/>
<input type="text" name="headline" value="<?= htmlentities($row['headline']) ?>" size="80"/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80"><?= htmlentities($row['summary']) ?></textarea>
<p>
<input type="submit" value="Save">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>
</div>
</body>
</html>