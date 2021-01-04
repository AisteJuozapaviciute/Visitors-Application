<?php
require_once "pdo.php";
session_start();
if (isset($_POST['cancel'])) {
    header('Location: index.php');
    return;
}


function validate_phone_number($phone)
{
    // Allow +, - and . in phone number
    $filtered_phone_number = filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
    // Remove "-" from number
    $phone_to_check = str_replace("-", "", $filtered_phone_number);
    // Check the lenght of number
    if (strlen($phone_to_check) < 9 || strlen($phone_to_check) > 12) {
        return false;
    } else {
        return true;
    }
}

if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['phone']) && isset($_POST['id'])) {

    // Data validation
    if (strlen($_POST['email']) < 1 || strlen($_POST['phone']) < 1 || strlen($_POST['name']) < 1) {
        $_SESSION['error'] = 'Missing data';
        header("Location: edit.php?id=" . $_POST['id']);
        return;
    }
    $sql = "UPDATE visitor SET
                            email = :email, 
                            phone = :phone, 
                            name = :name
            WHERE id = :id";

    if (validate_phone_number($_POST['phone']) == true) {

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':email' => $_POST['email'],
            ':phone' => $_POST['phone'],
            ':name' => $_POST['name'],
            ':id' => $_POST['id']
        ));

        $_SESSION['success'] = 'Record updated';
        header('Location: index.php');
        return;
    } else {
        $_SESSION['error'] = 'Invalid phone number';
        header("Location: edit.php?id=" . $_POST['id']);
        return;
    }
}

// Guardian: date sure that id is present
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Missing id";
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT * FROM visitor where id = :xyz");
$stmt->execute(array(":xyz" => $_GET['id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Bad value for id';
    header('Location: index.php');
    return;
}

// Flash pattern
if (isset($_SESSION['error'])) {
    echo '<p style="color:red">' . $_SESSION['error'] . "</p>\n";
    unset($_SESSION['error']);
}

$em = htmlentities($row['email']);
$p = htmlentities($row['phone']);
$n = htmlentities($row['name']);

$id = $row['id'];
?>
<html>

<head>
    <title>Visitors journal</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>

<body>
    <div class="Container">
        <h1>Edit visitor</h1>
        <form method="post">
            <div class="form-group">
                <label for="text">Name:</label>
                <input type="text" name="name" value="<?= $n ?>">
            </div>

            <div class="form-group">
                <label for="text">Phone:</label>
                <input type="text" name="phone" value="<?= $p ?>">
            </div>
            <div class="form-group">
                <label for="text">Email:</label>
                <input type="email" name="email" value="<?= $em ?>">
            </div>

            <input type="hidden" name="id" value="<?= $id ?>">

            <input class="btn btn-primary" type="submit" value="Save">
            <input class="btn btn-secondary" type="submit" name="cancel" value="Cancel">
        </form>
    </div>
</body>

</html>