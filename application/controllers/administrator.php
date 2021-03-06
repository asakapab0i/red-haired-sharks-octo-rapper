<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Administrator extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('administrator_model');
		$this->load->model('customer_model');
		$this->load->library('datatables');




		//Everytime this class is called it automatically checks the the function is_logged_in
		//If not then redirect to homepage
		if ($this->_is_logged_in() == false) {
			redirect('home');
		}
	}

	private function _is_logged_in(){
		$login_session = $this->session->userdata('login');

		if ($login_session['type'] == 'admin' || $login_session['type'] == 'staff') {
			return true;
		}else{
			return false;
		}

	}

	public function index()
	{
        	//Prepare Header Data
		$header['page_title'] = 'Administrator';

		//Navigation
		$navigation['page_cur_nav'] = 'dashboard';

		//Main Content
		$index['net_income'] = $this->administrator_model->get_net_income();
		$index['recent_books'] =  $this->administrator_model->get_recent_books();
		$index['recent_orders'] = $this->administrator_model->get_recent_orders();
		$index['recent_message'] = $this->administrator_model->get_recent_messages();

		//Page Header
		$this->parser->parse('template/header', $header);
		//Page Nav
		$this->load->view('template/navigation', $navigation);
		//Page Main Content
		$this->parser->parse('administrator/administrator_dashboad_view', $index);
		//Page Footer
		$this->load->view('template/footer');
	}

        public function sales($date = false){
          if($date == false){
            $date = date('Y-m-d');
          }


         	//Prepare Header Data
		$header['page_title'] = 'Administrator';

		//Navigation
		$navigation['page_cur_nav'] = 'dashboard';

                //Main Content
                $sales['date'] = $date;
                $sales['sales'] = $this->administrator_model->get_sales_by_date($date);
                $sales['earnings'] = $this->administrator_model->get_total_earning_by_date($date);
		//Page Header
		$this->parser->parse('template/header', $header);
		//Page Nav
		$this->load->view('template/navigation', $navigation);
		//Page Main Content
		$this->parser->parse('administrator/administrator_sales_view', $sales);
		//Page Footer
		$this->load->view('template/footer');

              
          
        }

	public function orders(){
		//Prepare Header Data
		$header['page_title'] = 'Administrator | Orders';

		//Navigation
		$navigation['page_cur_nav'] = 'dashboard';

		//Main Content



		//Page Header
		$this->parser->parse('template/header', $header);
		//Page Nav
		$this->load->view('template/navigation', $navigation);
		//Page Main Content
		$this->load->view('administrator/administrator_orders_view');
		//Page Footer
		$this->load->view('template/footer');
	}

	public function order($id){

		//Prepare Header Data
		$header['page_title'] = 'Administrator | Order No: ' . $id;

		//Navigation
		$navigation['page_cur_nav'] = 'dashboard';

		//Main Content
		$order['id'] = $id;
		$order['order_data'] = $this->administrator_model->get_order($id);

		$order['order_cart_contents'] = $this->customer_model->get_order_cart_contents($id);
		$order['total'] = $this->customer_model->get_order_cart_total($id);
		$order['message_box'] = $this->administrator_model->get_messages($id);



		//Page Header
		$this->parser->parse('template/header', $header);
		//Page Nav
		$this->load->view('template/navigation', $navigation);
		//Page Main Content
		$this->parser->parse('administrator/administrator_order_view', $order);
		//Page Footer
		$this->load->view('template/footer');
	}

	public function shipments(){
		//Prepare Header Data
		$header['page_title'] = 'Administrator | Shipments';

		//Navigation
		$navigation['page_cur_nav'] = 'dashboard';

		//Main Content
		//$order['id'] = $id;
		//$order['order_data'] = $this->administrator_model->get_order($id);


		//Page Header
		$this->parser->parse('template/header', $header);
		//Page Nav

		//Page Main Content
		$this->load->view('administrator/administrator_shipments_view');
		//Page Footer
		$this->load->view('template/footer');
	}

	public function shipment($shipment_id){
		//Prepare Header Data
		$header['page_title'] = 'Administrator | Shipments No '.$shipment_id.' ';

		//Navigation
		$navigation['page_cur_nav'] = 'dashboard';

		//Main Content
		$shipment['shipment_data'] = $this->administrator_model->get_shipment($shipment_id);

		//Page Header
		$this->parser->parse('template/header', $header);
		//Page Nav
		$this->load->view('template/navigation', $navigation);
		//Page Main Content
		$this->parser->parse('administrator/administrator_shipment_view', $shipment);
		//Page Footer
		$this->load->view('template/footer');
	}

	public function books(){
		//Prepare Header Data
		$header['page_title'] = 'Administrator | Books';

		//Navigation
		$navigation['page_cur_nav'] = 'dashboard';


		//Page Header
		$this->parser->parse('template/header', $header);
		//Page Nav
		$this->load->view('template/navigation', $navigation);
		//Page Main Content
		$this->load->view('administrator/administrator_books_view');
		//Page Footer
		$this->load->view('template/footer');
	}

	public function book($id){

		//Prepare Header Data
		$header['page_title'] = 'Administrator | Book No '.$id.' ';

		//Navigation
		$navigation['page_cur_nav'] = 'dashboard';

		$book['book_info'] = $this->administrator_model->get_book_by_id($id);
		$book['categories'] = $this->administrator_model->get_category();


		//Page Header
		$this->parser->parse('template/header', $header);
		//Page Nav
		$this->load->view('template/navigation', $navigation);
		//Page Main Content
		$this->parser->parse('administrator/administrator_book_edit_view', $book);
		//Page Footer
		$this->load->view('template/footer');

	}

	public function book_add(){
		//Prepare Header Data
		$header['page_title'] = 'Administrator | Book No ';

		//Navigation
		$navigation['page_cur_nav'] = 'dashboard';

		$book_add['category'] = $this->administrator_model->get_category();

		//Page Header
		$this->parser->parse('template/header', $header);
		//Page Nav
		$this->load->view('template/navigation', $navigation);
		//Page Main Content
		$this->parser->parse('administrator/administrator_book_add_view', $book_add);
		//Page Footer
		$this->load->view('template/footer');

	}

	public function  book_add_validate(){


		$this->form_validation->set_rules('title', 'Book Title', 'trim|required');
		$this->form_validation->set_rules('condition', 'Book Condition', 'trim|required');
		$this->form_validation->set_rules('author', 'Author', 'trim|required');
		$this->form_validation->set_rules('description', 'Description', 'trim|required');
		$this->form_validation->set_rules('publisher', 'Publisher', 'trim|required');
		$this->form_validation->set_rules('format', 'Format', 'trim|required');
		$this->form_validation->set_rules('isbn', 'ISBN', 'trim|required');
		$this->form_validation->set_rules('category', 'Category', 'trim|required');
		$this->form_validation->set_rules('price', 'Price', 'trim|required|callback_check_number');
		$this->form_validation->set_rules('quantity', 'Quantity', 'trim|required|integer|callback_check_number');

		if (empty($_FILES['userfile']['name']))
		{
			$this->form_validation->set_rules('userfile', 'Image', 'required');
		}

		//do an upload first before inserting the whole data

		$config['file_name'] = url_title($this->input->post('title'));
 		$config['upload_path'] = './assets/img/books_image/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '1000';


		$this->load->library('upload', $config);





		if (!$this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());	
			var_dump($error);
			die();	
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
		}


		$product_id = rand(100, 9999);
		$product_id = 'SKU'.$product_id; 

		$form_data = array('title' => $this->input->post('title'),
				'condition' => $this->input->post('condition'),	
				'author' =>  ascii_to_entities($this->input->post('author')),
				'description' => ascii_to_entities($this->input->post('description')),
				'publisher' => $this->input->post('publisher'),
				'format' => $this->input->post('format'),
				'isbn' => $this->input->post('isbn'),
				'dateadd' => date('Y-m-d'),
				'category' => $this->input->post('category'),
				'price' => $this->input->post('price'),
				'product_id' => $product_id,
				'product_qty' => $this->input->post('quantity'),
				'image' => $data['upload_data']['file_name'],
				'product_url' => url_title($this->input->post('title'))
				);





		if ($this->form_validation->run() == False) {
			//var_dump(validation_errors());
			//die();
			redirect('administrator/book_add');
		}else{
			$this->session->set_flashdata('add_success', 'Book Successfully Added!');
			$this->administrator_model->insert_add_book($form_data);
			redirect('administrator/book_add');
		}



	}


	public function check_number($number){

		if ($number > 0) {
			return TRUE;
		}else{
			$this->form_validation->set_message('check_number', 'This field should not be negative.');
			return False;
		}

	}


	public function book_edit($id){

		$this->form_validation->set_rules('title', 'Book Title', 'trim|required');
		$this->form_validation->set_rules('condition', 'Book Condition', 'trim|required');
		$this->form_validation->set_rules('author', 'Author', 'trim|required');
		$this->form_validation->set_rules('description', 'Description', 'trim|required');
		$this->form_validation->set_rules('publisher', 'Publisher', 'trim|required');
		$this->form_validation->set_rules('format', 'Format', 'trim|required');
		$this->form_validation->set_rules('isbn', 'ISBN', 'trim|required');
		$this->form_validation->set_rules('category', 'Category', 'trim|required');
		$this->form_validation->set_rules('price', 'Price', 'trim|required|callback_check_number');
		$this->form_validation->set_rules('quantity', 'Quantity', 'trim|required|callback_check_number');



		$form_data = array('title' => ascii_to_entities($this->input->post('title')),
				'condition' => $this->input->post('condition'),
				'author' => ascii_to_entities($this->input->post('author')),
				'description' => ascii_to_entities($this->input->post('description')),
				'publisher' => $this->input->post('publisher'),
				'format' => $this->input->post('format'),
				'isbn' => $this->input->post('isbn'),
				'category' => $this->input->post('category'),
				'price' => $this->input->post('price'),
				'product_qty' => $this->input->post('quantity'),
				'product_url' => url_title($this->input->post('title'))
				);







		if ($this->form_validation->run() == False) {

			$this->session->set_flashdata('edit_error', 'There is an error on the form field.');
			 #var_dump(validation_errors());
			 #die();
			redirect('administrator/book/'.$id.'');
		}else{
			$this->session->set_flashdata('edit_success', 'Book Successfully Edited!');
			$this->administrator_model->update_book($form_data,$id);

                        $this->administrator_model->check_if_email_to_wishlist($id);
			redirect('administrator/book/'.$id.'');
		}



	}

	public function category_add(){

		//Prepare Header Data
		$header['page_title'] = 'Administrator | Add Category';

		//Navigation
		$navigation['page_cur_nav'] = 'dashboard';



		//Page Header
		$this->parser->parse('template/header', $header);
		//Page Nav
		$this->load->view('template/navigation', $navigation);
		//Page Main Content
		$this->load->view('administrator/administrator_category_add_view');
		//Page Footer
		$this->load->view('template/footer');

	}




	public function category_add_validate(){

		$this->form_validation->set_rules('category', 'Category Name', 'trim|required|is_unique[category.name]');
		$form_data = array('name' => $this->input->post('category'));

		if ($this->form_validation->run() == FALSE) {
			redirect('administrator/category_add');
		}else{
			$this->session->set_flashdata('category_success', 'Category Successfully Added!');
			$this->administrator_model->insert_add_category($form_data);
			redirect('administrator/category_add');
		}

	}

	public function accountlist(){
		//Prepare Header Data
		$header['page_title'] = 'Administrator | Settings';

		//Navigation
		$navigation['page_cur_nav'] = 'dashboard';

		//Page Header
		$this->parser->parse('template/header', $header);
		//Page Nav
		$this->load->view('template/navigation', $navigation);
		//Page Main Content
		$this->load->view('administrator/administrator_accountlist_view');
		//Page Footer
		$this->load->view('template/footer');
	}

	public function account($id){

		$account['personal'] = $this->administrator_model->get_account($id);


		//Prepare Header Data
		$header['page_title'] = 'Administrator | Settings';

		//Navigation
		$navigation['page_cur_nav'] = 'dashboard';

		//Page Header
		$this->parser->parse('template/header', $header);
		//Page Nav
		$this->load->view('template/navigation', $navigation);
		//Page Main Content
		$this->parser->parse('administrator/administrator_account_view', $account);
		//Page Footer
		$this->load->view('template/footer');
	}

	public function settings(){
		//Prepare Header Data
		$header['page_title'] = 'Administrator | Settings';

		//Navigation
		$navigation['page_cur_nav'] = 'dashboard';

		//Page Header
		$this->parser->parse('template/header', $header);
		//Page Nav
		$this->load->view('template/navigation', $navigation);
		//Page Main Content
		$this->load->view('administrator/administrator_settings_view');
		//Page Footer
		$this->load->view('template/footer');

	}

	public function add_featured($id){
		$data = array('product_id' => $id);
		$this->db->select('*')->from('featured')->where('product_id', $id);
		$sql = $this->db->get();
		$num = $sql->num_rows();

		if($num == 1){
			$this->session->set_flashdata('featured', 'The book is already in the featured list.');	
			redirect('administrator/books');
		}else{
			$this->db->insert('featured',$data);
			$this->session->set_flashdata('featured', 'Book has been added to featured list.');	
			redirect('administrator/books');

		}
	}

	public function home_slider(){

		$this->load->library('upload');

		$files = $_FILES;
		$cpt = count($_FILES['userfile']['name']);
		for($i=0; $i<$cpt; $i++)
		{

			$_FILES['userfile']['name']= $files['userfile']['name'][$i];
			$_FILES['userfile']['type']= $files['userfile']['type'][$i];
			$_FILES['userfile']['tmp_name']= $files['userfile']['tmp_name'][$i];
			$_FILES['userfile']['error']= $files['userfile']['error'][$i];
			$_FILES['userfile']['size']= $files['userfile']['size'][$i];    

			$this->upload->initialize($this->set_upload_options($i));


			if(!$this->upload->do_upload()){
				$error = array('error' => $this->upload->display_errors());	
#var_dump($error);

			}
		}	
		if(!empty($error)){
			$this->session->set_flashdata('banner', 'There are errors while uploading the images.</br><pre>'.var_dump($error).'</pre> ');
		}else{
			$this->session->set_flashdata('banner', 'Banner has been uploaded.');
		}	

		redirect('administrator/settings');

	} 

	private  function set_upload_options($i){
		$i + 1;
		$config['file_name'] = 'content-slider-'.$i;
		$config['upload_path'] = './assets/img/slider/';
		$config['allowed_types'] = 'jpg';
		$config['max_size']	= '1000';
		$config['max_width'] = 1000;
		$config['max_height'] = 500;
		$config['overwrite'] = TRUE;
		return $config;

        }
        public function datatables_sales($date){
        $date = date('Y-m-d', strtotime($date));
        $this->datatables->select('order_id,order_total, dateorder, CONCAT(fname," ",lname) AS lname', FALSE)->from('orders')
          ->join('users', 'users.id = orders.user_id')->like('dateorder', $date);
        $datatables = $this->datatables->generate();
        echo $datatables;
        
        }
	public function datatables_orders_staff(){
		$this->datatables->select('order_id,order_total, dateorder, CONCAT(fname," ",lname) AS lname,package_status,order_status', FALSE)->from('orders')->join('users', 'users.id = orders.user_id');
		$datatables = $this->datatables->generate();
		echo $datatables;
	}

	public function datatables_orders(){
		$this->datatables->select('order_id,order_total, dateorder, CONCAT(fname," ",lname) AS lname,package_status,order_status',FALSE)->from('orders')->join('users', 'users.id = orders.user_id');
		$datatables = $this->datatables->generate();
		echo $datatables;
	}

	public function datatables_orders_by_id($id){
		$this->datatables->select('order_id,order_total, dateorder, CONCAT(fname," ",lname) AS lname,order_status',FALSE)->from('orders')->join('users', 'users.id = orders.user_id')->where('users.id',$id);
		$datatables = $this->datatables->generate();
		echo $datatables;
	}

	public function datatables_shipments(){
		$this->datatables->select('shipment_id, shipment_date,order_total, dateadd, lname,order_status')->from('shipments')->join('orders', 'shipments.order_id = orders.order_id')->join('users', 'users.id = orders.user_id');
		$datatables = $this->datatables->generate();
		echo $datatables;
	}

	public function datatables_books(){
		$this
			->datatables
			->select('product_id, title, author, category,book_wishlist, book_sold,product_qty, price, dateadd')
			->from('books');

		$datatables = $this->datatables->generate('JSON');
		echo $datatables;
	}

	public function datatables_accounts(){
		$this
			->datatables->select('id, fname, lname, email, user_type, mobile')
			->from('users');

		$datatables = $this->datatables->generate('JSON');
		echo $datatables;

	}


	/*** AJAX REQUESTS FOR STATUS CHANGE ***/

	public function post_change_status(){

		$status = $this->input->post('status');
		$order_id = $this->input->post('order_id');
		$status_arr = array('order_status' => $status);

		//Update
		$this->db->where('order_id', $order_id);
		$this->db->update('orders', $status_arr);
		//Retrieve
		$this->db->select('order_status')->from('orders')->where('order_id', $order_id);
		$sql = $this->db->get();
		$result = $sql->result_array();

		//Send this message back to HTML
		echo $result[0]['order_status'];

	}

	/*** AJAX REQUEST FOR USER TYPE CHANGE ***/
	public function post_change_user_type(){

		$status = $this->input->post('status');
		$user_id = $this->input->post('user_id');
		$status_arr = array('user_type' => $status);

		//Update
		$this->db->where('id', $user_id);
		$this->db->update('users', $status_arr);
		//Retrieve
		$this->db->select('user_type')->from('users')->where('id', $user_id);
		$sql = $this->db->get();
		$result = $sql->result_array();

		//Send this message back to HTML
		echo $result[0]['user_type'];
	}

	/*** AJAX REQUEST FOR GOOGLE CHARTS ***/

	public function get_bookstore_data(){

		$this->db->select('dateorder, order_total')->from('orders')->where('order_status', 'Approved');
		$sql = $this->db->get();
		$result = $sql->result_array();
		$result2 = $result;


		$i = 0;
		$day_total = array();
		$tableHeader = ['Month', 'Monthly Income'];
		$day_total[] = $tableHeader;
		foreach($result as $key => $value) {
			$odate = date('y-m', strtotime($value['dateorder']));
			$total = 0;
			foreach($result2 as $key2 => $value2) {
				$idate = date('y-m', strtotime($value2['dateorder']));
				if ($odate == $idate) {
					$total = $total + $value2['order_total']; 			
				}
			}

			if ($day_total[$i][0] == $odate && $day_total[$i][1] == $total) {
				continue;
			}else{
				$day_total[] = [$odate, $total];
			}


			$i++;


		}

		/* $rdata = [
		   ['Year', 'Sales', 'Expenses'],
		   ['2004',  1000,      400],
		   ['2005',  1170,      460],
		   ['2006',  660,       1120],
		   ['2007',  1030,      540]]; */

# echo '<pre>';
# var_dump($data);
		echo json_encode($day_total);


	}


	/*** AJAX REQUEST FOR GOOGLE CHARTS DAYS***/

	public function get_bookstore_data_days(){

		$this->db->select('dateorder, order_total')->from('orders')->where('order_status', 'Approved');
		$sql = $this->db->get();
		$result = $sql->result_array();
		$result2 = $result;


		$i = 0;
		$day_total = array();
		$tableHeader = ['Days', 'Daily Income'];
		$day_total[] = $tableHeader;
		foreach($result as $key => $value) {
			$odate = date('d-m-Y', strtotime($value['dateorder']));
			$total = 0;
			foreach($result2 as $key2 => $value2) {
				$idate = date('d-m-Y', strtotime($value2['dateorder']));
				if ($odate == $idate) {
					$total = $total + $value2['order_total'];			
				}
			}

			if ($day_total[$i][0] == $odate && $day_total[$i][1] == $total) {
				continue;
			}else{
				$day = date('d-m-Y', strtotime($value['dateorder']));
				$day_total[] = [$day, $total];
			}


			$i++;


		}

		echo json_encode($day_total);




#echo $day_total[1][0];


		// foreach ($day_total as $key => $value) {
		// 	if ($value[0] != '2013-12-29') {
		// 		echo 'he';
		// 	}

		// 	var_dump($value);
		// }

		// echo '<pre>';
		// var_dump($day_total);
#echo json_encode($day_total);


		// $month  = array('01' => '2014-02-08', '02' => '2014-02-09','03' => '2013-12-03');
		//       $data = [];
		//       $tableHeader = ['Day', 'Daily Income'];
		//       $data[] = $tableHeader;

		//       foreach ($month as $key => $value) {
		//       	$result = $this->db->query("SELECT dateorder, SUM(order_total) AS total_order FROM orders WHERE dateorder LIKE '%".$value."%' AND order_status = 'Approved' ");
		//       	$result = $result->result_array();

		//       	$total_val = (int)$result[0]['total_order'];
		//       	$date_month = (string)date('F m d', strtotime($result[0]['dateorder']));

		// 	$data[] = [$date_month,$total_val];        	
		//       }

		//         /* $rdata = [
		//         ['Year', 'Sales', 'Expenses'],
		//         ['2004',  1000,      400],
		//         ['2005',  1170,      460],
		//         ['2006',  660,       1120],
		//         ['2007',  1030,      540]]; */
	}

	public function get_bookstore_data_weeks(){
		$sql = $this->db->query("SELECT dateorder, SUM(order_total) AS weekly_sales FROM orders WHERE order_status = 'Approved' GROUP BY week(dateorder) ORDER BY dateorder ASC");
		$result = $sql->result_array();


		$tableHeader = ['Weeks', 'Weekly Income'];
		$weekly[] = $tableHeader;

		foreach($result as $key => $value){
			$week = date('y', strtotime($value['dateorder'])) .'-'.date('m-d', strtotime($value['dateorder']));
			$weekly[] = [$week, (int)$value['weekly_sales']];
		}

		echo json_encode($weekly);
#var_dump($weekly);

	}

        public function get_bookstore_sales_data($date){
        
                $sql = $this->db->query("SELECT order_id, SUM(order_total) as sales FROM orders WHERE order_status = 'Approved' AND dateorder LIKE '%$date%' GROUP BY dateorder ORDER BY dateorder ASC");
                $result = $sql->result_array();
                
                $tableHeader = ['Sales', 'Income'];
		$weekly[] = $tableHeader;

		foreach($result as $key => $value){
			$weekly[] = [$value['order_id'], (int)$value['sales']];
		}

		echo json_encode($weekly);


        }

}

/* End of file administrator.php */
/* Location: ./application/controllers/administrator.php */
