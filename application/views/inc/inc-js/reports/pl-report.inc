<script>
$(document).ready(function() {
    $('.pl-tab-btn').on('click', function(e) {
        e.preventDefault();
        var targetId = $(this).data('target');
        
        $('.pl-tab-btn').removeClass('active');
        $(this).addClass('active');

        $('.pl-view-panel').removeClass('active').hide();
        $('#' + targetId).addClass('active').fadeIn(200);
    });
});
</script>

<style>
    /* Filter Card */
    .filter-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 25px;
        overflow: hidden;
    }

    .filter-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        padding: 16px 22px;
        color: white;
    }

    .filter-header h3 {
        margin: 0;
        font-size: 17px;
        font-weight: 600;
    }

    .filter-body {
        padding: 20px 25px;
    }

    .filter-body label {
        font-weight: 600;
        color: #333;
        margin-bottom: 6px;
        font-size: 13px;
    }

    .filter-body .form-control {
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 14px;
        transition: all 0.3s;
    }

    .filter-body .form-control:focus {
        border-color: #2a5298;
        box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.12);
    }

    .filter-actions {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        padding-top: 24px;
    }

    .btn {
        padding: 9px 20px;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.25s;
        font-size: 13px;
    }

    .btn-generate {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
    }

    .btn-generate:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(42, 82, 152, 0.35);
        color: #fff;
    }

    .btn-print {
        background: #f8f9fa;
        color: #333;
        border: 1px solid #ced4da;
    }

    .btn-print:hover {
        background: #e9ecef;
        border-color: #adb5bd;
    }

    /* Executive KPI Cards */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 25px;
    }

    .kpi-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 18px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
        border: 1px solid #eef2f6;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.09);
    }

    .kpi-profit {
        border-left: 5px solid #10b981;
    }

    .kpi-profit .kpi-icon {
        background: #ecfdf5;
        color: #10b981;
    }

    .kpi-profit .kpi-value {
        color: #065f46;
    }

    .kpi-loss {
        border-left: 5px solid #ef4444;
    }

    .kpi-loss .kpi-icon {
        background: #fef2f2;
        color: #ef4444;
    }

    .kpi-loss .kpi-value {
        color: #991b1b;
    }

    .kpi-tax {
        border-left: 5px solid #6366f1;
    }

    .kpi-tax .kpi-icon {
        background: #eef2ff;
        color: #6366f1;
    }

    .kpi-tax .kpi-value {
        color: #3730a3;
    }

    .kpi-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .kpi-content {
        flex: 1;
        min-width: 0;
    }

    .kpi-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 4px;
    }

    .kpi-title {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: #64748b;
        text-transform: uppercase;
    }

    .kpi-tag {
        font-size: 10px;
        padding: 2px 7px;
        border-radius: 10px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .kpi-tag-wo {
        background: #e0f2fe;
        color: #0369a1;
    }

    .kpi-tag-with {
        background: #d1fae5;
        color: #047857;
    }

    .kpi-tag-tax {
        background: #e0e7ff;
        color: #4338ca;
    }

    .kpi-value {
        margin: 0 0 4px 0;
        font-size: 24px;
        font-weight: 700;
        line-height: 1.2;
    }

    .kpi-value small {
        font-size: 13px;
        font-weight: 500;
        color: #64748b;
    }

    .kpi-subtext {
        margin: 0;
        font-size: 11px;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Report Card */
    .report-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    /* Report Toolbar / View Switcher */
    .report-toolbar {
        padding: 16px 25px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
    }

    .report-toolbar-left {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .toolbar-label {
        font-size: 13px;
        font-weight: 600;
        color: #475569;
    }

    .pl-view-switcher {
        display: inline-flex;
        background: #e2e8f0;
        border-radius: 8px;
        padding: 3px;
        gap: 3px;
    }

    .pl-tab-btn {
        background: transparent;
        border: none;
        padding: 7px 15px;
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        border-radius: 6px;
        transition: all 0.2s;
    }

    .pl-tab-btn:hover {
        color: #1e293b;
    }

    .pl-tab-btn.active {
        background: #ffffff;
        color: #1e3c72;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-print-quick {
        background: #ffffff;
        color: #334155;
        border: 1px solid #cbd5e1;
        padding: 7px 16px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-print-quick:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }

    /* Report Header */
    .report-header {
        text-align: center;
        padding: 30px 20px 20px 20px;
    }

    .company-logo {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        color: white;
        font-size: 28px;
    }

    .report-header h2 {
        margin: 0 0 8px;
        font-size: 26px;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: 0.5px;
    }

    .period-text {
        font-size: 14px;
        color: #64748b;
        margin: 0;
    }

    .date-highlight {
        font-weight: 700;
        color: #1e3c72;
    }

    /* Panels */
    .pl-view-panel {
        display: none;
        padding: 0 25px 30px 25px;
    }

    .pl-view-panel.active {
        display: block;
    }

    /* SPLIT VIEW (Side-by-Side) */
    .split-comparison-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-top: 10px;
    }

    .split-column {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .split-column-wo-tax {
        border-top: 4px solid #0284c7;
    }

    .split-column-with-tax {
        border-top: 4px solid #059669;
    }

    .split-column-header {
        padding: 14px 18px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .split-column-title {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        letter-spacing: 0.5px;
    }

    .split-pill {
        font-size: 11px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 12px;
    }

    .split-pill-wo {
        background: #e0f2fe;
        color: #0369a1;
    }

    .split-pill-with {
        background: #d1fae5;
        color: #047857;
    }

    .pl-grid-split {
        display: flex;
        flex-direction: column;
        gap: 15px;
        padding: 15px;
        flex: 1;
    }

    /* Standard P&L Grid */
    .pl-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-top: 15px;
    }

    .pl-section {
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        background: #ffffff;
    }

    .section-header {
        padding: 12px 16px;
        font-weight: 700;
        font-size: 13px;
        letter-spacing: 0.5px;
        color: white;
    }

    .income-section .section-header {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    }

    .expense-section .section-header {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    }

    .section-body {
        background: #f8fafc;
        padding: 12px;
        min-height: 160px;
        flex: 1;
    }

    .pl-group {
        margin-bottom: 10px;
        background: white;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .pl-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 9px 12px;
        background: white;
        transition: background-color 0.2s;
    }

    .pl-item.pl-head {
        font-weight: 700;
        background: #ffffff;
        border-left: 3px solid #2a5298;
    }

    .income-section .pl-item.pl-head {
        border-left-color: #10b981;
    }

    .expense-section .pl-item.pl-head {
        border-left-color: #ef4444;
    }

    .pl-sub-items {
        background: #f8fafc;
        border-top: 1px solid #edf2f7;
    }

    .pl-item.pl-sub-item {
        padding-left: 24px;
        font-size: 12px;
        color: #475569;
        background: transparent;
        border-bottom: 1px solid #f1f5f9;
    }

    .pl-item.pl-sub-item:last-child {
        border-bottom: none;
    }

    .item-name {
        font-size: 13px;
        color: #334155;
        font-weight: 500;
    }

    .pl-item.pl-head .item-name {
        font-weight: 700;
        color: #0f172a;
    }

    .item-amount {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
    }

    .pl-empty-text {
        text-align: center;
        color: #94a3b8;
        font-size: 12px;
        padding: 20px;
        font-style: italic;
    }

    .section-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        background: #ffffff;
        border-top: 2px solid #e2e8f0;
        font-weight: 700;
        font-size: 14px;
    }

    .income-section .section-total {
        color: #059669;
    }

    .expense-section .section-total {
        color: #dc2626;
    }

    .total-amount {
        font-size: 15px;
    }

    /* Net Result Cards */
    .net-result {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 18px;
        padding: 16px 20px;
        border-radius: 8px;
        margin: 15px;
    }

    .net-result.profit {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        color: white;
    }

    .net-result.loss {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        color: white;
    }

    .net-result .result-icon {
        font-size: 32px;
    }

    .net-result .result-text h3 {
        margin: 0 0 2px;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .net-result .result-amount {
        margin: 0;
        font-size: 22px;
        font-weight: 700;
    }

    .net-result .result-amount .currency {
        font-size: 13px;
        font-weight: 500;
        opacity: 0.9;
    }

    /* Single view banner */
    .single-view-banner {
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 13px;
        margin-bottom: 10px;
    }

    .wo-tax-banner {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        color: #0369a1;
    }

    .with-tax-banner {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #15803d;
    }

    /* Comparative Table Styling */
    .pl-comparison-table {
        margin-top: 15px;
        font-size: 12px;
    }

    .pl-comparison-table thead tr.table-main-header th {
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 1px;
        color: white;
        padding: 10px;
    }

    .expense-col-header {
        background: #dc2626 !important;
    }

    .income-col-header {
        background: #059669 !important;
    }

    .pl-comparison-table thead tr.table-sub-header th {
        background: #f1f5f9;
        color: #334155;
        font-weight: 600;
        padding: 8px;
        font-size: 11px;
    }

    .pl-comparison-table tbody td {
        padding: 7px 10px;
        vertical-align: middle;
    }

    .cell-head {
        font-weight: 700;
        color: #0f172a;
        background-color: #fafafa;
    }

    .cell-sub {
        padding-left: 22px !important;
        color: #64748b;
        font-style: italic;
    }

    .table-total-row td {
        background: #f8fafc;
        border-top: 2px solid #334155 !important;
        font-size: 13px;
        padding: 10px !important;
    }

    .table-net-row td {
        padding: 14px !important;
        background: #f1f5f9;
        font-size: 14px;
        border-top: 2px solid #cbd5e1 !important;
    }

    /* Print View */
    .print-view {
        display: none;
    }

    @media print {
        .no-print,
        .screen-view {
            display: none !important;
        }

        .print-view {
            display: block !important;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #000;
            background: white !important;
        }

        .report-card {
            box-shadow: none;
            border: none;
            padding: 0;
            margin: 0;
        }

        .report-content {
            padding: 0 !important;
        }

        .print-table {
            page-break-inside: avoid;
            width: 100%;
        }

        @page {
            margin: 0.8cm;
        }
    }

    /* Responsive */
    @media (max-width: 992px) {
        .kpi-grid {
            grid-template-columns: 1fr;
        }

        .split-comparison-container {
            grid-template-columns: 1fr;
        }

        .pl-grid {
            grid-template-columns: 1fr;
        }
    }
</style>