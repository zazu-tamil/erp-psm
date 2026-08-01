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

        $this->db->where('status !=', 'Delete');
        $this->db->where('user_id !=', '1');
        $data['total_user'] = $this->db->count_all_results('user_login_info');

        $this->db->where('status !=', 'Delete');
        $data['total_items'] = $this->db->count_all_results('item_info');

        $this->db->where('status !=', 'Delete');
        $data['total_category'] = $this->db->count_all_results('category_info');

        $this->db->where('status !=', 'Delete');
        $data['brand_count'] = $this->db->count_all_results('brand_info');

        $this->db->where('status !=', 'Delete');
        $data['vendor_count'] = $this->db->count_all_results('vendor_info');

        $this->db->where('status !=', 'Delete');
        $data['customer_count'] = $this->db->count_all_results('customer_info');

        $sql = "
            SELECT COUNT(*) AS total_enquiry 
            FROM tender_enquiry_info 
            WHERE status != 'Delete' 
            AND DATE(created_date) = CURDATE()
        ";

        $query = $this->db->query($sql);
        $row = $query->row();
        $data['total_enquiry'] = $row ? $row->total_enquiry : 0;

        $sql = "
            SELECT COUNT(*) AS tender_quotation_count 
            FROM tender_quotation_info 
            WHERE status != 'Delete' 
            AND DATE(created_date) = CURDATE()
        ";

        $query = $this->db->query($sql);
        $row = $query->row();
        $data['tender_quotation_count'] = $row ? $row->tender_quotation_count : 0;

        $sql = "
            SELECT COUNT(*) AS vendor_enquiry_count 
            FROM vendor_rate_enquiry_info 
            WHERE status != 'Delete' 
            AND DATE(created_date) = CURDATE()
        ";

        $query = $this->db->query($sql);
        $row = $query->row();
        $data['vendor_enquiry_count'] = $row ? $row->vendor_enquiry_count : 0;

        // Redesign Queries:
        // 1. Total Tenders (Active)
        $this->db->where('status !=', 'Delete');
        $data['total_tenders'] = $this->db->count_all_results('tender_enquiry_info');

        // 2. Total Sales Revenue (Active)
        $revenue_sql = "
            SELECT SUM(total_amount - IFNULL(tax_amount, 0)) AS total_sales
            FROM tender_enq_invoice_info
            WHERE status = 'Active'
        ";
        $revenue_row = $this->db->query($revenue_sql)->row();
        $data['total_sales'] = $revenue_row ? floatval($revenue_row->total_sales) : 0;

        // 3. Total Purchases (Active)
        $expense_sql = "
            SELECT SUM(COALESCE(total_amount_wo_tax, total_amount, 0)) AS total_purchases
            FROM vendor_purchase_invoice_info
            WHERE status = 'Active'
        ";
        $expense_row = $this->db->query($expense_sql)->row();
        $data['total_purchases'] = $expense_row ? floatval($expense_row->total_purchases) : 0;

        // 4. Monthly Sales (last 6 months)
        $sales_sql = "
            SELECT 
                DATE_FORMAT(invoice_date, '%b %Y') AS month,
                SUM(total_amount - IFNULL(tax_amount, 0)) AS amount,
                DATE_FORMAT(invoice_date, '%Y-%m') AS month_key
            FROM tender_enq_invoice_info
            WHERE status = 'Active'
              AND invoice_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY month_key
            ORDER BY month_key ASC
        ";
        $sales_query = $this->db->query($sales_sql)->result_array();

        // 5. Monthly Purchases (last 6 months)
        $purchase_sql = "
            SELECT 
                DATE_FORMAT(invoice_date, '%b %Y') AS month,
                SUM(COALESCE(total_amount_wo_tax, total_amount, 0)) AS amount,
                DATE_FORMAT(invoice_date, '%Y-%m') AS month_key
            FROM vendor_purchase_invoice_info
            WHERE status = 'Active'
              AND invoice_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY month_key
            ORDER BY month_key ASC
        ";
        $purchase_query = $this->db->query($purchase_sql)->result_array();

        // Align Monthly data for the charts
        $months = array();
        for ($i = 5; $i >= 0; $i--) {
            $date_key = date('Y-m', strtotime("-$i months"));
            $label = date('M Y', strtotime("-$i months"));
            $months[$date_key] = array(
                'label' => $label,
                'sales' => 0,
                'purchases' => 0
            );
        }

        foreach ($sales_query as $row) {
            if (isset($months[$row['month_key']])) {
                $months[$row['month_key']]['sales'] = floatval($row['amount']);
            }
        }

        foreach ($purchase_query as $row) {
            if (isset($months[$row['month_key']])) {
                $months[$row['month_key']]['purchases'] = floatval($row['amount']);
            }
        }

        $data['chart_months'] = array_column($months, 'label');
        $data['chart_sales'] = array_column($months, 'sales');
        $data['chart_purchases'] = array_column($months, 'purchases');

        // 6. Tender Status Stats
        $status_sql = "
            SELECT tender_status, COUNT(*) AS count 
            FROM tender_enquiry_info 
            WHERE status != 'Delete' 
              AND tender_status IS NOT NULL 
              AND tender_status != ''
            GROUP BY tender_status
        ";
        $data['tender_status_stats'] = $this->db->query($status_sql)->result_array();

        // 7. Recent Tenders (all active with details)
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
        ";
        $data['recent_tenders'] = $this->db->query($recent_tenders_sql)->result_array();

        // 8. Cash Inflow Sum
        $inward_sum_sql = "SELECT SUM(amount) AS total FROM cb_cash_inward_info WHERE status='Active'";
        $inward_row = $this->db->query($inward_sum_sql)->row();
        $data['total_cash_inward'] = $inward_row ? floatval($inward_row->total) : 0;

        // 9. Cash Outflow Sum
        $outward_sum_sql = "SELECT SUM(amount) AS total FROM cb_cash_outward_info WHERE status='Active'";
        $outward_row = $this->db->query($outward_sum_sql)->row();
        $data['total_cash_outward'] = $outward_row ? floatval($outward_row->total) : 0;

        $this->load->view('page/dashboard', $data);
    }


}