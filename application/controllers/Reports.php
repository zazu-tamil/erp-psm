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


        if (isset($_POST['srch_from_date']))  {
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
        $query  = $this->db->query($sql); 
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


        if (isset($_POST['srch_from_date']))  {
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
        $query  = $this->db->query($sql); 
        $rec = $query->result_array(); 

        $grouped = [];

        foreach ($rec as $row) {
            $s_order = $row['s_order'];   // group key
            $grouped[$s_order][] = $row;  // push row inside that s_order
        }

        $data['record_list'] = $grouped;



        $this->load->view('page/reports/purchase-nbr-report', $data);
    }

}

?>