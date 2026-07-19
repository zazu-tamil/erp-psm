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
                ci.invoice_no asc, ci.invoice_date asc   ;
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

    public function get_customer_invoices_for_vat($srch_from_date, $srch_to_date, $company_id = '', $customer_id = '', $search_term = '')
    {
        $params = [];
        $where = ["a.status = 'Active'"];

        if (!empty($srch_from_date) && !empty($srch_to_date)) {
            $where[] = "a.invoice_date BETWEEN ? AND ?";
            $params[] = $srch_from_date;
            $params[] = $srch_to_date;
        }

        if (!empty($company_id)) {
            $where[] = "a.company_id = ?";
            $params[] = $company_id;
        }

        if (!empty($customer_id)) {
            $where[] = "a.customer_id = ?";
            $params[] = $customer_id;
        }

        if (!empty($search_term)) {
            $where[] = "(a.invoice_no LIKE ? OR c.customer_name LIKE ?)";
            $escaped = "%" . $search_term . "%";
            $params[] = $escaped;
            $params[] = $escaped;
        }

        $where_sql = implode(' AND ', $where);

        $sql = "
            SELECT 
                a.tender_enq_invoice_id,
                a.invoice_no,
                a.invoice_date,
                a.tender_enquiry_id,
                (a.total_amount - a.tax_amount) AS total_amount_wo_tax,
                a.tax_amount,
                a.total_amount,
                c.customer_name,
                c.gst AS client_vat_no
            FROM tender_enq_invoice_info a
            LEFT JOIN customer_info c ON a.customer_id = c.customer_id AND c.status = 'Active'
            WHERE {$where_sql}
            ORDER BY a.invoice_date ASC, a.invoice_no ASC
        ";

        return $this->db->query($sql, $params)->result_array();
    }

    public function get_vendor_bills_for_enquiries($enquiry_ids)
    {
        if (empty($enquiry_ids)) {
            return [];
        }

        $results = [];

        // 1. Supplier Invoices
        $this->db->select("
            a.tender_enquiry_id,
            a.invoice_date,
            a.invoice_no,
            v.vendor_name,
            v.gst AS vendor_vat,
            a.total_amount_wo_tax AS cif_val,
            a.total_duty_amount AS duty,
            (a.total_amount_wo_tax + a.total_duty_amount) AS total_val,
            a.tax_amount AS vat,
            a.tax_amount AS vat_paid,
            a.total_amount AS grand_total
        ");
        $this->db->from('vendor_purchase_invoice_info a');
        $this->db->join('vendor_info v', 'a.vendor_id = v.vendor_id AND v.status = \'Active\'', 'left');
        $this->db->where_in('a.tender_enquiry_id', $enquiry_ids);
        $this->db->where('a.status', 'Active');
        $q1 = $this->db->get()->result_array();
        foreach ($q1 as $row) {
            $row['type'] = 'Supplier Bill';
            $results[] = $row;
        }

        // 2. Local Supplier Bills
        $this->db->select("
            a.tender_enquiry_id,
            a.invoice_date,
            a.invoice_no,
            v.vendor_name,
            v.gst AS vendor_vat,
            a.tot_amt_wo_tax AS cif_val,
            0 AS duty,
            a.tot_amt_wo_tax AS total_val,
            a.vat_amt AS vat,
            a.vat_amt AS vat_paid,
            a.tot_amt_with_tax AS grand_total
        ");
        $this->db->from('local_purchase_bill_info a');
        $this->db->join('vendor_info v', 'a.vendor_id = v.vendor_id AND v.status = \'Active\'', 'left');
        $this->db->where_in('a.tender_enquiry_id', $enquiry_ids);
        $this->db->where('a.status', 'Active');
        $q2 = $this->db->get()->result_array();
        foreach ($q2 as $row) {
            $row['type'] = 'Local Supplier Bill';
            $results[] = $row;
        }

        // 3. Delivery Partner Bills
        $this->db->select("
            a.tender_enquiry_id,
            a.invoice_date,
            a.invoice_no,
            v.vendor_name,
            v.gst AS vendor_vat,
            a.dp_charges AS cif_val,
            0 AS duty,
            a.dp_charges AS total_val,
            a.dp_vat_amt AS vat,
            a.dp_vat_amt AS vat_paid,
            (a.dp_charges + a.dp_vat_amt) AS grand_total
        ");
        $this->db->from('dp_bill_info a');
        $this->db->join('vendor_info v', 'a.vendor_id = v.vendor_id AND v.status = \'Active\'', 'left');
        $this->db->where_in('a.tender_enquiry_id', $enquiry_ids);
        $this->db->where('a.status', 'Active');
        $q3 = $this->db->get()->result_array();
        foreach ($q3 as $row) {
            $row['type'] = 'Delivery Partner Bill';
            $results[] = $row;
        }

        // 4. Customs Bills
        $this->db->select("
            a.tender_enquiry_id,
            COALESCE(NULLIF(a.declaration_date, '0000-00-00'), a.invoice_date) AS invoice_date,
            COALESCE(NULLIF(a.declaration_no, ''), a.invoice_no) AS invoice_no,
            v.vendor_name,
            v.gst AS vendor_vat,
            a.tot_amt_wo_vat AS cif_val,
            a.custom_duty AS duty,
            (a.tot_amt_wo_vat + a.custom_duty) AS total_val,
            a.vat_amt AS vat,
            a.vat_amt AS vat_paid,
            a.customs_tot_amt AS grand_total
        ");
        $this->db->from('customs_bill_info a');
        $this->db->join('vendor_info v', 'a.vendor_id = v.vendor_id AND v.status = \'Active\'', 'left');
        $this->db->where_in('a.tender_enquiry_id', $enquiry_ids);
        $this->db->where('a.status', 'Active');
        $q4 = $this->db->get()->result_array();
        foreach ($q4 as $row) {
            $row['type'] = 'Customs Bill';
            $results[] = $row;
        }

        return $results;
    }
}