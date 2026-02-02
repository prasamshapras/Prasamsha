// crud_table.js - Reusable CRUD Table Component (Vanilla JS)
// This is a skeleton for a modern, accessible, paginated, searchable CRUD table.
// Usage: instantiate CRUDTable for users or roles, pass config and data endpoints.

class CRUDTable {
  constructor({ root, columns, fetchUrl, createUrl, updateUrl, deleteUrl, search = true, pagination = true, pageSize = 10, actions = [] }) {
    this.root = typeof root === 'string' ? document.querySelector(root) : root;
    this.columns = columns;
    this.fetchUrl = fetchUrl;
    this.createUrl = createUrl;
    this.updateUrl = updateUrl;
    this.deleteUrl = deleteUrl;
    this.searchEnabled = search;
    this.paginationEnabled = pagination;
    this.pageSize = pageSize;
    this.actions = actions;
    this.data = [];
    this.page = 1;
    this.total = 0;
    this.query = '';
    this.init();
  }

  async init() {
    this.renderSkeleton();
    await this.fetchData();
    this.attachEvents();
  }

  renderSkeleton() {
    this.root.innerHTML = `
      <div class="crud-table-toolbar">
        ${this.searchEnabled ? '<input type="search" class="crud-table-search" placeholder="Search..." />' : ''}
        <button class="btn primary crud-table-add">Add</button>
      </div>
      <div class="crud-table-responsive">
        <table class="crud-table">
          <thead><tr>${this.columns.map(col => `<th>${col.label}</th>`).join('')}${this.actions.length ? '<th>Actions</th>' : ''}</tr></thead>
          <tbody></tbody>
        </table>
      </div>
      <div class="crud-table-pagination"></div>
    `;
  }

  async fetchData() {
    // For demo, use static data. Replace with fetch(this.fetchUrl)
    // Example: const res = await fetch(`${this.fetchUrl}?q=${encodeURIComponent(this.query)}&page=${this.page}`);
    // this.data = await res.json();
    // this.total = ...;
    this.data = [];
    this.total = 0;
    this.renderRows();
    this.renderPagination();
  }

  renderRows() {
    const tbody = this.root.querySelector('tbody');
    tbody.innerHTML = this.data.length ? this.data.map(row => `
      <tr>
        ${this.columns.map(col => `<td>${row[col.key]}</td>`).join('')}
        ${this.actions.length ? `<td>${this.actions.map(action => `<button class="btn smallbtn" data-action="${action.type}" data-id="${row.id}">${action.label}</button>`).join('')}</td>` : ''}
      </tr>
    `).join('') : `<tr><td colspan="${this.columns.length + (this.actions.length ? 1 : 0)}" class="muted">No data found.</td></tr>`;
  }

  renderPagination() {
    const pag = this.root.querySelector('.crud-table-pagination');
    if (!this.paginationEnabled) { pag.innerHTML = ''; return; }
    // For demo, no real pagination
    pag.innerHTML = '';
  }

  attachEvents() {
    // Add event listeners for search, add, edit, delete, pagination
    // ...
  }
}

// Example usage for users and roles will be initialized in dashboard.js
