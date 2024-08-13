<?php

session_start();
$insert = false;
$update = false;
$delete = false;

// ------------connect to database--------------
$servername = "localhost";
$username = "root";
$password = "";
$database = "notes";

//----- create a connection------
$conn = mysqli_connect($servername, $username, $password, $database);

// -------die if connection wasn't successful------
if (!$conn) {
    die("Sorry we failed to connect: " . mysqli_connect_error());
}
// -----for deleting the record---------
if(isset($_GET['delete'])){
  $sno = $_GET['delete'];
  $_SESSION['delete']= true;
  $sql = "DELETE FROM `notes` WHERE `srno` = $sno";
  $result = mysqli_query($conn,$sql);
}

// ---------------
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['snoEdit'])) {
        // Update the record
        $sno = $_POST["snoEdit"];
        $title = $_POST['titleEdit'];
        $description = $_POST['descriptionEdit'];

        // SQL query to be executed
        $sql = "UPDATE `notes` SET `title` = '$title', `description` = '$description' WHERE `notes`.`srno` = $sno";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $_SESSION['update'] = true;
        } else {
            echo "Update failed: " . mysqli_error($conn);
        }
    } else {
        $title = $_POST["title"];
        $description = $_POST["description"];

        // SQL query to be executed
        $sql = "INSERT INTO `notes` (`title`, `description`) VALUES ('$title', '$description')";
        $result = mysqli_query($conn, $sql);

        // Add a new note to the database
        if ($result) {
            $_SESSION['insert'] = true;
        } else {
            echo "The record was not inserted successfully: " . mysqli_error($conn);
        }
    }
}
?>

<!-- HTML Code -->
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRUD Operation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Enqueue DataTables CSS -->
    <link rel="stylesheet" href="//cdn.datatables.net/2.1.2/css/dataTables.dataTables.min.css">
    <!-- Enqueue CSS for modal -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- For Edit Modal -->
    <!-- Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editModalLabel">Edit this Note</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="/crud/index.php" method="post">
                        <input type="hidden" name="snoEdit" value="" id="snoEdit">
                        <div class="mb-3">
                            <label class="form-label">Note Title:</label>
                            <input type="text" id="titleEdit" name="titleEdit" class="form-control" placeholder="Enter Note Title">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Note Description:</label>
                            <textarea class="form-control" id="descriptionEdit" name="descriptionEdit" rows="3"></textarea>
                        </div>
                        <div class="Button">
                            <button type="submit" class="btn btn-primary">Update Note</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- For displaying alert message on top (insert) -->
    <!-- For displaying alert message on top (insert) -->
     <?php
     if (isset($_SESSION['insert']) && $_SESSION['insert']) {
         echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
         <strong>SUCCESS!</strong> Your note has been added successfully.
         <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
       </div>";
       unset($_SESSION['insert']);  // Clear the session variable after displaying
     }
     ?>
     <!-- For displaying alert message on top (delete) -->
     <?php
     if (isset($_SESSION['delete']) && $_SESSION['delete']) {
         echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
         <strong>SUCCESS!</strong> Your note has been deleted successfully.
         <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
       </div>";
       unset($_SESSION['delete']);  // Clear the session variable after displaying
     }
     ?>

     <!-- For displaying alert message on top (update) -->
     <?php
     if (isset($_SESSION['update']) && $_SESSION['update']) {
         echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
         <strong>SUCCESS!</strong> Your note has been updated successfully.
         <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
       </div>";
       unset($_SESSION['update']);  // Clear the session variable after displaying
     }
     ?>
    <!-- Form -->
    <div class="container my-5">
        <h2 class="heading">Add A Note</h2>
        <form action="/crud/index.php" method="post">
            <div class="mb-3">
                <label class="form-label">Note Title:</label>
                <input type="text" id="title" name="title" class="form-control" placeholder="Enter Note Title">
            </div>
            <div class="mb-3">
                <label class="form-label">Note Description:</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <div class="Button">
                <button type="submit" class="btn btn-primary">Add Notes</button>
            </div>
        </form>
    </div>

    <!-- PHP -->
    <div class="container my-5">
        <!-- Table -->
        <table class="table" id="myTable">
            <thead>
                <tr>
                    <th scope="col">Sr No.</th>
                    <th scope="col">Title</th>
                    <th scope="col">Description</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM `notes`";
                $result = mysqli_query($conn, $sql);
                $srno = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    $srno++;
                    echo "<tr>
                    <th scope='row'>$srno</th>
                    <td>" . $row['title'] . "</td>
                    <td>" . $row['description'] . "</td>
                    <td> <button class='edit btn btn-sm btn-primary' id=" . $row['srno'] . " >Edit</button>
                    <button class='delete btn btn-sm btn-primary' id=d" . $row['srno'] . " >Delete</button></td>
                </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Enqueue jQuery for DataTables -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Enqueue JS for DataTables -->
    <script src="//cdn.datatables.net/2.1.2/js/dataTables.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#myTable').DataTable();
        });
    </script>

    <!-- Enqueue JS for modal -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- For Edit Modal -->
    <script>
        edits = document.getElementsByClassName('edit');
        Array.from(edits).forEach((element) => {
            element.addEventListener("click", (e) => {
                console.log("edit ", );
                tr = e.target.parentNode.parentNode;
                title = tr.getElementsByTagName("td")[0].innerText;
                description = tr.getElementsByTagName("td")[1].innerText;
                console.log(title, description);
                titleEdit.value = title;
                descriptionEdit.value = description;
                snoEdit.value = e.target.id;
                $('#editModal').modal('toggle');
                console.log(e.target.id);
            })
        })

        // -------------for delete button modal------------
        deletes = document.getElementsByClassName('delete');
        Array.from(deletes).forEach((element) => {
            element.addEventListener("click", (e) => {
                console.log("deletes ", );
                sno = e.target.id.substr(1,);

                if(confirm("Are you sure want to delete this note?")){
                  console.log("yes");
                  window.location = `/crud/index.php?delete=${sno}`;
                }else{
                  console.log("No")
                }
            })
        })
    </script>

</body>
</html>
