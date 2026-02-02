// dashboard.js - Dashboard logic for CRUD tables and role-based UI

document.addEventListener('DOMContentLoaded', () => {
  // User CRUD Table
  if (document.getElementById('user-crud-table')) {
    new CRUDTable({
      root: '#user-crud-table',
      columns: [
        { key: 'username', label: 'Username' },
        { key: 'role', label: 'Role' },
        { key: 'created_at', label: 'Created' }
      ],
      fetchUrl: 'user_fetch.php',
      createUrl: 'user_create.php',
      updateUrl: 'user_update.php',
      deleteUrl: 'user_delete.php',
      actions: [
        { type: 'edit', label: 'Edit' },
        { type: 'delete', label: 'Delete' }
      ]
    });
  }
  // Role CRUD Table
  if (document.getElementById('role-crud-table')) {
    new CRUDTable({
      root: '#role-crud-table',
      columns: [
        { key: 'name', label: 'Role Name' },
        { key: 'description', label: 'Description' }
      ],
      fetchUrl: 'role_fetch.php',
      createUrl: 'role_create.php',
      updateUrl: 'role_update.php',
      deleteUrl: 'role_delete.php',
      actions: [
        { type: 'edit', label: 'Edit' },
        { type: 'delete', label: 'Delete' }
      ]
    });
  }
  // TODO: Add role-based UI logic (hide actions for non-admin, protect Admin role, etc.)
  // Get current user role from a global JS variable (set in PHP)
  const currentUserRole = window.currentUserRole || 'admin'; // fallback to admin

  // User CRUD Table
  if (document.getElementById('user-crud-table')) {
    new CRUDTable({
      root: '#user-crud-table',
      columns: [
        { key: 'username', label: 'Username' },
        { key: 'role', label: 'Role' },
        { key: 'created_at', label: 'Created' }
      ],
      fetchUrl: 'user_fetch.php',
      createUrl: currentUserRole === 'admin' ? 'user_create.php' : null,
      updateUrl: currentUserRole === 'admin' ? 'user_update.php' : null,
      deleteUrl: currentUserRole === 'admin' ? 'user_delete.php' : null,
      actions: currentUserRole === 'admin' ? [
        { type: 'edit', label: 'Edit' },
        { type: 'delete', label: 'Delete' }
      ] : [],
    });
  }
  // Role CRUD Table
  if (document.getElementById('role-crud-table')) {
    new CRUDTable({
      root: '#role-crud-table',
      columns: [
        { key: 'name', label: 'Role Name' },
        { key: 'description', label: 'Description' }
      ],
      fetchUrl: 'role_fetch.php',
      createUrl: currentUserRole === 'admin' ? 'role_create.php' : null,
      updateUrl: currentUserRole === 'admin' ? 'role_update.php' : null,
      deleteUrl: currentUserRole === 'admin' ? 'role_delete.php' : null,
      actions: currentUserRole === 'admin' ? [
        { type: 'edit', label: 'Edit', filter: row => row.name.toLowerCase() !== 'admin' },
        { type: 'delete', label: 'Delete', filter: row => row.name.toLowerCase() !== 'admin' }
      ] : [],
    });
  }
});
