<?php
require_once 'includes/db_connect.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $reason = $_POST['reason'];
    $status = $_POST['status'];
    $is_edit = isset($_POST['edit']);
    $id = $is_edit ? $_POST['id'] : null;

    $check_availability_sql = "SELECT COUNT(*) FROM appointments 
                               WHERE doctor_id = ? 
                               AND appointment_date = ? 
                               AND appointment_time = ?";

    if ($is_edit) {
        $check_availability_sql .= " AND id != ?";
    }

    $stmt_check = $conn->prepare($check_availability_sql);

    if ($is_edit) {
        $stmt_check->bind_param("issi", $doctor_id, $appointment_date, $appointment_time, $id);
    } else {
        $stmt_check->bind_param("iss", $doctor_id, $appointment_date, $appointment_time);
    }

    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row_check = $result_check->fetch_assoc();
    $count = $row_check['COUNT(*)'];
    $stmt_check->close();

    if ($count > 0) {
        $error = "The selected doctor is already booked at this exact time. Please choose a different time.";
        header("Location: appointments.php?error=" . urlencode($error));
        exit;
    }

    if (isset($_POST['add'])) {
        $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, reason, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $patient_id, $doctor_id, $appointment_date, $appointment_time, $reason, $status);
        if ($stmt->execute()) {
            $message = "Appointment added successfully!";
        } else {
            $error = "Error adding appointment: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($is_edit) {
        $stmt = $conn->prepare("UPDATE appointments SET patient_id = ?, doctor_id = ?, appointment_date = ?, appointment_time = ?, reason = ?, status = ? WHERE id = ?");
        $stmt->bind_param("iissssi", $patient_id, $doctor_id, $appointment_date, $appointment_time, $reason, $status, $id);
        if ($stmt->execute()) {
            $message = "Appointment updated successfully!";
        } else {
            $error = "Error updating appointment: " . $stmt->error;
        }
        $stmt->close();
    }
    header("Location: appointments.php?message=" . urlencode($message) . "&error=" . urlencode($error));
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Appointment deleted successfully!";
    } else {
        $error = "Error deleting appointment: " . $stmt->error;
    }
    $stmt->close();
    header("Location: appointments.php?message=" . urlencode($message) . "&error=" . urlencode($error));
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

<h1>Manage Appointments</h1>
<a href="#" class="btn btn-primary add-new-btn">Add New Appointment</a>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Date</th>
                <th>Time</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT 
                        a.id, 
                        p.id AS patient_id, 
                        p.name AS patient_name, 
                        d.id AS doctor_id, 
                        d.name AS doctor_name, 
                        a.appointment_date, 
                        a.appointment_time, 
                        a.reason, 
                        a.status 
                    FROM appointments a
                    JOIN patients p ON a.patient_id = p.id
                    JOIN doctors d ON a.doctor_id = d.id";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr data-id='{$row['id']}' 
                              data-patient-id='{$row['patient_id']}' 
                              data-doctor-id='{$row['doctor_id']}' 
                              data-appointment-date='{$row['appointment_date']}' 
                              data-appointment-time='{$row['appointment_time']}' 
                              data-reason='{$row['reason']}' 
                              data-status='{$row['status']}'>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['patient_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['doctor_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['appointment_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['appointment_time']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['reason']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td class='actions-cell'>";
                    echo "<a href='#' class='btn btn-info edit-btn'>Edit</a> ";
                    echo "<a href='appointments.php?action=delete&id=" . $row['id'] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this appointment?\");'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No appointments found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<div id="addEditModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Add New Appointment</h2>
            <span class="close-btn">&times;</span>
        </div>
        <form id="crudForm" action="appointments.php" method="POST">
            <input type="hidden" name="id" id="entityId">
            <div class="form-group">
                <label for="patient_id">Patient</label>
                <select id="patient_id" name="patient_id" required>
                    <option value="">Select a Patient</option>
                    <?php 
                    $patients_list = $conn->query("SELECT id, name FROM patients ORDER BY name");
                    while ($patient = $patients_list->fetch_assoc()): ?>
                        <option value="<?php echo $patient['id']; ?>">
                            <?php echo htmlspecialchars($patient['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="doctor_id">Doctor</label>
                <select id="doctor_id" name="doctor_id" required>
                    <option value="">Select a Doctor</option>
                    <?php 
                    $doctors_list = $conn->query("SELECT id, name FROM doctors ORDER BY name");
                    while ($doctor = $doctors_list->fetch_assoc()): ?>
                        <option value="<?php echo $doctor['id']; ?>">
                            <?php echo htmlspecialchars($doctor['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="appointment_date">Date</label>
                <input type="date" id="appointment_date" name="appointment_date" required>
            </div>
            <div class="form-group">
                <label for="appointment_time">Time</label>
                <input type="time" id="appointment_time" name="appointment_time" required>
            </div>
            <div class="form-group">
                <label for="reason">Reason</label>
                <textarea id="reason" name="reason" required></textarea>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="Scheduled">Scheduled</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="submit" name="add" class="btn btn-success">Add Appointment</button>
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