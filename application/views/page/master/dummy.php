 public function ledger_transactions_report()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data['title'] = 'Ledger Transactions Report';
        $data['js'] = 'audit/ledger-report.inc'; // ← your custom js if any

        // ── Date range handling ────────────────────────────────────────
        if ($this->input->post('srch_from_date')) {
            $srch_from_date = $this->input->post('srch_from_date');
            $srch_to_date   = $this->input->post('srch_to_date') ?: date('Y-m-d');
            $this->session->set_userdata([
                'srch_from_date' => $srch_from_date,
                'srch_to_date'   => $srch_to_date
            ]);
        } else {
            $srch_from_date = $this->session->userdata('srch_from_date') ?: date('Y-m-d');
            $srch_to_date   = $this->session->userdata('srch_to_date')   ?: date('Y-m-d');
        }

        $data['srch_from_date'] = $srch_from_date;
        $data['srch_to_date']   = $srch_to_date;

        // ── Ledger selection ───────────────────────────────────────────
        $ledger_id = null;
        if ($this->input->post('srch_ledger_id') !== null && $this->input->post('srch_ledger_id') !== '') {
            $ledger_id = $this->input->post('srch_ledger_id');
            $this->session->set_userdata('srch_ledger_id', $ledger_id);
        } elseif ($this->session->userdata('srch_ledger_id')) {
            $ledger_id = $this->session->userdata('srch_ledger_id');
        }

        $data['srch_ledger_id'] = $ledger_id;

        // Default values
        $data['ledger_rows']     = [];
        $data['opening_balance'] = 0;
        $data['closing_balance'] = 0;
        $data['ledger_name']     = '';

        if ($ledger_id) {
            $sql = "
                SELECT 
                    NULL AS voucher_date,
                    NULL AS voucher_id,
                    'Opening Balance' AS voucher_type,
                    NULL AS debit,
                    NULL AS credit,
                    la.ledger_name,
                    (
                        CASE WHEN la.opening_type = 'Debit' THEN la.opening_balance ELSE -la.opening_balance END
                        + COALESCE(SUM(ve.debit - ve.credit), 0)
                    ) AS balance
                FROM ledger_accounts la
                LEFT JOIN voucher_entries ve ON la.ledger_id = ve.ledger_id
                LEFT JOIN vouchers v ON v.voucher_id = ve.voucher_id 
                    AND v.voucher_date < ?
                    AND v.status = 'Active'
                    AND ve.status = 'Active'
                WHERE la.ledger_id = ?
                GROUP BY la.ledger_id

                UNION ALL

                SELECT 
                    v.voucher_date,
                    v.voucher_id,
                    v.voucher_type,
                    ve.debit,
                    ve.credit,
                    la.ledger_name,
                    NULL AS balance  -- to be calculated in PHP
                FROM voucher_entries ve
                JOIN vouchers v ON v.voucher_id = ve.voucher_id
                JOIN ledger_accounts la ON la.ledger_id = ve.ledger_id
                WHERE ve.ledger_id = ?
                  AND v.voucher_date BETWEEN ? AND ?
                  AND v.status = 'Active'
                  AND ve.status = 'Active'

                ORDER BY voucher_date, voucher_id
            ";

            $params = [
                $srch_from_date,    // for opening balance
                $ledger_id,
                $ledger_id,
                $srch_from_date,
                $srch_to_date
            ];

            $rows = $this->db->query($sql, $params)->result();

            // Calculate running balance
            $running = 0;
            foreach ($rows as &$row) {  // Note: & for reference
                if ($row->voucher_type === 'Opening Balance') {
                    $running = (float) $row->balance;
                    $data['opening_balance'] = $running;
                    $data['ledger_name'] = $row->ledger_name;
                } else {
                    $running += (float) $row->debit - (float) $row->credit;
                    $row->balance = $running;
                }
            }
            unset($row); // good practice

            $data['closing_balance'] = $running;
            $data['ledger_rows'] = $rows;
        }

        // Ledger dropdown options
        $this->db->where('status', 'Active');
        $this->db->order_by('ledger_name', 'ASC');
        $ledgers = $this->db->get('ledger_accounts')->result();

        $data['ledger_opt'] = ['' => '-- Select Ledger --'];
        foreach ($ledgers as $l) {
            $data['ledger_opt'][$l->ledger_id] = $l->ledger_name;
        }

        $this->load->view('page/audit/ledger-transactions-report', $data);
    }
 