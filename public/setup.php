<?php
// Run this once to create admin and staff users with password '1234'.
// Usage: place this project in htdocs and visit http://localhost/barangay_ris/public/setup.php
require_once __DIR__ . '/../includes/db.php';
function create_user($pdo,$username,$password,$role,$first,$last){
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    if($stmt->fetch()){
        return false;
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $ins = $pdo->prepare('INSERT INTO users (username,password,role,first_name,last_name) VALUES (?,?,?,?,?)');
    $ins->execute([$username,$hash,$role,$first,$last]);
    return true;
}
$created = [];
try{
    $created[] = create_user($pdo,'admin','1234','admin','System','Administrator') ? 'admin created' : 'admin exists';
    $created[] = create_user($pdo,'staff','1234','staff','Barangay','Staff') ? 'staff created' : 'staff exists';
} catch(Exception $e){
    die('Error: '.$e->getMessage());
}
?>
<!doctype html><html><body><h3>Setup Results</h3><ul><?php foreach($created as $c){ echo "<li>".htmlspecialchars($c)."</li>"; } ?></ul><p>Delete this file after running for security.</p></body></html>
