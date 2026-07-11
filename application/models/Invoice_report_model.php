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

    public function get_tender_progress($srch_from_date, $srch_to_date, $customer_id = '', $search_term = '')
    {
        $params = [];
        $where_clauses = ["po.status = 'Active'", "tpi.status = 'Active'"];

        if (!empty($srch_from_date) && !empty($srch_to_date)) {
            $where_clauses[] = "po.po_date BETWEEN ? AND ?";
            $params[] = $srch_from_date;
            $params[] = $srch_to_date;
        }

        if (!empty($customer_id)) {
            $where_clauses[] = "po.customer_id = ?";
            $params[] = $customer_id;
        }

        if (!empty($search_term)) {
            $where_clauses[] = "(po.our_po_no LIKE ? OR po.customer_po_no LIKE ? OR c.customer_name LIKE ? OR tpi.item_code LIKE ? OR tpi.item_desc LIKE ? OR te.enquiry_no LIKE ?)";
            $escaped = "%" . $search_term . "%";
            $params[] = $escaped;
            $params[] = $escaped;
            $params[] = $escaped;
            $params[] = $escaped;
            $params[] = $escaped;
            $params[] = $escaped;
        }

        $where_sql = implode(' AND ', $where_clauses);

        $sql = "
            SELECT 
                po.tender_po_id,
                po.our_po_no,
                po.customer_po_no,
                po.po_date,
                po.tender_enquiry_id,
                get_tender_info(po.tender_enquiry_id) AS tender_order_id,
                c.customer_name,
                tpi.tender_po_item_id,
                tpi.item_code,
                tpi.item_desc,
                tpi.uom,
                tpi.qty AS po_qty,
                
                -- Vendor PO Qty
                IFNULL((
                    SELECT SUM(vpi.qty)
                    FROM vendor_po_item_info vpi
                    JOIN vendor_po_info vp ON vpi.vendor_po_id = vp.vendor_po_id
                    WHERE vp.tender_enquiry_id = po.tender_enquiry_id
                    AND vpi.item_code = tpi.item_code
                    AND vpi.status = 'Active'
                    AND vp.status = 'Active'
                ), 0) AS vendor_po_qty,
                
                -- Delivered Qty
                IFNULL((
                    SELECT SUM(dci.qty)
                    FROM tender_dc_item_info dci
                    JOIN tender_dc_info dc ON dci.tender_dc_id = dc.tender_dc_id
                    WHERE dc.tender_po_id = po.tender_po_id
                    AND dci.item_code = tpi.item_code
                    AND dci.status = 'Active'
                    AND dc.status = 'Active'
                ), 0) AS delivered_qty,
                
                -- Invoiced Qty
                IFNULL((
                    SELECT SUM(inv_item.qty)
                    FROM tender_enq_invoice_item_info inv_item
                    JOIN tender_enq_invoice_info inv ON inv_item.tender_enq_invoice_id = inv.tender_enq_invoice_id
                    WHERE inv.tender_po_id = po.tender_po_id
                    AND (inv_item.tender_po_item_id = tpi.tender_po_item_id OR inv_item.item_code = tpi.item_code)
                    AND inv_item.status = 'Active'
                    AND inv.status = 'Active'
                ), 0) AS invoiced_qty,

                -- Enquiry details
                te.enquiry_no,
                te.enquiry_date,
                te.tender_status,

                -- Quotation details
                tq.quotation_no,
                tq.quote_date,
                tq.quotation_status
                
            FROM customer_tender_po_info po
            JOIN tender_po_item_info tpi ON po.tender_po_id = tpi.tender_po_id
            LEFT JOIN tender_enquiry_info te ON po.tender_enquiry_id = te.tender_enquiry_id AND te.status = 'Active'
            LEFT JOIN (
                SELECT tq1.tender_enquiry_id, tq1.quotation_no, tq1.quote_date, tq1.quotation_status
                FROM tender_quotation_info tq1
                WHERE tq1.tender_quotation_id = (
                    SELECT MAX(tq2.tender_quotation_id)
                    FROM tender_quotation_info tq2
                    WHERE tq2.tender_enquiry_id = tq1.tender_enquiry_id
                    AND tq2.status = 'Active'
                )
            ) tq ON po.tender_enquiry_id = tq.tender_enquiry_id
            LEFT JOIN customer_info c ON po.customer_id = c.customer_id AND c.status = 'Active'
            WHERE {$where_sql}
            ORDER BY po.po_date DESC, po.tender_po_id DESC, tpi.tender_po_item_id ASC
        ";

        return $this->db->query($sql, $params)->result_array();
    }
}