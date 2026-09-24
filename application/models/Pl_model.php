<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pl_model extends CI_Model
{

    public function get_sales_summary($from_date, $to_date)
    {
        $this->db->select('
            COALESCE(SUM(total_amount - IFNULL(tax_amount, 0)), 0) AS total_sales_wo_tax,
            COALESCE(SUM(IFNULL(tax_amount, 0)), 0) AS total_sales_tax,
            COALESCE(SUM(total_amount), 0) AS total_sales_with_tax,
            COALESCE(SUM(total_amount - IFNULL(tax_amount, 0)), 0) AS total_sales
        ');

        $this->db->where("invoice_date between '$from_date' AND '$to_date'");
        $this->db->where('status =', 'Active');
        $row = $this->db->get('tender_enq_invoice_info')->row_array();
        return $row ?: [
            'total_sales_wo_tax' => 0,
            'total_sales_tax' => 0,
            'total_sales_with_tax' => 0,
            'total_sales' => 0
        ];
    }

    public function get_otherincome_summary($from_date, $to_date)
    {
        $this->db->select("
            c.account_head_name AS inc_type,
            b.sub_account_head_name AS sub_typ,
            SUM(a.amount) AS inc_amt,
            SUM(a.amount) AS inc_amt_wo_tax,
            0 AS tax_amt,
            SUM(a.amount) AS inc_amt_with_tax
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
        $this->db->select('
            COALESCE(SUM(COALESCE(total_amount_wo_tax_inc_addl, total_amount_wo_tax, (total_amount - IFNULL(tax_amount, 0)), 0)), 0) AS total_purchases_wo_tax,
            COALESCE(SUM(COALESCE(total_tax_amount_inc_addl, tax_amount, 0)), 0) AS total_purchases_tax,
            COALESCE(SUM(COALESCE(total_amount_inc_addl, total_amount, 0)), 0) AS total_purchases_with_tax,
            COALESCE(SUM(COALESCE(total_amount_wo_tax_inc_addl, total_amount_wo_tax, (total_amount - IFNULL(tax_amount, 0)), 0)), 0) AS total_purchases
        ');
        $this->db->where('invoice_date >=', $from_date);
        $this->db->where('invoice_date <=', $to_date);
        $this->db->where('status =', 'Active');
        $row = $this->db->get('vendor_purchase_invoice_info')->row_array();
        return $row ?: [
            'total_purchases_wo_tax' => 0,
            'total_purchases_tax' => 0,
            'total_purchases_with_tax' => 0,
            'total_purchases' => 0
        ];
    }

    public function get_indirect_expenses_summary($from_date, $to_date)
    {
        $sql = "
            SELECT
                exp_type,
                SUM(exp_amt_wo_tax) AS exp_amt_wo_tax,
                SUM(tax_amt) AS tax_amt,
                SUM(exp_amt_with_tax) AS exp_amt_with_tax,
                SUM(exp_amt_wo_tax) AS exp_amt
            FROM
            (
                /* -----------------------------------------
                 * 1. CASH / BANK OUTWARD EXPENSES
                 * ----------------------------------------- */
                SELECT
                    b.sub_account_head_name AS exp_type,
                    SUM(COALESCE(a.amount, 0)) AS exp_amt_wo_tax,
                    0 AS tax_amt,
                    SUM(COALESCE(a.amount, 0)) AS exp_amt_with_tax
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
                    SUM(COALESCE(a.tot_amt_wo_tax, 0)) AS exp_amt_wo_tax,
                    SUM(COALESCE(a.vat_amt, 0)) AS tax_amt,
                    SUM(COALESCE(a.tot_amt_with_tax, (COALESCE(a.tot_amt_wo_tax, 0) + COALESCE(a.vat_amt, 0)))) AS exp_amt_with_tax
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

                        SUM(COALESCE(a.dp_charges, 0)) AS exp_amt_wo_tax,

                        SUM(COALESCE(a.dp_vat_amt, 0)) AS tax_amt,

                        SUM(
                            COALESCE(a.dp_charges, 0)
                            + COALESCE(a.dp_vat_amt, 0)
                        ) AS exp_amt_with_tax
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
                    SUM(COALESCE(a.custom_stamp_fee, 0) + COALESCE(a.custom_duty, 0)) AS exp_amt_wo_tax,
                    SUM(COALESCE(a.vat_amt, 0)) AS tax_amt,
                    SUM(COALESCE(a.custom_stamp_fee, 0) + COALESCE(a.custom_duty, 0) + COALESCE(a.vat_amt, 0)) AS exp_amt_with_tax
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
