<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customer extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('customer_model');
		$this->load->model('administrator_model');
		$this->load->library('datatables');

		
		//Everytime this class is called it automatically checks the the function is_logged_in
		//If not then redirect to homepage
		if (!$this->is_logged_in()) {
			redirect("home");
		}

	}

	public function is_logged_in(){
		$login_session = $this->session->userdata('login');
		return $login_session["logged_in"];
	}

	public function index(){
		$this->dashboard();
	}

	public function dashboard(){
		/*
		DONE: @task - : remember to get the session user id
		*/
		$login_session = $this->session->userdata('login');
		$user_id = $login_session["id"];



		//Prepare Header Data
		$header['page_title'] = 'Customer Dashboard';
		
		//Navigation
		$navigation['page_cur_nav'] = 'dashboard';

		//Main Content
		$dashboard['user_info'] = $this->customer_model->customer_info($user_id);
		$dashboard['user_shipping_info'] = $this->customer_model->customer_shipping_info($user_id);

		//Page Header
		$this->parser->parse('template/header', $header);
		//Page Nav
		$this->load->view('template/navigation', $navigation);
		//Page Main Content
		$this->parser->parse('dashboard/account_dashboard', $dashboard);
		//Page Footer
		$this->load->view('template/footer');
	}

	public function account(){

		$login_session = $this->session->userdata('login');
		$user_id = $login_session["id"];

		//Prepare Header Data
		$header['page_title'] = 'Account Information';
		
		//Navigation
		$navigation['page_cur_nav'] = 'dashboard';

		//Main Content
		$information['account_information'] = $this->customer_model->customer_personal_info($user_id);



		//Page Header
		$this->parser->parse('template/header', $header);
		//Page Nav
		$this->load->view('template/navigation', $navigation);
		//Page Main Content
		$this->parser->parse('dashboard/account_information', $information);
		//Page Footer
		$this->load->view('template/footer');
	}

	public function account_validate(){
		/*
		@task : remember to get the session user id
		*/

		$login_session = $this->session->userdata('login');
		$user_id = $login_session["id"];


		$personal = array('fname' => $this->input->post('fname'),
						  'lname' => $this->input->post('lname'),
						  'email' => $this->input->post('email'),
						  'mobile' => $this->input->post('mobile'),
						  'dob' => $this->input->post('dob'),
						  'gender' => $this->input->post('gender'),
						  'marital_status' => $this->input->post('mstatus')
						  );

		$this->form_validation->set_error_delimiters('<span class="label label-danger">', '</span><br/>');
		$this->form_validation->set_rules('fname','First Name','required');
		$this->form_validation->set_rules('lname','Last Name','required');
		$this->form_validation->set_rules('email','Email Address','required|valid_email');
		$this->form_validation->set_message('valid_email', 'Must contain valid email.');
		$this->form_validation->set_rules('mobile','Mobile Number','required');
		$this->form_validation->set_rules('dob','Date of birth','required');
		$this->form_validation->set_rules('gender','Gender','required');
		$this->form_validation->set_rules('mstatus','Marital Status','required');



		if ($this->form_validation->run() == FALSE){
			$this->account_validate_error();
		}
		else{

			if ($this->customer_model->insert_personal_record($personal, $user_id)) {
				redirect('customer/dashboard');
			}
		}
	}

	public function account_validate_error(){
		//Prepare Header Data
		$header['page_title'] = 'Account Information';
		
		//Navigation
		$navigation['page_cur_nav'] = 'dashboard';

		//Main Content


		//Page Header
		$this->parser->parse('template/header', $header);
		//Page Nav
		$this->load->view('template/navigation', $navigation);
		//Page Main Content
		$this->load->view('dashboard/account_information_validate');
		//Page Footer
		$this->load->view('template/footer');
	}

	public function orders(){
		//Prepare Header Data
		$header['page_title'] = 'My Orders';
		
		//Navigation
		$navigation['page_cur_nav'] = 'dashboard';

		//Main Content
		//$orders['order_history'] = $this->customer_model->get_order_history($id = 40);

		//Page Header
		$this->parser->parse('template/header', $header);
		//Page Nav
		$this->load->view('template/navigation', $navigation);
		//Page Main Content
		$this->load->view('dashboard/account_orders');
		//Page Footer
		$this->load->view('template/footer');
	}

	public function order($order_no){
		//Prepare Header Data
		$header['page_title'] = 'My Order | '.$order_no.' ';
		


		//Navigation
		$navigation['page_cur_nav'] = 'dashboard';

		//Main Content
		$order['order_data'] = $this->customer_model->get_order_by_order_no($order_no);
		$order['order_cart_contents'] = $this->customer_model->get_order_cart_contents($order_no);
		$order['total'] = $this->customer_model->get_order_cart_total($order_no);
		$order['status'] = $order['order_data'][0]['order_status'];
		$order['message_box'] = $this->administrator_model->get_messages($order_no);



		//Page Header
		$this->parser->parse('template/header', $header);
		//Page Nav
		$this->load->view('template/navigation', $navigation);
		//Page Main Content
		$this->parser->parse('dashboard/account_order', $order);
		//Page Footer
		$this->load->view('template/footer');
	}

	public function invoice_log($order_no){
		//Prepare Header Data
		$header['page_title'] = 'Paypal Invoice Log | '.$order_no.' ';
		


		//Navigation
		$navigation['page_cur_nav'] = 'dashboard';

		//Main Content
		$invoice['paypal_log'] = $this->customer_model->get_paypal_log($order_no);
		$invoice['order_no'] = $order_no;



		//Page Header
		$this->parser->parse('template/header', $header);
		//Page Nav
		$this->load->view('template/navigation', $navigation);
		//Page Main Content
		$this->load->view('dashboard/account_invoice',$invoice);
		//Page Footer
		$this->load->view('template/footer');
	}

	public function address(){
		$login_session = $this->session->userdata('login');
		$user_id = $login_session["id"];


		//Prepare Header Data
		$header['page_title'] = 'My Address';
		
		//Navigation
		$navigation['page_cur_nav'] = 'dashboard';

		//Main Content
		$address['address_primary'] = $this->customer_model->get_address_primary($user_id);
		$address['address_shipping'] = $this->customer_model->get_address_shipping($user_id);

		//Page Header
		$this->parser->parse('template/header', $header);
		//Page Nav
		$this->load->view('template/navigation', $navigation);
		//Page Main Content
		$this->parser->parse('dashboard/account_address', $address);
		//Page Footer
		$this->load->view('template/footer');
	}

	public function address_add(){
		$login_session = $this->session->userdata('login');
		$user_id = $login_session["id"];


		//Prepare Header Data
		$header['page_title'] = 'My Address';
		
		//Navigation
		$navigation['page_cur_nav'] = 'dashboard';

		//Main Content
		$address['address_primary'] = $this->customer_model->get_address_primary($user_id);
		$address['address_shipping'] = $this->customer_model->get_address_shipping($user_id);

		//Page Header
		$this->parser->parse('template/header', $header);
		//Page Nav
		$this->load->view('template/navigation', $navigation);
		//Page Main Content
		$this->parser->parse('dashboard/account_address_add', $address);
		//Page Footer
		$this->load->view('template/footer');
	}

	public function address_add_validation(){
	$login_session = $this->session->userdata('login');
	$user_id = $login_session["id"];

	$this->form_validation->set_rules('company', 'Company', 'trim|required|xss_clean');
	$this->form_validation->set_rules('city', 'City', 'trim|required|xss_clean');
	$this->form_validation->set_rules('zip', 'Zip/Postal Code', 'trim|required|xss_clean');
	$this->form_validation->set_rules('street', 'Street Address', 'trim|required|xss_clean');
	$this->form_validation->set_rules('telephone', 'State/Province', 'trim|required|xss_clean');
	$this->form_validation->set_rules('country', 'country', 'trim|required|xss_clean');


	if ($this->form_validation->run() == FALSE) {
		redirect('address_add');
	}else{


		$formdata = array(
			"company" => $this->input->post('company'),
			"city" => $this->input->post('city'),
			"zip" => $this->input->post('zip'),
			"street" => $this->input->post('street'),
			"telephone" => $this->input->post('telephone'),
			"country" => $this->input->post('country'),
			"user_id" => $user_id,
			"type" => 'shipping'

			);

		$this->customer_model->insert_shipping_address($formdata);
		$this->session->set_flashdata('add_address', 'Address successfully added!');
		redirect('customer/address');
	}



	}



	public function wishlist(){
		$login_session = $this->session->userdata('login');
		$user_id = $login_session["id"];


		//Prepare Header Data
		$header['page_title'] = 'Account Wishlist';
		
		//Navigation
		$navigation['page_cur_nav'] = 'dashboard';


		$wishlist['wishlist'] = $this->customer_model->get_wishlist($user_id);


		//Page Header
		$this->parser->parse('template/header', $header);
		//Page Nav
		$this->load->view('template/navigation', $navigation);
		//Page Main Content
		$this->parser->parse('dashboard/account_wishlist', $wishlist);
		//Page Footer
		$this->load->view('template/footer');
	}

	public function remove_wishlist($product_id){

		$this->customer_model->remove_wishlist($product_id);
		redirect('customer/wishlist');

	}

	public function change_password(){

		$session = $this->session->userdata('login');
		$userid = $session['id'];

		if (($this->input->post('password'))) {

			$this->form_validation->set_error_delimiters('<span class="label label-danger">', '</span>');
			$this->form_validation->set_rules('old_password', 'Old Password', 'trim|required|xss_clean|callback_check_oldpw');
			$this->form_validation->set_rules('password', 'New Password', 'trim|required|xss_clean');
			$this->form_validation->set_rules('conf_password', 'Confirm Password', 'trim|required|xss_clean|matches[password]');

			if ($this->form_validation->run() == false) {

				//Prepare Header Data
				$header['page_title'] = 'Account Change Password';
				
				//Navigation
				$navigation['page_cur_nav'] = 'dashboard';

				//Page Header
				$this->parser->parse('template/header', $header);
				//Page Nav
				$this->load->view('template/navigation', $navigation);
				//Page Main Content
				$this->load->view('dashboard/account_change_password');
				//Page Footer
				$this->load->view('template/footer');
			}else{
				$new_password = $this->input->post('password');
				$this->customer_model->change_password($userid, $new_password);
				$this->session->set_flashdata('success_change_pw', 'Change password successful!');
				redirect('customer/change_password');
			}
		}else{
				//Prepare Header Data
				$header['page_title'] = 'Account Change Password';
				
				//Navigation
				$navigation['page_cur_nav'] = 'dashboard';

				//Page Header
				$this->parser->parse('template/header', $header);
				//Page Nav
				$this->load->view('template/navigation', $navigation);
				//Page Main Content
				$this->load->view('dashboard/account_change_password');
				//Page Footer
				$this->load->view('template/footer');
		}	
	}


	public function datatables_order(){
		$login_session = $this->session->userdata('login');
		$user_id = $login_session["id"];

		$this
		->datatables
		->select('order_id, dateorder, CONCAT(fname," ",lname) AS lname, order_total,package_status, order_status', FALSE)
		->from('users')
		->join('address', 'users.id = address.user_id')
		->join('orders','orders.address_id = address.address_id')
		->where('users.id', $user_id);


		$datatables = $this->datatables->generate('JSON');
		echo $datatables;


	}

	public function datatables_recent_order(){
		$login_session = $this->session->userdata('login');
		$user_id = $login_session["id"];

		$this
		->datatables
		->select('order_id, dateorder, CONCAT(fname," ",lname) AS lname, order_total, package_status, order_status',FALSE)
		->from('users')
		->join('address', 'users.id = address.user_id')
		->join('orders','orders.address_id = address.address_id')
		->where('users.id', $user_id);


		$datatables = $this->datatables->generate('JSON');
		echo $datatables;


	}

	public function check_oldpw($oldpw){


		$this->db->select('*')->from('users')->where('password', md5($oldpw));
		$sql = $this->db->get();

		if ($sql->num_rows() > 0) {
			return TRUE;
		}else{
			$this->form_validation->set_message('check_oldpw', 'Old Password does not match with your current password');
			return FALSE;
		}
	}


}
