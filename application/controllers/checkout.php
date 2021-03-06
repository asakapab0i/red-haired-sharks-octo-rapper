<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Checkout extends CI_Controller {
	
	public $paypal_id;

	function __construct(){
		parent::__construct();
		$this->load->model('checkout_model');
		$this->load->model('cart_model');
		$this->load->helper('string');

		$this->paypal_id = 'rongie.andrade@gmail.com';


		//Everytime this class is called it automatically checks the the function is_logged_in
		//If not then redirect to homepage
		// if (!$this->_is_logged_in()) {
		// 	$this->session->set_flashdata('checkout', 'You need to be logged in to checkout!');
		// 	redirect("account/login");
		// }

	}

	private function _is_logged_in(){
		$login_session = $this->session->userdata('login');
		return $login_session["logged_in"];
	}

	public function index(){
		$cart = $this->cart_model->check_checkout($this->cart->contents());
		
		if (!$this->_is_logged_in()) {
			$this->session->set_flashdata('checkout', 'You need to be logged in to checkout!');
			redirect("account/login");
		}


		if (!count($this->cart->contents()) > 0) {
			$this->session->set_flashdata('checkout', 'You cannot checkout because your cart is empty!');
			redirect("cart");
		}

		

		if ($cart == false) {
			$this->session->set_flashdata('checkout', 'Please check your cart for possible unavailability of items.');
			redirect("cart");
		}


		$login_session = $this->session->userdata('login');
		$user_id = $login_session["id"];


		$checkout['items'] = $this->cart->total_items();
		$checkout['address'] = $this->checkout_model->get_address($user_id);

		$checkout['total_price'] = $this->cart->format_number($this->cart->total());
		$checkout['total_items'] = $this->cart->total_items();
		$checkout['cart_contents'] = $this->cart->contents();

		$checkout['grand_total'] = $this->cart->format_number($this->cart->total());



	
		$q1 = $this->cart->contents();
		shuffle($q1);
		$checkout['recent_cart'] = $q1;


		//Prepare Header Data
		$header['page_title'] = 'Checkout Express';
		
		//Navigation
		$navigation['page_cur_nav'] = 'checkout';


		//Page Header
		$this->parser->parse('template/header', $header);
		//Page Nav
		$this->load->view('template/navigation', $navigation);
		//Page Main Content
		$this->parser->parse('checkout/checkout_view', $checkout);
		//Page Footer
		$this->load->view('template/footer');

		
		
	}


	public function place_order(){

		if (!$this->_is_logged_in()) {
			$this->session->set_flashdata('checkout', 'You need to be logged in to checkout!');
			redirect("account/login");
		}

		$payment_info = array('shipping_address' => $this->input->post('shipping_address'),
							'shipping_type' => $this->input->post('shipping_type'),
							'payment_method' => $this->input->post('payment_method')
							);


		if ($payment_info['payment_method'] == 'paypal_checkout') {
			$this->pay_with_paypal($payment_info['shipping_address'], $payment_info['shipping_type']);
		}else if ($payment_info['payment_method'] == 'bank') {
			$this->pay_with_bank($payment_info['shipping_address'], $payment_info['shipping_type']);
		}else if ($payment_info['payment_method'] == 'credit_card') {
			$this->pay_with_credit_card($payment_info['shipping_address'], $payment_info['shipping_type']);
		}


	}
	
	public function pay_with_paypal($address_id, $shipping_type){

				if (!$this->_is_logged_in()) {
					$this->session->set_flashdata('checkout', 'You need to be logged in to checkout!');
					redirect("account/login");
				}

				 $login = $this->session->userdata('login');
				 $user_id = $login['id'];

				 $cart = $this->cart->contents();
				//insert order data
				$order_data = array('order_id' => random_string('alnum', 16),
									'address_id' => $address_id,
									'order_total' => $this->cart->total(),
									'order_status' => 'Pending', //Always pending, to be changed by administrator
									'dateorder' => date('Y-m-d H:i:s'),
									'user_id' => $user_id,
									'payment_method' => 'Paypal',
									'shipping_info' => 'Standard',
									'cart_data' => serialize($cart)
									); 
				$this->checkout_model->insert_order_data($order_data);

				

				//destroy the cart
				$this->cart->destroy();


				//Perform the payment
                $config['business']                         = $this->paypal_id;
                $config['cpp_header_image']         = ''; //Image header url [750 pixels wide by 90 pixels high]
                $config['return']                                 = base_url() . 'checkout/notify_payment';
                $config['cancel_return']                 = base_url() . 'checkout/cancel_payment';
                $config['notify_url']                         = base_url() . 'checkout/paypal_notify'; //IPN Post
                $config['production']                         = FALSE; //Its false by default and will use sandbox
                $config["invoice"]                                = random_string('alnum', 16); //The invoice id
                $config["custom"] = $order_data['order_id']; 


                $this->load->library('paypal',$config);
                
                #$this->paypal->add(<name>,<price>,<quantity>[Default 1],<code>[Optional]);

               

				foreach ($cart as $key => $cart2) {
					foreach ($cart2 as $key2 => $value) {
					}

					$this->paypal->add($cart2['name'],$cart2['price'],$cart2['qty']); //First item

				}

                
               $this->paypal->pay(); //Proccess the payment


	}


	public function approve_order(){
		

		$order_id = $this->input->post('order_id');
		//get cart contents
			$cart_data = $this->cart_model->get_order_cart_data($order_id);
			$cart_data = unserialize($cart_data[0]['cart_data']);



			$cart_availability = $this->cart_model->check_checkout($cart_data);

			//check if items in the cart is available for purchase
			if ($cart_availability == false) {
				$this->session->set_flashdata('approve_order', 'One or more items in this current order is either not available or on limited stock. <br/> Please reorder and adjust your cart contents.');
				redirect('customer/order/'.$order_id.'');
			}




			//Perform the payment
                $config['business']                         = $this->paypal_id;
                $config['cpp_header_image']         = ''; //Image header url [750 pixels wide by 90 pixels high]
                $config['return']                                 = base_url() . 'checkout/notify_payment';
                $config['cancel_return']                 = base_url() . 'checkout/cancel_payment';
                $config['notify_url']                         = base_url() . 'checkout/paypal_notify'; //IPN Post
                $config['production']                         = FALSE; //Its false by default and will use sandbox
                $config["invoice"]                                = random_string('alnum', 8); //The invoice id
                $config["custom"] = $order_id; 


                $this->load->library('paypal',$config);
                
                #$this->paypal->add(<name>,<price>,<quantity>[Default 1],<code>[Optional]);

	               foreach ($cart_data as $key => $value) {

						// $data = array(
						// 	'id'      => $value['id'],
						// 	'qty'     => $value['qty'],
						// 	'price'   => $value['price'],
						// 	'name'    => $value['name'],
						// 	'image'	  => $value['image'],
						// 	'link'	  => $value['link']
						// );
						
						$this->paypal->add($value['name'],$value['price'],$value['qty']); //First item
					}

				// foreach ($cart as $key => $cart2) {
				// 	foreach ($cart2 as $key2 => $value) {
				// 	}

				// 	$this->paypal->add($cart2['name'],$cart2['price'],$cart2['qty']); //First item

				// }

               //destroy the cart
			   $this->cart->destroy();
               $this->paypal->pay(); //Proccess the payment

	}

	public function pay_with_bank($address_id, $shipping_type){

		echo 'Not yet implemented';
		
	}

	public function pay_with_credit_card($address_id, $shipping_type){
		echo 'Not yet implemented';
	}

	public function notify_payment(){
		
		if ($this->input->post()) {

			
			$data['paypal'] = $this->input->post();

			//Prepare Header Data
			$header['page_title'] = 'My Orders';
			
			//Navigation
			$navigation['page_cur_nav'] = 'checkout';

			//Main Content
			

			//Page Header
			$this->parser->parse('template/header', $header);
			//Page Nav
			$this->load->view('template/navigation', $navigation);
			//Page Main Content
			$this->load->view('checkout/checkout_success_view',$data);
			//Page Footer
			$this->load->view('template/footer');
		
		}

	}

	public function cancel_payment(){
		redirect('home');
	}

	public function paypal_notify(){
	
				//save paypal_log
				// 
				
				if ($this->input->post()) {

					#email			
					$paypal_log = $this->input->post();
					#$order_id = $paypal["custom"];
					#$link = site_url("administrator/order/$order_id"); 
				 	#$url = "<a href='$link'>Order $order_id";
					$message = "Another sales report. \n Please check your adminstrator dashboard";
					mail('helpdesk@labelleaurorebookshop.com', 'Sales Notification', $message);

				 	$this->checkout_model->insert_paypal_log(array('data' => serialize($paypal_log),'order_id' => $paypal_log['custom']));
				 	$this->checkout_model->update_approve_order($paypal_log['custom']);

				 	$cart = $this->checkout_model->get_cart_details($paypal_log['custom']);
					foreach ($cart as $key => $value) {
				 	$id = $value['id'];
				 	$qty = $value['qty'];
				 	$this->db->query("UPDATE books SET product_qty = product_qty - $qty WHERE product_id = '$id' ");
					$this->db->query("UPDATE books SET book_sold = book_sold + 1 WHERE product_id = '$id' ");
				 }
				 	
			
				 	#SKU4990 = 5
				 	#SKU2579 = 2

				}
			
				
	}
	

	/*
	Email Template
	*/


}
