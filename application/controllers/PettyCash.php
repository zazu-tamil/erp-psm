<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PettyCash extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }
        $this->setup_db();
    }

    private function setup_db()
    {
        // Check if petty cash tables exist, if not create them
        $query = $this->db->query("SHOW TABLES LIKE 'petty_cash_transactions'");
        if ($query->num_rows() == 0) {
            $this->db->query("
                CREATE TABLE `petty_cash_transactions` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `transaction_date` date NOT NULL,
                  `transaction_type` varchar(50) NOT NULL DEFAULT 'Outward',
                  `category_id` int(11) DEFAULT NULL,
                  `amount` decimal(10,3) NOT NULL,
                  `remarks` text,
                  `ref_outward_id` int(11) DEFAULT NULL,
                  `created_by` int(11) NOT NULL,
                  `created_at` datetime NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        } else {
            // Modify transaction_type to VARCHAR(50) so it accepts 'Outward', 'Cash', 'Expense', 'Inward'
            $this->db->query("ALTER TABLE `petty_cash_transactions` MODIFY `transaction_type` VARCHAR(50) NOT NULL DEFAULT 'Outward'");

            // Check if ref_outward_id and ref_inward_id column exists
            $fields = $this->db->list_fields('petty_cash_transactions');
            if (!in_array('account_head_id', $fields)) {
                $this->db->query("ALTER TABLE `petty_cash_transactions` ADD `account_head_id` INT(11) NULL AFTER `transaction_type`");
            }
            if (!in_array('ref_outward_id', $fields)) {
                $this->db->query("ALTER TABLE `petty_cash_transactions` ADD `ref_outward_id` INT(11) NULL AFTER `remarks`");
            }
            if (!in_array('ref_inward_id', $fields)) {
                $this->db->query("ALTER TABLE `petty_cash_transactions` ADD `ref_inward_id` INT(11) NULL AFTER `ref_outward_id`");
            }
        }

        // Update any invalid/empty transaction_type records to 'Outward'
        $this->db->query("UPDATE `petty_cash_transactions` SET `transaction_type` = 'Outward' WHERE `transaction_type` = '' OR `transaction_type` IS NULL");

        $query = $this->db->query("SHOW TABLES LIKE 'petty_cash_categories'");
        if ($query->num_rows() == 0) {
            $this->db->query("
                CREATE TABLE `petty_cash_categories` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `category_name` varchar(100) NOT NULL,
                  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            // Insert default categories
            $categories = ['Fuel', 'Tea', 'Salary', 'Auto Rent', 'Worker Labor'];
            foreach ($categories as $cat) {
                $this->db->insert('petty_cash_categories', ['category_name' => $cat]);
            }
        }
    }

    public function index()
    {
        $data['js'] = 'accounts/petty-cash.inc'; // Dummy inc if needed

        // 0. Date Filter
        $from_date = $this->input->post('from_date') ?: $this->input->get('from_date');
        $to_date = $this->input->post('to_date') ?: $this->input->get('to_date');

        if (!$from_date) {
            $from_date = date('Y-m-01'); // Default to start of current month
        }
        if (!$to_date) {
            $to_date = date('Y-m-t'); // Default to end of current month
        }

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;

        // 1. Direct Petty Cash Inward (Income) in date range
        $this->db->select_sum('amount');
        $this->db->where_in('transaction_type', ['Inward', 'Income']);
        $this->db->where('status !=', 'Deleted');
        $this->db->where('transaction_date >=', $from_date);
        $this->db->where('transaction_date <=', $to_date);
        $inward_query = $this->db->get('petty_cash_transactions')->row();
        $petty_inward = $inward_query->amount ? (float)$inward_query->amount : 0;

        // Tender Receipts Cash Inward in date range
        $this->db->select_sum('amount');
        $this->db->where('receipt_mode', 'Cash');
        $this->db->where('status !=', 'Delete');
        $this->db->where('status !=', 'Deleted');
        $this->db->where('receipt_date >=', $from_date);
        $this->db->where('receipt_date <=', $to_date);
        $tr_inward_query = $this->db->get('tender_receipt_info')->row();
        $tr_inward = $tr_inward_query->amount ? (float)$tr_inward_query->amount : 0;

        // Overall Income (Total Inward in date range)
        $total_inward = $petty_inward + $tr_inward;

        // 2. Direct Petty Cash Outward (Expense) in date range
        $this->db->select_sum('amount');
        $this->db->where_in('transaction_type', ['Outward', 'Cash', 'Expense']);
        $this->db->where('status !=', 'Deleted');
        $this->db->where('transaction_date >=', $from_date);
        $this->db->where('transaction_date <=', $to_date);
        $outward_query = $this->db->get('petty_cash_transactions')->row();
        $petty_outward = $outward_query->amount ? (float)$outward_query->amount : 0;

        // Vendor Payments Cash Outward in date range
        $this->db->select_sum('amount');
        $this->db->where('payment_mode', 'Cash');
        $this->db->where('status !=', 'Delete');
        $this->db->where('status !=', 'Deleted');
        $this->db->where('payment_date >=', $from_date);
        $this->db->where('payment_date <=', $to_date);
        $vp_outward_query = $this->db->get('vendor_payment_info')->row();
        $vp_outward = $vp_outward_query->amount ? (float)$vp_outward_query->amount : 0;

        // Customs Bills Outward in date range
        $this->db->select_sum('customs_tot_amt');
        $this->db->where('status !=', 'Delete');
        $this->db->where('status !=', 'Deleted');
        $this->db->where('invoice_date >=', $from_date);
        $this->db->where('invoice_date <=', $to_date);
        $cb_outward_query = $this->db->get('customs_bill_info')->row();
        $cb_outward = $cb_outward_query->customs_tot_amt ? (float)$cb_outward_query->customs_tot_amt : 0;

        // Delivery Partner Bills Outward in date range
        $this->db->select_sum('g_total');
        $this->db->where('status !=', 'Delete');
        $this->db->where('status !=', 'Deleted');
        $this->db->where('invoice_date >=', $from_date);
        $this->db->where('invoice_date <=', $to_date);
        $dp_outward_query = $this->db->get('dp_bill_info')->row();
        $dp_outward = $dp_outward_query->g_total ? (float)$dp_outward_query->g_total : 0;

        // Local Purchase Bills Outward in date range
        $this->db->select_sum('tot_amt_with_tax');
        $this->db->where('status !=', 'Delete');
        $this->db->where('status !=', 'Deleted');
        $this->db->where('invoice_date >=', $from_date);
        $this->db->where('invoice_date <=', $to_date);
        $lp_outward_query = $this->db->get('local_purchase_bill_info')->row();
        $lp_outward = $lp_outward_query->tot_amt_with_tax ? (float)$lp_outward_query->tot_amt_with_tax : 0;

        // Overall Expenses (Total Outward in date range)
        $total_outward = $petty_outward + $vp_outward + $cb_outward + $dp_outward + $lp_outward;

        // Key Bar Chart & Summary Metrics
        $data['overall_income'] = $total_inward;
        $data['overall_expenses'] = $total_outward;
        $data['pettycash_income'] = $petty_inward;
        $data['pettycash_expenses'] = $petty_outward;

        $data['balance'] = $total_inward - $total_outward;
        $data['total_inward'] = $total_inward;
        $data['total_outward'] = $total_outward;
        $data['this_month_inward'] = $total_inward;
        $data['this_month_outward'] = $total_outward;

        // Total Transactions count in date range
        $this->db->where('status !=', 'Deleted');
        $this->db->where('transaction_date >=', $from_date);
        $this->db->where('transaction_date <=', $to_date);
        $petty_tx_count = $this->db->count_all_results('petty_cash_transactions');

        $this->db->where('receipt_mode', 'Cash');
        $this->db->where('status !=', 'Delete');
        $this->db->where('status !=', 'Deleted');
        $this->db->where('receipt_date >=', $from_date);
        $this->db->where('receipt_date <=', $to_date);
        $tr_tx_count = $this->db->count_all_results('tender_receipt_info');

        $this->db->where('payment_mode', 'Cash');
        $this->db->where('status !=', 'Delete');
        $this->db->where('status !=', 'Deleted');
        $this->db->where('payment_date >=', $from_date);
        $this->db->where('payment_date <=', $to_date);
        $vp_tx_count = $this->db->count_all_results('vendor_payment_info');

        $this->db->where('status !=', 'Delete');
        $this->db->where('status !=', 'Deleted');
        $this->db->where('invoice_date >=', $from_date);
        $this->db->where('invoice_date <=', $to_date);
        $cb_tx_count = $this->db->count_all_results('customs_bill_info');

        $this->db->where('status !=', 'Delete');
        $this->db->where('status !=', 'Deleted');
        $this->db->where('invoice_date >=', $from_date);
        $this->db->where('invoice_date <=', $to_date);
        $dp_tx_count = $this->db->count_all_results('dp_bill_info');

        $this->db->where('status !=', 'Delete');
        $this->db->where('status !=', 'Deleted');
        $this->db->where('invoice_date >=', $from_date);
        $this->db->where('invoice_date <=', $to_date);
        $lp_tx_count = $this->db->count_all_results('local_purchase_bill_info');

        $data['this_month_transactions'] = $petty_tx_count + $tr_tx_count + $vp_tx_count + $cb_tx_count + $dp_tx_count + $lp_tx_count;

        // Expense Summary Breakdown for Donut Chart & Legend
        $expense_summary = [];

        if ($petty_outward > 0) {
            $this->db->select('c.sub_account_head_name as category_name, SUM(t.amount) as total_amount');
            $this->db->from('petty_cash_transactions t');
            $this->db->join('cb_sub_account_head_info c', 'c.sub_account_head_id = t.category_id', 'left');
            $this->db->where_in('t.transaction_type', ['Outward', 'Cash', 'Expense']);
            $this->db->where('t.status !=', 'Deleted');
            $this->db->where('t.transaction_date >=', $from_date);
            $this->db->where('t.transaction_date <=', $to_date);
            $this->db->group_by('t.category_id');
            $this->db->order_by('total_amount', 'DESC');
            $petty_summary = $this->db->get()->result_array();

            $categorized_total = 0;
            foreach ($petty_summary as $ps) {
                if (!empty($ps['category_name'])) {
                    $amt = (float)$ps['total_amount'];
                    $expense_summary[] = [
                        'category_name' => $ps['category_name'],
                        'total_amount' => $amt
                    ];
                    $categorized_total += $amt;
                }
            }

            $uncategorized = $petty_outward - $categorized_total;
            if ($uncategorized > 0) {
                $expense_summary[] = [
                    'category_name' => 'Petty Cash Expense',
                    'total_amount' => (float)$uncategorized
                ];
            }
        }

        if ($vp_outward > 0) {
            $expense_summary[] = [
                'category_name' => 'Vendor Bills (Cash)',
                'total_amount' => $vp_outward
            ];
        }

        if ($cb_outward > 0) {
            $expense_summary[] = [
                'category_name' => 'Customs Bills',
                'total_amount' => $cb_outward
            ];
        }

        if ($dp_outward > 0) {
            $expense_summary[] = [
                'category_name' => 'Delivery Partner Bills',
                'total_amount' => $dp_outward
            ];
        }

        if ($lp_outward > 0) {
            $expense_summary[] = [
                'category_name' => 'Local Purchase Bills',
                'total_amount' => $lp_outward
            ];
        }

        $data['expense_summary'] = $expense_summary;

        // Filtered History UNION SQL
        $esc_from = $this->db->escape_str($from_date);
        $esc_to = $this->db->escape_str($to_date);

        $union_sql = "
            SELECT * FROM (
                SELECT 
                    t.id,
                    t.transaction_date,
                    IF(t.transaction_type = 'Inward' OR t.transaction_type = 'Income', 'Inward', 'Outward') AS transaction_type,
                    t.account_head_id,
                    t.category_id,
                    t.amount,
                    t.remarks,
                    t.created_at,
                    c.sub_account_head_name AS category_name,
                    a.account_head_name,
                    'Petty Cash' AS source_type
                FROM petty_cash_transactions t
                LEFT JOIN cb_sub_account_head_info c ON c.sub_account_head_id = t.category_id
                LEFT JOIN cb_account_head_info a ON a.account_head_id = t.account_head_id
                WHERE t.status != 'Deleted' AND t.transaction_date >= '$esc_from' AND t.transaction_date <= '$esc_to'

                UNION ALL

                SELECT 
                    tr.tender_receipt_id AS id,
                    tr.receipt_date AS transaction_date,
                    'Inward' AS transaction_type,
                    NULL AS account_head_id,
                    NULL AS category_id,
                    tr.amount,
                    CONCAT('Receipt No: ', IFNULL(tr.receipt_no, ''), IF(cust.customer_name IS NOT NULL AND cust.customer_name != '', CONCAT(' | Customer: ', cust.customer_name), ''), IF(tr.remarks IS NOT NULL AND tr.remarks != '', CONCAT(' | ', tr.remarks), '')) AS remarks,
                    IFNULL(tr.created_date, tr.receipt_date) AS created_at,
                    'Cash Inward' AS category_name,
                    'Tender Receipt (Cash)' AS account_head_name,
                    'Tender Receipt' AS source_type
                FROM tender_receipt_info tr
                LEFT JOIN customer_info cust ON cust.customer_id = tr.customer_id
                WHERE tr.receipt_mode = 'Cash' AND (tr.status = 'Active' OR (tr.status != 'Delete' AND tr.status != 'Deleted')) AND tr.receipt_date >= '$esc_from' AND tr.receipt_date <= '$esc_to'

                UNION ALL

                SELECT 
                    vp.vendor_payment_id AS id,
                    vp.payment_date AS transaction_date,
                    'Outward' AS transaction_type,
                    NULL AS account_head_id,
                    NULL AS category_id,
                    vp.amount,
                    CONCAT('Payment No: ', IFNULL(vp.payment_no, ''), IF(ven.vendor_name IS NOT NULL AND ven.vendor_name != '', CONCAT(' | Vendor: ', ven.vendor_name), ''), IF(vp.remarks IS NOT NULL AND vp.remarks != '', CONCAT(' | ', vp.remarks), '')) AS remarks,
                    IFNULL(vp.created_date, vp.payment_date) AS created_at,
                    'Cash Outward' AS category_name,
                    'Vendor Bill (Cash)' AS account_head_name,
                    'Vendor Payment' AS source_type
                FROM vendor_payment_info vp
                LEFT JOIN vendor_info ven ON ven.vendor_id = vp.vendor_id
                WHERE vp.payment_mode = 'Cash' AND (vp.status = 'Active' OR (vp.status != 'Delete' AND vp.status != 'Deleted')) AND vp.payment_date >= '$esc_from' AND vp.payment_date <= '$esc_to'

                UNION ALL

                SELECT 
                    cb.customs_bill_id AS id,
                    cb.invoice_date AS transaction_date,
                    'Outward' AS transaction_type,
                    NULL AS account_head_id,
                    NULL AS category_id,
                    cb.customs_tot_amt AS amount,
                    CONCAT('Customs Bill No: ', IFNULL(cb.invoice_no, ''), IF(ven.vendor_name IS NOT NULL AND ven.vendor_name != '', CONCAT(' | Vendor: ', ven.vendor_name), ''), IF(cb.remarks IS NOT NULL AND cb.remarks != '', CONCAT(' | ', cb.remarks), '')) AS remarks,
                    IFNULL(cb.created_date, cb.invoice_date) AS created_at,
                    'Customs Expense' AS category_name,
                    'Customs Bill' AS account_head_name,
                    'Customs Bill' AS source_type
                FROM customs_bill_info cb
                LEFT JOIN vendor_info ven ON ven.vendor_id = cb.vendor_id
                WHERE (cb.status = 'Active' OR (cb.status != 'Delete' AND cb.status != 'Deleted')) AND cb.invoice_date >= '$esc_from' AND cb.invoice_date <= '$esc_to'

                UNION ALL

                SELECT 
                    dp.dp_bill_id AS id,
                    dp.invoice_date AS transaction_date,
                    'Outward' AS transaction_type,
                    NULL AS account_head_id,
                    NULL AS category_id,
                    dp.g_total AS amount,
                    CONCAT('DP Bill No: ', IFNULL(dp.invoice_no, ''), IF(ven.vendor_name IS NOT NULL AND ven.vendor_name != '', CONCAT(' | Vendor: ', ven.vendor_name), ''), IF(dp.remarks IS NOT NULL AND dp.remarks != '', CONCAT(' | ', dp.remarks), '')) AS remarks,
                    IFNULL(dp.created_date, dp.invoice_date) AS created_at,
                    'Delivery Expense' AS category_name,
                    'Delivery Partner Bill' AS account_head_name,
                    'DP Bill' AS source_type
                FROM dp_bill_info dp
                LEFT JOIN vendor_info ven ON ven.vendor_id = dp.vendor_id
                WHERE (dp.status = 'Active' OR (dp.status != 'Delete' AND dp.status != 'Deleted')) AND dp.invoice_date >= '$esc_from' AND dp.invoice_date <= '$esc_to'

                UNION ALL

                SELECT 
                    lp.local_purchase_bill_id AS id,
                    lp.invoice_date AS transaction_date,
                    'Outward' AS transaction_type,
                    NULL AS account_head_id,
                    NULL AS category_id,
                    lp.tot_amt_with_tax AS amount,
                    CONCAT('Local Bill No: ', IFNULL(lp.invoice_no, ''), IF(ven.vendor_name IS NOT NULL AND ven.vendor_name != '', CONCAT(' | Vendor: ', ven.vendor_name), ''), IF(lp.remarks IS NOT NULL AND lp.remarks != '', CONCAT(' | ', lp.remarks), '')) AS remarks,
                    IFNULL(lp.created_date, lp.invoice_date) AS created_at,
                    'Purchase Expense' AS category_name,
                    'Local Purchase Bill' AS account_head_name,
                    'Local Purchase Bill' AS source_type
                FROM local_purchase_bill_info lp
                LEFT JOIN vendor_info ven ON ven.vendor_id = lp.vendor_id
                WHERE (lp.status = 'Active' OR (lp.status != 'Delete' AND lp.status != 'Deleted')) AND lp.invoice_date >= '$esc_from' AND lp.invoice_date <= '$esc_to'
            ) AS combined_history
            ORDER BY transaction_date DESC, id DESC
        ";

        $data['history'] = $this->db->query($union_sql)->result_array();

        $this->load->view('page/accounts/petty-cash', $data);
    }

    public function add_funds()
    {
        $amount = $this->input->post('amount');
        $date = $this->input->post('transaction_date');
        $remarks = $this->input->post('remarks');

        if ($amount > 0) {
            $this->db->insert('petty_cash_transactions', [
                'transaction_date' => $date,
                'transaction_type' => 'Inward',
                'amount' => $amount,
                'remarks' => $remarks,
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_at' => date('Y-m-d H:i:s')
            ]);

        }

        $this->session->set_flashdata('success', 'Funds added successfully');
        redirect('petty-cash');
    }

    public function add_expense()
    {
        $amount = $this->input->post('amount');
        $date = $this->input->post('transaction_date');
        $account_head_id = $this->input->post('account_head_id');
        $category_id = $this->input->post('category_id');
        $remarks = $this->input->post('remarks');

        if ($amount > 0) {
            $this->db->insert('petty_cash_transactions', [
                'transaction_date' => $date,
                'transaction_type' => 'Outward', 
                'amount' => $amount,
                'remarks' => $remarks,
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_at' => date('Y-m-d H:i:s')
            ]); 
        }

        $this->session->set_flashdata('success', 'Expense recorded successfully');
        redirect('petty-cash');
    }

    public function edit_transaction()
    {
        $id = $this->input->post('id');
        $transaction_type = $this->input->post('transaction_type');
        $amount = $this->input->post('amount');
        $date = $this->input->post('transaction_date');
        $remarks = $this->input->post('remarks');

        // Fetch current transaction to check mapping
        $txn = $this->db->get_where('petty_cash_transactions', ['id' => $id])->row();

        $update_data = [
            'transaction_date' => $date,
            'amount' => $amount,
            'remarks' => $remarks
        ]; 
        $this->db->where('id', $id);
        $this->db->update('petty_cash_transactions', $update_data);
 

        $this->session->set_flashdata('success', 'Transaction updated successfully');
        redirect('petty-cash');
    }
    public function delete_transaction($id = null)
    {
        if (!$id) {
            redirect('petty-cash'); 
        }

        $txn = $this->db->get_where('petty_cash_transactions', ['id' => $id])->row();

        if ($txn) { 
            $this->db->where('id', $id);
            $this->db->update('petty_cash_transactions', ['status' => 'Deleted']);

            $this->session->set_flashdata('success', 'Transaction soft deleted successfully');
        } 
        redirect('petty-cash');
    } 
}
