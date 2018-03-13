<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_Authentication extends CI_Controller
{
	function __construct(){
		 parent::__construct(); 
		 // Load session library
		 $this->load->library('session');
		 // Load facebook library
		 $this->load->library('facebook');
		 // load user model
		 $this->load->model('user_model');
		 
		 $this->load->helper('url');
	}
	
	public function index()
	{
		$userData = array();
		
		if($this->facebook->is_authenticated()){
			$userProfile = $this->facebook->request('get', 'me?fields=id,first_name,last_name,email,gender,locale,picture');
			
			$userData['oauth_provider'] = 'facebook';
			$userData['oauth_uid'] = $userProfile['id'];
			$userData['first_name'] = $userProfile['first_name'];
			$userData['last_name'] = $userProfile['last_name'];
			$userData['email'] = $userProfile['email'];
			$userData['gender'] = $userProfile['gender'];
			$userData['locale'] = $userProfile['locale'];
			$userData['profile_url'] = 'https://www.facebook.com/'.$userProfile['id'];
			$userData['picture_url'] = $userProfile['picture']['data']['url'];
			
			// Insert or Update user data
			$userID = $this->user_model->checkUser($userData);
			
			if(!empty($userID)){
				$data['userData'] = $userData,
				$this->session->set_userdata('userData', $userData);
			}else{
				$data['userData'] = array();
			}
			
			// Logout URL
			$data['logoutUrl'] = $this->facebook->logout_url();
		}else{
			$fbuser = '';
			
			// Login URL
			$data['authUrl'] = $this->facebook->login_url();
		}
		
		// Load view
		$this->load->view('auth/index', $data);
	}
	
	public function logout()
	{
		// remove facebook session
		$this->facebook->destroy_session();
		
		// remove user session
		$this->session->unset_userdata('userData');
		
		// Redirect to login page
		redirect('/auth');
	}
	
}