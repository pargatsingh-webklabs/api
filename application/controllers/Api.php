<?php
if (!defined('BASEPATH'))
exit('No direct script access allowed');
class Api extends CI_Controller {
    public function __construct() {
        parent::__construct();
		$this->load->model('User_model');
		$this->load->model('User_token_model');
		$this->load->model('Feed_model');
		$this->load->helper('Token');
	}
	
	public function login(){
	    $userdata=$this->input->post();
	    $addexpire_at_time = $this->config->item('addexpire_at_time');
		if(empty($userdata['password']) || empty($userdata["email"])){
			return $this->output->set_content_type('application/json')->set_status_header(401)->set_output(json_encode(['message' => 'Email and password cannot be empty']));
		}
		$userdata = $this->User_model->get_Users($userdata);
		if(empty($userdata)){
			return $this->output->set_content_type('application/json')->set_status_header(401)->set_output(json_encode(['message' => 'Wrong credentials']));
		}
		
		$usertoken = $this->User_token_model->get_usertoken($userdata);
		
		if(empty($usertoken)){
		    $refresh_token_data = generate_refresh_token();
		    $accestoken_data = generate_access_token();
		    $data['user_id'] = $userdata['id'];
            $data['refresh_token'] = $refresh_token_data;
            $data['access_token'] = $accestoken_data;
            $data['expire_at'] = time()+$addexpire_at_time*60*60;
            $data['status'] = '1';
            $data['created'] = time();
            $userarray=$this->User_token_model->generate_token($data);
            if(!empty($userarray)){
				return $this->output->set_content_type('application/json')->set_status_header(200)->set_output(json_encode($userarray));
			} else{
				return $this->output->set_content_type('application/json')->set_status_header(401)->set_output(json_encode(['message' => 'There is some error generating tokens, Please try again']));
			    
			}
		}else{
		        $userarray["user_id"]=$usertoken['user_id'];
				$userarray["refresh_token"]=$usertoken['refresh_token'];
				$userarray["access_token"]=$usertoken['access_token'];
				$userarray["expire_at"]=$usertoken['expire_at'];
				return $this->output->set_content_type('application/json')->set_status_header(200)->set_output(json_encode($userarray));
		}
	
    }	
    
    public function register(){
        $userdata=$this->input->post();
		if(empty($userdata["fname"]) || empty($userdata["lname"]) || empty($userdata["email"]) || empty($userdata["password"]) || empty($userdata["dob"])){
			return $this->output->set_content_type('application/json')->set_status_header(401)->set_output(json_encode(['message' => 'Please fill out all fields']));
		}
	
		$check_user = $this->User_model->check_user($userdata["email"]);
		if(empty($check_user)){
			$userdata = $this->User_model->save_user($userdata);
			if($userdata){
				return $this->output->set_content_type('application/json')->set_status_header(200)->set_output(json_encode(['message' => 'User created successfully']));	
			}else{
				return $this->output->set_content_type('application/json')->set_status_header(401)->set_output(json_encode(['message' => 'Error while registration']));
			}
		}else{
			return $this->output->set_content_type('application/json')->set_status_header(401)->set_output(json_encode(['message' => 'User with same email already registered']));
		}
	}
  
	public function profile(){
	
			$headers=array();
			
			foreach (getallheaders() as $name => $value) {
				$headers[$name] = $value;
			}	
			$userdata["access_token"]=$headers['access_token'];
			$userdata=$this->input->post();
			$usertoken_data = $this->User_token_model->get_token_data($userdata);
			if(!empty($usertoken_data)){
				$userdata = $this->User_model->get_user($usertoken_data['user_id']);
				if($userdata){
					return $this->output->set_content_type('application/json')->set_status_header(200)->set_output(json_encode($userdata));
					
				}else{
					return $this->output->set_content_type('application/json')->set_status_header(401)->set_output(json_encode(['message' => 'No user exist']));
						
				}
			}else{
				return $this->output->set_content_type('application/json')->set_status_header(401)->set_output(json_encode(['message' => 'Invalid token']));
			}
		
	}
	
	public function token(){
	
	        $addexpire_at_time = $this->config->item('addexpire_at_time');
			$userdata['refresh_token']=$this->input->post("refresh_token");
			$userdata['user_id']=$this->input->post("user_id");
			if(!$this->User_token_model->validateAccessToken($userdata)){
				return $this->output->set_content_type('application/json')->set_status_header(401)->set_output(json_encode(['message' => 'Invalid token']));
			}
			
			$usertoken_data = $this->User_token_model->get_refreshtoken_data($userdata);
			
			
			
			if(!empty($usertoken_data)){
			    unset($usertoken_data["id"]);
			    return $this->output->set_content_type('application/json')->set_status_header(200)->set_output(json_encode($usertoken_data));
				
			}else{
			    $data['user_id'] = $userdata['user_id'];
                $data['refresh_token'] = $userdata['refresh_token'];
                $data['access_token'] = generate_access_token();
                $data['expire_at'] = time()+$addexpire_at_time*60*60;
                $data['status'] = '1';
                $data['created'] = time();
                $userarray=$this->User_token_model->generate_token($data);	
    			
    			if (!empty($userarray)) {
                return $this->output->set_content_type('application/json')->set_status_header(200)->set_output(json_encode($userarray));
				} else {
				return $this->output->set_content_type('application/json')->set_status_header(401)->set_output(json_encode(['message' => 'There is some error generating tokens, Please try again']));
				}
    			
			}
			
		
	}
	
	public function feed(){
		$postdata = $this->input->post();
		if(@$postdata){
		
			if(empty($postdata["title"]) || empty($postdata["description"]) || empty($postdata["created_at"])){
				echo json_encode(array("result"=>"error","data"=>"Please fill out all fields"));die;
			}
			
			$result = $this->Feed_model->save_feed($postdata);
				
			if ($result) {
                return $this->output->set_content_type('application/json')->set_status_header(200)->set_output(json_encode(['message' =>'Feed added successfully']));
            } else {
                return $this->output->set_content_type('application/json')->set_status_header(401)->set_output(json_encode(['message' => 'There is some error while adding feeds']));
            }	

		}
		
		    $count_feeds = $this->Feed_model->count_feeds();
		    $per_page = $this->config->item('per_page_records');
		    $number_of_pages = ceil($count_feeds/$per_page);
			$page = ($_GET['page']) ? $_GET['page'] : 1;
			$page_limit = ($page-1) * $per_page;	
			$get_feeds = $this->Feed_model->get_feeds($per_page,$page_limit);
			
			if (!empty($get_feeds)) {
				$result_data = array("items"=>$get_feeds,"num_pages"=>$number_of_pages,"page"=>$page);
                return $this->output->set_content_type('application/json')->set_status_header(200)->set_output(json_encode($result_data));
            } else {
                return $this->output->set_content_type('application/json')->set_status_header(401)->set_output(json_encode(['message' => 'No record found']));
            }
		   
	}
	
   
	
   
   
}
