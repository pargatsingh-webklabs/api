<?php
if (!defined('BASEPATH'))
exit('No direct script access allowed');
class Api extends CI_Controller {
    public function __construct() {
        parent::__construct();
		$this->load->model('User_model');
		$this->load->model('User_token_model');
		$this->load->helper('Token');
	}
	
	public function login(){
	    $userdata=$this->input->post();
	    $addexpire_at_time = $this->config->item('addexpire_at_time');
		if(empty($userdata['password']) || empty($userdata["email"])){
			echo json_encode(array("result"=>"error","data"=>"Email and password cannot be empty"));die;
		}
		$userdata = $this->User_model->get_Users($userdata);
		if(empty($userdata)){
			echo json_encode(array("result"=>"error","data"=>"Wrong credentials"));
			die;
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
			   echo json_encode(array("result"=>"success","data"=>$userarray));die;
			} else{
			    echo json_encode(array("result"=>"error","data"=>"There is some error generating tokens, Please try again"));die;
			    
			}
		}else{
		        $userarray["user_id"]=$usertoken['user_id'];
				$userarray["refresh_token"]=$usertoken['refresh_token'];
				$userarray["access_token"]=$usertoken['access_token'];
				$userarray["expire_at"]=$usertoken['expire_at'];
				echo json_encode(array("result"=>"success","data"=>$userarray));die;
		}
	
    }	
    
    public function register(){
        $userdata=$this->input->post();
		if(empty($userdata["fname"]) || empty($userdata["lname"]) || empty($userdata["email"]) || empty($userdata["password"]) || empty($userdata["dob"])){
			echo json_encode(array("result"=>"error","data"=>"Please fill out all fields"));die;
		}
	
		$check_user = $this->User_model->check_user($userdata["email"]);
		if(empty($check_user)){
			$userdata = $this->User_model->save_user($userdata);
			if($userdata){
				echo json_encode(array("result"=>"success","data"=>"User created successfully"));die;		
			}else{
				echo json_encode(array("result"=>"error","data"=>"Error while registration"));die;	
			}
		}else{
			echo json_encode(array("result"=>"error","data"=>"User with same email already registered"));die;
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
					echo json_encode(array("result"=>"success","data"=>$userdata));die;		
				}else{
					echo json_encode(array("result"=>"error","data"=>"No user exist"));die;	
				}
			}else{
				echo json_encode(array("result"=>"error","data"=>"Invalid token"));die;
			}
		
	}
	
	public function token(){
	
	        $addexpire_at_time = $this->config->item('addexpire_at_time');
			$userdata['refresh_token']=$this->input->post("refresh_token");
			$userdata['user_id']=$this->input->post("user_id");
			if(!$this->User_token_model->validateAccessToken($userdata)){
			    echo json_encode(array("result"=>"error","data"=>"Invalid token"));die;
			}
			
			$usertoken_data = $this->User_token_model->get_refreshtoken_data($userdata);
			
			
			
			if(!empty($usertoken_data)){
			    unset($usertoken_data["id"]);
				echo json_encode(array("result"=>"success","data"=>$usertoken_data));die;
			}else{
			    $data['user_id'] = $userdata['user_id'];
                $data['refresh_token'] = $userdata['refresh_token'];
                $data['access_token'] = generate_access_token();
                $data['expire_at'] = time()+$addexpire_at_time*60*60;
                $data['status'] = '1';
                $data['created'] = time();
                $userarray=$this->User_token_model->generate_token($data);	
                if(!empty($userarray)){
    			   echo json_encode(array("result"=>"success","data"=>$userarray));die;
    			} else{
    			    echo json_encode(array("result"=>"error","data"=>"There is some error generating tokens, Please try again"));die;
    			    
    			}
			}
			
		
	}
   
   
}