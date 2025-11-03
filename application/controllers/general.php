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

    }
 
}