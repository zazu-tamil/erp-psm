<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tender extends CI_Controller
{

    public function add_tender_enquiry()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        $data = array();
        $data['js'] = 'tender/add-tender-enquiry.inc';
        $data['title'] = 'Tender Enquiry';

        $srch_company_id = $this->input->post('company_id');
        $srch_customer_id = $this->input->post('customer_id');

        // Handle Add Tender Enquiry with Items
        if ($this->input->post('mode') == 'Add') {
            $this->db->trans_start();

            // Insert main record
            $insert_data = array(
                'company_id' => $srch_company_id,
                'enquiry_date' => $this->input->post('enquiry_date'),
                'enquiry_no' => $this->input->post('enquiry_no'),
                'customer_id' => $srch_customer_id,
                'opening_date' => $this->input->post('opening_date') ? date('Y-m-d H:i:s', strtotime($this->input->post('opening_date'))) : null,
                'closing_date' => $this->input->post('closing_date') ? date('Y-m-d H:i:s', strtotime($this->input->post('closing_date'))) : null,
                'status' => $this->input->post('status') ?: 'Active',
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s')
            );
            $this->db->insert('tender_enquiry_info', $insert_data);
            $tender_enquiry_id = $this->db->insert_id();

            // Insert item records
            $category_ids = $this->input->post('category_id');
            $item_ids = $this->input->post('item_id');
            $item_descs = $this->input->post('item_desc');
            $uoms = $this->input->post('uom');
            $qtys = $this->input->post('qty');

            if (!empty($item_ids)) {
                foreach ($item_ids as $index => $item_id) {
                    if (!empty($item_id)) {
                        $insert_item_data = array(
                            'tender_enquiry_id' => $tender_enquiry_id,
                            'category_id' => $category_ids[$index] ?? 0,
                            'item_id' => $item_id,
                            'item_desc' => $item_descs[$index] ?? '',
                            'uom' => $uoms[$index] ?? '',
                            'qty' => $qtys[$index] ?? 0.00,
                            'status' => 'Active',
                            'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                            'created_date' => date('Y-m-d H:i:s')
                        );
                        $this->db->insert('tender_enquiry_item_info', $insert_item_data);
                    }
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error saving data. Please try again.');
            } else {
                $this->session->set_flashdata('success', 'Tender Enquiry saved successfully.');
            }
            redirect('tender-enquiry-list');
        }

        // Populate dropdowns
        $data['company_opt'] = [];
        $data['customer_opt'] = [];
        $data['category_opt'] = [];
        $data['item_opt'] = [];
        $data['uom_opt'] = [];

        // Companies
        $sql = "
        SELECT 
        company_id, 
        company_name 
        FROM company_info 
        WHERE status = 'Active' 
        ORDER BY company_name ASC";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }


           $sql = "
        SELECT 
        customer_id, 
        customer_name 
        FROM customer_info 
        WHERE status = 'Active' 
        ORDER BY customer_name ASC";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }

        // Categories
        $sql = "
        SELECT 
        category_id, 
        category_name 
        FROM category_info 
        WHERE status = 'Active' 
        ORDER BY category_name ASC";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['category_opt'][$row['category_id']] = $row['category_name'];
        }

        // Items
        $sql = "
        SELECT 
        item_id, 
        item_name 
        FROM item_info 
        WHERE status = 'Active' 
        ORDER BY item_name ASC";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['item_opt'][$row['item_id']] = $row['item_name'];
        }

        // UOM
        $sql = "
        SELECT 
        uom_id, 
        uom_name 
        FROM uom_info 
        WHERE status = 'Active' 
        ORDER BY uom_name ASC";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['uom_opt'][$row['uom_id']] = $row['uom_name'];
        }

        $data['status_opt'] = ['Active' => 'Active', 'Inactive' => 'Inactive'];


        $this->load->view('page/tender/add-tender-enquiry', $data);
    }


    public function get_data()
    {
        $table = $this->input->post('tbl');
        $rec_id = $this->input->post('id');

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $rec_list = array();

        if ($table == 'get-company-customer-list') {
            $query = $this->db->query(" 
                SELECT 
                c.customer_id,
                c.customer_name
                FROM project_info AS p
                LEFT JOIN customer_info AS c ON c.customer_id = p.customer_id
                WHERE p.company_id = ? 
                AND p.status = 'Active'
                AND c.status = 'Active'
                GROUP BY c.customer_id
                ORDER BY c.customer_name ASC
            ", [$rec_id]);

            $rec_list = $query->result_array();
        }

        if ($table == 'get-category-item-list') {
            $query = $this->db->query(" 
                SELECT
                a.item_id, 
                a.item_name 
                FROM item_info AS a
                WHERE a.category_id = ? 
                AND a.`status` = 'Active'
                ORDER BY a.item_name 
            ", [$rec_id]);

            $rec_list = $query->result_array();
        }


           if ($table == 'get-uom-desc-from-item') {
            $query = $this->db->query(" 
                SELECT
                a.item_id,
                a.uom,
                a.item_description,
                a.item_name 
                FROM item_info AS a
                WHERE a.item_id = ? 
                AND a.`status` = 'Active'
                ORDER BY a.item_name 
            ", [$rec_id]);

            $rec_list = $query->result_array();
        }

        header('Content-Type: application/x-json; charset=utf-8');
        echo json_encode($rec_list);
    }
}