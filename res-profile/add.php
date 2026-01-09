<?php
require_once "pdo.php";
require_once "util.php";
session_start();

if ( ! isset($_SESSION['user_id']) ) {
    die('ACCESS DENIED');
    return;
}

if ( isset($_POST['cancel']) ) {
    header("Location: index.php");
    return;
}

if ( isset($_POST['first_name']) && isset($_POST['last_name']) && 
     isset($_POST['email']) && isset($_POST['headline']) && 
     isset($_POST['summary']) ) {
    
    $msg = validateProfile();
    if ( is_string($msg) ) {
        $_SESSION['error'] = $msg;
        header("Location: add.php");
        return;
    }
    
    $stmt = $pdo->prepare('INSERT INTO Profile
        (user_id, first_name, last_name, email, headline, summary)
        VALUES ( :uid, :fn, :ln, :em, :he, :su)');
    $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'])
    );
    $_SESSION['success'] = 'Profile added';
    header('Location: index.php');
    return;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>John Doe's Profile Add</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Adding Profile for <?= htmlentities($_SESSION['name']); ?></h1>
<?php flashMessages(); ?>
<form method="post">
<p>First Name:
<input type="text" name="first_name" size="60"/></p>
<p>Last Name:
<input type="text" name="last_name" size="60"/></p>
<p>Email:
<input type="text" name="email" size="30"/></p>
<p>Headline:<br/>
<input type="text" name="headline" size="80"/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80"></textarea>
<p>
<input type="submit" value="Add">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>
</div>
</body>
</html>