<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pl_model extends CI_Model
{

    public function get_sales_summary($from_date, $to_date)
    {
        $this->db->select('(SUM(total_amount - ifnull(tax_amount, 0))) as total_sales');

        $this->db->where("invoice_date between '$from_date' AND '$to_date'");
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

        $this->db->where("a.inward_date between '$from_date' AND '$to_date'");
        $this->db->where('a.status', 'Active');
        $this->db->where('b.status', 'Active');
        $this->db->where('c.status', 'Active');
        $this->db->where('c.nature_type', 'Income');
        // $this->db->where_between('a.inward_date', $from_date, $to_date);

        $this->db->group_by(array(
            'a.account_head_id',
            'a.sub_account_head_id'
        ));

        $this->db->order_by('c.account_head_name');
        $this->db->order_by('b.sub_account_head_name');

        return $this->db->get()->result_array();
    }

    public function get_purchases_summary($from_date, $to_date)
    {
        $this->db->select('SUM(total_amount_wo_tax) as total_purchases');
        $this->db->where('invoice_date >=', $from_date);
        $this->db->where('invoice_date <=', $to_date);
        $this->db->where('status =', 'Active');
        return $this->db->get('vendor_purchase_invoice_info')->row('');
    }

    public function get_indirect_expenses_summary($from_date, $to_date)
    {
    $sql = "
        SELECT
            exp_type,
            SUM(exp_amt) AS exp_amt
        FROM
        (
            /* -----------------------------------------
             * 1. CASH / BANK OUTWARD EXPENSES
             * ----------------------------------------- */
            SELECT
                b.sub_account_head_name AS exp_type,
                SUM(COALESCE(a.amount, 0)) AS exp_amt
            FROM cb_cash_outward_info AS a
            INNER JOIN cb_sub_account_head_info AS b
                ON b.sub_account_head_id = a.sub_account_head_id
            WHERE a.status = 'Active'
                AND b.status = 'Active'
                AND b.nature_type = 'Expense'
                AND a.outward_date BETWEEN ? AND ?
            GROUP BY
                a.sub_account_head_id,
                b.sub_account_head_name

            UNION ALL

            /* -----------------------------------------
             * 2. LOCAL PURCHASE BILL EXPENSES
             * ----------------------------------------- */
            SELECT
                b.sub_account_head_name AS exp_type,
                SUM(COALESCE(a.tot_amt_wo_tax, 0)) AS exp_amt
            FROM local_purchase_bill_info AS a
            INNER JOIN cb_sub_account_head_info AS b
                ON b.sub_account_head_id = a.sub_account_head_id
            WHERE a.status = 'Active'
                AND b.status = 'Active'
                AND b.nature_type = 'Expense'
                AND a.invoice_date BETWEEN ? AND ?
            GROUP BY
                a.sub_account_head_id,
                b.sub_account_head_name

            UNION ALL

            /* -----------------------------------------
             * 3. DP BILL EXPENSES
             * ----------------------------------------- */
            SELECT
                b.sub_account_head_name AS exp_type,
                SUM(COALESCE(a.dp_charges, 0)) AS exp_amt
            FROM dp_bill_info AS a
            INNER JOIN cb_sub_account_head_info AS b
                ON b.sub_account_head_id = a.sub_account_head_id
            WHERE a.status = 'Active'
                AND b.status = 'Active'
                AND b.nature_type = 'Expense'
                AND a.invoice_date BETWEEN ? AND ?
            GROUP BY
                a.sub_account_head_id,
                b.sub_account_head_name

            UNION ALL

            /* -----------------------------------------
             * 4. CUSTOMS EXPENSE
             * ----------------------------------------- */
            SELECT
                'Customs & Others Dutys' AS exp_type,
                SUM(
                    COALESCE(a.custom_stamp_fee, 0)
                    + COALESCE(a.custom_duty, 0)
                ) AS exp_amt
            FROM customs_bill_info AS a
            WHERE a.status = 'Active'
                AND a.invoice_date BETWEEN ? AND ?
        ) AS x

        GROUP BY exp_type
        ORDER BY exp_type
    ";

        $query = $this->db->query($sql, array(
            // Cash / Bank Outward
            $from_date,
            $to_date,

            // Local Purchase
            $from_date,
            $to_date,

            // DP Bill
            $from_date,
            $to_date,

            // Customs
            $from_date,
            $to_date
        ));

        return $query->result_array();
    }
}
