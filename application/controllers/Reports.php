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

        if (isset($_POST['vat_payer_sales_grp'])) {
            $data['vat_payer_sales_grp'] = $vat_payer_sales_grp = $this->input->post('vat_payer_sales_grp');
        } else {
            $data['vat_payer_sales_grp'] = $vat_payer_sales_grp = '';
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
                and ('" . $this->db->escape_str($vat_payer_sales_grp) . "' = '' or v.vat_filing_head_name = '" . $this->db->escape_str($vat_payer_sales_grp) . "')
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


        $sql = "
            SELECT 
            vat_filing_head_name 
            FROM vat_filing_head_info 
            WHERE status = 'Active' 
            and vat_filing_head_type = 'Sales'
            ORDER BY vat_filing_head_id ASC
            ";
        $query = $this->db->query($sql);
        $data['vat_payer_sales_opt'] = ['' => 'All VAT Payer Sales Category'];
        foreach ($query->result_array() as $row) {
            $data['vat_payer_sales_opt'][$row['vat_filing_head_name']] = $row['vat_filing_head_name'];
        }



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

        if (isset($_POST['vat_payer_purchase_grp'])) {
            $data['vat_payer_purchase_grp'] = $vat_payer_purchase_grp = $this->input->post('vat_payer_purchase_grp');
        } else {
            $data['vat_payer_purchase_grp'] = $vat_payer_purchase_grp = '';
        }

        $data['record_list'] = [];

        // $sql = "
        //         select
        //         c.s_order,
        //         c.template,
        //         a.vat_payer_purchase_grp,
        //         a.vat_payer_purchase_grp as vat_rtn_fld, 
        //         a.invoice_no,
        //         a.invoice_date,
        //         b.gst as supplier_vat_no,
        //         b.vendor_name supplier_name,
        //         b.crno,
        //         'General trading' g_desc,
        //         a.declaration_no,
        //         a.declaration_date,
        //         d.country_code,
        //         (a.total_amount - a.tax_amount) as tot_amt_ex_tax,
        //         a.tax_amount as vat_amt,
        //         a.total_amount as tot_amt_inc_tax
        //         from vendor_purchase_invoice_info as a
        //         left join vendor_info as b on b.vendor_id = a.vendor_id and b.`status` = 'Active'
        //         left join vat_filing_head_info as c on c.vat_filing_head_name = a.vat_payer_purchase_grp and c.vat_filing_head_type = 'Purchase' and c.`status` = 'Active'
        //         left join country_info as d on d.country_name = b.country and a.`status` = 'Active'
        //         where a.`status` = 'Active'
        //         and a.invoice_date between '$srch_from_date'  and '$srch_to_date'
        //         order by c.s_order asc , a.vat_payer_purchase_grp ,  a.invoice_date , a.vendor_purchase_invoice_id asc

        // ";


        /* $sql = "
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

        "; */


        $sql = "
        select 
        c.s_order,
        c.template,
        c.vat_filing_head_name as vat_payer_purchase_grp, 
        a1.vat_payer_purchase_grp as vat_rtn_fld, 
        a1.invoice_no,   
        a1.inv_date as invoice_date,
        b.gst as supplier_vat_no,
        b.vendor_name supplier_name,
        b.crno,
        a1.g_desc,
        a1.total_amount_wo_tax as tot_amt_ex_tax,
        a1.tax_amount as vat_amt,
        a1.total_amount as tot_amt_inc_tax,
        a1.declaration_date,
        a1.declaration_no
        from
        (
            (
            select  
            'Supplier Bill' as v_type,
            a.vendor_id,  
            a.invoice_no,
            a.entry_date as inv_date,  
            a.vat_payer_purchase_grp,
            'General trading' g_desc,
            a.total_amount_wo_tax,
            a.tax_amount,
            a.total_amount,
            a.declaration_date,
            a.declaration_no
            from vendor_purchase_invoice_info as a  
            where a.`status` = 'Active' 
            and a.only_accounting_entry != '1'
            and a.entry_date between '$srch_from_date' and '$srch_to_date'
            order by  a.entry_date asc 
            ) union all (
             select 
            'Local Bill' as v_type,
            a.vendor_id,
            a.invoice_no,
            a.inv_entry_date as inv_date,
            a.vat_payer_purchase_grp,
            'General trading' g_desc,
            a.tot_amt_wo_tax as total_amount_wo_tax,
            a.vat_amt as tax_amount,
            a.tot_amt_with_tax as total_amount,
            '' as declaration_date,
            '' as declaration_no
            from local_purchase_bill_info as a
            where a.`status` = 'Active'  
            and a.inv_entry_date between '$srch_from_date' and '$srch_to_date'
            order by  a.inv_entry_date asc  
            ) union all (
            select 
            'DP Bill' as v_type,
            a.vendor_id,
            a.invoice_no,
            a.inv_entry_date as inv_date,
            a.vat_payer_purchase_grp,
            'Service' g_desc,
            a.dp_charges as total_amount_wo_tax,
            a.dp_vat_amt as tax_amount,
            (a.dp_charges + a.dp_vat_amt) as total_amount,
            '' as declaration_date,
            '' as declaration_no
            from dp_bill_info as a
            where a.`status` = 'Active'  
            and a.inv_entry_date between '$srch_from_date' and '$srch_to_date'
            order by a.inv_entry_date asc 
            ) union all (
            select 
            'Customs Bill' as v_type,
            a.vendor_id,
            a.invoice_no,
            a.inv_entry_date as inv_date,
            a.vat_payer_purchase_grp,
            'Service' g_desc,
            a.tot_amt_wo_vat as total_amount_wo_tax,
            a.vat_amt as tax_amount,
            a.customs_tot_amt as total_amount,
            '' as declaration_date,
            '' as declaration_no
            from customs_bill_info as a
            where a.`status` = 'Active'  
            and a.ac_type_opt = 'Accountable'
            and a.inv_entry_date between '$srch_from_date' and '$srch_to_date'
            order by a.inv_entry_date asc 
            ) 
        ) as a1
         left join vendor_info as b on b.vendor_id = a1.vendor_id and b.`status` = 'Active'
         left join vat_filing_head_info as c on c.vat_filing_head_name = a1.vat_payer_purchase_grp and c.vat_filing_head_type = 'Purchase' and c.`status` = 'Active'
         where ('" . $this->db->escape_str($vat_payer_purchase_grp) . "' = '' or a1.vat_payer_purchase_grp = '" . $this->db->escape_str($vat_payer_purchase_grp) . "')
         order by c.s_order asc , c.vat_filing_head_name ,  a1.inv_date ,  a1.v_type 
        ";


        $query = $this->db->query($sql);
        $rec = $query->result_array();

        $grouped = [];

        foreach ($rec as $row) {
            $s_order = $row['s_order'];   // group key
            $grouped[$s_order][] = $row;  // push row inside that s_order
        }

        $data['record_list'] = $grouped;


        $sql = "
            SELECT 
            vat_filing_head_name 
            FROM vat_filing_head_info 
            WHERE status = 'Active' 
            and vat_filing_head_type = 'Purchase'
            ORDER BY vat_filing_head_id ASC
            ";
        $query = $this->db->query($sql);
        $data['vat_payer_purchase_opt'] = ['' => 'All VAT Payer Purchase Category'];
        foreach ($query->result_array() as $row) {
            $data['vat_payer_purchase_opt'][$row['vat_filing_head_name']] = $row['vat_filing_head_name'];
        }



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
        $data['title'] = 'Tender Info Report';

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
                    b.tender_enquiry_item_id,
                    a.enquiry_no,
                    a.enquiry_date,
                    a.opening_date,
                    a.closing_date,
                    a.tender_name,
                    a.tender_status,
                    c.company_name,
                    d.customer_name,

                    b.serial_no,

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
                    a.tender_enquiry_id,
                    b.tender_enquiry_item_id,
                    a.quotation_no,
                    a.tender_ref_no,
                    a.quote_date,
                    a.quotation_status,
                    c.company_name,
                    d.customer_name,
                    a.transport_charges,
                    a.other_charges,
                    a.remarks,

                    b.tender_quotation_item_id,
                    b.item_code,
                    b.item_desc,
                    b.uom,
                    f.serial_no,
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
                left join tender_enquiry_item_info as f on b.tender_enquiry_item_id = f.tender_enquiry_item_id and f.`status`='Active'

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
                    a.tender_enquiry_id,
                    a.tender_quotation_id,
                    b.tender_quotation_item_id,
                    a.customer_po_no,
                    a.po_received_date,
                    a.delivery_date,
                    a.po_date, 
                    a.po_status,
                    c.company_name,
                    d.customer_name,

                    b.tender_po_item_id,
                    b.item_code,
                    b.serial_no,
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
                    a.tender_po_id,
                    a.tender_enquiry_id, 
                    a.dc_no,
                    a.dc_date,
                    f.enquiry_no, 
                    'Dc' AS dc_status,
                    c.company_name,
                    d.customer_name,

                    b.vendor_pur_inward_id,
                    b.vendor_pur_inward_item_id,
                    b.tender_dc_item_id,
                    b.item_code,
                    b.item_desc,
                    b.uom,    
                    b.qty,
                    item.serial_no
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
                left join tender_enquiry_item_info as item on a.tender_enquiry_id = item.tender_enquiry_id and item.status='Active'

                WHERE a.status='Active'
                AND a.tender_enquiry_id = '" . $this->db->escape_str($tender_enquiry_id) . "'
                group by b.tender_dc_item_id 

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
                    e.decimal_point,
                    tpo.serial_no

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
                left join customer_tender_po_info as po on a.tender_po_id = po.tender_po_id and po.`status`='Active'
                left join tender_po_item_info  as tpo on b.tender_po_item_id = tpo.tender_po_item_id and tpo.`status`='Active'
              

                WHERE a.status='Active'
                AND a.tender_enquiry_id = '" . $this->db->escape_str($tender_enquiry_id) . "'
                group by b.tender_enq_invoice_item_id
                ORDER BY a.tender_enq_invoice_id ASC, b.tender_enq_invoice_item_id ASC
            ";

            $query = $this->db->query($sql);

            // GROUP DATA BY INVOICE
            foreach ($query->result_array() as $row) {
                $data['tender_invoice_list'][$row['tender_enq_invoice_id']]['info'] = $row;
                $data['tender_invoice_list'][$row['tender_enq_invoice_id']]['items'][] = $row;
            }


            // tender invoice receipt list

            $sql = "
                select
                a.tender_receipt_invoice_id,
                b.tender_receipt_id,
                a.tender_enquiry_id,
                a.tender_enq_invoice_id,
                b.receipt_no,
                DATE_FORMAT(b.receipt_date,'%d-%m-%Y') as receipt_date,
                c.customer_name, 
                a.inv_amount,
                d.invoice_no ,
                b.receipt_mode,
                b.receipt_type,
                b.cheque_date,
                b.cheque_no,
                b.cheque_bank 

                from tender_receipt_invoice_info as  a 
                left join tender_receipt_info as b on a.tender_receipt_id = b.tender_receipt_id and b.`status`='Active'
                left join customer_info as c on b.customer_id = c.customer_id and c.`status`='Active'
                left join tender_enq_invoice_info as d on a.tender_enq_invoice_id = d.tender_enq_invoice_id and d.`status`='Active'
                left join company_bank_info as e on b.bank_id = e.bank_id and e.`status`='Active'
                where a.`status`='Active'
                AND a.tender_enquiry_id = '" . $this->db->escape_str($tender_enquiry_id) . "'
            ";

            $query = $this->db->query($sql);

            // GROUP DATA BY INVOICE
            foreach ($query->result_array() as $row) {
                $data['tender_receipt_invoice_list'][$row['tender_receipt_id']]['info'] = $row;
                $data['tender_receipt_invoice_list'][$row['tender_receipt_id']]['items'][] = $row;
            }



            //vendor_rate_enquiry_list 
            $sql = "
                SELECT 
                    a.vendor_rate_enquiry_id,
                    a.enquiry_no as vendor_rate_enquiry_no,
                    a.enquiry_date as vendor_rate_enquiry_date, 
                    a.vendor_rate_enquiry_status,
                    c.company_name as company_name, 
                    d.customer_name,
                    a.opening_date,
                    a.closing_date,
                    b.vendor_rate_enquiry_item_id,
                    a.tender_enquiry_id,
                    b.tender_enquiry_item_id,
                    b.item_code,
                    b.item_desc,
                    b.uom,
                    b.qty,
                    b.rate,
                    b.gst,
                    b.amount,  
                    e.vendor_name,
                    f.contact_person_name as vendor_contact_person

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
                left join vendor_info as e on a.vendor_id = e.vendor_id and e.`status`='Active'
                left join vendor_contact_info as f on a.vendor_id = f.vendor_id and e.vendor_id = f.vendor_id and f.`status`='Active'
           
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
                    a.tender_enquiry_id,
                   
                    a.quote_status,
                    c.company_name,
                    d.customer_name,

                    b.vendor_quote_item_id,
                    b.vendor_rate_enquiry_item_id,
                    b.item_code,
                    b.item_desc,
                    b.uom,
                    b.qty,
                    b.rate,
                    b.gst,
                    b.amount,
                    e.currency_code,
                    e.decimal_point,
                    
                    v.vendor_name,
                    vc.contact_person_name,

                    a.transport_charges,
                    a.other_charges

                 
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

              	left join vendor_info as v on a.vendor_id = v.vendor_id and v.`status`='Active'
              	left join vendor_contact_info  as vc on a.vendor_contact_person_id = vc.vendor_contact_id and vc.`status`='Active'
            
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
                    a.tender_enquiry_id,
                    a.po_no,
                    a.po_date, 
                    a.po_status,
                    c.company_name,
                    d.customer_name,

                    b.vendor_po_item_id,
                    b.vendor_quote_item_id,
                    b.item_code,
                    b.item_desc,                    
                    b.uom,
                    b.qty,
                    b.rate,
                    b.gst,
                    b.amount,
                    e.currency_code,
                    e.decimal_point,

                    v.vendor_name,
                    vc.contact_person_name,

                    a.transport_charges,
                    a.other_charges

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
                
              	left join vendor_info as v on a.vendor_id = v.vendor_id and v.`status`='Active'
              	left join vendor_contact_info  as vc on a.vendor_contact_person_id = vc.vendor_contact_id and vc.`status`='Active' 

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
                    a.tender_enquiry_id,
                    a.vendor_po_id,
                    a.inward_no,
                    a.inward_date, 
                    'Delivered' as inward_status,
                    c.company_name, 
                    d.customer_name,

                    b.vendor_pur_inward_item_id,
                    b.vendor_po_item_id,
                    b.item_code,
                    b.item_desc,                    
                    b.uom,
                    b.qty,
                    

                     v.vendor_name,
                    vc.contact_person_name
                   
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
              
              	left join vendor_info as v on a.vendor_id = v.vendor_id and v.`status`='Active'
              	left join vendor_contact_info  as vc on a.vendor_contact_person_id = vc.vendor_contact_id and vc.`status`='Active' 

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
                    a.tender_enquiry_id,
                    a.invoice_no,
                    a.vendor_po_id,
                    a.invoice_date, 
                    'Invoice ' as invoice_status,
                    c.company_name, 
                    d.customer_name,

                    a.vat_payer_purchase_grp,
                    a.declaration_no,
                    a.declaration_date, 

                    a.tax_amount,
                    a.total_amount_wo_tax,
                    a.entry_date,
                    a.total_amount,

                    b.vendor_purchase_invoice_item_id,
                    b.vendor_po_item_id,
                    b.item_code,
                    b.item_desc,                    
                    b.uom,
                    b.qty,
                    b.rate,
                    b.gst,
                    b.amount , 
                    v.vendor_name,
                    vc.contact_person_name

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
            
                left join vendor_info as v on a.vendor_id = v.vendor_id and v.`status`='Active'
              	left join vendor_contact_info  as vc on a.vendor_contact_person_id = vc.vendor_contact_id and vc.`status`='Active' 

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


            $sql = "
                select
                a.vendor_purchase_invoice_id, 
                a.tender_enquiry_id,
                c.payment_no,
                DATE_FORMAT(c.payment_date, '%d-%m-%Y') as payment_date ,
                d.vendor_name,
                c.payment_mode,
                c.payment_type,
                c.amount,

                b.vendor_payment_id,
                b.bill_id,
                b.bill_type,
                b.bill_amount
                from vendor_purchase_invoice_info as a 
                left join vendor_payment_bill_info as b on a.vendor_purchase_invoice_id = b.bill_id  and b.`status`='Active'
                left join vendor_payment_info as c on b.vendor_payment_id = c.vendor_payment_id and c.`status`='Active'
                left join vendor_info as d on c.vendor_id = d.vendor_id and d.`status`='Active'
                where a.`status`='Active'
                and b.bill_type ='Purchase Invoice'
                and a.tender_enquiry_id = '" . $this->db->escape_str($tender_enquiry_id) . "'
                order by c.payment_date
            ";

            $query = $this->db->query($sql);

            // GROUP DATA BY INVOICE
            foreach ($query->result_array() as $row) {
                $data['vendor_payment_list'][$row['vendor_payment_id']]['info'] = $row;
                $data['vendor_payment_list'][$row['vendor_payment_id']]['items'][] = $row;
            }


            $sql = "
                select
                a.local_purchase_bill_id, 
                a.tender_enquiry_id,
                c.payment_no,
                DATE_FORMAT(c.payment_date, '%d-%m-%Y') as payment_date ,
                d.vendor_name,
                c.payment_mode,
                c.payment_type,
                c.amount,

                b.vendor_payment_id,
                b.bill_id,
                b.bill_type,
                b.bill_amount   
                from local_purchase_bill_info as a 
                left join vendor_payment_bill_info as b on a.local_purchase_bill_id = b.bill_id  and b.`status`='Active'
                left join vendor_payment_info as c on b.vendor_payment_id = c.vendor_payment_id and c.`status`='Active'
                left join vendor_info as d on c.vendor_id = d.vendor_id and d.`status`='Active'
                where a.`status`='Active'
                and b.bill_type ='Local Bill'
                and a.tender_enquiry_id = '" . $this->db->escape_str($tender_enquiry_id) . "'
                order by c.payment_date asc
            ";

            $query = $this->db->query($sql);

            // GROUP DATA BY INVOICE
            foreach ($query->result_array() as $row) {
                $data['vendor_payment_local_bill_list'][$row['vendor_payment_id']]['info'] = $row;
                $data['vendor_payment_local_bill_list'][$row['vendor_payment_id']]['items'][] = $row;
            }
            $sql = "
                select
                a.dp_bill_id, 
                a.tender_enquiry_id,
                c.payment_no,
                DATE_FORMAT(c.payment_date, '%d-%m-%Y') as payment_date ,
                d.vendor_name,
                c.payment_mode,
                c.payment_type,
                c.amount,

                b.vendor_payment_id,
                b.bill_id,
                b.bill_type,
                b.bill_amount   
                from dp_bill_info  as a 
                left join vendor_payment_bill_info as b on a.dp_bill_id = b.bill_id  and b.`status`='Active'
                left join vendor_payment_info as c on b.vendor_payment_id = c.vendor_payment_id and c.`status`='Active'
                left join vendor_info as d on c.vendor_id = d.vendor_id and d.`status`='Active'
                where a.`status`='Active'
                and b.bill_type ='Delivery Bill'
                and a.tender_enquiry_id = '" . $this->db->escape_str($tender_enquiry_id) . "'
                order by c.payment_date asc
            ";

            $query = $this->db->query($sql);

            // GROUP DATA BY INVOICE
            foreach ($query->result_array() as $row) {
                $data['vendor_payment_delivery_bill_list'][$row['vendor_payment_id']]['info'] = $row;
                $data['vendor_payment_delivery_bill_list'][$row['vendor_payment_id']]['items'][] = $row;
            }


            $sql = "
                select
                a.customs_bill_id, 
                a.tender_enquiry_id,
                c.payment_no,
                DATE_FORMAT(c.payment_date, '%d-%m-%Y') as payment_date ,
                d.vendor_name,
                c.payment_mode,
                c.payment_type,
                c.amount,

                b.vendor_payment_id,
                b.bill_id,
                b.bill_type,
                b.bill_amount   
                from customs_bill_info  as a 
                left join vendor_payment_bill_info as b on a.customs_bill_id = b.bill_id  and b.`status`='Active'
                left join vendor_payment_info as c on b.vendor_payment_id = c.vendor_payment_id and c.`status`='Active'
                left join vendor_info as d on c.vendor_id = d.vendor_id and d.`status`='Active'
                where a.`status`='Active'
                and b.bill_type ='Customs Bill'
                and a.tender_enquiry_id = '" . $this->db->escape_str($tender_enquiry_id) . "'
                order by c.payment_date asc
            ";

            $query = $this->db->query($sql);

            // GROUP DATA BY INVOICE
            foreach ($query->result_array() as $row) {
                $data['vendor_payment_customer_bill_list'][$row['vendor_payment_id']]['info'] = $row;
                $data['vendor_payment_customer_bill_list'][$row['vendor_payment_id']]['items'][] = $row;
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



    public function item_rate_report()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data = array();
        $data['js'] = 'summary/tender-enquiry-summary-report.inc';
        $data['s_url'] = 'tender-quotation-list';
        $data['title'] = 'Tender Enquiry Summary Report';

        $where = "1=1";




        $this->load->view('page/reports/item-rate-report', $data);
    }


    public function item_search_v2()
    {
        $term = $this->input->post('search');
        $srch_typ = $this->input->post('srch_typ');

        if ($srch_typ == 'desc') {
            $sql = "
           
            select 
            p.* 
            from 
            (
                (
                select 
                'Customer PO' as tbl, 
                date_format(a.po_date,'%d-%m-%Y') as po_date,
                b.item_code,
                b.item_desc,
                b.uom 
                from customer_tender_po_info as a
                left join tender_po_item_info as b on b.tender_po_id = a.tender_po_id 
                where a.`status` = 'Active'
                and b.`status` = 'Active'
                and ( b.item_code like '%" . $this->db->escape_like_str($term) . "%' )
                order by b.item_code asc , a.po_date  desc
                )  union all (
                select 
                'Vendor PO' as tbl,
                date_format(q.po_date,'%d-%m-%Y') as po_date,
                w.item_code,
                w.item_desc,
                w.uom 
                from vendor_po_info as q 
                left join vendor_po_item_info as w on w.vendor_po_id = q.vendor_po_id
                where q.`status` = 'Active'
                and w.`status` = 'Active'
                and ( w.item_desc like '%" . $this->db->escape_like_str($term) . "%' )
                order by w.item_code , q.po_date desc
                ) union all (
                select 
                'Tender Enquiry' as tbl, 
                date_format(a.enquiry_date,'%d-%m-%Y') as po_date,
                b.item_code,
                b.item_desc,
                b.uom 
                from tender_enquiry_info as a
                left join tender_enquiry_item_info as b on b.tender_enquiry_id = a.tender_enquiry_id
                where a.`status` = 'Active'
                and b.`status` = 'Active'
                and (  b.item_desc like '%" . $this->db->escape_like_str($term) . "%' )
                order by b.item_code asc,  a.enquiry_date desc 
                ) union all (
                select 
                'Tender Quotation' as tbl, 
                date_format(a.quote_date,'%d-%m-%Y') as po_date,
                b.item_code,
                b.item_desc,
                b.uom 
                from tender_quotation_info as a
                left join tender_quotation_item_info as b on b.tender_quotation_id = a.tender_quotation_id
                where a.`status` = 'Active'
                and b.`status` = 'Active'
                and ( b.item_desc like '%" . $this->db->escape_like_str($term) . "%' )
                order by b.item_code asc,  a.quote_date  desc 
                )
            ) as p
            order by p.tbl , p.item_code , p.po_date desc 
        ";
        } else {

            $sql = "
           
            select 
            p.* 
            from 
            (
                (
                select 
                'Customer PO' as tbl, 
                date_format(a.po_date,'%d-%m-%Y') as po_date,
                b.item_code,
                b.item_desc,
                b.uom 
                from customer_tender_po_info as a
                left join tender_po_item_info as b on b.tender_po_id = a.tender_po_id 
                where a.`status` = 'Active'
                and b.`status` = 'Active'
                and ( b.item_code like '%" . $this->db->escape_like_str($term) . "%')
                order by b.item_code asc , a.po_date  desc
                )  union all (
                select 
                'Vendor PO' as tbl, 
                date_format(q.po_date,'%d-%m-%Y') as po_date,
                w.item_code,
                w.item_desc,
                w.uom 
                from vendor_po_info as q 
                left join vendor_po_item_info as w on w.vendor_po_id = q.vendor_po_id
                where q.`status` = 'Active'
                and w.`status` = 'Active'
                and ( w.item_code like '%" . $this->db->escape_like_str($term) . "%' )
                order by w.item_code , q.po_date desc
                ) union all (
                select 
                'Tender Enquiry' as tbl, 
                date_format(a.enquiry_date,'%d-%m-%Y') as po_date,
                b.item_code,
                b.item_desc,
                b.uom 
                from tender_enquiry_info as a
                left join tender_enquiry_item_info as b on b.tender_enquiry_id = a.tender_enquiry_id
                where a.`status` = 'Active'
                and b.`status` = 'Active'
                and ( b.item_code like '%" . $this->db->escape_like_str($term) . "%' )
                order by b.item_code asc,  a.enquiry_date desc 
                ) union all (
                select 
                'Tender Quotation' as tbl, 
                date_format(a.quote_date,'%d-%m-%Y') as po_date,
                b.item_code,
                b.item_desc,
                b.uom 
                from tender_quotation_info as a
                left join tender_quotation_item_info as b on b.tender_quotation_id = a.tender_quotation_id
                where a.`status` = 'Active'
                and b.`status` = 'Active'
                and ( b.item_code like '%" . $this->db->escape_like_str($term) . "%' )
                order by b.item_code asc,  a.quote_date  desc 
                )
            ) as p
            order by p.tbl , p.item_code , p.po_date desc 
        ";
        }

        $query = $this->db->query($sql);

        $result = [];
        $result = $query->result_array();

        echo json_encode($result);



    }


    public function customer_invoice_pending_report()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data = array();
        $data['js'] = 'reports/reports.inc';
        $data['s_url'] = 'customer-invoice-pending-report';
        $data['title'] = 'Customer Invoice Pending Report';



        $where = "1=1";

        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');
            $this->session->set_userdata('srch_from_date', $this->input->post('srch_from_date'));
            $this->session->set_userdata('srch_to_date', $this->input->post('srch_to_date'));
        } elseif ($this->session->userdata('srch_from_date')) {
            $data['srch_from_date'] = $srch_from_date = $this->session->userdata('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->session->userdata('srch_to_date');
        } else {
            $data['srch_from_date'] = $srch_from_date = '';
            $data['srch_to_date'] = $srch_to_date = '';
        }

        if (!empty($srch_from_date) && !empty($srch_to_date)) {
            $where .= " AND  ( a.invoice_date BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "') ";
        }


        // Customer Filter
        if ($this->input->post('srch_customer_id') !== null) {
            $data['srch_customer_id'] = $srch_customer_id = $this->input->post('srch_customer_id');
            $this->session->set_userdata('srch_customer_id', $srch_customer_id);
        } elseif ($this->session->userdata('srch_customer_id')) {
            $data['srch_customer_id'] = $srch_customer_id = $this->session->userdata('srch_customer_id');
        } else {
            $data['srch_customer_id'] = $srch_customer_id = '';
        }
        if (!empty($srch_customer_id)) {
            $where .= " AND a.customer_id = '" . $this->db->escape_str($srch_customer_id) . "'";
        }


        $sql = "
            SELECT company_id, company_name 
            FROM company_info 
            WHERE status = 'Active' 
            ORDER BY company_name ASC";
        $query = $this->db->query($sql);
        $data['company_opt'] = [];
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }
        $sql = "
            SELECT  customer_id, customer_name
            FROM customer_info 
            WHERE status = 'Active' 
            ORDER BY customer_name ASC";
        $query = $this->db->query($sql);
        $data['customer_opt'] = [];
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }


        $sql = "
            SELECT 
                DATE_FORMAT(a.invoice_date, '%d-%m-%Y') AS invoice_date,
                a.invoice_no,
                a.total_amount,
                b.customer_name
            FROM tender_enq_invoice_info AS a
            LEFT JOIN customer_info AS b 
                ON a.customer_id = b.customer_id 
                AND b.status = 'Active'
            WHERE a.status = 'Active'
            AND " . $where . "
            ORDER BY a.invoice_date ASC, a.invoice_no ASC
        ";

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();

        $this->load->view('page/reports/customer-invoice-pending-report', $data);
    }
    public function vendor_invoice_pending_report()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data = array();
        $data['js'] = 'reports/reports.inc';
        $data['s_url'] = 'customer-invoice-pending-report';
        $data['title'] = 'Customer Invoice Pending Report';



        $where = "";

        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');
            $this->session->set_userdata('srch_from_date', $this->input->post('srch_from_date'));
            $this->session->set_userdata('srch_to_date', $this->input->post('srch_to_date'));
        } elseif ($this->session->userdata('srch_from_date')) {
            $data['srch_from_date'] = $srch_from_date = $this->session->userdata('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->session->userdata('srch_to_date');
        } else {
            $data['srch_from_date'] = $srch_from_date = '';
            $data['srch_to_date'] = $srch_to_date = '';
        }

        if (!empty($srch_from_date) && !empty($srch_to_date)) {
            $where .= " AND  ( a.invoice_date BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "') ";
        }


        // Customer Filter
        if ($this->input->post('vendor_id') !== null) {
            $data['vendor_id'] = $vendor_id = $this->input->post('vendor_id');
            $this->session->set_userdata('vendor_id', $vendor_id);
        } elseif ($this->session->userdata('vendor_id')) {
            $data['vendor_id'] = $vendor_id = $this->session->userdata('vendor_id');
        } else {
            $data['vendor_id'] = $vendor_id = '';
        }
        if (!empty($vendor_id)) {
            $where .= " AND a.vendor_id = '" . $this->db->escape_str($vendor_id) . "'";
        }

        $sql = "
            SELECT vendor_id, vendor_name 
            FROM vendor_info 
            WHERE status = 'Active' 
            ORDER BY vendor_name ASC";
        $query = $this->db->query($sql);
        $data['customer_opt'] = [];
        foreach ($query->result_array() as $row) {
            $data['vendor_opt'][$row['vendor_id']] = $row['vendor_name'];
        }


        $sql = "
            SELECT * FROM (
                
                SELECT 
                    a.invoice_date,
                    a.invoice_no,
                    b.vendor_name,
                    a.total_amount AS total_amount,
                    'Purchase Invoice' AS bill_type
                FROM vendor_purchase_invoice_info AS a
                LEFT JOIN vendor_info AS b 
                    ON a.vendor_id = b.vendor_id AND b.status = 'Active'
                WHERE a.status = 'Active' $where

                UNION ALL

                SELECT
                    a.invoice_date,
                    a.invoice_no,
                    b.vendor_name,
                    a.tot_amt_with_tax AS total_amount,
                    'Local Bill' AS bill_type
                FROM local_purchase_bill_info AS a
                LEFT JOIN vendor_info AS b 
                    ON a.vendor_id = b.vendor_id AND b.status = 'Active'
                WHERE a.status = 'Active' $where

                UNION ALL

                SELECT 
                    a.invoice_date,
                    a.invoice_no,
                    b.vendor_name,
                    a.g_total AS total_amount,
                    'Delivery Bill' AS bill_type
                FROM dp_bill_info AS a
                LEFT JOIN vendor_info AS b 
                    ON a.vendor_id = b.vendor_id AND b.status = 'Active'
                WHERE a.status = 'Active' $where

                UNION ALL

                SELECT 
                    a.invoice_date,
                    a.invoice_no,
                    b.vendor_name,
                    a.customs_tot_amt AS total_amount,
                    'Customer Bill' AS bill_type
                FROM customs_bill_info AS a
                LEFT JOIN vendor_info AS b 
                    ON a.vendor_id = b.vendor_id AND b.status = 'Active'
                WHERE a.status = 'Active' $where

            ) AS final_table

            ORDER BY invoice_date DESC
        ";
        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();

        $this->load->view('page/reports/vendor-invoice-pending-report', $data);
    }

    public function in_stock_item_report()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data['title'] = 'In Stock Item Report';
        $data['js'] = 'reports/in-stock-item-report.inc'; // ← your custom js if any




        $where = "1=1";

        // ===================== SEARCH FILTERS =====================
        if ($this->input->post('srch_vendor_id') !== null) {
            $data['srch_vendor_id'] = $srch_vendor_id = $this->input->post('srch_vendor_id');
            $this->session->set_userdata('srch_vendor_id', $srch_vendor_id);
        } elseif ($this->session->userdata('srch_vendor_id')) {
            $data['srch_vendor_id'] = $srch_vendor_id = $this->session->userdata('srch_vendor_id');
        } else {
            $data['srch_vendor_id'] = $srch_vendor_id = '';
        }

        if (!empty($srch_vendor_id)) {
            $where .= " AND aii_stock.vendor_id = '" . $this->db->escape_str($srch_vendor_id) . "'";
        }


        //vendor_opt

        $sql = "
            SELECT vendor_id, vendor_name 
            FROM vendor_info 
            WHERE status = 'Active' 
            ORDER BY vendor_name ASC";
        $query = $this->db->query($sql);
        $data['vendor_opt'] = [];
        foreach ($query->result_array() as $row) {
            $data['vendor_opt'][$row['vendor_id']] = $row['vendor_name'];
        }

        $data['record_list'] = [];

        $sql = "
          SELECT *
            FROM 
            (
                SELECT 
                    a.vendor_pur_inward_item_id, 
                    a.item_code,
                    a.item_desc,
                    a.uom,
                    b.vendor_id,                     
                    c.vendor_name,
                    a.qty AS inward_qty, 
                    IFNULL(dc.total_dc_qty, 0) AS dc_qty, 
                    '' AS instock,
                    (a.qty - IFNULL(dc.total_dc_qty, 0)) AS instock_item_qty 
                FROM vendor_pur_inward_item_info AS a 
                LEFT JOIN vendor_pur_inward_info AS b 
                    ON a.vendor_pur_inward_id = b.vendor_pur_inward_id 
                    AND b.status = 'Active' 
                LEFT JOIN vendor_info AS c 
                    ON b.vendor_id = c.vendor_id 
                    AND c.status = 'Active' 
                LEFT JOIN (
                    SELECT 
                        tdc.vendor_pur_inward_item_id,
                        SUM(tdc.qty) AS total_dc_qty
                    FROM tender_dc_item_info AS tdc
                    WHERE tdc.status = 'Active'
                    GROUP BY tdc.vendor_pur_inward_item_id
                ) dc 
                    ON dc.vendor_pur_inward_item_id = a.vendor_pur_inward_item_id
                WHERE a.status = 'Active' 

                UNION ALL

                SELECT
                    0 AS vendor_pur_inward_item_id,
                    a.item_code,
                    a.item_desc,
                    a.uom,
                    a.vendor_id,                    
                    b.vendor_name,
                    a.qty AS inward_qty, 
                    0 AS dc_qty, 
                    'instock' AS instock,
                    a.qty AS instock_item_qty
                FROM in_stock_item_info AS a 
                LEFT JOIN vendor_info AS b 
                    ON a.vendor_id = b.vendor_id 
                    AND b.status = 'Active'
                WHERE a.status = 'Active' 

            ) AS aii_stock 

            WHERE instock_item_qty > 0 
            AND $where
            ORDER BY item_code ASC;
        ";

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();

        $this->load->view('page/reports/in-stock-item-report', $data);
    }




    public function vendor_statement_report()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data['title'] = 'Vendor Statement Report';
        $data['js'] = 'reports/reports.inc';

        // Fetch inputs from either GET or POST
        $vendor_id = $this->input->get_post('vendor_id');
        $from_date = $this->input->get_post('from_date');
        $to_date = $this->input->get_post('to_date');

        // Store / Retrieve from session for sticky behavior
        if ($vendor_id !== null) {
            $this->session->set_userdata('stmt_vendor_id', $vendor_id);
        } else {
            $vendor_id = $this->session->userdata('stmt_vendor_id') ?? '';
        }

        if ($from_date !== null) {
            $this->session->set_userdata('stmt_from_date', $from_date);
        } else {
            $from_date = $this->session->userdata('stmt_from_date') ?? '';
        }

        if ($to_date !== null) {
            $this->session->set_userdata('stmt_to_date', $to_date);
        } else {
            $to_date = $this->session->userdata('stmt_to_date') ?? '';
        }

        $data['vendor_id'] = $vendor_id;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;

        // Fetch active vendors mapped as 'id' and 'vendor_name' to perfectly match the user's view
        $sql = "
            SELECT vendor_id AS id, vendor_name 
            FROM vendor_info 
            WHERE status = 'Active' 
            ORDER BY vendor_name ASC";
        $query = $this->db->query($sql);
        $data['vendors'] = $query->result_array();

        $data['opening_balance'] = 0.000;
        $data['record_list'] = [];
        $data['op_exists'] = false;
        $data['op_details'] = null;

        $esc_vendor = !empty($vendor_id) ? $this->db->escape_str($vendor_id) : '';

        if (!empty($vendor_id)) {
            // Check if vendor opening balance is configured
            $op_query = $this->db->get_where('vendor_opening_balance_info', ['vendor_id' => $vendor_id]);
            $op_exists = $op_query->num_rows() > 0;
            $data['op_exists'] = $op_exists;

            if ($op_exists) {
                $op_row = $op_query->row_array();
                $data['op_details'] = $op_row;
                $op_date = $op_row['opening_date'];
                $op_amount = (float)$op_row['opening_amount'];
                $op_type = $op_row['balance_type'];
                
                $signed_op_amount = ($op_type === 'CR') ? $op_amount : -$op_amount;

                // Adjust from_date if it is empty or prior to opening_date
                if (empty($from_date) || $from_date < $op_date) {
                    $from_date = $op_date;
                    $data['from_date'] = $from_date;
                }
                $esc_from = $this->db->escape_str($from_date);
                $esc_op = $this->db->escape_str($op_date);

                // Calculate in-between purchases and payments from opening date to from_date
                $purchase_sql = "
                    SELECT IFNULL(SUM(total_amount), 0) AS total_purchases
                    FROM (
                        SELECT total_amount FROM vendor_purchase_invoice_info
                        WHERE status = 'Active' AND vendor_id = '$esc_vendor' AND invoice_date >= '$esc_op' AND invoice_date < '$esc_from'
                        
                        UNION ALL
                        
                        SELECT tot_amt_with_tax AS total_amount FROM local_purchase_bill_info
                        WHERE status = 'Active' AND vendor_id = '$esc_vendor' AND invoice_date >= '$esc_op' AND invoice_date < '$esc_from'
                        
                        UNION ALL
                        
                        SELECT g_total AS total_amount FROM dp_bill_info
                        WHERE status = 'Active' AND vendor_id = '$esc_vendor' AND invoice_date >= '$esc_op' AND invoice_date < '$esc_from'
                        
                        UNION ALL
                        
                        SELECT customs_tot_amt AS total_amount FROM customs_bill_info
                        WHERE status = 'Active' AND vendor_id = '$esc_vendor' AND invoice_date >= '$esc_op' AND invoice_date < '$esc_from'
                    ) AS prev_purchases
                ";
                $p_query = $this->db->query($purchase_sql);
                $p_row = $p_query->row_array();
                $total_purchases = (float)($p_row['total_purchases'] ?? 0);

                $payment_sql = "
                    SELECT IFNULL(SUM(amount), 0) AS total_payments
                    FROM vendor_payment_info
                    WHERE status = 'Active' AND vendor_id = '$esc_vendor' AND payment_date >= '$esc_op' AND payment_date < '$esc_from'
                ";
                $pay_query = $this->db->query($payment_sql);
                $pay_row = $pay_query->row_array();
                $total_payments = (float)($pay_row['total_payments'] ?? 0);

                $data['opening_balance'] = $signed_op_amount + $total_purchases - $total_payments;
            } else {
                $data['opening_balance'] = 0.000;
            }
        } else {
            // General logic for "All Vendors" or when no vendor is selected
            if (!empty($from_date)) {
                $esc_from = $this->db->escape_str($from_date);

                // Total Purchases before from_date
                $purchase_sql = "
                    SELECT IFNULL(SUM(total_amount), 0) AS total_purchases
                    FROM (
                        SELECT total_amount FROM vendor_purchase_invoice_info
                        WHERE status = 'Active' AND invoice_date < '$esc_from'
                        
                        UNION ALL
                        
                        SELECT tot_amt_with_tax AS total_amount FROM local_purchase_bill_info
                        WHERE status = 'Active' AND invoice_date < '$esc_from'
                        
                        UNION ALL
                        
                        SELECT g_total AS total_amount FROM dp_bill_info
                        WHERE status = 'Active' AND invoice_date < '$esc_from'
                        
                        UNION ALL
                        
                        SELECT customs_tot_amt AS total_amount FROM customs_bill_info
                        WHERE status = 'Active' AND invoice_date < '$esc_from'
                    ) AS prev_purchases
                ";
                $p_query = $this->db->query($purchase_sql);
                $p_row = $p_query->row_array();
                $total_purchases = $p_row['total_purchases'] ?? 0;

                // Total Payments before from_date
                $payment_sql = "
                    SELECT IFNULL(SUM(amount), 0) AS total_payments
                    FROM vendor_payment_info
                    WHERE status = 'Active' AND payment_date < '$esc_from'
                ";
                $pay_query = $this->db->query($payment_sql);
                $pay_row = $pay_query->row_array();
                $total_payments = $pay_row['total_payments'] ?? 0;

                $data['opening_balance'] = $total_purchases - $total_payments;
            }
        }

        // Re-escape just to make sure
        $esc_from = !empty($from_date) ? $this->db->escape_str($from_date) : '';

        // 2. Fetch all chronological transactions (Purchases & Payments) in range
        $txn_sql = "
            SELECT 
                tr_date,
                voucher_no,
                description,
                purchase_amt,
                paid_amt,
                type,
                vendor_name
            FROM (
                SELECT 
                    a.invoice_date AS tr_date,
                    a.invoice_no AS voucher_no,
                    'Purchase Invoice' AS description,
                    a.total_amount AS purchase_amt,
                    0.000 AS paid_amt,
                    'purchase' AS type,
                    v.vendor_name
                FROM vendor_purchase_invoice_info a
                LEFT JOIN vendor_info v ON a.vendor_id = v.vendor_id AND v.status = 'Active'
                WHERE a.status = 'Active'
                  " . (!empty($vendor_id) ? "AND a.vendor_id = '$esc_vendor'" : "") . "
                  " . (!empty($from_date) ? "AND a.invoice_date >= '$esc_from'" : "") . "
                  " . (!empty($to_date) ? "AND a.invoice_date <= '" . $this->db->escape_str($to_date) . "'" : "") . "
                  
                UNION ALL
                
                SELECT 
                    a.invoice_date AS tr_date,
                    a.invoice_no AS voucher_no,
                    'Local Bill' AS description,
                    a.tot_amt_with_tax AS purchase_amt,
                    0.000 AS paid_amt,
                    'purchase' AS type,
                    v.vendor_name
                FROM local_purchase_bill_info a
                LEFT JOIN vendor_info v ON a.vendor_id = v.vendor_id AND v.status = 'Active'
                WHERE a.status = 'Active'
                  " . (!empty($vendor_id) ? "AND a.vendor_id = '$esc_vendor'" : "") . "
                  " . (!empty($from_date) ? "AND a.invoice_date >= '$esc_from'" : "") . "
                  " . (!empty($to_date) ? "AND a.invoice_date <= '" . $this->db->escape_str($to_date) . "'" : "") . "
                  
                UNION ALL
                
                SELECT 
                    a.invoice_date AS tr_date,
                    a.invoice_no AS voucher_no,
                    'Delivery Bill' AS description,
                    a.g_total AS purchase_amt,
                    0.000 AS paid_amt,
                    'purchase' AS type,
                    v.vendor_name
                FROM dp_bill_info a
                LEFT JOIN vendor_info v ON a.vendor_id = v.vendor_id AND v.status = 'Active'
                WHERE a.status = 'Active'
                  " . (!empty($vendor_id) ? "AND a.vendor_id = '$esc_vendor'" : "") . "
                  " . (!empty($from_date) ? "AND a.invoice_date >= '$esc_from'" : "") . "
                  " . (!empty($to_date) ? "AND a.invoice_date <= '" . $this->db->escape_str($to_date) . "'" : "") . "
                  
                UNION ALL
                
                SELECT 
                    a.invoice_date AS tr_date,
                    a.invoice_no AS voucher_no,
                    'Customer Bill' AS description,
                    a.customs_tot_amt AS purchase_amt,
                    0.000 AS paid_amt,
                    'purchase' AS type,
                    v.vendor_name
                FROM customs_bill_info a
                LEFT JOIN vendor_info v ON a.vendor_id = v.vendor_id AND v.status = 'Active'
                WHERE a.status = 'Active'
                  " . (!empty($vendor_id) ? "AND a.vendor_id = '$esc_vendor'" : "") . "
                  " . (!empty($from_date) ? "AND a.invoice_date >= '$esc_from'" : "") . "
                  " . (!empty($to_date) ? "AND a.invoice_date <= '" . $this->db->escape_str($to_date) . "'" : "") . "
                  
                UNION ALL
                
                SELECT 
                    a.payment_date AS tr_date,
                    a.payment_no AS voucher_no,
                    'Vendor Payment' AS description,
                    0.000 AS purchase_amt,
                    a.amount AS paid_amt,
                    'payment' AS type,
                    v.vendor_name
                FROM vendor_payment_info a
                LEFT JOIN vendor_info v ON a.vendor_id = v.vendor_id AND v.status = 'Active'
                WHERE a.status = 'Active'
                  " . (!empty($vendor_id) ? "AND a.vendor_id = '$esc_vendor'" : "") . "
                  " . (!empty($from_date) ? "AND a.payment_date >= '$esc_from'" : "") . "
                  " . (!empty($to_date) ? "AND a.payment_date <= '" . $this->db->escape_str($to_date) . "'" : "") . "
            ) AS transactions
            ORDER BY tr_date ASC, voucher_no ASC
        ";
        
        $query = $this->db->query($txn_sql);
        $data['record_list'] = $query->result_array();

        $this->load->view('page/reports/vendor-statement-report', $data);
    }

    public function get_vendor_opening_balance_ajax()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        $vendor_id = $this->input->post('vendor_id');
        if (empty($vendor_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid Vendor ID']);
            exit;
        }

        $query = $this->db->get_where('vendor_opening_balance_info', ['vendor_id' => $vendor_id]);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            echo json_encode([
                'status' => 'success',
                'exists' => true,
                'opening_date' => $row['opening_date'],
                'opening_amount' => $row['opening_amount'],
                'balance_type' => $row['balance_type']
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'exists' => false
            ]);
        }
        exit;
    }

    public function customer_statement_report()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data['title'] = 'Customer Statement Report';
        $data['js'] = 'reports/customer-reports.inc';

        // Fetch inputs from either GET or POST
        $customer_id = $this->input->get_post('customer_id');
        $from_date = $this->input->get_post('from_date');
        $to_date = $this->input->get_post('to_date');

        // Store / Retrieve from session for sticky behavior
        if ($customer_id !== null) {
            $this->session->set_userdata('stmt_customer_id', $customer_id);
        } else {
            $customer_id = $this->session->userdata('stmt_customer_id') ?? '';
        }

        if ($from_date !== null) {
            $this->session->set_userdata('stmt_cust_from_date', $from_date);
        } else {
            $from_date = $this->session->userdata('stmt_cust_from_date') ?? '';
        }

        if ($to_date !== null) {
            $this->session->set_userdata('stmt_cust_to_date', $to_date);
        } else {
            $to_date = $this->session->userdata('stmt_cust_to_date') ?? '';
        }

        $data['customer_id'] = $customer_id;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;

        // Fetch active customers
        $sql = "
            SELECT customer_id AS id, customer_name 
            FROM customer_info 
            WHERE status = 'Active' 
            ORDER BY customer_name ASC";
        $query = $this->db->query($sql);
        $data['customers'] = $query->result_array();

        $data['opening_balance'] = 0.000;
        $data['record_list'] = [];
        $data['op_exists'] = false;
        $data['op_details'] = null;

        $esc_customer = !empty($customer_id) ? $this->db->escape_str($customer_id) : '';

        if (!empty($customer_id)) {
            // Check if customer opening balance is configured
            $op_query = $this->db->get_where('customer_opening_balance_info', ['customer_id' => $customer_id]);
            $op_exists = $op_query->num_rows() > 0;
            $data['op_exists'] = $op_exists;

            if ($op_exists) {
                $op_row = $op_query->row_array();
                $data['op_details'] = $op_row;
                $op_date = $op_row['opening_date'];
                $op_amount = (float)$op_row['opening_amount'];
                $op_type = $op_row['balance_type'];
                
                // For customer: Debit (DR) is positive (receivable/debit), Credit (CR) is negative (advance received/credit)
                $signed_op_amount = ($op_type === 'DR') ? $op_amount : -$op_amount;

                // Adjust from_date if it is empty or prior to opening_date
                if (empty($from_date) || $from_date < $op_date) {
                    $from_date = $op_date;
                    $data['from_date'] = $from_date;
                }
                $esc_from = $this->db->escape_str($from_date);
                $esc_op = $this->db->escape_str($op_date);

                // Calculate in-between invoices and receipts from opening date to from_date
                $invoice_sql = "
                    SELECT IFNULL(SUM(total_amount), 0) AS total_invoices
                    FROM tender_enq_invoice_info
                    WHERE status = 'Active' AND customer_id = '$esc_customer' AND invoice_date >= '$esc_op' AND invoice_date < '$esc_from'
                ";
                $inv_query = $this->db->query($invoice_sql);
                $inv_row = $inv_query->row_array();
                $total_invoices = (float)($inv_row['total_invoices'] ?? 0);

                $receipt_sql = "
                    SELECT IFNULL(SUM(amount), 0) AS total_receipts
                    FROM tender_receipt_info
                    WHERE status = 'Active' AND customer_id = '$esc_customer' AND receipt_date >= '$esc_op' AND receipt_date < '$esc_from'
                ";
                $rec_query = $this->db->query($receipt_sql);
                $rec_row = $rec_query->row_array();
                $total_receipts = (float)($rec_row['total_receipts'] ?? 0);

                $data['opening_balance'] = $signed_op_amount + $total_invoices - $total_receipts;
            } else {
                $data['opening_balance'] = 0.000;
            }
        } else {
            // General logic for "All Customers"
            if (!empty($from_date)) {
                $esc_from = $this->db->escape_str($from_date);

                // Total Invoices before from_date
                $invoice_sql = "
                    SELECT IFNULL(SUM(total_amount), 0) AS total_invoices
                    FROM tender_enq_invoice_info
                    WHERE status = 'Active' AND invoice_date < '$esc_from'
                ";
                $inv_query = $this->db->query($invoice_sql);
                $inv_row = $inv_query->row_array();
                $total_invoices = $inv_row['total_invoices'] ?? 0;

                // Total Receipts before from_date
                $receipt_sql = "
                    SELECT IFNULL(SUM(amount), 0) AS total_receipts
                    FROM tender_receipt_info
                    WHERE status = 'Active' AND receipt_date < '$esc_from'
                ";
                $rec_query = $this->db->query($receipt_sql);
                $rec_row = $rec_query->row_array();
                $total_receipts = $rec_row['total_receipts'] ?? 0;

                $data['opening_balance'] = $total_invoices - $total_receipts;
            }
        }

        // Re-escape just to make sure
        $esc_from = !empty($from_date) ? $this->db->escape_str($from_date) : '';

        // 2. Fetch all chronological transactions (Invoices & Receipts) in range
        $txn_sql = "
            SELECT 
                tr_date,
                voucher_no,
                description,
                debit_amt,
                credit_amt,
                type,
                customer_name
            FROM (
                SELECT 
                    a.invoice_date AS tr_date,
                    a.invoice_no AS voucher_no,
                    'Sales Invoice' AS description,
                    a.total_amount AS debit_amt,
                    0.000 AS credit_amt,
                    'invoice' AS type,
                    c.customer_name
                FROM tender_enq_invoice_info a
                LEFT JOIN customer_info c ON a.customer_id = c.customer_id AND c.status = 'Active'
                WHERE a.status = 'Active'
                  " . (!empty($customer_id) ? "AND a.customer_id = '$esc_customer'" : "") . "
                  " . (!empty($from_date) ? "AND a.invoice_date >= '$esc_from'" : "") . "
                  " . (!empty($to_date) ? "AND a.invoice_date <= '" . $this->db->escape_str($to_date) . "'" : "") . "
                  
                UNION ALL
                
                SELECT 
                    a.receipt_date AS tr_date,
                    a.receipt_no AS voucher_no,
                    'Customer Payment' AS description,
                    0.000 AS debit_amt,
                    a.amount AS credit_amt,
                    'receipt' AS type,
                    c.customer_name
                FROM tender_receipt_info a
                LEFT JOIN customer_info c ON a.customer_id = c.customer_id AND c.status = 'Active'
                WHERE a.status = 'Active'
                  " . (!empty($customer_id) ? "AND a.customer_id = '$esc_customer'" : "") . "
                  " . (!empty($from_date) ? "AND a.receipt_date >= '$esc_from'" : "") . "
                  " . (!empty($to_date) ? "AND a.receipt_date <= '" . $this->db->escape_str($to_date) . "'" : "") . "
            ) AS transactions
            ORDER BY tr_date ASC, voucher_no ASC
        ";
        
        $query = $this->db->query($txn_sql);
        $data['record_list'] = $query->result_array();

        $this->load->view('page/reports/customer-statement-report', $data);
    }

    public function get_customer_opening_balance_ajax()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        $customer_id = $this->input->post('customer_id');
        if (empty($customer_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid Customer ID']);
            exit;
        }

        $query = $this->db->get_where('customer_opening_balance_info', ['customer_id' => $customer_id]);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            echo json_encode([
                'status' => 'success',
                'exists' => true,
                'opening_date' => $row['opening_date'],
                'opening_amount' => $row['opening_amount'],
                'balance_type' => $row['balance_type']
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'exists' => false
            ]);
        }
        exit;
    }

}

