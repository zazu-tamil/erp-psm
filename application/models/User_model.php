<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
  public function get_by_email($email){
    return $this->db->where('email',$email)->get('users')->row();
  }
  public function get_all(){
    return $this->db->order_by('name','asc')->get('users')->result();
  }
  public function insert($data){
    $this->db->insert('users',$data);
    return $this->db->insert_id();
  }
}
