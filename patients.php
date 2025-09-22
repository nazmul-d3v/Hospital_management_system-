<?php
require_once 'includes/db_connect.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    if (isset($_POST['add'])) {
        $stmt = $conn->prepare("INSERT INTO patients (name, date_of_birth, gender, phone, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $date_of_birth, $gender, $phone, $address);
        if ($stmt->execute()) {
            $message = "Patient added successfully!";
        } else {
            $error = "Error adding patient: " . $stmt->error;
        }
        $stmt->close();
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE patients SET name = ?, date_of_birth = ?, gender = ?, phone = ?, address = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $name, $date_of_birth, $gender, $phone, $address, $id);
        if ($stmt->execute()) {
            $message = "Patient updated successfully!";
        } else {
            $error = "Error updating patient: " . $stmt->error;
        }
        $stmt->close();
    }
    header("Location: patients.php?message=" . urlencode($message) . "&error=" . urlencode($error));
    exit;
}

// Handle Delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM patients WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Patient deleted successfully!";
    } else {
        $error = "Error deleting patient: " . $stmt->error;
    }
    $stmt->close();
    header("Location: patients.php?message=" . urlencode($message) . "&error=" . urlencode($error));
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

<h1>Manage Patients</h1>
<a href="#" class="btn btn-primary add-new-btn">Add New Patient</a>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Date of Birth</th>
                <th>Gender</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM patients";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr data-id='{$row['id']}' data-name='{$row['name']}' data-date-of-birth='{$row['date_of_birth']}' data-gender='{$row['gender']}' data-phone='{$row['phone']}' data-address='{$row['address']}'>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date_of_birth']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                    echo "<td class='actions-cell'>";
                    echo "<a href='#' class='btn btn-info edit-btn'>Edit</a> ";
                    echo "<a href='patients.php?action=delete&id=" . $row['id'] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this patient?\");'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No patients found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<div id="addEditModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Add New Patient</h2>
            <span class="close-btn">&times;</span>
        </div>
        <form id="crudForm" action="patients.php" method="POST">
            <input type="hidden" name="id" id="entityId">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="date_of_birth">Date of Birth</label>
                <input type="date" id="date_of_birth" name="date_of_birth" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone">
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address"></textarea>
            </div>
            <div class="modal-footer">
                <button type="submit" name="add" class="btn btn-success">Add Patient</button>
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