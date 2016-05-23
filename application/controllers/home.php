<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		header('Content-Type: application/json');
	}
	public function index()
	{
		echo json_encode("welcome ahihi. I dont know what i'm doing. something wrong here");
	}

}

/* End of file home.php */
/* Location: ./application/controllers/home.php */