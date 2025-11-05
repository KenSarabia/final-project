<?php
require_once __DIR__ . '/../includes/header.php';
require_login();
require_once __DIR__ . '/../includes/db.php';
?>
<h2>Officials</h2>
<button class="btn btn-primary mb-2" id="btnAdd">Add Official</button>
<table class="table table-striped" id="officialsTable"><thead><tr><th>ID</th><th>Name</th><th>Position</th><th>Contact</th><th>Address</th><th>Actions</th></tr></thead><tbody></tbody></table>

<!-- Modal -->
<div class="modal fade" id="officialModal" tabindex="-1"><div class="modal-dialog"><form id="officialForm" class="modal-content"><div class="modal-header"><h5 class="modal-title">Official</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="hidden" name="official_id" id="off_id"><div class="mb-2"><label>First Name</label><input name="first_name" id="off_first" class="form-control" required></div><div class="mb-2"><label>Last Name</label><input name="last_name" id="off_last" class="form-control" required></div><div class="mb-2"><label>Position</label><input name="position" id="off_pos" class="form-control" required></div><div class="mb-2"><label>Contact</label><input name="contact_number" id="off_contact" class="form-control"></div><div class="mb-2"><label>Address</label><input name="address" id="off_address" class="form-control"></div></div><div class="modal-footer"><button class="btn btn-primary">Save</button></div></form></div></div>

<script>
const offTableBody = document.querySelector('#officialsTable tbody');
async function loadOfficials(){
  const res = await fetch('api.php?action=list_officials'); const j = await res.json();
  offTableBody.innerHTML = '';
  if(j.success){ j.data.forEach(o=>{
      const deleteBtn = <?= json_encode($_SESSION['user']['role'] === 'admin') ?> ? `<button class="btn btn-sm btn-danger del" data-id='${o.official_id}'>Delete</button>` : '';
      const tr = document.createElement('tr'); tr.innerHTML = `<td>${o.official_id}</td><td>${o.first_name} ${o.last_name}</td><td>${o.position}</td><td>${o.contact_number||''}</td><td>${o.address||''}</td><td><button class="btn btn-sm btn-secondary edit" data='${JSON.stringify(o)}'>Edit</button> ${deleteBtn}</td>`;
      offTableBody.appendChild(tr);
  }); }
  attachOfficialsEvents();
}
function attachOfficialsEvents(){
  document.querySelectorAll('.edit').forEach(b=>{
    b.addEventListener('click', ()=>{
      const o = JSON.parse(b.getAttribute('data'));
      document.getElementById('off_id').value = o.official_id; document.getElementById('off_first').value = o.first_name; document.getElementById('off_last').value = o.last_name; document.getElementById('off_pos').value = o.position; document.getElementById('off_contact').value = o.contact_number; document.getElementById('off_address').value = o.address;
      new bootstrap.Modal(document.getElementById('officialModal')).show();
    });
  });
  document.querySelectorAll('.del').forEach(b=>{
    b.addEventListener('click', async ()=>{
      if(!confirm('Delete official?')) return; const fd=new FormData(); fd.append('official_id', b.dataset.id);
      const res = await fetch('api.php?action=delete_official',{method:'POST',body:fd}); const j = await res.json(); showToast(j.message||'Deleted', j.success ? 'success' : 'error'); if(j.success) loadOfficials();
    });
  });
}

// Add
document.getElementById('btnAdd').addEventListener('click', ()=>{ document.getElementById('officialForm').reset(); document.getElementById('off_id').value=''; new bootstrap.Modal(document.getElementById('officialModal')).show(); });

// Save (create or update)
document.getElementById('officialForm').addEventListener('submit', async function(e){ e.preventDefault(); const fd = new FormData(this); const id = fd.get('official_id'); const action = id? 'update_official' : 'create_official'; const res = await fetch('api.php?action='+action,{method:'POST',body:fd}); const j = await res.json(); showToast(j.message||'Saved', j.success ? 'success' : 'error'); if(j.success){ loadOfficials(); var modalEl = document.getElementById('officialModal'); var modal = bootstrap.Modal.getInstance(modalEl); modal.hide(); } });

loadOfficials();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
