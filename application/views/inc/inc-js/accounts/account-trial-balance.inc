<script>
$(document).ready(function() {

    // 1. Initial State Setup from Server Data
    var initialColMode = $('#col_mode').val() || '6col';
    var initialViewMode = $('#view_mode').val() || 'detailed';
    var initialHideZero = $('#chkHideZero').is(':checked');

    applyColMode(initialColMode);
    applyViewMode(initialViewMode);
    applyHideZero(initialHideZero);

    // 2. Quick Date Presets Click Handler
    $('.btn-preset').on('click', function(e) {
        e.preventDefault();
        var preset = $(this).data('preset');
        var now = new Date();
        var fromDate = '', toDate = '';

        function fmtDate(d) {
            var month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();
            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;
            return [year, month, day].join('-');
        }

        if (preset === 'month') {
            var firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
            fromDate = fmtDate(firstDay);
            toDate = fmtDate(now);
        } else if (preset === 'quarter') {
            var currentQuarter = Math.floor(now.getMonth() / 3);
            var qStartMonth = currentQuarter * 3;
            var qStart = new Date(now.getFullYear(), qStartMonth, 1);
            fromDate = fmtDate(qStart);
            toDate = fmtDate(now);
        } else if (preset === 'current_fy') {
            fromDate = now.getFullYear() + '-01-01';
            toDate = now.getFullYear() + '-12-31';
        } else if (preset === 'prev_fy') {
            var prevYear = now.getFullYear() - 1;
            fromDate = prevYear + '-01-01';
            toDate = prevYear + '-12-31';
        }

        if (fromDate && toDate) {
            $('#srch_from_date').val(fromDate);
            $('#srch_to_date').val(toDate);
            $('#export_excel').val('0');
            $('#frmsearch').submit();
        }
    });

    // 3. Column Mode Switcher (6-Column vs 2-Column)
    $('#btnCol6').on('click', function() {
        applyColMode('6col');
        $('#col_mode').val('6col');
    });

    $('#btnCol2').on('click', function() {
        applyColMode('2col');
        $('#col_mode').val('2col');
    });

    function applyColMode(mode) {
        var $tbl = $('#trialBalanceTable');
        if (mode === '2col') {
            $tbl.removeClass('mode-6col').addClass('mode-2col');
            $('#btnCol2').removeClass('btn-default').addClass('btn-primary');
            $('#btnCol6').removeClass('btn-primary').addClass('btn-default');
            // Hide Op and Pr columns
            $tbl.find('.col-op, .col-pr').hide();
        } else {
            $tbl.removeClass('mode-2col').addClass('mode-6col');
            $('#btnCol6').removeClass('btn-default').addClass('btn-primary');
            $('#btnCol2').removeClass('btn-primary').addClass('btn-default');
            // Show Op and Pr columns
            $tbl.find('.col-op, .col-pr').show();
        }
    }

    // 4. View Mode Switcher (Detailed vs Summary)
    $('#btnViewDetailed').on('click', function() {
        applyViewMode('detailed');
        $('#view_mode').val('detailed');
    });

    $('#btnViewSummary').on('click', function() {
        applyViewMode('summary');
        $('#view_mode').val('summary');
    });

    function applyViewMode(mode) {
        var $tbl = $('#trialBalanceTable');
        if (mode === 'summary') {
            $tbl.addClass('mode-summary');
            $('#btnViewSummary').removeClass('btn-default').addClass('btn-primary');
            $('#btnViewDetailed').removeClass('btn-primary').addClass('btn-default');
            // In summary mode, hide all ledger rows and subtotal rows
            $tbl.find('.ledger-row, .head-subtotal-row').hide();
            // Show group summary totals
            $tbl.find('.group-summary-cell').show();
        } else {
            $tbl.removeClass('mode-summary');
            $('#btnViewDetailed').removeClass('btn-default').addClass('btn-primary');
            $('#btnViewSummary').removeClass('btn-primary').addClass('btn-default');
            // In detailed mode, restore ledger rows
            if ($('#chkHideZero').is(':checked')) {
                $tbl.find('.ledger-row:not(.zero-bal-row)').show();
            } else {
                $tbl.find('.ledger-row').show();
            }
            $tbl.find('.head-subtotal-row').show();
            // Keep group header icons facing down
            $tbl.find('.group-toggle-icon').removeClass('fa-angle-right').addClass('fa-angle-down');
        }
    }

    // 5. Hide Zero Balances Toggle
    $('#chkHideZero').on('change', function() {
        applyHideZero($(this).is(':checked'));
    });

    function applyHideZero(hide) {
        var $tbl = $('#trialBalanceTable');
        if (hide) {
            $tbl.addClass('hide-zero-active');
            $tbl.find('.zero-bal-row').hide();
        } else {
            $tbl.removeClass('hide-zero-active');
            if ($('#view_mode').val() !== 'summary') {
                $tbl.find('.zero-bal-row').show();
            }
        }
    }

    // 6. Group Header Accordion Collapse / Expand
    $(document).on('click', '.group-header-row', function(e) {
        // Do not toggle if in summary mode
        if ($('#view_mode').val() === 'summary') {
            return;
        }

        var slug = $(this).data('target-group');
        var $items = $('.group-item-' + slug);
        var $subtotal = $('.group-subtotal-' + slug);
        var $icon = $(this).find('.group-toggle-icon');

        if ($items.is(':visible')) {
            $items.hide();
            $subtotal.hide();
            $icon.removeClass('fa-angle-down').addClass('fa-angle-right');
        } else {
            if ($('#chkHideZero').is(':checked')) {
                $items.filter(':not(.zero-bal-row)').show();
            } else {
                $items.show();
            }
            $subtotal.show();
            $icon.removeClass('fa-angle-right').addClass('fa-angle-down');
        }
    });

});

// 7. Server-side Excel Export Trigger
function triggerServerExcel() {
    $('#export_excel').val('1');
    $('#frmsearch').submit();
    setTimeout(function() {
        $('#export_excel').val('0');
    }, 1000);
}

// 8. Client-side Instant Excel Export
function exportToExcel() {
    var table = document.getElementById('trialBalanceTable');
    if (!table) return;

    // Clone table so modifications don't alter the visible DOM
    var clone = table.cloneNode(true);

    // Clean up icons, links, and styling for Excel
    var links = clone.getElementsByTagName('a');
    while (links.length > 0) {
        var parent = links[0].parentNode;
        while (links[0].firstChild) {
            parent.insertBefore(links[0].firstChild, links[0]);
        }
        parent.removeChild(links[0]);
    }

    var icons = clone.getElementsByTagName('i');
    while (icons.length > 0) {
        icons[0].parentNode.removeChild(icons[0]);
    }

    var badges = clone.getElementsByClassName('count-badge');
    while (badges.length > 0) {
        badges[0].parentNode.removeChild(badges[0]);
    }

    // Show all rows and columns in the export
    var hiddenCols = clone.querySelectorAll('.col-op, .col-pr');
    for (var i = 0; i < hiddenCols.length; i++) {
        hiddenCols[i].style.display = '';
    }
    var hiddenRows = clone.querySelectorAll('.ledger-row, .head-subtotal-row');
    for (var j = 0; j < hiddenRows.length; j++) {
        hiddenRows[j].style.display = '';
    }

    var html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">' +
        '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' +
        '<style>' +
        'table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 10pt; }' +
        'th { background-color: #2c3e50; color: #ffffff; font-weight: bold; border: 1px solid #000; text-align: center; }' +
        'td { border: 1px solid #d9d9d9; padding: 4px; }' +
        '.category-header-row td { background-color: #1e2b37; color: #ffffff; font-weight: bold; }' +
        '.group-header-row td { background-color: #ebf3f9; color: #1f3a52; font-weight: bold; }' +
        '.head-subtotal-row td { background-color: #f9fbfd; font-weight: bold; }' +
        '.grand-total-row td { background-color: #0f172a; color: #ffffff; font-weight: bold; }' +
        '.text-right { text-align: right; }' +
        '</style></head><body>' +
        '<h3>TRIAL BALANCE (ACCOUNTS BOOK)</h3>' +
        '<p>Period: ' + document.getElementById('srch_from_date').value + ' to ' + document.getElementById('srch_to_date').value + '</p>' +
        clone.outerHTML +
        '</body></html>';

    var blob = new Blob([html], { type: 'application/vnd.ms-excel;charset=utf-8' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'Trial_Balance_' + (document.getElementById('srch_from_date').value || '') + '_to_' + (document.getElementById('srch_to_date').value || '') + '.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
