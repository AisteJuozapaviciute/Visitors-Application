<?php
require_once "pdo.php";
session_start();

if (isset($_POST['cancel'])) {
    header('Location: index.php');
    return;
}

if (isset($_POST['delete']) && isset($_POST['id'])) {
    $sql = "DELETE FROM visitor WHERE id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['id']));
    $_SESSION['success'] = 'Record deleted';
    header('Location: index.php');
    return;
}
// Guardian: Make sure that id is present
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Missing id";
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT name, email, phone, date, id FROM visitor where id = :xyz");
$stmt->execute(array(":xyz" => $_GET['id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Bad value for id';
    header('Location: index.php');
    return;
}

?>
<html>

<head>
    <title>Visitors journal</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>

<body>
    <div class="container">
        <h3>Confirm: Deleting <?= htmlentities($row['name']) ?></h3>

        <form method="post">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <input type="submit" class="btn btn-danger" value="Delete" name="delete">
            <input class="btn btn-secondary" type="submit" name="cancel" value="Cancel">
        </form>
    </div>
</body>

</html>