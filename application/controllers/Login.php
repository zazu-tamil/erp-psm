<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{


    public function index()
    {
        $data['js'] = '';
        $data['login'] = true;

        $this->load->library('form_validation');
        $this->form_validation->set_rules('user_name', 'User Name', 'required');
        $this->form_validation->set_rules('user_pwd', 'Password', 'required', array('required' => 'You must provide %s.'));

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('page/login', $data);
        } else {
            $this->load->model('User_session_model');
            $this->load->model('Parent_session_model');
            $this->load->model('Student_session_model');
            $lat_lng = $this->security->xss_clean($this->input->post('lat_lng'));

            $username = $this->security->xss_clean($this->input->post('user_name'));
            $password = $this->security->xss_clean($this->input->post('user_pwd'));

            $sql = "
                
                SELECT 
                    a.user_id as id, 
                    a.user_name,
                    if(a.ref_id = 0 , a.user_name , LOWER(b.employee_name)) as name,
                    a.level,
                    a.edit_flg,
                    a.ref_id,
                    if(a.ref_id = 0 , '' , b.photo_img) as photo_img, 
                    IFNULL(b.department_head, 0) as department_head,
                    b.department_id,
                    IFNULL(b.mgt_head, 0) as mgt_head,
                    IFNULL(b.emp_category_head, 0) as emp_category_head,
                    b.employee_category
                FROM user_login a
                LEFT JOIN pyr_employee_info b ON b.employee_id = a.ref_id
                WHERE a.user_name = '$username'
                  AND a.user_pwd = '$password'
                  AND a.status = 'Active'
                  AND a.level IN ('Admin', 'Staff', 'Manager', 'PF Office','Canteen','Printing')
            ";

            $query = $this->db->query($sql);
            $row = $query->row();

            if ($row) {
                $session_id = $this->User_session_model->log_login($row->id, $row->ref_id);
                $mentor_class_id = $this->User_session_model->get_mentor_class_id($row->ref_id);
                $coordinator_class_category_id = $this->User_session_model->get_class_category_by_coordinator($row->ref_id);

                $session_data = [
                    SESS_HD . 'user_id' => $row->id,
                    SESS_HD . 'user_name' => $row->name,
                    SESS_HD . 'user_type' => $row->level,
                    SESS_HD . 'ref_id' => $row->ref_id,
                    SESS_HD . 'emp_photo' => (($row->ref_id == 0 or $row->photo_img == '') ? '' : '../payroll/' . $row->photo_img),
                    SESS_HD . 'department_head' => $row->department_head,
                    SESS_HD . 'department_id' => $row->department_id,
                    SESS_HD . 'employee_category' => $row->employee_category,
                    SESS_HD . 'emp_category_head' => $row->emp_category_head,
                    SESS_HD . 'mgt_head' => $row->mgt_head,
                    SESS_HD . 'session_record_id' => $session_id,
                    SESS_HD . 'mentor_class_id' => ($mentor_class_id != '' ? $mentor_class_id : 0),
                    SESS_HD . 'coordinator_class_category_id' => ($coordinator_class_category_id != '' ? $coordinator_class_category_id : 0),
                    SESS_HD . 'logged_in' => TRUE,
                    SESS_HD . 'login_time' => time()
                ];

                $this->session->set_userdata($session_data);

                $this->db->insert('crit_user_track_info', [
                    'user_id' => $row->id,
                    'page' => 'Payroll-Login',
                    'date_time' => date('Y-m-d H:i:s'),
                    'lat_lng' => $lat_lng
                ]);

                if ($row->level == 'Canteen')
                    redirect('canteen-dash');
                elseif ($row->level == 'Manager')
                    redirect('manager-dash');
                elseif ($row->level == 'Printing')
                    redirect('calendar');
                else
                    redirect('module');

            } else {

                // 1. Check Parent Login
                $parent_sql = "
                    SELECT 
                    *
                    FROM sas_parent_login_info
                    WHERE p_user_name = ?
                    AND p_user_pwd = ?
                    AND status = 'Active'
                    LIMIT 1
                ";

                $query2 = $this->db->query($parent_sql, [$username, $password]);
                $parent = $query2->row();


                // 2. Check Student Login
                $student_sql = "
                    SELECT 
                    a.student_login_id ,
                    a.s_name ,
                    a.stud_id ,
                    b.student_photo 
                    FROM sas_student_login_info as a
                    left join sas_student_info as b on b.student_id = a.stud_id 
                    WHERE a.s_user_name = ?
                    AND a.s_user_pwd = ?
                    AND a.status = 'Active'
                    and b.status = 'Active'
                    LIMIT 1
                ";
                $query = $this->db->query($student_sql, [$username, $password]);
                $student = $query->row();

                // ... inside if ($parent) { ... }
                if ($parent) {
                    // Log parent login session: user_id = parent_login_id, emp_id = 0
                    $session_id = $this->Parent_session_model->log_login($parent->parent_login_id, $parent->parent_login_id);
                    $session_data = [
                        SESS_HD . 'user_id' => $parent->parent_login_id,
                        SESS_HD . 'user_name' => $parent->p_name,
                        SESS_HD . 'user_type' => 'Parent',
                        SESS_HD . 'ref_id' => $parent->stud_id,
                        SESS_HD . 'emp_photo' => '',
                        SESS_HD . 'department_head' => '',
                        SESS_HD . 'department_id' => '',
                        SESS_HD . 'employee_category' => '',
                        SESS_HD . 'emp_category_head' => '',
                        SESS_HD . 'mgt_head' => '',
                        SESS_HD . 'session_record_id_parent' => $session_id, // Stores the actual session ID from user_sessions_info
                        SESS_HD . 'logged_in' => TRUE,
                        SESS_HD . 'login_time' => time()
                    ];
                    $this->session->set_userdata($session_data);
                    redirect('parent-dash');
                }
                // ... inside elseif ($student) { ... }
                elseif ($student) {
                    // Log student login session: user_id = student_login_id, emp_id = 0
                    $session_id = $this->Student_session_model->log_login($student->student_login_id, 0);
                    $session_data = [
                        SESS_HD . 'user_id' => $student->student_login_id,
                        SESS_HD . 'user_name' => $student->s_name,
                        SESS_HD . 'user_type' => 'Student',
                        SESS_HD . 'ref_id' => $student->stud_id,
                        SESS_HD . 'emp_photo' => $student->student_photo,
                        SESS_HD . 'department_head' => '',
                        SESS_HD . 'department_id' => '',
                        SESS_HD . 'employee_category' => '',
                        SESS_HD . 'emp_category_head' => '',
                        SESS_HD . 'mgt_head' => '',
                        SESS_HD . 'session_record_id_student' => $session_id, // Stores the actual session ID from user_sessions_info
                        SESS_HD . 'logged_in' => TRUE,
                        SESS_HD . 'login_time' => time()
                    ];
                    $this->session->set_userdata($session_data);
                    redirect('student/dash');
                } else {
                    //  No match
                    $data['msg'] = 'Invalid Username or Password';
                    $data['login'] = false;
                    $this->load->view('page/login', $data);
                }
            }
        }
    }

 public function logout($reason = 'manual')
{
    $this->load->model('User_session_model');
    $this->load->model('Parent_session_model');
    $this->load->model('Student_session_model');

    $session_record_id = $this->session->userdata(SESS_HD . 'session_record_id');
    if ($session_record_id) {
        $this->User_session_model->log_logout($session_record_id, $reason);
    }

    $session_record_id_parent = $this->session->userdata(SESS_HD . 'session_record_id_parent');
    if ($session_record_id_parent) {
        $this->Parent_session_model->log_logout($session_record_id_parent, $reason);
    }

    $session_record_id_student = $this->session->userdata(SESS_HD . 'session_record_id_student');
    if ($session_record_id_student) {
        
        $this->Student_session_model->log_logout($session_record_id_student, $reason);
    }

    $this->db->insert('crit_user_track_info', [
        'user_id' => $this->session->userdata(SESS_HD . 'user_id'),
        'page' => 'Payroll-Logout',
        'date_time' => date('Y-m-d H:i:s')
    ]);

    // Flash message
    if ($reason === 'timeout') {
        $this->session->set_flashdata('session_expired', 'Your session has expired due to inactivity. Please log in again.');
    } else {
        $this->session->set_flashdata('logout_msg', 'You have been logged out successfully.');
    }

    $this->session->sess_destroy();
    redirect('');
}


    public function user_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if ($this->session->userdata(SESS_HD . 'user_type') != 'Admin') {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'user.inc';


        if ($this->input->post('mode') == 'Add') {
            $ins = array(
                'user_name' => $this->input->post('user_name'),
                'user_pwd' => $this->input->post('user_pwd'),
                'level' => $this->input->post('level'),
                'edit_flg' => $this->input->post('edit_flg'),
                'status' => $this->input->post('status')
            );

            $this->db->insert('user_login', $ins);
            redirect('user-list');
        }

        if ($this->input->post('mode') == 'Edit') {
            $upd = array(
                'user_name' => $this->input->post('user_name'),
                'user_pwd' => $this->input->post('user_pwd'),
                'level' => $this->input->post('level'),
                'edit_flg' => $this->input->post('edit_flg'),
                'status' => $this->input->post('status')
            );

            $this->db->where('user_id', $this->input->post('user_id'));
            $this->db->update('user_login', $upd);

            redirect('user-list/' . $this->uri->segment(2, 0));
        }


        $this->load->library('pagination');

        $this->db->where('status != ', 'Delete');
        $this->db->from('user_login');
        $data['total_records'] = $cnt = $this->db->count_all_results();

        $data['sno'] = $this->uri->segment(2, 0);

        $config['base_url'] = trim(site_url('user-list/'), '/' . $this->uri->segment(2, 0));
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

        $sql = "
                select 
                a.*             
                from user_login as a  
                where a.status != 'Delete'
                order by a.status , a.level,  a.user_name
                limit " . $this->uri->segment(2, 0) . "," . $config['per_page'] . "                
        ";

        $data['record_list'] = array();

        $query = $this->db->query($sql);

        foreach ($query->result_array() as $row) {
            $data['record_list'][] = $row;
        }


        $data['user_type_opt'] = array('Admin' => 'Admin', 'User' => 'User');



        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('page/user-list', $data);
    }

    public function change_password()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        $data['js'] = 'change-password.inc';

        $data['user_name'] = $this->session->userdata(SESS_HD . 'user_name');
        $data['login_name'] = $this->session->userdata(SESS_HD . 'user_name');
        $data['user_id'] = $this->session->userdata(SESS_HD . 'user_id');

        $this->load->view('page/change-password', $data);
    }

}