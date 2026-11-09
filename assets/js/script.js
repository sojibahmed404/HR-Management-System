/**
 * Apex HR Management System
 * Interactive Client Scripts
 */

document.addEventListener('DOMContentLoaded', function() {
  // Mobile Sidebar Toggle
  const toggleBtn = document.getElementById('toggleSidebar');
  const sidebar = document.getElementById('appSidebar');

  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', function() {
      sidebar.classList.toggle('show');
    });
  }

  // Quick Client Search for Tables
  const searchInput = document.getElementById('tableSearchInput');
  if (searchInput) {
    searchInput.addEventListener('keyup', function() {
      const filter = this.value.toLowerCase();
      const rows = document.querySelectorAll('.custom-table tbody tr');

      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(filter)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });
  }
});

// Confirm deletion dialog
function confirmDelete(url, name) {
  if (confirm(`Are you sure you want to delete employee record for "${name}"?\nThis will remove their salary and attendance records permanently.`)) {
    window.location.href = url;
  }
}

// Print Payslip
function printPayslip(id) {
  const printContent = document.getElementById('payslip-' + id);
  if (!printContent) return;

  const win = window.open('', '', 'height=700,width=850');
  win.document.write('<html><head><title>Payslip</title>');
  win.document.write('<link rel="stylesheet" href="assets/css/style.css">');
  win.document.write('<style>body{background:#fff;padding:40px;font-family:sans-serif;} .no-print{display:none;} @media print{.no-print{display:none;}}</style>');
  win.document.write('</head><body>');
  win.document.write(printContent.innerHTML);
  win.document.write('</body></html>');
  win.document.close();
  win.focus();
  setTimeout(() => {
    win.print();
    win.close();
  }, 500);
}

