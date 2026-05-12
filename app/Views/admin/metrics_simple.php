<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<h2>📊 Analytics Dashboard</h2>

<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px;">
    <div class="card-box" style="text-align: center;">
        <h3><?= $latestSnapshot ? number_format($latestSnapshot['precision'], 2) . '%' : '—' ?></h3>
        <p>Precision</p>
    </div>
    <div class="card-box" style="text-align: center;">
        <h3><?= $latestSnapshot ? number_format($latestSnapshot['recall'], 2) . '%' : '—' ?></h3>
        <p>Recall</p>
    </div>
    <div class="card-box" style="text-align: center;">
        <h3><?= $latestSnapshot ? number_format($latestSnapshot['rmse'], 4) : '—' ?></h3>
        <p>RMSE</p>
    </div>
    <div class="card-box" style="text-align: center;">
        <h3><?= $latestSnapshot ? number_format($latestSnapshot['coverage'], 2) . '%' : '—' ?></h3>
        <p>Coverage</p>
    </div>
</div>

<!-- Filter Section -->
<div style="background: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
    <form method="get" style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 10px; align-items: flex-end;">
        <div>
            <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 5px;">From Date</label>
            <input type="date" name="date_from" value="<?= $dateFrom ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" />
        </div>
        <div>
            <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 5px;">To Date</label>
            <input type="date" name="date_to" value="<?= $dateTo ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" />
        </div>
        <div>
            <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 5px;">Method</label>
            <select name="method" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="Overall" <?= $method === 'Overall' ? 'selected' : '' ?>>Overall</option>
                <option value="CF" <?= $method === 'CF' ? 'selected' : '' ?>>CF</option>
                <option value="CBF" <?= $method === 'CBF' ? 'selected' : '' ?>>CBF</option>
                <option value="Hybrid" <?= $method === 'Hybrid' ? 'selected' : '' ?>>Hybrid</option>
            </select>
        </div>
        <button type="submit" style="background: #4CAF50; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">Filter</button>
    </form>
</div>

<!-- Buttons -->
<div style="margin-bottom: 20px; display: flex; gap: 10px;">
    <button onclick="calculateMetrics()" style="background: #2196F3; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">🔄 Calculate</button>
    <button onclick="exportData()" style="background: #FF9800; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">📥 Export</button>
</div>

<!-- Metrics Table -->
<div style="background: white; padding: 15px; border-radius: 8px; overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
        <thead>
            <tr style="background: #f5f5f5; border-bottom: 2px solid #ddd;">
                <th style="padding: 10px; text-align: left;">Date</th>
                <th style="padding: 10px; text-align: center;">Precision %</th>
                <th style="padding: 10px; text-align: center;">Recall %</th>
                <th style="padding: 10px; text-align: center;">RMSE</th>
                <th style="padding: 10px; text-align: center;">CTR %</th>
                <th style="padding: 10px; text-align: center;">Conversion %</th>
                <th style="padding: 10px; text-align: center;">Coverage %</th>
                <th style="padding: 10px; text-align: center;">Recs</th>
                <th style="padding: 10px; text-align: center;">Clicks</th>
                <th style="padding: 10px; text-align: center;">Purchases</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($snapshots)): ?>
                <?php foreach ($snapshots as $snap): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px;"><?= date('M d, Y', strtotime($snap['snapshot_date'])) ?></td>
                        <td style="padding: 10px; text-align: center; font-weight: 600;"><?= number_format($snap['precision'], 2) ?></td>
                        <td style="padding: 10px; text-align: center;"><?= number_format($snap['recall'], 2) ?></td>
                        <td style="padding: 10px; text-align: center;"><?= number_format($snap['rmse'], 4) ?></td>
                        <td style="padding: 10px; text-align: center;"><?= number_format($snap['ctr'], 2) ?></td>
                        <td style="padding: 10px; text-align: center;"><?= number_format($snap['conversion_rate'], 2) ?></td>
                        <td style="padding: 10px; text-align: center;"><?= number_format($snap['coverage'], 2) ?></td>
                        <td style="padding: 10px; text-align: center;"><?= $snap['total_recommendations'] ?></td>
                        <td style="padding: 10px; text-align: center;"><?= $snap['total_clicks'] ?></td>
                        <td style="padding: 10px; text-align: center;"><?= $snap['total_purchases'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" style="padding: 20px; text-align: center; color: #999;">
                        No data. Click "Calculate" to generate metrics.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    function calculateMetrics() {
        const date = prompt('Enter date (YYYY-MM-DD):', '<?= date('Y-m-d') ?>');
        if (!date) return;

        const formData = new FormData();
        formData.append('date', date);

        fetch('/admin/metrics/calculate', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                alert(data.success ? '✅ Metrics calculated!' : '❌ Error');
                if (data.success) location.reload();
            })
            .catch(err => alert('Error: ' + err));
    }

    function exportData() {
        const type = prompt('Export type? (logs/interactions/metrics/all):', 'logs');
        if (!type) return;

        const format = confirm('CSV (OK) or JSON (Cancel)?') ? 'CSV' : 'JSON';
        const dateFrom = document.querySelector('input[name="date_from"]').value;
        const dateTo = document.querySelector('input[name="date_to"]').value;

        const formData = new FormData();
        formData.append('export_type', type);
        formData.append('format', format);
        if (dateFrom) formData.append('date_from', dateFrom);
        if (dateTo) formData.append('date_to', dateTo);

        fetch('/admin/metrics/export', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const a = document.createElement('a');
                    a.href = data.download_link;
                    a.download = data.file_name;
                    a.click();
                    alert(`✅ Downloaded: ${data.file_name} (${data.file_size})`);
                } else {
                    alert('❌ Export failed: ' + data.message);
                }
            })
            .catch(err => alert('Error: ' + err));
    }
</script>

<?= $this->endSection() ?>