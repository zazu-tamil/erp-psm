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

            // Insert main record
            $insert_data = array(
                'customer_id' => $this->input->post('customer_id'),
                'tender_enquiry_id' => $this->input->post('tender_enquiry_id'),
                'vendor_id' => $this->input->post('vendor_id'),
                'customer_id' => $this->input->post('customer_id'),
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

            // Insert item records
            $tender_enquiry_item_id = $this->input->post('tender_enquiry_item_id');
            $category_ids = $this->input->post('category_id');
            $item_ids = $this->input->post('item_id');
            $item_descs = $this->input->post('item_desc');
            $uoms = $this->input->post('uom');
            $qtys = $this->input->post('qty');

            if (!empty($item_ids)) {
                foreach ($item_ids as $index => $item_id) {
                    if (!empty($item_id)) {
                        $insert_item_data = array(
                            'vendor_rate_enquiry_id' => $vendor_rate_enquiry_id,
                            'tender_enquiry_item_id' => $tender_enquiry_item_id,
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
        //$config['num_links'] = 2; 
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

        // Companies
        $sql = "
            SELECT 
            vendor_id, 
            vendor_name 
            FROM vendor_info 
            WHERE status = 'Active' 
            ORDER BY vendor_name ASC";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['vendor_opt'][$row['vendor_id']] = $row['vendor_name'];
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
        $sql = "
            SELECT
                a.tender_enquiry_id,
                a.enquiry_no,
                b.company_name,
                c.customer_name 
            FROM
                tender_enquiry_info AS a
                LEFT JOIN company_info as b on a.company_id = b.company_id and b.status='Active'
                LEFT JOIN customer_info as c on a.customer_id = c.customer_id and c.status='Active'
            WHERE
                a.status = 'Active'
            ORDER BY a.tender_enquiry_id , a.enquiry_no ASC";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['tender_enquiry_id'] . ' -> ' . $row['enquiry_no'] . ' -> ' . $row['company_name'] . ' -> ' . $row['customer_name'];
        }

        $sql = "
            SELECT *
            FROM company_info
            WHERE status != 'Delete'
            order by company_id desc
            limit " . $this->uri->segment(2, 0) . "," . $config['per_page'] . "                
        ";

        $data['record_list'] = array();

        $query = $this->db->query($sql);

        foreach ($query->result_array() as $row) {
            $data['record_list'][] = $row;
        }



        $data['pagination'] = $this->pagination->create_links();



        $this->load->view('page/vendor/vendor-rate-enquiry', $data);
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
                d.category_name,
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
                a.status = 'Active'
            and a.tender_enquiry_id = '" . $rec_id . "'
                
            ");

            $rec_list = $query->result_array();
        }
        header('Content-Type: application/x-json; charset=utf-8');
        echo json_encode($rec_list);
    }

}