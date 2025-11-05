<?php
require_once __DIR__ . '/../includes/db.php';
session_start();
header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? null;

if (!$action) { 
    echo json_encode(['success' => false, 'message' => 'No action']); 
    exit; 
}

// ✅ Helper: Role-based access control
function requireRole($allowedRoles) {
    if (empty($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit;
    }
    $role = $_SESSION['user']['role'] ?? '';
    if (!in_array($role, (array)$allowedRoles)) {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }
}

try {
    // --------------------------------------------------
    // ✅ Fetch all residents (for dropdown)
    // --------------------------------------------------
    if ($action === 'get_residents' || $action === 'list_residents') {
        requireRole(['admin','staff']);
        $stmt = $pdo->query("SELECT resident_id, first_name, last_name FROM residents ORDER BY first_name ASC");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }

    // --------------------------------------------------
    // Authentication actions
    // --------------------------------------------------
    if ($action === 'current_user') {
        echo json_encode(['success' => true, 'user' => $_SESSION['user'] ?? null]);
        exit;
    }

    // --------------------------------------------------
    // Charts (staff and admin can view)
    // --------------------------------------------------
    if (in_array($action, ['chart_gender', 'chart_age', 'chart_purok'])) {
        requireRole(['admin','staff']);
    }

    if ($action === 'chart_gender') {
        $stmt = $pdo->query("SELECT gender, COUNT(*) as cnt FROM residents GROUP BY gender");
        $labels = [];
        $data = [];
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) { 
            $labels[] = $r['gender']; 
            $data[] = (int)$r['cnt']; 
        }
        echo json_encode(['labels' => $labels, 'data' => $data]);
        exit;
    }

    if ($action === 'chart_age') {
        $stmt = $pdo->query("SELECT birthdate FROM residents");
        $groups = ['0-17' => 0, '18-30' => 0, '31-50' => 0, '51+' => 0];
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $b = new DateTime($r['birthdate']);
            $age = (new DateTime())->diff($b)->y;
            if ($age <= 17) $groups['0-17']++; 
            elseif ($age <= 30) $groups['18-30']++; 
            elseif ($age <= 50) $groups['31-50']++; 
            else $groups['51+']++;
        }
        echo json_encode(['labels' => array_keys($groups), 'data' => array_values($groups)]);
        exit;
    }

    if ($action === 'chart_purok') {
        $stmt = $pdo->query("SELECT purok, COUNT(*) as cnt FROM residents GROUP BY purok");
        $labels = []; $data = [];
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) { 
            $labels[] = $r['purok']; 
            $data[] = (int)$r['cnt']; 
        }
        echo json_encode(['labels' => $labels, 'data' => $data]);
        exit;
    }

    // --------------------------------------------------
    // Residents CRUD
    // --------------------------------------------------
    if (in_array($action, ['create_resident','update_resident'])) requireRole(['admin','staff']);
    if ($action === 'delete_resident') requireRole(['admin']);

    if ($action === 'create_resident') {
        $fn = $_POST['first_name']; 
        $ln = $_POST['last_name']; 
        $bd = $_POST['birthdate']; 
        $gender = $_POST['gender']; 
        $purok = $_POST['purok'];
        $stmt = $pdo->prepare('INSERT INTO residents (first_name,last_name,birthdate,gender,purok,address) VALUES (?,?,?,?,?,?)');
        $stmt->execute([$fn, $ln, $bd, $gender, $purok, '']);
        echo json_encode(['success' => true, 'message' => 'Resident created']); 
        exit;
    }

    if ($action === 'update_resident') {
        $id = (int)$_POST['resident_id']; 
        $fn = $_POST['first_name']; 
        $ln = $_POST['last_name']; 
        $bd = $_POST['birthdate']; 
        $gender = $_POST['gender']; 
        $purok = $_POST['purok'];
        $stmt = $pdo->prepare('UPDATE residents SET first_name=?, last_name=?, birthdate=?, gender=?, purok=? WHERE resident_id=?');
        $stmt->execute([$fn, $ln, $bd, $gender, $purok, $id]);
        echo json_encode(['success' => true, 'message' => 'Updated']); 
        exit;
    }

    if ($action === 'delete_resident') {
        $id = (int)($_POST['resident_id'] ?? 0);
        $pdo->prepare('DELETE FROM barangay_clearances WHERE resident_id = ?')->execute([$id]);
        $pdo->prepare('DELETE FROM households WHERE head_id = ?')->execute([$id]);
        $pdo->prepare('DELETE FROM residents WHERE resident_id = ?')->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Resident deleted successfully']);
        exit;
    }

    // --------------------------------------------------
    // Officials CRUD
    // --------------------------------------------------
    if ($action === 'list_officials') requireRole(['admin','staff']);
    if (in_array($action, ['create_official','update_official'])) requireRole(['admin','staff']);
    if ($action === 'delete_official') requireRole(['admin']);

    if ($action === 'list_officials') {
        $stmt = $pdo->query('SELECT * FROM officials ORDER BY official_id DESC');
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]); 
        exit;
    }

    if ($action === 'create_official') {
        $fn = $_POST['first_name']; 
        $ln = $_POST['last_name']; 
        $pos = $_POST['position']; 
        $contact = $_POST['contact_number']; 
        $addr = $_POST['address'];
        $stmt = $pdo->prepare('INSERT INTO officials (first_name,last_name,position,contact_number,address) VALUES (?,?,?,?,?)');
        $stmt->execute([$fn, $ln, $pos, $contact, $addr]);
        echo json_encode(['success' => true, 'message' => 'Official created']); 
        exit;
    }

    if ($action === 'update_official') {
        $id = (int)$_POST['official_id']; 
        $fn = $_POST['first_name']; 
        $ln = $_POST['last_name']; 
        $pos = $_POST['position']; 
        $contact = $_POST['contact_number']; 
        $addr = $_POST['address'];
        $stmt = $pdo->prepare('UPDATE officials SET first_name=?, last_name=?, position=?, contact_number=?, address=? WHERE official_id=?');
        $stmt->execute([$fn, $ln, $pos, $contact, $addr, $id]);
        echo json_encode(['success' => true, 'message' => 'Official updated']); 
        exit;
    }

    if ($action === 'delete_official') {
        $id = (int)($_POST['official_id'] ?? 0);
        $stmt = $pdo->prepare('DELETE FROM officials WHERE official_id = ?');
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Deleted']); 
        exit;
    }

    // --------------------------------------------------
    // Clearances CRUD
    // --------------------------------------------------
    if ($action === 'list_clearances') requireRole(['admin','staff']);
    if (in_array($action, ['create_clearance','update_clearance'])) requireRole(['admin','staff']);
    if ($action === 'delete_clearance') requireRole(['admin']);

    if ($action === 'list_clearances') {
        $stmt = $pdo->query("SELECT bc.*, r.first_name AS rfn, r.last_name AS rln, u.first_name AS ufn, u.last_name AS uln 
                             FROM barangay_clearances bc
                             LEFT JOIN residents r ON bc.resident_id = r.resident_id
                             LEFT JOIN users u ON bc.issued_by = u.user_id
                             ORDER BY bc.clearance_id DESC");
        $rows = [];
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = [
                'clearance_id' => (int)$r['clearance_id'],
                'resident_id' => (int)$r['resident_id'],
                'resident_name' => trim(($r['rfn'] ?? '').' '.($r['rln'] ?? '')),
                'purpose' => $r['purpose'],
                'issued_date' => $r['issued_date'],
                'status' => $r['status'],
                'issued_by' => $r['issued_by'] ? (int)$r['issued_by'] : null,
                'issued_by_name' => isset($r['ufn']) ? trim($r['ufn'].' '.($r['uln'] ?? '')) : null,
            ];
        }
        echo json_encode(['success' => true, 'data' => $rows]);
        exit;
    }

    if ($action === 'create_clearance') {
        $resident_id = (int)$_POST['resident_id'];
        $purpose = trim($_POST['purpose']);
        $issued_date = $_POST['issued_date'];
        $status = $_POST['status'];
        $issued_by = $_SESSION['user']['user_id'] ?? null;

        $stmt = $pdo->prepare("INSERT INTO barangay_clearances (resident_id, purpose, issued_date, status, issued_by)
                               VALUES (?,?,?,?,?)");
        $stmt->execute([$resident_id, $purpose, $issued_date, $status, $issued_by]);
        echo json_encode(['success' => true, 'message' => 'Clearance created']);
        exit;
    }

    if ($action === 'update_clearance') {
        $id = (int)$_POST['clearance_id'];
        $resident_id = (int)$_POST['resident_id'];
        $purpose = trim($_POST['purpose']);
        $issued_date = $_POST['issued_date'];
        $status = $_POST['status'];
        $issued_by = $_SESSION['user']['user_id'] ?? null;

        $stmt = $pdo->prepare("UPDATE barangay_clearances
                               SET resident_id=?, purpose=?, issued_date=?, status=?, issued_by=?
                               WHERE clearance_id=?");
        $stmt->execute([$resident_id, $purpose, $issued_date, $status, $issued_by, $id]);
        echo json_encode(['success' => true, 'message' => 'Clearance updated']);
        exit;
    }

    if ($action === 'delete_clearance') {
        $id = (int)$_POST['clearance_id'];
        $stmt = $pdo->prepare("DELETE FROM barangay_clearances WHERE clearance_id=?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Clearance deleted']);
        exit;
    }

    // --------------------------------------------------
    // Households CRUD (admin only)
    // --------------------------------------------------
    if (preg_match('/household/', $action)) {
        requireRole(['admin']);
    }

    // --------------------------------------------------
    // Users CRUD (admin only)
    // --------------------------------------------------
    if (preg_match('/user/', $action) && !in_array($action, ['login','current_user'])) {
        requireRole(['admin']);
    }

    // --------------------------------------------------
    // Users CRUD (admin only)
    // --------------------------------------------------
    if ($action === 'list_users') {
        requireRole(['admin']);
        $stmt = $pdo->query('SELECT user_id, username, first_name, last_name, role FROM users ORDER BY user_id DESC');
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }

    if ($action === 'create_user') {
        requireRole(['admin']);
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $role = strtolower(trim($_POST['role']));
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, password, first_name, last_name, role) VALUES (?,?,?,?,?)');
        $stmt->execute([$username, $hashedPassword, $first_name, $last_name, $role]);
        echo json_encode(['success' => true, 'message' => 'User created']);
        exit;
    }

    if ($action === 'update_user') {
        requireRole(['admin']);
        $id = (int)$_POST['user_id'];
        $username = trim($_POST['username']);
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $role = strtolower(trim($_POST['role']));
        
        if (!empty($_POST['password'])) {
            $hashedPassword = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET username=?, password=?, first_name=?, last_name=?, role=? WHERE user_id=?');
            $stmt->execute([$username, $hashedPassword, $first_name, $last_name, $role, $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET username=?, first_name=?, last_name=?, role=? WHERE user_id=?');
            $stmt->execute([$username, $first_name, $last_name, $role, $id]);
        }
        echo json_encode(['success' => true, 'message' => 'User updated']);
        exit;
    }

    if ($action === 'delete_user') {
        requireRole(['admin']);
        $id = (int)$_POST['user_id'];
        $stmt = $pdo->prepare('DELETE FROM users WHERE user_id = ?');
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'User deleted']);
        exit;
    }

    // --------------------------------------------------
    // Login
    // --------------------------------------------------
    if ($action === 'login') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'role' => $user['role'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name']
            ];
            echo json_encode(['success' => true, 'user' => $_SESSION['user']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
        }
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Unknown action']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
