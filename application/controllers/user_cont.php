<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_cont extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		header('Content-Type: application/json');
		error_reporting(2048);
	}
	public function index()
	{
		$info = array();
		$info['status'] = 'Welcome';
		$info['name'] = 'Tran Ngoc Huy';
		$info['personal Infomation'] = array('phone' => '(+84)1638007223','email' => 'ne.4get2903@gmail.com', 'address' => 'KTXB VNU' ,'birth' => '01/10/1995');
		$info['hobbies'] = array('Dota2', 'Gym', 'Photograhp', 'Travel', 'Swim', 'High-tech equipment', 'Mech Keyboard', 'Gaming Gear');
		$info['Soft skill'] = array('research', 'Listening', 'Teamwork', 'Flexibility', 'Determination', 'Opportunist');
		$info['Working skill'] = array('HTML/CSS/Javascript', 'PHP & Framework', 'Photoshop', 'OOP', 'MVC', 'Database design', 'Modern system analysis & design');
		echo json_encode($info);
	}
	public function getuser()
	{
		if ($this->session->userdata('username')) {
			echo json_encode($this->session->userdata('username'));
		}
		else
		{
			$result['status'] = 'fail';
			$result['mess'] = 'User not login';
			echo json_encode($result);
		}
	}
	public function logout()
	{
		if($this->session->userdata('username'))
		{
			$this->session->unset_userdata('username');
			$result['status'] = 'success';
			$result['mess'] = 'logout success';
			echo json_encode($result);
		}
		else
		{
			$result['status'] = 'fail';
			$result['mess'] = 'User not login';
			header("HTTP/1.1 404 Not Found");
			echo json_encode($result);
		}
	}
	public function login()
	{
		$this->load->model('user_model');
		if ($this->input->post('username') && $this->input->post('pass')) {
			$username = $this->input->post('username');
			$pass = md5(sha1($this->input->post('pass')));
			if ($this->user_model->checklogin($username, $pass))
			{
				$result['status'] = 'success';
				$result['mess'] = 'Login success';
				$this->session->set_userdata('username', $username);
				echo json_encode($result);
			}
			else
			{
				header("HTTP/1.1 404 Not Found");
				$result['status'] = 'fail';
				$result['mess'] = 'Login failed';
				echo json_encode($result);
			}
		}
		else
		{
			$result['status'] = 'fail';
			if (!$this->input->post('username')) {
				$result['error']['username'] = 'Not found username field';
			}
			if (!$this->input->post('pass')) {
				$result['error']['pass'] = 'Not found pass field';
			}
			echo json_encode($result);
		}
	}
	public function user($username = '', $image = '')
	{
		// load thư viện và model CI
		$this->load->library('form_validation');
		$this->load->helper('form');
		$this->load->model('user_model');
		//kiểm tra username
		if ($username != '')
		{
			if ($this->session->userdata('username') === $username)
			{
				if ($image)
				{
					if ($image == 'image')
					{
						$user = $username;
						$where['username'] = $username;
						$id = $this->user_model->getuserid($where);
						$info = $this->user_model->getuserinfo($id);
						$data['info'] = $info;
						$filename = $_FILES['avata']['name'];
						$filedata = $_FILES['avata']['tmp_name'];
						$filetype = $_FILES['avata']['type'];
						$filesize = $_FILES['avata']['size'];
						if ($filedata) {
							$uploadpath = "upload/";
							$des =  md5(rand()).$filename;
							if (copy($filedata, $uploadpath.$des))
							{
						    	$avata['avata'] = $des;
						    	if ($this->user_model->updateinfo($avata, $id))
						    	{
						    		$result['status'] = 'success';
						    		$result['mess'] = 'Insert success';
						    		echo json_encode($result);
						    	}
						    	else
						    	{
						    		$result['status'] = 'fail';
						    		$result['mess'] = 'Insert fail';
						    		echo json_encode($result);
						    	}
						    }
						    else
						    {
						    	$result['status'] = 'fail';
						    	$result['mess'] = 'Can not upload';
						    	echo json_encode($result);
						    }
						}
						else
						{
							$result['status'] = 'fail';
							$result['mess'] = 'Photo not found';
							echo json_encode($result);
						}
					}
					else
					{
						header("HTTP/1.1 404 Not Found");
						$result['status'] = 'fail';
						$result['mess'] = 'Wrong link';
						echo json_encode($result);
					}

				}
				// có username nhưng không yêu cầu đổi hình ảnh
				else
				{
					if ($_SERVER['REQUEST_METHOD'] == 'GET')
					{
						if ($_SERVER['PHP_AUTH_USER']) {
							$user = $_SERVER['PHP_AUTH_USER'];
							$password = $_SERVER['PHP_AUTH_PW'];
							if ($user === 'wsgroup' && $password === 'proudtobehere')
							{
								$user = $username;
								$where['username'] = $user;
								if ($id = $this->user_model->getuserid($where)) {
									if ($info = $this->user_model->getuserinfo($id)) {
										foreach ($info as $key => $value) {
											if ($key === 'username' || $key === 'name' || $key === 'avata' || $key === 'email') {
												$result[$key] = $value;
											}
										}
										echo json_encode($result);
									}
									else
									{
										$result['status'] = 'fail';
										$result['mess'] = 'Invalid username';
										echo json_encode($result);
									}
								}
								else
								{
									$result['status'] = 'fail';
									$result['mess'] = 'Invalid username';
									echo json_encode($result);
								}
							}
							else
							{
								$result['status'] = 'fail';
								$result['mess'] = 'Wrong Basic Auth account';
								echo json_encode($result);
							}
						}
						else
						{
							$result['status'] = 'fail1';
							$result['mess'] = 'Wrong Basic Auth account';
							echo json_encode($result);
						}
					}
					if ($_SERVER['REQUEST_METHOD'] == 'PUT')
					{
						$user = $username;
						$where['username'] = $user;
						if ($id = $this->user_model->getuserid($where))
						{
							parse_str(file_get_contents('php://input'), $requestData);
							foreach ($requestData as $key => $value)
							{
								if ($key === 'id' || $key == 'username' || $key == 'role')
								{
									$result['status'] = 'fail';
									$result['mess'] ='Wrong field';
									echo json_encode($result);
									die();
								}
								else
								{
									$data[$key] = $value;
								}
							}
							if ($this->user_model->updateinfo($data, $id))
							{
								$result['status'] = 'success';
								$result['mess'] = 'Update success';
								echo json_encode($result);
							}
							else
							{
								$result['status'] = 'fail';
								$result['mess'] = 'Update fail';
								echo json_encode($result);
							}
						}
						else
						{
							header("HTTP/1.1 404 Not Found");
							$result['status'] = 'fail';
							$result['mess'] = 'Invalid username';
							echo json_encode($result);
						}
					}
				}
			}
			else
			{
				header("HTTP/1.1 404 Not Found");
				$result['status'] = 'fail';
				$result['mess'] = 'not access to link';
				echo json_encode($result);
			}
		}
		//không có username. đến trang đăng ký
		else
		{
			$this->form_validation->set_rules('user', 'Username', 'trim|required|callback_check_username');
			$this->form_validation->set_rules('pass', 'Password', 'trim|required');
			$this->form_validation->set_rules('pass_r', 'Password', 'trim|required|matches[pass]');
			$this->form_validation->set_rules('name', 'Name', 'required');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|callback_check_email');
			if ($this->form_validation->run())
			{
				$regist = array(
				'username' => $this->input->post('user'),
				'pass' => md5(sha1($this->input->post('pass'))),
				'name' => $this->input->post('name'),
				'email' => $this->input->post('email')
				);
				if ($this->user_model->adduser($regist))
				{
					$result['status'] = 'success';
					$result['mess'] = 'Regist success';
					echo json_encode($result);
				}
				else
				{
					$result['status'] = 'fail';
					$result['mess'] = 'Regist fail';
					echo json_encode($result);
				}
			}
			else
			{
				if (form_error('user')) {
					$error['user'] = form_error('user');
				}
				if (form_error('pass')) {
					$error['pass'] = form_error('pass');
				}
				if (form_error('pass_r')) {
					$error['pass_r'] = form_error('pass_r');
				}
				if (form_error('name')) {
					$error['name'] = form_error('name');
				}
				if (form_error('email')) {
					$error['email'] = form_error('email');
				}
				$result['status'] = 'fail';
				$result['error'] = $error;
				echo json_encode($result);
			}
		}
	}
	//kiểm tra tồn tại username
	public function check_username($user = '')
	{
		$this->load->library('form_validation');
		$this->load->model('user_model');
		if (!$this->user_model->checkusername($user)) {
			return true;
		}
		else
		{
			$this->form_validation->set_message('check_username', 'Username đã tồn tại');
			return false;
		}
	}
	//kiểm tra tồn tại email
	public function check_email($email = '')
	{
		$this->load->library('form_validation');
		$this->load->model('user_model');
		if (!$this->user_model->checkemail($email)) {
			return true;
		}
		else
		{
			$this->form_validation->set_message('check_email', 'Email đã tồn tại');
			return false;
		}
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */