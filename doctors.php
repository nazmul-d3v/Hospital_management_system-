<?php
require_once 'includes/db_connect.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    if (isset($_POST['add'])) {
        $stmt = $conn->prepare("INSERT INTO doctors (name, specialization, phone, email) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $specialization, $phone, $email);
        if ($stmt->execute()) {
            $message = "Doctor added successfully!";
        } else {
            $error = "Error adding doctor: " . $stmt->error;
        }
        $stmt->close();
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE doctors SET name = ?, specialization = ?, phone = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $specialization, $phone, $email, $id);
        if ($stmt->execute()) {
            $message = "Doctor updated successfully!";
        } else {
            $error = "Error updating doctor: " . $stmt->error;
        }
        $stmt->close();
    }
    header("Location: doctors.php?message=" . urlencode($message) . "&error=" . urlencode($error));
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM doctors WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Doctor deleted successfully!";
    } else {
        $error = "Error deleting doctor: " . $stmt->error;
    }
    $stmt->close();
    header("Location: doctors.php?message=" . urlencode($message) . "&error=" . urlencode($error));
    exit;
}

require_once 'includes/header.php';

if (isset($_GET['message']) && $_GET['message']) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_GET['message']) . '</div>';
}
if (isset($_GET['error']) && $_GET['error']) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
}
?>

<h1>Manage Doctors</h1>
<a href="#" class="btn btn-primary add-new-btn">Add New Doctor</a>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Specialization</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM doctors";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr data-id='{$row['id']}' data-name='{$row['name']}' data-specialization='{$row['specialization']}' data-phone='{$row['phone']}' data-email='{$row['email']}'>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['specialization']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td class='actions-cell'>";
                    echo "<a href='#' class='btn btn-info edit-btn'>Edit</a> ";
                    echo "<a href='doctors.php?action=delete&id=" . $row['id'] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this doctor?\");'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No doctors found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<div id="addEditModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Add New Doctor</h2>
            <span class="close-btn">&times;</span>
        </div>
        <form id="crudForm" action="doctors.php" method="POST">
            <input type="hidden" name="id" id="entityId">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="specialization">Specialization</label>
                <input type="text" id="specialization" name="specialization" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email">
            </div>
            <div class="modal-footer">
                <button type="submit" name="add" class="btn btn-success">Add Doctor</button>
                <button type="submit" name="edit" class="btn btn-info" style="display:none;">Save Changes</button>
                <a href="#" class="btn btn-secondary close-btn">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php
$conn->close();
require_once 'includes/footer.php';
?>