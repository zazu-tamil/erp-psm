<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pl_model extends CI_Model {  

     public function get_sales_summary($from_date, $to_date){
        $this->db->select('SUM(total_amount) as total_sales');
        $this->db->where('invoice_date >=', $from_date);
        $this->db->where('invoice_date <=', $to_date);
        $this->db->where('status =', 'Active');
        return $this->db->get('tender_enq_invoice_info')->row('total_sales');
    }
     
    public function get_otherincome_summary($from_date, $to_date)
    {
        $this->db->select("
            c.account_head_name AS inc_type,
            b.sub_account_head_name AS sub_typ,
            SUM(a.amount) AS inc_amt
        ");
        $this->db->from('cb_cash_inward_info AS a');
        $this->db->join(
            'cb_sub_account_head_info AS b',
            'b.sub_account_head_id = a.sub_account_head_id',
            'left'
        );
        $this->db->join(
            'cb_account_head_info AS c',
            'c.account_head_id = a.account_head_id',
            'left'
        );

        $this->db->where('a.status', 'Active');
        $this->db->where('b.status', 'Active');
        $this->db->where('c.status', 'Active');
        $this->db->where('a.inward_date >=', $from_date);
        $this->db->where('a.inward_date <=', $to_date);

        $this->db->group_by(array(
            'a.account_head_id',
            'a.sub_account_head_id'
        ));

        $this->db->order_by('c.account_head_name');
        $this->db->order_by('b.sub_account_head_name');

        return $this->db->get()->result_array();
    }

    public function get_purchases_summary($from_date, $to_date){ 
        $this->db->select('SUM(total_amount) as total_purchases');
        $this->db->where('entry_date >=', $from_date);
        $this->db->where('entry_date <=', $to_date);
        $this->db->where('status =', 'Active');
        return $this->db->get('vendor_purchase_invoice_info')->row('total_purchases');
    }

    public function get_indirect_expenses_summary($from_date, $to_date)
    {
        $sql = "
            SELECT
                exp_type,
                SUM(exp_amt) AS exp_amt
            FROM
            (
                SELECT
                    b.sub_account_head_name AS exp_type,
                    SUM(a.tot_amt_wo_tax) AS exp_amt
                FROM local_purchase_bill_info AS a
                LEFT JOIN cb_sub_account_head_info AS b
                    ON b.sub_account_head_id = a.sub_account_head_id
                WHERE a.status = 'Active'
                AND b.status = 'Active'
                AND a.inv_entry_date BETWEEN ? AND ?
                GROUP BY a.sub_account_head_id

                UNION ALL

                SELECT
                    b.sub_account_head_name AS exp_type,
                    SUM(a.dp_charges) AS exp_amt
                FROM dp_bill_info AS a
                LEFT JOIN cb_sub_account_head_info AS b
                    ON b.sub_account_head_id = a.sub_account_head_id
                WHERE a.status = 'Active'
                AND b.status = 'Active'
                AND a.inv_entry_date BETWEEN ? AND ?
                GROUP BY a.sub_account_head_id

                UNION ALL

                SELECT
                    'Customs' AS exp_type,
                    SUM(a.custom_stamp_fee + a.custom_duty) AS exp_amt
                FROM customs_bill_info AS a
                WHERE a.status = 'Active'
                AND a.inv_entry_date BETWEEN ? AND ?
            ) x
            GROUP BY exp_type
            ORDER BY exp_type
        ";

        $query = $this->db->query($sql, array(
            $from_date, $to_date,
            $from_date, $to_date,
            $from_date, $to_date
        ));

        return $query->result_array();
    } 
}
