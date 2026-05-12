<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div style="padding: 20px;">
    <h2>📊 Analytics Dashboard</h2>
    <p style="color: #666; font-size: 14px; margin-bottom: 20px;">Hybrid Filtering Metrics & Performance Analysis</p>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" class="form-control" name="date_from" value="<?= $dateFrom ?>" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" class="form-control" name="date_to" value="<?= $dateTo ?>" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Method</label>
                    <select class="form-select" name="method">
                        <option value="Overall" <?= $method === 'Overall' ? 'selected' : '' ?>>Overall</option>
                        <option value="CF" <?= $method === 'CF' ? 'selected' : '' ?>>CF (Collaborative)</option>
                        <option value="CBF" <?= $method === 'CBF' ? 'selected' : '' ?>>CBF (Content-Based)</option>
                        <option value="Hybrid" <?= $method === 'Hybrid' ? 'selected' : '' ?>>Hybrid (CF+CBF)</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">🔍 Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Latest Metrics Cards -->
    <?php if ($latestSnapshot): ?>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title">Precision</h6>
                    <h3 class="text-primary"><?= number_format($latestSnapshot['precision'], 2) ?>%</h3>
                    <small class="text-muted">Recommended & Clicked / Total Recs</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title">Recall</h6>
                    <h3 class="text-success"><?= number_format($latestSnapshot['recall'], 2) ?>%</h3>
                    <small class="text-muted">Recommended & Purchased / Total Purchased</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title">RMSE</h6>
                    <h3 class="text-warning"><?= number_format($latestSnapshot['rmse'], 4) ?></h3>
                    <small class="text-muted">Prediction Accuracy</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title">Coverage</h6>
                    <h3 class="text-info"><?= number_format($latestSnapshot['coverage'], 2) ?>%</h3>
                    <small class="text-muted">Products Recommended / Total</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Metrics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title">Click-Through Rate (CTR)</h6>
                    <h3 class="text-primary"><?= number_format($latestSnapshot['ctr'], 2) ?>%</h3>
                    <small class="text-muted">Clicks / Total Recommendations</small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title">Conversion Rate</h6>
                    <h3 class="text-success"><?= number_format($latestSnapshot['conversion_rate'], 2) ?>%</h3>
                    <small class="text-muted">Purchases / Total Recommendations</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Metrics Table -->
    <div class="card">
        <div class="card-header">
            <h5>📈 Metrics Timeline</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Precision</th>
                        <th>Recall</th>
                        <th>RMSE</th>
                        <th>MAE</th>
                        <th>CTR</th>
                        <th>Conversion</th>
                        <th>Coverage</th>
                        <th>Recs</th>
                        <th>Clicks</th>
                        <th>Purchases</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($snapshots)): ?>
                        <?php foreach ($snapshots as $snap): ?>
                        <tr>
                            <td><?= date('M d, Y', strtotime($snap['snapshot_date'])) ?></td>
                            <td><strong><?= number_format($snap['precision'], 2) ?>%</strong></td>
                            <td><?= number_format($snap['recall'], 2) ?>%</td>
                            <td><?= number_format($snap['rmse'], 4) ?></td>
                            <td><?= number_format($snap['mae'], 4) ?></td>
                            <td><?= number_format($snap['ctr'], 2) ?>%</td>
                            <td><?= number_format($snap['conversion_rate'], 2) ?>%</td>
                            <td><?= number_format($snap['coverage'], 2) ?>%</td>
                            <td><?= $snap['total_recommendations'] ?></td>
                            <td><?= $snap['total_clicks'] ?></td>
                            <td><?= $snap['total_purchases'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                                No metrics data available. Click "Calculate Metrics" to generate.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Comparison Chart -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>📊 Method Comparison (CF vs CBF vs Hybrid)</h5>
        </div>
        <div class="card-body">
            <canvas id="comparisonChart" style="max-height: 300px;"></canvas>
        </div>
    </div>

    <!-- Data Export Section -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>📥 Export Data</h5>
        </div>
        <div class="card-body">
            <form id="exportForm" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Export Type</label>
                    <select class="form-select" id="exportType">
                        <option value="logs">Recommendation Logs</option>
                        <option value="interactions">Interactions</option>
                        <option value="metrics">Metrics Snapshots</option>
                        <option value="all">All Data</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Format</label>
                    <select class="form-select" id="exportFormat">
                        <option value="CSV">CSV</option>
                        <option value="JSON">JSON</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="date" class="form-control" id="exportDateFrom" value="<?= $dateFrom ?>" />
                </div>
                <div class="col-md-2">
                    <label class="form-label">To</label>
                    <input type="date" class="form-control" id="exportDateTo" value="<?= $dateTo ?>" />
                </div>
                <div class="col-md-2">
                    <label class="form-label">Method</label>
                    <select class="form-select" id="exportMethod">
                        <option value="">All Methods</option>
                        <option value="CF">CF</option>
                        <option value="CBF">CBF</option>
                        <option value="Hybrid">Hybrid</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-success w-100" onclick="exportData()">
                        📥 Export
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Calculate Modal -->
<div class="modal fade" id="calculateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Calculate Metrics</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="calculateForm">
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" id="calculateDate" value="<?= date('Y-m-d') ?>" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Method (leave empty for all)</label>
                        <select class="form-select" id="calculateMethod">
                            <option value="">All Methods</option>
                            <option value="CF">CF</option>
                            <option value="CBF">CBF</option>
                            <option value="Hybrid">Hybrid</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="calculateMetrics()">Calculate</button>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: none;
    }
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    .table-responsive {
        max-height: 500px;
        overflow-y: auto;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
    function calculateMetrics() {
        const date = document.getElementById('calculateDate').value;
        const method = document.getElementById('calculateMethod').value;

        const formData = new FormData();
        formData.append('date', date);
        if (method) formData.append('method', method);

        fetch('/admin/metrics/calculate', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('✅ Metrics calculated successfully!');
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(err => alert('Error: ' + err));
    }

    function exportData() {
        const type = document.getElementById('exportType').value;
        const format = document.getElementById('exportFormat').value;
        const dateFrom = document.getElementById('exportDateFrom').value;
        const dateTo = document.getElementById('exportDateTo').value;
        const method = document.getElementById('exportMethod').value;

        const formData = new FormData();
        formData.append('export_type', type);
        formData.append('format', format);
        if (dateFrom) formData.append('date_from', dateFrom);
        if (dateTo) formData.append('date_to', dateTo);
        if (method) formData.append('method', method);

        // Show loading state
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '⏳ Exporting...';
        btn.disabled = true;

        fetch('/admin/metrics/export', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            btn.innerHTML = originalText;
            btn.disabled = false;

            if (data.success) {
                // Create download link
                const a = document.createElement('a');
                a.href = data.download_link;
                a.download = data.file_name;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);

                alert(`✅ Exported ${data.row_count} rows (${data.file_size})`);
            } else {
                alert('❌ Export failed: ' + data.message);
            }
        })
        .catch(err => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            alert('Error: ' + err);
        });
    }

    // Load comparison chart
    document.addEventListener('DOMContentLoaded', function() {
        fetch('/admin/metrics/data?date_from=<?= $dateFrom ?>&date_to=<?= $dateTo ?>')
            .then(r => r.json())
            .then(result => {
                if (result.success) {
                    const data = result.data;
                    const dates = [];
                    const cfPrecision = [];
                    const cbfPrecision = [];
                    const hybridPrecision = [];

                    // Extract data
                    const methods = ['CF', 'CBF', 'Hybrid'];
                    const maxSnapshots = Math.max(
                        data.CF.snapshots.length,
                        data.CBF.snapshots.length,
                        data.Hybrid.snapshots.length
                    );

                    for (let i = 0; i < maxSnapshots; i++) {
                        if (data.CF.snapshots[i]) {
                            dates.push(new Date(data.CF.snapshots[i].snapshot_date).toLocaleDateString());
                            cfPrecision.push(data.CF.snapshots[i].precision);
                            cbfPrecision.push(data.CBF.snapshots[i]?.precision || 0);
                            hybridPrecision.push(data.Hybrid.snapshots[i]?.precision || 0);
                        }
                    }

                    const ctx = document.getElementById('comparisonChart')?.getContext('2d');
                    if (ctx) {
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: dates,
                                datasets: [
                                    {
                                        label: 'CF (Collaborative Filtering)',
                                        data: cfPrecision,
                                        borderColor: '#007bff',
                                        backgroundColor: 'rgba(0,123,255,0.1)',
                                        tension: 0.4
                                    },
                                    {
                                        label: 'CBF (Content-Based)',
                                        data: cbfPrecision,
                                        borderColor: '#28a745',
                                        backgroundColor: 'rgba(40,167,69,0.1)',
                                        tension: 0.4
                                    },
                                    {
                                        label: 'Hybrid (CF+CBF)',
                                        data: hybridPrecision,
                                        borderColor: '#dc3545',
                                        backgroundColor: 'rgba(220,53,69,0.1)',
                                        borderWidth: 3,
                                        tension: 0.4
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: {
                                        position: 'bottom'
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        max: 100,
                                        title: { display: true, text: 'Precision (%)' }
                                    }
                                }
                            }
                        });
                    }
                }
            })
            .catch(err => console.error('Chart error:', err));
    });
</script>

<?= $this->endSection() ?>
