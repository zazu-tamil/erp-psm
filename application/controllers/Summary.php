<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Summary extends CI_Controller
{


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


        // $data['tender_quotation_list'] = array();

        // if (!empty($srch_enquiry_no_id)) {
        //       
        //     $sql = "
        //         select
        //             *
        //         from  vendor_rate_enquiry_info  as a 
        //         left join vendor_rate_enquiry_item_info as b on a.vendor_rate_enquiry_id = b.vendor_rate_enquiry_id and a.`status`='Active'
        //          left join company_info as c on a.company_id = c.company_id and c.`status`='Active'  
        //         left join customer_info as d on a.customer_id = d.customer_id and d.`status`='Active'
        //         where a.`status`='Active'
        //         and a.tender_enquiry_id ='" . $this->db->escape_str($srch_enquiry_no_id) . "'
        //         group by a.vendor_rate_enquiry_id
        //         order by a.vendor_rate_enquiry_id asc, a.tender_enquiry_id asc;
        //     ";
        //     $query = $this->db->query($sql);
        //     $data['vendor_rate_enquiry_list'] = $query->result_array();


        //     $sql = "
        //         select
        //             *
        //         from  vendor_quotation_info as a 
        //         left join vendor_quote_item_info as b on a.vendor_quote_id = b.vendor_quote_id and a.`status`='Active'
        //          left join company_info as c on a.company_id = c.company_id and c.`status`='Active'  
        //         left join customer_info as d on a.customer_id = d.customer_id and d.`status`='Active'
        //         where a.`status`='Active'
        //         and a.tender_enquiry_id ='" . $this->db->escape_str($srch_enquiry_no_id) . "'
        //         group by a.vendor_quote_id
        //         order by a.vendor_quote_id asc, a.tender_enquiry_id asc;
        //     ";
        //     $query = $this->db->query($sql);
        //     $data['vendor_quotation_list'] = $query->result_array();

        //     $sql = "
        //         select
        //             *
        //         from vendor_po_info as a 
        //         left join vendor_po_item_info as b on a.vendor_po_id = b.vendor_po_id  and a.`status`='Active'
        //          left join company_info as c on a.company_id = c.company_id and c.`status`='Active'  
        //         left join customer_info as d on a.customer_id = d.customer_id and d.`status`='Active'
        //         where a.`status`='Active'
        //         and a.tender_enquiry_id ='" . $this->db->escape_str($srch_enquiry_no_id) . "'
        //         group by a.vendor_po_id
        //         order by a.vendor_po_id asc, a.tender_enquiry_id asc;
        //     ";
        //     $query = $this->db->query($sql);
        //     $data['vendor_po_list'] = $query->result_array();


        //     $sql = "
        //         select
        //             *
        //         from  vendor_pur_inward_info as a 
        //         left join vendor_pur_inward_item_info as b on a.vendor_pur_inward_id = b.vendor_pur_inward_id  and a.`status`='Active'
        //          left join company_info as c on a.company_id = c.company_id and c.`status`='Active'  
        //         left join customer_info as d on a.customer_id = d.customer_id and d.`status`='Active'
        //         where a.`status`='Active'
        //         and a.tender_enquiry_id ='" . $this->db->escape_str($srch_enquiry_no_id) . "'
        //         group by a.vendor_pur_inward_id
        //         order by a.vendor_pur_inward_id asc, a.tender_enquiry_id asc;
        //     ";
        //     $query = $this->db->query($sql);
        //     $data['vendor_pur_inward_list'] = $query->result_array();



        //     $sql = "
        //         select
        //         *
        //         from vendor_purchase_invoice_info  as a 
        //         left join vendor_purchase_invoice_item_info as b on a.vendor_purchase_invoice_id = b.vendor_purchase_invoice_id  and a.`status`='Active'
        //          left join company_info as c on a.company_id = c.company_id and c.`status`='Active'  
        //         left join customer_info as d on a.customer_id = d.customer_id and d.`status`='Active'
        //         where a.`status`='Active'
        //         and a.tender_enquiry_id ='" . $this->db->escape_str($srch_enquiry_no_id) . "'
        //         group by a.vendor_purchase_invoice_id
        //         order by a.vendor_purchase_invoice_id asc, a.tender_enquiry_id asc;
        //     ";
        //     $query = $this->db->query($sql);
        //     $data['vendor_purchase_invoice_list'] = $query->result_array();
        // }

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

        if (!empty($srch_enquiry_no_id)) {
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
                WHERE a.status='Active' AND a.tender_enquiry_id = '" . $this->db->escape_str($srch_enquiry_no_id) . "'
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
                AND a.tender_enquiry_id = '" . $this->db->escape_str($srch_enquiry_no_id) . "'

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
                AND a.tender_enquiry_id = '" . $this->db->escape_str($srch_enquiry_no_id) . "'

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
                AND a.tender_enquiry_id = '" . $this->db->escape_str($srch_enquiry_no_id) . "'

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
                AND a.tender_enquiry_id = '" . $this->db->escape_str($srch_enquiry_no_id) . "'

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
                AND a.tender_enquiry_id = '" . $this->db->escape_str($srch_enquiry_no_id) . "'

                ORDER BY a.vendor_rate_enquiry_id ASC, b.vendor_rate_enquiry_item_id ASC
            ";

            $query = $this->db->query($sql);

            // GROUP DATA BY INVOICE
            foreach ($query->result_array() as $row) {
                $data['vendor_rate_enquiry_list'][$row['vendor_rate_enquiry_id']]['info'] = $row;
                $data['vendor_rate_enquiry_list'][$row['vendor_rate_enquiry_id']]['items'][] = $row;
            }
        }

        $this->load->view('page/summary/tender-enquiry-summary-report', $data);
    }



    public function get_data()
    {
        $table = $this->input->post('tbl');
        $rec_id = $this->input->post('id');
        $rec_list = array();


        if ($table == 'get-company-customer-list') {
            $query = $this->db->query("
                SELECT c.customer_id, c.customer_name
                FROM project_info p
                LEFT JOIN customer_info c ON c.customer_id = p.customer_id
                WHERE p.company_id = ? AND p.status = 'Active' AND c.status = 'Active'
                GROUP BY c.customer_id ORDER BY c.customer_name
            ", [$rec_id]);
            $rec_list = $query->result_array();
        }

        if ($table == 'get-category-item-list') {
            $query = $this->db->query("
                SELECT item_id, item_name
                FROM item_info
                WHERE category_id = ? AND status = 'Active'
                ORDER BY item_name
            ", [$rec_id]);
            $rec_list = $query->result_array();
        }


        header('Content-Type: application/json');
        echo json_encode($rec_list);
    }


    public function ajax_add_master_inline()
    {
        if ($this->input->post('mode') == 'Add Customer Contact') {

            $data = [
                'customer_id' => $this->input->post('customer_id'),
                'contact_person_name' => $this->input->post('contact_person_name'),
                'mobile' => $this->input->post('mobile'),
                'email' => $this->input->post('email'),
                'address' => $this->input->post('address'),
                'department' => $this->input->post('department'),
                'designation' => $this->input->post('designation'),
                'status' => $this->input->post('status'),
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s'),
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('customer_contact_info', $data);
            $insert_id = $this->db->insert_id();

            // Return response for JS
            echo json_encode([
                'status' => 'success',
                'message' => 'Customer contact added successfully!',
                'id' => $insert_id,
                'name' => $data['contact_person_name']
            ]);
        }

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
                'value' => $row->tender_enquiry_id,        // filled in textbox
                'company_id' => $row->company_id,
                'customer_id' => $row->customer_id,
                'tender_enquiry_id' => $row->tender_enquiry_id,
                'customer_name' => $row->customer_name,
                'enquiry_no' => $row->enquiry_no
            ];
        }
        echo json_encode($result);

    }



    public function get_content($table = '', $rec_id = '')
    {
        //if(!$this->session->userdata('zazu_logged_in'))  redirect();

        if (empty($table) && empty($rec_id)) {
            $table = $this->input->post('tbl');
            $rec_id = $this->input->post('id');
            $edit_mode = $this->input->post('edit_mode');
            $del_mode = $this->input->post('del_mode');
            $flg = true;
        } else {
            $flg = false;
        }


        if ($table == 'get_tender_enquiry_timeline') {

            $heading = $this->input->post('modal_label');

            if ($heading === 'Customer Quotation') {

                $query = $this->db->query("
                    SELECT 
                        date_format(b.quote_date, '%d-%m-%Y') as quote_date,
                        b.tender_ref_no,
                        b.quotation_no, 
                        b.quotation_status,
                        FORMAT(b.other_charges, 3)     AS other_charges,
                        FORMAT(b.transport_charges, 3) AS transport_charges
                    FROM tender_enquiry_info AS a
                    LEFT JOIN tender_quotation_info AS b 
                        ON a.tender_enquiry_id = b.tender_enquiry_id 
                        AND b.status = 'Active'
                    WHERE a.status = 'Active'
                    AND a.tender_enquiry_id = '" . $rec_id . "'
                    ORDER BY b.quote_date ASC
                ");

            } elseif ($heading === 'Customer PO') {
                $query = $this->db->query("
                   select 
                     date_format(b.po_date, '%d-%m-%Y') as po_date,
                    a.enquiry_no,
                    b.our_po_no,
                    b.customer_po_no,
                    b.po_status
                    from tender_enquiry_info as a 
                    left join customer_tender_po_info as b on a.tender_enquiry_id = b.tender_enquiry_id
                    where a.`status`='Active'
                    and a.tender_enquiry_id = '" . $rec_id . "'
                    order by b.po_date asc
                ");

            } elseif ($heading === 'Customer DC') {

                $query = $this->db->query("
                   select 
                    date_format(b.dc_date, '%d-%m-%Y') as dc_date,
                    a.enquiry_no,
                    b.dc_no,
                    'Delivered' as dc_status
                    from tender_enquiry_info as a 
                    left join tender_dc_info as b on  a.tender_enquiry_id = b.tender_enquiry_id and b.`status`='Active'
                    where a.`status`='Active'
                    and a.tender_enquiry_id = '" . $rec_id . "'
                    order by b.dc_date asc
                ");

            } elseif ($heading === 'Customer Enquiry') {

                $query = $this->db->query("
                    select 
                    date_format(a.enquiry_date, '%d-%m-%Y') as enquiry_date,                    
                    a.enquiry_no, 
                    a.tender_name,
                    DATE_FORMAT(a.closing_date, '%d-%m-%Y %h:%i %p') AS closing_date, 
                    a.tender_status
                    from tender_enquiry_info as a 
                    where a.`status`='Active'
                    and a.tender_enquiry_id = '" . $rec_id . "'
                    order by a.enquiry_date asc
                ");

            } elseif ($heading === 'Customer Invoice') {

                $query = $this->db->query("
                   select
                    DATE_FORMAT(b.invoice_date, '%d-%m-%Y') AS invoice_date,
                    b.invoice_no,
                    b.invoice_status,
                    FORMAT(b.tax_amount,3) as tax_amount,
                    FORMAT(b.total_amount,3) as total_amount
                    
                    from tender_enquiry_info as a 
                    left join tender_enq_invoice_info as b on a.tender_enquiry_id =b.tender_enquiry_id and b.`status`='Active'
                    where a.`status`='Active'
                    and a.tender_enquiry_id = '" . $rec_id . "'
                    order by b.invoice_date asc
                ");

            } else {
                $rec_list = [];
                return;
            }

            $rec_list = $query->result_array();
        }



        if (!empty($rec_list)) {

            if (count($rec_list) > 1) {

                $content = '
           <table class="table table-bordered table-responsive table-striped" id="sts">
             <thead>
                <tr>';
                $content .= '<th>S.No</th>';
                foreach ($rec_list[0] as $fld => $val) {
                    if ($fld != 'id' && $fld != 'tbl')
                        $content .= '<th class="text-capitalize">' . str_replace('_', ' ', $fld) . '</th>';
                }
                if ($edit_mode == 1)
                    $content .= '<th>Edit</th>';
                if ($del_mode == 1)
                    $content .= '<th>Delete</th>';
                $content .= '</tr>
              </thead>  
              <tbody>';
                foreach ($rec_list as $k => $info) {
                    $content .= '<tr>
                      <td>' . ($k + 1) . '</td>';
                    foreach ($rec_list[0] as $fld => $val) {
                        if ($fld != 'id') {
                            if ($fld != 'tbl')
                                $content .= '<td>' . $info[$fld] . '</td>';
                        }

                    }
                    if ($edit_mode == 1)
                        $content .= '<td><button type="button" class="btn btn-xs btn-info btn-sm btn_chld_edit" value="' . $info['id'] . '" data="' . $table . '"><i class="fa fa-edit"></i></button></td>';
                    if ($del_mode == 1)
                        $content .= '<td><button type="button" class="btn btn-xs btn-danger btn-sm btn_chld_del" value="' . $info['id'] . '" data="' . $table . '"><i class="fa fa-remove"></i></button></td>';
                    $content .= '</tr>';
                }
                $content .= '
              </tbody>  
            </table>';
            } else {
                $content = ' <table class="table table-bordered table-responsive table-striped">  ';
                $content .= '<tr><th colspan="2">' . strtoupper(str_replace('_', ' ', $table)) . '</th></tr>';
                foreach ($rec_list[0] as $fld => $val) {
                    if ($fld != 'id' && $fld != 'tbl') {
                        $content .= '<tr>';
                        $content .= '<th>' . strtoupper(str_replace('_', ' ', $fld)) . '</th>';
                        $content .= '<td>' . $val . '</td>';
                        $content .= '</tr>';
                    }
                }
                if ($edit_mode == 1)
                    $content .= '<tr><th>Edit</th><td><button type="button" class="btn btn-xs btn-info btn-sm btn_chld_edit" value="' . $rec_list[0]['id'] . '" data="' . $table . '"><i class="fa fa-edit"></i></button></td></tr>';
                if ($del_mode == 1)
                    $content .= '<tr><th>Delete</th><td><button type="button" class="btn btn-xs btn-danger btn-sm btn_chld_del" value="' . $rec_list[0]['id'] . '" data="' . $table . '"><i class="fa fa-remove"></i></button></td></tr>';

                $content .= '</table>';
            }
        } else {
            $content = "<strong>No Record Found</strong>";
        }

        if (!$flg)
            return $content;
        else
            echo $content;
    }




}