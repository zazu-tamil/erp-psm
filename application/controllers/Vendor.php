<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Vendor extends CI_Controller
{

    public function vendor_rate_enquiry()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if ($this->session->userdata(SESS_HD . 'level') != 'Admin' && $this->session->userdata(SESS_HD . 'level') != 'Staff') {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'vendor/vendor-rate-enquiry.inc';
        $data['title'] = 'Vendor Rate Enquiry';

        if ($this->input->post('mode') == 'Add') {
            $this->db->trans_start();

            $insert_data = array(
                'customer_id' => $this->input->post('srch_customer_id'),
                'tender_enquiry_id' => $this->input->post('srch_tender_enquiry_id'),
                'vendor_id' => $this->input->post('srch_vendor_id'),
                'enquiry_date' => $this->input->post('enquiry_date') ? date('Y-m-d H:i:s', strtotime($this->input->post('enquiry_date'))) : null,
                'opening_date' => $this->input->post('opening_date') ? date('Y-m-d H:i:s', strtotime($this->input->post('opening_date'))) : null,
                'closing_date' => $this->input->post('closing_date') ? date('Y-m-d H:i:s', strtotime($this->input->post('closing_date'))) : null,
                'enquiry_no' => $this->input->post('enquiry_no'),
                'status' => $this->input->post('status') ?: 'Active',
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s')
            );
            $this->db->insert('vendor_rate_enquiry_info', $insert_data);
            $vendor_rate_enquiry_id = $this->db->insert_id();

            $selected_items = $this->input->post('selected_items');
            $tender_enquiry_item_id = $this->input->post('tender_enquiry_item_id');
            $category_ids = $this->input->post('category_id');
            $item_ids = $this->input->post('item_id');
            $item_descs = $this->input->post('item_desc');
            $uoms = $this->input->post('uom');
            $qtys = $this->input->post('qty');

            if (!empty($selected_items)) {
                foreach ($selected_items as $index) {
                    if (!empty($item_ids[$index])) {
                        $insert_item_data = array(
                            'vendor_rate_enquiry_id' => $vendor_rate_enquiry_id,
                            'tender_enquiry_item_id' => $tender_enquiry_item_id[$index] ?? 0,
                            'category_id' => $category_ids[$index] ?? 0,
                            'item_id' => $item_ids[$index],
                            'item_desc' => $item_descs[$index] ?? '',
                            'uom' => $uoms[$index] ?? '',
                            'qty' => $qtys[$index] ?? 0.00,
                            'status' => 'Active',
                            'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                            'created_date' => date('Y-m-d H:i:s')
                        );
                        $this->db->insert('vendor_rate_enquiry_item_info', $insert_item_data);
                    }
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error saving data. Please try again.');
            } else {
                $this->session->set_flashdata('success', 'Tender Enquiry saved successfully.');
            }
            redirect('vendor-rate-enquiry-list/');
        }

        $this->load->library('pagination');

        $this->db->where('status != ', 'Delete');
        $this->db->from('vendor_rate_enquiry_info');
        $data['total_records'] = $cnt = $this->db->count_all_results();

        $data['sno'] = $this->uri->segment(2, 0);

        $config['base_url'] = trim(site_url('vendor-rate-enquiry-list') . '/' . $this->uri->segment(2, 0));
        $config['total_rows'] = $cnt;
        $config['per_page'] = 50;
        $config['uri_segment'] = 2;
        $config['attributes'] = array('class' => 'page-link');
        $config['full_tag_open'] = '<ul class="pagination pagination-sm no-margin pull-right">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a href="#" class="page-link">';
        $config['cur_tag_close'] = '<span class="sr-only">(current)</span></a></li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['prev_link'] = "Prev";
        $config['next_link'] = "Next";
        $this->pagination->initialize($config);

        $data['customer_opt'] = [];
        $data['vendor_opt'] = [];
        $data['country_opt'] = [];

        $sql = "
          SELECT
                a.country_id,
                a.country_name
            FROM
                country_info AS a
            WHERE
                a.status != 'Delete'
            ORDER BY
                a.country_name ASC
         ";

        $query = $this->db->query($sql);
        $data['country_opt'] = array();
        foreach ($query->result_array() as $row) {
            $data['country_opt'][$row['country_name']] = $row['country_name'];
        }

        $sql = "
            SELECT vendor_id,vendor_name 
            FROM vendor_info 
            WHERE status = 'Active' 
            ORDER BY vendor_name ASC";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['vendor_opt'][$row['vendor_id']] = $row['vendor_name'];
        }

        $sql = "
            SELECT customer_id,customer_name
            FROM customer_info
            WHERE status = 'Active' 
            ORDER BY customer_name ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }

        $sql = "
            SELECT 
                a.tender_enquiry_id,
                a.enquiry_no,
                b.company_name,
                c.customer_name 
            FROM tender_enquiry_info AS a LEFT JOIN company_info as b on a.company_id = b.company_id and b.status='Active' 
            LEFT JOIN customer_info as c on a.customer_id = c.customer_id and c.status='Active' 
            WHERE a.status = 'Active' ORDER BY a.tender_enquiry_id , a.enquiry_no ASC
        ";
        $query = $this->db->query($sql);
        $data['tender_enquiry_opt'] = array();
        foreach ($query->result_array() as $row) {
            $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['tender_enquiry_id'] . ' -> ' . $row['enquiry_no'] . ' -> ' . $row['company_name'] . ' -> ' . $row['customer_name'];
        }

        $sql = "
            SELECT * FROM company_info 
            WHERE status != 'Delete' 
            order by company_id desc 
            limit " . $this->uri->segment(2, 0) . "," . $config['per_page']
        ;
        $data['record_list'] = array();
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['record_list'][] = $row;
        }

        $data['pagination'] = $this->pagination->create_links();
        $this->load->view('page/vendor/vendor-rate-enquiry', $data);
    }

    public function vendor_rate_enquiry_edit($id = 0)
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();
        if ($this->session->userdata(SESS_HD . 'level') != 'Admin' && $this->session->userdata(SESS_HD . 'level') != 'Staff') {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }
        if ($id <= 0)
            redirect('vendor-rate-enquiry-list');

        $data['js'] = 'vendor/vendor-rate-enquiry-edit.inc';
        $data['title'] = 'Edit Vendor Rate Enquiry';

        if ($this->input->post('mode') == 'Edit') {
            $this->db->trans_start();
            $update_data = array(
                'customer_id' => $this->input->post('srch_customer_id'),
                'tender_enquiry_id' => $this->input->post('srch_tender_enquiry_id'),
                'vendor_id' => $this->input->post('srch_vendor_id'),
                'enquiry_date' => $this->input->post('enquiry_date') ? date('Y-m-d H:i:s', strtotime($this->input->post('enquiry_date'))) : null,
                'opening_date' => $this->input->post('opening_date') ? date('Y-m-d H:i:s', strtotime($this->input->post('opening_date'))) : null,
                'closing_date' => $this->input->post('closing_date') ? date('Y-m-d H:i:s', strtotime($this->input->post('closing_date'))) : null,
                'enquiry_no' => $this->input->post('enquiry_no'),
                'status' => $this->input->post('status') ?: 'Active',
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s')
            );
            $this->db->where('vendor_rate_enquiry_id', $id);
            $this->db->update('vendor_rate_enquiry_info', $update_data);

            $this->db->where('vendor_rate_enquiry_id', $id);
            $this->db->delete('vendor_rate_enquiry_item_info');

            $tender_enquiry_item_ids = $this->input->post('tender_enquiry_item_id');
            $category_ids = $this->input->post('category_id');
            $item_ids = $this->input->post('item_id');
            $item_descs = $this->input->post('item_desc');
            $uoms = $this->input->post('uom');
            $qtys = $this->input->post('qty');

            if (!empty($item_ids) && is_array($item_ids)) {
                foreach ($item_ids as $index => $item_id) {
                    if (!empty($item_id)) {
                        $insert_item_data = array(
                            'vendor_rate_enquiry_id' => $id,
                            'tender_enquiry_item_id' => $tender_enquiry_item_ids[$index] ?? 0,
                            'category_id' => $category_ids[$index] ?? 0,
                            'item_id' => $item_id,
                            'item_desc' => $item_descs[$index] ?? '',
                            'uom' => $uoms[$index] ?? '',
                            'qty' => $qtys[$index] ?? 0.00,
                            'status' => 'Active',
                            'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                            'created_date' => date('Y-m-d H:i:s')
                        );
                        $this->db->insert('vendor_rate_enquiry_item_info', $insert_item_data);
                    }
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error updating data.');
            } else {
                $this->session->set_flashdata('success', 'Vendor Rate Enquiry updated successfully.');
            }
            redirect('vendor-rate-enquiry-list/');
        }

        // Load main record
        $sql = "
            SELECT * FROM 
            vendor_rate_enquiry_info 
            WHERE vendor_rate_enquiry_id = ? 
            AND status != 'Delete'
        ";
        $query = $this->db->query($sql, array($id));
        if ($query->num_rows() == 0)
            redirect('vendor-rate-enquiry-list');
        $data['main'] = $query->row_array();

        $data['customer_opt'] = [];
        $data['vendor_opt'] = [];
        $data['tender_enquiry_opt'] = [];

        $sql = "
            SELECT vendor_id, vendor_name 
            FROM vendor_info 
            WHERE status = 'Active' 
            ORDER BY vendor_name ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['vendor_opt'][$row['vendor_id']] = $row['vendor_name'];
        }

        $sql = "
             SELECT
                customer_id,
                customer_name
            FROM
                customer_info
            WHERE
            STATUS
                = 'Active'
            ORDER BY
                customer_name ASC 
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }

        $sql = "
            SELECT 
            a.tender_enquiry_id,
            a.enquiry_no, 
            b.company_name, 
            c.customer_name
            FROM tender_enquiry_info AS a
            LEFT JOIN company_info as b on a.company_id = b.company_id and b.status='Active'
            LEFT JOIN customer_info as c on a.customer_id = c.customer_id and c.status='Active'
            WHERE a.status = 'Active'
            ORDER BY a.tender_enquiry_id , a.enquiry_no ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['tender_enquiry_id'] . ' -> ' . $row['enquiry_no'] . ' -> ' . $row['company_name'] . ' -> ' . $row['customer_name'];
        }

        $sql = "
           SELECT
                a.*,
                b.category_name,
                c.item_name
            FROM
                vendor_rate_enquiry_item_info a
            LEFT JOIN category_info b ON
                a.category_id = b.category_id
            LEFT JOIN item_info c ON
                a.item_id = c.item_id
            WHERE
            a.status ='Active'
            and a.vendor_rate_enquiry_id = ?
            GROUP by a.vendor_rate_enquiry_id 
        ";
        $query = $this->db->query($sql, array($id));
        $data['item_rows'] = $query->result_array();

        $this->load->view('page/vendor/vendor-rate-enquiry-edit', $data);
    }



    public function vendor_rate_enquiry_print($vendor_rate_enquiry_id = 0)
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        if (!$vendor_rate_enquiry_id) {
            show_404();
        }

        // Fetch main record
        $sql = "
        SELECT 
            vrei.*,
            c.customer_name,
            v.vendor_name,
            v.mobile AS vendor_mobile,
            v.address AS vendor_address,
            te.enquiry_no AS tender_enquiry_no,
            ci.company_name AS our_company
        FROM vendor_rate_enquiry_info vrei
        LEFT JOIN customer_info c ON vrei.customer_id = c.customer_id
        LEFT JOIN vendor_info v ON vrei.vendor_id = v.vendor_id
        LEFT JOIN tender_enquiry_info te ON vrei.tender_enquiry_id = te.tender_enquiry_id
        LEFT JOIN company_info ci ON te.company_id = ci.company_id AND ci.status = 'Active'
        WHERE vrei.vendor_rate_enquiry_id = ? AND vrei.status != 'Delete'
    ";
        $query = $this->db->query($sql, [$vendor_rate_enquiry_id]);
        $data['record'] = $query->row_array();

        if (!$data['record']) {
            show_404();
        }

        // Fetch items
        $sql = "
        SELECT 
            vrei_item.*,
            cat.category_name,
            item.item_name,
            item.item_description,
            item.uom AS item_uom
        FROM vendor_rate_enquiry_item_info vrei_item
        LEFT JOIN category_info cat ON vrei_item.category_id = cat.category_id
        LEFT JOIN item_info item ON vrei_item.item_id = item.item_id
        WHERE vrei_item.vendor_rate_enquiry_id = ? AND vrei_item.status = 'Active'
        ORDER BY vrei_item.vendor_rate_enquiry_item_id
    ";
        $query = $this->db->query($sql, [$vendor_rate_enquiry_id]);
        $data['items'] = $query->result_array();

        // Calculate totals
        $data['grand_total'] = 0;
        $data['total_gst'] = 0;
        foreach ($data['items'] as &$item) {
            $item['amount'] = $item['rate'] * $item['qty'];
            $item['gst_amount'] = $item['amount'] * ($item['gst'] / 100);
            $data['grand_total'] += $item['amount'];
            $data['total_gst'] += $item['gst_amount'];
        }
        $data['final_total'] = $data['grand_total'] + $data['total_gst'];

        $this->load->view('page/vendor/vendor-rate-enquiry-print', $data);
    }

    public function get_data()
    {
        $table = $this->input->post('tbl');
        $rec_id = $this->input->post('id');

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $rec_list = array();

        if ($table == 'get-tender-enquiry-item-list-rate') {
            $query = $this->db->query("
            SELECT
                a.tender_enquiry_item_id,
                d.category_id,
                d.category_name,
                c.item_id,
                c.item_name,
                a.item_desc,
                a.uom,
                a.qty,
                a.status
            FROM
                tender_enquiry_item_info AS a
            LEFT JOIN tender_enquiry_info AS b
            ON
                a.tender_enquiry_id = b.tender_enquiry_id AND b.status = 'Active'
            LEFT JOIN item_info AS c
            ON
                a.item_id = c.item_id AND c.status = 'Active'
            LEFT JOIN category_info AS d
            ON
                a.category_id = d.category_id AND d.status = 'Active'
            WHERE
                a.status = 'Active' AND a.tender_enquiry_id = '" . $rec_id . "'
            ");
            $rec_list = $query->result_array();
        }
        header('Content-Type: application/x-json; charset=utf-8');
        echo json_encode($rec_list);
    }


    public function vendor_rate_enquiry_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data = array();
        $data['js'] = 'vendor/vendor-rate-enquiry-list.inc';
        $data['s_url'] = 'vendor-rate-enquiry-list';
        $data['title'] = 'Vendor Rate Enquiry List';

        // === FILTERS ===
        $where = "1 = 1";

        // Customer Filter
        if ($this->input->post('srch_customer_id') !== null) {
            $data['srch_customer_id'] = $srch_customer_id = $this->input->post('srch_customer_id');
            $this->session->set_userdata('srch_customer_id', $srch_customer_id);
        } elseif ($this->session->userdata('srch_customer_id')) {
            $data['srch_customer_id'] = $srch_customer_id = $this->session->userdata('srch_customer_id');
        } else {
            $data['srch_customer_id'] = $srch_customer_id = '';
        }
        if (!empty($srch_customer_id)) {
            $where .= " AND a.customer_id = '" . $this->db->escape_str($srch_customer_id) . "'";
        }

        // Vendor Filter
        if ($this->input->post('srch_vendor_id') !== null) {
            $data['srch_vendor_id'] = $srch_vendor_id = $this->input->post('srch_vendor_id');
            $this->session->set_userdata('srch_vendor_id', $srch_vendor_id);
        } elseif ($this->session->userdata('srch_vendor_id')) {
            $data['srch_vendor_id'] = $srch_vendor_id = $this->session->userdata('srch_vendor_id');
        } else {
            $data['srch_vendor_id'] = $srch_vendor_id = '';
        }
        if (!empty($srch_vendor_id)) {
            $where .= " AND a.vendor_id = '" . $this->db->escape_str($srch_vendor_id) . "'";
        }

        // Status Filter
        if ($this->input->post('srch_status') !== null) {
            $data['srch_status'] = $srch_status = $this->input->post('srch_status');
            $this->session->set_userdata('srch_status', $srch_status);
        } elseif ($this->session->userdata('srch_status')) {
            $data['srch_status'] = $srch_status = $this->session->userdata('srch_status');
        } else {
            $data['srch_status'] = $srch_status = '';
        }
        if (!empty($srch_status) && $srch_status !== 'All') {
            $where .= " AND a.status = '" . $this->db->escape_str($srch_status) . "'";
        }

        // === COUNT TOTAL ===
        $sql_count = "SELECT COUNT(*) as total FROM vendor_rate_enquiry_info a WHERE a.status != 'Delete' AND $where";
        $query_count = $this->db->query($sql_count);
        $data['total_records'] = $query_count->row()->total;

        // === PAGINATION ===
        $data['sno'] = $this->uri->segment(2, 0);
        $this->load->library('pagination');

        $config['base_url'] = trim(site_url($data['s_url']), '/' . $this->uri->segment(2, 0));
        $config['total_rows'] = $data['total_records'];
        $config['per_page'] = 25;
        $config['uri_segment'] = 2;
        $config['attributes'] = ['class' => 'page-link'];
        $config['full_tag_open'] = '<ul class="pagination pagination-sm no-margin pull-right">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a href="#" class="page-link">';
        $config['cur_tag_close'] = '</a></li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['prev_link'] = 'Prev';
        $config['next_link'] = 'Next';

        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();

        // === FETCH RECORDS ===
        $sql = "
            SELECT 
                a.vendor_rate_enquiry_id,
                a.enquiry_date,
                a.enquiry_no,
                a.opening_date,
                a.closing_date,
                a.status,
                c.customer_name,
                v.vendor_name,
                t.enquiry_no AS tender_enquiry_no
            FROM vendor_rate_enquiry_info a
            LEFT JOIN customer_info c ON a.customer_id = c.customer_id AND c.status = 'Active'
            LEFT JOIN vendor_info v ON a.vendor_id = v.vendor_id AND v.status = 'Active'
            LEFT JOIN tender_enquiry_info t ON a.tender_enquiry_id = t.tender_enquiry_id AND t.status != 'Delete'
            WHERE a.status != 'Delete' AND $where
            ORDER BY a.vendor_rate_enquiry_id DESC
            LIMIT " . $this->uri->segment(2, 0) . ", " . $config['per_page'];

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();

        // === DROPDOWNS ===
        $data['customer_opt'] = ['' => 'All'];
        $sql = "SELECT customer_id, customer_name FROM customer_info WHERE status = 'Active' ORDER BY customer_name";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }

        $data['vendor_opt'] = ['' => 'All'];
        $sql = "SELECT vendor_id, vendor_name FROM vendor_info WHERE status = 'Active' ORDER BY vendor_name";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['vendor_opt'][$row['vendor_id']] = $row['vendor_name'];
        }

        $data['status_opt'] = ['' => 'All', 'Active' => 'Active', 'Inactive' => 'Inactive'];

        $this->load->view('page/vendor/vendor-rate-enquiry-list', $data);
    }

    public function delete_record()
    {
        $table = $this->input->post('tbl');
        $rec_id = $this->input->post('id');

        if ($table == 'vendor_rate_enquiry_info' && !empty($rec_id)) {
            $this->db->where('vendor_rate_enquiry_id', $rec_id);
            $this->db->update('vendor_rate_enquiry_info', [
                'status' => 'Delete',
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s')
            ]);
            echo 'Vendor Rate Enquiry marked as deleted.';
        } else {
            echo 'Invalid request.';
        }
    }
    public function ajax_add_master_inline()
    {
        if ($this->input->post('mode') == 'Add Vendor') {

            $data = [
                'vendor_name' => $this->input->post('vendor_name'),
                'contact_name' => $this->input->post('contact_name'),
                'crno' => $this->input->post('crno'),
                'country' => $this->input->post('country'),
                'address' => $this->input->post('address'),
                'mobile' => $this->input->post('mobile'),
                'mobile_alt' => $this->input->post('mobile_alt'),
                'email' => $this->input->post('email'),
                'remarks' => $this->input->post('remarks'),
                'gst' => $this->input->post('gst'), 
                'status' => $this->input->post('status'), 
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s'),
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('vendor_info', $data);
            $insert_id = $this->db->insert_id();

            // Return response for JS
            echo json_encode([
                'status' => 'success',
                'message' => 'Vendor added successfully!',
                'id' => $insert_id,
                'name' => $data['vendor_name']
            ]);
        } 
    }

}