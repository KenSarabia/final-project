<?php
require_once __DIR__ . '/../includes/header.php';
require_login();
require_once __DIR__ . '/../includes/db.php';
$res = $pdo->query('SELECT * FROM residents ORDER BY resident_id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Residents</h2>
<button class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addModal">Add Resident</button>
<table class="table table-striped">
  <thead><tr><th>ID</th><th>Name</th><th>Birthdate</th><th>Gender</th><th>Purok</th><th>Actions</th></tr></thead>
  <tbody>
  <?php foreach($res as $r): ?>
    <tr>
      <td><?=htmlspecialchars($r['resident_id'])?></td>
      <td><?=htmlspecialchars($r['first_name'].' '.$r['last_name'])?></td>
      <td><?=htmlspecialchars($r['birthdate'])?></td>
      <td><?=htmlspecialchars($r['gender'])?></td>
      <td><?=htmlspecialchars($r['purok'])?></td>
      <td>
        <button class="btn btn-sm btn-secondary edit-btn" data-id="<?= $r['resident_id'] ?>" data-first="<?=htmlspecialchars($r['first_name'])?>" data-last="<?=htmlspecialchars($r['last_name'])?>" data-birth="<?=htmlspecialchars($r['birthdate'])?>" data-gender="<?=htmlspecialchars($r['gender'])?>" data-purok="<?=htmlspecialchars($r['purok'])?>">Edit</button>
        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
          <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $r['resident_id'] ?>">Delete</button>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="addForm" class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Add Resident</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-2"><label>First Name</label><input name="first_name" class="form-control" required></div>
        <div class="mb-2"><label>Last Name</label><input name="last_name" class="form-control" required></div>
        <div class="mb-2"><label>Birthdate</label><input name="birthdate" type="date" class="form-control" required></div>
        <div class="mb-2"><label>Gender</label><select name="gender" class="form-control"><option>Male</option><option>Female</option><option>Other</option></select></div>
        <div class="mb-2"><label>Purok</label><input name="purok" class="form-control" required></div>
      </div>
      <div class="modal-footer"><button class="btn btn-primary">Save</button></div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="editForm" class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Edit Resident</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" name="resident_id" id="edit_id">
        <div class="mb-2"><label>First Name</label><input id="edit_first" name="first_name" class="form-control" required></div>
        <div class="mb-2"><label>Last Name</label><input id="edit_last" name="last_name" class="form-control" required></div>
        <div class="mb-2"><label>Birthdate</label><input id="edit_birth" name="birthdate" type="date" class="form-control" required></div>
        <div class="mb-2"><label>Gender</label><select id="edit_gender" name="gender" class="form-control"><option>Male</option><option>Female</option><option>Other</option></select></div>
        <div class="mb-2"><label>Purok</label><input id="edit_purok" name="purok" class="form-control" required></div>
      </div>
      <div class="modal-footer"><button class="btn btn-primary">Update</button></div>
    </form>
  </div>
</div>

<script>
// Add resident via API
document.getElementById('addForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const fd = new FormData(this);
  const res = await fetch('api.php?action=create_resident',{method:'POST',body:fd});
  const j = await res.json(); showToast(j.message||'Saved', j.success ? 'success' : 'error'); if(j.success) setTimeout(() => location.reload(), 1000);
});

// Edit button
document.querySelectorAll('.edit-btn').forEach(btn=>{
  btn.addEventListener('click',()=>{
    document.getElementById('edit_id').value = btn.dataset.id;
    document.getElementById('edit_first').value = btn.dataset.first;
    document.getElementById('edit_last').value = btn.dataset.last;
    document.getElementById('edit_birth').value = btn.dataset.birth;
    document.getElementById('edit_gender').value = btn.dataset.gender;
    document.getElementById('edit_purok').value = btn.dataset.purok;
    var modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
  });
});

// Update resident
document.getElementById('editForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const fd = new FormData(this);
  const res = await fetch('api.php?action=update_resident',{method:'POST',body:fd});
  const j = await res.json(); showToast(j.message||'Updated', j.success ? 'success' : 'error'); if(j.success) setTimeout(() => location.reload(), 1000);
});

// Delete
document.querySelectorAll('.delete-btn').forEach(btn=>{
  btn.addEventListener('click', async ()=>{
    if(!confirm('Delete this resident?')) return;
    const fd = new FormData(); fd.append('resident_id', btn.dataset.id);
    const res = await fetch('api.php?action=delete_resident',{method:'POST',body:fd});
    const j = await res.json(); showToast(j.message||'Deleted', j.success ? 'success' : 'error'); if(j.success) setTimeout(() => location.reload(), 1000);
  });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
