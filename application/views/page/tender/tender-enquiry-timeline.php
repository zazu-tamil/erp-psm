<?php include_once(VIEWPATH . 'inc/header.php'); ?>

<section class="content-header">
    <h1>
        <i class="fa fa-line-chart"></i> <?php echo htmlspecialchars($title); ?>
        <small>Customer & Vendor Journey Tracking</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Timeline Tracker</li>
    </ol>
</section>

<section class="content">

    <!-- Search Section -->
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-search"></i> Search Enquiry
            </h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Search by Enquiry Number</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-search"></i></span>
                            <input type="text" class="form-control srch_enq_id" placeholder="Type to search...">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Details Section -->
    <div class="box box-info timeline-section" id="customer_details_box" style="display:none;">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-user"></i> Customer Information
            </h3>
        </div>
        <div class="box-body">
            <div class="row customer-info-row">
                <div class="col-md-3">
                    <div class="info-box bg-aqua">
                        <span class="info-box-icon"><i class="fa fa-file-text"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">RFQ Number</span>
                            <span class="info-box-number" id="customer_enquiry_no">-</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-green">
                        <span class="info-box-icon"><i class="fa fa-user"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Customer Name</span>
                            <span class="info-box-number" id="customer_name" style="font-size: 16px;">-</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-yellow">
                        <span class="info-box-icon"><i class="fa fa-calendar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Enquiry Date</span>
                            <span class="info-box-number" id="customer_enquiry_date" style="font-size: 16px;">-</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-red">
                        <span class="info-box-icon"><i class="fa fa-flag"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Current Status</span>
                            <span class="info-box-number" id="customer_current_status" style="font-size: 16px;">-</span>
                        </div>
                    </div>
                </div>
            </div>
            <div id="customer_timeline" class="tender-timeline"></div>
        </div>
    </div>

    <!-- Vendor Details Section -->
    <div class="box box-warning timeline-section" id="vendor_details_box" style="display:none;">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-truck"></i> Vendor Information
            </h3>
        </div>
        <div class="box-body">
            <div class="row customer-info-row">
                <div class="col-md-3">
                    <div class="info-box bg-aqua">
                        <span class="info-box-icon"><i class="fa fa-file-text"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">RFQ Number</span>
                            <span class="info-box-number" id="vendor_enquiry_no">-</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-purple">
                        <span class="info-box-icon"><i class="fa fa-truck"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Vendor Name</span>
                            <span class="info-box-number" id="vendor_name" style="font-size: 16px;">-</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-yellow">
                        <span class="info-box-icon"><i class="fa fa-calendar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Enquiry Date</span>
                            <span class="info-box-number" id="vendor_enquiry_date" style="font-size: 16px;">-</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-red">
                        <span class="info-box-icon"><i class="fa fa-flag"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Current Status</span>
                            <span class="info-box-number" id="vendor_current_status" style="font-size: 16px;">-</span>
                        </div>
                    </div>
                </div>
            </div>
            <div id="vendor_timeline" class="tender-timeline"></div>
        </div>
    </div>

</section>

<style>
    /* Customer Info Styles */
    .customer-info-row {
        margin-bottom: 20px;
    }

    .info-box-number {
        font-size: 18px;
        font-weight: bold;
    }

    .info-box.bg-purple {
        background-color: #605ca8 !important;
        color: #fff;
    }

    .info-box.bg-purple .info-box-icon {
        background-color: rgba(0, 0, 0, 0.1);
    }

    /* Timeline Type Buttons */
    .timeline-type-btn {
        transition: all 0.3s ease;
    }

    .timeline-type-btn.active {
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
        transform: translateY(-2px);
    }

    /* Timeline Container */
    .tender-timeline {
        position: relative;
        padding: 40px 0;
        margin: 30px 0;
    }

    .timeline-container {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        position: relative;
    }

    /* Timeline Progress Bar */
    .timeline-progress-bar {
        position: absolute;
        top: 50px;
        left: 10%;
        right: 10%;
        height: 4px;
        background: #e0e0e0;
        z-index: 1;
    }

    .timeline-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #00c0ef, #00a65a);
        transition: width 0.5s ease;
        position: relative;
    }

    .timeline-progress-fill::after {
        content: '';
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 12px;
        height: 12px;
        background: #00a65a;
        border-radius: 50%;
        box-shadow: 0 0 0 4px rgba(0, 166, 90, 0.2);
    }

    /* Timeline Step */
    .tender-step {
        flex: 1;
        text-align: center;
        position: relative;
        z-index: 2;
        transition: transform 0.3s ease;
        cursor: pointer;
    }

    .tender-step:hover {
        transform: translateY(-5px);
    }

    /* Step Circle */
    .step-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        margin: 0 auto 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 4px solid #fff;
    }

    .step-done .step-circle {
        background: linear-gradient(135deg, #00c0ef, #00a65a);
        animation: pulse-success 2s infinite;
    }

    .step-pending .step-circle {
        background: linear-gradient(135deg, #f39c12, #ff851b);
    }

    .step-failed .step-circle {
        background: linear-gradient(135deg, #dd4b39, #d73925);
    }

    .step-circle i {
        font-size: 32px;
        color: #fff;
    }

    /* Status Badge */
    .step-badge {
        position: absolute;
        top: -10px;
        right: -10px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .step-badge i {
        font-size: 14px;
    }

    .step-done .step-badge i {
        color: #00a65a;
    }

    .step-pending .step-badge i {
        color: #f39c12;
    }

    .step-failed .step-badge i {
        color: #dd4b39;
    }

    /* Step Content */
    .step-label {
        font-size: 16px;
        font-weight: 700;
        color: #333;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .step-date {
        font-size: 13px;
        color: #666;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .step-status {
        margin-top: 10px;
    }

    .step-status .label {
        font-size: 12px;
        padding: 6px 12px;
        border-radius: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    /* Additional Details */
    .step-details {
        margin-top: 10px;
        font-size: 12px;
        color: #999;
        min-height: 20px;
    }

    .step-reference {
        font-size: 11px;
        color: #3c8dbc;
        font-weight: 600;
        margin-top: 5px;
    }

    /* Modal Header Styles */
    .modal-header-customer {
        background: linear-gradient(135deg, #00c0ef, #00a65a);
        color: #fff;
    }

    .modal-header-vendor {
        background: linear-gradient(135deg, #605ca8, #9b59b6);
        color: #fff;
    }

    .modal-header-customer .close,
    .modal-header-vendor .close {
        color: #fff;
        opacity: 0.8;
    }

    .modal-header-customer .close:hover,
    .modal-header-vendor .close:hover {
        opacity: 1;
    }

    /* Animations */
    @keyframes pulse-success {

        0%,
        100% {
            box-shadow: 0 4px 15px rgba(0, 166, 90, 0.3);
        }

        50% {
            box-shadow: 0 4px 25px rgba(0, 166, 90, 0.6);
        }
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .timeline-container {
            flex-direction: column;
            align-items: center;
        }

        .tender-step {
            margin-bottom: 40px;
            width: 100%;
        }

        .timeline-progress-bar {
            display: none;
        }
    }

    /* Print Styles */
    @media print {

        .box-tools,
        .content-header,
        .breadcrumb,
        .timeline-type-btn {
            display: none;
        }
    }

    /* Loading Animation */
    .loading-spinner {
        text-align: center;
        padding: 40px;
    }

    .loading-spinner i {
        font-size: 48px;
        color: #3c8dbc;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        font-size: 64px;
        color: #ddd;
        margin-bottom: 20px;
    }

    .empty-state h4 {
        color: #999;
        font-weight: 600;
    }
</style>

<!-- Single Modal for Both Customer and Vendor Details -->
<div class="modal fade" id="view_timeline_details" role="dialog" aria-labelledby="timelineModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" id="timeline_modal_header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="timelineModalLabel">
                    <i class="fa" id="timeline_modal_icon"></i>
                    <strong id="timeline_modal_title"></strong>
                </h3>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="timeline_details_table">
                        <!-- Dynamic content will be loaded here -->
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>

<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
    $(document).ready(function () {

        let currentEnquiryId = null;

        /* ================= ENQUIRY AUTOCOMPLETE ================= */
        $(".srch_enq_id").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url('tender/tender_enquiry_id_search'); ?>",
                    type: "POST",
                    data: { search: request.term },
                    dataType: "json",
                    success: function (data) {
                        response(data);
                    },
                    error: function () {
                        response([]);
                    }
                });
            },
            minLength: 2,
            select: function (event, ui) {
                currentEnquiryId = ui.item.tender_enquiry_id;

                // Load both timelines
                load_tender_enq_customer_timeline(currentEnquiryId);
                load_tender_enq_vendor_timeline(currentEnquiryId);
            }
        });

        /* ================= DATE HELPERS ================= */
        function formatDateDMY(dateStr) {
            if (!dateStr) return '-';
            let d = new Date(dateStr);
            if (isNaN(d.getTime())) return '-';
            return `${String(d.getDate()).padStart(2, '0')}-${String(d.getMonth() + 1).padStart(2, '0')}-${d.getFullYear()}`;
        }

        function formatDateReadable(dateStr) {
            if (!dateStr) return '-';
            let d = new Date(dateStr);
            if (isNaN(d.getTime())) return '-';
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
        }

        /* ================= STATUS LABEL ================= */
        function statusLabel(status) {
            if (!status) return '<span class="label label-default">Unknown</span>';

            let cls = 'label-default';
            let icon = 'fa-circle';

            if (['Won', 'Payment Paid', 'Completed', 'Active', 'Open', 'Quotation Received', 'Confirmed', 'Delivered'].includes(status)) {
                cls = 'label-success';
                icon = 'fa-check-circle';
            } else if (['Pending', 'Processing'].includes(status)) {
                cls = 'label-warning';
                icon = 'fa-clock-o';
            } else if (['Rejected', 'Cancelled', 'Lost'].includes(status)) {
                cls = 'label-danger';
                icon = 'fa-times-circle';
            }

            return `<span class="label ${cls}"><i class="fa ${icon}"></i> ${status}</span>`;
        }

        /* ================= STEP ICON ================= */
        function getStepIcon(label) {
            const icons = {
                'Enquiry': 'fa-file-text-o',
                'Quotation': 'fa-calculator',
                'PO': 'fa-shopping-cart',
                'DC': 'fa-truck',
                'Invoice': 'fa-money',
                'Rate Enquiry': 'fa-question-circle',
                'Vendor Quote': 'fa-file-text',
                'Vendor PO': 'fa-shopping-bag',
                'Inward': 'fa-inbox',
                'Purchase Invoice': 'fa-file'
            };
            return icons[label] || 'fa-circle';
        }

        /* ================= TIMELINE HTML ================= */
        function generateTimelineHTML(steps, timelineType) {
            let completedSteps = steps.filter(s => s.done).length;
            let progressPercent = (completedSteps / steps.length) * 100;

            let html = `
            <div class="timeline-progress-bar">
                <div class="timeline-progress-fill" style="width:${progressPercent}%"></div>
            </div>
            <div class="timeline-container">
        `;

            steps.forEach(step => {
                let stepClass = step.done ? 'step-done' : (step.status ? 'step-pending' : 'step-failed');
                let badgeIcon = step.done ? 'fa-check' : (step.status ? 'fa-clock-o' : 'fa-times');

                // Determine which label to use based on timeline type
                let displayLabel = timelineType === 'customer'
                    ? (step.customer_label || step.label)
                    : (step.vendor_label || step.label);

                html += `
                <div class="tender-step ${stepClass} click_to_get_tender_enq_details"
                     data-tender-enq-id="${step.tender_enq_id || ''}"
                     data-timeline-type="${timelineType}"
                     data-customer-label="${step.customer_label || step.label}" 
                     data-vendor-label="${step.vendor_label || step.label}">

                    <div class="step-circle">
                        <i class="fa ${getStepIcon(step.label)}"></i>
                        <div class="step-badge"><i class="fa ${badgeIcon}"></i></div>
                    </div>

                    <div class="step-label">${displayLabel}</div>
                    <div class="step-date">${formatDateReadable(step.date)}</div>
                    <div class="step-status">${statusLabel(step.status)}</div>

                    ${step.reference ? `<div class="step-reference">#${step.reference}</div>` : ''}
                    ${step.details ? `<div class="step-details">${step.details}</div>` : ''}
                </div>
            `;
            });

            html += '</div>';
            return html;
        }

        /* ================= CUSTOMER TIMELINE ================= */
        function load_tender_enq_customer_timeline(t_enq_id) {
            if (!t_enq_id) return;

            $("#customer_details_box").show();
            $("#customer_timeline").html(
                '<p class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</p>'
            );

            $.post(
                "<?php echo site_url('tender/get_load_tender_enq_customer_timeline'); ?>",
                { t_enq_id: t_enq_id },
                function (res) {
                    if (!res || res.length === 0) {
                        $("#customer_timeline").html(
                            '<p class="text-center text-muted">No customer data</p>'
                        );
                        return;
                    }

                    let r = res[0];

                    /* Update Customer Info */
                    $("#customer_enquiry_no").text(r.enquiry_no || '-');
                    $("#customer_name").text(r.customer_name || '-');
                    $("#customer_enquiry_date").text(formatDateDMY(r.enquiry_date));
                    $("#customer_current_status").html(statusLabel(r.tender_status));

                    let steps = [
                        {
                            label: 'Enquiry',
                            customer_label: 'Customer Enquiry',
                            done: !!r.enquiry_date,
                            tender_enq_id: r.tender_enquiry_id,
                            date: r.enquiry_date,
                            status: r.tender_status,
                            reference: r.enquiry_no
                        },
                        {
                            label: 'Quotation',
                            customer_label: 'Customer Quotation',
                            done: r.quotation_status === 'Won',
                            tender_enq_id: r.tender_enquiry_id,
                            date: r.quote_date,
                            status: r.quotation_status,
                            reference: r.quote_no
                        },
                        {
                            label: 'PO',
                            customer_label: 'Customer PO',
                            done: r.po_status === 'Open',
                            tender_enq_id: r.tender_enquiry_id,
                            date: r.po_date,
                            status: r.po_status,
                            reference: r.po_no
                        },
                        {
                            label: 'DC',
                            customer_label: 'Customer DC',
                            done: r.dc_status === 'Active',
                            tender_enq_id: r.tender_enquiry_id,
                            date: r.dc_date,
                            status: r.dc_status,
                            reference: r.dc_no
                        },
                        {
                            label: 'Invoice',
                            customer_label: 'Customer Invoice',
                            done: r.invoice_status === 'Payment Paid',
                            tender_enq_id: r.tender_enquiry_id,
                            date: r.invoice_date,
                            status: r.invoice_status,
                            reference: r.invoice_no
                        }
                    ];

                    $("#customer_timeline").html(generateTimelineHTML(steps, 'customer'));
                },
                "json"
            );
        }

        /* ================= VENDOR TIMELINE ================= */
        function load_tender_enq_vendor_timeline(t_enq_id) {
            if (!t_enq_id) return;

            $("#vendor_details_box").show();
            $("#vendor_timeline").html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</p>');

            $.post("<?php echo site_url('tender/get_load_tender_enq_vendor_timeline'); ?>",
                { t_enq_id: t_enq_id },
                function (res) {
                    if (!res || res.length === 0) {
                        $("#vendor_timeline").html('<p class="text-center text-muted">No vendor data</p>');
                        return;
                    }

                    let r = res[0];

                    /* Update Vendor Info */
                    $("#vendor_enquiry_no").text(r.enquiry_no || '-');
                    $("#vendor_name").text(r.vendor_name || '-');
                    $("#vendor_enquiry_date").text(formatDateDMY(r.enquiry_date));
                    $("#vendor_current_status").html(statusLabel(r.tender_status));

                    let steps = [
                        {
                            label: 'Enquiry',
                            vendor_label: 'Vendor Enquiry',
                            done: !!r.enquiry_date,
                            date: r.enquiry_date,
                            tender_enq_id: r.tender_enquiry_id,
                            status: r.tender_status,
                            reference: r.enquiry_no
                        },
                        {
                            label: 'Rate Enquiry',
                            vendor_label: 'Vendor Rate Enquiry',
                            done: r.vendor_rate_enquiry_status === 'Quotation Received',
                            date: r.vendor_rate_enquiry_date,
                            tender_enq_id: r.tender_enquiry_id,
                            status: r.vendor_rate_enquiry_status
                        },
                        {
                            label: 'Vendor Quote',
                            vendor_label: 'Vendor Quotation',
                            done: r.quote_status === 'Confirmed',
                            date: r.quote_date,
                            tender_enq_id: r.tender_enquiry_id,
                            status: r.quote_status
                        },
                        {
                            label: 'Vendor PO',
                            vendor_label: 'Vendor Purchase Order',
                            done: r.po_status === 'Delivered',
                            date: r.po_date,
                            tender_enq_id: r.tender_enquiry_id,
                            status: r.po_status
                        },
                        {
                            label: 'Inward',
                            vendor_label: 'Material Inward',
                            done: r.inward_status === 'Active',
                            date: r.inward_date,
                            tender_enq_id: r.tender_enquiry_id,
                            status: r.inward_status
                        },
                        {
                            label: 'Purchase Invoice',
                            vendor_label: 'Vendor Invoice',
                            done: r.invoice_status === 'Active',
                            date: r.invoice_date,
                            tender_enq_id: r.tender_enquiry_id,
                            status: r.invoice_status
                        }
                    ];

                    $("#vendor_timeline").html(generateTimelineHTML(steps, 'vendor'));
                }, "json");
        }


        $(document).on("click", ".click_to_get_tender_enq_details", function () {
            let customer_label = $(this).data("customer-label");
            let vendor_label = $(this).data("vendor-label");
            let tender_enq_id = $(this).data("tender-enq-id");
            let timeline_type = $(this).data("timeline-type");




            let modal_label = timeline_type === 'customer' ? customer_label : vendor_label;
            let modal_icon = timeline_type === 'customer' ? 'fa-user' : 'fa-truck';
            let header_class = timeline_type === 'customer' ? 'modal-header-customer' : 'modal-header-vendor';


            $("#timeline_modal_header").removeClass('modal-header-customer modal-header-vendor').addClass(header_class);
            $("#timeline_modal_icon").removeClass().addClass('fa ' + modal_icon);
            $("#timeline_modal_title").text(modal_label + ' Details');
            $("#timeline_modal_label_hidden").text(modal_label);
            // alert(modal_label);

            // Show loading state
            $("#timeline_details_table").html('<tr><td class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>');

            $.ajax({
                url: "<?php echo site_url('tender/get_content'); ?>",
                type: "POST",
                data: {
                    tbl: "get_tender_enquiry_timeline",
                    id: tender_enq_id,
                    modal_label: modal_label
                },
                dataType: "html",
                success: function (res) {
                    $("#timeline_details_table").html(res);
                    $('#view_timeline_details').modal('show');
                },
                error: function (xhr, status, error) {
                    console.error("Error loading timeline details:", error);
                    $("#timeline_details_table").html(
                        '<tr><td class="text-center text-danger"><i class="fa fa-exclamation-triangle"></i> Error loading data. Please try again.</td></tr>'
                    );
                    $('#view_timeline_details').modal('show');
                }
            });
        });

    });
</script>