// script.js - centrale JS voor zoek- en sorteertaken in accountoverzicht

function searchTable() {
  let input = document.getElementById("searchInput").value.toLowerCase();
  let errorDiv = document.getElementById("clientError");
  if (errorDiv) {
    errorDiv.style.display = 'none';
    errorDiv.innerText = '';
    if (input.length > 0 && input.length < 2) {
      errorDiv.innerText = 'Voer minimaal 2 tekens in om te zoeken.';
      errorDiv.style.display = 'block';
      return;
    }
  }
  let rows = document.querySelectorAll("tbody tr");
  rows.forEach(row => {
    row.style.display = row.innerText.toLowerCase().includes(input) ? "" : "none";
  });
}

function sortTable(n) {
  let table = document.querySelector("table tbody");
  let rows = Array.from(table.rows);
  let ascending = table.dataset.sortOrder !== "asc";
  rows.sort((rowA, rowB) => {
    let cellA = rowA.cells[n].innerText.trim().toLowerCase();
    let cellB = rowB.cells[n].innerText.trim().toLowerCase();
    return ascending ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
  });
  rows.forEach(row => table.appendChild(row));
  table.dataset.sortOrder = ascending ? "asc" : "desc";
}

document.getElementById("menu-toggle")?.addEventListener("click", function() {
  document.getElementById("menu")?.classList.toggle("hidden");
});
