<script>
  //test
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px 25px;
        color: white;
    }

    .filter-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .filter-body {
        padding: 25px;
    }

    .filter-body label {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .filter-body .form-control {
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 10px 15px;
        font-size: 14px;
        transition: all 0.3s;
    }

    .filter-body .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .filter-actions {
        display: flex;
        align-items: flex-end;
        gap: 10px;
    }

    .btn {
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 14px;
    }

    .btn-generate {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-generate:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-print {
        background: #f8f9fa;
        color: #333;
        border: 2px solid #e0e0e0;
    }

    .btn-print:hover {
        background: #e9ecef;
        border-color: #ccc;
    }

    /* Report Card */
    .report-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .report-content {
        padding: 40px;
    }

    /* Report Header */
    .report-header {
        text-align: center;
        margin-bottom: 40px;
        padding-bottom: 30px;
        border-bottom: 3px solid #f0f0f0;
    }

    .company-logo {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        color: white;
        font-size: 36px;
    }

    .report-header h2 {
        margin: 0 0 15px;
        font-size: 32px;
        font-weight: 700;
        color: #1a1a1a;
        letter-spacing: 1px;
    }

    .period-text {
        font-size: 15px;
        color: #666;
        margin: 0;
    }

    .date-highlight {
        font-weight: 700;
        color: #667eea;
    }

    /* P&L Grid */
    .pl-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }

    .pl-section {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        display: flex;
        flex-direction: column;
    }

    .section-header {
        padding: 18px 20px;
        font-weight: 700;
        font-size: 16px;
        text-align: center;
        color: white;
    }

    .income-section .section-header {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    .expense-section .section-header {
        background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
    }

    .section-body {
        background: #fafafa;
        padding: 20px;
        min-height: 200px;
        flex: 1;
    }

    .pl-group {
        margin-bottom: 15px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transition: all 0.3s;
    }

    .pl-group:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .pl-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        background: white;
        margin-bottom: 0;
        border-radius: 0;
        box-shadow: none;
        transition: all 0.3s;
    }

    .pl-item.pl-head {
        font-weight: 700;
        background: #ffffff;
        border-left: 4px solid #667eea;
    }

    .income-section .pl-item.pl-head {
        border-left-color: #11998e;
    }

    .expense-section .pl-item.pl-head {
        border-left-color: #eb3349;
    }

    .pl-sub-items {
        background: #fafafa;
        border-top: 1px solid #f0f0f0;
    }

    .pl-item.pl-sub-item {
        padding-left: 35px;
        font-size: 13px;
        color: #555;
        background: transparent;
        border-bottom: 1px solid #f3f3f3;
    }

    .pl-item.pl-sub-item:last-child {
        border-bottom: none;
    }

    .item-name {
        font-size: 14px;
        color: #333;
        font-weight: 500;
    }

    .pl-item.pl-head .item-name {
        font-weight: 700;
        color: #1a1a1a;
    }

    .item-amount {
        font-size: 15px;
        font-weight: 700;
        color: #1a1a1a;
    }

    .no-data {
        text-align: center;
        color: #999;
        padding: 40px;
        font-style: italic;
    }

    .section-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 20px;
        background: #fff;
        border-top: 3px solid #e0e0e0;
        font-weight: 700;
        font-size: 16px;
    }

    .income-section .section-total {
        color: #11998e;
    }

    .expense-section .section-total {
        color: #eb3349;
    }

    .total-amount {
        font-size: 18px;
    }

    /* Net Result */
    .net-result {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 25px;
        padding: 30px;
        border-radius: 12px;
        margin-top: 30px;
    }

    .net-result.profit {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }

    .net-result.loss {
        background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        color: white;
    }

    .result-icon {
        font-size: 48px;
    }

    .result-text h3 {
        margin: 0 0 8px;
        font-size: 20px;
        font-weight: 600;
        letter-spacing: 2px;
    }

    .result-amount {
        margin: 0;
        font-size: 36px;
        font-weight: 700;
    }

    /* Print Styles */
    @media print {
        .no-print {
            display: none !important;
        }

        body {
            font-size: 12px;
        }

        .report-card {
            box-shadow: none;
        }

        .report-content {
            padding: 20px;
        }

        .pl-item:hover {
            transform: none;
        }

        .net-result {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .pl-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .filter-actions {
            margin-top: 15px;
            flex-direction: column;
            align-items: stretch;
        }

        .btn {
            width: 100%;
        }

        .report-content {
            padding: 20px;
        }

        .result-text h3 {
            font-size: 18px;
        }

        .result-amount {
            font-size: 28px;
        }
    }
</style>