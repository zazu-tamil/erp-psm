<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Credit_debit_note_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_companies()
    {
        $this->db->select('company_id, company_name');
        $this->db->where('status', 'Active');
        $query = $this->db->get('company_info');
        $opts = ['' => 'Select Company'];
        foreach ($query->result() as $row) {
            $opts[$row->company_id] = $row->company_name;
        }
        return $opts;
    }

    public function get_customers()
    {
        $this->db->select('customer_id, customer_name');
        $this->db->where('status', 'Active');
        $query = $this->db->get('customer_info');
        $opts = ['' => 'Select Customer'];
        foreach ($query->result() as $row) {
            $opts[$row->customer_id] = $row->customer_name;
        }
        return $opts;
    }

    public function get_suppliers()
    {
        $this->db->select('vendor_id, vendor_name');
        $this->db->where('status', 'Active');
        $query = $this->db->get('vendor_info');
        $opts = ['' => 'Select Supplier'];
        foreach ($query->result() as $row) {
            $opts[$row->vendor_id] = $row->vendor_name;
        }
        return $opts;
    }

    public function get_tender_enquiries()
    {
        $this->db->select('tender_enquiry_id, get_tender_info(tender_enquiry_id) as tender_no');
        $this->db->where('status !=', 'Delete');
        $query = $this->db->get('tender_enquiry_info');
        $opts = ['' => 'Select Enquiry'];
        foreach ($query->result() as $row) {
            $opts[$row->tender_enquiry_id] = $row->tender_no;
        }
        return $opts;
    }

    public function get_currencies()
    {
        $this->db->select('currency_id, currency_code, currency_name');
        $this->db->where('status', 'Active');
        $query = $this->db->get('currencies_info');
        $opts = ['' => 'Select Currency'];
        if ($query) {
            foreach ($query->result() as $row) {
                $opts[$row->currency_id] = $row->currency_code . ' - ' . $row->currency_name;
            }
        }
        return $opts;
    }
    
    public function get_taxes()
    {
        $this->db->select('gst_id, gst_percentage');
        $this->db->where('status', 'Active');
        $query = $this->db->get('gst_info');
        $opts = ['0' => '0%'];
        if ($query) {
            foreach ($query->result() as $row) {
                $opts[$row->gst_percentage] = (float)$row->gst_percentage . '%';
            }
        }
        return $opts;
    }

    public function insert_note($data, $items, $adjustments, $attachments)
    {
        $this->db->trans_start();

        // 1. Insert main record
        $this->db->insert('credit_debit_note_info', $data);
        $note_id = $this->db->insert_id();

        // 2. Insert items
        if (!empty($items)) {
            foreach ($items as &$item) {
                $item['credit_debit_note_id'] = $note_id;
            }
            $this->db->insert_batch('credit_debit_note_item_info', $items);
        }

        // 3. Insert adjustments
        if (!empty($adjustments)) {
            $adjustments['credit_debit_note_id'] = $note_id;
            $this->db->insert('credit_debit_note_adjustment_info', $adjustments);
        }

        // 4. Insert attachments
        if (!empty($attachments)) {
            foreach ($attachments as &$att) {
                $att['credit_debit_note_id'] = $note_id;
            }
            $this->db->insert_batch('credit_debit_note_attachment_info', $attachments);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        }

        return $note_id;
    }

    public function get_list($limit, $start, $filters = [])
    {
        $this->db->select('a.*, c.company_name, cus.customer_name, ven.vendor_name, get_tender_info(a.tender_enquiry_id) as tender_no');
        $this->db->from('credit_debit_note_info as a');
        $this->db->join('company_info as c', 'a.company_id = c.company_id', 'left');
        $this->db->join('customer_info as cus', 'a.customer_id = cus.customer_id', 'left');
        $this->db->join('vendor_info as ven', 'a.supplier_id = ven.vendor_id', 'left');

        if (!empty($filters['from_date'])) {
            $this->db->where('a.note_date >=', $filters['from_date']);
        }
        if (!empty($filters['to_date'])) {
            $this->db->where('a.note_date <=', $filters['to_date']);
        }
        if (!empty($filters['party_type'])) {
            $this->db->where('a.party_type', $filters['party_type']);
        }
        if (!empty($filters['note_type'])) {
            $this->db->where('a.note_type', $filters['note_type']);
        }

        $this->db->order_by('a.credit_debit_note_id', 'DESC');
        if ($limit !== null) {
            $this->db->limit($limit, $start);
        }
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_list_count($filters = [])
    {
        $this->db->from('credit_debit_note_info as a');
        if (!empty($filters['from_date'])) {
            $this->db->where('a.note_date >=', $filters['from_date']);
        }
        if (!empty($filters['to_date'])) {
            $this->db->where('a.note_date <=', $filters['to_date']);
        }
        if (!empty($filters['party_type'])) {
            $this->db->where('a.party_type', $filters['party_type']);
        }
        if (!empty($filters['note_type'])) {
            $this->db->where('a.note_type', $filters['note_type']);
        }
        return $this->db->count_all_results();
    }

    public function get_note($id)
    {
        $this->db->select('a.*, get_tender_info(a.tender_enquiry_id) as tender_no');
        $this->db->from('credit_debit_note_info as a');
        $this->db->where('a.credit_debit_note_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_items($id)
    {
        $this->db->where('credit_debit_note_id', $id);
        $query = $this->db->get('credit_debit_note_item_info');
        return $query->result_array();
    }

    public function get_adjustments($id)
    {
        $this->db->where('credit_debit_note_id', $id);
        $query = $this->db->get('credit_debit_note_adjustment_info');
        return $query->row_array();
    }

    public function get_attachments($id)
    {
        $this->db->where('credit_debit_note_id', $id);
        $query = $this->db->get('credit_debit_note_attachment_info');
        return $query->result_array();
    }

    public function update_note($note_id, $data, $items, $adjustments, $attachments)
    {
        $this->db->trans_start();

        // 1. Update main record
        $this->db->where('credit_debit_note_id', $note_id);
        $this->db->update('credit_debit_note_info', $data);

        // 2. Update items (Delete & Re-insert)
        $this->db->where('credit_debit_note_id', $note_id);
        $this->db->delete('credit_debit_note_item_info');
        
        if (!empty($items)) {
            foreach ($items as &$item) {
                $item['credit_debit_note_id'] = $note_id;
            }
            $this->db->insert_batch('credit_debit_note_item_info', $items);
        }

        // 3. Update adjustments (Delete & Re-insert)
        $this->db->where('credit_debit_note_id', $note_id);
        $this->db->delete('credit_debit_note_adjustment_info');
        
        if (!empty($adjustments)) {
            $adjustments['credit_debit_note_id'] = $note_id;
            $this->db->insert('credit_debit_note_adjustment_info', $adjustments);
        }

        // 4. Update attachments (Insert only new ones)
        if (!empty($attachments)) {
            foreach ($attachments as &$att) {
                $att['credit_debit_note_id'] = $note_id;
            }
            $this->db->insert_batch('credit_debit_note_attachment_info', $attachments);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        }

        return $note_id;
    }
    public function delete_note($id)
    {
        $this->db->trans_start();
        $this->db->where('credit_debit_note_id', $id);
        $this->db->update('credit_debit_note_info', ['status' => 'Delete']);
        $this->db->trans_complete();
        
        return $this->db->trans_status();
    }
}
