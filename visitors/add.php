<?php
require_once "pdo.php";

$status = false;
$status_color = 'red';
$date = date("Y-m-d h:i:s");

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
if (isset($_POST['phone']) && isset($_POST['email']) && isset($_POST['name'])) {
    $name = htmlentities($_POST['name']);
    $email = htmlentities($_POST['email']);
    $phone = htmlentities($_POST['phone']);
    if (validate_phone_number($phone) == true) {
        $sql = "INSERT INTO visitor (name, email, phone, date) 
            VALUES (:name, :email, :phone, '$date')";
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone
        ]);

        $status = 'Record inserted';
        $status_color = 'green';
    } else {
        $status = 'Invalid phone number';
    }
}

$all_autos = $pdo->query("SELECT * FROM visitor");

$autos = $all_autos->fetchAll(PDO::FETCH_ASSOC);

?>
<html>

<head>
    <title>Visitors journal</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>

<body>
    <div class="container">
        <h1>New customer</h1>
        <?php
        if ($status !== false) {

            echo ('<p style="color: ' . $status_color . ';" class="col-sm-10 col-sm-offset-2">' .
                htmlentities($status) .
                "</p>\n");
        }
        ?>

        <form method="post">
            <div class="form-group">
                <label for="text">Name:</label>
                <input type="text" name="name" class="form-control">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" class="form-control">
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="phone" name="phone" class="form-control" placeholder="+3706XXXXXXX">
            </div>
            <input class="btn btn-primary" type="submit" value="Add">
            <input class="btn btn-secondary" type="submit" name="cancel" value="Cancel">
        </form>
    </div>
</body>

</html>