<?php
require_once "pdo.php";

$status = false;
$status_color = 'red';
$date = date("Y-m-d h:i:s");

//CSV
if (isset($_POST["submit"])) {
    if ($_FILES['file']['name']) {
        $filename = explode(".", $_FILES['file']['name']);
        if ($filename[1] == 'csv') {
            $handle = fopen($_FILES['file']['tmp_name'], "r");
            $stmt = $pdo->prepare("INSERT INTO visitor (name, email, phone, date) VALUES (:name, :email, :phone, '$date')");

            while (!feof($handle)) {
                $row = fgetcsv($handle);
                $stmt->bindParam(':name', $row[0]);
                $stmt->bindParam(':email', $row[1]);
                $stmt->bindParam(':phone', $row[2]);
                $stmt->execute();
            }
            fclose($handle);
            echo "<script>alert('Import done');</script>";
        } else
            echo "<script>alert('Selected file is not supported for upload. Please select CSV file');</script>";
    }
}

$all_visitors = $pdo->query("SELECT * FROM visitor");

$visitors = $all_visitors->fetchAll(PDO::FETCH_ASSOC);

?>
<html>

<head>
    <title>Visitors journal</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>

<body>
    <div class="container">
        <h1>Visitors journal </h1>
        <?php
        if ($status !== false) {

            echo ('<p style="color: ' . $status_color . ';" class="col-sm-10 col-sm-offset-2">' .
                htmlentities($status) .
                "</p>\n");
        }
        ?>

        <?php if (!empty($visitors)) : ?>
            <table class="table table-striped">
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Phone</th>
                <th scope="col">Date</th>
                <th scope="col">Action</th>
                <?php
                foreach ($visitors as $auto) {
                    echo "<tr><td>";
                    echo (htmlentities($auto['name']));
                    echo ("</td><td>");
                    echo (htmlentities($auto['email']));
                    echo ("</td><td>");
                    echo (htmlentities($auto['phone']));
                    echo ("</td><td>");
                    echo (htmlentities($auto['date']));
                    echo ("</td><td>");
                    echo ('<a href="edit.php?id=' . $auto['id'] . '">Edit</a> / ');
                    echo ('<a href="delete.php?id=' . $auto['id'] . '">Delete</a>');
                    echo ("</td></tr>\n");
                } ?>

            </table>
        <?php endif; ?>

        <div>
            <h5> Add new customer</h5>
            <button class=" btn btn-primary">
                <a href="add.php" style="color: white"> Add </a>
            </button>
        </div>

        <div>
            <h5>Import Data from CSV File</h5>
            <form method="post" enctype="multipart/form-data">
                <div>
                    <label>Select CSV File:</label>
                    <input type="file" name="file" />
                    <input type="submit" name="submit" value="Import" accept=".csv" class=" btn btn-primary" />
                </div>
            </form>
        </div>


    </div>
</body>

</html>