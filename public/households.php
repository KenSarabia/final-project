<?php
require_once __DIR__ . '/../includes/header.php';
require_login();
require_once __DIR__ . '/../includes/db.php';

// Only admin can access households
if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}
?>

<h2>Households</h2>
<div id="alertPlaceholder"></div>

<button id="btnAdd" class="btn btn-primary mb-3">+ Add Household</button>

<table class="table table-bordered" id="tblHouseholds">
  <thead class="table-dark">
    <tr>
      <th>ID</th>
      <th>Household No</th>
      <th>Head</th>
      <th>Address</th>
      <th>Purok</th>
      <th>Created At</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody></tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="householdModal">
  <div class="modal-dialog">
    <form id="householdForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Household</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="household_id" id="household_id">

        <div class="mb-2">
          <label>Household No</label>
          <input type="text" name="household_no" id="household_no" class="form-control" required>
        </div>

        <div class="mb-2">
          <label>Head of Household</label>
          <select name="head_id" id="head_id" class="form-control" required></select>
        </div>

        <div class="mb-2">
          <label>Address</label>
          <input type="text" name="address" id="address" class="form-control" required>
        </div>

        <div class="mb-2">
          <label>Purok</label>
          <input type="text" name="purok" id="purok" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
async function fetchResidents() {
  const res = await fetch('api.php?action=get_residents');
  const j = await res.json();
  const sel = document.getElementById('head_id');
  sel.innerHTML = '<option value="">-- Select --</option>';
  if (j.success) {
    j.data.forEach(r => {
      sel.innerHTML += `<option value="${r.resident_id}">${r.first_name} ${r.last_name}</option>`;
    });
  } else {
    sel.innerHTML = '<option value="">Failed to load residents</option>';
  }
}


async function loadHouseholds() {
  const res = await fetch('api_households.php?action=list');
  const j = await res.json();
  const body = document.querySelector('#tblHouseholds tbody');
  body.innerHTML = '';
  if (j.success) {
    j.data.forEach(h => {
      body.innerHTML += `
        <tr>
          <td>${h.household_id}</td>
          <td>${h.household_no}</td>
          <td>${h.head_name || 'N/A'}</td>
          <td>${h.address}</td>
          <td>${h.purok}</td>
          <td>${h.created_at}</td>
          <td>
            <button class="btn btn-sm btn-secondary edit" data='${JSON.stringify(h)}'>Edit</button>
            <button class="btn btn-sm btn-danger del" data-id="${h.household_id}">Delete</button>
          </td>
        </tr>`;
    });
  }
  attachEvents();
}

function attachEvents() {
  document.querySelectorAll('.edit').forEach(b => {
    b.onclick = () => {
      const h = JSON.parse(b.getAttribute('data'));
      document.getElementById('household_id').value = h.household_id;
      document.getElementById('household_no').value = h.household_no;
      document.getElementById('head_id').value = h.head_id;
      document.getElementById('address').value = h.address;
      document.getElementById('purok').value = h.purok;
      new bootstrap.Modal('#householdModal').show();
    };
  });
  document.querySelectorAll('.del').forEach(b => {
    b.onclick = async () => {
      if (!confirm('Are you sure?')) return;
      const fd = new FormData();
      fd.append('household_id', b.dataset.id);
      const res = await fetch('api_households.php?action=delete', { method: 'POST', body: fd });
      const j = await res.json();
      showToast(j.message, j.success ? 'success' : 'error');
      if (j.success) loadHouseholds();
    };
  });
}

document.getElementById('btnAdd').onclick = async () => {
  const res = await fetch('api_households.php?action=next_household_no');
  const j = await res.json();
  document.getElementById('householdForm').reset();
  document.getElementById('household_no').value = j.next_no;
  new bootstrap.Modal('#householdModal').show();
};

document.getElementById('householdForm').onsubmit = async e => {
  e.preventDefault();
  const fd = new FormData(e.target);
  const act = fd.get('household_id') ? 'update' : 'create';
  const res = await fetch('api_households.php?action=' + act, { method: 'POST', body: fd });
  const j = await res.json();
  showToast(j.message, j.success ? 'success' : 'error');
  if (j.success) {
    bootstrap.Modal.getInstance(document.getElementById('householdModal')).hide();
    loadHouseholds();
  }
};

fetchResidents();
loadHouseholds();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
