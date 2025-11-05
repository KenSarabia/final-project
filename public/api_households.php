<?php
require_once __DIR__ . '/../includes/db.php';
session_start();
header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? null;
if (!$action) {
    echo json_encode(['success' => false, 'message' => 'No action specified']);
    exit;
}

try {
    // LIST HOUSEHOLDS
    if ($action === 'list') {
        $stmt = $pdo->query("
            SELECT 
                h.household_id,
                h.household_no,
                h.address,
                h.purok,
                h.created_at,
                h.head_id,
                CONCAT(r.first_name, ' ', r.last_name) AS head_name
            FROM households h
            LEFT JOIN residents r ON h.head_id = r.resident_id
            ORDER BY h.household_id DESC
        ");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }

    // GENERATE NEXT HOUSEHOLD NUMBER
    if ($action === 'next_household_no') {
        $stmt = $pdo->query("SELECT household_no FROM households ORDER BY household_id DESC LIMIT 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $next = 'HH-001';
        if ($row && !empty($row['household_no'])) {
            $num = (int) preg_replace('/\D/', '', $row['household_no']);
            $next = sprintf('HH-%03d', $num + 1);
        }
        echo json_encode(['success' => true, 'next_no' => $next]);
        exit;
    }

    // CREATE
    if ($action === 'create') {
        $no = trim($_POST['household_no'] ?? '');
        $head = (int) ($_POST['head_id'] ?? 0);
        $addr = trim($_POST['address'] ?? '');
        $purok = trim($_POST['purok'] ?? '');

        if (!$no || !$head || !$addr || !$purok) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }

        $check = $pdo->prepare("SELECT COUNT(*) FROM households WHERE household_no = ?");
        $check->execute([$no]);
        if ($check->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Household number already exists.']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO households (household_no, head_id, address, purok, created_at)
                               VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$no, $head, $addr, $purok]);

        echo json_encode(['success' => true, 'message' => 'Household created successfully.']);
        exit;
    }

    // UPDATE
    if ($action === 'update') {
        $id = (int) ($_POST['household_id'] ?? 0);
        $no = trim($_POST['household_no'] ?? '');
        $head = (int) ($_POST['head_id'] ?? 0);
        $addr = trim($_POST['address'] ?? '');
        $purok = trim($_POST['purok'] ?? '');

        if (!$id || !$no || !$head || !$addr || !$purok) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
            exit;
        }

        $check = $pdo->prepare("SELECT COUNT(*) FROM households WHERE household_no = ? AND household_id != ?");
        $check->execute([$no, $id]);
        if ($check->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Another household with this number already exists.']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE households SET household_no=?, head_id=?, address=?, purok=? WHERE household_id=?");
        $stmt->execute([$no, $head, $addr, $purok, $id]);

        echo json_encode(['success' => true, 'message' => 'Household updated successfully.']);
        exit;
    }

    // DELETE
    if ($action === 'delete') {
        $id = (int) ($_POST['household_id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid household ID.']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM households WHERE household_id = ?");
        $stmt->execute([$id]);

        echo json_encode(['success' => true, 'message' => 'Household deleted successfully.']);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
