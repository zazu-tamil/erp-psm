<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tender extends CI_Controller
{

    // ============================================
// CONTROLLER: add_tender_enquiry() function
// ============================================

    // ============================================
// CONTROLLER: add_tender_enquiry() function
// ============================================

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

            // Handle File Upload
            $tender_document = '';

            // Only process if file is uploaded
            if (!empty($_FILES['tender_document']['name'])) {
                $upload_path = FCPATH . 'tender-documents/' . COMPANY . '/';

                // Check if folder exists, if not create it
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, true);
                }

                $config['upload_path'] = $upload_path;
                $config['file_name'] = "TENDER_" . date('YmdHis');
                $config['allowed_types'] = '*'; // Allow all file types
                $config['max_size'] = 10240; // 10MB in KB

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('tender_document')) {
                    $file_array = $this->upload->data();
                    $tender_document = 'tender-documents/' . COMPANY . '/' . $file_array['file_name'];
                } else {
                    // Get upload error for debugging
                    $error = $this->upload->display_errors();
                    echo "<pre>Upload Error: " . $error . "</pre>";
                    $tender_document = '';
                }
            }

            $insert_data = array(
                'company_id' => $srch_company_id,
                'enquiry_date' => $this->input->post('enquiry_date'),
                'enquiry_no' => $this->input->post('enquiry_no'),
                'customer_contact_id' => $this->input->post('customer_contact_id'),
                'tender_status' => $this->input->post('tender_status'),
                'customer_id' => $srch_customer_id,
                'opening_date' => $this->input->post('opening_date') ? date('Y-m-d H:i:s', strtotime($this->input->post('opening_date'))) : null,
                'closing_date' => $this->input->post('closing_date') ? date('Y-m-d H:i:s', strtotime($this->input->post('closing_date'))) : null,
                'status' => $this->input->post('status') ?: 'Active',
                'tender_name' => $this->input->post('tender_name'),
                'tender_document' => $tender_document,
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s')
            );



            $this->db->insert('tender_enquiry_info', $insert_data);
            $tender_enquiry_id = $this->db->insert_id();

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

            // Comment this out for debugging, uncomment after testing
            redirect('tender-enquiry-list');

        }

        // Populate dropdowns
        $data['company_opt'] = [];
        $data['customer_opt'] = [];
        $data['category_opt'] = [];
        $data['uom_opt'] = [];

        // Companies
        $query = $this->db->query("
        SELECT 
        company_id, 
        company_name 
        FROM company_info 
        WHERE status = 'Active' 
        ORDER BY company_name");
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }

        // Customers
        $query = $this->db->query("
        SELECT 
        customer_id, 
        customer_name 
        FROM customer_info 
        WHERE status = 'Active' 
        ORDER BY customer_name");
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }

        // Categories
        $query = $this->db->query("
        SELECT 
        category_id, 
        category_name 
        FROM category_info 
        WHERE status = 'Active' 
        ORDER BY category_name");
        foreach ($query->result_array() as $row) {
            $data['category_opt'][$row['category_id']] = $row['category_name'];
        }

        // UOMs (using uom_name as key and value)
        $query = $this->db->query("
        SELECT 
        uom_name 
        FROM uom_info 
        WHERE status = 'Active' 
        ORDER BY uom_name");
        foreach ($query->result_array() as $row) {
            $data['uom_opt'][$row['uom_name']] = $row['uom_name'];
        }

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
           select 
           a.gst_id, 
           a.gst_percentage 
        from gst_info as a 
        where status != 'Delete' 
        order by a.status asc , a.gst_percentage asc
    ";
        $query = $this->db->query($sql);
        $data['gst_opt'] = array();
        foreach ($query->result_array() as $row) {
            $data['gst_opt'][$row['gst_percentage']] = $row['gst_percentage'];
        }

        // Brands
        $sql = " 
        SELECT brand_id, brand_name 
        FROM brand_info WHERE status != 'Delete'
        ORDER BY brand_name ASC
    ";

        $query = $this->db->query($sql);
        $data['brand_opt'] = array();
        foreach ($query->result_array() as $row) {
            $data['brand_opt'][$row['brand_id']] = $row['brand_name'];
        }
        $data['tender_status_opt'] = ['' => 'Select Tender Status', 'Open' => 'Open', 'Quoted' => 'Quoted', 'Won' => 'Won', 'Lost' => 'Lost', 'On Hold' => 'On Hold'];
        $this->load->view('page/tender/add-tender-enquiry', $data);
    }







    public function tender_enquiry_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data = array();
        $data['js'] = 'tender/tender-enquiry-list.inc';
        $data['s_url'] = 'tender-enquiry-list';
        $data['title'] = 'Tender Enquiry List';

        // === FILTERS ===
        $where = "1";

        // === FILTERS ===
        $where = "1 = 1";
        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');
            $this->session->set_userdata('srch_from_date', $this->input->post('srch_from_date'));
            $this->session->set_userdata('srch_to_date', $this->input->post('srch_to_date'));
        } elseif ($this->session->userdata('srch_from_date')) {
            $data['srch_from_date'] = $srch_from_date = $this->session->userdata('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->session->userdata('srch_to_date');
        } else {
            $data['srch_from_date'] = $srch_from_date = date('Y-m-d');
            $data['srch_to_date'] = $srch_to_date = date('Y-m-d');
        }

        // Company Filter
        if ($this->input->post('srch_company_id') !== null) {
            $data['srch_company_id'] = $srch_company_id = $this->input->post('srch_company_id');
            $this->session->set_userdata('srch_company_id', $srch_company_id);
        } elseif ($this->session->userdata('srch_company_id')) {
            $data['srch_company_id'] = $srch_company_id = $this->session->userdata('srch_company_id');
        } else {
            $data['srch_company_id'] = $srch_company_id = '';
        }
        if (!empty($srch_company_id)) {
            $where .= " AND a.company_id = '" . $this->db->escape_str($srch_company_id) . "'";
        }

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
            $where .= " AND a.tender_status = '" . $this->db->escape_str($srch_status) . "'";
        }


        $this->db->from('tender_enquiry_info a');
        $this->db->where('a.status !=', 'Delete');
        $this->db->where($where);
        $this->db->where("DATE(a.enquiry_date) BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "'");
        $data['total_records'] = $this->db->count_all_results();

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
                a.*, 
                b.company_code,
                c.customer_code,
                b.company_name,
                c.customer_name,
                get_tender_info(a.tender_enquiry_id) as tender_details
            FROM tender_enquiry_info AS a
            LEFT JOIN company_info AS b 
                ON a.company_id = b.company_id AND b.status = 'Active'
            LEFT JOIN customer_info AS c 
                ON a.customer_id = c.customer_id AND c.status = 'Active' 
            WHERE a.status != 'Delete' 
            AND a.enquiry_date BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "' 
            AND $where
            ORDER BY a.tender_enquiry_id DESC
            LIMIT " . $this->uri->segment(2, 0) . ", " . $config['per_page'];

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();

        // === DROPDOWNS ===
        $data['company_opt'] = ['' => 'All'];
        $sql = "
            SELECT 
            company_id, 
            company_name 
            FROM company_info 
            WHERE status = 'Active' 
            ORDER BY company_name";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }

        $data['customer_opt'] = ['' => 'All'];
        $sql = "
            SELECT 
            customer_id, 
            customer_name 
            FROM customer_info 
            WHERE status = 'Active' 
            ORDER BY customer_name";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }

        $data['status_opt'] = ['' => 'All', 'Active' => 'Active', 'Inactive' => 'Inactive'];
        $data['tender_status_opt'] = ['' => 'Select Tender Status', 'Open' => 'Open', 'Quoted' => 'Quoted', 'Won' => 'Won', 'Lost' => 'Lost', 'On Hold' => 'On Hold'];

        $this->load->view('page/tender/tender-enquiry-list', $data);
    }

    public function edit_tender_enquiry($tender_enquiry_id = 0)
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        if (!$tender_enquiry_id) {
            $this->session->set_flashdata('error', 'Invalid record ID.');
            redirect('tender-enquiry-list');
        }

        $data = [];
        $data['js'] = 'tender/edit-tender-enquiry.inc';
        $data['title'] = 'Edit Tender Enquiry';
        $data['tender_enquiry_id'] = $tender_enquiry_id;

        /* ---------- UPDATE ---------- */
        if ($this->input->post('mode') == 'Edit') {
            $this->db->trans_start();

            $current_record = $this->db->select('tender_document')
                ->where('tender_enquiry_id', $tender_enquiry_id)
                ->get('tender_enquiry_info')
                ->row_array();
            $old_document_path = $current_record ? $current_record['tender_document'] : '';
            $new_document_path = $old_document_path; // Start with the old path

            // FILE UPLOAD LOGIC (MATCHING ADD FUNCTION)
            if (!empty($_FILES['tender_document']['name'])) {
                $upload_path = FCPATH . 'tender-documents/' . COMPANY . '/';

                // Check if folder exists, if not create it
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, true);
                }

                $config['upload_path'] = $upload_path;
                $config['file_name'] = "TENDER_EDIT_" . date('YmdHis');
                $config['allowed_types'] = '*'; // Allow all file types
                $config['max_size'] = 10240; // 10MB in KB

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('tender_document')) {
                    $file_array = $this->upload->data();
                    $new_document_path = 'tender-documents/' . COMPANY . '/' . $file_array['file_name'];

                    // Successfully uploaded new file: DELETE the old one (if it exists)
                    if ($old_document_path && file_exists(FCPATH . $old_document_path)) {
                        @unlink(FCPATH . $old_document_path);
                    }
                } else {
                    // Get upload error for debugging (remove echo in production)
                    $error = $this->upload->display_errors();
                    echo "<pre>Upload Error: " . $error . "</pre>";
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('error', 'Error uploading document: ' . $error);
                    redirect('tender-enquiry-edit/' . $tender_enquiry_id);
                    return;
                }
            }

            // Main record update
            $main = [
                'company_id' => $this->input->post('company_id'),
                'enquiry_date' => $this->input->post('enquiry_date'),
                'enquiry_no' => $this->input->post('enquiry_no'),
                'tender_status' => $this->input->post('tender_status'),
                'customer_id' => $this->input->post('customer_id'),
                'company_sno' => $this->input->post('company_sno'),
                'customer_contact_id' => $this->input->post('customer_contact_id'),
                'customer_sno' => $this->input->post('customer_sno'),
                'opening_date' => $this->input->post('opening_date')
                    ? date('Y-m-d H:i:s', strtotime($this->input->post('opening_date'))) : null,
                'closing_date' => $this->input->post('closing_date')
                    ? date('Y-m-d H:i:s', strtotime($this->input->post('closing_date'))) : null,
                'status' => $this->input->post('status') ?: 'Active',
                'tender_document' => $new_document_path,
                'tender_name' => $this->input->post('tender_name'),
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s')
            ];
            $this->db->where('tender_enquiry_id', $tender_enquiry_id)
                ->update('tender_enquiry_info', $main);

            // Get existing item IDs from database
            $existing = [];
            $q = $this->db->select('tender_enquiry_item_id')
                ->where('tender_enquiry_id', $tender_enquiry_id)
                ->where('status !=', 'Deleted')
                ->get('tender_enquiry_item_info')
                ->result_array();
            foreach ($q as $r) {
                $existing[] = $r['tender_enquiry_item_id'];
            }

            // Get posted data
            $cat_ids = $this->input->post('category_id') ?: [];
            $item_ids = $this->input->post('item_id') ?: [];
            $desc = $this->input->post('item_desc') ?: [];
            $uom = $this->input->post('uom') ?: [];
            $qty = $this->input->post('qty') ?: [];
            $rec_ids = $this->input->post('tender_enquiry_item_id') ?: [];

            $processed = [];

            foreach ($item_ids as $i => $itm) {
                if (empty($itm))
                    continue;

                $row = [
                    'tender_enquiry_id' => $tender_enquiry_id,
                    'category_id' => isset($cat_ids[$i]) ? $cat_ids[$i] : 0,
                    'item_id' => $itm,
                    'item_desc' => isset($desc[$i]) ? $desc[$i] : '',
                    'uom' => isset($uom[$i]) ? $uom[$i] : '',
                    'qty' => isset($qty[$i]) ? $qty[$i] : 0,
                    'status' => 'Active',
                    'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                    'updated_date' => date('Y-m-d H:i:s')
                ];

                $rec_id = isset($rec_ids[$i]) ? $rec_ids[$i] : 0;

                // If record ID exists and is in existing records, update it
                if ($rec_id && in_array($rec_id, $existing)) {
                    $this->db->where('tender_enquiry_item_id', $rec_id)
                        ->update('tender_enquiry_item_info', $row);
                    $processed[] = $rec_id;
                }
                // Otherwise insert new record
                else {
                    $row['created_by'] = $this->session->userdata(SESS_HD . 'user_id');
                    $row['created_date'] = date('Y-m-d H:i:s');
                    $this->db->insert('tender_enquiry_item_info', $row);
                }
            }

            // Mark removed items as Deleted
            $to_delete = array_diff($existing, $processed);
            foreach ($to_delete as $del) {
                $this->db->where('tender_enquiry_item_id', $del)
                    ->update('tender_enquiry_item_info', ['status' => 'Deleted']);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Error updating data. Please try again.');
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('success', 'Tender Enquiry updated successfully.');
            }
            redirect('tender-enquiry-list');
        }
        $sql = "SELECT * FROM tender_enquiry_info WHERE tender_enquiry_id = ? AND status != 'Deleted'";
        $q = $this->db->query($sql, [$tender_enquiry_id]);
        $data['main_record'] = $q->row_array();

        if (!$data['main_record']) {
            $this->session->set_flashdata('error', 'Record not found.');
            redirect('tender-enquiry-list');
        }

        $sql = "
          SELECT 
                tei.*,
                ii.item_name,
                ii.item_description,
                ii.item_code,
                ii.uom AS item_uom,
                CONCAT(
                    ii.item_code, ' : ', ii.item_name, ' [ ',
                    SUBSTRING(ii.item_description, 1, 100),
                    ' ]'
                ) AS item_label1,
                 ii.item_code AS item_label
            FROM tender_enquiry_item_info tei
            LEFT JOIN item_info ii 
                ON ii.item_id = tei.item_id 
                AND ii.status = 'Active'
            WHERE tei.tender_enquiry_id = ?
            AND tei.status = 'Active'
            ORDER BY tei.tender_enquiry_item_id ASC;

        ";
        $q = $this->db->query($sql, [$tender_enquiry_id]);
        $data['item_list'] = $q->result_array();


        // === DROPDOWNS ===
        $data['company_opt'] = ['' => 'Select Company'];
        $sql = "
            SELECT 
            company_id, 
            company_name 
            FROM company_info 
            WHERE status = 'Active' 
            ORDER BY company_name";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }

        $data['customer_opt'] = ['' => 'Select Customer'];
        $sql = "
            SELECT 
            customer_id, 
            customer_name 
            FROM customer_info 
            WHERE status = 'Active' 
            ORDER BY customer_name";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }

        $data['category_opt'] = ['' => 'Select Category'];
        $sql = "
            SELECT 
            category_id, 
            category_name 
            FROM category_info 
            WHERE status = 'Active' 
            ORDER BY category_name";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['category_opt'][$row['category_id']] = $row['category_name'];
        }

        $data['customer_contact_opt'] = ['' => '--Select Contact Person--'];

        $sql = "
            SELECT a.customer_contact_id, a.contact_person_name
            FROM customer_contact_info a
            WHERE a.status='Active'
            ORDER BY a.contact_person_name
        ";
        $q = $this->db->query($sql);

        foreach ($q->result_array() as $row) {
            $data['customer_contact_opt'][$row['customer_contact_id']] = $row['contact_person_name'];
        }


        $data['uom_opt'] = [];
        $sql = "
            SELECT 
            uom_name 
            FROM uom_info 
            WHERE status = 'Active' 
            ORDER BY uom_name";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['uom_opt'][$row['uom_name']] = $row['uom_name'];
        }

        $data['status_opt'] = ['' => 'Select Status', 'Active' => 'Active', 'Inactive' => 'Inactive'];
        $data['tender_status_opt'] = ['' => 'Select Tender Status', 'Open' => 'Open', 'Quoted' => 'Quoted', 'Won' => 'Won', 'Lost' => 'Lost', 'On Hold' => 'On Hold'];

        // JSON encode for JavaScript
        $data['category_json'] = json_encode($data['category_opt']);
        $data['uom_json'] = json_encode(['' => '—'] + $data['uom_opt']);

        $this->load->view('page/tender/edit-tender-enquiry', $data);
    }

    public function tender_quotation_add()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if ($this->session->userdata(SESS_HD . 'level') != 'Admin' && $this->session->userdata(SESS_HD . 'level') != 'Staff') {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'tender/tender-quotation-add.inc';
        $data['title'] = 'Add Tender Quotation';

        if ($this->input->post('mode') == 'Add') {
            $this->db->trans_start();

            /* ---- 1. Header record ---- */
            $header = [
                'company_id' => $this->input->post('srch_company_id'),
                'customer_id' => $this->input->post('srch_customer_id'),
                'tender_enquiry_id' => $this->input->post('srch_tender_enquiry_id'),
                'quotation_no' => $this->input->post('quotation_no'),
                'tender_ref_no' => $this->input->post('tender_ref_no'),
                'quote_date' => $this->input->post('quote_date'),
                'remarks' => $this->input->post('remarks'),
                'terms' => $this->input->post('terms'),
                'quotation_status' => $this->input->post('quotation_status'),
                'status' => 'Active',
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('tender_quotation_info', $header);
            $tender_quotation_id = $this->db->insert_id();

            /* ---- 2. ONLY SELECTED items ---- */
            $selected_idxs = $this->input->post('selected_items') ?? [];

            if (!empty($selected_idxs)) {
                $tender_enquiry_item_ids = $this->input->post('tender_enquiry_item_id') ?? [];
                $category_ids = $this->input->post('category_id') ?? [];
                $item_ids = $this->input->post('item_id') ?? [];
                $item_descs = $this->input->post('item_desc') ?? [];
                $uoms = $this->input->post('uom') ?? [];
                $qtys = $this->input->post('qty') ?? [];
                $rates = $this->input->post('rate') ?? [];
                $gsts = $this->input->post('gst') ?? [];
                $amounts = $this->input->post('amount') ?? [];

                foreach ($selected_idxs as $idx) {
                    $item_data = [
                        'tender_quotation_id' => $tender_quotation_id,
                        'tender_enquiry_item_id' => $tender_enquiry_item_ids[$idx] ?? 0,
                        'category_id' => $category_ids[$idx] ?? 0,
                        'item_id' => $item_ids[$idx] ?? 0,
                        'item_desc' => $item_descs[$idx] ?? '',
                        'uom' => $uoms[$idx] ?? '',
                        'qty' => $qtys[$idx] ?? 0,
                        'rate' => $rates[$idx] ?? 0,
                        'gst' => $gsts[$idx] ?? 0,
                        'amount' => $amounts[$idx] ?? 0,
                        'status' => 'Active',
                        'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                        'created_date' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('tender_quotation_item_info', $item_data);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error saving data. Please try again.');
            } else {
                $this->session->set_flashdata('success', 'Tender Quotation saved successfully.');
            }
            redirect('tender-quotation-list/');
        }

        // Get all companies
        $sql = "SELECT company_id, company_name FROM company_info WHERE status = 'Active' ORDER BY company_name ASC";
        $query = $this->db->query($sql);
        $data['company_opt'] = [];
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }

        // Get GST options
        $sql = "SELECT gst_id, gst_percentage FROM gst_info WHERE status = 'Active' ORDER BY gst_percentage ASC";
        $query = $this->db->query($sql);
        $data['gst_opt'] = [];
        foreach ($query->result_array() as $row) {
            $data['gst_opt'][$row['gst_id']] = $row['gst_percentage'];
        }

        $this->load->view('page/tender/tender-quotation-add', $data);
    }

    // Add this method to get ALL customers (no company filtering)


    public function tender_quotation_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data = array();
        $data['js'] = 'tender/tender-quotation-list.inc';
        $data['s_url'] = 'tender-quotation-list';
        $data['title'] = 'Tender Quotation List';

        // === FILTERS ===
        $where = "1 = 1";

        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');
            $this->session->set_userdata('srch_from_date', $this->input->post('srch_from_date'));
            $this->session->set_userdata('srch_to_date', $this->input->post('srch_to_date'));
        } elseif ($this->session->userdata('srch_from_date')) {
            $data['srch_from_date'] = $srch_from_date = $this->session->userdata('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->session->userdata('srch_to_date');
        } else {
            $data['srch_from_date'] = $srch_from_date = date('Y-m-d');
            $data['srch_to_date'] = $srch_to_date = date('Y-m-d');
        }

        // Company Filter
        if ($this->input->post('srch_company_id') !== null) {
            $data['srch_company_id'] = $srch_company_id = $this->input->post('srch_company_id');
            $this->session->set_userdata('srch_company_id', $srch_company_id);
        } elseif ($this->session->userdata('srch_company_id')) {
            $data['srch_company_id'] = $srch_company_id = $this->session->userdata('srch_company_id');
        } else {
            $data['srch_company_id'] = $srch_company_id = '';
        }
        if (!empty($srch_company_id)) {
            $where .= " AND a.company_id = '" . $this->db->escape_str($srch_company_id) . "'";
        }

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

        // Tender Enquiry Filter
        if ($this->input->post('srch_tender_enquiry_id') !== null) {
            $data['srch_tender_enquiry_id'] = $srch_tender_enquiry_id = $this->input->post('srch_tender_enquiry_id');
            $this->session->set_userdata('srch_tender_enquiry_id', $srch_tender_enquiry_id);
        } elseif ($this->session->userdata('srch_tender_enquiry_id')) {
            $data['srch_tender_enquiry_id'] = $srch_tender_enquiry_id = $this->session->userdata('srch_tender_enquiry_id');
        } else {
            $data['srch_tender_enquiry_id'] = $srch_tender_enquiry_id = '';
        }
        if (!empty($srch_tender_enquiry_id)) {
            $where .= " AND a.tender_enquiry_id = '" . $this->db->escape_str($srch_tender_enquiry_id) . "'";
        }

        // Status Filter
        if ($this->input->post('srch_quotation_status') !== null) {
            $data['srch_quotation_status'] = $srch_quotation_status = $this->input->post('srch_quotation_status');
            $this->session->set_userdata('srch_quotation_status', $srch_quotation_status);
        } elseif ($this->session->userdata('srch_quotation_status')) {
            $data['srch_quotation_status'] = $srch_quotation_status = $this->session->userdata('srch_quotation_status');
        } else {
            $data['srch_quotation_status'] = $srch_quotation_status = '';
        }
        if (!empty($srch_quotation_status) && $srch_quotation_status !== 'All') {
            $where .= " AND a.quotation_status = '" . $this->db->escape_str($srch_quotation_status) . "'";
        }

        // === COUNT TOTAL ===
        $sql_count = "SELECT COUNT(*) as total FROM tender_quotation_info a WHERE a.status != 'Delete' AND $where";
        $query_count = $this->db->query($sql_count);
        $data['total_records'] = $query_count->row()->total;

        $this->db->from('tender_quotation_info a');
        $this->db->where('a.status !=', 'Delete');
        $this->db->where($where);
        $this->db->where("DATE(a.quote_date) BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "'");
        $data['total_records'] = $this->db->count_all_results();

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
                a.tender_quotation_id,
                a.quotation_no,
                a.tender_ref_no,
                a.quote_date,
                a.remarks,
                a.status,
                b.company_name,
                a.quotation_status,
                c.customer_name,
                get_tender_info(a.tender_enquiry_id) as tender_details
            FROM tender_quotation_info a
            LEFT JOIN company_info b ON a.company_id = b.company_id AND b.status = 'Active'
            LEFT JOIN customer_info c ON a.customer_id = c.customer_id AND c.status = 'Active'
            LEFT JOIN tender_enquiry_info d ON a.tender_enquiry_id = d.tender_enquiry_id AND d.status = 'Active'
            WHERE a.status != 'Delete' 
             AND a.quote_date BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "' 
            AND $where 
            ORDER BY a.tender_quotation_id DESC
            LIMIT " . $this->uri->segment(2, 0) . ", " . $config['per_page'];

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();

        // === DROPDOWNS ===
        $data['company_opt'] = ['' => 'All'];
        $sql = "SELECT company_id, company_name FROM company_info WHERE status = 'Active' ORDER BY company_name";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }

        $data['customer_opt'] = ['' => 'All'];
        $sql = "SELECT customer_id, customer_name FROM customer_info WHERE status = 'Active' ORDER BY customer_name";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }

        // $data['tender_enquiry_opt'] = ['' => 'All'];
        // $sql = "
        //     SELECT tender_enquiry_id,
        //     enquiry_no FROM tender_enquiry_info
        //     WHERE status = 'Active'
        //     ORDER BY enquiry_no
        // ";
        // $query = $this->db->query($sql);
        // foreach ($query->result_array() as $row) {
        //     $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['enquiry_no'];
        // }
        $data['tender_enquiry_opt'] = ['' => 'All'];
        if (!empty($srch_customer_id)) {
            $sql = "
                SELECT 
                    a.tender_enquiry_id, 
                    get_tender_info(a.tender_enquiry_id) as tender_details
                FROM tender_enquiry_info AS a 
                WHERE a.status = 'Active' 
                and a.customer_id= '" . $srch_customer_id . "'
                ORDER BY a.tender_enquiry_id, a.enquiry_no ASC
            ";


            $query = $this->db->query($sql);
            $data['tender_enquiry_opt'] = [];
            foreach ($query->result_array() as $row) {
                // $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['company_code'] . ' -> ' . $row['company_sno'] . ' -> ' . $row['customer_code'] . ' -> ' . $row['customer_sno'] . ' -> ' . $row['enquiry_no'];
                $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['tender_details'];
            }
        }

        $data['quotation_status_opt'] = [
            '' => 'All',
            'Open' => 'Open',
            'Quoted' => 'Quoted',
            'Won' => 'Won',
            'Lost' => 'Lost',
            'On Hold' => 'On Hold',
        ];


        $this->load->view('page/tender/tender-quotation-list', $data);
    }
    public function tender_quotation_edit($tender_quotation_id)
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if ($this->session->userdata(SESS_HD . 'level') != 'Admin' && $this->session->userdata(SESS_HD . 'level') != 'Staff') {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'tender/tender-quotation-edit.inc';
        $data['title'] = 'Edit Tender Quotation';

        if ($this->input->post('mode') == 'Edit') {
            $this->db->trans_start();

            /* ---- 1. Header record ------------------------------------------------ */
            $header = [
                'company_id' => $this->input->post('srch_company_id'),
                'customer_id' => $this->input->post('srch_customer_id'),
                // 'tender_enquiry_id' => $this->input->post('srch_tender_enquiry_id'),
                'quotation_no' => $this->input->post('quotation_no'),
                'tender_ref_no' => $this->input->post('tender_ref_no'),
                'quote_date' => $this->input->post('quote_date'),
                'remarks' => $this->input->post('remarks'),
                'terms' => $this->input->post('terms'),
                'quotation_status' => $this->input->post('quotation_status'),
                'status' => 'Active',
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s')
            ];
            $this->db->where('tender_quotation_id', $tender_quotation_id);
            $this->db->update('tender_quotation_info', $header);

            /* ---- 2. DELETE old items and insert new ------------------------------ */
            $this->db->where('tender_quotation_id', $tender_quotation_id);
            $this->db->delete('tender_quotation_item_info');

            $selected_idxs = $this->input->post('selected_items') ?? [];   // array of "i" values

            if (!empty($selected_idxs)) {
                // All arrays are posted with the SAME order as the rows
                $tender_enquiry_item_ids = $this->input->post('tender_enquiry_item_id') ?? [];
                $category_ids = $this->input->post('category_id') ?? [];
                $item_ids = $this->input->post('item_id') ?? [];
                $item_descs = $this->input->post('item_desc') ?? [];
                $uoms = $this->input->post('uom') ?? [];
                $qtys = $this->input->post('qty') ?? [];
                $gsts = $this->input->post('gst') ?? [];
                $rates = $this->input->post('rate') ?? [];
                $amounts = $this->input->post('amount') ?? [];

                foreach ($selected_idxs as $idx) {
                    $item_data = [
                        'tender_quotation_id' => $tender_quotation_id,
                        'tender_enquiry_item_id' => $tender_enquiry_item_ids[$idx] ?? 0,
                        'category_id' => $category_ids[$idx] ?? 0,
                        'item_id' => $item_ids[$idx] ?? 0,
                        'item_desc' => $item_descs[$idx] ?? '',
                        'uom' => $uoms[$idx] ?? '',
                        'qty' => $qtys[$idx] ?? 0,
                        'gst' => $gsts[$idx] ?? 0,
                        'rate' => $rates[$idx] ?? 0,
                        'amount' => $amounts[$idx] ?? 0,
                        'status' => 'Active',
                        'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                        'created_date' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('tender_quotation_item_info', $item_data);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error updating data. Please try again.');
            } else {
                $this->session->set_flashdata('success', 'Tender Quotation updated successfully.');
            }
            redirect('tender-quotation-list');
        }

        $this->load->library('pagination');

        $this->db->where('status != ', 'Delete');
        $this->db->from('vendor_rate_enquiry_info');
        $data['total_records'] = $cnt = $this->db->count_all_results();

        $data['sno'] = $this->uri->segment(2, 0);

        $config['base_url'] = trim(site_url('tender-quotation-list') . '/' . $this->uri->segment(2, 0));
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

        $data['company_opt'] = [];
        $data['customer_opt'] = [];
        $data['vendor_opt'] = [];
        $data['tender_enquiry_opt'] = [];
        $data['gst_opt'] = [];

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
            SELECT company_id,company_name
            FROM company_info
            WHERE status = 'Active' 
            ORDER BY company_name ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
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
        foreach ($query->result_array() as $row) {
            $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['tender_enquiry_id'] . ' -> ' . $row['enquiry_no'] . ' -> ' . $row['company_name'] . ' -> ' . $row['customer_name'];
        }

        $sql = "
            SELECT gst_id, gst_percentage
            FROM gst_info 
            WHERE status = 'Active'
            ORDER BY gst_percentage ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['gst_opt'][$row['gst_percentage']] = $row['gst_percentage'];
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

        /* ---- Load existing record for edit ---- */
        $data['header'] = $this->db->where('tender_quotation_id', $tender_quotation_id)->get('tender_quotation_info')->row_array();
        $sql = "
            SELECT
                tqi.*,
                ii.item_code
            FROM
                tender_quotation_item_info tqi
            LEFT JOIN item_info ii ON
                tqi.item_id = ii.item_id AND ii.status = 'Active'
            WHERE
                tqi.tender_quotation_id = ? AND tqi.status != 'Delete'
        ";
        $query = $this->db->query($sql, [$tender_quotation_id]);
        $data['items'] = $query->result_array();  // FIXED: row_array() → result_array()


        $data['pagination'] = $this->pagination->create_links();
        $this->load->view('page/tender/tender-quotation-edit', $data);
    }


    public function tender_quotation_print($tender_quotation_id = 0)
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        if (!$tender_quotation_id) {
            show_404();
        }

        // === MAIN RECORD ===
        $sql = "
         SELECT
            tqi.*,
            c.customer_name,
            c.address,
            c.country AS customer_country,
            ci.company_name AS our_company,
            te.enquiry_no AS tender_enquiry_no,
            tqi.quotation_no as tender_quotation_no,
            ci.ltr_header_img
        FROM
            tender_quotation_info tqi
        LEFT JOIN customer_info c ON
            tqi.customer_id = c.customer_id AND c.status = 'Active'
        LEFT JOIN company_info ci ON
            tqi.company_id = ci.company_id AND ci.status = 'Active'
        LEFT JOIN tender_enquiry_info te ON
            tqi.tender_enquiry_id = te.tender_enquiry_id AND te.status = 'Active'
        WHERE
            tqi.tender_quotation_id = ? AND tqi.status != 'Delete'
        ";
        $query = $this->db->query($sql, [$tender_quotation_id]);
        $data['record'] = $query->row_array();

        if (!$data['record']) {
            show_404();
        }

        // === ITEMS WITH RATE CALCULATION ===
        $sql = "
        SELECT 
            tqii.*,
            cat.category_name,
            item.item_name,
            item.item_description,
            item.item_code,
            item.uom AS item_uom
        FROM tender_quotation_item_info tqii
        LEFT JOIN category_info cat ON tqii.category_id = cat.category_id
        LEFT JOIN item_info item ON tqii.item_id = item.item_id
        WHERE tqii.tender_quotation_id = ? 
          AND tqii.status IN ('Active', 'Inactive')
        ORDER BY tqii.tender_quotation_item_id
    ";
        $query = $this->db->query($sql, [$tender_quotation_id]);
        $items = $query->result_array();

        $data['items'] = [];
        $gst_summary = [];

        foreach ($items as $item) {
            $qty = floatval($item['qty']);
            $gst = floatval($item['gst']);
            $amount = floatval($item['amount']);
            $rate = floatval($item['rate']);


            // === GST Amount ===
            $gst_amount = $amount - ($qty * $rate);

            // === Store in item ===
            $item['rate'] = $rate;
            $item['gst_amount'] = $gst_amount;
            $item['base_amount'] = $qty * $rate;

            $data['items'][] = $item;

            // === GST Summary ===
            $gst_key = number_format($gst, 2);
            if (!isset($gst_summary[$gst_key])) {
                $gst_summary[$gst_key] = ['gst' => $gst, 'base' => 0, 'gst_amount' => 0];
            }
            $gst_summary[$gst_key]['base'] += $qty * $rate;
            $gst_summary[$gst_key]['gst_amount'] += $gst_amount;
        }

        $data['gst_summary'] = $gst_summary;
        $data['grand_total'] = array_sum(array_column($data['items'], 'base_amount'));
        $data['total_gst'] = array_sum(array_column($data['items'], 'gst_amount'));
        $data['final_total'] = $data['grand_total'] + $data['total_gst'];

        $this->load->view('page/tender/tender-quotation-print', $data);
    }

    public function tender_quotation_po()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if ($this->session->userdata(SESS_HD . 'level') != 'Admin' && $this->session->userdata(SESS_HD . 'level') != 'Staff') {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'tender/tender-quotation-po.inc';
        $data['title'] = 'Add Tender Quotation PO';

        if ($this->input->post('mode') == 'Add') {
            $this->db->trans_start();

            /* ---- 1. Header record ------------------------------------------------ */
            $header = [
                'company_id' => $this->input->post('srch_company_id'),
                'customer_id' => $this->input->post('srch_customer_id'),
                'tender_enquiry_id' => $this->input->post('srch_tender_enquiry_id'),
                'status' => 'Active',
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('tender_quotation_info', $header);
            $tender_quotation_id = $this->db->insert_id();

            $selected_idxs = $this->input->post('selected_items') ?? [];

            if (!empty($selected_idxs)) {
                $tender_enquiry_item_ids = $this->input->post('tender_enquiry_item_id') ?? [];
                $category_ids = $this->input->post('category_id') ?? [];
                $item_ids = $this->input->post('item_id') ?? [];
                $item_descs = $this->input->post('item_desc') ?? [];
                $uoms = $this->input->post('uom') ?? [];
                $qtys = $this->input->post('qty') ?? [];
                $gsts = $this->input->post('gst') ?? [];
                $amounts = $this->input->post('amount') ?? [];

                foreach ($selected_idxs as $idx) {
                    $item_data = [
                        'tender_quotation_id' => $tender_quotation_id,
                        'tender_enquiry_item_id' => $tender_enquiry_item_ids[$idx] ?? 0,
                        'category_id' => $category_ids[$idx] ?? 0,
                        'item_id' => $item_ids[$idx] ?? 0,
                        'item_desc' => $item_descs[$idx] ?? '',
                        'uom' => $uoms[$idx] ?? '',
                        'qty' => $qtys[$idx] ?? 0,
                        'gst' => $gsts[$idx] ?? 0,
                        'amount' => $amounts[$idx] ?? 0,
                        'status' => 'Active',
                        'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                        'created_date' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('tender_quotation_item_info', $item_data);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error saving data. Please try again.');
            } else {
                $this->session->set_flashdata('success', 'Tender Quotation saved successfully.');
            }
            redirect('tender-quotation-po/');
        }


        $this->load->library('pagination');

        $this->db->where('status != ', 'Delete');
        $this->db->from('vendor_rate_enquiry_info');
        $data['total_records'] = $cnt = $this->db->count_all_results();

        $data['sno'] = $this->uri->segment(2, 0);

        $config['base_url'] = trim(site_url('tender-quotation-po-list') . '/' . $this->uri->segment(2, 0));
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

        $data['company_opt'] = [];
        $data['customer_opt'] = [];
        $data['vendor_opt'] = [];
        $data['tender_enquiry_opt'] = [];
        $data['gst_opt'] = [];

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
            SELECT company_id,company_name
            FROM company_info
             WHERE status = 'Active' 
            ORDER BY company_name ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
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
        foreach ($query->result_array() as $row) {
            $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['tender_enquiry_id'] . ' -> ' . $row['enquiry_no'] . ' -> ' . $row['company_name'] . ' -> ' . $row['customer_name'];
        }

        $sql = "
            SELECT gst_id, gst_percentage
            FROM gst_info 
            WHERE status = 'Active'
            ORDER BY gst_percentage ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['gst_opt'][$row['gst_id']] = $row['gst_percentage'];
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
        $this->load->view('page/tender/tender-quotation-po', $data);
    }


    public function customer_tender_po_add()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if ($this->session->userdata(SESS_HD . 'level') != 'Admin' && $this->session->userdata(SESS_HD . 'level') != 'Staff') {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'tender/customer-tender-po-add.inc';
        $data['title'] = 'Tender PO';

        if ($this->input->post('mode') == 'Add') {
            $this->db->trans_start();

            /* ---- 1. Header record ---- */
            $header = [
                'company_id' => $this->input->post('srch_company_id'),
                'customer_id' => $this->input->post('srch_customer_id'),
                'tender_enquiry_id' => $this->input->post('srch_tender_enquiry_id'),
                'tender_quotation_id' => $this->input->post('srch_quotation_no'),
                'our_po_no' => $this->input->post('our_po_no'),
                'customer_po_no' => $this->input->post('customer_po_no'),
                'po_date' => $this->input->post('po_date'),
                'po_received_date' => $this->input->post('po_received_date'),
                'delivery_date' => $this->input->post('delivery_date'),
                'remarks' => $this->input->post('remarks'),
                'terms' => $this->input->post('terms'),
                'po_status' => $this->input->post('po_status'),
                'status' => 'Active',
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('customer_tender_po_info', $header);
            $tender_po_id = $this->db->insert_id();

            /* ---- 2. ONLY SELECTED items ---- */
            $selected_idxs = $this->input->post('selected_items') ?? [];

            if (!empty($selected_idxs)) {
                $tender_quotation_item_ids = $this->input->post('tender_quotation_item_id') ?? [];
                $category_ids = $this->input->post('category_id') ?? [];
                $item_ids = $this->input->post('item_id') ?? [];
                $item_descs = $this->input->post('item_desc') ?? [];
                $uoms = $this->input->post('uom') ?? [];
                $qtys = $this->input->post('qty') ?? [];
                $rates = $this->input->post('rate') ?? [];
                $gsts = $this->input->post('gst') ?? [];
                $amounts = $this->input->post('amount') ?? [];

                foreach ($selected_idxs as $idx) {
                    $item_data = [
                        'tender_po_id' => $tender_po_id,
                        'tender_quotation_item_id' => $tender_quotation_item_ids[$idx] ?? 0,
                        'category_id' => $category_ids[$idx] ?? 0,
                        'item_id' => $item_ids[$idx] ?? 0,
                        'item_desc' => $item_descs[$idx] ?? '',
                        'uom' => $uoms[$idx] ?? '',
                        'qty' => $qtys[$idx] ?? 0,
                        'rate' => $rates[$idx] ?? 0,
                        'gst' => $gsts[$idx] ?? 0,
                        'amount' => $amounts[$idx] ?? 0,
                        'po_itm_status' => 'Pending',
                        'status' => 'Active',
                        'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                        'created_date' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('tender_po_item_info', $item_data);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error saving data. Please try again.');
            } else {
                $this->session->set_flashdata('success', 'Tender PO saved successfully.');
            }
            redirect('customer-tender-po-list/');
        }

        // Get all companies
        $sql = "SELECT company_id, company_name FROM company_info WHERE status = 'Active' ORDER BY company_name ASC";
        $query = $this->db->query($sql);
        $data['company_opt'] = [];
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }

        // Get GST options
        $sql = "SELECT gst_id, gst_percentage FROM gst_info WHERE status = 'Active' ORDER BY gst_percentage ASC";
        $query = $this->db->query($sql);
        $data['gst_opt'] = [];
        foreach ($query->result_array() as $row) {
            $data['gst_opt'][$row['gst_id']] = $row['gst_percentage'];
        }

        $this->load->view('page/tender/customer-tender-po-add', $data);
    }


    public function customer_tender_po_edit($tender_po_id)
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();
        if ($this->session->userdata(SESS_HD . 'level') != 'Admin' && $this->session->userdata(SESS_HD . 'level') != 'Staff') {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }
        $data['js'] = 'tender/customer-tender-po-edit.inc';
        $data['title'] = 'Edit Tender PO';

        if ($this->input->post('mode') == 'Edit') {
            $this->db->trans_start();

            /* ---- 1. UPDATE Header record ---- */
            $header = [
                'company_id' => $this->input->post('srch_company_id'),
                'customer_id' => $this->input->post('srch_customer_id'),
                'tender_enquiry_id' => $this->input->post('srch_tender_enquiry_id'),
                'tender_quotation_id' => $this->input->post('tender_quotation_id'),
                'our_po_no' => $this->input->post('our_po_no'),
                'customer_po_no' => $this->input->post('customer_po_no'),
                'po_date' => $this->input->post('po_date'),
                'po_received_date' => $this->input->post('po_received_date'),
                'delivery_date' => $this->input->post('delivery_date'),
                'remarks' => $this->input->post('remarks'),
                'terms' => $this->input->post('terms'),
                'po_status' => $this->input->post('po_status'),
                'status' => $this->input->post('status'),
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s')
            ];
            $this->db->where('tender_po_id', $tender_po_id);
            $this->db->update('customer_tender_po_info', $header);

            /* ---- 2. Handle Items (Insert/Update/Delete) ---- */
            $selected_idxs = $this->input->post('selected_items') ?? [];
            $po_item_ids_to_keep = [];

            // Preload all tender_quotation_item_ids posted (may be sparse due to checkboxes)
            $tender_quotation_item_ids = $this->input->post('tender_quotation_item_id') ?: [];
            $category_ids = $this->input->post('category_id') ?: [];
            $item_ids = $this->input->post('item_id') ?: [];
            $item_descs = $this->input->post('item_desc') ?: [];
            $uoms = $this->input->post('uom') ?: [];
            $qtys = $this->input->post('qty') ?: [];
            $rates = $this->input->post('rate') ?: [];
            $gsts = $this->input->post('gst') ?: [];
            $amounts = $this->input->post('amount') ?: [];

            foreach ($selected_idxs as $idx) {
                // Get existing PO item ID if it exists (from hidden field)
                $existing_po_item_id = $this->input->post('tender_po_item_id_' . $idx);

                if ($existing_po_item_id) {
                    // UPDATE existing item
                    $item_data = [
                        'rate' => $rates[$idx] ?? 0,
                        'gst' => $gsts[$idx] ?? 0,
                        'amount' => $amounts[$idx] ?? 0,
                        'item_desc' => $item_descs[$idx] ?? '',
                        'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                        'updated_date' => date('Y-m-d H:i:s')
                    ];
                    $this->db->where('tender_po_item_id', $existing_po_item_id);
                    $this->db->update('tender_po_item_info', $item_data);
                    $po_item_ids_to_keep[] = $existing_po_item_id;
                } else {
                    // INSERT new item
                    $item_data = [
                        'tender_po_id' => $tender_po_id,
                        'tender_quotation_item_id' => $tender_quotation_item_ids[$idx] ?? 0,
                        'category_id' => $category_ids[$idx] ?? 0,
                        'item_id' => $item_ids[$idx] ?? 0,
                        'item_desc' => $item_descs[$idx] ?? '',
                        'uom' => $uoms[$idx] ?? '',
                        'qty' => $qtys[$idx] ?? 0,
                        'rate' => $rates[$idx] ?? 0,
                        'gst' => $gsts[$idx] ?? 0,
                        'amount' => $amounts[$idx] ?? 0,
                        'po_itm_status' => 'Pending',
                        'status' => 'Active',
                        'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                        'created_date' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('tender_po_item_info', $item_data);
                    $new_id = $this->db->insert_id();
                    $po_item_ids_to_keep[] = $new_id;
                }
            }

            // Soft-delete deselected items
            $this->db->where('tender_po_id', $tender_po_id);
            if (!empty($po_item_ids_to_keep)) {
                $this->db->where_not_in('tender_po_item_id', $po_item_ids_to_keep);
            }
            $this->db->where('status !=', 'Delete');
            $this->db->update('tender_po_item_info', ['status' => 'Delete']);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error updating data. Please try again.');
            } else {
                $this->session->set_flashdata('success', ' Tender PO updated successfully.');
            }
            redirect('customer-tender-po-list');
        }

        // ———————— Load dropdown options ————————
        $sql = "SELECT company_id, company_name FROM company_info WHERE status = 'Active' ORDER BY company_name ASC";
        $query = $this->db->query($sql);
        $data['company_opt'] = array('' => 'Select');
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }

        $sql = "SELECT customer_id, customer_name FROM customer_info WHERE status = 'Active' ORDER BY customer_name ASC";
        $query = $this->db->query($sql);
        $data['customer_opt'] = array('' => 'Select');
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }

        $sql = "SELECT 
                a.tender_enquiry_id,
                get_tender_info(a.tender_enquiry_id) AS tender_details,
                b.company_name,
                c.customer_name 
            FROM tender_enquiry_info AS a 
            LEFT JOIN company_info b ON a.company_id = b.company_id AND b.status='Active' 
            LEFT JOIN customer_info c ON a.customer_id = c.customer_id AND c.status='Active' 
            WHERE a.status = 'Active'
              and a.tender_status = 'Won' 
            ORDER BY a.tender_enquiry_id, tender_details ASC";
        $query = $this->db->query($sql);
        $data['tender_enquiry_opt'] = array('' => 'Select');
        foreach ($query->result_array() as $row) {
            $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['tender_details'];
        }

        $sql = "SELECT gst_id, gst_percentage FROM gst_info WHERE status = 'Active' ORDER BY gst_percentage ASC";
        $query = $this->db->query($sql);
        $data['gst_opt'] = [];
        foreach ($query->result_array() as $row) {
            $data['gst_opt'][$row['gst_id']] = $row['gst_percentage'];
        }

        // ———————— Load header ————————
        $data['header'] = $this->db
            ->where('tender_po_id', $tender_po_id)
            ->where('status !=', 'Delete')
            ->get('customer_tender_po_info')
            ->row_array();

        if (!$data['header']) {
            show_error('PO not found', 404);
        }

        // ———————— CORE FIX: Merge Quotation Items + Existing PO Items ————————
        $tender_quotation_id = $data['header']['tender_quotation_id'];

        // Fetch ALL items from the selected quotation
        $quotation_items = [];
        if ($tender_quotation_id) {
            $sql = "SELECT 
                    tqi.tender_quotation_item_id,
                    tqi.category_id,
                    ci.category_name,
                    tqi.item_id,
                    ii.item_name,
                    tqi.item_desc,
                    ii.item_code,
                    tqi.uom,
                    tqi.qty,
                    tqi.rate AS default_rate,
                    tqi.gst AS default_gst,
                    tqi.amount AS default_amount
                FROM tender_quotation_item_info tqi
                LEFT JOIN category_info ci ON tqi.category_id = ci.category_id
                LEFT JOIN item_info ii ON tqi.item_id = ii.item_id
                WHERE tqi.tender_quotation_id = ? AND tqi.status = 'Active'
                ORDER BY tqi.tender_quotation_item_id ASC";
            $query = $this->db->query($sql, [$tender_quotation_id]);
            $quotation_items = $query->result_array();
        }

        // Fetch already saved items for this PO
        $saved_items = $this->db
            ->where('tender_po_id', $tender_po_id)
            ->where('status !=', 'Delete')
            ->get('tender_po_item_info')
            ->result_array();

        // Build map: tender_quotation_item_id → saved item data
        $saved_map = [];
        foreach ($saved_items as $item) {
            $saved_map[$item['tender_quotation_item_id']] = $item;
        }

        // Merge: For each quotation item, attach saved data (if exists)
        $data['merged_items'] = [];
        foreach ($quotation_items as $qi) {
            $tqi_id = $qi['tender_quotation_item_id'];
            $is_saved = isset($saved_map[$tqi_id]);

            $merged = [
                // Quotation defaults
                'tender_quotation_item_id' => $qi['tender_quotation_item_id'],
                'category_id' => $qi['category_id'],
                'category_name' => $qi['category_name'],
                'item_id' => $qi['item_id'],
                'item_name' => $qi['item_name'],
                'item_code' => $qi['item_code'],
                'item_desc' => $is_saved ? $saved_map[$tqi_id]['item_desc'] : $qi['item_desc'],
                'uom' => $qi['uom'],
                'qty' => $qi['qty'],
                'default_rate' => $qi['default_rate'],
                'default_gst' => $qi['default_gst'],
                'default_amount' => $qi['default_amount'],
                // Saved overrides (if exists)
                'saved' => $is_saved,
                'tender_po_item_id' => $is_saved ? $saved_map[$tqi_id]['tender_po_item_id'] : null,
                'rate' => $is_saved ? $saved_map[$tqi_id]['rate'] : $qi['default_rate'],
                'gst' => $is_saved ? $saved_map[$tqi_id]['gst'] : $qi['default_gst'],
                'amount' => $is_saved ? $saved_map[$tqi_id]['amount'] : $qi['default_amount'],
            ];
            $data['merged_items'][] = $merged;
        }

        // Optional: Also include items in PO not from this quotation (legacy), but usually not needed.
        // You may skip unless multi-quotation POs are allowed.

        $this->load->view('page/tender/customer-tender-po-edit', $data);
    }

    public function customer_tender_po_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data = array();
        $data['js'] = 'tender/customer-tender-po-list.inc';
        $data['s_url'] = 'customer-tender-po-list';
        $data['title'] = ' Tender PO List';

        $where = "1 = 1";
        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');
            $this->session->set_userdata('srch_from_date', $this->input->post('srch_from_date'));
            $this->session->set_userdata('srch_to_date', $this->input->post('srch_to_date'));
        } elseif ($this->session->userdata('srch_from_date')) {
            $data['srch_from_date'] = $srch_from_date = $this->session->userdata('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->session->userdata('srch_to_date');
        } else {
            $data['srch_from_date'] = $srch_from_date = date('Y-m-d');
            $data['srch_to_date'] = $srch_to_date = date('Y-m-d');
        }

        // Company Filter
        if ($this->input->post('srch_company_id') !== null) {
            $data['srch_company_id'] = $srch_company_id = $this->input->post('srch_company_id');
            $this->session->set_userdata('srch_po_company_id', $srch_company_id);
        } elseif ($this->session->userdata('srch_po_company_id')) {
            $data['srch_company_id'] = $srch_company_id = $this->session->userdata('srch_po_company_id');
        } else {
            $data['srch_company_id'] = $srch_company_id = '';
        }
        if (!empty($srch_company_id)) {
            $where .= " AND a.company_id = '" . $this->db->escape_str($srch_company_id) . "'";
        }

        // Customer Filter
        if ($this->input->post('srch_customer_id') !== null) {
            $data['srch_customer_id'] = $srch_customer_id = $this->input->post('srch_customer_id');
            $this->session->set_userdata('srch_po_customer_id', $srch_customer_id);
        } elseif ($this->session->userdata('srch_po_customer_id')) {
            $data['srch_customer_id'] = $srch_customer_id = $this->session->userdata('srch_po_customer_id');
        } else {
            $data['srch_customer_id'] = $srch_customer_id = '';
        }
        if (!empty($srch_customer_id)) {
            $where .= " AND a.customer_id = '" . $this->db->escape_str($srch_customer_id) . "'";
        }

        if ($this->input->post('srch_tender_enquiry_id') !== null) {
            $data['srch_tender_enquiry_id'] = $srch_tender_enquiry_id = $this->input->post('srch_tender_enquiry_id');
            $this->session->set_userdata('srch_tender_enquiry_id', $srch_tender_enquiry_id);
        } elseif ($this->session->userdata('srch_tender_enquiry_id')) {
            $data['srch_tender_enquiry_id'] = $srch_tender_enquiry_id = $this->session->userdata('srch_tender_enquiry_id');
        } else {
            $data['srch_tender_enquiry_id'] = $srch_tender_enquiry_id = '';
        }
        if (!empty($srch_tender_enquiry_id)) {
            $where .= " AND a.tender_enquiry_id = '" . $this->db->escape_str($srch_tender_enquiry_id) . "'";
        }

        if ($this->input->post('srch_tender_quotation_id') !== null) {
            $data['srch_tender_quotation_id'] = $srch_tender_quotation_id = $this->input->post('srch_tender_quotation_id');
            $this->session->set_userdata('srch_tender_quotation_id', $srch_tender_quotation_id);
        } elseif ($this->session->userdata('srch_tender_quotation_id')) {
            $data['srch_tender_quotation_id'] = $srch_tender_quotation_id = $this->session->userdata('srch_tender_quotation_id');
        } else {
            $data['srch_tender_quotation_id'] = $srch_tender_quotation_id = '';
        }
        if (!empty($srch_tender_quotation_id)) {
            $where .= " AND a.tender_quotation_id = '" . $this->db->escape_str($srch_tender_quotation_id) . "'";
        }



        // PO Status Filter
        if ($this->input->post('srch_po_status') !== null) {
            $data['srch_po_status'] = $srch_po_status = $this->input->post('srch_po_status');
            $this->session->set_userdata('srch_po_status', $srch_po_status);
        } elseif ($this->session->userdata('srch_po_status')) {
            $data['srch_po_status'] = $srch_po_status = $this->session->userdata('srch_po_status');
        } else {
            $data['srch_po_status'] = $srch_po_status = '';
        }
        if (!empty($srch_po_status) && $srch_po_status !== 'All') {
            $where .= " AND a.po_status = '" . $this->db->escape_str($srch_po_status) . "'";
        }
        $this->db->from('customer_tender_po_info a');
        $this->db->where('a.status !=', 'Delete');
        $this->db->where($where);
        $this->db->where("DATE(a.po_date) BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "'");
        $data['total_records'] = $this->db->count_all_results();


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
                a.tender_po_id,
                a.our_po_no,
                a.customer_po_no,
                a.po_date,
                a.delivery_date,
                a.po_status,
                a.status,
                b.company_name,
                c.customer_name,
                t.enquiry_no,
                tq.quotation_no
            FROM customer_tender_po_info a
            LEFT JOIN company_info b ON a.company_id = b.company_id AND b.status = 'Active'
            LEFT JOIN customer_info c ON a.customer_id = c.customer_id AND c.status = 'Active'
            LEFT JOIN tender_enquiry_info t ON t.tender_enquiry_id = a.tender_enquiry_id AND c.status='Active'
            LEFT JOIN tender_quotation_info tq ON tq.tender_quotation_id = a.tender_quotation_id AND c.status='Active'
            WHERE a.status != 'Delete'
            AND a.po_date BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "' 
            AND $where
            ORDER BY a.tender_po_id DESC
            LIMIT " . $this->uri->segment(2, 0) . ", " . $config['per_page'];

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();

        // === DROPDOWNS ===
        $data['company_opt'] = ['' => 'All'];
        $sql = "SELECT company_id, company_name FROM company_info WHERE status = 'Active' ORDER BY company_name";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }

        $data['customer_opt'] = ['' => 'All'];
        $sql = "SELECT customer_id, customer_name FROM customer_info WHERE status = 'Active' ORDER BY customer_name";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }

        $data['tender_quotation_opt'] = ['' => 'All'];
        $sql = "SELECT tender_quotation_id , quotation_no FROM tender_quotation_info WHERE status = 'Active' ORDER BY tender_quotation_id asc";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['tender_quotation_opt'][$row['tender_quotation_id']] = $row['quotation_no'];
        }

        $data['tender_enquiry_opt'] = ['' => 'All'];
        $sql = "SELECT 
                a.tender_enquiry_id,
                a.enquiry_no,
                b.company_name,
                c.customer_name 
            FROM tender_enquiry_info AS a 
            LEFT JOIN company_info b ON a.company_id = b.company_id AND b.status='Active' 
            LEFT JOIN customer_info c ON a.customer_id = c.customer_id AND c.status='Active' 
            WHERE a.status = 'Active'
            and a.tender_status = 'Won' 
            ORDER BY a.tender_enquiry_id, a.enquiry_no ASC";
        $query = $this->db->query($sql);
        $data['tender_enquiry_opt'] = array('' => 'Select');
        foreach ($query->result_array() as $row) {
            $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['enquiry_no'] . ' → ' . $row['company_name'] . ' → ' . $row['customer_name'];
        }

        $data['po_status_opt'] = [
            '' => 'All',
            'Open' => 'Open',
            'In Progress' => 'In Progress',
            'Completed' => 'Completed',
            'Cancelled' => 'Cancelled',
        ];

        $this->load->view('page/tender/customer-tender-po-list', $data);
    }

    public function tender_invoice_add()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if ($this->session->userdata(SESS_HD . 'level') != 'Admin' && $this->session->userdata(SESS_HD . 'level') != 'Staff') {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'tender/tender-invoice-add.inc';
        $data['title'] = 'Tender Invoice Generator';

        if ($this->input->post('mode') == 'Add') {
            $this->db->trans_start();

            /* ---- 1. Header record ---- */
            $header = [
                'company_id' => $this->input->post('srch_company_id'),
                'customer_id' => $this->input->post('srch_customer_id'),
                'tender_enquiry_id' => $this->input->post('srch_tender_enquiry_id'),
                'tender_po_id' => $this->input->post('srch_tender_po_id'),
                'invoice_no' => $this->input->post('invoice_no'),
                'invoice_date' => $this->input->post('invoice_date'),
                'invoice_status' => $this->input->post('invoice_status'),
                'remarks' => $this->input->post('remarks'),
                'terms' => $this->input->post('terms'),
                'status' => 'Active',
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('tender_enq_invoice_info', $header);
            $tender_enq_invoice_id = $this->db->insert_id();

            /* ---- 2. ONLY SELECTED items ---- */
            $selected_idxs = $this->input->post('selected_items') ?? [];

            if (!empty($selected_idxs)) {
                $tender_po_item_id = $this->input->post('tender_po_item_id') ?? [];
                $category_ids = $this->input->post('category_id') ?? [];
                $item_ids = $this->input->post('item_id') ?? [];
                $item_descs = $this->input->post('item_desc') ?? [];
                $uoms = $this->input->post('uom') ?? [];
                $qtys = $this->input->post('qty') ?? [];
                $rates = $this->input->post('rate') ?? [];
                $gsts = $this->input->post('gst') ?? [];
                $amounts = $this->input->post('amount') ?? [];

                foreach ($selected_idxs as $idx) {
                    $item_data = [
                        'tender_enq_invoice_id' => $tender_enq_invoice_id,
                        'tender_po_item_id' => $tender_po_item_id[$idx] ?? 0,
                        'category_id' => $category_ids[$idx] ?? 0,
                        'item_id' => $item_ids[$idx] ?? 0,
                        'item_desc' => $item_descs[$idx] ?? '',
                        'uom' => $uoms[$idx] ?? '',
                        'qty' => $qtys[$idx] ?? 0,
                        'rate' => $rates[$idx] ?? 0,
                        'gst' => $gsts[$idx] ?? 0,
                        'amount' => $amounts[$idx] ?? 0,
                        'status' => 'Active',
                        'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                        'created_date' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('tender_enq_invoice_item_info', $item_data);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error saving data. Please try again.');
            } else {
                $this->session->set_flashdata('success', 'Tender Invoice added successfully.');
            }
            redirect('tender-invoice-list');
        }

        // Get all companies
        $sql = "SELECT company_id, company_name FROM company_info WHERE status = 'Active' ORDER BY company_name ASC";
        $query = $this->db->query($sql);
        $data['company_opt'] = [];
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }

        // Get GST options
        $sql = "SELECT gst_id, gst_percentage FROM gst_info WHERE status = 'Active' ORDER BY gst_percentage ASC";
        $query = $this->db->query($sql);
        $data['gst_opt'] = [];
        foreach ($query->result_array() as $row) {
            $data['gst_opt'][$row['gst_id']] = $row['gst_percentage'];
        }

        $this->load->view('page/tender/tender-invoice-add', $data);
    }

    public function tender_po_invoice_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data = array();
        $data['js'] = 'tender/tender-po-invoice-list.inc';
        $data['s_url'] = 'tender-po-invoice-list';
        $data['title'] = ' Tender PO Invoice List';

        $where = "1 = 1";
        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');
            $this->session->set_userdata('srch_from_date', $this->input->post('srch_from_date'));
            $this->session->set_userdata('srch_to_date', $this->input->post('srch_to_date'));
        } elseif ($this->session->userdata('srch_from_date')) {
            $data['srch_from_date'] = $srch_from_date = $this->session->userdata('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->session->userdata('srch_to_date');
        } else {
            $data['srch_from_date'] = $srch_from_date = date('Y-m-d');
            $data['srch_to_date'] = $srch_to_date = date('Y-m-d');
        }

        // Company Filter
        if ($this->input->post('srch_company_id') !== null) {
            $data['srch_company_id'] = $srch_company_id = $this->input->post('srch_company_id');
            $this->session->set_userdata('srch_po_company_id', $srch_company_id);
        } elseif ($this->session->userdata('srch_po_company_id')) {
            $data['srch_company_id'] = $srch_company_id = $this->session->userdata('srch_po_company_id');
        } else {
            $data['srch_company_id'] = $srch_company_id = '';
        }
        if (!empty($srch_company_id)) {
            $where .= " AND a.company_id = '" . $this->db->escape_str($srch_company_id) . "'";
        }

        // Customer Filter
        if ($this->input->post('srch_customer_id') !== null) {
            $data['srch_customer_id'] = $srch_customer_id = $this->input->post('srch_customer_id');
            $this->session->set_userdata('srch_po_customer_id', $srch_customer_id);
        } elseif ($this->session->userdata('srch_po_customer_id')) {
            $data['srch_customer_id'] = $srch_customer_id = $this->session->userdata('srch_po_customer_id');
        } else {
            $data['srch_customer_id'] = $srch_customer_id = '';
        }
        if (!empty($srch_customer_id)) {
            $where .= " AND a.customer_id = '" . $this->db->escape_str($srch_customer_id) . "'";
        }

        if ($this->input->post('srch_tender_enquiry_id') !== null) {
            $data['srch_tender_enquiry_id'] = $srch_tender_enquiry_id = $this->input->post('srch_tender_enquiry_id');
            $this->session->set_userdata('srch_tender_enquiry_id', $srch_tender_enquiry_id);
        } elseif ($this->session->userdata('srch_tender_enquiry_id')) {
            $data['srch_tender_enquiry_id'] = $srch_tender_enquiry_id = $this->session->userdata('srch_tender_enquiry_id');
        } else {
            $data['srch_tender_enquiry_id'] = $srch_tender_enquiry_id = '';
        }
        if (!empty($srch_tender_enquiry_id)) {
            $where .= " AND a.tender_enquiry_id = '" . $this->db->escape_str($srch_tender_enquiry_id) . "'";
        }

        if ($this->input->post('srch_tender_po_id') !== null) {
            $data['srch_tender_po_id'] = $srch_tender_po_id = $this->input->post('srch_tender_po_id');
            $this->session->set_userdata('srch_tender_po_id', $srch_tender_po_id);
        } elseif ($this->session->userdata('srch_tender_po_id')) {
            $data['srch_tender_po_id'] = $srch_tender_po_id = $this->session->userdata('srch_tender_po_id');
        } else {
            $data['srch_tender_po_id'] = $srch_tender_po_id = '';
        }
        if (!empty($srch_tender_po_id)) {
            $where .= " AND a.tender_quotation_id = '" . $this->db->escape_str($srch_tender_po_id) . "'";
        }

        $this->db->from('tender_enq_invoice_info as a');
        $this->db->where('a.status !=', 'Delete');
        $this->db->where($where);
        $this->db->where("DATE(a.invoice_date) BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "'");
        $data['total_records'] = $this->db->count_all_results();


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
               a.*,
               get_tender_info(a.tender_enquiry_id) as tender_details,
               b.company_name,
               c.customer_name 
            FROM tender_enq_invoice_info a
            LEFT JOIN company_info b ON a.company_id = b.company_id AND b.status = 'Active'
            LEFT JOIN customer_info c ON a.customer_id = c.customer_id AND c.status = 'Active' 
            WHERE a.status != 'Delete'
            AND a.invoice_date BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "' 
            AND $where
            ORDER BY a.tender_po_id DESC
            LIMIT " . $this->uri->segment(2, 0) . ", " . $config['per_page'];

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();

        // === DROPDOWNS ===
        $data['company_opt'] = ['' => 'All'];
        $sql = "SELECT company_id, company_name FROM company_info WHERE status = 'Active' ORDER BY company_name";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }

        $data['customer_opt'] = ['' => 'All'];
        $sql = "SELECT customer_id, customer_name FROM customer_info WHERE status = 'Active' ORDER BY customer_name";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }

        $data['tender_quotation_opt'] = ['' => 'All'];
        $sql = "SELECT tender_quotation_id , quotation_no FROM tender_quotation_info WHERE status = 'Active' ORDER BY tender_quotation_id asc";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['tender_quotation_opt'][$row['tender_quotation_id']] = $row['quotation_no'];
        }

        $data['tender_enquiry_opt'] = ['' => 'All'];
        $sql = "SELECT 
                a.tender_enquiry_id,
                a.enquiry_no,
                b.company_name,
                c.customer_name 
            FROM tender_enquiry_info AS a 
            LEFT JOIN company_info b ON a.company_id = b.company_id AND b.status='Active' 
            LEFT JOIN customer_info c ON a.customer_id = c.customer_id AND c.status='Active' 
            WHERE a.status = 'Active'
            and a.tender_status = 'Won' 
            ORDER BY a.tender_enquiry_id, a.enquiry_no ASC";
        $query = $this->db->query($sql);
        $data['tender_enquiry_opt'] = array('' => 'Select');
        foreach ($query->result_array() as $row) {
            $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['enquiry_no'] . ' → ' . $row['company_name'] . ' → ' . $row['customer_name'];
        }
        $this->load->view('page/tender/tender-po-invoice-list', $data);
    }


    public function tender_po_invoice_edit($tender_enq_invoice_id = null)
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if ($this->session->userdata(SESS_HD . 'level') != 'Admin' && $this->session->userdata(SESS_HD . 'level') != 'Staff') {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'tender/tender-po-invoice-edit.inc';
        $data['title'] = 'Edit Tender PO Invoice';

        if ($this->input->post('mode') == 'Edit') {

            $this->db->trans_start();

            /* ----------------- 1. UPDATE HEADER ----------------- */
            $header = [
                'company_id' => $this->input->post('srch_company_id'),
                'customer_id' => $this->input->post('srch_customer_id'),
                'tender_enquiry_id' => $this->input->post('srch_tender_enquiry_id'),
                'tender_po_id' => $this->input->post('srch_tender_po_id'),
                'invoice_no' => $this->input->post('invoice_no'),
                'invoice_date' => $this->input->post('invoice_date'),
                'invoice_status' => $this->input->post('invoice_status'),
                'terms' => $this->input->post('terms'),
                'remarks' => $this->input->post('remarks'),
                'status' => $this->input->post('status'),
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s'),
            ];

            $tender_enq_invoice_id = $this->input->post('tender_enq_invoice_id');

            $this->db->where('tender_enq_invoice_id', $tender_enq_invoice_id);
            $this->db->update('tender_enq_invoice_info', $header);


            /* ----------------- 2. UPDATE ONLY ITEM ROWS ----------------- */
            $selected_items = $this->input->post('selected_items') ?? [];
            $item_ids_arr = $this->input->post('tender_enq_invoice_item_id') ?? [];

            if (!empty($selected_items)) {

                $tender_po_item_id = $this->input->post('tender_po_item_id') ?? [];
                $category_id = $this->input->post('category_id') ?? [];
                $item_id = $this->input->post('item_id') ?? [];
                $item_desc = $this->input->post('item_desc') ?? [];
                $uom = $this->input->post('uom') ?? [];
                $qty = $this->input->post('qty') ?? [];
                $rate = $this->input->post('rate') ?? [];
                $gst = $this->input->post('gst') ?? [];
                $amount = $this->input->post('amount') ?? [];

                foreach ($selected_items as $idx) {

                    $item_data = [
                        'tender_enq_invoice_id' => $tender_enq_invoice_id,
                        'tender_po_item_id' => $tender_po_item_id[$idx] ?? 0,
                        'category_id' => $category_id[$idx] ?? 0,
                        'item_id' => $item_id[$idx] ?? 0,
                        'item_desc' => $item_desc[$idx] ?? '',
                        'uom' => $uom[$idx] ?? '',
                        'qty' => $qty[$idx] ?? 0,
                        'rate' => $rate[$idx] ?? 0,
                        'gst' => $gst[$idx] ?? 0,
                        'amount' => $amount[$idx] ?? 0,
                        'status' => 'Active',
                        'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                        'updated_date' => date('Y-m-d H:i:s'),
                    ];

                    /* --- IMPORTANT: update using row-wise item ID --- */
                    $this->db->where('tender_enq_invoice_item_id', $item_ids_arr[$idx]);
                    $this->db->update('tender_enq_invoice_item_info', $item_data);
                }
            }

            /* ----------------- COMPLETE TRANSACTION ----------------- */
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error updating Vendor Inward.');
            } else {
                $this->session->set_flashdata('success', 'Vendor Inward updated successfully.');
            }

            redirect('tender-invoice-list/');
        }



        $this->load->library('pagination');

        $this->db->where('status != ', 'Delete');
        $this->db->from('vendor_rate_enquiry_info');
        $data['total_records'] = $cnt = $this->db->count_all_results();

        $data['sno'] = $this->uri->segment(2, 0);

        $config['base_url'] = trim(site_url('tender-quotation-list') . '/' . $this->uri->segment(2, 0));
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

        $data['company_opt'] = [];
        $data['customer_opt'] = [];
        $data['vendor_opt'] = [];
        $data['tender_enquiry_opt'] = [];
        $data['gst_opt'] = [];

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
            SELECT company_id,company_name
            FROM company_info
            WHERE status = 'Active' 
            ORDER BY company_name ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }

        $sql = "
            SELECT 
            a.tender_enquiry_id, 
            get_tender_info(a.tender_enquiry_id) tender_details
            FROM tender_enquiry_info AS a 
            LEFT JOIN company_info b ON a.company_id = b.company_id AND b.status='Active' 
            LEFT JOIN customer_info c ON a.customer_id = c.customer_id AND c.status='Active' 
            WHERE a.status = 'Active'
            and a.tender_status = 'Won' 
            ORDER BY a.tender_enquiry_id ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['tender_details'];
        }

        $sql = "
            SELECT gst_id, gst_percentage
            FROM gst_info 
            WHERE status = 'Active'
            ORDER BY gst_percentage ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['gst_opt'][$row['gst_id']] = $row['gst_percentage'];
        }

        $sql = "
              SELECT 
                a.tender_po_id,
                a.customer_po_no
            FROM 
                customer_tender_po_info a
            LEFT JOIN tender_enquiry_info b 
                ON a.tender_enquiry_id = b.tender_enquiry_id AND b.status='Active'
            LEFT JOIN company_info c 
                ON a.company_id = c.company_id AND c.status='Active'
            LEFT JOIN customer_info d 
                ON a.customer_id = d.customer_id AND d.status='Active'
            WHERE 
                a.status='Active'
               
            ORDER BY 
                a.tender_po_id DESC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['tender_po_opt'][$row['tender_po_id']] = $row['customer_po_no'];
        }
        /* ---- Load existing record for edit ---- */
        $data['header'] = $this->db->where('tender_enq_invoice_id ', $tender_enq_invoice_id)->get('tender_enq_invoice_info')->row_array();
        $data['item_list'] = [];

        $sql = "
            SELECT 
                a.*, 
                b.*
            FROM tender_enq_invoice_item_info AS a
            LEFT JOIN item_info AS b 
                ON a.item_id = b.item_id AND b.status = 'Active'
            WHERE 
                a.status = 'Active'
                AND a.tender_enq_invoice_id = ?
        ";

        $query = $this->db->query($sql, [$tender_enq_invoice_id]);
        $data['item_list'] = $query->result_array();


        $data['pagination'] = $this->pagination->create_links();
        $this->load->view('page/tender/tender-po-invoice-edit', $data);
    }

    public function tender_po_invoice_print($tender_enq_invoice_id = 0)
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        if (!$tender_enq_invoice_id) {
            show_404();
        }

        // === MAIN RECORD ===
        $sql = "
         SELECT
            tei.*,
            c.customer_name,
            c.address,
            c.country AS customer_country,
            ci.company_name AS our_company,
            te.enquiry_no AS tender_enquiry_no,
            tei.invoice_no,
            ci.ltr_header_img
        FROM
            tender_enq_invoice_info as  tei
        LEFT JOIN customer_info c ON
            tei.customer_id = c.customer_id AND c.status = 'Active'
        LEFT JOIN company_info ci ON
            tei.company_id = ci.company_id AND ci.status = 'Active'
        LEFT JOIN tender_enquiry_info te ON
            tei.tender_enquiry_id = te.tender_enquiry_id AND te.status = 'Active'
        WHERE
            tei.tender_enq_invoice_id = ? AND tei.status != 'Delete'
        ";
        $query = $this->db->query($sql, [$tender_enq_invoice_id]);
        $data['record'] = $query->row_array();

        if (!$data['record']) {
            show_404();
        }

        // === ITEMS WITH RATE CALCULATION ===
        $sql = "
        SELECT 
            teni.*,
            cat.category_name,
            item.item_name,
            item.item_description,
            item.item_code,
            item.uom AS item_uom
        FROM tender_enq_invoice_item_info as teni
        LEFT JOIN category_info cat ON teni.category_id = cat.category_id
        LEFT JOIN item_info item ON teni.item_id = item.item_id
        WHERE teni.tender_enq_invoice_id = ?
          AND teni.status='Active'
        ORDER BY teni.tender_enq_invoice_item_id
    ";
        $query = $this->db->query($sql, [$tender_enq_invoice_id]);
        $items = $query->result_array();

        $data['items'] = [];
        $gst_summary = [];

        foreach ($items as $item) {
            $qty = floatval($item['qty']);
            $gst = floatval($item['gst']);
            $amount = floatval($item['amount']);
            $rate = floatval($item['rate']);


            // === GST Amount ===
            $gst_amount = $amount - ($qty * $rate);

            // === Store in item ===
            $item['rate'] = $rate;
            $item['gst_amount'] = $gst_amount;
            $item['base_amount'] = $qty * $rate;

            $data['items'][] = $item;

            // === GST Summary ===
            $gst_key = number_format($gst, 2);
            if (!isset($gst_summary[$gst_key])) {
                $gst_summary[$gst_key] = ['gst' => $gst, 'base' => 0, 'gst_amount' => 0];
            }
            $gst_summary[$gst_key]['base'] += $qty * $rate;
            $gst_summary[$gst_key]['gst_amount'] += $gst_amount;
        }

        $data['gst_summary'] = $gst_summary;
        $data['grand_total'] = array_sum(array_column($data['items'], 'base_amount'));
        $data['total_gst'] = array_sum(array_column($data['items'], 'gst_amount'));
        $data['final_total'] = $data['grand_total'] + $data['total_gst'];

        $this->load->view('page/tender/tender-po-invoice-print', $data);
    }

    public function get_tender_invoice_po_no_load()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $tender_enquiry_id = $this->input->post('srch_tender_enquiry_id');

        $sql = "
            SELECT 
                a.tender_po_id,
                a.customer_po_no
            FROM 
                customer_tender_po_info a
            LEFT JOIN tender_enquiry_info b 
                ON a.tender_enquiry_id = b.tender_enquiry_id AND b.status='Active'
            LEFT JOIN company_info c 
                ON a.company_id = c.company_id AND c.status='Active'
            LEFT JOIN customer_info d 
                ON a.customer_id = d.customer_id AND d.status='Active'
            WHERE 
                a.status='Active'
                AND a.tender_enquiry_id = ?
            ORDER BY 
                a.tender_po_id DESC
        ";

        $query = $this->db->query($sql, [$tender_enquiry_id]);
        echo json_encode($query->result_array());
    }


    public function get_quotations_by_customer()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $company_id = $this->input->post('company_id');
        $customer_id = $this->input->post('customer_id');

        if (empty($company_id) || empty($customer_id)) {
            echo json_encode([]);
            return;
        }

        $sql = "
            SELECT 
                tq.tender_quotation_id,
                tq.quotation_no,
                tq.quote_date,
                te.enquiry_no,
                tq.quotation_no as display
            FROM tender_quotation_info tq
            LEFT JOIN tender_enquiry_info te ON tq.tender_enquiry_id = te.tender_enquiry_id
            WHERE tq.company_id = ?
                AND tq.customer_id = ?
                AND tq.quotation_status = 'Won'
                AND tq.status = 'Active'
            ORDER BY tq.quote_date DESC";

        $query = $this->db->query($sql, [$company_id, $customer_id]);
        $result = $query->result_array();
        echo json_encode($result);
    }

    public function get_tender_po_invoice_load_items()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $tender_po_id = $this->input->post('tender_po_id');

        if (empty($tender_po_id)) {
            echo json_encode([]);
            return;
        }

        $sql = "
           SELECT 
                tpo.*,
                ii.item_code,
                ii.item_id,
                ci.category_id
            FROM tender_po_item_info AS tpo
            LEFT JOIN category_info AS ci ON tpo.category_id = ci.category_id
            LEFT JOIN item_info AS ii ON tpo.item_id = ii.item_id
            WHERE 
                tpo.tender_po_id = ?
                AND tpo.status = 'Active'
            ORDER BY 
                tpo.tender_po_item_id ASC
        ";

        $query = $this->db->query($sql, [$tender_po_id]);
        $result = $query->result_array();
        echo json_encode($result);
    }
    public function get_quotation_items()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $tender_quotation_id = $this->input->post('tender_quotation_id');

        if (empty($tender_quotation_id)) {
            echo json_encode([]);
            return;
        }

        $sql = "
            SELECT 
                tqi.tender_quotation_item_id,
                tqi.category_id,
                tqi.item_id,
                ci.category_name,
                ii.item_name,
                tqi.item_desc,
                ii.item_code,
                tqi.uom,
                tqi.qty,
                tqi.rate,
                tqi.gst,
                tqi.amount
            FROM tender_quotation_item_info tqi
            LEFT JOIN category_info ci ON tqi.category_id = ci.category_id
            LEFT JOIN item_info ii ON tqi.item_id = ii.item_id
            WHERE tqi.tender_quotation_id = ?
                AND tqi.status = 'Active'
            ORDER BY tqi.tender_quotation_item_id ASC";

        $query = $this->db->query($sql, [$tender_quotation_id]);
        $result = $query->result_array();
        echo json_encode($result);
    }


    public function get_data()
    {
        $table = $this->input->post('tbl');
        $rec_id = $this->input->post('id');
        $rec_list = array();

        if ($table == 'get-company-customer-list') {
            $query = $this->db->query("
                SELECT c.customer_id, c.customer_name
                FROM project_info p
                LEFT JOIN customer_info c ON c.customer_id = p.customer_id
                WHERE p.company_id = ? AND p.status = 'Active' AND c.status = 'Active'
                GROUP BY c.customer_id ORDER BY c.customer_name
            ", [$rec_id]);
            $rec_list = $query->result_array();
        }

        if ($table == 'get-category-item-list') {
            $query = $this->db->query("
                SELECT item_id, item_name
                FROM item_info
                WHERE category_id = ? AND status = 'Active'
                ORDER BY item_name
            ", [$rec_id]);
            $rec_list = $query->result_array();
        }

        if ($table == 'get-uom-desc-from-item') {
            $query = $this->db->query("
                SELECT item_id, uom, item_description, item_name
                FROM item_info
                WHERE item_id = ? AND status = 'Active'
            ", [$rec_id]);
            $rec_list = $query->result_array();
        }

        if ($table == 'get-quotation-with-enquiry') {
            $query = $this->db->query("
                SELECT tender_quotation_id, 
                quotation_no
                FROM tender_quotation_info
                WHERE tender_enquiry_id = ? AND status = 'Active'
            ", [$rec_id]);
            $rec_list = $query->result_array();
        }


        if ($table == 'get-tender-quotation-item-list-rate') {
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

        if ($table == 'get-customer-contacts') {

            $sql = "
            SELECT 
                customer_contact_id, 
                contact_person_name
            FROM customer_contact_info
            WHERE customer_id = ?
              AND status = 'active'
        ";

            $query = $this->db->query($sql, [$rec_id]);

            echo json_encode($query->result());
            return;
        }

        header('Content-Type: application/json');
        echo json_encode($rec_list);
    }


    public function ajax_add_master_inline()
    {
        if ($this->input->post('mode') == 'Add Customer Contact') {

            $data = [
                'customer_id' => $this->input->post('customer_id'),
                'contact_person_name' => $this->input->post('contact_person_name'),
                'mobile' => $this->input->post('mobile'),
                'email' => $this->input->post('email'),
                'address' => $this->input->post('address'),
                'department' => $this->input->post('department'),
                'designation' => $this->input->post('designation'),
                'status' => $this->input->post('status'),
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s'),
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('customer_contact_info', $data);
            $insert_id = $this->db->insert_id();

            // Return response for JS
            echo json_encode([
                'status' => 'success',
                'message' => 'Customer contact added successfully!',
                'id' => $insert_id,
                'name' => $data['contact_person_name']
            ]);
        }
        if ($this->input->post('mode') == 'Add Customer') {

            $data = [
                'customer_name' => $this->input->post('customer_name'),
                'contact_name' => $this->input->post('contact_name'),
                'crno' => $this->input->post('crno'),
                'country' => $this->input->post('country'),
                'address' => $this->input->post('address'),
                'mobile' => $this->input->post('mobile'),
                'mobile_alt' => $this->input->post('mobile_alt'),
                'email' => $this->input->post('email'),
                'gst' => $this->input->post('gst'),
                'remarks' => $this->input->post('remarks'),
                'status' => $this->input->post('status'),
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s'),
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('customer_info', $data);
            $insert_id = $this->db->insert_id();

            // Return response for JS
            echo json_encode([
                'status' => 'success',
                'message' => 'Customer added successfully!',
                'id' => $insert_id,
                'name' => $data['customer_name']
            ]);
        }

        if ($this->input->post('mode') == 'Add Category') {

            $data = [
                'category_name' => $this->input->post('category_name'),
                'status' => $this->input->post('status')
            ];

            $this->db->insert('category_info', $data);
            $insert_id = $this->db->insert_id();

            // Return response for JS
            echo json_encode([
                'status' => 'success',
                'message' => 'Category added successfully!',
                'id' => $insert_id,
                'name' => $data['category_name']
            ]);
        }
        if ($this->input->post('mode') == 'Add Brand') {

            $data = [
                'brand_name' => $this->input->post('brand_name'),
                'status' => $this->input->post('status')
            ];

            $this->db->insert('brand_info', $data);
            $insert_id = $this->db->insert_id();

            // Return response for JS
            echo json_encode([
                'status' => 'success',
                'message' => 'Brand added successfully!',
                'id' => $insert_id,
                'name' => $data['brand_name']
            ]);
        }


        if ($this->input->post('mode') == 'Add Item') {


            $item_name = $this->input->post('item_name');
            $item_image = '';

            // 1. Handle file uploads
            $upload_path = 'Item_doc/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048;

            $this->load->library('upload', $config);

            if (!empty($_FILES['item_image']['name'])) {
                if ($this->upload->do_upload('item_image')) {
                    $upload_data = $this->upload->data();
                    $item_image = $upload_path . $upload_data['file_name']; // Full path saved
                }
            }

            $data = array(
                'category_id' => $this->input->post('category_id'),
                'brand_id' => $this->input->post('brand_id'),
                'item_name' => $item_name,
                'item_description' => $this->input->post('item_description'),
                'uom' => $this->input->post('uom'),
                'hsn_code' => $this->input->post('hsn_code'),
                'item_code' => $this->input->post('item_code'),
                'gst' => $this->input->post('gst'),
                'item_image' => $item_image,
                'status' => $this->input->post('status'),
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s')
            );

            $this->db->insert('item_info', $data);
            $insert_id = $this->db->insert_id();

            // Return response for JS
            echo json_encode([
                'status' => 'success',
                'message' => 'Item added successfully!',
                'id' => $insert_id,
                'name' => $data['item_name']
            ]);
        }
    }


    public function get_tender_enquiries_by_customer()
    {
        $company_id = $this->input->post('company_id');
        $customer_id = $this->input->post('customer_id');

        $sql = "
            SELECT 
                a.tender_enquiry_id,
                get_tender_info(a.tender_enquiry_id) AS tender_details,
                b.company_name,
                c.customer_name 
            FROM tender_enquiry_info AS a
            LEFT JOIN company_info AS b ON a.company_id = b.company_id AND b.status = 'Active'
            LEFT JOIN customer_info AS c ON a.customer_id = c.customer_id AND c.status = 'Active'
            WHERE a.company_id = ? AND a.customer_id = ? AND a.status = 'Active'
            and a.tender_status = 'Won'
            ORDER BY a.tender_enquiry_id, a.enquiry_no ASC
        ";
        $query = $this->db->query($sql, [$company_id, $customer_id]);
        $result = [];
        foreach ($query->result_array() as $row) {
            $result[] = [
                'tender_enquiry_id' => $row['tender_enquiry_id'],
                'display' => $row['tender_details']
            ];
        }
        echo json_encode($result);
    }

    public function item_search()
    {
        $term = $this->input->post('search');

        $this->db->group_start();
        $this->db->like('item_name', $term);
        $this->db->or_like('item_description', $term);
        $this->db->or_like('item_code', $term);
        $this->db->group_end();

        $query = $this->db->get('item_info')->result();

        $data = [];
        foreach ($query as $row) {

            $data[] = [
                'label' => $row->item_code . ' : ' . $row->item_name . '[ ' . substr($row->item_description, 1, 100) . ' ]',       // what user sees
                //'value' => $row->item_code . ' : ' . $row->item_name . '[ ' . substr($row->item_description, 1, 100) . ' ]',        // filled in textbox
                'value' => $row->item_code,        // filled in textbox
                'desc' => $row->item_description,
                'uom' => $row->uom,
                'category_id' => $row->category_id,
                'id' => $row->item_id          // optional
            ];
        }

        echo json_encode($data);
    }



}