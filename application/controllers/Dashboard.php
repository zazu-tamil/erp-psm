<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MY_Controller
{
    public function index()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        date_default_timezone_set("Asia/Calcutta");

        $data = array();
        $data['js'] = 'dash.inc';

        // 1. Period Filter Handling (Default to this_year, easily switchable to this_month, this_quarter, all_time)
        $period = $this->input->get('period') ?: 'this_year';
        $data['selected_period'] = $period;

        switch ($period) {
            case 'this_month':
                $start_date = date('Y-m-01');
                $end_date = date('Y-m-t');
                $prev_start = date('Y-m-01', strtotime('-1 month'));
                $prev_end = date('Y-m-t', strtotime('-1 month'));
                $data['period_label'] = date('F Y');
                $data['prev_period_label'] = date('M Y', strtotime('-1 month'));
                break;
            case 'this_quarter':
                $current_month = date('n');
                $current_quarter = ceil($current_month / 3);
                $start_month = sprintf("%02d", ($current_quarter - 1) * 3 + 1);
                $start_date = date('Y-') . $start_month . '-01';
                $end_date = date('Y-m-t', strtotime($start_date . ' +2 months'));
                $prev_start = date('Y-m-d', strtotime("$start_date -3 months"));
                $prev_end = date('Y-m-t', strtotime("$start_date -1 day"));
                $data['period_label'] = "Q$current_quarter " . date('Y');
                $data['prev_period_label'] = "Prev Quarter";
                break;
            case 'all_time':
                $start_date = null;
                $end_date = null;
                $prev_start = null;
                $prev_end = null;
                $data['period_label'] = "All Time";
                $data['prev_period_label'] = "";
                break;
            case 'this_year':
            default:
                $period = 'this_year';
                $start_date = date('Y-01-01');
                $end_date = date('Y-12-31');
                $prev_start = date('Y-01-01', strtotime('-1 year'));
                $prev_end = date('Y-12-31', strtotime('-1 year'));
                $data['period_label'] = "Year " . date('Y');
                $data['prev_period_label'] = "Year " . date('Y', strtotime('-1 year'));
                break;
        }

        // Master Counts
        $this->db->where('status !=', 'Delete');
        $this->db->where('user_id !=', '1');
        $data['total_user'] = $this->db->count_all_results('user_login_info');

        $this->db->where('status !=', 'Delete');
        $data['total_items'] = $this->db->count_all_results('item_info');

        $this->db->where('status !=', 'Delete');
        $data['total_category'] = $this->db->count_all_results('category_info');

        $this->db->where('brand_info.status !=', 'Delete');
        $data['brand_count'] = $this->db->count_all_results('brand_info');

        $this->db->where('status !=', 'Delete');
        $data['vendor_count'] = $this->db->count_all_results('vendor_info');

        $this->db->where('status !=', 'Delete');
        $data['customer_count'] = $this->db->count_all_results('customer_info');

        $this->db->where('status !=', 'Delete');
        $data['total_tenders'] = $this->db->count_all_results('tender_enquiry_info');

        // Customer POs and DCs counts
        $this->db->where('status !=', 'Delete');
        $data['total_customer_pos'] = $this->db->count_all_results('customer_tender_po_info');

        $this->db->where('status !=', 'Delete');
        $data['total_delivery_challans'] = $this->db->count_all_results('tender_dc_info');

        // 2. Financial Metrics for Selected Period
        $date_sales_where = $start_date ? "AND invoice_date BETWEEN '$start_date' AND '$end_date'" : "";
        $sales_sql = "
            SELECT IFNULL(SUM(total_amount - IFNULL(tax_amount, 0)), 0) AS total_sales
            FROM tender_enq_invoice_info
            WHERE status = 'Active' $date_sales_where
        ";
        $sales_row = $this->db->query($sales_sql)->row();
        $data['period_sales'] = $sales_row ? floatval($sales_row->total_sales) : 0;
        $data['total_sales'] = $data['period_sales']; // backward compat

        // Previous period sales (for trend)
        if ($prev_start) {
            $prev_sales_sql = "
                SELECT IFNULL(SUM(total_amount - IFNULL(tax_amount, 0)), 0) AS prev_sales
                FROM tender_enq_invoice_info
                WHERE status = 'Active' AND invoice_date BETWEEN '$prev_start' AND '$prev_end'
            ";
            $prev_sales_row = $this->db->query($prev_sales_sql)->row();
            $prev_sales = $prev_sales_row ? floatval($prev_sales_row->prev_sales) : 0;
            if ($prev_sales > 0) {
                $data['sales_growth_pct'] = round((($data['period_sales'] - $prev_sales) / $prev_sales) * 100, 1);
            } else {
                $data['sales_growth_pct'] = $data['period_sales'] > 0 ? 100 : 0;
            }
        } else {
            $data['sales_growth_pct'] = null;
        }

        // Purchases for Selected Period
        $date_pur_where = $start_date ? "AND invoice_date BETWEEN '$start_date' AND '$end_date'" : "";
        $expense_sql = "
            SELECT IFNULL(SUM(COALESCE(total_amount_wo_tax, total_amount, 0)), 0) AS total_purchases
            FROM (
                SELECT invoice_date, total_amount_wo_tax, total_amount FROM vendor_purchase_invoice_info WHERE status = 'Active' $date_pur_where
                UNION ALL
                SELECT invoice_date, total_amount_wo_tax, total_amount FROM vendor_purchase_multiple_invoice_info WHERE status = 'Active' $date_pur_where
            ) AS vpi_all
        ";
        $expense_row = $this->db->query($expense_sql)->row();
        $data['period_purchases'] = $expense_row ? floatval($expense_row->total_purchases) : 0;
        $data['total_purchases'] = $data['period_purchases']; // backward compat

        // 3. Accounts Receivable (Total Due from Customers) - Current Position
        $ar_sql = "
            SELECT 
                (
                    (SELECT IFNULL(SUM(total_amount), 0) FROM tender_enq_invoice_info WHERE status = 'Active')
                    +
                    (SELECT IFNULL(SUM(total_amount), 0) FROM credit_debit_note_info WHERE status != 'Delete' AND party_type = 'Customer' AND note_type = 'Debit')
                ) - (
                    (SELECT IFNULL(SUM(amount), 0) FROM tender_receipt_info WHERE status = 'Active')
                    +
                    (SELECT IFNULL(SUM(adv_payment_amt), 0) FROM customer_advance_payment_info WHERE status = 'Active')
                    +
                    (SELECT IFNULL(SUM(total_amount), 0) FROM credit_debit_note_info WHERE status != 'Delete' AND party_type = 'Customer' AND note_type = 'Credit')
                ) AS net_ar
        ";
        $ar_row = $this->db->query($ar_sql)->row();
        $data['total_receivable'] = $ar_row ? floatval($ar_row->net_ar) : 0;

        // 4. Accounts Payable (Total Due to Suppliers) - Current Position
        $ap_sql = "
            SELECT 
                (
                    (
                        SELECT IFNULL(SUM(tot), 0) FROM (
                            SELECT COALESCE(total_amount_inc_addl, total_amount) AS tot FROM vendor_purchase_invoice_info WHERE status = 'Active'
                            UNION ALL
                            SELECT COALESCE(total_amount_inc_addl, total_amount) AS tot FROM vendor_purchase_multiple_invoice_info WHERE status = 'Active'
                            UNION ALL
                            SELECT tot_amt_with_tax AS tot FROM local_purchase_bill_info WHERE status = 'Active'
                            UNION ALL
                            SELECT g_total AS tot FROM dp_bill_info WHERE status = 'Active'
                            UNION ALL
                            SELECT customs_payable AS tot FROM customs_bill_info WHERE status = 'Active' AND ac_type_opt = 'Accountable'
                        ) AS p
                    )
                    +
                    (SELECT IFNULL(SUM(total_amount), 0) FROM credit_debit_note_info WHERE status != 'Delete' AND party_type = 'Supplier' AND note_type = 'Credit')
                ) - (
                    (SELECT IFNULL(SUM(amount), 0) FROM vendor_payment_info WHERE status = 'Active')
                    +
                    (SELECT IFNULL(SUM(adv_payment_amt), 0) FROM vendor_advance_payment_info WHERE status = 'Active')
                    +
                    (SELECT IFNULL(SUM(total_amount), 0) FROM credit_debit_note_info WHERE status != 'Delete' AND party_type = 'Supplier' AND note_type = 'Debit')
                ) AS net_ap
        ";
        $ap_row = $this->db->query($ap_sql)->row();
        $data['total_payable'] = $ap_row ? floatval($ap_row->net_ap) : 0;

        // 5. Cash Inflow, Outflow & Balance
        $inward_sum_sql = "SELECT IFNULL(SUM(amount), 0) AS total FROM cb_cash_inward_info WHERE status='Active'";
        $inward_row = $this->db->query($inward_sum_sql)->row();
        $data['total_cash_inward'] = $inward_row ? floatval($inward_row->total) : 0;

        $outward_sum_sql = "SELECT IFNULL(SUM(amount), 0) AS total FROM cb_cash_outward_info WHERE status='Active'";
        $outward_row = $this->db->query($outward_sum_sql)->row();
        $data['total_cash_outward'] = $outward_row ? floatval($outward_row->total) : 0;
        $data['cash_balance'] = $data['total_cash_inward'] - $data['total_cash_outward'];

        // 6. Today's Activity Queries (for modals)
        // a. Enquiries Added Today
        $sql = "
            SELECT 
                te.tender_enquiry_id, 
                get_tender_info(te.tender_enquiry_id) as enquiry_no,
                te.enquiry_date,
                te.tender_name,
                te.tender_status,
                c.customer_name,
                comp.company_name
            FROM tender_enquiry_info te
            LEFT JOIN customer_info c ON te.customer_id = c.customer_id
            LEFT JOIN company_info comp ON te.company_id = comp.company_id
            WHERE te.status != 'Delete' 
            AND DATE(te.created_date) = CURDATE()
            ORDER BY te.tender_enquiry_id DESC
        ";
        $data['enquiries_added_today_list'] = $this->db->query($sql)->result_array();
        $data['total_enquiry'] = count($data['enquiries_added_today_list']);

        // b. Quotations Generated Today
        $sql = "
            SELECT 
                tq.tender_quotation_id, 
                tq.quotation_no,
                tq.quote_date,
                get_tender_info(tq.tender_enquiry_id) as enquiry_no,
                c.customer_name,
                comp.company_name
            FROM tender_quotation_info tq
            LEFT JOIN customer_info c ON tq.customer_id = c.customer_id
            LEFT JOIN company_info comp ON tq.company_id = comp.company_id
            WHERE tq.status != 'Delete' 
            AND DATE(tq.created_date) = CURDATE()
            ORDER BY tq.tender_quotation_id DESC
        ";
        $data['quotations_generated_today_list'] = $this->db->query($sql)->result_array();
        $data['tender_quotation_count'] = count($data['quotations_generated_today_list']);

        // c. Vendor Rate Inquiries Today
        $sql = "
            SELECT 
                vq.vendor_rate_enquiry_id, 
                vq.enquiry_no as vendor_enquiry_no,
                vq.enquiry_date,
                get_tender_info(vq.tender_enquiry_id) as enquiry_no,
                c.customer_name,
                comp.company_name
            FROM vendor_rate_enquiry_info vq
            LEFT JOIN customer_info c ON vq.customer_id = c.customer_id
            LEFT JOIN company_info comp ON vq.company_id = comp.company_id
            WHERE vq.status != 'Delete' 
            AND DATE(vq.created_date) = CURDATE()
            ORDER BY vq.vendor_rate_enquiry_id DESC
        ";
        $data['vendor_inquiries_today_list'] = $this->db->query($sql)->result_array();
        $data['vendor_enquiry_count'] = count($data['vendor_inquiries_today_list']);

        // d. Tenders Closing Today & Next 7 Days
        $closing_today_sql = "
            SELECT 
                te.tender_enquiry_id, 
                get_tender_info(te.tender_enquiry_id) as enquiry_no,
                te.enquiry_date,
                te.closing_date,
                te.tender_name,
                te.tender_status,
                c.customer_name,
                comp.company_name,
                DATEDIFF(te.closing_date, CURDATE()) AS days_left
            FROM tender_enquiry_info te
            LEFT JOIN customer_info c ON te.customer_id = c.customer_id
            LEFT JOIN company_info comp ON te.company_id = comp.company_id
            WHERE te.status = 'Active' 
            AND DATE(te.closing_date) = CURDATE()
            ORDER BY te.closing_date ASC, te.tender_enquiry_id DESC
        ";
        $data['tenders_closing_today'] = $this->db->query($closing_today_sql)->result_array();
        $data['tenders_closing_today_count'] = count($data['tenders_closing_today']);

        $closing_week_sql = "
            SELECT 
                te.tender_enquiry_id, 
                get_tender_info(te.tender_enquiry_id) as enquiry_no,
                te.enquiry_date,
                te.closing_date,
                te.tender_name,
                te.tender_status,
                c.customer_name,
                comp.company_name,
                DATEDIFF(te.closing_date, CURDATE()) AS days_left
            FROM tender_enquiry_info te
            LEFT JOIN customer_info c ON te.customer_id = c.customer_id
            LEFT JOIN company_info comp ON te.company_id = comp.company_id
            WHERE te.status = 'Active' 
            AND te.closing_date >= CURDATE()
            AND te.closing_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
            ORDER BY te.closing_date ASC, te.tender_enquiry_id DESC
        ";
        $data['tenders_closing_week'] = $this->db->query($closing_week_sql)->result_array();
        $data['tenders_closing_week_count'] = count($data['tenders_closing_week']);

        // 7. Monthly Sales vs Purchases (Last 12 Months) for Chart
        $chart_months_count = 12;
        $sales_chart_sql = "
            SELECT 
                DATE_FORMAT(invoice_date, '%b %Y') AS month,
                SUM(total_amount - IFNULL(tax_amount, 0)) AS amount,
                DATE_FORMAT(invoice_date, '%Y-%m') AS month_key
            FROM tender_enq_invoice_info
            WHERE status = 'Active'
              AND invoice_date >= DATE_SUB(CURDATE(), INTERVAL $chart_months_count MONTH)
            GROUP BY month_key
            ORDER BY month_key ASC
        ";
        $sales_chart_query = $this->db->query($sales_chart_sql)->result_array();

        $purchase_chart_sql = "
            SELECT 
                DATE_FORMAT(invoice_date, '%b %Y') AS month,
                SUM(COALESCE(total_amount_wo_tax, total_amount, 0)) AS amount,
                DATE_FORMAT(invoice_date, '%Y-%m') AS month_key
            FROM (
                SELECT invoice_date, total_amount_wo_tax, total_amount FROM vendor_purchase_invoice_info
                WHERE status = 'Active' AND invoice_date >= DATE_SUB(CURDATE(), INTERVAL $chart_months_count MONTH)
                UNION ALL
                SELECT invoice_date, total_amount_wo_tax, total_amount FROM vendor_purchase_multiple_invoice_info
                WHERE status = 'Active' AND invoice_date >= DATE_SUB(CURDATE(), INTERVAL $chart_months_count MONTH)
            ) AS vpi_all
            GROUP BY month_key
            ORDER BY month_key ASC
        ";
        $purchase_chart_query = $this->db->query($purchase_chart_sql)->result_array();

        // Align 12 months data
        $months = array();
        for ($i = $chart_months_count - 1; $i >= 0; $i--) {
            $date_key = date('Y-m', strtotime("-$i months"));
            $label = date('M Y', strtotime("-$i months"));
            $months[$date_key] = array(
                'label' => $label,
                'sales' => 0,
                'purchases' => 0
            );
        }

        foreach ($sales_chart_query as $row) {
            if (isset($months[$row['month_key']])) {
                $months[$row['month_key']]['sales'] = floatval($row['amount']);
            }
        }

        foreach ($purchase_chart_query as $row) {
            if (isset($months[$row['month_key']])) {
                $months[$row['month_key']]['purchases'] = floatval($row['amount']);
            }
        }

        $data['chart_months'] = array_column($months, 'label');
        $data['chart_sales'] = array_column($months, 'sales');
        $data['chart_purchases'] = array_column($months, 'purchases');

        // 8. Tender Status Pipeline Stats
        $status_sql = "
            SELECT tender_status, COUNT(*) AS count 
            FROM tender_enquiry_info 
            WHERE status != 'Delete' 
              AND tender_status IS NOT NULL 
              AND tender_status != ''
            GROUP BY tender_status
        ";
        $data['tender_status_stats'] = $this->db->query($status_sql)->result_array();

        // 9. Top 5 Customers by Sales Revenue
        $top_cust_sql = "
            SELECT c.customer_name, SUM(i.total_amount) AS total_val, COUNT(i.tender_enq_invoice_id) AS inv_count
            FROM tender_enq_invoice_info i
            JOIN customer_info c ON i.customer_id = c.customer_id
            WHERE i.status = 'Active'
            GROUP BY i.customer_id, c.customer_name
            ORDER BY total_val DESC
            LIMIT 5
        ";
        $data['top_customers'] = $this->db->query($top_cust_sql)->result_array();

        // 10. Top 5 Vendors by Purchase Volume
        $top_vend_sql = "
            SELECT v.vendor_name, SUM(p.tot) AS total_val, COUNT(*) AS bill_count
            FROM (
                SELECT vendor_id, COALESCE(total_amount_inc_addl, total_amount) AS tot FROM vendor_purchase_invoice_info WHERE status = 'Active'
                UNION ALL
                SELECT vendor_id, COALESCE(total_amount_inc_addl, total_amount) AS tot FROM vendor_purchase_multiple_invoice_info WHERE status = 'Active'
            ) p
            JOIN vendor_info v ON p.vendor_id = v.vendor_id
            GROUP BY p.vendor_id, v.vendor_name
            ORDER BY total_val DESC
            LIMIT 5
        ";
        $data['top_vendors'] = $this->db->query($top_vend_sql)->result_array();

        // 11. Recent Tenders (Performance Optimized: Top 15 active tenders)
        $recent_tenders_sql = "
            SELECT 
                te.tender_enquiry_id, 
                get_tender_info(te.tender_enquiry_id) as enquiry_no,
                te.enquiry_date,
                te.tender_name,
                te.tender_status,
                c.customer_name,
                comp.company_name
            FROM tender_enquiry_info te
            LEFT JOIN customer_info c ON te.customer_id = c.customer_id
            LEFT JOIN company_info comp ON te.company_id = comp.company_id
            WHERE te.status = 'Active'
            ORDER BY te.tender_enquiry_id DESC
            LIMIT 15
        ";
        $data['recent_tenders'] = $this->db->query($recent_tenders_sql)->result_array();

        $this->load->view('page/dashboard', $data);
    }
}
