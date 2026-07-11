<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_report_model extends CI_Model
{

    public function get_invoice_summary($srch_from_date, $srch_to_date)
    {
        $sql = "
            SELECT
                a.tender_enquiry_id,
                get_tender_info(a.tender_enquiry_id) AS tender_order_id,

                c.customer_name,

                /*================ CUSTOMER INVOICE =================*/
                ci.invoice_no AS customer_invoice_no,
                ci.invoice_date AS customer_invoice_date,
                ci.total_amount AS customer_invoice_amount,

                /*================ SUPPLIER INVOICE =================*/
                v.vendor_name,
                vi.invoice_no AS supplier_invoice_no,
                vi.invoice_date AS supplier_invoice_date,
                vi.total_amount AS supplier_invoice_amount,

                /*================ LOCAL SUPPLIER BILL =================*/
                lp.invoice_no AS local_bill_no,
                lp.invoice_date AS local_bill_date,
                lp.tot_amt_with_tax AS local_bill_amount,

                /*================ DELIVERY PARTNER BILL =================*/
                dp.invoice_no AS dp_bill_no,
                dp.invoice_date AS dp_bill_date,
                dp.g_total AS dp_bill_amount,

                /*================ CUSTOMS BILL =================*/
                cb.invoice_no AS customs_bill_no,
                cb.invoice_date AS customs_bill_date,
                cb.customs_tot_amt AS customs_bill_amount

            FROM customer_tender_po_info a

            LEFT JOIN customer_info c
                ON c.customer_id = a.customer_id

            /* Customer Invoice */
            LEFT JOIN tender_enq_invoice_info ci
                ON ci.tender_po_id = a.tender_po_id
                AND ci.status = 'Active'

            /* Supplier Invoice */
            LEFT JOIN vendor_purchase_invoice_info vi
                ON vi.tender_enquiry_id = a.tender_enquiry_id
                AND vi.status = 'Active'

            LEFT JOIN vendor_info v
                ON v.vendor_id = vi.vendor_id

            /* Local Supplier Bill */
            LEFT JOIN local_purchase_bill_info lp
                ON lp.tender_enquiry_id = a.tender_enquiry_id
                AND lp.status = 'Active'

            /* Delivery Partner Bill */
            LEFT JOIN dp_bill_info dp
                ON dp.tender_enquiry_id = a.tender_enquiry_id
                AND dp.status = 'Active'

            /* Customs Bill */
            LEFT JOIN customs_bill_info cb
                ON cb.tender_enquiry_id = a.tender_enquiry_id
                AND cb.status = 'Active'

            WHERE
                a.status = 'Active'
                AND ci.invoice_date BETWEEN ? AND ? 
            ORDER BY
                ci.invoice_date,
                ci.invoice_no ASC;
        ";

        return $this->db->query($sql, [$srch_from_date, $srch_to_date])->result_array();
    }
}