document.addEventListener('DOMContentLoaded', () => {
    const exportBtn = document.getElementById('startExport');
    const statusEl = document.getElementById('status');
    const pagesDoneEl = document.getElementById('pagesDone');
    const recordsFetchedEl = document.getElementById('recordsFetched');
    const totalRecordsEl = document.getElementById('totalRecords');
    const totalPagesEl = document.getElementById('totalPages');

    const percentBox = document.createElement('p');
    percentBox.innerHTML = `<strong>Progress:</strong> <span id="percentDone">0%</span>`;
    exportBtn.insertAdjacentElement('afterend', percentBox);
    const percentDoneEl = document.getElementById('percentDone');

    let isExportRunning = false;
    let fetchedRecords = 0;
    let totalRecords = 0;
    let totalPages = 0;
    let currentPage = 1;
    const perPage = 50;
    const csvPath = 'issues-' + new Date().toISOString().replace(/[:.]/g, '-') + '.csv';

    async function fetchPage(page) {
        statusEl.textContent = `Fetching page ${page}...`;
        const params = new URLSearchParams({
            action: 'issues_exporter_for_gitlab_export',
            page,
            per_page: perPage,
            csv_path_file: csvPath,
            security: ISSUES_EXPORTER_FOR_GITLAB_JS.nonce
        });

        try {
            const response = await fetch(`${ISSUES_EXPORTER_FOR_GITLAB_JS.ajaxUrl}?${params.toString()}`);
            const data = await response.json();
            console.log(data);
            if (data.success) {
                fetchedRecords += data.data.recordsFetched;
                totalRecords = data.data.totalRecords;
                totalPages = data.data.totalPages;

                const percentage = Math.min(Math.round((fetchedRecords / Math.min(totalRecords, totalRecords)) * 100), 100);

                pagesDoneEl.textContent = page;
                recordsFetchedEl.textContent = fetchedRecords;
                totalRecordsEl.textContent = totalRecords;
                totalPagesEl.textContent = totalPages;
                percentDoneEl.textContent = percentage + '%';

                if (data.data.hasMore && page < totalPages) {
                    currentPage++;
                    await fetchPage(currentPage + 1);
                } else {
                    exportBtn.style.display = 'none';
                    statusEl.textContent = '✅ All data successfully fetched.';
                    exportBtn.textContent = 'Start Export';
                    exportBtn.disabled = false;
                    isExportRunning = false;
                    alert('✅ All data successfully fetched. Page will refresh in 10 seconds...');
                    setTimeout(() => location.reload(), 10000);
                }

            } else {
                //throw new Error(data.message);
                alert('❌ Error: '+ data.data.message);
                statusEl.textContent = '❌ Error: ' + data.data.message;
                exportBtn.textContent = 'Start Export';
                exportBtn.disabled = false;
                isExportRunning = false;
            }
        } catch (err) {
            statusEl.textContent = '❌ Error: ' + err.message;
            exportBtn.textContent = 'Start Export';
            exportBtn.disabled = false;
            isExportRunning = false;
        }
    }

    if (exportBtn) {
        exportBtn.addEventListener('click', () => {
            if (isExportRunning) {
                alert('Export already in progress...');
                return;
            }

            currentPage = 1;
            fetchedRecords = 0;
            totalRecords = 0;
            totalPages = 0;

            isExportRunning = true;
            exportBtn.disabled = true;
            exportBtn.textContent = 'Exporting...';
            statusEl.textContent = '⏳ Starting...';

            pagesDoneEl.textContent = '0';
            recordsFetchedEl.textContent = '0';
            totalRecordsEl.textContent = '0';
            totalPagesEl.textContent = '0';
            percentDoneEl.textContent = '0%';

            fetchPage(currentPage);
        });
    }

    document.querySelectorAll('.issues-exporter-for-gitlab-delete-csv').forEach((btn) => {
        btn.addEventListener('click', () => {
            const filename = btn.getAttribute('data-filename');
            if (!confirm('Are you sure you want to delete this file?')) return;

            const formData = new FormData();
            formData.append('action', 'issues_exporter_for_gitlab_delete_csv_file');
            formData.append('file_name', filename);
            formData.append('security', ISSUES_EXPORTER_FOR_GITLAB_JS.nonce);

            fetch(ISSUES_EXPORTER_FOR_GITLAB_JS.ajaxUrl, {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        btn.closest('tr').remove();
                        alert('✅ File deleted successfully.');
                    } else {
                        alert('❌ ' + data.message);
                    }
                })
                .catch(() => alert('❌ Error deleting file.'));
        });
    });
});
