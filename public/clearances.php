<?php
require_once __DIR__ . '/../includes/header.php';
require_login();
require_once __DIR__ . '/../includes/db.php';

// Fetch residents and users for form selects
$resStmt = $pdo->query('SELECT resident_id, first_name, last_name FROM residents ORDER BY first_name');
$residents = $resStmt->fetchAll(PDO::FETCH_ASSOC);

$userStmt = $pdo->query('SELECT user_id, username, first_name, last_name FROM users ORDER BY username');
$users = $userStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Barangay Clearances</h2>
<button class="btn btn-primary mb-2" id="btnAdd">Add Clearance</button>
<table class="table table-striped" id="clearancesTable">
  <thead><tr><th>ID</th><th>Resident</th><th>Purpose</th><th>Issued Date</th><th>Status</th><th>Issued By</th><th>Actions</th></tr></thead>
  <tbody></tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="clearanceModal" tabindex="-1"><div class="modal-dialog"><form id="clearanceForm" class="modal-content">
  <div class="modal-header"><h5 class="modal-title">Clearance</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <input type="hidden" name="clearance_id" id="cl_id">
    <div class="mb-2">
      <label>Resident</label>
      <select name="resident_id" id="cl_resident" class="form-control" required>
        <option value="">-- Select Resident --</option>
        <?php foreach($residents as $r): ?>
          <option value="<?= $r['resident_id'] ?>"><?= htmlspecialchars($r['first_name'].' '.$r['last_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-2"><label>Purpose</label><input name="purpose" id="cl_purpose" class="form-control" required></div>
    <div class="mb-2"><label>Issued Date</label><input name="issued_date" id="cl_date" type="date" class="form-control" required></div>
    <div class="mb-2"><label>Status</label>
      <select name="status" id="cl_status" class="form-control">
        <option>Pending</option>
        <option>Approved</option>
        <option>Released</option>
      </select>
    </div>
    <div class="mb-2"><label>Issued By</label>
      <select name="issued_by" id="cl_issued_by" class="form-control">
        <option value="">-- Select User (or leave blank for current user) --</option>
        <?php foreach($users as $u): ?>
          <option value="<?= $u['user_id'] ?>"><?= htmlspecialchars($u['first_name'].' '.$u['last_name'].' ('.$u['username'].')') ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="modal-footer"><button class="btn btn-primary">Save</button></div>
</form></div></div>

<script>
const tableBody = document.querySelector('#clearancesTable tbody');
async function loadClearances(){
  const res = await fetch('api.php?action=list_clearances');
  const j = await res.json();
  tableBody.innerHTML = '';
  if(j.success){
    j.data.forEach(c=>{
      const tr = document.createElement('tr');
      const deleteBtn = <?= json_encode($_SESSION['user']['role'] === 'admin') ?> ? `<button class="btn btn-sm btn-danger del" data-id='${c.clearance_id}'>Delete</button>` : '';
      tr.innerHTML = `<td>${c.clearance_id}</td><td>${c.resident_name}</td><td>${c.purpose}</td><td>${c.issued_date}</td><td>${c.status}</td><td>${c.issued_by_name||''}</td><td>
        <button class="btn btn-sm btn-secondary edit" data='${JSON.stringify(c)}'>Edit</button>
        ${deleteBtn}
      </td>`;
      tableBody.appendChild(tr);
    });
  } else {
    showToast(j.message || 'Failed to load', 'error');
  }
  attachEvents();
}

function attachEvents(){
  document.querySelectorAll('.edit').forEach(b=>{
    b.addEventListener('click', ()=>{
      const c = JSON.parse(b.getAttribute('data'));
      document.getElementById('cl_id').value = c.clearance_id;
      document.getElementById('cl_resident').value = c.resident_id;
      document.getElementById('cl_purpose').value = c.purpose;
      document.getElementById('cl_date').value = c.issued_date;
      document.getElementById('cl_status').value = c.status;
      document.getElementById('cl_issued_by').value = c.issued_by || '';
      new bootstrap.Modal(document.getElementById('clearanceModal')).show();
    });
  });
  // Only attach delete events if user is admin
  if (<?= json_encode($_SESSION['user']['role'] === 'admin') ?>) {
    document.querySelectorAll('.del').forEach(b=>{
      b.addEventListener('click', async ()=>{
        if(!confirm('Delete clearance?')) return;
        const fd = new FormData(); fd.append('clearance_id', b.dataset.id);
        const res = await fetch('api.php?action=delete_clearance',{method:'POST',body:fd});
        const j = await res.json(); showToast(j.message||'Deleted', j.success ? 'success' : 'error'); if(j.success) loadClearances();
      });
    });
  }
}

document.getElementById('btnAdd').addEventListener('click', ()=>{
  document.getElementById('clearanceForm').reset();
  document.getElementById('cl_id').value = '';
  // set date to today
  document.getElementById('cl_date').value = new Date().toISOString().substr(0,10);
  // default status to Approved
  document.getElementById('cl_status').value = 'Approved';
  // set issued_by to blank to use current user by default
  document.getElementById('cl_issued_by').value = '';
  new bootstrap.Modal(document.getElementById('clearanceModal')).show();
});

document.getElementById('clearanceForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const fd = new FormData(this);
  const id = fd.get('clearance_id');
  const action = id ? 'update_clearance' : 'create_clearance';
  const res = await fetch('api.php?action='+action,{method:'POST',body:fd});
  const j = await res.json(); showToast(j.message||'Saved', j.success ? 'success' : 'error'); if(j.success){ loadClearances(); var modalEl = document.getElementById('clearanceModal'); var modal = bootstrap.Modal.getInstance(modalEl); if(modal) modal.hide(); }
});

loadClearances();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
