<?php
defined('BASEPATH') or exit('No direct script access allowed');

class General extends CI_Controller
{

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    public function index()
    {
        //$this->load->view('page/dashboard');
    }


    public function get_data()
    {
        //if(!$this->session->userdata('zazu_logged_in'))  redirect();

        date_default_timezone_set("Asia/Calcutta");


        $table = $this->input->post('tbl');
        $rec_id = $this->input->post('id');

        if ($table == 'company_info') {
            $query = $this->db->query(" 
                select 
                a.* 
                from company_info as a  
                where a.company_id = '" . $rec_id . "'
            ");

            $rec_list = array();

            foreach ($query->result_array() as $row) {
                $rec_list = $row;
            }

        }
        if ($table == 'customer_contact_info') {
            $query = $this->db->query(" 
                select 
                a.* 
                from customer_contact_info as a  
                where a.customer_contact_id = '" . $rec_id . "'
            ");

            $rec_list = array();

            foreach ($query->result_array() as $row) {
                $rec_list = $row;
            }

        }
        if ($table == 'get-account-head-type') {
            $query = $this->db->query(" 
                select 
                a.account_head_id,
                a.account_head_name 
                from cb_account_head_info as a
                where a.type = '" . $rec_id . "' 
                and a.`status` = 'Active' 
                order by a.account_head_name asc
                 
            ");

            $rec_list = array();

            foreach ($query->result_array() as $row) {
                $rec_list[] = $row;
            }
        }
        if ($table == 'get-sub-account-head') {
            $query = $this->db->query(" 
                select 
                a.sub_account_head_id,
                a.sub_account_head_name
                from cb_sub_account_head_info as a
                where a.account_head_id = '" . $rec_id . "' 
                and a.status='Active'
                order by a.sub_account_head_name asc
                 
            ");

            $rec_list = array();

            foreach ($query->result_array() as $row) {
                $rec_list[] = $row;
            }
        }

        if ($table == 'get_project_with_company') {
            $query = $this->db->query("
              SELECT
                    a.tender_enquiry_id,
                    enquiry_no
                FROM
                    tender_enquiry_info AS a
                LEFT JOIN customer_info AS b
                ON
                    b.customer_id = a.customer_id AND b.status = 'Active'
                WHERE
                    a.company_id = '" . $rec_id . "'
                    AND a.status != 'Delete'
            ");

            $rec_list = $query->result_array();
            echo json_encode($rec_list);
            exit;
        }
        if ($table == 'get_sub_account_headlvl3') {
            $query = $this->db->query(" 
                select 
                a.sub_account_headlvl3_id as id,
                a.sub_account_headlvl3_name as val
                from cb_sub_account_head_lvl3_info as a
                where a.sub_account_head_id = '" . $rec_id . "' 
                and a.status='Active'
                order by a.sub_account_headlvl3_name asc
                 
            ");

            $rec_list = array();

            foreach ($query->result_array() as $row) {
                $rec_list[] = $row;
            }
        }

        if ($table == 'vendor_contact_info') {
            $query = $this->db->query(" 
                select 
                a.* 
                from vendor_contact_info as a  
                where a.vendor_contact_id = '" . $rec_id . "'
            ");

            $rec_list = array();

            foreach ($query->result_array() as $row) {
                $rec_list = $row;
            }

        }

        if ($table == 'category_info') {
            $query = $this->db->query(" 
                select 
                a.* 
                from category_info as a  
                where a.category_id = '" . $rec_id . "'
            ");

            $rec_list = array();

            foreach ($query->result_array() as $row) {
                $rec_list = $row;
            }

        }

        if ($table == 'brand_info') {
            $query = $this->db->query(" 
                select 
                a.* 
                from brand_info as a  
                where a.brand_id = '" . $rec_id . "'
            ");

            $rec_list = array();

            foreach ($query->result_array() as $row) {
                $rec_list = $row;
            }

        }
        if ($table == 'uom_info') {
            $query = $this->db->query(" 
                select 
                a.* 
                from uom_info as a  
                where a.uom_id = '" . $rec_id . "'
            ");

            $rec_list = array();

            foreach ($query->result_array() as $row) {
                $rec_list = $row;
            }

        }
        if ($table == 'gst_info') {
            $query = $this->db->query(" 
                select 
                a.* 
                from gst_info as a  
                where a.gst_id = '" . $rec_id . "'
            ");

            $rec_list = array();

            foreach ($query->result_array() as $row) {
                $rec_list = $row;
            }

        }

        if ($table == 'item_info') {
            $query = $this->db->query(" 
                select 
                a.* 
                from item_info as a  
                where a.item_id = '" . $rec_id . "'
            ");

            $rec_list = array();

            foreach ($query->result_array() as $row) {
                $rec_list = $row;
            }

        }
        if ($table == 'user_login_info') {
            $query = $this->db->query(" 
                select 
                a.* 
                from user_login_info as a  
                where a.user_id = '" . $rec_id . "'
            ");

            $rec_list = array();

            foreach ($query->result_array() as $row) {
                $rec_list = $row;
            }

        }
        if ($table == 'vendor_info') {
            $query = $this->db->query(" 
                select 
                a.* 
                from vendor_info as a  
                where a.vendor_id = '" . $rec_id . "'
            ");

            $rec_list = array();

            foreach ($query->result_array() as $row) {
                $rec_list = $row;
            }

        }
        if ($table == 'customer_info') {
            $query = $this->db->query(" 
                select 
                a.* 
                from customer_info as a  
                where a.customer_id = '" . $rec_id . "'
            ");

            $rec_list = array();

            foreach ($query->result_array() as $row) {
                $rec_list = $row;
            }

        }
        if ($table == 'currency_info') {
            $query = $this->db->query(" 
                select 
                a.* 
                from currencies_info as a  
                where a.currency_id = '" . $rec_id . "'
            ");

            $rec_list = array();

            foreach ($query->result_array() as $row) {
                $rec_list = $row;
            }

        }
        if ($table == 'country_info') {
            $query = $this->db->query(" 
                select 
                a.* 
                from country_info as a  
                where a.country_id = '" . $rec_id . "'
            ");

            $rec_list = array();

            foreach ($query->result_array() as $row) {
                $rec_list = $row;
            }

        }

        $this->db->close();

        header('Content-Type: application/x-json; charset=utf-8');

        echo (json_encode($rec_list));
    }

    public function delete_record()
    {

        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        date_default_timezone_set("Asia/Calcutta");


        $table = $this->input->post('tbl');
        $rec_id = $this->input->post('id');


        if ($table == 'country_info') {
             $this->db->where('country_id', $rec_id);
            $this->db->update('country_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }
        if ($table == 'vendor_purchase_invoice_info') {
             $this->db->where('vendor_purchase_invoice_id ', $rec_id);
            $this->db->update('vendor_purchase_invoice_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }
        if ($table == 'tender_enq_invoice_info') {
            $this->db->where('tender_enq_invoice_id ', $rec_id);
            $this->db->update('tender_enq_invoice_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }
        if ($table == 'tender_dc_info') {
            $this->db->where('tender_dc_id ', $rec_id);
            $this->db->update('tender_dc_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }

        if ($table == 'vendor_pur_inward_info') {
            $this->db->where('vendor_pur_inward_id', $rec_id);
            $this->db->update('vendor_pur_inward_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }

        if ($table == 'vendor_quotation_info') {
            $this->db->where('vendor_quote_id', $rec_id);
            $this->db->update('vendor_quotation_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }
        if ($table == 'tender_quotation_info') {
            $this->db->where('tender_quotation_id', $rec_id);
            $this->db->update('tender_quotation_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }
        if ($table == 'customer_tender_po_info') {
            $this->db->where('tender_po_id', $rec_id);
            $this->db->update('customer_tender_po_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }
        if ($table == 'tender_enquiry_info') {
            $this->db->where('tender_enquiry_id', $rec_id);
            $this->db->update('tender_enquiry_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }
        if ($table == 'vendor_po_info') {
            $this->db->where('vendor_po_id', $rec_id);
            $this->db->update('vendor_po_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }
        if ($table == 'company_info') {
            $this->db->where('company_id', $rec_id);
            $this->db->update('company_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }

        if ($table == 'category_info') {
            $this->db->where('category_id', $rec_id);
            $this->db->update('category_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }
        if ($table == 'brand_info') {
            $this->db->where('brand_id', $rec_id);
            $this->db->update('brand_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }
        if ($table == 'uom_info') {
            $this->db->where('uom_id', $rec_id);
            $this->db->update('uom_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }
        if ($table == 'currencies_info') {
            $this->db->where('currency_id', $rec_id);
            $this->db->update('currencies_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }
        if ($table == 'gst_info') {
            $this->db->where('gst_id', $rec_id);
            $this->db->update('gst_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }
        if ($table == 'item_info') {
            $this->db->where('item_id', $rec_id);
            $this->db->update('item_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }
        if ($table == 'user_login_info') {
            $this->db->where('user_id', $rec_id);
            $this->db->update('user_login_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }
        if ($table == 'vendor_info') {
            $this->db->where('vendor_id', $rec_id);
            $this->db->update('vendor_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }
        if ($table == 'customer_info') {
            $this->db->where('customer_id', $rec_id);
            $this->db->update('customer_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }
        if ($table == 'customer_contact_info') {
            $this->db->where('customer_contact_id', $rec_id);
            $this->db->update('customer_contact_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }
        if ($table == 'vendor_contact_info') {
            $this->db->where('vendor_contact_id', $rec_id);
            $this->db->update('vendor_contact_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }

    }

}