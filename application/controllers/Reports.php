<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reports extends CI_Controller
{
    public function sales_nbr_report()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        $data['js'] = 'reports/reports.inc';
        //$data['js'] = 'reports/sales-nbr-report.inc';


        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');
        } else {
            $data['srch_from_date'] = $srch_from_date = date('Y-m-') . '01';
            $data['srch_to_date'] = $srch_to_date = date('Y-m-d');
        }

        $data['record_list'] = [];

        /* $sql = "
                    select
                    c.s_order,
                    c.template,
                    a.vat_payer_sales_grp,
                    a.vat_payer_sales_grp as vat_rtn_fld, 
                    a.invoice_no,
                    a.invoice_date,
                    b.gst as client_vat_no,
                    b.customer_name client_name,
                    'General trading' g_desc,
                    a.declaration_no,
                    a.declaration_date,
                    d.country_code,
                    (a.total_amount - a.tax_amount) as tot_amt_ex_tax,
                    a.tax_amount as vat_amt,
                    a.total_amount as tot_amt_inc_tax
                    from tender_enq_invoice_info as a
                    left join customer_info as b on b.customer_id = a.customer_id and b.`status` = 'Active'
                    left join vat_filing_head_info as c on c.vat_filing_head_name = a.vat_payer_sales_grp and c.vat_filing_head_type = 'Sales' and c.`status` = 'Active'  
                    left join country_info as d on d.country_name = b.country and a.`status` = 'Active'
                    where a.`status` = 'Active'
                    and a.invoice_date between '$srch_from_date'  and '$srch_to_date'
                    order by c.s_order asc , a.vat_payer_sales_grp ,  a.invoice_date , a.tender_enq_invoice_id asc 
            "; */

        $sql = "
                

                select 
                v.s_order,
                v.template,
                v.vat_filing_head_name vat_payer_sales_grp,
                a.vat_payer_sales_grp as vat_rtn_fld, 
                a.invoice_no,
                a.invoice_date,
                b.gst as client_vat_no,
                b.customer_name client_name,
                'General trading' g_desc,
                a.declaration_no,
                a.declaration_date,
                d.country_code,
                (a.total_amount - a.tax_amount) as tot_amt_ex_tax,
                a.tax_amount as vat_amt,
                a.total_amount as tot_amt_inc_tax 
                from vat_filing_head_info as v
                left join tender_enq_invoice_info as a 
                    on a.vat_payer_sales_grp = v.vat_filing_head_name 
                    and a.invoice_date between '$srch_from_date' and '$srch_to_date'
                    and a.`status` = 'Active' 
                left join customer_info as b on b.customer_id = a.customer_id and b.`status` = 'Active'
                left join country_info as d on d.country_name = b.country and a.`status` = 'Active'
                where v.`status` = 'Active' 
                and v.vat_filing_head_type = 'Sales'
                order by v.s_order asc , v.vat_filing_head_name ,  a.invoice_date , a.tender_enq_invoice_id asc


        ";
        $query = $this->db->query($sql);
        $rec = $query->result_array();

        $grouped = [];

        foreach ($rec as $row) {
            $s_order = $row['s_order'];   // group key
            $grouped[$s_order][] = $row;  // push row inside that s_order
        }

        $data['record_list'] = $grouped;



        $this->load->view('page/reports/sales-nbr-report', $data);
    }

    public function purchase_nbr_report()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        $data['js'] = 'reports/reports.inc';
        //$data['js'] = 'reports/purchase-nbr-report.inc';


        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');
        } else {
            $data['srch_from_date'] = $srch_from_date = date('Y-m-') . '01';
            $data['srch_to_date'] = $srch_to_date = date('Y-m-d');
        }

        $data['record_list'] = [];

        $sql = "
                select
                c.s_order,
                c.template,
                a.vat_payer_purchase_grp,
                a.vat_payer_purchase_grp as vat_rtn_fld, 
                a.invoice_no,
                a.invoice_date,
                b.gst as supplier_vat_no,
                b.vendor_name supplier_name,
                b.crno,
                'General trading' g_desc,
                a.declaration_no,
                a.declaration_date,
                d.country_code,
                (a.total_amount - a.tax_amount) as tot_amt_ex_tax,
                a.tax_amount as vat_amt,
                a.total_amount as tot_amt_inc_tax
                from vendor_purchase_invoice_info as a
                left join vendor_info as b on b.vendor_id = a.vendor_id and b.`status` = 'Active'
                left join vat_filing_head_info as c on c.vat_filing_head_name = a.vat_payer_purchase_grp and c.vat_filing_head_type = 'Purchase' and c.`status` = 'Active'
                left join country_info as d on d.country_name = b.country and a.`status` = 'Active'
                where a.`status` = 'Active'
                and a.invoice_date between '$srch_from_date'  and '$srch_to_date'
                order by c.s_order asc , a.vat_payer_purchase_grp ,  a.invoice_date , a.vendor_purchase_invoice_id asc

        ";


        $sql = "
                select
                v.s_order,
                v.template,
                v.vat_filing_head_name as vat_payer_purchase_grp,
                a.vat_payer_purchase_grp as vat_rtn_fld, 
                a.invoice_no,
                a.invoice_date,
                b.gst as supplier_vat_no,
                b.vendor_name supplier_name,
                b.crno,
                'General trading' g_desc,
                a.declaration_no,
                a.declaration_date,
                d.country_code,
                (a.total_amount - a.tax_amount) as tot_amt_ex_tax,
                a.tax_amount as vat_amt,
                a.total_amount as tot_amt_inc_tax
                from vat_filing_head_info as v
                left join vendor_purchase_invoice_info as a 
                    on a.vat_payer_purchase_grp = v.vat_filing_head_name 
                    and a.invoice_date between '$srch_from_date' and '$srch_to_date' 
                    and a.`status` = 'Active'
                left join vendor_info as b on b.vendor_id = a.vendor_id and b.`status` = 'Active'
                left join vat_filing_head_info as c on c.vat_filing_head_name = a.vat_payer_purchase_grp and c.vat_filing_head_type = 'Purchase' and c.`status` = 'Active'
                left join country_info as d on d.country_name = b.country and a.`status` = 'Active'
                where v.`status` = 'Active' 
                and v.vat_filing_head_type = 'Purchase'
                order by v.s_order asc , v.vat_filing_head_name ,  a.invoice_date , a.vendor_purchase_invoice_id asc

        ";
        $query = $this->db->query($sql);
        $rec = $query->result_array();

        $grouped = [];

        foreach ($rec as $row) {
            $s_order = $row['s_order'];   // group key
            $grouped[$s_order][] = $row;  // push row inside that s_order
        }

        $data['record_list'] = $grouped;



        $this->load->view('page/reports/purchase-nbr-report', $data);
    }


    public function tender_enquiry_summary_report()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data = array();
        $data['js'] = 'summary/tender-enquiry-summary-report.inc';
        $data['s_url'] = 'tender-quotation-list';
        $data['title'] = 'Tender Enquiry Summary Report';

        $where = "1=1";


        // Company Filter
        if ($this->input->post('srch_enquiry_no_id') !== null) {
            $data['srch_enquiry_no_id'] = $srch_enquiry_no_id = $this->input->post('srch_enquiry_no_id');
            $this->session->set_userdata('srch_enquiry_no_id', $srch_enquiry_no_id);
        } elseif ($this->session->userdata('srch_enquiry_no_id')) {
            $data['srch_enquiry_no_id'] = $srch_enquiry_no_id = $this->session->userdata('srch_enquiry_no_id');
        } else {
            $data['srch_enquiry_no_id'] = $srch_enquiry_no_id = '';
        }
        if (!empty($srch_enquiry_no_id)) {
            $where = " (a.tender_enquiry_id = '" . $this->db->escape_str($srch_enquiry_no_id) . "')";
        }

        // Company Filter
        if ($this->input->post('tender_enquiry_id') !== null) {
            $data['tender_enquiry_id'] = $tender_enquiry_id = $this->input->post('tender_enquiry_id');
            $this->session->set_userdata('tender_enquiry_id', $tender_enquiry_id);
        } elseif ($this->session->userdata('tender_enquiry_id')) {
            $data['tender_enquiry_id'] = $tender_enquiry_id = $this->session->userdata('tender_enquiry_id');
        } else {
            $data['tender_enquiry_id'] = $tender_enquiry_id = '';
        }
        if (!empty($tender_enquiry_id)) {
            $where = " (a.tender_enquiry_id = '" . $this->db->escape_str($tender_enquiry_id) . "')";
        }


        $data['tender_enquiry_list'] = array();
        $data['tender_quotation_list'] = array();
        $data['tender_po_list'] = array();
        $data['tender_dc_list'] = array();
        $data['tender_invoice_list'] = array();



        $data['vendor_rate_enquiry_list'] = array();
        $data['vendor_quotation_list'] = array();
        $data['vendor_po_list'] = array();
        $data['vendor_pur_inward_list'] = array();
        $data['vendor_purchase_invoice_list'] = array();

        if (!empty($tender_enquiry_id)) {
            $sql = "
                SELECT 
                    a.tender_enquiry_id,
                    a.enquiry_no,
                    a.enquiry_date,
                    a.tender_name,
                    a.tender_status,
                    c.company_name,
                    d.customer_name,

                    b.item_code,
                    b.item_desc,
                    b.uom,
                    b.qty
                FROM tender_enquiry_info as a
                left join tender_enquiry_item_info as b on a.tender_enquiry_id = b.tender_enquiry_id and b.`status`='Active'
                LEFT JOIN company_info as c 
                    ON a.company_id = c.company_id 
                    AND c.status='Active'
                LEFT JOIN customer_info as d 
                    ON a.customer_id = d.customer_id 
                    AND d.status='Active' 
                WHERE a.status='Active' AND a.tender_enquiry_id = '" . $this->db->escape_str($tender_enquiry_id) . "'
                ORDER BY a.tender_enquiry_id ASC
            ";

            $query = $this->db->query($sql);

            // GROUP DATA BY ENQUIRY
            foreach ($query->result_array() as $row) {
                $data['tender_enquiry_list'][$row['tender_enquiry_id']]['info'] = $row;
                $data['tender_enquiry_list'][$row['tender_enquiry_id']]['items'][] = $row;
            }

            $sql = "
                SELECT 
                    a.tender_quotation_id,
                    a.quotation_no,
                    a.tender_ref_no,
                    a.quote_date,
                    a.quotation_status,
                    c.company_name,
                    d.customer_name,

                    b.tender_quotation_item_id,
                    b.item_code,
                    b.item_desc,
                    b.uom,
                    b.qty,
                    b.rate,
                    b.gst,
                    b.amount,
                    e.currency_code,
                    e.decimal_point

                FROM tender_quotation_info as a

                LEFT JOIN tender_quotation_item_info as b 
                    ON a.tender_quotation_id = b.tender_quotation_id 
                    AND b.status='Active'

                LEFT JOIN company_info as c 
                    ON a.company_id = c.company_id 
                    AND c.status='Active'

                LEFT JOIN customer_info as d 
                    ON a.customer_id = d.customer_id 
                    AND d.status='Active'
                left join currencies_info as e on a.currency_id = e.currency_id and e.`status`='Active'

                WHERE a.status='Active'
                AND a.tender_enquiry_id = '" . $this->db->escape_str($tender_enquiry_id) . "'

                ORDER BY a.tender_quotation_id ASC, b.tender_quotation_item_id ASC
            ";

            $query = $this->db->query($sql);

            // GROUP DATA BY QUOTATION
            foreach ($query->result_array() as $row) {
                $data['tender_quotation_list'][$row['tender_quotation_id']]['info'] = $row;
                $data['tender_quotation_list'][$row['tender_quotation_id']]['items'][] = $row;
            }

            //tender po list
            $sql = "
                SELECT 
                    a.tender_po_id,
                    a.our_po_no,
                    a.po_date,
                    f.enquiry_no, 
                    a.po_status,
                    c.company_name,
                    d.customer_name,

                    b.tender_po_item_id,
                    b.item_code,
                    b.item_desc,
                    b.uom,
                    b.qty,
                    b.rate,
                    b.gst,
                    b.amount,
                    e.currency_code,
                    e.decimal_point

                FROM customer_tender_po_info as a
                LEFT JOIN tender_po_item_info as b 
                    ON a.tender_po_id = b.tender_po_id 
                    AND b.status='Active'

                LEFT JOIN company_info as c 
                    ON a.company_id = c.company_id 
                    AND c.status='Active'

                LEFT JOIN customer_info as d 
                    ON a.customer_id = d.customer_id 
                    AND d.status='Active'
                left join currencies_info as e on a.currency_id = e.currency_id and e.`status`='Active'
                left join tender_enquiry_info as f on a.tender_enquiry_id = f.tender_enquiry_id and f.`status`='Active'

                WHERE a.status='Active'
                AND a.tender_enquiry_id = '" . $this->db->escape_str($tender_enquiry_id) . "'

                ORDER BY a.tender_po_id ASC, b.tender_po_item_id ASC
            ";

            $query = $this->db->query($sql);

            // GROUP DATA BY PO
            foreach ($query->result_array() as $row) {
                $data['tender_po_list'][$row['tender_po_id']]['info'] = $row;
                $data['tender_po_list'][$row['tender_po_id']]['items'][] = $row;
            }

            //tender dc list
            $sql = "
                SELECT 
                    a.tender_dc_id,
                    a.dc_no,
                    a.dc_date,
                    f.enquiry_no, 
                    'Dc' AS dc_status,
                    c.company_name,
                    d.customer_name,

                    b.tender_dc_item_id,
                    b.item_code,
                    b.item_desc,
                    b.uom,    
                    b.qty  
                FROM tender_dc_info as a
                LEFT JOIN tender_dc_item_info as b 
                    ON a.tender_dc_id = b.tender_dc_id 
                    AND b.status='Active'

                LEFT JOIN company_info as c 
                    ON a.company_id = c.company_id 
                    AND c.status='Active'

                LEFT JOIN customer_info as d 
                    ON a.customer_id = d.customer_id 
                    AND d.status='Active' 
                left join tender_enquiry_info as f on a.tender_enquiry_id = f.tender_enquiry_id and f.`status`='Active'

                WHERE a.status='Active'
                AND a.tender_enquiry_id = '" . $this->db->escape_str($tender_enquiry_id) . "'

                ORDER BY a.tender_dc_id ASC, b.tender_dc_item_id ASC
            ";

            $query = $this->db->query($sql);

            // GROUP DATA BY DC
            foreach ($query->result_array() as $row) {
                $data['tender_dc_list'][$row['tender_dc_id']]['info'] = $row;
                $data['tender_dc_list'][$row['tender_dc_id']]['items'][] = $row;
            }

            //tender invoice list
            $sql = "
                SELECT 
                    a.tender_enq_invoice_id,
                    a.invoice_no,
                    a.invoice_date,
                    f.enquiry_no, 
                    a.invoice_status,
                    c.company_name,
                    d.customer_name,

                    b.tender_enq_invoice_item_id,
                    b.item_code,
                    b.item_desc,
                    b.uom,
                    b.qty,
                    b.rate,
                    b.gst,
                    b.amount,
                    e.currency_code,
                    e.decimal_point

                FROM tender_enq_invoice_info as a
                LEFT JOIN tender_enq_invoice_item_info as b 
                    ON a.tender_enq_invoice_id = b.tender_enq_invoice_id 
                    AND b.status='Active'

                LEFT JOIN company_info as c 
                    ON a.company_id = c.company_id 
                    AND c.status='Active'    

                LEFT JOIN customer_info as d 
                    ON a.customer_id = d.customer_id 
                    AND d.status='Active'
                left join currencies_info as e on a.currency_id = e.currency_id and e.`status`='Active'
                left join tender_enquiry_info as f on a.tender_enquiry_id = f.tender_enquiry_id and f.`status`='Active'

                WHERE a.status='Active'
                AND a.tender_enquiry_id = '" . $this->db->escape_str($tender_enquiry_id) . "'

                ORDER BY a.tender_enq_invoice_id ASC, b.tender_enq_invoice_item_id ASC
            ";

            $query = $this->db->query($sql);

            // GROUP DATA BY INVOICE
            foreach ($query->result_array() as $row) {
                $data['tender_invoice_list'][$row['tender_enq_invoice_id']]['info'] = $row;
                $data['tender_invoice_list'][$row['tender_enq_invoice_id']]['items'][] = $row;
            }

            //vendor_rate_enquiry_list 
            $sql = "
                SELECT 
                    a.vendor_rate_enquiry_id,
                    a.enquiry_no,
                    a.enquiry_date,
                    f.enquiry_no, 
                    a.vendor_rate_enquiry_status,
                    c.company_name,
                    d.customer_name,

                    b.vendor_rate_enquiry_item_id,
                    b.item_code,
                    b.item_desc,
                    b.uom,
                    b.qty,
                    b.rate,
                    b.gst,
                    b.amount

                FROM vendor_rate_enquiry_info as a
                LEFT JOIN vendor_rate_enquiry_item_info as b 
                    ON a.vendor_rate_enquiry_id = b.vendor_rate_enquiry_id 
                    AND b.status='Active'

                LEFT JOIN company_info as c 
                    ON a.company_id = c.company_id 
                    AND c.status='Active'    

                LEFT JOIN customer_info as d 
                    ON a.customer_id = d.customer_id 
                    AND d.status='Active'
               
                left join tender_enquiry_info as f on a.tender_enquiry_id = f.tender_enquiry_id and f.`status`='Active'

                WHERE a.status='Active'
                AND a.tender_enquiry_id = '" . $this->db->escape_str($tender_enquiry_id) . "'

                ORDER BY a.vendor_rate_enquiry_id ASC, b.vendor_rate_enquiry_item_id ASC
            ";

            $query = $this->db->query($sql);

            // GROUP DATA BY INVOICE
            foreach ($query->result_array() as $row) {
                $data['vendor_rate_enquiry_list'][$row['vendor_rate_enquiry_id']]['info'] = $row;
                $data['vendor_rate_enquiry_list'][$row['vendor_rate_enquiry_id']]['items'][] = $row;
            }


            //vendor quotation list
            $sql = "
                SELECT 
                    a.vendor_quote_id,
                    a.quote_no,
                    a.quote_date,
                    f.enquiry_no, 
                    a.quote_status,
                    c.company_name,
                    d.customer_name,

                    b.vendor_quote_item_id,
                    b.item_code,
                    b.item_desc,
                    b.uom,
                    b.qty,
                    b.rate,
                    b.gst,
                    b.amount,
                    e.currency_code,
                    e.decimal_point

                FROM vendor_quotation_info as a
                LEFT JOIN vendor_quote_item_info as b 
                    ON a.vendor_quote_id = b.vendor_quote_id
                    AND b.status='Active'

                LEFT JOIN company_info as c 
                    ON a.company_id = c.company_id 
                    AND c.status='Active'    

                LEFT JOIN customer_info as d 
                    ON a.customer_id = d.customer_id 
                    AND d.status='Active' 
                left join currencies_info as e on a.currency_id = e.currency_id and e.`status`='Active'
                left join tender_enquiry_info as f on a.tender_enquiry_id = f.tender_enquiry_id and f.`status`='Active' 
                WHERE a.status='Active'
                AND a.tender_enquiry_id = '" . $this->db->escape_str($tender_enquiry_id) . "'

                ORDER BY a.vendor_quote_id ASC, b.vendor_quote_item_id ASC
            ";

            $query = $this->db->query($sql);

            // GROUP DATA BY INVOICE
            foreach ($query->result_array() as $row) {
                $data['vendor_quotation_list'][$row['vendor_quote_id']]['info'] = $row;
                $data['vendor_quotation_list'][$row['vendor_quote_id']]['items'][] = $row;
            }

            //vendor po list
            $sql = "
                SELECT 
                    a.vendor_po_id,
                    a.po_no,
                    a.po_date,
                    f.enquiry_no, 
                    a.po_status,
                    c.company_name,
                    d.customer_name,

                    b.vendor_po_item_id,
                    b.item_code,
                    b.item_desc,                    
                    b.uom,
                    b.qty,
                    b.rate,
                    b.gst,
                    b.amount,
                    e.currency_code,
                    e.decimal_point

                FROM vendor_po_info as a
                LEFT JOIN vendor_po_item_info as b 
                    ON a.vendor_po_id = b.vendor_po_id
                    AND b.status='Active'

                LEFT JOIN company_info as c 
                    ON a.company_id = c.company_id 
                    AND c.status='Active'    

                LEFT JOIN customer_info as d 
                    ON a.customer_id = d.customer_id 
                    AND d.status='Active'   
                left join currencies_info as e on a.currency_id = e.currency_id and e.`status`='Active'
                left join tender_enquiry_info as f on a.tender_enquiry_id = f.tender_enquiry_id and f.`status`='Active' 
                WHERE a.status='Active'
                AND a.tender_enquiry_id = '" . $this->db->escape_str($tender_enquiry_id) . "'

                ORDER BY a.vendor_po_id ASC, b.vendor_po_item_id ASC
            ";

            $query = $this->db->query($sql);

            // GROUP DATA BY INVOICE
            foreach ($query->result_array() as $row) {
                $data['vendor_po_list'][$row['vendor_po_id']]['info'] = $row;
                $data['vendor_po_list'][$row['vendor_po_id']]['items'][] = $row;
            }


            //vendor purchase inward list
            $sql = "
                 SELECT 
                    a.vendor_pur_inward_id,
                    a.inward_no,
                    a.inward_date,
                    f.enquiry_no, 
                    'Delivered' as inward_status,
                    c.company_name,
                    f.tender_name,
                    d.customer_name,

                    b.vendor_pur_inward_item_id,
                    b.item_code,
                    b.item_desc,                    
                    b.uom,
                    b.qty
                   
                FROM vendor_pur_inward_info as a
                LEFT JOIN vendor_pur_inward_item_info as b 
                    ON a.vendor_pur_inward_id = b.vendor_pur_inward_id
                    AND b.status='Active'

                LEFT JOIN company_info as c 
                    ON a.company_id = c.company_id 
                    AND c.status='Active'    

                LEFT JOIN customer_info as d 
                    ON a.customer_id = d.customer_id 
                    AND d.status='Active'   
                left join tender_enquiry_info as f on a.tender_enquiry_id = f.tender_enquiry_id and f.`status`='Active' 
                WHERE a.status='Active'
                AND a.tender_enquiry_id = '" . $this->db->escape_str($tender_enquiry_id) . "'

                ORDER BY a.vendor_pur_inward_id ASC, b.vendor_pur_inward_item_id ASC
            ";

            $query = $this->db->query($sql);

            // GROUP DATA BY INVOICE
            foreach ($query->result_array() as $row) {
                $data['vendor_pur_inward_list'][$row['vendor_pur_inward_id']]['info'] = $row;
                $data['vendor_pur_inward_list'][$row['vendor_pur_inward_id']]['items'][] = $row;
            }

            // vendor invoice list 
            $sql = "
                SELECT 
                    a.vendor_purchase_invoice_id,
                    a.invoice_no,
                    a.invoice_date,
                    f.enquiry_no, 
                    'Invoice ' as invoice_status,
                    c.company_name,
                    f.tender_name,
                    d.customer_name,

                    b.vendor_purchase_invoice_item_id,
                    b.item_code,
                    b.item_desc,                    
                    b.uom,
                    b.qty,
                    b.rate,
                    b.gst,
                    b.amount 

                FROM vendor_purchase_invoice_info as a
                LEFT JOIN vendor_purchase_invoice_item_info as b 
                    ON a.vendor_purchase_invoice_id = b.vendor_purchase_invoice_id
                    AND b.status='Active'

                LEFT JOIN company_info as c 
                    ON a.company_id = c.company_id 
                    AND c.status='Active'    

                LEFT JOIN customer_info as d 
                    ON a.customer_id = d.customer_id 
                    AND d.status='Active'   
                left join tender_enquiry_info as f on a.tender_enquiry_id = f.tender_enquiry_id and f.`status`='Active' 
                WHERE a.status='Active'
                AND a.tender_enquiry_id = '" . $this->db->escape_str($tender_enquiry_id) . "'

                ORDER BY a.vendor_purchase_invoice_id ASC, b.vendor_purchase_invoice_item_id ASC
            ";

            $query = $this->db->query($sql);

            // GROUP DATA BY INVOICE
            foreach ($query->result_array() as $row) {
                $data['vendor_invoice_list'][$row['vendor_purchase_invoice_id']]['info'] = $row;
                $data['vendor_invoice_list'][$row['vendor_purchase_invoice_id']]['items'][] = $row;
            }
        }

        $this->load->view('page/summary/tender-enquiry-summary-report', $data);
    }

    public function tender_enquiry_id_search()
    {
        $term = $this->input->post('search');


        $sql = "      
            SELECT 
            concat(a.tender_enquiry_id , ' || ', ifnull(b.company_code,'') , ' || ', ifnull(a.company_sno,'') ,  ' || ' , ifnull(c.customer_code,'') ,  ' || ' , ifnull(a.customer_sno,''),  ' || ' , ifnull(a.enquiry_no,'')) as tender_ref,
            concat(ifnull(b.company_code,'') , '/', ifnull(a.company_sno,'') ,  '/' , ifnull(c.customer_code,'') ,  '/' , ifnull(a.customer_sno,''),  '/' , ifnull(a.enquiry_no,'')) as enq1,
            concat(ifnull(b.company_code,'') , '/', ifnull(a.company_sno,'') ,  '/' , ifnull(c.customer_code,'') ,  '/' , ifnull(a.customer_sno,''),  '/' , DATE_FORMAT(a.enquiry_date,'%Y') ) as enq,
            a.company_id,
            a.customer_id,
            a.tender_enquiry_id,
            c.customer_name,
            a.enquiry_no
            FROM tender_enquiry_info AS a
            LEFT JOIN company_info AS b ON a.company_id = b.company_id AND b.status = 'Active'
            LEFT JOIN customer_info AS c ON a.customer_id = c.customer_id AND c.status = 'Active'
            WHERE  a.`status` = 'Active' 
            having enq like '%" . $this->db->escape_like_str($term) . "%'
            ORDER BY a.tender_enquiry_id desc, a.enquiry_no ASC  
        ";
        //and a.enquiry_no like '%" . $this->db->escape_like_str($term) . "%'

        $query = $this->db->query($sql);

        $result = [];

        foreach ($query->result() as $row) {
            $result[] = [
                'label' => $row->enq,       // what user sees 
                //'value' => $row->enq . ' [ ' . $row->enquiry_no . ' ]',        // filled in textbox
                'value' => $row->enq,        // filled in textbox
                'company_id' => $row->company_id,
                'customer_id' => $row->customer_id,
                'tender_enquiry_id' => $row->tender_enquiry_id,
                'customer_name' => $row->customer_name,
                'enquiry_no' => $row->enquiry_no
            ];
        }
        echo json_encode($result);

    }

}

?>