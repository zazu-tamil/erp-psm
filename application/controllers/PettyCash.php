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
            if (!in_array('ac_type', $fields)) {
                $this->db->query("ALTER TABLE `petty_cash_transactions` ADD `ac_type` VARCHAR(50) NULL AFTER `transaction_type`");
            }
            if (!in_array('bank_id', $fields)) {
                $this->db->query("ALTER TABLE `petty_cash_transactions` ADD `bank_id` INT(11) NULL AFTER `ac_type`");
            }
            if (!in_array('cash_category_id', $fields)) {
                $this->db->query("ALTER TABLE `petty_cash_transactions` ADD `cash_category_id` INT(11) NULL AFTER `bank_id`");
            }
            if (!in_array('account_head_id', $fields)) {
                $this->db->query("ALTER TABLE `petty_cash_transactions` ADD `account_head_id` INT(11) NULL AFTER `cash_category_id`");
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
                    t.ac_type,
                    t.bank_id,
                    t.cash_category_id,
                    cc.category_name AS cash_category_name,
                    t.account_head_id,
                    t.category_id,
                    t.amount,
                    t.remarks,
                    t.created_at,
                    c.sub_account_head_name AS category_name,
                    a.account_head_name,
                    'Petty Cash' AS source_type,
                    bank.bank_name
                FROM petty_cash_transactions t
                LEFT JOIN cb_sub_account_head_info c ON c.sub_account_head_id = t.category_id
                LEFT JOIN cb_account_head_info a ON a.account_head_id = t.account_head_id
                LEFT JOIN company_bank_info bank ON bank.bank_id = t.bank_id AND bank.status != 'Delete'
                LEFT JOIN cash_category cc ON cc.cash_category_id = t.cash_category_id AND cc.status = 'Active'
                WHERE t.status != 'Deleted' AND t.transaction_date >= '$esc_from' AND t.transaction_date <= '$esc_to'

                UNION ALL

                SELECT 
                    tr.tender_receipt_id AS id,
                    tr.receipt_date AS transaction_date,
                    'Inward' AS transaction_type,
                    'Cash' AS ac_type,
                    NULL AS bank_id,
                    NULL AS cash_category_id,
                    NULL AS cash_category_name,
                    NULL AS account_head_id,
                    NULL AS category_id,
                    tr.amount,
                    CONCAT('Receipt No: ', IFNULL(tr.receipt_no, ''), IF(cust.customer_name IS NOT NULL AND cust.customer_name != '', CONCAT(' | Customer: ', cust.customer_name), ''), IF(tr.remarks IS NOT NULL AND tr.remarks != '', CONCAT(' | ', tr.remarks), '')) AS remarks,
                    IFNULL(tr.created_date, tr.receipt_date) AS created_at,
                    'Cash Inward' AS category_name,
                    'Tender Receipt (Cash)' AS account_head_name,
                    'Tender Receipt' AS source_type,
                    NULL AS bank_name
                FROM tender_receipt_info tr
                LEFT JOIN customer_info cust ON cust.customer_id = tr.customer_id
                WHERE tr.receipt_mode = 'Cash' AND (tr.status = 'Active' OR (tr.status != 'Delete' AND tr.status != 'Deleted')) AND tr.receipt_date >= '$esc_from' AND tr.receipt_date <= '$esc_to'

                UNION ALL

                SELECT 
                    vp.vendor_payment_id AS id,
                    vp.payment_date AS transaction_date,
                    'Outward' AS transaction_type,
                    'Cash' AS ac_type,
                    NULL AS bank_id,
                    NULL AS cash_category_id,
                    NULL AS cash_category_name,
                    NULL AS account_head_id,
                    NULL AS category_id,
                    vp.amount,
                    CONCAT('Payment No: ', IFNULL(vp.payment_no, ''), IF(ven.vendor_name IS NOT NULL AND ven.vendor_name != '', CONCAT(' | Vendor: ', ven.vendor_name), ''), IF(vp.remarks IS NOT NULL AND vp.remarks != '', CONCAT(' | ', vp.remarks), '')) AS remarks,
                    IFNULL(vp.created_date, vp.payment_date) AS created_at,
                    'Cash Outward' AS category_name,
                    'Vendor Bill (Cash)' AS account_head_name,
                    'Vendor Payment' AS source_type,
                    NULL AS bank_name
                FROM vendor_payment_info vp
                LEFT JOIN vendor_info ven ON ven.vendor_id = vp.vendor_id
                WHERE vp.payment_mode = 'Cash' AND (vp.status = 'Active' OR (vp.status != 'Delete' AND vp.status != 'Deleted')) AND vp.payment_date >= '$esc_from' AND vp.payment_date <= '$esc_to'

                UNION ALL

                SELECT 
                    cb.customs_bill_id AS id,
                    cb.invoice_date AS transaction_date,
                    'Outward' AS transaction_type,
                    NULL AS ac_type,
                    NULL AS bank_id,
                    NULL AS cash_category_id,
                    NULL AS cash_category_name,
                    NULL AS account_head_id,
                    NULL AS category_id,
                    cb.customs_tot_amt AS amount,
                    CONCAT('Customs Bill No: ', IFNULL(cb.invoice_no, ''), IF(ven.vendor_name IS NOT NULL AND ven.vendor_name != '', CONCAT(' | Vendor: ', ven.vendor_name), ''), IF(cb.remarks IS NOT NULL AND cb.remarks != '', CONCAT(' | ', cb.remarks), '')) AS remarks,
                    IFNULL(cb.created_date, cb.invoice_date) AS created_at,
                    'Customs Expense' AS category_name,
                    'Customs Bill' AS account_head_name,
                    'Customs Bill' AS source_type,
                    NULL AS bank_name
                FROM customs_bill_info cb
                LEFT JOIN vendor_info ven ON ven.vendor_id = cb.vendor_id
                WHERE (cb.status = 'Active' OR (cb.status != 'Delete' AND cb.status != 'Deleted')) AND cb.invoice_date >= '$esc_from' AND cb.invoice_date <= '$esc_to'

                UNION ALL

                SELECT 
                    dp.dp_bill_id AS id,
                    dp.invoice_date AS transaction_date,
                    'Outward' AS transaction_type,
                    NULL AS ac_type,
                    NULL AS bank_id,
                    NULL AS cash_category_id,
                    NULL AS cash_category_name,
                    NULL AS account_head_id,
                    NULL AS category_id,
                    dp.g_total AS amount,
                    CONCAT('DP Bill No: ', IFNULL(dp.invoice_no, ''), IF(ven.vendor_name IS NOT NULL AND ven.vendor_name != '', CONCAT(' | Vendor: ', ven.vendor_name), ''), IF(dp.remarks IS NOT NULL AND dp.remarks != '', CONCAT(' | ', dp.remarks), '')) AS remarks,
                    IFNULL(dp.created_date, dp.invoice_date) AS created_at,
                    'Delivery Expense' AS category_name,
                    'Delivery Partner Bill' AS account_head_name,
                    'DP Bill' AS source_type,
                    NULL AS bank_name
                FROM dp_bill_info dp
                LEFT JOIN vendor_info ven ON ven.vendor_id = dp.vendor_id
                WHERE (dp.status = 'Active' OR (dp.status != 'Delete' AND dp.status != 'Deleted')) AND dp.invoice_date >= '$esc_from' AND dp.invoice_date <= '$esc_to'

                UNION ALL

                SELECT 
                    lp.local_purchase_bill_id AS id,
                    lp.invoice_date AS transaction_date,
                    'Outward' AS transaction_type,
                    NULL AS ac_type,
                    NULL AS bank_id,
                    NULL AS cash_category_id,
                    NULL AS cash_category_name,
                    NULL AS account_head_id,
                    NULL AS category_id,
                    lp.tot_amt_with_tax AS amount,
                    CONCAT('Local Bill No: ', IFNULL(lp.invoice_no, ''), IF(ven.vendor_name IS NOT NULL AND ven.vendor_name != '', CONCAT(' | Vendor: ', ven.vendor_name), ''), IF(lp.remarks IS NOT NULL AND lp.remarks != '', CONCAT(' | ', lp.remarks), '')) AS remarks,
                    IFNULL(lp.created_date, lp.invoice_date) AS created_at,
                    'Purchase Expense' AS category_name,
                    'Local Purchase Bill' AS account_head_name,
                    'Local Purchase Bill' AS source_type,
                    NULL AS bank_name
                FROM local_purchase_bill_info lp
                LEFT JOIN vendor_info ven ON ven.vendor_id = lp.vendor_id
                WHERE (lp.status = 'Active' OR (lp.status != 'Delete' AND lp.status != 'Deleted')) AND lp.invoice_date >= '$esc_from' AND lp.invoice_date <= '$esc_to'
            ) AS combined_history
            ORDER BY transaction_date DESC, id DESC
        ";

        $data['history'] = $this->db->query($union_sql)->result_array();

        // Fetch all active account heads
        $all_heads_query = $this->db->query("
            SELECT account_head_id, account_head_name, type 
            FROM cb_account_head_info 
            WHERE status = 'Active'
            ORDER BY account_head_name ASC
        ");
        $data['account_heads'] = $all_heads_query->result_array();

        // Fetch active banks
        $banks_query = $this->db->query("
            SELECT bank_id, bank_name 
            FROM company_bank_info 
            WHERE status = 'Active'
            ORDER BY bank_name ASC
        ");
        $data['bank_opt'] = [];
        foreach ($banks_query->result_array() as $row) {
            $data['bank_opt'][$row['bank_id']] = $row['bank_name'];
        }

        $data['cash_categories'] = [];
        $sql = "
            SELECT cash_category_id, category_name
            FROM cash_category
            WHERE status = 'Active'
            AND category_type IN ('Inward', 'Outward', 'Both', 'Cash')
            ORDER BY category_name ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['cash_categories'][$row['cash_category_id']] = $row['category_name'];
        }

        $this->load->view('page/accounts/petty-cash', $data);
    }

    public function add_funds()
    {
        $amount = $this->input->post('amount');
        $date = $this->input->post('transaction_date');
        $ac_type = $this->input->post('ac_type');
        $bank_id = $ac_type == 'Bank' ? $this->input->post('bank_id') : null;
        $cash_category_id = $ac_type == 'Cash' ? ($this->input->post('cash_category_id') ?: null) : null;
        $account_head_id = $this->input->post('account_head_id');
        $category_id = $this->input->post('category_id');
        $remarks = $this->input->post('remarks');

        if ($amount > 0) {
            $this->db->insert('petty_cash_transactions', [
                'transaction_date' => $date,
                'transaction_type' => 'Inward',
                'ac_type' => $ac_type,
                'bank_id' => $bank_id,
                'cash_category_id' => $cash_category_id,
                'account_head_id' => $account_head_id,
                'category_id' => $category_id,
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
        $cash_category_id = $this->input->post('cash_category_id') ?: null;
        $account_head_id = $this->input->post('account_head_id');
        $category_id = $this->input->post('category_id');
        $remarks = $this->input->post('remarks');

        if ($amount > 0) {
            $this->db->insert('petty_cash_transactions', [
                'transaction_date' => $date,
                'transaction_type' => 'Outward', 
                'ac_type' => 'Cash',
                'cash_category_id' => $cash_category_id,
                'account_head_id' => $account_head_id,
                'category_id' => $category_id,
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
        $ac_type = $this->input->post('ac_type');
        $bank_id = $ac_type == 'Bank' ? $this->input->post('bank_id') : null;
        $cash_category_id = $ac_type == 'Cash' ? ($this->input->post('cash_category_id') ?: null) : null;
        $account_head_id = $this->input->post('account_head_id');
        $category_id = $this->input->post('category_id');
        $remarks = $this->input->post('remarks');

        $update_data = [
            'transaction_date' => $date,
            'ac_type' => $ac_type,
            'bank_id' => $bank_id,
            'cash_category_id' => $cash_category_id,
            'account_head_id' => $account_head_id,
            'category_id' => $category_id,
            'amount' => $amount,
            'remarks' => $remarks
        ]; 
        $this->db->where('id', $id);
        $this->db->update('petty_cash_transactions', $update_data);

        $this->session->set_flashdata('success', 'Transaction updated successfully');
        redirect('petty-cash');
    }

    public function get_sub_accounts()
    {
        $account_head_id = $this->input->post('account_head_id');
        $query = $this->db->query("
            SELECT sub_account_head_id, sub_account_head_name 
            FROM cb_sub_account_head_info 
            WHERE account_head_id = '" . $this->db->escape_str($account_head_id) . "' AND status = 'Active'
            ORDER BY sub_account_head_name ASC
        ");
        
        echo '<option value="">Select Sub Account</option>';
        foreach ($query->result_array() as $row) {
            echo '<option value="' . $row['sub_account_head_id'] . '">' . htmlspecialchars($row['sub_account_head_name']) . '</option>';
        }
        exit;
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

    public function petty_cash_statement()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');
            $data['srch_cash_category_id'] = $srch_cash_category_id = $this->input->post('srch_cash_category_id');
        } else {
            $data['srch_from_date'] = $srch_from_date = date('Y-m-') . '01';
            $data['srch_to_date'] = $srch_to_date = date('Y-m-d');
            $data['srch_cash_category_id'] = $srch_cash_category_id = '';
        }

        // Build where conditions: enforce Cash only
        $w_tr = "tr.status = 'Active' AND tr.receipt_mode = 'Cash'";
        $w_vp = "vp.status = 'Active' AND vp.payment_mode = 'Cash'";
        $w_vap = "vap.status = 'Active' AND vap.ac_type_opt = 'Cash'";
        $w_pt = "pt.status != 'Deleted' AND pt.ac_type = 'Cash'";
        $w_cin = "cin.status = 'Active' AND cin.ac_type = 'Cash'";
        $w_cout = "cout.status = 'Active' AND cout.ac_type = 'Cash'";
        $w_op = "status != 'Delete' AND ac_type = 'Cash'";

        $w_ce_to = "1=1";
        $w_ce_from = "1=1";
        if (!empty($srch_cash_category_id)) {
            $w_tr .= " AND tr.cash_category_id = " . (int)$srch_cash_category_id;
            $w_vp .= " AND vp.cash_category_id = " . (int)$srch_cash_category_id;
            $w_vap .= " AND vap.cash_category_id = " . (int)$srch_cash_category_id;
            $w_pt .= " AND pt.cash_category_id = " . (int)$srch_cash_category_id;
            $w_cin .= " AND cin.cash_category_id = " . (int)$srch_cash_category_id;
            $w_cout .= " AND cout.cash_category_id = " . (int)$srch_cash_category_id;
            $w_ce_to = "ce.to_cash_category_id = " . (int)$srch_cash_category_id;
            $w_ce_from = "ce.from_cash_category_id = " . (int)$srch_cash_category_id;
        }

        // 1. Calculate Opening Balance before srch_from_date
        // A. Base Opening Balances from cb_opening_balance_info
        $sql_cb_op = "SELECT COALESCE(SUM(amount), 0) AS op_amount FROM cb_opening_balance_info WHERE $w_op AND opening_date < '" . $this->db->escape_str($srch_from_date) . "'";
        $cb_op = (float)$this->db->query($sql_cb_op)->row()->op_amount;

        // B. Combined transactions before srch_from_date
        $sql_tr_before = "
            SELECT COALESCE(SUM(amount_in), 0) AS total_in, COALESCE(SUM(amount_out), 0) AS total_out
            FROM (
                SELECT receipt_date AS tr_date, amount AS amount_in, 0 AS amount_out FROM tender_receipt_info tr WHERE $w_tr
                UNION ALL
                SELECT payment_date AS tr_date, 0 AS amount_in, amount AS amount_out FROM vendor_payment_info vp WHERE $w_vp
                UNION ALL
                SELECT adv_payment_date AS tr_date, 0 AS amount_in, adv_payment_amt AS amount_out FROM vendor_advance_payment_info vap WHERE $w_vap
                UNION ALL
                SELECT transaction_date AS tr_date, amount AS amount_in, 0 AS amount_out FROM petty_cash_transactions pt WHERE $w_pt AND pt.transaction_type IN ('Inward', 'Income')
                UNION ALL
                SELECT transaction_date AS tr_date, 0 AS amount_in, amount AS amount_out FROM petty_cash_transactions pt WHERE $w_pt AND pt.transaction_type IN ('Outward', 'Cash', 'Expense')
                UNION ALL
                SELECT inward_date AS tr_date, amount AS amount_in, 0 AS amount_out FROM cb_cash_inward_info cin WHERE $w_cin
                UNION ALL
                SELECT outward_date AS tr_date, 0 AS amount_in, amount AS amount_out FROM cb_cash_outward_info cout WHERE $w_cout
                UNION ALL
                SELECT entry_date AS tr_date, amount AS amount_in, 0 AS amount_out FROM cb_contra_entry_info ce WHERE ce.status = 'Active' AND ce.to_ac_type = 'Cash' AND $w_ce_to
                UNION ALL
                SELECT entry_date AS tr_date, 0 AS amount_in, amount AS amount_out FROM cb_contra_entry_info ce WHERE ce.status = 'Active' AND ce.from_ac_type = 'Cash' AND $w_ce_from
            ) t
            WHERE t.tr_date < '" . $this->db->escape_str($srch_from_date) . "'
        ";
        $tr_before_res = $this->db->query($sql_tr_before)->row_array();
        $total_tr_in_before = (float)$tr_before_res['total_in'];
        $total_tr_out_before = (float)$tr_before_res['total_out'];

        $data['opening_balance'] = $cb_op + $total_tr_in_before - $total_tr_out_before;

        // 2. Fetch main transactions in range
        $sql_tr = "
            SELECT * FROM (
                -- Tender Receipts (Customer Receipts)
                SELECT 
                    'Customer Receipt' AS tr_type, 
                    tr.receipt_no AS ref_no, 
                    tr.receipt_date AS tr_date, 
                    tr.amount AS amount_in, 
                    0 AS amount_out, 
                    tr.receipt_mode AS mode, 
                    cc.category_name AS cash_category_name,
                    c.customer_name AS party_name, 
                    tr.remarks,
                    tr.created_date
                FROM tender_receipt_info tr
                LEFT JOIN customer_info c ON c.customer_id = tr.customer_id
                LEFT JOIN cash_category cc ON cc.cash_category_id = tr.cash_category_id AND cc.status = 'Active'
                WHERE $w_tr AND tr.receipt_date BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "'

                UNION ALL

                -- Vendor Payments (Supplier Payments)
                SELECT 
                    'Supplier Payment' AS tr_type, 
                    vp.payment_no AS ref_no, 
                    vp.payment_date AS tr_date, 
                    0 AS amount_in, 
                    vp.amount AS amount_out, 
                    vp.payment_mode AS mode, 
                    cc.category_name AS cash_category_name,
                    v.vendor_name AS party_name, 
                    vp.remarks,
                    vp.created_date
                FROM vendor_payment_info vp
                LEFT JOIN vendor_info v ON v.vendor_id = vp.vendor_id
                LEFT JOIN cash_category cc ON cc.cash_category_id = vp.cash_category_id AND cc.status = 'Active'
                WHERE $w_vp AND vp.payment_date BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "'

                UNION ALL

                -- Vendor Advance Payments (Supplier Advance Payments)
                SELECT 
                    'Supplier Adv Payment' AS tr_type, 
                    '' AS ref_no, 
                    vap.adv_payment_date AS tr_date, 
                    0 AS amount_in, 
                    vap.adv_payment_amt AS amount_out, 
                    vap.ac_type_opt AS mode, 
                    cc.category_name AS cash_category_name,
                    v.vendor_name AS party_name, 
                    'Advance Payment' AS remarks,
                    vap.created_date
                FROM vendor_advance_payment_info vap
                LEFT JOIN vendor_info v ON v.vendor_id = vap.vendor_id
                LEFT JOIN cash_category cc ON cc.cash_category_id = vap.cash_category_id AND cc.status = 'Active'
                WHERE $w_vap AND vap.adv_payment_date BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "'

                UNION ALL

                -- Petty Cash Inward
                SELECT 
                    'Petty Cash Inward' AS tr_type, 
                    '' AS ref_no, 
                    pt.transaction_date AS tr_date, 
                    pt.amount AS amount_in, 
                    0 AS amount_out, 
                    pt.ac_type AS mode, 
                    cc.category_name AS cash_category_name,
                    'Petty Cash' AS party_name, 
                    pt.remarks,
                    pt.created_at AS created_date
                FROM petty_cash_transactions pt
                LEFT JOIN cash_category cc ON cc.cash_category_id = pt.cash_category_id AND cc.status = 'Active'
                WHERE $w_pt AND pt.transaction_type IN ('Inward', 'Income') AND pt.transaction_date BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "'

                UNION ALL

                -- Petty Cash Outward
                SELECT 
                    'Petty Cash Outward' AS tr_type, 
                    '' AS ref_no, 
                    pt.transaction_date AS tr_date, 
                    0 AS amount_in, 
                    pt.amount AS amount_out, 
                    pt.ac_type AS mode, 
                    cc.category_name AS cash_category_name,
                    'Petty Cash' AS party_name, 
                    pt.remarks,
                    pt.created_at AS created_date
                FROM petty_cash_transactions pt
                LEFT JOIN cash_category cc ON cc.cash_category_id = pt.cash_category_id AND cc.status = 'Active'
                WHERE $w_pt AND pt.transaction_type IN ('Outward', 'Cash', 'Expense') AND pt.transaction_date BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "'

                UNION ALL

                -- Cash Inward Entry
                SELECT 
                    'Cash Inward' AS tr_type, 
                    cin.vno AS ref_no, 
                    cin.inward_date AS tr_date, 
                    cin.amount AS amount_in, 
                    0 AS amount_out, 
                    cin.ac_type AS mode, 
                    cc.category_name AS cash_category_name,
                    COALESCE(e.sub_account_headlvl3_name, sh.sub_account_head_name, ah.account_head_name) AS party_name, 
                    cin.remarks,
                    cin.created_datetime AS created_date
                FROM cb_cash_inward_info cin
                LEFT JOIN cb_account_head_info ah ON ah.account_head_id = cin.account_head_id
                LEFT JOIN cb_sub_account_head_info sh ON sh.sub_account_head_id = cin.sub_account_head_id
                LEFT JOIN cb_sub_account_head_lvl3_info e ON e.sub_account_headlvl3_id = cin.sub_account_headlvl3_id
                LEFT JOIN cash_category cc ON cc.cash_category_id = cin.cash_category_id AND cc.status = 'Active'
                WHERE $w_cin AND cin.inward_date BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "'

                UNION ALL

                -- Cash Outward Entry
                SELECT 
                    'Cash Outward' AS tr_type, 
                    CONCAT(vt.prefix, cout.vno) AS ref_no, 
                    cout.outward_date AS tr_date, 
                    0 AS amount_in, 
                    cout.amount AS amount_out, 
                    cout.ac_type AS mode, 
                    cc.category_name AS cash_category_name,
                    COALESCE(e.sub_account_headlvl3_name, sh.sub_account_head_name, ah.account_head_name) AS party_name, 
                    cout.remarks,
                    cout.created_datetime AS created_date
                FROM cb_cash_outward_info cout
                LEFT JOIN cb_account_head_info ah ON ah.account_head_id = cout.account_head_id
                LEFT JOIN cb_sub_account_head_info sh ON sh.sub_account_head_id = cout.sub_account_head_id
                LEFT JOIN cb_sub_account_head_lvl3_info e ON e.sub_account_headlvl3_id = cout.sub_account_headlvl3_id
                LEFT JOIN cb_voucher_type_info vt ON vt.voucher_type_id = cout.voucher_type_id
                LEFT JOIN cash_category cc ON cc.cash_category_id = cout.cash_category_id AND cc.status = 'Active'
                WHERE $w_cout AND cout.outward_date BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "'

                UNION ALL

                -- Contra Entry Inward (To Cash)
                SELECT 
                    'Contra Inward' AS tr_type, 
                    '' AS ref_no, 
                    ce.entry_date AS tr_date, 
                    ce.amount AS amount_in, 
                    0 AS amount_out, 
                    'Cash' AS mode, 
                    cc.category_name AS cash_category_name,
                    IF(ce.from_ac_type = 'Cash', CONCAT('Cash (', fcc.category_name, ')'), fb.bank_name) AS party_name, 
                    ce.to_remarks AS remarks,
                    ce.created_at AS created_date
                FROM cb_contra_entry_info ce
                LEFT JOIN company_bank_info fb ON fb.bank_id = ce.from_bank_id
                LEFT JOIN cash_category cc ON cc.cash_category_id = ce.to_cash_category_id AND cc.status = 'Active'
                LEFT JOIN cash_category fcc ON fcc.cash_category_id = ce.from_cash_category_id AND fcc.status = 'Active'
                WHERE ce.status = 'Active' AND ce.to_ac_type = 'Cash' AND $w_ce_to AND ce.entry_date BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "'

                UNION ALL

                -- Contra Entry Outward (From Cash)
                SELECT 
                    'Contra Outward' AS tr_type, 
                    '' AS ref_no, 
                    ce.entry_date AS tr_date, 
                    0 AS amount_in, 
                    ce.amount AS amount_out, 
                    'Cash' AS mode, 
                    cc.category_name AS cash_category_name,
                    IF(ce.to_ac_type = 'Cash', CONCAT('Cash (', tcc.category_name, ')'), tb.bank_name) AS party_name, 
                    ce.from_remarks AS remarks,
                    ce.created_at AS created_date
                FROM cb_contra_entry_info ce
                LEFT JOIN company_bank_info tb ON tb.bank_id = ce.to_bank_id
                LEFT JOIN cash_category cc ON cc.cash_category_id = ce.from_cash_category_id AND cc.status = 'Active'
                LEFT JOIN cash_category tcc ON tcc.cash_category_id = ce.to_cash_category_id AND tcc.status = 'Active'
                WHERE ce.status = 'Active' AND ce.from_ac_type = 'Cash' AND $w_ce_from AND ce.entry_date BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "'
            ) t
            ORDER BY t.tr_date ASC, t.created_date ASC
        ";
        $data['records'] = $this->db->query($sql_tr)->result_array();

        // Load cash categories list
        $data['cash_categories_opt'] = ['' => 'All Cash Categories'];
        $sql = "
            SELECT cash_category_id, category_name
            FROM cash_category
            WHERE status = 'Active'
            AND category_type IN ('Inward', 'Outward', 'Both', 'Cash')
            ORDER BY category_name ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['cash_categories_opt'][$row['cash_category_id']] = $row['category_name'];
        }

        $data['js'] = 'accounts/petty-cash-statement.inc';

        $this->load->view('page/accounts/petty-cash-statement', $data);
    }
}
