(function () {
  function addSearch(container) {
    if (container.closest('.table-search-wrapper')) return;

    if (container.tagName === 'TABLE') {
      const headerRow = container.tHead && container.tHead.rows[0];
      if (headerRow) {
        const tableWidth = container.getBoundingClientRect().width;
        const headerCells = Array.from(headerRow.cells);
        container.style.tableLayout = 'fixed';
        container.style.width = '100%';
        headerCells.forEach(function (cell) {
          cell.style.width = ((cell.getBoundingClientRect().width / tableWidth) * 100) + '%';
        });
      }
    }

    const wrapper = document.createElement('div');
    wrapper.className = 'table-search-wrapper';

    const input = document.createElement('input');
    input.type = 'search';
    input.className = 'form-control table-search-input';
    input.placeholder = 'Cari data...';
    input.setAttribute('aria-label', 'Cari data pada tabel');

    const result = document.createElement('small');
    result.className = 'table-search-result text-muted';

    wrapper.appendChild(input);
    wrapper.appendChild(result);
    container.parentNode.insertBefore(wrapper, container);

    const rows = container.tagName === 'TABLE'
      ? Array.from(container.tBodies[0].rows)
      : Array.from(container.children);
    const emptyRow = rows.find(function (row) {
      return (row.cells && row.cells.length === 1 && row.cells[0].hasAttribute('colspan'))
        || row.textContent.trim() === 'Belum ada pertanyaan.';
    });
    const dataRows = rows.filter(function (row) {
      return row !== emptyRow && row.matches('tr, li');
    });

    function filterRows() {
      const query = input.value.trim().toLowerCase();
      let visible = 0;

      dataRows.forEach(function (row) {
        const matches = !query || row.textContent.toLowerCase().includes(query);
        row.hidden = !matches;
        if (matches) visible++;
      });

      if (emptyRow) {
        emptyRow.hidden = dataRows.length > 0;
      }

      if (query && visible === 0) {
        result.textContent = 'Tidak ada data yang cocok.';
      } else if (query) {
        result.textContent = visible + ' data ditemukan.';
      } else {
        result.textContent = '';
      }
    }

    input.addEventListener('input', filterRows);
    filterRows();
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('table, ol[data-table-search]').forEach(addSearch);
  });
})();
