<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Gb_client extends ClientsController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('saas_model');
    }

    /**
     * @throws Exception
     */
    public function assignPackage($company_id = null)
    {
        $data['title'] = _l('assign_package');
        isClientLogin($company_id);
        $data['current_package'] = get_company_subscription_by_id()->package_id;
        $data['all_packages'] = get_old_result('tbl_saas_packages', array('status' => 'published'));
        $this->set_layout($data, 'packages/assign_package');
    }

    /**
     * @throws Exception
     */
    public
    function checkoutPayment($package_id = null, $company_id = null)
    {

        $data['package_id'] = $package_id;
        $data['frequency'] = 'monthly';
        if (empty($data['package_id']) && !empty(subdomain())) {
            $subs_info = get_company_subscription(null, 'running');
            $data['package_id'] = $subs_info->package_id;
            $data['frequency'] = $subs_info->frequency;
        }
        $package_info = get_old_result('tbl_saas_packages', array('id' => $data['package_id']), false);
        $data['title'] = _l('checkout') . ' ' . _l('payment') . ' ' . _l('for') . ' ' . $package_info->name;
        $data['package_info'] = $package_info;
        $data['all_packages'] = get_old_result('tbl_saas_packages', array('status' => 'published'));
        $subview = 'checkoutPayment';
        if (!empty(subdomain())) {
            $front_end = true;
            $data['subs_info'] = get_company_subscription();
            $data['payment_modes'] = $this->saas_model->get_payment_modes();
            $subview = 'checkoutPaymentOpen';
        } else if (!empty($company_id)) {
            $company_id = url_decode($company_id);
            $data['company_info'] = $this->saas_model->company_info($company_id);
            $data['payment_modes'] = $this->saas_model->get_payment_modes();
            $subview = 'checkoutPaymentOpen';
        }
        $this->set_layout($data, 'saas/packages/' . $subview);
    }

    public function billings()
    {
        // check if not logged
        if (!is_client_logged_in()) {
            redirect('authentication/login');
        }
        $data['title'] = _l('billing');
        $this->set_layout($data, 'companies/billing');
    }

    public function referrals()
    {
        // check if not logged
        if (!is_client_logged_in()) {
            redirect('authentication/login');
        }
        // check if affiliate is enabled
        if (!get_option('enable_affiliate')) {
            redirect('clients/dashboard');
        }
        $id = $this->saas_model->get_affiliate_user_id();
        $data['affiliate_info'] = $data['user'] = $this->saas_model->getAffiliateUser($id);
        $data['states'] = $this->saas_model->get_affiliate_states($data['affiliate_info']);
        $data['commission_histories'] = get_order_by('tbl_saas_affiliates', array('referral_by' => $id), 'affiliate_id', null, 5);
        $data['payout_histories'] = get_order_by('tbl_saas_affiliate_payouts', array('user_id' => $id), 'affiliate_payout_id', null, 5);
        $data['subview'] = $this->load->view('affiliates/user/dashboard', $data, true);
        $data['title'] = _l('referrals');
        $data['payouts'] = true;
        $this->set_layout($data, 'companies/referrals');
    }


    public
    function upgrade()
    {
        $data['title'] = _l('upgrade') . ' ' . _l('plan');
        if (!empty($type)) {
            $data['type'] = $type;
        }
        $data['payment_modes'] = $this->saas_model->get_payment_modes();
        $data['sub_info'] = get_company_subscription_by_id();
        $this->set_layout($data, 'settings/upgrade');
    }

    public
    function companyHistoryList($id = null)
    {
        // make datatable
        $this->db = config_db(null, true);
        $this->load->model('datatables');
        $this->datatables->table = 'tbl_saas_companies_history';
        $column = array('package_name', 'amount', 'frequency', 'created_at', 'validity', 'payment_method', 'status');
        $this->datatables->column_order = $column;
        $this->datatables->column_search = $column;
        $this->datatables->order = array('id' => 'desc');
        if ($id) {
            $where = array('tbl_saas_companies_history.companies_id' => $id);
        } else {
            $where = array();
        }
        $fetch_data = make_datatables($where, null, true);
        $data = array();
        $access = super_admin_access();
        foreach ($fetch_data as $_key => $v_history) {
            if ($v_history->active == 1) {
                $label = 'success';
                $status = 'active';
            } else {
                $label = 'warning';
                $status = 'inactive';
            }
            if ($v_history->frequency == 'monthly') {
                $frequency = _l('mo');
            } else if ($v_history->frequency == 'lifetime') {
                $frequency = _l('lt');
            } else if ($v_history->frequency == 'yearly') {
                $frequency = _l('yr');
            }
            $action = null;
            $sub_array = array();
            $name = '<a href="' . base_url('subs_package_details/' . $v_history->id . '/1') . '"  data-toggle="modal" data-target="#myModal" >' . $v_history->package_name . '</a>';
            if (!empty($access)) {
                $name .= '<div class="row-options">';
                if (!empty($access) && $v_history->active != 1) {
                    $name .= '<a 
                    data-toggle="tooltip" data-placement="top"
                    href="' . base_url('saas/gb/delete_companies_history/' . $v_history->id) . '"  title="' . _l('delete') . '" class="text-danger _delete">' . _l('delete') . '</a>';
                }
                $name .= '</div>';
            }
            $sub_array[] = $name;
            $sub_array[] = display_money($v_history->amount, default_currency()) . ' /' . $frequency;
            $sub_array[] = _dt($v_history->created_at);
            $sub_array[] = (!empty($v_history->validity) ? $v_history->validity : '-');
            $sub_array[] = $v_history->payment_method;
            if (!empty($access)) {
                $sub_array[] = '<span class="label label-' . $label . '">' . _l($status) . '</span>';
            }
            $data[] = $sub_array;
        }

        render_table_old($data, $where);
    }


    public
    function companyPaymentList($id = null)
    {
        // make datatable
        $this->db = config_db(null, true);
        $this->load->model('datatables');
        $this->datatables->table = 'tbl_saas_companies_payment';
        $this->datatables->join_table = array('tbl_saas_companies', 'tbl_saas_companies_history');
        $this->datatables->join_where = array('tbl_saas_companies.id=tbl_saas_companies_payment.companies_id', 'tbl_saas_companies_history.id=tbl_saas_companies_payment.companies_history_id');

        $column = array('tbl_saas_companies_history.package_name', 'transaction_id', 'total_amount', 'payment_date', 'payment_method');
        $this->datatables->column_order = $column;
        $this->datatables->column_search = $column;
        $this->datatables->order = array('id' => 'desc');
        $this->datatables->select = ('tbl_saas_companies_payment.*,tbl_saas_companies_history.package_name,tbl_saas_companies.name as company_name');
        // select tbl_saas_companies_history.name
        if (!empty($id)) {
            $where = array('tbl_saas_companies_payment.companies_id' => $id);
        } else {
            $where = array();
        }
        $fetch_data = make_datatables($where);
        $access = super_admin_access();
        $data = array();
        foreach ($fetch_data as $_key => $v_history) {
            $action = null;
            $sub_array = array();

            if (!empty($access)) {
                $name = $v_history->company_name;

                $name .= '<div class="row-options">';
                $name .= '<a 
                    data-toggle="tooltip" data-placement="top"
                    href="' . base_url('saas/gb/delete_companies_payment/' . $v_history->id) . '"  title="' . _l('delete') . '" class="text-danger _delete">' . _l('delete') . '</a>';
                $name .= '</div>';
                $sub_array[] = $name;
            }
            $sub_array[] = '<a href="' . base_url('subs_package_details/' . $v_history->companies_history_id . '/1') . '"  data-toggle="modal" data-target="#myModal" >' . $v_history->package_name . '</a>';
            $sub_array[] = $v_history->transaction_id;
            $sub_array[] = display_money($v_history->total_amount, default_currency());
            $sub_array[] = _dt($v_history->payment_date);
            $sub_array[] = $v_history->payment_method;
            $data[] = $sub_array;
        }
        render_table_old($data, $where);
    }

    /**
     * @throws Exception
     */
    public function custom_domain($action = null, $id = null)
    {

        $data['title'] = _l('custom_domain');
        $data['company_info'] = get_company_subscription_by_id();
        if (!empty($action)) {
            if (!empty($id)) {
                $data['domain_info'] = get_old_result('tbl_saas_domain_requests', array('request_id' => $id), false);
            }
            if ($action == 'update') {
                // check already exist the domain request
                $where = array('company_id' => $data['company_info']->companies_id, 'status' => 'pending');
                if (!empty($id)) {
                    $where['request_id !='] = $id;
                }

                $check = get_old_result('tbl_saas_domain_requests', $where, false);
                if (!empty($check)) {
                    set_alert('warning', _l('already_request'));
                    redirect('clients/custom_domain');
                }

                $pdata['custom_domain'] = $this->input->post('custom_domain', true);
                $pdata['status'] = 'pending';
                $pdata['company_id'] = $data['company_info']->companies_id;
                $this->saas_model->_table_name = 'tbl_saas_domain_requests';
                $this->saas_model->_primary_key = 'request_id';
                $this->saas_model->save_old($pdata, $id);

                $superadmin = get_old_result(db_prefix() . 'staff', array('admin' => 1, 'role' => 4));
                $users = [];
                foreach ($superadmin as $key => $value) {
                    add_notification([
                        'description' => _l('not_domain_request', $pdata['custom_domain']),
                        'touserid' => $value->staffid,
                        'fromcompany' => true,
                        'link' => 'saas/domain/requests/',
                    ]);
                    $users[] = $value->staffid;
                }
                pusher_trigger_notification(array_unique($users));

                set_alert('success', _l('domain_request_updated_successfully'));
                redirect('clients/custom_domain');
            }
            if ($action == 'delete') {
                if ($data['domain_info']->company_id == $data['company_info']->companies_id) {
                    if ($data['domain_info']->status == 'approved') {
                        $this->saas_model->_table_name = 'tbl_saas_domain_requests';
                        $this->saas_model->_primary_key = 'request_id';
                        $this->saas_model->delete_old($id);

                        $this->saas_model->_table_name = 'tbl_saas_companies';
                        $this->saas_model->_primary_key = 'id';
                        $this->saas_model->save_old(array('domain_url' => ''), $data['company_info']->companies_id);


                    } else {
                        $this->saas_model->_table_name = 'tbl_saas_domain_requests';
                        $this->saas_model->_primary_key = 'request_id';
                        $this->saas_model->delete_old($id);
                    }
                    set_alert('success', _l('domain_request_deleted_successfully'));


                } else {
                    set_alert('warning', _l('404_error'));
                }
                redirect('clients/custom_domain');
            }

        }
        $data['action'] = $action;
        $data['id'] = $id;
        $data['c_url'] = 'clients/';
        $data['all_domain'] = get_old_result('tbl_saas_domain_requests', array('company_id' => $data['company_info']->companies_id));
        $this->set_layout($data, 'domain/custom_domain');

    }


    /**
     * @throws Exception
     */
    public function customizePackages($comp_id = null)
    {
        $data['title'] = _l('customize_packages');
        isClientLogin($comp_id);
        $data['companyInfo'] = get_company_subscription_by_id();
        if (!empty($data['companyInfo'])) {
            $customer_id = get_client_user_id();
            $data['packageInfo'] = get_usages($data['companyInfo']);
            $data['invoices_to_merge'] = $this->saas_model->check_for_merge_invoice($customer_id);
            $company_id = $data['companyInfo']->companies_id;
            $data['company_id'] = $company_id;
            $data['moduleInfo'] = get_old_result('tbl_saas_package_module');
            $data['payment_modes'] = $this->saas_model->get_payment_modes();
            $data['url'] = 'clients/';
            $this->set_layout($data, 'packages/customize_packages');
        } else {
            set_alert('warning', _l('404_error'));
            redirect('clients/dashboard');
        }
    }

    public function proceedPayment($payment_method = null)
    {
        $subs_info = get_company_subscription_by_id(null, 'running');
        $data = $_POST;
        if (!empty($subs_info) && !empty($data['paymentmode'])) {
            $this->saas_model->proceedPayment($subs_info);
        } else {
            set_alert('warning', _l('select_payment_method'));
            redirect('clients/customizePackages');
        }
    }

    public
    function get_expired_date($package_type)
    {
        $type_title = str_replace('_price', '', $package_type);
        if ($type_title == 'lifetime') {
            $renew_date = date('Y-m-d', strtotime('+100 year'));
        } elseif ($type_title == 'yearly') {
            $renew_date = date('Y-m-d', strtotime('+1 year'));
        } else {
            $renew_date = date('Y-m-d', strtotime('+1 month'));
        }
        return $renew_date;
    }

    public
    function get_modules($comp_id = null)
    {
        $data['title'] = _l('modules');
        isClientLogin($comp_id);
        $data['payment_modes'] = $this->saas_model->get_payment_modes();
        $data['all_modules'] = get_old_result('tbl_saas_package_module', array('status' => 'published'));
        $this->set_layout($data, 'packages/modules/get_modules');
    }

    public
    function module_details($module)
    {
        $data['title'] = _l('customize_packages');
        $data['module'] = get_old_result('tbl_saas_package_module', array('module_name' => $module, 'status' => 'published'), false);
        if (empty($data['module'])) {
            set_alert('warning', _l('404_error'));
            redirect('clients/dashboard');
        }
        $this->set_layout($data, 'packages/modules/module_details');

    }

    private
    function set_layout($data, $view)
    {
        $this->data($data);
        $this->view($view);
        no_index_customers_area();
        $this->layout();
    }

    public function proceedPackage($package_id = null, $company_id = null)
    {
        $data['package_id'] = $package_id;
        $data['frequency'] = 'monthly';
        if (empty($data['package_id']) && !empty(is_client_logged_in())) {
            $subs_info = get_company_subscription_by_id(null, 'running');
            $data['package_id'] = $subs_info->package_id;
            $data['frequency'] = $subs_info->frequency;
        }
        $package_info = get_old_result('tbl_saas_packages', array('id' => $data['package_id']), false);
        $data['title'] = _l('checkout') . ' ' . _l('payment') . ' ' . _l('for') . ' ' . $package_info->name;
        $data['package_info'] = $package_info;
        $data['all_packages'] = get_old_result('tbl_saas_packages', array('status' => 'published'));
        $subview = 'checkoutPayment';
        if (!empty(is_client_logged_in())) {
            $data['subs_info'] = get_company_subscription_by_id();
            $data['payment_modes'] = $this->saas_model->get_payment_modes();
            $subview = 'checkoutPaymentOpen';
        } else if (!empty($company_id)) {
            $company_id = url_decode($company_id);
            $data['subs_info'] = $this->saas_model->company_info($company_id);
            $data['subs_info']->companies_id = $company_id;
            $data['payment_modes'] = $this->saas_model->get_payment_modes();
            $subview = 'checkoutPaymentOpen';
            $data['company_id'] = $company_id;
            $data['front'] = true;
        }
        $view = 'saas/packages/' . $subview;
        $this->set_layout($data, $view);
    }


}
