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
                a.tender_enq_invoice_id as invoice_id,
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

        if (isset($_POST['srch_ac_type_opt'])) {
            $data['srch_ac_type_opt'] = $srch_ac_type_opt = $this->input->post('srch_ac_type_opt');
        } else {
            $data['srch_ac_type_opt'] = $srch_ac_type_opt = '';
        }

        $data['record_list'] = [];

        $ac_type_cond_customs = "";
        $include_other_bills = true;

        if ($srch_ac_type_opt === 'Accountable') {
            $ac_type_cond_customs = "and a.ac_type_opt = 'Accountable'";
        } elseif ($srch_ac_type_opt === 'Not-Accountable') {
            $ac_type_cond_customs = "and a.ac_type_opt = 'Not-Accountable'";
            $include_other_bills = false;
        }

        $union_parts = [];
        if ($include_other_bills) {
            $union_parts[] = "
            (select  
            'Supplier Bill' as v_type,
            a.vendor_id,  
            a.invoice_no,
            a.invoice_date as inv_date,  
            a.vat_payer_purchase_grp,
            'General trading' g_desc,
            a.total_amount_wo_tax as items_tot_ex_tax,
            (coalesce(a.total_amount_wo_tax_inc_addl, a.total_amount_wo_tax) - a.total_amount_wo_tax) as addl_amt_ex_tax,
            coalesce(a.total_tax_amount_inc_addl, a.tax_amount) as tax_amount,
            coalesce(a.total_amount_inc_addl, a.total_amount) as total_amount,
            a.declaration_date,
            a.declaration_no
            from vendor_purchase_invoice_info as a  
            where a.`status` = 'Active' 
            and (a.only_accounting_entry != 1 or a.only_accounting_entry = 0 or a.only_accounting_entry is null)
            and a.invoice_date between '$srch_from_date' and '$srch_to_date'
            order by  a.invoice_date asc) 
            ";

            $union_parts[] = "
            (select 
            'Local Bill' as v_type,
            a.vendor_id,
            a.invoice_no,
            a.invoice_date as inv_date,
            a.vat_payer_purchase_grp,
            'General trading' g_desc,
            a.tot_amt_wo_tax as items_tot_ex_tax,
            0.000 as addl_amt_ex_tax,
            a.vat_amt as tax_amount,
            a.tot_amt_with_tax as total_amount,
            '' as declaration_date,
            '' as declaration_no
            from local_purchase_bill_info as a
            where a.`status` = 'Active'  
            and a.invoice_date between '$srch_from_date' and '$srch_to_date'
            order by  a.invoice_date asc) 
            ";

            $union_parts[] = "
            (select 
            'DP Bill' as v_type,
            a.vendor_id,
            a.invoice_no,
            a.invoice_date as inv_date,
            a.vat_payer_purchase_grp,
            'Service' g_desc,
            a.dp_charges as items_tot_ex_tax,
            0.000 as addl_amt_ex_tax,
            a.dp_vat_amt as tax_amount,
            (a.dp_charges + a.dp_vat_amt) as total_amount,
            '' as declaration_date,
            '' as declaration_no
            from dp_bill_info as a
            where a.`status` = 'Active'  
            and a.invoice_date between '$srch_from_date' and '$srch_to_date'
            order by a.invoice_date asc) 
            ";
        }

        $union_parts[] = "
        (select 
        'Customs Bill' as v_type,
        a.vendor_id,
        a.invoice_no,
        a.invoice_date as inv_date,
        a.vat_payer_purchase_grp,
        'Service' g_desc,
        a.tot_amt_wo_vat as items_tot_ex_tax,
        0.000 as addl_amt_ex_tax,
        a.vat_amt as tax_amount,
        a.customs_tot_amt as total_amount,
        a.declaration_date as declaration_date,
        a.declaration_no as declaration_no
        from customs_bill_info as a
        where a.`status` = 'Active'  
        $ac_type_cond_customs
        and a.invoice_date between '$srch_from_date' and '$srch_to_date'
        order by a.invoice_date asc) 
        ";

        $union_sql = implode(" union all ", $union_parts);

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
        a1.items_tot_ex_tax,
        a1.addl_amt_ex_tax,
        (a1.items_tot_ex_tax + a1.addl_amt_ex_tax) as tot_amt_ex_tax,
        a1.tax_amount as vat_amt,
        a1.total_amount as tot_amt_inc_tax,
        a1.declaration_date,
        a1.declaration_no
        from
        (
            $union_sql
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

    public function sales_purchase_report()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        $data['js'] = 'reports/reports.inc';

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

        if (isset($_POST['vat_payer_purchase_grp'])) {
            $data['vat_payer_purchase_grp'] = $vat_payer_purchase_grp = $this->input->post('vat_payer_purchase_grp');
        } else {
            $data['vat_payer_purchase_grp'] = $vat_payer_purchase_grp = '';
        }

        if (isset($_POST['srch_ac_type_opt'])) {
            $data['srch_ac_type_opt'] = $srch_ac_type_opt = $this->input->post('srch_ac_type_opt');
        } else {
            $data['srch_ac_type_opt'] = $srch_ac_type_opt = '';
        }


        // Fetch Sales Data
        $data['sales_record_list'] = [];
        $sql_sales = " 
                select 
                a.tender_enq_invoice_id as invoice_id,
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
        $query_sales = $this->db->query($sql_sales);
        $rec_sales = $query_sales->result_array();
        $grouped_sales = [];
        foreach ($rec_sales as $row) {
            $s_order = $row['s_order'];   // group key
            $grouped_sales[$s_order][] = $row;  // push row inside that s_order
        }
        $data['sales_record_list'] = $grouped_sales;

        // Fetch Purchase Data
        $data['purchase_record_list'] = [];
        $ac_type_cond_customs = "";
        $include_other_bills = true;

        if ($srch_ac_type_opt === 'Accountable') {
            $ac_type_cond_customs = "and a.ac_type_opt = 'Accountable'";
        } elseif ($srch_ac_type_opt === 'Not-Accountable') {
            $ac_type_cond_customs = "and a.ac_type_opt = 'Not-Accountable'";
            $include_other_bills = false;
        }

        $union_parts = [];
        if ($include_other_bills) {
            $union_parts[] = "
            (select  
            'Supplier Bill' as v_type,
            a.vendor_id,  
            a.invoice_no,
            a.invoice_date as inv_date,  
            a.vat_payer_purchase_grp,
            'General trading' g_desc,
            a.total_amount_wo_tax as items_tot_ex_tax,
            (coalesce(a.total_amount_wo_tax_inc_addl, a.total_amount_wo_tax) - a.total_amount_wo_tax) as addl_amt_ex_tax,
            coalesce(a.total_tax_amount_inc_addl, a.tax_amount) as tax_amount,
            coalesce(a.total_amount_inc_addl, a.total_amount) as total_amount,
            a.declaration_date,
            a.declaration_no
            from vendor_purchase_invoice_info as a  
            where a.`status` = 'Active' 
            and (a.only_accounting_entry != 1 or a.only_accounting_entry = 0 or a.only_accounting_entry is null)
            and a.invoice_date between '$srch_from_date' and '$srch_to_date'
            order by  a.invoice_date asc) 
            ";

            $union_parts[] = "
            (select 
            'Local Bill' as v_type,
            a.vendor_id,
            a.invoice_no,
            a.invoice_date as inv_date,
            a.vat_payer_purchase_grp,
            'General trading' g_desc,
            a.tot_amt_wo_tax as items_tot_ex_tax,
            0.000 as addl_amt_ex_tax,
            a.vat_amt as tax_amount,
            a.tot_amt_with_tax as total_amount,
            '' as declaration_date,
            '' as declaration_no
            from local_purchase_bill_info as a
            where a.`status` = 'Active'  
            and a.invoice_date between '$srch_from_date' and '$srch_to_date'
            order by  a.invoice_date asc) 
            ";

            $union_parts[] = "
            (select 
            'DP Bill' as v_type,
            a.vendor_id,
            a.invoice_no,
            a.invoice_date as inv_date,
            a.vat_payer_purchase_grp,
            'Service' g_desc,
            a.dp_charges as items_tot_ex_tax,
            0.000 as addl_amt_ex_tax,
            a.dp_vat_amt as tax_amount,
            (a.dp_charges + a.dp_vat_amt) as total_amount,
            '' as declaration_date,
            '' as declaration_no
            from dp_bill_info as a
            where a.`status` = 'Active'  
            and a.invoice_date between '$srch_from_date' and '$srch_to_date'
            order by a.invoice_date asc) 
            ";
        }

        $union_parts[] = "
        (select 
        'Customs Bill' as v_type,
        a.vendor_id,
        a.invoice_no,
        a.invoice_date as inv_date,
        a.vat_payer_purchase_grp,
        'Service' g_desc,
        a.tot_amt_wo_vat as items_tot_ex_tax,
        0.000 as addl_amt_ex_tax,
        a.vat_amt as tax_amount,
        a.customs_tot_amt as total_amount,
        a.declaration_date as declaration_date,
        a.declaration_no as declaration_no
        from customs_bill_info as a
        where a.`status` = 'Active'  
        $ac_type_cond_customs
        and a.invoice_date between '$srch_from_date' and '$srch_to_date'
        order by a.invoice_date asc) 
        ";

        $union_sql = implode(" union all ", $union_parts);

        $sql_purchase = "
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
        a1.items_tot_ex_tax,
        a1.addl_amt_ex_tax,
        (a1.items_tot_ex_tax + a1.addl_amt_ex_tax) as tot_amt_ex_tax,
        a1.tax_amount as vat_amt,
        a1.total_amount as tot_amt_inc_tax,
        a1.declaration_date,
        a1.declaration_no
        from
        (
            $union_sql
        ) as a1
         left join vendor_info as b on b.vendor_id = a1.vendor_id and b.`status` = 'Active'
         left join vat_filing_head_info as c on c.vat_filing_head_name = a1.vat_payer_purchase_grp and c.vat_filing_head_type = 'Purchase' and c.`status` = 'Active'
         where ('" . $this->db->escape_str($vat_payer_purchase_grp) . "' = '' or a1.vat_payer_purchase_grp = '" . $this->db->escape_str($vat_payer_purchase_grp) . "')
         order by c.s_order asc , c.vat_filing_head_name ,  a1.inv_date ,  a1.v_type 
        ";


        $query_purchase = $this->db->query($sql_purchase);
        $rec_purchase = $query_purchase->result_array();
        $grouped_purchase = [];
        foreach ($rec_purchase as $row) {
            $s_order = $row['s_order'];   // group key
            $grouped_purchase[$s_order][] = $row;  // push row inside that s_order
        }
        $data['purchase_record_list'] = $grouped_purchase;

        // Fetch Sales Categories for Dropdown
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

        // Fetch Purchase Categories for Dropdown
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

        $this->load->view('page/reports/sales-purchase-report', $data);
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
                    tpo.serial_no,
                    b.tender_po_item_id

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
                left join vendor_contact_info as f on a.vendor_id = f.vendor_id and f.vendor_contact_id = a.vendor_contact_person_id and f.`status`='Active'
           
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
                    b.vendor_rate_enquiry_item_id,
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
            c.vendor_name as local_supplier,
            b.sub_account_head_name ,
            a.invoice_date,
            a.invoice_no,
            a.inv_entry_date,
            a.vat_payer_purchase_grp,
            a.tot_amt_wo_tax,
            a.vat,
            a.vat_amt,
            a.tot_amt_with_tax
            from local_purchase_bill_info as a
            left join cb_sub_account_head_info as b on b.sub_account_head_id = a.sub_account_head_id and b.`status` = 'Active' 
            left join vendor_info as c on c.vendor_id = a.vendor_id and c.`status` = 'Active'
            where a.status='Active'
            and a.tender_enquiry_id = '" . $this->db->escape_str($tender_enquiry_id) . "'
            order by a.invoice_date asc
            ";

            $query = $this->db->query($sql);

            $data['vendor_local_bill_list'] = $query->result_array();


            $sql = "
            select 
                c.vendor_name as local_supplier,
                b.sub_account_head_name ,
                a.invoice_date,
                a.invoice_no,
                a.inv_entry_date,
                a.vat_payer_purchase_grp, 
                a.custom_stamp_fee,
                a.custom_duty,
                a.custom_vat_amt,
                a.tot_amt_wo_dp,
                a.dp_charges,
                a.dp_vat_amt,
                (a.dp_charges + a.dp_vat_amt) as dp_total_amt,
                a.g_total
                from dp_bill_info as a
                left join cb_sub_account_head_info as b on b.sub_account_head_id = a.sub_account_head_id and b.`status` = 'Active' 
                left join vendor_info as c on c.vendor_id = a.vendor_id and c.`status` = 'Active' 
            where a.tender_enquiry_id = '" . $this->db->escape_str($tender_enquiry_id) . "'
            order by a.invoice_date asc
            ";

            $query = $this->db->query($sql);

            $data['dp_bill_list'] = $query->result_array();

            $sql = "
            select 
            c.vendor_name as local_supplier, 
            a.invoice_date,
            a.invoice_no,
            a.inv_entry_date,
            a.vat_payer_purchase_grp,  
            a.declaration_no,
            a.declaration_date,
            a.custom_stamp_fee,
            a.bill_amount,
            a.custom_duty,
            a.tot_amt_wo_vat,
            a.vat_amt,
            (a.custom_stamp_fee + a.custom_duty + a.vat_amt) as customs_payable,
            a.customs_tot_amt
            from customs_bill_info as a 
            left join vendor_info as c on c.vendor_id = a.vendor_id and c.`status` = 'Active'
            where a.tender_enquiry_id = '" . $this->db->escape_str($tender_enquiry_id) . "'
            order by a.invoice_date asc
            ";

            $query = $this->db->query($sql);

            $data['custom_bill_list'] = $query->result_array();


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
        $data['js'] = 'reports/reports.inc';
        $data['s_url'] = 'item-rate-report';
        $data['title'] = 'Item Rate Report';

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


    public function item_search_v3()
    {
        $term = $this->input->post('search');
        $srch_typ = $this->input->post('srch_typ');

        $term_esc = $this->db->escape_like_str($term);
        if ($srch_typ == 'desc') {
            $cond = "item_desc LIKE '%" . $term_esc . "%'";
        } else {
            $cond = "item_code LIKE '%" . $term_esc . "%'";
        }

        $sql = "
            SELECT * FROM (
                -- Customer Quotation (Tender Quotation)
                SELECT 
                    'Customer Quotation' AS tbl,
                    b.item_code,
                    b.item_desc,
                    b.uom,
                    DATE_FORMAT(a.quote_date, '%d-%m-%Y') AS doc_date,
                    a.quote_date AS raw_date,
                    b.rate,
                    b.qty,
                    a.tender_enquiry_id,
                    get_tender_info(a.tender_enquiry_id) AS tender_details
                FROM tender_quotation_info a 
                JOIN tender_quotation_item_info b ON a.tender_quotation_id = b.tender_quotation_id 
                WHERE a.status = 'Active' AND b.status = 'Active' AND b.{$cond}

                UNION ALL

                -- Customer PO (Tender PO)
                SELECT 
                    'Customer PO' AS tbl,
                    b.item_code,
                    b.item_desc,
                    b.uom,
                    DATE_FORMAT(a.po_date, '%d-%m-%Y') AS doc_date,
                    a.po_date AS raw_date,
                    b.rate,
                    b.qty,
                    a.tender_enquiry_id,
                    get_tender_info(a.tender_enquiry_id) AS tender_details
                FROM customer_tender_po_info a 
                JOIN tender_po_item_info b ON a.tender_po_id = b.tender_po_id 
                WHERE a.status = 'Active' AND b.status = 'Active' AND b.{$cond}

                UNION ALL

                -- Vendor Quotation
                SELECT 
                    'Vendor Quotation' AS tbl,
                    b.item_code,
                    b.item_desc,
                    b.uom,
                    DATE_FORMAT(a.quote_date, '%d-%m-%Y') AS doc_date,
                    a.quote_date AS raw_date,
                    b.rate,
                    b.qty,
                    a.tender_enquiry_id,
                    get_tender_info(a.tender_enquiry_id) AS tender_details
                FROM vendor_quotation_info a 
                JOIN vendor_quote_item_info b ON a.vendor_quote_id = b.vendor_quote_id 
                WHERE a.status = 'Active' AND b.status = 'Active' AND b.{$cond}

                UNION ALL

                -- Vendor PO
                SELECT 
                    'Vendor PO' AS tbl,
                    b.item_code,
                    b.item_desc,
                    b.uom,
                    DATE_FORMAT(a.po_date, '%d-%m-%Y') AS doc_date,
                    a.po_date AS raw_date,
                    b.rate,
                    b.qty,
                    a.tender_enquiry_id,
                    get_tender_info(a.tender_enquiry_id) AS tender_details
                FROM vendor_po_info a 
                JOIN vendor_po_item_info b ON a.vendor_po_id = b.vendor_po_id 
                WHERE a.status = 'Active' AND b.status = 'Active' AND b.{$cond}
            ) AS p
            ORDER BY p.item_code ASC, p.raw_date DESC
        ";

        $query = $this->db->query($sql);
        $results = $query->result_array();

        $grouped = [];
        foreach ($results as $row) {
            $item_code = $row['item_code'];
            $enq_id = $row['tender_enquiry_id'] ? $row['tender_enquiry_id'] : 0;
            $key = $item_code . '_' . $enq_id;

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'item_code' => $item_code,
                    'item_desc' => $row['item_desc'],
                    'uom' => $row['uom'],
                    'tender_enquiry_id' => $enq_id,
                    'enquiry_no' => $row['tender_details'] ? $row['tender_details'] : '',
                    'cust_quote' => null,
                    'cust_po' => null,
                    'vend_quote' => null,
                    'vend_po' => null
                ];
            }

            if (empty($grouped[$key]['item_desc'])) {
                $grouped[$key]['item_desc'] = $row['item_desc'];
            }
            if (empty($grouped[$key]['uom'])) {
                $grouped[$key]['uom'] = $row['uom'];
            }

            $type = $row['tbl'];
            $tx = [
                'date' => $row['doc_date'],
                'rate' => $row['rate'],
                'qty' => $row['qty'],
                'enq_no' => $row['tender_details'] ? $row['tender_details'] : ''
            ];

            if ($type === 'Customer Quotation') {
                if ($grouped[$key]['cust_quote'] === null) {
                    $grouped[$key]['cust_quote'] = $tx;
                }
            } elseif ($type === 'Customer PO') {
                if ($grouped[$key]['cust_po'] === null) {
                    $grouped[$key]['cust_po'] = $tx;
                }
            } elseif ($type === 'Vendor Quotation') {
                if ($grouped[$key]['vend_quote'] === null) {
                    $grouped[$key]['vend_quote'] = $tx;
                }
            } elseif ($type === 'Vendor PO') {
                if ($grouped[$key]['vend_po'] === null) {
                    $grouped[$key]['vend_po'] = $tx;
                }
            }
        }

        echo json_encode(array_values($grouped));
    }


    public function customer_invoice_pending_report()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data = array();
        $data['js'] = 'reports/reports.inc';
        $data['s_url'] = 'customer-invoice-pending-report';
        $data['title'] = 'Customer Invoice Report';



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
                b.gst as vat,
                ifnull(a.tax_amount, 0) as tax_amount,
                a.total_amount,
                b.customer_name
            FROM tender_enq_invoice_info AS a
            LEFT JOIN customer_info AS b 
                ON a.customer_id = b.customer_id  
            WHERE a.status = 'Active' AND b.status = 'Active'
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
        $data['s_url'] = 'vendor-invoice-pending-report';
        $data['title'] = 'Vendor Invoice  Report';



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
                    COALESCE(a.total_amount_inc_addl, a.total_amount) AS total_amount,
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
                    a.customs_payable AS total_amount,
                    'Customer Bill' AS bill_type
                FROM customs_bill_info AS a
                LEFT JOIN vendor_info AS b 
                    ON a.vendor_id = b.vendor_id AND b.status = 'Active'
                WHERE a.status = 'Active' AND a.ac_type_opt = 'Accountable' $where

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

    public function vendor_statement_report($action = '')
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        // Handle Reset
        if ($action === 'reset' || $this->input->post('reset') == '1' || $this->input->get('reset') == '1') {
            $this->session->unset_userdata('stmt_vendor_id');
            $this->session->unset_userdata('stmt_from_date');
            $this->session->unset_userdata('stmt_to_date');
            redirect('vendor-statement-report');
            return;
        }

        // If accessed with GET query parameters (e.g. ?vendor_id=3&to_date=2026-09-12),
        // save to session and immediately redirect to clean URL without query string
        if ($this->input->server('REQUEST_METHOD') === 'GET' && ($this->input->get('vendor_id') !== null || $this->input->get('to_date') !== null || $this->input->get('from_date') !== null)) {
            $get_vendor_id = $this->input->get('vendor_id');
            $get_from_date = $this->input->get('from_date');
            $get_to_date = $this->input->get('to_date');

            if ($get_vendor_id !== null) {
                $this->session->set_userdata('stmt_vendor_id', $get_vendor_id);
            }
            if ($get_from_date !== null) {
                $this->session->set_userdata('stmt_from_date', $get_from_date);
            }
            if ($get_to_date !== null) {
                $this->session->set_userdata('stmt_to_date', $get_to_date);
            }

            redirect('vendor-statement-report');
            return;
        }

        $data['title'] = 'Vendor Statement Report';
        $data['js'] = 'reports/reports.inc';

        // Process POST submission or read from session
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $vendor_id = $this->input->post('vendor_id') !== null ? $this->input->post('vendor_id') : '';
            $from_date = $this->input->post('from_date') !== null ? $this->input->post('from_date') : '';
            $to_date = $this->input->post('to_date') !== null ? $this->input->post('to_date') : '';

            // Only save into session if not a one-off export
            if ($this->input->post('export_excel') != '1') {
                $this->session->set_userdata('stmt_vendor_id', $vendor_id);
                $this->session->set_userdata('stmt_from_date', $from_date);
                $this->session->set_userdata('stmt_to_date', $to_date);
            }
        } else {
            $vendor_id = $this->session->userdata('stmt_vendor_id') ?? '';
            $from_date = $this->session->userdata('stmt_from_date') ?? '';
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
                $op_amount = (float) $op_row['opening_amount'];
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
                        SELECT COALESCE(total_amount_inc_addl, total_amount) AS total_amount FROM vendor_purchase_invoice_info
                        WHERE status = 'Active' AND vendor_id = '$esc_vendor' AND invoice_date >= '$esc_op' AND invoice_date < '$esc_from'
                        
                        UNION ALL
                        
                        SELECT tot_amt_with_tax AS total_amount FROM local_purchase_bill_info
                        WHERE status = 'Active' AND vendor_id = '$esc_vendor' AND invoice_date >= '$esc_op' AND invoice_date < '$esc_from'
                        
                        UNION ALL
                        
                        SELECT g_total AS total_amount FROM dp_bill_info
                        WHERE status = 'Active' AND vendor_id = '$esc_vendor' AND invoice_date >= '$esc_op' AND invoice_date < '$esc_from'
                        
                        UNION ALL
                        
                        SELECT customs_payable AS total_amount FROM customs_bill_info
                        WHERE status = 'Active' AND ac_type_opt = 'Accountable' AND vendor_id = '$esc_vendor' AND invoice_date >= '$esc_op' AND invoice_date < '$esc_from'
                        
                        UNION ALL
                        
                        SELECT total_amount FROM credit_debit_note_info
                        WHERE status != 'Delete' AND party_type = 'Supplier' AND note_type = 'Credit' AND supplier_id = '$esc_vendor' AND note_date >= '$esc_op' AND note_date < '$esc_from'
                    ) AS prev_purchases
                ";
                $p_query = $this->db->query($purchase_sql);
                $p_row = $p_query->row_array();
                $total_purchases = (float) ($p_row['total_purchases'] ?? 0);

                $payment_sql = "
                    SELECT IFNULL(SUM(amount), 0) AS total_payments
                    FROM (
                        SELECT amount FROM vendor_payment_info
                        WHERE status = 'Active' AND vendor_id = '$esc_vendor' AND payment_date >= '$esc_op' AND payment_date < '$esc_from'
                        UNION ALL
                        SELECT adv_payment_amt AS amount FROM vendor_advance_payment_info
                        WHERE status = 'Active' AND vendor_id = '$esc_vendor' AND adv_payment_date >= '$esc_op' AND adv_payment_date < '$esc_from'
                        
                        UNION ALL
                        
                        SELECT total_amount AS amount FROM credit_debit_note_info
                        WHERE status != 'Delete' AND party_type = 'Supplier' AND note_type = 'Debit' AND supplier_id = '$esc_vendor' AND note_date >= '$esc_op' AND note_date < '$esc_from'
                    ) AS pay_adv
                ";
                $pay_query = $this->db->query($payment_sql);
                $pay_row = $pay_query->row_array();
                $total_payments = (float) ($pay_row['total_payments'] ?? 0);

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
                        SELECT COALESCE(total_amount_inc_addl, total_amount) AS total_amount FROM vendor_purchase_invoice_info
                        WHERE status = 'Active' AND invoice_date < '$esc_from'
                        
                        UNION ALL
                        
                        SELECT tot_amt_with_tax AS total_amount FROM local_purchase_bill_info
                        WHERE status = 'Active' AND invoice_date < '$esc_from'
                        
                        UNION ALL
                        
                        SELECT g_total AS total_amount FROM dp_bill_info
                        WHERE status = 'Active' AND invoice_date < '$esc_from'
                        
                        UNION ALL
                        
                        SELECT customs_payable AS total_amount FROM customs_bill_info
                        WHERE status = 'Active' AND ac_type_opt = 'Accountable' AND invoice_date < '$esc_from'
                        
                        UNION ALL
                        
                        SELECT total_amount FROM credit_debit_note_info
                        WHERE status != 'Delete' AND party_type = 'Supplier' AND note_type = 'Credit' AND note_date < '$esc_from'
                    ) AS prev_purchases
                ";
                $p_query = $this->db->query($purchase_sql);
                $p_row = $p_query->row_array();
                $total_purchases = $p_row['total_purchases'] ?? 0;

                // Total Payments before from_date
                $payment_sql = "
                    SELECT IFNULL(SUM(amount), 0) AS total_payments
                    FROM (
                        SELECT amount FROM vendor_payment_info
                        WHERE status = 'Active' AND payment_date < '$esc_from'
                        UNION ALL
                        SELECT adv_payment_amt AS amount FROM vendor_advance_payment_info
                        WHERE status = 'Active' AND adv_payment_date < '$esc_from'
                        
                        UNION ALL
                        
                        SELECT total_amount AS amount FROM credit_debit_note_info
                        WHERE status != 'Delete' AND party_type = 'Supplier' AND note_type = 'Debit' AND note_date < '$esc_from'
                    ) AS pay_adv
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
                    COALESCE(a.total_amount_inc_addl, a.total_amount) AS purchase_amt,
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
                    a.customs_payable AS purchase_amt,
                    0.000 AS paid_amt,
                    'purchase' AS type,
                    v.vendor_name
                FROM customs_bill_info a
                LEFT JOIN vendor_info v ON a.vendor_id = v.vendor_id AND v.status = 'Active'
                WHERE a.status = 'Active'
                  AND a.ac_type_opt = 'Accountable'
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
                  
                UNION ALL
                
                SELECT 
                    a.adv_payment_date AS tr_date,
                    CONCAT('ADV-', a.adv_payment_id) AS voucher_no,
                    'Vendor Advance Payment' AS description,
                    0.000 AS purchase_amt,
                    a.adv_payment_amt AS paid_amt,
                    'payment' AS type,
                    v.vendor_name
                FROM vendor_advance_payment_info a
                LEFT JOIN vendor_info v ON a.vendor_id = v.vendor_id AND v.status = 'Active'
                WHERE a.status = 'Active'
                  " . (!empty($vendor_id) ? "AND a.vendor_id = '$esc_vendor'" : "") . "
                  " . (!empty($from_date) ? "AND a.adv_payment_date >= '$esc_from'" : "") . "
                  " . (!empty($to_date) ? "AND a.adv_payment_date <= '" . $this->db->escape_str($to_date) . "'" : "") . "
                  
                UNION ALL
                
                SELECT 
                    a.note_date AS tr_date,
                    a.note_no AS voucher_no,
                    'Credit Note' AS description,
                    a.total_amount AS purchase_amt,
                    0.000 AS paid_amt,
                    'purchase' AS type,
                    v.vendor_name
                FROM credit_debit_note_info a
                LEFT JOIN vendor_info v ON a.supplier_id = v.vendor_id AND v.status = 'Active'
                WHERE a.status != 'Delete' AND a.party_type = 'Supplier' AND a.note_type = 'Credit'
                  " . (!empty($vendor_id) ? "AND a.supplier_id = '$esc_vendor'" : "") . "
                  " . (!empty($from_date) ? "AND a.note_date >= '$esc_from'" : "") . "
                  " . (!empty($to_date) ? "AND a.note_date <= '" . $this->db->escape_str($to_date) . "'" : "") . "
                  
                UNION ALL
                
                SELECT 
                    a.note_date AS tr_date,
                    a.note_no AS voucher_no,
                    'Debit Note' AS description,
                    0.000 AS purchase_amt,
                    a.total_amount AS paid_amt,
                    'payment' AS type,
                    v.vendor_name
                FROM credit_debit_note_info a
                LEFT JOIN vendor_info v ON a.supplier_id = v.vendor_id AND v.status = 'Active'
                WHERE a.status != 'Delete' AND a.party_type = 'Supplier' AND a.note_type = 'Debit'
                  " . (!empty($vendor_id) ? "AND a.supplier_id = '$esc_vendor'" : "") . "
                  " . (!empty($from_date) ? "AND a.note_date >= '$esc_from'" : "") . "
                  " . (!empty($to_date) ? "AND a.note_date <= '" . $this->db->escape_str($to_date) . "'" : "") . "
            ) AS transactions
            ORDER BY tr_date ASC, voucher_no ASC
        ";

        $query = $this->db->query($txn_sql);
        $data['record_list'] = $query->result_array();

        // Get selected vendor name
        $selected_vendor_name = 'All_Vendors';
        if (!empty($vendor_id) && !empty($data['vendors'])) {
            foreach ($data['vendors'] as $v) {
                if ($v['id'] == $vendor_id) {
                    $selected_vendor_name = $v['vendor_name'];
                    break;
                }
            }
        }
        $data['selected_vendor_name'] = $selected_vendor_name;

        if ($this->input->get_post('export_excel') == '1') {
            header("Content-Type: application/vnd.ms-excel");
            $clean_vendor_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $selected_vendor_name);
            $filename = "Vendor_Statement_Report_" . $clean_vendor_name . "_" . ($from_date ? $from_date : 'start') . "_to_" . ($to_date ? $to_date : 'end') . ".xls";
            header("Content-Disposition: attachment; filename=" . $filename);
            header("Pragma: no-cache");
            header("Expires: 0");
            $this->load->view('page/reports/vendor-statement-report-xls', $data);
            return;
        }

        $this->load->view('page/reports/vendor-statement-report', $data);
    }

    public function vendor_balance_report($action = '')
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data['title'] = 'Vendor Balance Report';
        $data['js'] = 'reports/vendor-balance-report.inc';

        // Check for Reset request (via action segment, POST, or fallback GET)
        if ($action === 'reset' || $this->input->post('reset') == '1' || $this->input->get('reset') == '1') {
            $this->session->unset_userdata('vbal_vendor_id');
            $this->session->unset_userdata('vbal_as_on_date');
            $this->session->unset_userdata('vbal_hide_zero');
            redirect('vendor-balance-report');
            return;
        }

        // Process POST submission
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $vendor_id = $this->input->post('vendor_id') ?? '';
            $as_on_date = $this->input->post('as_on_date') ?? '';
            $hide_zero = $this->input->post('hide_zero') ? '1' : '0';

            // Only save into session for persistent filtering if not a one-off export
            if ($this->input->post('export_excel') != '1') {
                $this->session->set_userdata('vbal_vendor_id', $vendor_id);
                $this->session->set_userdata('vbal_as_on_date', $as_on_date);
                $this->session->set_userdata('vbal_hide_zero', $hide_zero);
            }
        } else {
            // GET request (initial navigation or page refresh)
            if ($this->input->get('as_on_date') !== null || $this->input->get('vendor_id') !== null) {
                $vendor_id = $this->input->get('vendor_id') ?? '';
                $as_on_date = $this->input->get('as_on_date') ?? '';
                $hide_zero = $this->input->get('hide_zero') ? '1' : '0';
            } else {
                $vendor_id = $this->session->userdata('vbal_vendor_id') ?? '';
                $as_on_date = $this->session->userdata('vbal_as_on_date');
                $hide_zero = $this->session->userdata('vbal_hide_zero') ?? '0';

                // Default to today's date if never saved in session
                if ($as_on_date === null) {
                    $as_on_date = date('Y-m-d');
                }
            }
        }

        $data['vendor_id'] = $vendor_id;
        $data['as_on_date'] = $as_on_date;
        $data['hide_zero'] = $hide_zero;

        // Fetch active vendors for dropdown
        $sql = "
            SELECT vendor_id, vendor_name, crno, mobile 
            FROM vendor_info 
            WHERE status = 'Active' 
            ORDER BY vendor_name ASC";
        $data['vendors'] = $this->db->query($sql)->result_array();

        // Vendor filter condition
        $vendor_where = "";
        if (!empty($vendor_id)) {
            $esc_v = $this->db->escape_str($vendor_id);
            $vendor_where = " AND vendor_id = '$esc_v'";
        }

        $esc_as_on = !empty($as_on_date) ? $this->db->escape_str($as_on_date) : date('Y-m-d');

        // 1. Fetch Vendor Opening Balances configured in DB
        $op_sql = "SELECT vendor_id, opening_date, opening_amount, balance_type 
                   FROM vendor_opening_balance_info";
        if (!empty($vendor_id)) {
            $op_sql .= " WHERE vendor_id = '$esc_v'";
        }
        $op_rows = $this->db->query($op_sql)->result_array();
        $op_map = [];
        foreach ($op_rows as $op) {
            $op_map[$op['vendor_id']] = $op;
        }

        // 2. Fetch all bills up to as_on_date
        $bills_sql = "
            SELECT vendor_id, invoice_date, amount FROM (
                SELECT vendor_id, invoice_date, COALESCE(total_amount_inc_addl, total_amount) AS amount
                FROM vendor_purchase_invoice_info
                WHERE status = 'Active' AND invoice_date <= '$esc_as_on' {$vendor_where}
                
                UNION ALL
                
                SELECT vendor_id, invoice_date, tot_amt_with_tax AS amount
                FROM local_purchase_bill_info
                WHERE status = 'Active' AND invoice_date <= '$esc_as_on' {$vendor_where}
                
                UNION ALL
                
                SELECT vendor_id, invoice_date, g_total AS amount
                FROM dp_bill_info
                WHERE status = 'Active' AND invoice_date <= '$esc_as_on' {$vendor_where}
                
                UNION ALL
                
                SELECT vendor_id, invoice_date, customs_payable AS amount
                FROM customs_bill_info
                WHERE status = 'Active' AND ac_type_opt = 'Accountable' AND invoice_date <= '$esc_as_on' {$vendor_where}
            ) AS all_bills
        ";
        $bills_rows = $this->db->query($bills_sql)->result_array();
        $bills_map = [];
        foreach ($bills_rows as $b) {
            $vId = $b['vendor_id'];
            if (!isset($bills_map[$vId])) {
                $bills_map[$vId] = [];
            }
            $bills_map[$vId][] = $b;
        }

        // 3. Fetch all advance payments up to as_on_date
        $adv_sql = "
            SELECT vendor_id, adv_payment_date, adv_payment_amt
            FROM vendor_advance_payment_info
            WHERE status = 'Active' AND adv_payment_date <= '$esc_as_on' {$vendor_where}
        ";
        $adv_rows = $this->db->query($adv_sql)->result_array();
        $adv_map = [];
        foreach ($adv_rows as $a) {
            $vId = $a['vendor_id'];
            if (!isset($adv_map[$vId])) {
                $adv_map[$vId] = [];
            }
            $adv_map[$vId][] = $a;
        }

        // 4. Fetch all bill payments up to as_on_date
        $pay_sql = "
            SELECT vendor_id, payment_date, amount
            FROM vendor_payment_info
            WHERE status = 'Active' AND payment_date <= '$esc_as_on' {$vendor_where}
        ";
        $pay_rows = $this->db->query($pay_sql)->result_array();
        $pay_map = [];
        foreach ($pay_rows as $p) {
            $vId = $p['vendor_id'];
            if (!isset($pay_map[$vId])) {
                $pay_map[$vId] = [];
            }
            $pay_map[$vId][] = $p;
        }

        // Aggregate records per vendor
        $record_list = [];
        $total_summary = [
            'opening_balance' => 0.000,
            'total_bills' => 0.000,
            'advance_paid' => 0.000,
            'bill_payments' => 0.000,
            'total_paid' => 0.000,
            'closing_balance' => 0.000,
            'total_payable' => 0.000,
            'total_advance' => 0.000,
        ];

        foreach ($data['vendors'] as $vend) {
            $vId = $vend['vendor_id'];

            // Skip if single vendor filter is active and this is not the vendor
            if (!empty($vendor_id) && $vendor_id != $vId) {
                continue;
            }

            $opening_bal = 0.000;
            $vendor_bills = 0.000;
            $vendor_adv = 0.000;
            $vendor_pay = 0.000;

            if (isset($op_map[$vId])) {
                $op_row = $op_map[$vId];
                $op_date = $op_row['opening_date'];
                $base_op = ($op_row['balance_type'] === 'CR') ? (float) $op_row['opening_amount'] : -(float) $op_row['opening_amount'];

                if (empty($op_date) || $esc_as_on >= $op_date) {
                    $opening_bal = $base_op;
                    // Bills on or after opening_date up to as_on_date
                    if (!empty($bills_map[$vId])) {
                        foreach ($bills_map[$vId] as $item) {
                            if (empty($op_date) || $item['invoice_date'] >= $op_date) {
                                $vendor_bills += (float) $item['amount'];
                            }
                        }
                    }
                    // Advances on or after opening_date up to as_on_date
                    if (!empty($adv_map[$vId])) {
                        foreach ($adv_map[$vId] as $item) {
                            if (empty($op_date) || $item['adv_payment_date'] >= $op_date) {
                                $vendor_adv += (float) $item['adv_payment_amt'];
                            }
                        }
                    }
                    // Payments on or after opening_date up to as_on_date
                    if (!empty($pay_map[$vId])) {
                        foreach ($pay_map[$vId] as $item) {
                            if (empty($op_date) || $item['payment_date'] >= $op_date) {
                                $vendor_pay += (float) $item['amount'];
                            }
                        }
                    }
                } else {
                    // as_on_date is prior to configured opening_date
                    $opening_bal = 0.000;
                    if (!empty($bills_map[$vId])) {
                        foreach ($bills_map[$vId] as $item) {
                            $vendor_bills += (float) $item['amount'];
                        }
                    }
                    if (!empty($adv_map[$vId])) {
                        foreach ($adv_map[$vId] as $item) {
                            $vendor_adv += (float) $item['adv_payment_amt'];
                        }
                    }
                    if (!empty($pay_map[$vId])) {
                        foreach ($pay_map[$vId] as $item) {
                            $vendor_pay += (float) $item['amount'];
                        }
                    }
                }
            } else {
                // No configured opening balance record
                $opening_bal = 0.000;
                if (!empty($bills_map[$vId])) {
                    foreach ($bills_map[$vId] as $item) {
                        $vendor_bills += (float) $item['amount'];
                    }
                }
                if (!empty($adv_map[$vId])) {
                    foreach ($adv_map[$vId] as $item) {
                        $vendor_adv += (float) $item['adv_payment_amt'];
                    }
                }
                if (!empty($pay_map[$vId])) {
                    foreach ($pay_map[$vId] as $item) {
                        $vendor_pay += (float) $item['amount'];
                    }
                }
            }

            $total_paid = $vendor_adv + $vendor_pay;
            $closing_bal = $opening_bal + $vendor_bills - $total_paid;

            // Status
            if ($closing_bal > 0.001) {
                $status = 'Payable';
                $status_color = 'danger';
            } elseif ($closing_bal < -0.001) {
                $status = 'Advance';
                $status_color = 'primary';
            } else {
                $status = 'Settled';
                $status_color = 'success';
            }

            // Check if hide_zero is active
            if ($hide_zero == '1') {
                if (abs($opening_bal) < 0.001 && abs($vendor_bills) < 0.001 && abs($total_paid) < 0.001 && abs($closing_bal) < 0.001) {
                    continue;
                }
            }

            $row_data = [
                'vendor_id' => $vId,
                'vendor_name' => $vend['vendor_name'],
                'crno' => $vend['crno'] ?? '',
                'mobile' => $vend['mobile'] ?? '',
                'opening_balance' => $opening_bal,
                'total_bills' => $vendor_bills,
                'advance_paid' => $vendor_adv,
                'bill_payments' => $vendor_pay,
                'total_paid' => $total_paid,
                'closing_balance' => $closing_bal,
                'status' => $status,
                'status_color' => $status_color
            ];

            $record_list[] = $row_data;

            // Totals
            $total_summary['opening_balance'] += $opening_bal;
            $total_summary['total_bills'] += $vendor_bills;
            $total_summary['advance_paid'] += $vendor_adv;
            $total_summary['bill_payments'] += $vendor_pay;
            $total_summary['total_paid'] += $total_paid;
            $total_summary['closing_balance'] += $closing_bal;

            if ($closing_bal > 0) {
                $total_summary['total_payable'] += $closing_bal;
            } else {
                $total_summary['total_advance'] += abs($closing_bal);
            }
        }

        $data['record_list'] = $record_list;
        $data['summary'] = $total_summary;

        // Selected vendor name for titles and export
        $selected_vendor_name = 'All_Vendors';
        if (!empty($vendor_id)) {
            foreach ($data['vendors'] as $v) {
                if ($v['vendor_id'] == $vendor_id) {
                    $selected_vendor_name = $v['vendor_name'];
                    break;
                }
            }
        }
        $data['selected_vendor_name'] = $selected_vendor_name;

        // Excel Export
        if ($this->input->get_post('export_excel') == '1') {
            header("Content-Type: application/vnd.ms-excel");
            $clean_vendor_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $selected_vendor_name);
            $filename = "Vendor_Balance_Report_" . $clean_vendor_name . "_As_On_" . ($as_on_date ? $as_on_date : date('Y-m-d')) . ".xls";
            header("Content-Disposition: attachment; filename=" . $filename);
            header("Pragma: no-cache");
            header("Expires: 0");
            $this->load->view('page/reports/vendor-balance-report-xls', $data);
            return;
        }

        $this->load->view('page/reports/vendor-balance-report', $data);
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

    public function customer_statement_report($action = '')
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        // Handle Reset
        if ($action === 'reset' || $this->input->post('reset') == '1' || $this->input->get('reset') == '1') {
            $this->session->unset_userdata('stmt_customer_id');
            $this->session->unset_userdata('stmt_cust_from_date');
            $this->session->unset_userdata('stmt_cust_to_date');
            redirect('customer-statement-report');
            return;
        }

        // If accessed with GET query parameters (e.g. ?customer_id=3&to_date=...),
        // save to session and immediately redirect to clean URL without query string
        if ($this->input->server('REQUEST_METHOD') === 'GET' && ($this->input->get('customer_id') !== null || $this->input->get('to_date') !== null || $this->input->get('from_date') !== null)) {
            $get_customer_id = $this->input->get('customer_id');
            $get_from_date = $this->input->get('from_date');
            $get_to_date = $this->input->get('to_date');

            if ($get_customer_id !== null) {
                $this->session->set_userdata('stmt_customer_id', $get_customer_id);
            }
            if ($get_from_date !== null) {
                $this->session->set_userdata('stmt_cust_from_date', $get_from_date);
            }
            if ($get_to_date !== null) {
                $this->session->set_userdata('stmt_cust_to_date', $get_to_date);
            }

            redirect('customer-statement-report');
            return;
        }

        $data['title'] = 'Customer Statement Report';
        $data['js'] = 'reports/customer-reports.inc';

        // Process POST submission or read from session
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $customer_id = $this->input->post('customer_id') !== null ? $this->input->post('customer_id') : '';
            $from_date = $this->input->post('from_date') !== null ? $this->input->post('from_date') : '';
            $to_date = $this->input->post('to_date') !== null ? $this->input->post('to_date') : '';

            // Only save into session if not a one-off export
            if ($this->input->post('export_excel') != '1') {
                $this->session->set_userdata('stmt_customer_id', $customer_id);
                $this->session->set_userdata('stmt_cust_from_date', $from_date);
                $this->session->set_userdata('stmt_cust_to_date', $to_date);
            }
        } else {
            $customer_id = $this->session->userdata('stmt_customer_id') ?? '';
            $from_date = $this->session->userdata('stmt_cust_from_date') ?? '';
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
                $op_amount = (float) $op_row['opening_amount'];
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
                    FROM (
                        SELECT total_amount FROM tender_enq_invoice_info
                        WHERE status = 'Active' AND customer_id = '$esc_customer' AND invoice_date >= '$esc_op' AND invoice_date < '$esc_from'
                        UNION ALL
                        SELECT total_amount FROM credit_debit_note_info
                        WHERE status != 'Delete' AND party_type = 'Customer' AND note_type = 'Debit' AND customer_id = '$esc_customer' AND note_date >= '$esc_op' AND note_date < '$esc_from'
                    ) AS inv
                ";
                $inv_query = $this->db->query($invoice_sql);
                $inv_row = $inv_query->row_array();
                $total_invoices = (float) ($inv_row['total_invoices'] ?? 0);

                $receipt_sql = "
                    SELECT IFNULL(SUM(amount), 0) AS total_receipts
                    FROM (
                        SELECT amount FROM tender_receipt_info
                        WHERE status = 'Active' AND customer_id = '$esc_customer' AND receipt_date >= '$esc_op' AND receipt_date < '$esc_from'
                        UNION ALL
                        SELECT total_amount AS amount FROM credit_debit_note_info
                        WHERE status != 'Delete' AND party_type = 'Customer' AND note_type = 'Credit' AND customer_id = '$esc_customer' AND note_date >= '$esc_op' AND note_date < '$esc_from'
                    ) AS rec
                ";
                $rec_query = $this->db->query($receipt_sql);
                $rec_row = $rec_query->row_array();
                $total_receipts = (float) ($rec_row['total_receipts'] ?? 0);

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
                    FROM (
                        SELECT total_amount FROM tender_enq_invoice_info
                        WHERE status = 'Active' AND invoice_date < '$esc_from'
                        UNION ALL
                        SELECT total_amount FROM credit_debit_note_info
                        WHERE status != 'Delete' AND party_type = 'Customer' AND note_type = 'Debit' AND note_date < '$esc_from'
                    ) AS inv
                ";
                $inv_query = $this->db->query($invoice_sql);
                $inv_row = $inv_query->row_array();
                $total_invoices = $inv_row['total_invoices'] ?? 0;

                // Total Receipts before from_date
                $receipt_sql = "
                    SELECT IFNULL(SUM(amount), 0) AS total_receipts
                    FROM (
                        SELECT amount FROM tender_receipt_info
                        WHERE status = 'Active' AND receipt_date < '$esc_from'
                        UNION ALL
                        SELECT total_amount AS amount FROM credit_debit_note_info
                        WHERE status != 'Delete' AND party_type = 'Customer' AND note_type = 'Credit' AND note_date < '$esc_from'
                    ) AS rec
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
                  
                UNION ALL
                
                SELECT 
                    a.note_date AS tr_date,
                    a.note_no AS voucher_no,
                    'Debit Note' AS description,
                    a.total_amount AS debit_amt,
                    0.000 AS credit_amt,
                    'invoice' AS type,
                    c.customer_name
                FROM credit_debit_note_info a
                LEFT JOIN customer_info c ON a.customer_id = c.customer_id AND c.status = 'Active'
                WHERE a.status != 'Delete' AND a.party_type = 'Customer' AND a.note_type = 'Debit'
                  " . (!empty($customer_id) ? "AND a.customer_id = '$esc_customer'" : "") . "
                  " . (!empty($from_date) ? "AND a.note_date >= '$esc_from'" : "") . "
                  " . (!empty($to_date) ? "AND a.note_date <= '" . $this->db->escape_str($to_date) . "'" : "") . "

                UNION ALL

                SELECT 
                    a.note_date AS tr_date,
                    a.note_no AS voucher_no,
                    'Credit Note' AS description,
                    0.000 AS debit_amt,
                    a.total_amount AS credit_amt,
                    'receipt' AS type,
                    c.customer_name
                FROM credit_debit_note_info a
                LEFT JOIN customer_info c ON a.customer_id = c.customer_id AND c.status = 'Active'
                WHERE a.status != 'Delete' AND a.party_type = 'Customer' AND a.note_type = 'Credit'
                  " . (!empty($customer_id) ? "AND a.customer_id = '$esc_customer'" : "") . "
                  " . (!empty($from_date) ? "AND a.note_date >= '$esc_from'" : "") . "
                  " . (!empty($to_date) ? "AND a.note_date <= '" . $this->db->escape_str($to_date) . "'" : "") . "
            ) AS transactions
            ORDER BY tr_date ASC, voucher_no ASC
        ";

        $query = $this->db->query($txn_sql);
        $data['record_list'] = $query->result_array();

        // Get selected customer name
        $selected_customer_name = 'All_Customers';
        if (!empty($customer_id) && !empty($data['customers'])) {
            foreach ($data['customers'] as $c) {
                if ($c['id'] == $customer_id) {
                    $selected_customer_name = $c['customer_name'];
                    break;
                }
            }
        }
        $data['selected_customer_name'] = $selected_customer_name;

        if ($this->input->get_post('export_excel') == '1') {
            header("Content-Type: application/vnd.ms-excel");
            $clean_customer_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $selected_customer_name);
            $filename = "Customer_Statement_Report_" . $clean_customer_name . "_" . ($from_date ? $from_date : 'start') . "_to_" . ($to_date ? $to_date : 'end') . ".xls";
            header("Content-Disposition: attachment; filename=" . $filename);
            header("Pragma: no-cache");
            header("Expires: 0");
            $this->load->view('page/reports/customer-statement-report-xls', $data);
            return;
        }

        $this->load->view('page/reports/customer-statement-report', $data);
    }

    public function customer_balance_report($action = '')
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data['title'] = 'Customer Balance Report';
        $data['js'] = 'reports/customer-balance-report.inc';

        // Check for Reset request (via action segment, POST, or fallback GET)
        if ($action === 'reset' || $this->input->post('reset') == '1' || $this->input->get('reset') == '1') {
            $this->session->unset_userdata('cbal_customer_id');
            $this->session->unset_userdata('cbal_as_on_date');
            $this->session->unset_userdata('cbal_hide_zero');
            redirect('customer-balance-report');
            return;
        }

        // Process POST submission
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $customer_id = $this->input->post('customer_id') ?? '';
            $as_on_date = $this->input->post('as_on_date') ?? '';
            $hide_zero = $this->input->post('hide_zero') ? '1' : '0';

            // Only save into session for persistent filtering if not a one-off export
            if ($this->input->post('export_excel') != '1') {
                $this->session->set_userdata('cbal_customer_id', $customer_id);
                $this->session->set_userdata('cbal_as_on_date', $as_on_date);
                $this->session->set_userdata('cbal_hide_zero', $hide_zero);
            }
        } else {
            // GET request (initial navigation or page refresh)
            if ($this->input->get('as_on_date') !== null || $this->input->get('customer_id') !== null) {
                $customer_id = $this->input->get('customer_id') ?? '';
                $as_on_date = $this->input->get('as_on_date') ?? '';
                $hide_zero = $this->input->get('hide_zero') ? '1' : '0';
            } else {
                $customer_id = $this->session->userdata('cbal_customer_id') ?? '';
                $as_on_date = $this->session->userdata('cbal_as_on_date');
                $hide_zero = $this->session->userdata('cbal_hide_zero') ?? '0';

                // Default to today's date if never saved in session
                if ($as_on_date === null) {
                    $as_on_date = date('Y-m-d');
                }
            }
        }

        $data['customer_id'] = $customer_id;
        $data['as_on_date'] = $as_on_date;
        $data['hide_zero'] = $hide_zero;

        // Fetch active customers for dropdown
        $sql = "
            SELECT customer_id, customer_name, crno, mobile 
            FROM customer_info 
            WHERE status = 'Active' 
            ORDER BY customer_name ASC";
        $data['customers'] = $this->db->query($sql)->result_array();

        // Customer filter condition
        $cust_where = "";
        if (!empty($customer_id)) {
            $esc_c = $this->db->escape_str($customer_id);
            $cust_where = " AND customer_id = '$esc_c'";
        }

        $esc_as_on = !empty($as_on_date) ? $this->db->escape_str($as_on_date) : date('Y-m-d');

        // 1. Fetch Customer Opening Balances configured in DB
        $op_sql = "SELECT customer_id, opening_date, opening_amount, balance_type 
                   FROM customer_opening_balance_info";
        if (!empty($customer_id)) {
            $op_sql .= " WHERE customer_id = '$esc_c'";
        }
        $op_rows = $this->db->query($op_sql)->result_array();
        $op_map = [];
        foreach ($op_rows as $op) {
            $op_map[$op['customer_id']] = $op;
        }

        // 2. Fetch all sales invoices up to as_on_date
        $inv_sql = "
            SELECT customer_id, invoice_date, total_amount AS amount
            FROM tender_enq_invoice_info
            WHERE status = 'Active' AND invoice_date <= '$esc_as_on' {$cust_where}
        ";
        $inv_rows = $this->db->query($inv_sql)->result_array();
        $inv_map = [];
        foreach ($inv_rows as $inv) {
            $cId = $inv['customer_id'];
            if (!isset($inv_map[$cId])) {
                $inv_map[$cId] = [];
            }
            $inv_map[$cId][] = $inv;
        }

        // 3. Fetch all receipts up to as_on_date
        $rec_sql = "
            SELECT customer_id, receipt_date, amount, is_without_bill
            FROM tender_receipt_info
            WHERE status = 'Active' AND receipt_date <= '$esc_as_on' {$cust_where}
        ";
        $rec_rows = $this->db->query($rec_sql)->result_array();
        $rec_map = [];
        foreach ($rec_rows as $r) {
            $cId = $r['customer_id'];
            if (!isset($rec_map[$cId])) {
                $rec_map[$cId] = [];
            }
            $rec_map[$cId][] = $r;
        }

        // Aggregate records per customer
        $record_list = [];
        $total_summary = [
            'opening_balance' => 0.000,
            'total_invoices' => 0.000,
            'advance_received' => 0.000,
            'invoice_receipts' => 0.000,
            'total_received' => 0.000,
            'closing_balance' => 0.000,
            'total_receivable' => 0.000,
            'total_advance' => 0.000,
        ];

        foreach ($data['customers'] as $cust) {
            $cId = $cust['customer_id'];

            // Skip if single customer filter is active and this is not the customer
            if (!empty($customer_id) && $customer_id != $cId) {
                continue;
            }

            $opening_bal = 0.000;
            $cust_invoices = 0.000;
            $cust_adv_rec = 0.000;
            $cust_inv_rec = 0.000;

            if (isset($op_map[$cId])) {
                $op_row = $op_map[$cId];
                $op_date = $op_row['opening_date'];
                // DR is positive receivable (customer owes us), CR is negative advance (we owe customer)
                $base_op = ($op_row['balance_type'] === 'DR') ? (float) $op_row['opening_amount'] : -(float) $op_row['opening_amount'];

                if (empty($op_date) || $esc_as_on >= $op_date) {
                    $opening_bal = $base_op;
                    // Invoices on or after opening_date up to as_on_date
                    if (!empty($inv_map[$cId])) {
                        foreach ($inv_map[$cId] as $item) {
                            if (empty($op_date) || $item['invoice_date'] >= $op_date) {
                                $cust_invoices += (float) $item['amount'];
                            }
                        }
                    }
                    // Receipts on or after opening_date up to as_on_date
                    if (!empty($rec_map[$cId])) {
                        foreach ($rec_map[$cId] as $item) {
                            if (empty($op_date) || $item['receipt_date'] >= $op_date) {
                                if (!empty($item['is_without_bill']) && $item['is_without_bill'] == 1) {
                                    $cust_adv_rec += (float) $item['amount'];
                                } else {
                                    $cust_inv_rec += (float) $item['amount'];
                                }
                            }
                        }
                    }
                } else {
                    // as_on_date is prior to configured opening_date
                    $opening_bal = 0.000;
                    if (!empty($inv_map[$cId])) {
                        foreach ($inv_map[$cId] as $item) {
                            $cust_invoices += (float) $item['amount'];
                        }
                    }
                    if (!empty($rec_map[$cId])) {
                        foreach ($rec_map[$cId] as $item) {
                            if (!empty($item['is_without_bill']) && $item['is_without_bill'] == 1) {
                                $cust_adv_rec += (float) $item['amount'];
                            } else {
                                $cust_inv_rec += (float) $item['amount'];
                            }
                        }
                    }
                }
            } else {
                // No configured opening balance record
                $opening_bal = 0.000;
                if (!empty($inv_map[$cId])) {
                    foreach ($inv_map[$cId] as $item) {
                        $cust_invoices += (float) $item['amount'];
                    }
                }
                if (!empty($rec_map[$cId])) {
                    foreach ($rec_map[$cId] as $item) {
                        if (!empty($item['is_without_bill']) && $item['is_without_bill'] == 1) {
                            $cust_adv_rec += (float) $item['amount'];
                        } else {
                            $cust_inv_rec += (float) $item['amount'];
                        }
                    }
                }
            }

            $total_received = $cust_adv_rec + $cust_inv_rec;
            $closing_bal = $opening_bal + $cust_invoices - $total_received;

            // Status: positive = Receivable, negative = Advance, zero = Settled
            if ($closing_bal > 0.001) {
                $status = 'Receivable';
                $status_color = 'danger';
            } elseif ($closing_bal < -0.001) {
                $status = 'Advance';
                $status_color = 'primary';
            } else {
                $status = 'Settled';
                $status_color = 'success';
            }

            // Check if hide_zero is active
            if ($hide_zero == '1') {
                if (abs($opening_bal) < 0.001 && abs($cust_invoices) < 0.001 && abs($total_received) < 0.001 && abs($closing_bal) < 0.001) {
                    continue;
                }
            }

            $row_data = [
                'customer_id' => $cId,
                'customer_name' => $cust['customer_name'],
                'crno' => $cust['crno'] ?? '',
                'mobile' => $cust['mobile'] ?? '',
                'opening_balance' => $opening_bal,
                'total_invoices' => $cust_invoices,
                'advance_received' => $cust_adv_rec,
                'invoice_receipts' => $cust_inv_rec,
                'total_received' => $total_received,
                'closing_balance' => $closing_bal,
                'status' => $status,
                'status_color' => $status_color
            ];

            $record_list[] = $row_data;

            // Totals
            $total_summary['opening_balance'] += $opening_bal;
            $total_summary['total_invoices'] += $cust_invoices;
            $total_summary['advance_received'] += $cust_adv_rec;
            $total_summary['invoice_receipts'] += $cust_inv_rec;
            $total_summary['total_received'] += $total_received;
            $total_summary['closing_balance'] += $closing_bal;

            if ($closing_bal > 0) {
                $total_summary['total_receivable'] += $closing_bal;
            } else {
                $total_summary['total_advance'] += abs($closing_bal);
            }
        }

        $data['record_list'] = $record_list;
        $data['summary'] = $total_summary;

        // Selected customer name for titles and export
        $selected_customer_name = 'All_Customers';
        if (!empty($customer_id)) {
            foreach ($data['customers'] as $c) {
                if ($c['customer_id'] == $customer_id) {
                    $selected_customer_name = $c['customer_name'];
                    break;
                }
            }
        }
        $data['selected_customer_name'] = $selected_customer_name;

        // Excel Export
        if ($this->input->get_post('export_excel') == '1') {
            header("Content-Type: application/vnd.ms-excel");
            $clean_customer_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $selected_customer_name);
            $filename = "Customer_Balance_Report_" . $clean_customer_name . "_As_On_" . ($as_on_date ? $as_on_date : date('Y-m-d')) . ".xls";
            header("Content-Disposition: attachment; filename=" . $filename);
            header("Pragma: no-cache");
            header("Expires: 0");
            $this->load->view('page/reports/customer-balance-report-xls', $data);
            return;
        }

        $this->load->view('page/reports/customer-balance-report', $data);
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

    public function customer_invoice_report($action = '')
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data['title'] = 'Customer Invoice Report';
        $data['js'] = 'reports/customer-invoice-report.inc';
        $data['s_url'] = 'customer-invoice-report';

        // Check for Reset request
        if ($action === 'reset' || $this->input->post('reset') == '1' || $this->input->get('reset') == '1') {
            $this->session->unset_userdata('cir_from_date');
            $this->session->unset_userdata('cir_to_date');
            $this->session->unset_userdata('cir_customer_id');
            redirect('customer-invoice-report');
            return;
        }

        // Process POST submission
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $srch_from_date = $this->input->post('srch_from_date') ?? '';
            $srch_to_date = $this->input->post('srch_to_date') ?? '';
            $srch_customer_id = $this->input->post('srch_customer_id') ?? '';

            if ($this->input->post('export_excel') != '1') {
                $this->session->set_userdata('cir_from_date', $srch_from_date);
                $this->session->set_userdata('cir_to_date', $srch_to_date);
                $this->session->set_userdata('cir_customer_id', $srch_customer_id);
            }
        } else {
            // GET request
            if ($this->input->get('srch_from_date') !== null || $this->input->get('srch_to_date') !== null || $this->input->get('srch_customer_id') !== null) {
                $srch_from_date = $this->input->get('srch_from_date') ?? '';
                $srch_to_date = $this->input->get('srch_to_date') ?? '';
                $srch_customer_id = $this->input->get('srch_customer_id') ?? '';
            } else {
                $srch_from_date = $this->session->userdata('cir_from_date');
                $srch_to_date = $this->session->userdata('cir_to_date');
                $srch_customer_id = $this->session->userdata('cir_customer_id') ?? '';

                // Default to 1st of current month and today
                if ($srch_from_date === null) {
                    $srch_from_date = date('Y-m-01');
                }
                if ($srch_to_date === null) {
                    $srch_to_date = date('Y-m-d');
                }
            }
        }

        $data['srch_from_date'] = $srch_from_date;
        $data['srch_to_date'] = $srch_to_date;
        $data['srch_customer_id'] = $srch_customer_id;

        // Fetch active customers for dropdown
        $sql = "
            SELECT customer_id, customer_name 
            FROM customer_info 
            WHERE status = 'Active' 
            ORDER BY customer_name ASC";
        $data['customer_list'] = $this->db->query($sql)->result_array();

        // Selected customer name for display
        $selected_customer_name = 'All Customers';
        if (!empty($srch_customer_id)) {
            foreach ($data['customer_list'] as $c) {
                if ($c['customer_id'] == $srch_customer_id) {
                    $selected_customer_name = $c['customer_name'];
                    break;
                }
            }
        }
        $data['selected_customer_name'] = $selected_customer_name;

        // Build WHERE conditions
        $where_clauses = ["a.status = 'Active'"];
        $params = [];

        if (!empty($srch_from_date) && !empty($srch_to_date)) {
            $where_clauses[] = "a.invoice_date BETWEEN ? AND ?";
            $params[] = $srch_from_date;
            $params[] = $srch_to_date;
        } elseif (!empty($srch_from_date)) {
            $where_clauses[] = "a.invoice_date >= ?";
            $params[] = $srch_from_date;
        } elseif (!empty($srch_to_date)) {
            $where_clauses[] = "a.invoice_date <= ?";
            $params[] = $srch_to_date;
        }

        if (!empty($srch_customer_id)) {
            $where_clauses[] = "a.customer_id = ?";
            $params[] = $srch_customer_id;
        }

        $where_sql = implode(' AND ', $where_clauses);

        $sql = "
            SELECT 
                a.tender_enq_invoice_id,
                a.tender_enquiry_id,
                a.tender_po_id,
                a.invoice_date,
                a.invoice_no,
                a.customer_id,
                b.customer_name,
                b.gst AS client_vat_no,
                b.crno AS customer_crno,
                cpo.customer_po_no,
                cpo.our_po_no,
                te.enquiry_no,
                get_tender_info(a.tender_enquiry_id) AS tender_details,
                COALESCE(cur.currency_code, po_cur.currency_code, 'BHD') AS currency_code,
                COALESCE(cur.decimal_point, po_cur.decimal_point, 3) AS decimal_point,
                (a.total_amount - IFNULL(a.tax_amount, 0)) AS taxable_amount,
                IFNULL(a.tax_amount, 0) AS tax_amount,
                a.total_amount
            FROM tender_enq_invoice_info a
            LEFT JOIN customer_info b ON a.customer_id = b.customer_id AND b.status = 'Active'
            LEFT JOIN customer_tender_po_info cpo ON a.tender_po_id = cpo.tender_po_id AND cpo.status = 'Active'
            LEFT JOIN tender_enquiry_info te ON a.tender_enquiry_id = te.tender_enquiry_id AND te.status = 'Active'
            LEFT JOIN currencies_info cur ON cur.currency_id = a.currency_id AND cur.status = 'Active'
            LEFT JOIN currencies_info po_cur ON po_cur.currency_id = cpo.currency_id AND po_cur.status = 'Active'
            WHERE {$where_sql}
            ORDER BY a.invoice_date ASC, a.invoice_no ASC, a.tender_enq_invoice_id ASC
        ";

        $records = $this->db->query($sql, $params)->result_array();
        $data['records'] = $records;

        // KPI metrics
        $total_invoices = count($records);
        $total_taxable = 0;
        $total_vat = 0;
        $grand_total = 0;

        foreach ($records as $row) {
            $total_taxable += (float) $row['taxable_amount'];
            $total_vat += (float) $row['tax_amount'];
            $grand_total += (float) $row['total_amount'];
        }

        $data['total_invoices'] = $total_invoices;
        $data['total_taxable'] = $total_taxable;
        $data['total_vat'] = $total_vat;
        $data['grand_total'] = $grand_total;

        // Excel Export
        if ($this->input->post('export_excel') == '1' || $this->input->get('export') == 'excel') {
            $this->load->helper('download');
            $filename = "Customer_Invoice_Report_" . ($srch_from_date ? $srch_from_date : 'all') . "_to_" . ($srch_to_date ? $srch_to_date : 'all') . ".xls";
            $content = $this->load->view('page/reports/customer-invoice-report-xls', $data, TRUE);
            force_download($filename, $content);
            return;
        }

        $this->load->view('page/reports/customer-invoice-report', $data);
    }

    public function supplier_invoice_report($action = '')
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data['title'] = 'Supplier Invoice Report';
        $data['js'] = 'reports/supplier-invoice-report.inc';
        $data['s_url'] = 'supplier-invoice-report';

        // Check for Reset request
        if ($action === 'reset' || $this->input->post('reset') == '1' || $this->input->get('reset') == '1') {
            $this->session->unset_userdata('sir_from_date');
            $this->session->unset_userdata('sir_to_date');
            $this->session->unset_userdata('sir_vendor_id');
            $this->session->unset_userdata('sir_bill_type');
            redirect('supplier-invoice-report');
            return;
        }

        // Process POST submission
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $srch_from_date = $this->input->post('srch_from_date') ?? '';
            $srch_to_date = $this->input->post('srch_to_date') ?? '';
            $srch_vendor_id = $this->input->post('srch_vendor_id') ?? '';
            $srch_bill_type = $this->input->post('srch_bill_type') ?? '';

            if ($this->input->post('export_excel') != '1') {
                $this->session->set_userdata('sir_from_date', $srch_from_date);
                $this->session->set_userdata('sir_to_date', $srch_to_date);
                $this->session->set_userdata('sir_vendor_id', $srch_vendor_id);
                $this->session->set_userdata('sir_bill_type', $srch_bill_type);
            }
        } else {
            // GET request
            if ($this->input->get('srch_from_date') !== null || $this->input->get('srch_to_date') !== null || $this->input->get('srch_vendor_id') !== null || $this->input->get('srch_bill_type') !== null) {
                $srch_from_date = $this->input->get('srch_from_date') ?? '';
                $srch_to_date = $this->input->get('srch_to_date') ?? '';
                $srch_vendor_id = $this->input->get('srch_vendor_id') ?? '';
                $srch_bill_type = $this->input->get('srch_bill_type') ?? '';
            } else {
                $srch_from_date = $this->session->userdata('sir_from_date');
                $srch_to_date = $this->session->userdata('sir_to_date');
                $srch_vendor_id = $this->session->userdata('sir_vendor_id') ?? '';
                $srch_bill_type = $this->session->userdata('sir_bill_type') ?? '';

                // Default to 1st of current month and today
                if ($srch_from_date === null) {
                    $srch_from_date = date('Y-m-01');
                }
                if ($srch_to_date === null) {
                    $srch_to_date = date('Y-m-d');
                }
            }
        }

        $data['srch_from_date'] = $srch_from_date;
        $data['srch_to_date'] = $srch_to_date;
        $data['srch_vendor_id'] = $srch_vendor_id;
        $data['srch_bill_type'] = $srch_bill_type;

        // Fetch active vendors/suppliers for dropdown
        $sql = "
            SELECT vendor_id, vendor_name 
            FROM vendor_info 
            WHERE status = 'Active' 
            ORDER BY vendor_name ASC";
        $data['vendor_list'] = $this->db->query($sql)->result_array();

        // Bill Type Options
        $data['bill_type_opt'] = [
            '' => 'All Bill Types',
            'Supplier Bill' => 'Supplier Bill (Purchase Invoice)',
            'Local Supplier Bill' => 'Local Supplier Bill'
        ];

        // Selected vendor name for display
        $selected_vendor_name = 'All Suppliers';
        if (!empty($srch_vendor_id)) {
            foreach ($data['vendor_list'] as $v) {
                if ($v['vendor_id'] == $srch_vendor_id) {
                    $selected_vendor_name = $v['vendor_name'];
                    break;
                }
            }
        }
        $data['selected_vendor_name'] = $selected_vendor_name;

        // Selected bill type label
        $selected_bill_type_label = !empty($srch_bill_type) && isset($data['bill_type_opt'][$srch_bill_type])
            ? $data['bill_type_opt'][$srch_bill_type]
            : 'All Bill Types';
        $data['selected_bill_type_label'] = $selected_bill_type_label;

        // Build outer WHERE conditions
        $where_clauses = ["1=1"];
        $params = [];

        if (!empty($srch_from_date) && !empty($srch_to_date)) {
            $where_clauses[] = "all_bills.invoice_date BETWEEN ? AND ?";
            $params[] = $srch_from_date;
            $params[] = $srch_to_date;
        } elseif (!empty($srch_from_date)) {
            $where_clauses[] = "all_bills.invoice_date >= ?";
            $params[] = $srch_from_date;
        } elseif (!empty($srch_to_date)) {
            $where_clauses[] = "all_bills.invoice_date <= ?";
            $params[] = $srch_to_date;
        }

        if (!empty($srch_vendor_id)) {
            $where_clauses[] = "all_bills.vendor_id = ?";
            $params[] = $srch_vendor_id;
        }

        if (!empty($srch_bill_type)) {
            $where_clauses[] = "all_bills.bill_type = ?";
            $params[] = $srch_bill_type;
        }

        $where_sql = implode(' AND ', $where_clauses);

        $sql = "
            SELECT * FROM (
                -- 1. Supplier Bill / Purchase Invoice
                SELECT
                    a.vendor_purchase_invoice_id AS bill_id,
                    a.invoice_date,
                    a.invoice_no,
                    a.vendor_id,
                    v.vendor_name,
                    COALESCE(v.gst, v.crno) AS vendor_vat_cr,
                    a.tender_enquiry_id,
                    get_tender_info(a.tender_enquiry_id) AS tender_details, 
                    3 as decimal_point,

                    a.total_amount_wo_tax_inc_addl AS taxable_amount,
                    IFNULL(a.total_tax_amount_inc_addl, 0) AS tax_amount,
                    a.total_amount_inc_addl AS total_amount,

                    'Supplier Bill' AS bill_type
                FROM vendor_purchase_invoice_info a
                LEFT JOIN vendor_info v ON a.vendor_id = v.vendor_id AND v.status = 'Active'
                LEFT JOIN vendor_po_info vpo ON vpo.vendor_po_id = a.vendor_po_id AND vpo.status = 'Active'
                 WHERE a.status = 'Active'

                UNION ALL

                -- 2. Local Supplier Bill
                SELECT
                    a.local_purchase_bill_id AS bill_id,
                    a.invoice_date,
                    a.invoice_no,
                    a.vendor_id,
                    v.vendor_name,
                    COALESCE(v.gst, v.crno) AS vendor_vat_cr,
                    a.tender_enquiry_id,
                    get_tender_info(a.tender_enquiry_id) AS tender_details, 
                    3 AS decimal_point,
                    IFNULL(a.tot_amt_wo_tax, 0) AS taxable_amount,
                    IFNULL(a.vat_amt, 0) AS tax_amount,
                    IFNULL(a.tot_amt_with_tax, 0) AS total_amount,
                    'Local Supplier Bill' AS bill_type
                 FROM local_purchase_bill_info a
                LEFT JOIN vendor_info v ON a.vendor_id = v.vendor_id AND v.status = 'Active'
                WHERE a.status = 'Active'  
            ) AS all_bills
            WHERE {$where_sql}
            ORDER BY all_bills.invoice_date ASC, all_bills.invoice_no ASC, all_bills.bill_id ASC
        ";

        $records = $this->db->query($sql, $params)->result_array();
        $data['records'] = $records;

        // KPI metrics
        $total_bills = count($records);
        $total_taxable = 0;
        $total_vat = 0;
        $grand_total = 0;

        foreach ($records as $row) {
            $total_taxable += (float) $row['taxable_amount'];
            $total_vat += (float) $row['tax_amount'];
            $grand_total += (float) $row['total_amount'];
        }

        $data['total_bills'] = $total_bills;
        $data['total_taxable'] = $total_taxable;
        $data['total_vat'] = $total_vat;
        $data['grand_total'] = $grand_total;

        // Excel Export
        if ($this->input->post('export_excel') == '1' || $this->input->get('export') == 'excel') {
            $this->load->helper('download');
            $filename = "Supplier_Invoice_Report_" . ($srch_from_date ? $srch_from_date : 'all') . "_to_" . ($srch_to_date ? $srch_to_date : 'all') . ".xls";
            $content = $this->load->view('page/reports/supplier-invoice-report-xls', $data, TRUE);
            force_download($filename, $content);
            return;
        }

        $this->load->view('page/reports/supplier-invoice-report', $data);
    }

    public function pl_report()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data['title'] = 'Profit & Loss Report';
        $data['js'] = 'reports/pl-report.inc';

        // Date Filter
        if ($this->input->post('srch_from_date')) {
            $srch_from_date = $this->input->post('srch_from_date');
            $srch_to_date = $this->input->post('srch_to_date');
        } else {
            $srch_from_date = date('Y-m-01');
            $srch_to_date = date('Y-m-d');
        }

        $data['srch_from_date'] = $srch_from_date;
        $data['srch_to_date'] = $srch_to_date;


        $this->load->model('Pl_model');
        $sales_summary = $this->Pl_model->get_sales_summary($srch_from_date, $srch_to_date);
        $purchases_summary = $this->Pl_model->get_purchases_summary($srch_from_date, $srch_to_date);
        $other_income = $this->Pl_model->get_otherincome_summary($srch_from_date, $srch_to_date);
        $indirect_expenses = $this->Pl_model->get_indirect_expenses_summary($srch_from_date, $srch_to_date);

        $data['sales_summary'] = $sales_summary;
        $data['purchases_summary'] = $purchases_summary;
        $data['sales'] = is_array($sales_summary) ? ($sales_summary['total_sales_wo_tax'] ?? 0) : $sales_summary;
        $data['purchases'] = is_array($purchases_summary) ? ($purchases_summary['total_purchases_wo_tax'] ?? 0) : $purchases_summary;
        $data['other_income'] = $other_income;
        $data['indirect_expenses'] = $indirect_expenses;

        // Load the Profit & Loss report view
        $this->load->view('page/reports/pl-report', $data);
    }



    public function supplier_summary_report()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data['title'] = 'PO Summary Report';
        $data['js'] = 'reports/supplier-summary-report.inc';

        // Date Filter
        if ($this->input->post('srch_from_date')) {
            $srch_from_date = $this->input->post('srch_from_date');
            $srch_to_date = $this->input->post('srch_to_date');
        } else {
            $srch_from_date = date('Y-m-01');
            $srch_to_date = date('Y-m-d');
        }

        $data['srch_from_date'] = $srch_from_date;
        $data['srch_to_date'] = $srch_to_date;


        $this->load->model('Supplier_summary_model');
        $data['suppliers'] = $this->Supplier_summary_model->get_supplier_summary($srch_from_date, $srch_to_date);

        if ($this->input->get_post('export_excel') == '1') {
            $this->load->helper('download');
            $filename = "Supplier_Summary_Report_" . ($srch_from_date ? $srch_from_date : 'start') . "_to_" . ($srch_to_date ? $srch_to_date : 'end') . ".xls";
            $content = $this->load->view('page/reports/supplier-summary-report-xls', $data, TRUE);
            force_download($filename, $content);
            return;
        }

        $this->load->view('page/reports/supplier-summary-report', $data);
    }
    public function invoice_report()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data['title'] = 'Invoice Summary Report';
        $data['js'] = 'reports/invoice-report.inc';

        // Date Filter
        if ($this->input->post('srch_from_date')) {
            $srch_from_date = $this->input->post('srch_from_date');
            $srch_to_date = $this->input->post('srch_to_date');
        } else {
            $srch_from_date = date('Y-m-01');
            $srch_to_date = date('Y-m-d');
        }
        $data['srch_from_date'] = $srch_from_date;
        $data['srch_to_date'] = $srch_to_date;


        $this->load->model('Invoice_report_model');
        $data['invoices'] = $this->Invoice_report_model->get_invoice_summary($srch_from_date, $srch_to_date);

        if ($this->input->get_post('export_excel') == '1') {
            $this->load->helper('download');
            $filename = "Invoice_Report_" . ($srch_from_date ? $srch_from_date : 'start') . "_to_" . ($srch_to_date ? $srch_to_date : 'end') . ".xls";
            $content = $this->load->view('page/reports/invoice-report-xls', $data, TRUE);
            force_download($filename, $content);
            return;
        }

        $this->load->view('page/reports/invoice-report', $data);
    }

    public function tender_progress_report()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data['title'] = 'Tender Progress Report';
        $data['js'] = 'reports/tender-progress-report.inc';

        // Date Filter
        if ($this->input->post('mode') == 'Search') {
            $srch_from_date = $this->input->post('srch_from_date');
            $srch_to_date = $this->input->post('srch_to_date');
            $srch_customer_id = $this->input->post('srch_customer_id');
            $srch_text = $this->input->post('srch_text');
        } else {
            $srch_from_date = date('Y-m-01');
            $srch_to_date = date('Y-m-d');
            $srch_customer_id = '';
            $srch_text = '';
        }

        $data['srch_from_date'] = $srch_from_date;
        $data['srch_to_date'] = $srch_to_date;
        $data['srch_customer_id'] = $srch_customer_id;
        $data['srch_text'] = $srch_text;

        // Fetch active customers
        $data['customer_list'] = $this->db->get_where('customer_info', array('status' => 'Active'))->result_array();

        $this->load->model('Invoice_report_model');
        $raw_results = $this->Invoice_report_model->get_tender_progress($srch_from_date, $srch_to_date, $srch_customer_id, $srch_text);

        // Group items under POs
        $grouped = [];
        $item_counts = [];
        foreach ($raw_results as $row) {
            $po_id = $row['tender_po_id'];
            $item_code = $row['item_code'];
            $key = $po_id . '_' . $item_code;
            $item_counts[$key] = isset($item_counts[$key]) ? $item_counts[$key] + 1 : 1;
        }

        $allocated_totals = [];
        $remaining_vendor = [];
        $remaining_delivered = [];
        $remaining_invoiced = [];
        $item_indices = [];

        foreach ($raw_results as $row) {
            $po_id = $row['tender_po_id'];
            $item_code = $row['item_code'];

            if (!isset($grouped[$po_id])) {
                $grouped[$po_id] = [
                    'tender_po_id' => $row['tender_po_id'],
                    'our_po_no' => $row['our_po_no'],
                    'customer_po_no' => $row['customer_po_no'],
                    'po_date' => $row['po_date'],
                    'tender_enquiry_id' => $row['tender_enquiry_id'],
                    'tender_order_id' => $row['tender_order_id'],
                    'customer_name' => $row['customer_name'],
                    'enquiry_no' => $row['enquiry_no'],
                    'enquiry_date' => $row['enquiry_date'],
                    'tender_status' => $row['tender_status'],
                    'quotation_no' => $row['quotation_no'],
                    'quote_date' => $row['quote_date'],
                    'quotation_status' => $row['quotation_status'],
                    'items' => [],
                    'total_po_qty' => 0,
                    'total_vendor_po_qty' => 0,
                    'total_delivered_qty' => 0,
                    'total_invoiced_qty' => 0
                ];
            }

            $key = $po_id . '_' . $item_code;
            if (!isset($allocated_totals[$key])) {
                $allocated_totals[$key] = true;
                $remaining_vendor[$key] = floatval($row['vendor_po_qty']);
                $remaining_delivered[$key] = floatval($row['delivered_qty']);
                $remaining_invoiced[$key] = floatval($row['invoiced_qty']);
            }

            if (!isset($item_indices[$key])) {
                $item_indices[$key] = 0;
            }
            $item_indices[$key]++;
            $is_last_item = ($item_indices[$key] === $item_counts[$key]);

            $po_qty = floatval($row['po_qty']);

            // Allocate Vendor Qty
            $alloc_vendor = $is_last_item ? $remaining_vendor[$key] : min($po_qty, $remaining_vendor[$key]);
            if ($alloc_vendor < 0)
                $alloc_vendor = 0;
            $remaining_vendor[$key] -= $alloc_vendor;

            // Allocate Delivered Qty
            $alloc_delivered = $is_last_item ? $remaining_delivered[$key] : min($po_qty, $remaining_delivered[$key]);
            if ($alloc_delivered < 0)
                $alloc_delivered = 0;
            $remaining_delivered[$key] -= $alloc_delivered;

            // Allocate Invoiced Qty
            $alloc_invoiced = $is_last_item ? $remaining_invoiced[$key] : min($po_qty, $remaining_invoiced[$key]);
            if ($alloc_invoiced < 0)
                $alloc_invoiced = 0;
            $remaining_invoiced[$key] -= $alloc_invoiced;

            $grouped[$po_id]['items'][] = [
                'tender_po_item_id' => $row['tender_po_item_id'],
                'item_code' => $row['item_code'],
                'item_desc' => $row['item_desc'],
                'uom' => $row['uom'],
                'po_qty' => $po_qty,
                'vendor_po_qty' => $alloc_vendor,
                'delivered_qty' => $alloc_delivered,
                'invoiced_qty' => $alloc_invoiced
            ];

            $grouped[$po_id]['total_po_qty'] += $po_qty;
            $grouped[$po_id]['total_vendor_po_qty'] += $alloc_vendor;
            $grouped[$po_id]['total_delivered_qty'] += $alloc_delivered;
            $grouped[$po_id]['total_invoiced_qty'] += $alloc_invoiced;
        }

        $data['po_records'] = array_values($grouped);

        $this->load->view('page/reports/tender-progress-report', $data);
    }

    public function vat_statement_report()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data['title'] = 'VAT Statement Report';
        $data['js'] = 'reports/vat-statement-report.inc';

        $companies = $this->db->get_where('company_info', array('status' => 'Active'))->result_array();
        $data['company_list'] = $companies;

        // Date & Company Filter
        if ($this->input->post('mode') == 'Search') {
            $srch_from_date = $this->input->post('srch_from_date');
            $srch_to_date = $this->input->post('srch_to_date');
            $srch_company_id = $this->input->post('srch_company_id');
            $srch_customer_id = $this->input->post('srch_customer_id');
            $srch_text = $this->input->post('srch_text');
        } else {
            $srch_from_date = date('Y-m-01');
            $srch_to_date = date('Y-m-d');
            $srch_company_id = !empty($companies) ? $companies[0]['company_id'] : '';
            $srch_customer_id = '';
            $srch_text = '';
        }

        $data['srch_from_date'] = $srch_from_date;
        $data['srch_to_date'] = $srch_to_date;
        $data['srch_company_id'] = $srch_company_id;
        $data['srch_customer_id'] = $srch_customer_id;
        $data['srch_text'] = $srch_text;

        // Fetch selected company details
        $data['selected_company_name'] = 'AL HILLO TRADING CO W.L.L'; // Fallback
        $data['selected_company_vat'] = '200011371800002'; // Fallback
        if (!empty($srch_company_id)) {
            $comp = $this->db->get_where('company_info', array('company_id' => $srch_company_id))->row_array();
            if ($comp) {
                $data['selected_company_name'] = $comp['company_name'];
                $data['selected_company_vat'] = $comp['GST']; // GST field stores VAT No
            }
        }

        // Fetch active customers for filter
        $data['customer_list'] = $this->db->get_where('customer_info', array('status' => 'Active'))->result_array();

        $this->load->model('Invoice_report_model');

        // 1. Fetch Customer Invoices
        $invoices = $this->Invoice_report_model->get_customer_invoices_for_vat($srch_from_date, $srch_to_date, $srch_company_id, $srch_customer_id, $srch_text);

        // 2. Extract Enquiry IDs
        $enquiry_ids = [];
        foreach ($invoices as $inv) {
            if (!empty($inv['tender_enquiry_id'])) {
                $enquiry_ids[] = $inv['tender_enquiry_id'];
            }
        }
        $enquiry_ids = array_unique($enquiry_ids);

        // 3. Fetch related Vendor Bills
        $vendor_bills = [];
        if (!empty($enquiry_ids)) {
            $vendor_bills = $this->Invoice_report_model->get_vendor_bills_for_enquiries($enquiry_ids);
        }

        // Group vendor bills by tender_enquiry_id
        $grouped_bills = [];
        foreach ($vendor_bills as $bill) {
            $enq_id = $bill['tender_enquiry_id'];
            $grouped_bills[$enq_id][] = $bill;
        }

        // 4. Merge
        $merged_records = [];
        foreach ($invoices as $inv) {
            $enq_id = $inv['tender_enquiry_id'];
            $bills = isset($grouped_bills[$enq_id]) ? $grouped_bills[$enq_id] : [];

            $merged_records[] = [
                'invoice' => $inv,
                'bills' => $bills
            ];
        }

        $data['records'] = $merged_records;

        // Check Excel Export
        if ($this->input->get('export') == 'excel' || $this->input->post('export') == 'excel') {
            $this->load->view('page/reports/vat-statement-report-xls', $data);
        } else {
            $this->load->view('page/reports/vat-statement-report', $data);
        }
    }

    /**
     * Item Wise Inward & Outward Report (Based on Month)
     */
    public function item_inward_outward_report()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data['title'] = 'Item Wise Inward & Outward Report';
        $data['js'] = 'reports/item-inward-outward-report.inc';

        // 1. Month and Quick Navigation handling
        $srch_month = $this->input->post('srch_month');
        $month_action = $this->input->post('month_action');

        if (empty($srch_month)) {
            $srch_month = $this->session->userdata('srch_item_io_month') ?: date('Y-m');
        }

        if ($month_action === 'prev') {
            $srch_month = date('Y-m', strtotime($srch_month . '-01 -1 month'));
        } elseif ($month_action === 'next') {
            $srch_month = date('Y-m', strtotime($srch_month . '-01 +1 month'));
        } elseif ($month_action === 'current') {
            $srch_month = date('Y-m');
        }

        $this->session->set_userdata('srch_item_io_month', $srch_month);

        $start_date = $srch_month . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));

        $data['srch_month'] = $srch_month;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['month_label'] = date('F Y', strtotime($start_date));

        // 2. Filters
        $srch_keyword = trim($this->input->post('srch_keyword') ?? '');
        $srch_view_mode = $this->input->post('srch_view_mode') ?? 'monthly';
        $srch_movement = $this->input->post('srch_movement') ?? 'all'; // 'all', 'inward_only', 'outward_only'

        $data['srch_keyword'] = $srch_keyword;
        $data['srch_view_mode'] = $srch_view_mode;
        $data['srch_movement'] = $srch_movement;

        // Keyword LIKE filter for SQL
        $kw_inw = !empty($srch_keyword) ? " AND (ii.item_code LIKE '%" . $this->db->escape_like_str($srch_keyword) . "%' OR ii.item_desc LIKE '%" . $this->db->escape_like_str($srch_keyword) . "%')" : "";
        $kw_dc = !empty($srch_keyword) ? " AND (di.item_code LIKE '%" . $this->db->escape_like_str($srch_keyword) . "%' OR di.item_desc LIKE '%" . $this->db->escape_like_str($srch_keyword) . "%')" : "";

        // 3. Month Inward Quantity
        $sql_inw = "
            SELECT 
                TRIM(ii.item_code) AS item_code,
                MAX(ii.item_desc) AS item_desc,
                MAX(ii.uom) AS uom,
                SUM(ii.qty) AS month_inward_qty,
                SUM(ii.amount) AS month_inward_amt,
                COUNT(DISTINCT i.vendor_pur_inward_id) AS inward_tx_count
            FROM vendor_pur_inward_item_info ii
            JOIN vendor_pur_inward_info i ON ii.vendor_pur_inward_id = i.vendor_pur_inward_id
            WHERE i.status = 'Active' AND ii.status = 'Active'
              AND i.inward_date >= '$start_date' AND i.inward_date <= '$end_date'
              $kw_inw
            GROUP BY TRIM(ii.item_code)
        ";
        $inw_records = $this->db->query($sql_inw)->result_array();

        // 4. Month Outward Quantity (Delivery Challan)
        $sql_out = "
            SELECT 
                TRIM(di.item_code) AS item_code,
                MAX(di.item_desc) AS item_desc,
                MAX(di.uom) AS uom,
                SUM(di.qty) AS month_outward_qty,
                COUNT(DISTINCT d.tender_dc_id) AS outward_tx_count
            FROM tender_dc_item_info di
            JOIN tender_dc_info d ON di.tender_dc_id = d.tender_dc_id
            WHERE d.status = 'Active' AND di.status = 'Active'
              AND d.dc_date >= '$start_date' AND d.dc_date <= '$end_date'
              $kw_dc
            GROUP BY TRIM(di.item_code)
        ";
        $out_records = $this->db->query($sql_out)->result_array();

        // 5. Prior Inward Quantity (Prior to start_date)
        $sql_prior_inw = "
            SELECT 
                TRIM(ii.item_code) AS item_code,
                SUM(ii.qty) AS prior_inward_qty
            FROM vendor_pur_inward_item_info ii
            JOIN vendor_pur_inward_info i ON ii.vendor_pur_inward_id = i.vendor_pur_inward_id
            WHERE i.status = 'Active' AND ii.status = 'Active'
              AND i.inward_date < '$start_date'
            GROUP BY TRIM(ii.item_code)
        ";
        $prior_inw_records = $this->db->query($sql_prior_inw)->result_array();

        // 6. Prior Outward Quantity (Prior to start_date)
        $sql_prior_out = "
            SELECT 
                TRIM(di.item_code) AS item_code,
                SUM(di.qty) AS prior_outward_qty
            FROM tender_dc_item_info di
            JOIN tender_dc_info d ON di.tender_dc_id = d.tender_dc_id
            WHERE d.status = 'Active' AND di.status = 'Active'
              AND d.dc_date < '$start_date'
            GROUP BY TRIM(di.item_code)
        ";
        $prior_out_records = $this->db->query($sql_prior_out)->result_array();

        // 7. Direct Initial Stock (in_stock_item_info)
        $sql_instock = "
            SELECT 
                TRIM(stk.item_code) AS item_code,
                MAX(stk.item_desc) AS item_desc,
                MAX(stk.uom) AS uom,
                SUM(stk.qty) AS initial_stock_qty
            FROM in_stock_item_info stk
            WHERE stk.status = 'Active'
            GROUP BY TRIM(stk.item_code)
        ";
        $instock_records = $this->db->query($sql_instock)->result_array();

        // 8. Item Catalog (item_info)
        $sql_catalog = "
            SELECT 
                TRIM(item_code) AS item_code,
                item_name,
                item_description,
                uom
            FROM item_info
            WHERE status != 'Delete'
        ";
        $catalog_records = $this->db->query($sql_catalog)->result_array();

        // Assemble All Items
        $items_map = [];

        // Seed from Item Catalog
        foreach ($catalog_records as $cat) {
            $code = $cat['item_code'];
            if ($code === '')
                continue;
            $items_map[$code] = [
                'item_code' => $code,
                'item_desc' => !empty($cat['item_description']) ? $cat['item_description'] : $cat['item_name'],
                'uom' => $cat['uom'] ?: '',
                'opening_qty' => 0.0,
                'month_inward_qty' => 0.0,
                'month_inward_amt' => 0.0,
                'month_outward_qty' => 0.0,
                'inward_tx_count' => 0,
                'outward_tx_count' => 0
            ];
        }

        // Add In-Stock Items
        foreach ($instock_records as $stk) {
            $code = $stk['item_code'];
            if ($code === '')
                continue;
            if (!isset($items_map[$code])) {
                $items_map[$code] = [
                    'item_code' => $code,
                    'item_desc' => $stk['item_desc'],
                    'uom' => $stk['uom'] ?: '',
                    'opening_qty' => 0.0,
                    'month_inward_qty' => 0.0,
                    'month_inward_amt' => 0.0,
                    'month_outward_qty' => 0.0,
                    'inward_tx_count' => 0,
                    'outward_tx_count' => 0
                ];
            }
            $items_map[$code]['opening_qty'] += floatval($stk['initial_stock_qty']);
        }

        // Add Prior Inwards to Opening
        foreach ($prior_inw_records as $pinw) {
            $code = $pinw['item_code'];
            if ($code === '')
                continue;
            if (!isset($items_map[$code])) {
                $items_map[$code] = [
                    'item_code' => $code,
                    'item_desc' => '',
                    'uom' => '',
                    'opening_qty' => 0.0,
                    'month_inward_qty' => 0.0,
                    'month_inward_amt' => 0.0,
                    'month_outward_qty' => 0.0,
                    'inward_tx_count' => 0,
                    'outward_tx_count' => 0
                ];
            }
            $items_map[$code]['opening_qty'] += floatval($pinw['prior_inward_qty']);
        }

        // Deduct Prior Outwards from Opening
        foreach ($prior_out_records as $pout) {
            $code = $pout['item_code'];
            if ($code === '')
                continue;
            if (!isset($items_map[$code])) {
                $items_map[$code] = [
                    'item_code' => $code,
                    'item_desc' => '',
                    'uom' => '',
                    'opening_qty' => 0.0,
                    'month_inward_qty' => 0.0,
                    'month_inward_amt' => 0.0,
                    'month_outward_qty' => 0.0,
                    'inward_tx_count' => 0,
                    'outward_tx_count' => 0
                ];
            }
            $items_map[$code]['opening_qty'] -= floatval($pout['prior_outward_qty']);
        }

        // Add Month Inwards
        foreach ($inw_records as $inw) {
            $code = $inw['item_code'];
            if ($code === '')
                continue;
            if (!isset($items_map[$code])) {
                $items_map[$code] = [
                    'item_code' => $code,
                    'item_desc' => $inw['item_desc'],
                    'uom' => $inw['uom'] ?: '',
                    'opening_qty' => 0.0,
                    'month_inward_qty' => 0.0,
                    'month_inward_amt' => 0.0,
                    'month_outward_qty' => 0.0,
                    'inward_tx_count' => 0,
                    'outward_tx_count' => 0
                ];
            }
            if (empty($items_map[$code]['item_desc']) && !empty($inw['item_desc'])) {
                $items_map[$code]['item_desc'] = $inw['item_desc'];
            }
            if (empty($items_map[$code]['uom']) && !empty($inw['uom'])) {
                $items_map[$code]['uom'] = $inw['uom'];
            }
            $items_map[$code]['month_inward_qty'] += floatval($inw['month_inward_qty']);
            $items_map[$code]['month_inward_amt'] += floatval($inw['month_inward_amt']);
            $items_map[$code]['inward_tx_count'] += intval($inw['inward_tx_count']);
        }

        // Add Month Outwards
        foreach ($out_records as $out) {
            $code = $out['item_code'];
            if ($code === '')
                continue;
            if (!isset($items_map[$code])) {
                $items_map[$code] = [
                    'item_code' => $code,
                    'item_desc' => $out['item_desc'],
                    'uom' => $out['uom'] ?: '',
                    'opening_qty' => 0.0,
                    'month_inward_qty' => 0.0,
                    'month_inward_amt' => 0.0,
                    'month_outward_qty' => 0.0,
                    'inward_tx_count' => 0,
                    'outward_tx_count' => 0
                ];
            }
            if (empty($items_map[$code]['item_desc']) && !empty($out['item_desc'])) {
                $items_map[$code]['item_desc'] = $out['item_desc'];
            }
            if (empty($items_map[$code]['uom']) && !empty($out['uom'])) {
                $items_map[$code]['uom'] = $out['uom'];
            }
            $items_map[$code]['month_outward_qty'] += floatval($out['month_outward_qty']);
            $items_map[$code]['outward_tx_count'] += intval($out['outward_tx_count']);
        }

        // Final filtering and KPI calculation
        $filtered_records = [];
        $total_opening_qty = 0.0;
        $total_month_inward_qty = 0.0;
        $total_month_outward_qty = 0.0;
        $total_net_movement_qty = 0.0;
        $total_closing_qty = 0.0;
        $total_inward_tx = 0;
        $total_outward_tx = 0;

        foreach ($items_map as $code => $item) {
            // Keyword LIKE filter
            if (!empty($srch_keyword)) {
                $match_code = stripos($item['item_code'], $srch_keyword) !== false;
                $match_desc = stripos($item['item_desc'], $srch_keyword) !== false;
                if (!$match_code && !$match_desc) {
                    continue;
                }
            }

            $op = round($item['opening_qty'], 2);
            $in_q = round($item['month_inward_qty'], 2);
            $out_q = round($item['month_outward_qty'], 2);
            $net_q = round($in_q - $out_q, 2);
            $close_q = round($op + $in_q - $out_q, 2);

            // Month-wise filter: Inward and Outward movement based
            if ($srch_movement === 'inward_only') {
                if ($in_q <= 0)
                    continue;
            } elseif ($srch_movement === 'outward_only') {
                if ($out_q <= 0)
                    continue;
            } else {
                // Must have inward or outward movement in this selected month
                if ($in_q == 0 && $out_q == 0)
                    continue;
            }

            $row_data = [
                'item_code' => $item['item_code'],
                'item_desc' => $item['item_desc'] ?: '-',
                'uom' => $item['uom'] ?: '-',
                'opening_qty' => $op,
                'inward_qty' => $in_q,
                'outward_qty' => $out_q,
                'net_movement_qty' => $net_q,
                'closing_qty' => $close_q,
                'inward_tx_count' => $item['inward_tx_count'],
                'outward_tx_count' => $item['outward_tx_count']
            ];

            $filtered_records[] = $row_data;

            $total_opening_qty += $op;
            $total_month_inward_qty += $in_q;
            $total_month_outward_qty += $out_q;
            $total_net_movement_qty += $net_q;
            $total_closing_qty += $close_q;
            $total_inward_tx += $item['inward_tx_count'];
            $total_outward_tx += $item['outward_tx_count'];
        }

        // Sort records alphabetically by item_code
        usort($filtered_records, function ($a, $b) {
            return strnatcasecmp($a['item_code'], $b['item_code']);
        });

        $data['records'] = $filtered_records;
        $data['kpi'] = [
            'total_opening_qty' => $total_opening_qty,
            'total_inward_qty' => $total_month_inward_qty,
            'total_outward_qty' => $total_month_outward_qty,
            'total_net_movement_qty' => $total_net_movement_qty,
            'total_closing_qty' => $total_closing_qty,
            'total_items_count' => count($filtered_records),
            'total_inward_tx' => $total_inward_tx,
            'total_outward_tx' => $total_outward_tx
        ];

        // 9. Yearly Trend Mode Data (if requested)
        if ($srch_view_mode === 'yearly_trend') {
            $year = substr($srch_month, 0, 4);
            $year_start = $year . '-01-01';
            $year_end = $year . '-12-31';
            $data['trend_year'] = $year;

            // Inwards by month
            $trend_in_sql = "
                SELECT 
                    TRIM(ii.item_code) AS item_code,
                    DATE_FORMAT(i.inward_date, '%m') AS m_num,
                    SUM(ii.qty) AS qty
                FROM vendor_pur_inward_item_info ii
                JOIN vendor_pur_inward_info i ON ii.vendor_pur_inward_id = i.vendor_pur_inward_id
                WHERE i.status = 'Active' AND ii.status = 'Active'
                  AND i.inward_date >= '$year_start' AND i.inward_date <= '$year_end'
                GROUP BY TRIM(ii.item_code), m_num
            ";
            $trend_in_res = $this->db->query($trend_in_sql)->result_array();

            // Outwards by month
            $trend_out_sql = "
                SELECT 
                    TRIM(di.item_code) AS item_code,
                    DATE_FORMAT(d.dc_date, '%m') AS m_num,
                    SUM(di.qty) AS qty
                FROM tender_dc_item_info di
                JOIN tender_dc_info d ON di.tender_dc_id = d.tender_dc_id
                WHERE d.status = 'Active' AND di.status = 'Active'
                  AND d.dc_date >= '$year_start' AND d.dc_date <= '$year_end'
                GROUP BY TRIM(di.item_code), m_num
            ";
            $trend_out_res = $this->db->query($trend_out_sql)->result_array();

            $yearly_matrix = [];
            foreach ($filtered_records as $rec) {
                $c = $rec['item_code'];
                $yearly_matrix[$c] = [
                    'item_code' => $c,
                    'item_desc' => $rec['item_desc'],
                    'uom' => $rec['uom'],
                    'months' => array_fill(1, 12, ['in' => 0.0, 'out' => 0.0])
                ];
            }

            foreach ($trend_in_res as $tr) {
                $c = $tr['item_code'];
                $m = intval($tr['m_num']);
                if (isset($yearly_matrix[$c])) {
                    $yearly_matrix[$c]['months'][$m]['in'] += floatval($tr['qty']);
                }
            }

            foreach ($trend_out_res as $tr) {
                $c = $tr['item_code'];
                $m = intval($tr['m_num']);
                if (isset($yearly_matrix[$c])) {
                    $yearly_matrix[$c]['months'][$m]['out'] += floatval($tr['qty']);
                }
            }

            $data['yearly_matrix'] = $yearly_matrix;
        }

        // 10. Check Excel Export
        if ($this->input->get_post('export_excel') == '1' || $this->input->get('export') == 'excel') {
            $this->load->helper('download');
            $filename = "Item_Wise_Inward_Outward_Report_" . $srch_month . ".xls";
            $content = $this->load->view('page/reports/item-inward-outward-report-xls', $data, TRUE);
            force_download($filename, $content);
            return;
        }

        $this->load->view('page/reports/item-inward-outward-report', $data);
    }

    /**
     * AJAX: Get Item Inward & Outward Detailed Transactions for Modal
     */
    public function item_inward_outward_details_ajax()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            return;
        }

        $item_code = trim($this->input->post('item_code') ?? '');
        $month = trim($this->input->post('month') ?? date('Y-m'));

        if (empty($item_code)) {
            echo json_encode(['status' => false, 'message' => 'Item Code is required']);
            return;
        }

        $start_date = $month . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));

        // Inward Transactions
        $sql_inw = "
            SELECT 
                i.vendor_pur_inward_id,
                i.inward_no,
                i.inward_date,
                v.vendor_name,
                ii.qty,
                ii.rate,
                ii.amount,
                ii.uom
            FROM vendor_pur_inward_item_info ii
            JOIN vendor_pur_inward_info i ON ii.vendor_pur_inward_id = i.vendor_pur_inward_id
            LEFT JOIN vendor_info v ON i.vendor_id = v.vendor_id
            WHERE TRIM(ii.item_code) = ?
              AND i.status = 'Active' AND ii.status = 'Active'
              AND i.inward_date >= ? AND i.inward_date <= ?
            ORDER BY i.inward_date ASC, i.vendor_pur_inward_id ASC
        ";
        $inwards = $this->db->query($sql_inw, [$item_code, $start_date, $end_date])->result_array();

        // Outward Transactions (DC)
        $sql_out = "
            SELECT 
                d.tender_dc_id,
                d.dc_no,
                d.dc_date,
                c.customer_name,
                di.qty,
                di.uom
            FROM tender_dc_item_info di
            JOIN tender_dc_info d ON di.tender_dc_id = d.tender_dc_id
            LEFT JOIN customer_info c ON d.customer_id = c.customer_id
            WHERE TRIM(di.item_code) = ?
              AND d.status = 'Active' AND di.status = 'Active'
              AND d.dc_date >= ? AND d.dc_date <= ?
            ORDER BY d.dc_date ASC, d.tender_dc_id ASC
        ";
        $outwards = $this->db->query($sql_out, [$item_code, $start_date, $end_date])->result_array();

        echo json_encode([
            'status' => true,
            'item_code' => $item_code,
            'month' => date('F Y', strtotime($start_date)),
            'inwards' => $inwards,
            'outwards' => $outwards
        ]);
    }


    public function dp_custom_invoice_report($action = '')
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data['title'] = 'DP & Customs Invoice Report';
        $data['js'] = 'reports/dp-custom-invoice-report.inc';
        $data['s_url'] = 'dp-custom-invoice-report';

        // Check for Reset request
        if ($action === 'reset' || $this->input->post('reset') == '1' || $this->input->get('reset') == '1') {
            $this->session->unset_userdata('sir_from_date');
            $this->session->unset_userdata('sir_to_date');
            $this->session->unset_userdata('sir_vendor_id');
            $this->session->unset_userdata('sir_bill_type');
            redirect('supplier-invoice-report');
            return;
        }

        // Process POST submission
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $srch_from_date = $this->input->post('srch_from_date') ?? '';
            $srch_to_date = $this->input->post('srch_to_date') ?? '';
            $srch_vendor_id = $this->input->post('srch_vendor_id') ?? '';
            $srch_bill_type = $this->input->post('srch_bill_type') ?? '';

            if ($this->input->post('export_excel') != '1') {
                $this->session->set_userdata('sir_from_date', $srch_from_date);
                $this->session->set_userdata('sir_to_date', $srch_to_date);
                $this->session->set_userdata('sir_vendor_id', $srch_vendor_id);
                $this->session->set_userdata('sir_bill_type', $srch_bill_type);
            }
        } else {
            // GET request
            if ($this->input->get('srch_from_date') !== null || $this->input->get('srch_to_date') !== null || $this->input->get('srch_vendor_id') !== null || $this->input->get('srch_bill_type') !== null) {
                $srch_from_date = $this->input->get('srch_from_date') ?? '';
                $srch_to_date = $this->input->get('srch_to_date') ?? '';
                $srch_vendor_id = $this->input->get('srch_vendor_id') ?? '';
                $srch_bill_type = $this->input->get('srch_bill_type') ?? '';
            } else {
                $srch_from_date = $this->session->userdata('sir_from_date');
                $srch_to_date = $this->session->userdata('sir_to_date');
                $srch_vendor_id = $this->session->userdata('sir_vendor_id') ?? '';
                $srch_bill_type = $this->session->userdata('sir_bill_type') ?? '';

                // Default to 1st of current month and today
                if ($srch_from_date === null) {
                    $srch_from_date = date('Y-m-01');
                }
                if ($srch_to_date === null) {
                    $srch_to_date = date('Y-m-d');
                }
            }
        }

        $data['srch_from_date'] = $srch_from_date;
        $data['srch_to_date'] = $srch_to_date;
        $data['srch_vendor_id'] = $srch_vendor_id;
        $data['srch_bill_type'] = $srch_bill_type;

        // Fetch active vendors/suppliers for dropdown
        $sql = "
            SELECT vendor_id, vendor_name 
            FROM vendor_info 
            WHERE status = 'Active' 
            ORDER BY vendor_name ASC";
        $data['vendor_list'] = $this->db->query($sql)->result_array();

        // Bill Type Options
        $data['bill_type_opt'] = [
            '' => 'All Bill Types',
            'Delivery Partner Bill' => 'Delivery Partner Bill',
            'Customs Bill' => 'Customs Bill'
        ];

        // Selected vendor name for display
        $selected_vendor_name = 'All Suppliers';
        if (!empty($srch_vendor_id)) {
            foreach ($data['vendor_list'] as $v) {
                if ($v['vendor_id'] == $srch_vendor_id) {
                    $selected_vendor_name = $v['vendor_name'];
                    break;
                }
            }
        }
        $data['selected_vendor_name'] = $selected_vendor_name;

        // Selected bill type label
        $selected_bill_type_label = !empty($srch_bill_type) && isset($data['bill_type_opt'][$srch_bill_type])
            ? $data['bill_type_opt'][$srch_bill_type]
            : 'All Bill Types';
        $data['selected_bill_type_label'] = $selected_bill_type_label;

        // Build outer WHERE conditions
        $where_clauses = ["1=1"];
        $params = [];

        if (!empty($srch_from_date) && !empty($srch_to_date)) {
            $where_clauses[] = "all_bills.invoice_date BETWEEN ? AND ?";
            $params[] = $srch_from_date;
            $params[] = $srch_to_date;
        } elseif (!empty($srch_from_date)) {
            $where_clauses[] = "all_bills.invoice_date >= ?";
            $params[] = $srch_from_date;
        } elseif (!empty($srch_to_date)) {
            $where_clauses[] = "all_bills.invoice_date <= ?";
            $params[] = $srch_to_date;
        }

        if (!empty($srch_vendor_id)) {
            $where_clauses[] = "all_bills.vendor_id = ?";
            $params[] = $srch_vendor_id;
        }

        if (!empty($srch_bill_type)) {
            $where_clauses[] = "all_bills.bill_type = ?";
            $params[] = $srch_bill_type;
        }

        $where_sql = implode(' AND ', $where_clauses);

        $sql = "
            SELECT * FROM (
              
                SELECT
                    a.dp_bill_id AS bill_id,
                    a.invoice_date,
                    a.invoice_no,
                    a.vendor_id,
                    v.vendor_name,
                    COALESCE(v.gst, v.crno) AS vendor_vat_cr,
                    a.tender_enquiry_id,
                    get_tender_info(a.tender_enquiry_id) AS tender_details, 
                    3 AS decimal_point,
                    IFNULL(a.dp_charges, 0) AS amt_wo_vat,
                    IFNULL((a.dp_charges + a.dp_vat_amt), 0) AS payable,
                    IFNULL(a.dp_vat_amt, 0) AS vat_amt,
                    a.g_total AS grand_amount,
                    'Delivery Partner Bill' AS bill_type 
                FROM dp_bill_info a
                LEFT JOIN vendor_info v ON a.vendor_id = v.vendor_id AND v.status = 'Active'
                WHERE a.status = 'Active'

                UNION ALL 
                SELECT
                    a.customs_bill_id AS bill_id,
                    COALESCE(NULLIF(a.declaration_date, '0000-00-00'), a.invoice_date) AS invoice_date,
                    COALESCE(NULLIF(a.declaration_no, ''), a.invoice_no) AS invoice_no,
                    a.vendor_id,
                    v.vendor_name,
                    COALESCE(v.gst, v.crno) AS vendor_vat_cr,
                    a.tender_enquiry_id,
                    get_tender_info(a.tender_enquiry_id) AS tender_details, 
                    3 AS decimal_point,
                    IFNULL((a.custom_stamp_fee + a.custom_duty), 0) AS amt_wo_vat,
                    IFNULL(a.customs_payable, 0) AS payable,
                    IFNULL(a.vat_amt, 0) AS vat_amt,
                    a.customs_tot_amt AS grand_amount,
                    'Customs Bill' AS bill_type 
                FROM customs_bill_info a
                LEFT JOIN vendor_info v ON a.vendor_id = v.vendor_id AND v.status = 'Active'
                WHERE a.status = 'Active'
            ) AS all_bills
            WHERE {$where_sql}
            ORDER BY all_bills.invoice_date ASC, all_bills.invoice_no ASC, all_bills.bill_id ASC
        ";

        $records = $this->db->query($sql, $params)->result_array();
        $data['records'] = $records;

        // KPI metrics
        $total_bills = count($records);
        $total_taxable = 0;
        $total_vat = 0;
        $total_payable = 0;
        $grand_total = 0;

        foreach ($records as $row) {
            $total_taxable += (float) $row['amt_wo_vat'];
            $total_vat += (float) $row['vat_amt'];
            $total_payable += (float) $row['payable'];
            $grand_total += (float) $row['grand_amount'];
        }

        $data['total_bills'] = $total_bills;
        $data['total_taxable'] = $total_taxable;
        $data['total_vat'] = $total_vat;
        $data['total_payable'] = $total_payable;
        $data['grand_total'] = $grand_total;

        // Excel Export
        if ($this->input->post('export_excel') == '1' || $this->input->get('export') == 'excel') {
            $this->load->helper('download');
            $filename = "DP_Custom_Invoice_Report_" . ($srch_from_date ? $srch_from_date : 'all') . "_to_" . ($srch_to_date ? $srch_to_date : 'all') . ".xls";
            $content = $this->load->view('page/reports/dp-custom-invoice-report-xls', $data, TRUE);
            force_download($filename, $content);
            return;
        }

        $this->load->view('page/reports/dp-custom-invoice-report', $data);
    }
}