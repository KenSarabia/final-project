<?php
require_once __DIR__ . '/../includes/header.php';
require_login();
require_once __DIR__ . '/../includes/db.php';

// Only admin can access user management
if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}
?>
<h2>System Users</h2>
<button class="btn btn-primary mb-2" id="btnAdd">Add User</button>

<table class="table table-striped" id="usersTable">
  <thead>
    <tr>
      <th>ID</th>
      <th>Username</th>
      <th>Name</th>
      <th>Role</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody></tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="userForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">User</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="user_id" id="user_id">
        <div class="mb-2">
          <label>Username</label>
          <input name="username" id="username" class="form-control" required>
        </div>
        <div class="mb-2">
          <label>Password</label>
          <input type="password" name="password" id="password" class="form-control" placeholder="Leave blank to keep old password">
        </div>
        <div class="mb-2">
          <label>First Name</label>
          <input name="first_name" id="first_name" class="form-control" required>
        </div>
        <div class="mb-2">
          <label>Last Name</label>
          <input name="last_name" id="last_name" class="form-control" required>
        </div>
        <div class="mb-2">
          <label>Role</label>
          <select name="role" id="role" class="form-control" required>
            <option value="Admin">Admin</option>
            <option value="Staff">Staff</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
const tableBody = document.querySelector('#usersTable tbody');

async function loadUsers(){
  const res = await fetch('api.php?action=list_users');
  const j = await res.json();
  tableBody.innerHTML = '';
  if(j.success){
    j.data.forEach(u=>{
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${u.user_id}</td>
        <td>${u.username}</td>
        <td>${u.first_name} ${u.last_name}</td>
        <td>${u.role}</td>
        <td>
          <button class="btn btn-sm btn-secondary edit" data='${JSON.stringify(u)}'>Edit</button>
          <button class="btn btn-sm btn-danger del" data-id="${u.user_id}">Delete</button>
        </td>`;
      tableBody.appendChild(tr);
    });
  } else {
    showToast(j.message || 'Failed to load users', 'error');
  }
  attachEvents();
}

function attachEvents(){
  document.querySelectorAll('.edit').forEach(b=>{
    b.addEventListener('click', ()=>{
      const u = JSON.parse(b.getAttribute('data'));
      document.getElementById('user_id').value = u.user_id;
      document.getElementById('username').value = u.username;
      document.getElementById('first_name').value = u.first_name;
      document.getElementById('last_name').value = u.last_name;
      document.getElementById('role').value = u.role;
      document.getElementById('password').value = '';
      new bootstrap.Modal(document.getElementById('userModal')).show();
    });
  });

  document.querySelectorAll('.del').forEach(b=>{
    b.addEventListener('click', async ()=>{
      if(!confirm('Delete user?')) return;
      const fd = new FormData();
      fd.append('user_id', b.dataset.id);
      const res = await fetch('api.php?action=delete_user',{method:'POST',body:fd});
      const j = await res.json();
      showToast(j.message || 'Deleted', j.success ? 'success' : 'error');
      if(j.success) loadUsers();
    });
  });
}

document.getElementById('btnAdd').addEventListener('click', ()=>{
  document.getElementById('userForm').reset();
  document.getElementById('user_id').value = '';
  new bootstrap.Modal(document.getElementById('userModal')).show();
});

document.getElementById('userForm').addEventListener('submit', async e=>{
  e.preventDefault();
  const fd = new FormData(e.target);
  const id = fd.get('user_id');
  const action = id ? 'update_user' : 'create_user';
  const res = await fetch('api.php?action=' + action, {method: 'POST', body: fd});
  const j = await res.json();
  showToast(j.message || 'Saved', j.success ? 'success' : 'error');
  if(j.success){
    loadUsers();
    bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
  }
});

loadUsers();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
