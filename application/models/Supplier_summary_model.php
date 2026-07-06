<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier_summary_model extends CI_Model
{

    public function get_supplier_summary($srch_from_date, $srch_to_date)
    {
        $sql = "
            SELECT
                a.tender_po_id,
                get_tender_info(a.tender_enquiry_id) AS order_id,
                a.po_date,
                a.customer_po_no,
                c.customer_name AS customer,

                /* PO Amount */
                (IFNULL(pi.po_item_amount,0) + IFNULL(pc.po_addl_charge,0)) AS po_amt,

                /* Customer Invoice */
                ci.invoice_date,
                LPAD(ci.invoice_no,4,'0') AS invoice_no,
                ci.total_amount AS customer_invoice_amt,

                /* Vendor Invoice */
                v.vendor_name,
                vi.invoice_date AS vendor_invoice_date,
                vi.invoice_no AS vendor_invoice_no,
                vi.total_amount AS vendor_invoice_amt

            FROM customer_tender_po_info a

            LEFT JOIN customer_info c
                ON c.customer_id = a.customer_id

            /* PO Item Total */
            LEFT JOIN
            (
                SELECT
                    tender_po_id,
                    SUM(amount) AS po_item_amount
                FROM tender_po_item_info
                WHERE status='Active'
                GROUP BY tender_po_id
            ) pi
            ON pi.tender_po_id = a.tender_po_id

            /* Additional Charges */
            LEFT JOIN
            (
                SELECT
                    tender_po_id,
                    SUM(addt_charges_tot_amt) AS po_addl_charge
                FROM tender_po_addtchrg_info
                WHERE status='Active'
                GROUP BY tender_po_id
            ) pc
            ON pc.tender_po_id = a.tender_po_id

            /* Customer Invoice */
            LEFT JOIN tender_enq_invoice_info ci
                ON ci.tender_po_id = a.tender_po_id
                AND ci.status='Active'

            /* Vendor Invoice */
            LEFT JOIN vendor_purchase_invoice_info vi
                ON vi.tender_enquiry_id = a.tender_enquiry_id
                AND vi.status='Active'

            LEFT JOIN vendor_info v
                ON v.vendor_id = vi.vendor_id

            WHERE a.status='Active'
            AND a.po_date BETWEEN '$srch_from_date' AND '$srch_to_date'

            ORDER BY
                a.po_date ASC,
                a.tender_po_id ASC,
                vi.invoice_date ASC,
                vi.invoice_no ASC;
        ";

        return $this->db->query($sql)->result_array();
    }
}