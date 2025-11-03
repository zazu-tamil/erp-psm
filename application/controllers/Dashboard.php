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

 
        $this->load->view('page/dashboard', $data);
    } 


}