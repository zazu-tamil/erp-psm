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
                  `transaction_type` enum('Inward','Outward') NOT NULL,
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

        // Get total inwards
        if (!$this->db->field_exists('status', 'petty_cash_transactions')) {
            $this->db->query("ALTER TABLE petty_cash_transactions ADD COLUMN status VARCHAR(20) DEFAULT 'Active'");
        }

        $this->db->select_sum('amount');
        $this->db->where('transaction_type', 'Inward');
        $this->db->where('status !=', 'Deleted');
        $inward_query = $this->db->get('petty_cash_transactions')->row();
        $total_inward = $inward_query->amount ? $inward_query->amount : 0;

        // Get total outwards
        $this->db->select_sum('amount');
        $this->db->where('transaction_type', 'Outward');
        $this->db->where('status !=', 'Deleted');
        $outward_query = $this->db->get('petty_cash_transactions')->row();
        $total_outward = $outward_query->amount ? $outward_query->amount : 0;

        $data['balance'] = $total_inward - $total_outward;
        $data['total_inward'] = $total_inward;
        $data['total_outward'] = $total_outward;

        // Get this month's start and end date
        $start_of_month = date('Y-m-01');
        $end_of_month = date('Y-m-t');

        // Total Inward this month
        $this->db->select_sum('amount');
        $this->db->where('transaction_type', 'Inward');
        $this->db->where('status !=', 'Deleted');
        $this->db->where('transaction_date >=', $start_of_month);
        $this->db->where('transaction_date <=', $end_of_month);
        $this_month_inward_query = $this->db->get('petty_cash_transactions')->row();
        $data['this_month_inward'] = $this_month_inward_query->amount ? $this_month_inward_query->amount : 0;

        // Total Outward this month
        $this->db->select_sum('amount');
        $this->db->where('transaction_type', 'Outward');
        $this->db->where('status !=', 'Deleted');
        $this->db->where('transaction_date >=', $start_of_month);
        $this->db->where('transaction_date <=', $end_of_month);
        $this_month_outward_query = $this->db->get('petty_cash_transactions')->row();
        $data['this_month_outward'] = $this_month_outward_query->amount ? $this_month_outward_query->amount : 0;

        // Total Transactions this month
        $this->db->where('status !=', 'Deleted');
        $this->db->where('transaction_date >=', $start_of_month);
        $this->db->where('transaction_date <=', $end_of_month);
        $data['this_month_transactions'] = $this->db->count_all_results('petty_cash_transactions');

        // Expense Summary (Grouped by Sub Account Head for This Month)
        $this->db->select('c.sub_account_head_name as category_name, SUM(t.amount) as total_amount');
        $this->db->from('petty_cash_transactions t');
        $this->db->join('cb_sub_account_head_info c', 'c.sub_account_head_id = t.category_id', 'left');
        $this->db->where('t.transaction_type', 'Outward');
        $this->db->where('t.status !=', 'Deleted');
        $this->db->where('t.transaction_date >=', $start_of_month);
        $this->db->where('t.transaction_date <=', $end_of_month);
        $this->db->group_by('t.category_id');
        $this->db->order_by('total_amount', 'DESC');
        $data['expense_summary'] = $this->db->get()->result_array();

        // Get all active account heads for the dropdown (Filtered to Petty Cash Expenses only)
        $this->db->where('status', 'Active');
        $this->db->like('account_head_name', 'Petty Cash Expenses');
        $this->db->order_by('account_head_name', 'ASC');
        $data['account_heads'] = $this->db->get('cb_account_head_info')->result_array();

        // Get recent history
        $this->db->select('t.*, c.sub_account_head_name as category_name, a.account_head_name');
        $this->db->from('petty_cash_transactions t');
        $this->db->join('cb_sub_account_head_info c', 'c.sub_account_head_id = t.category_id', 'left');
        $this->db->join('cb_account_head_info a', 'a.account_head_id = t.account_head_id', 'left');
        $this->db->where('t.status !=', 'Deleted');
        $this->db->order_by('t.transaction_date', 'DESC');
        $this->db->order_by('t.id', 'DESC');
        $data['history'] = $this->db->get()->result_array();

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
            $pc_id = $this->db->insert_id();
            // Get Account Head ID for 'Petty Cash'
            $ah_query = $this->db->get_where('cb_account_head_info', ['account_head_name' => 'Petty Cash']);
            $ah_id = $ah_query->num_rows() > 0 ? $ah_query->row()->account_head_id : 0;

            //outward
            $this->db->insert('cb_cash_outward_info', [
                'franchise_id' => ($this->session->userdata('cr_franchise_id') == '' ? 0 : $this->session->userdata('cr_franchise_id')),
                'account_head_id' => $ah_id,
                'sub_account_head_id' => 0,
                'ac_type' => 'Petty Cash',
                'outward_date' => $date,
                'amount' => $amount,
                'remarks' => 'Transfer to Petty Cash: ' . $remarks,
                'status' => 'Active',
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_datetime' => date('Y-m-d H:i:s')
            ]);
            $outward_id = $this->db->insert_id();

          
           
            $this->db->insert('cb_cash_inward_info', [
                'franchise_id' => ($this->session->userdata('cr_franchise_id') == '' ? 0 : $this->session->userdata('cr_franchise_id')),
                'account_head_id' => $ah_id,
                'ac_type' => 'Petty Cash',
                'inward_date' => $date,
                'amount' => $amount,
                'remarks' => 'Transfer from Main Cash: ' . $remarks,
                'status' => 'Active',
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_datetime' => date('Y-m-d H:i:s')
            ]);
            $inward_id = $this->db->insert_id();
            // Link them
            $this->db->where('id', $pc_id);
            $this->db->update('petty_cash_transactions',['ref_outward_id' => $outward_id,'ref_inward_id' => $inward_id]);           
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
                'account_head_id' => $account_head_id,
                'category_id' => $category_id,
                'amount' => $amount,
                'remarks' => $remarks,
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $pc_id = $this->db->insert_id();
            
            // Fetch category name for outward remarks
            $cat_name = $this->db->get_where('cb_sub_account_head_info', ['sub_account_head_id' => $category_id])->row()->sub_account_head_name;

            // Insert into cb_cash_outward_info
            $this->db->insert('cb_cash_outward_info', [
                'franchise_id' => ($this->session->userdata('cr_franchise_id') == '' ? 0 : $this->session->userdata('cr_franchise_id')),
                'account_head_id' => $account_head_id,
                'sub_account_head_id' => $category_id,
                'ac_type' => 'Petty Cash',
                'outward_date' => $date,
                'amount' => $amount,
                'remarks' => 'Petty Cash Expense (' . $cat_name . '): ' . $remarks,
                'status' => 'Active',
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_datetime' => date('Y-m-d H:i:s')
            ]);
            $outward_id = $this->db->insert_id();

            // Link them
            $this->db->where('id', $pc_id);
            $this->db->update('petty_cash_transactions', ['ref_outward_id' => $outward_id]);
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

        if ($transaction_type === 'Outward') {
            $update_data['account_head_id'] = $this->input->post('account_head_id');
            $update_data['category_id'] = $this->input->post('category_id');
        }

        $this->db->where('id', $id);
        $this->db->update('petty_cash_transactions', $update_data);

        // Sync with inward/outward info if mapping exists
        if ($txn) {
            if ($transaction_type === 'Inward') {
                $inward_remarks = 'Transfer from Main Cash: ' . $remarks;
                $outward_remarks = 'Transfer to Petty Cash: ' . $remarks;

                if (!empty($txn->ref_inward_id)) {
                    $this->db->where('cash_inward_id', $txn->ref_inward_id);
                    $this->db->update('cb_cash_inward_info', [
                        'inward_date' => $date,
                        'amount' => $amount,
                        'remarks' => $inward_remarks,
                        'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                        'updated_datetime' => date('Y-m-d H:i:s')
                    ]);
                }
                
                if (!empty($txn->ref_outward_id)) {
                    $this->db->where('cash_outward_id', $txn->ref_outward_id);
                    $this->db->update('cb_cash_outward_info', [
                        'outward_date' => $date,
                        'amount' => $amount,
                        'remarks' => $outward_remarks,
                        'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                        'updated_datetime' => date('Y-m-d H:i:s')
                    ]);
                }
            } else if ($transaction_type === 'Outward' && !empty($txn->ref_outward_id)) {
                $cat_name = $this->db->get_where('cb_sub_account_head_info', ['sub_account_head_id' => $update_data['category_id']])->row()->sub_account_head_name;
                $outward_remarks = 'Petty Cash Expense (' . $cat_name . '): ' . $remarks;

                $this->db->where('cash_outward_id', $txn->ref_outward_id);
                $this->db->update('cb_cash_outward_info', [
                    'account_head_id' => $update_data['account_head_id'],
                    'sub_account_head_id' => $update_data['category_id'],
                    'outward_date' => $date,
                    'amount' => $amount,
                    'remarks' => $outward_remarks,
                    'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                    'updated_datetime' => date('Y-m-d H:i:s')
                ]);
            }
        }

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
            // Soft delete associated inward/outward records
            if (!empty($txn->ref_inward_id)) {
                $this->db->where('cash_inward_id', $txn->ref_inward_id);
                $this->db->update('cb_cash_inward_info', ['status' => 'Deleted']);
            }
            if (!empty($txn->ref_outward_id)) {
                $this->db->where('cash_outward_id', $txn->ref_outward_id);
                $this->db->update('cb_cash_outward_info', ['status' => 'Deleted']);
            }

            // Soft delete the petty cash transaction
            if (!$this->db->field_exists('status', 'petty_cash_transactions')) {
                $this->db->query("ALTER TABLE petty_cash_transactions ADD COLUMN status VARCHAR(20) DEFAULT 'Active'");
            }
            $this->db->where('id', $id);
            $this->db->update('petty_cash_transactions', ['status' => 'Deleted']);

            $this->session->set_flashdata('success', 'Transaction soft deleted successfully');
        }

        redirect('petty-cash');
    }

    public function get_sub_accounts()
    {
        $account_head_id = $this->input->post('account_head_id');
        $this->db->where('account_head_id', $account_head_id);
        $this->db->where('status', 'Active');
        $this->db->order_by('sub_account_head_name', 'ASC');
        $sub_accounts = $this->db->get('cb_sub_account_head_info')->result_array();
        
        $options = '<option value="">Select Sub Account Head</option>';
        foreach ($sub_accounts as $sa) {
            $options .= '<option value="' . $sa['sub_account_head_id'] . '">' . htmlspecialchars($sa['sub_account_head_name']) . '</option>';
        }
        echo $options;
    }
}
