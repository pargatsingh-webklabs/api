<?php
/**
 * Super Class
 *
 * @package     Package Name
 * @subpackage  Subpackage
 * @category    Category
 * @author      Author Name
 * @link        http://example.com
 */
ob_start();
class User_model extends CI_Model {
	function __construct() {
		parent::__construct();
	}
	
	
	public function save_user($data=array()){
	    $insertarray = array('fname'=>$data['fname'],'lname'=>$data['lname'],'email'=>$data['email'],'password'=>md5($data['password']),'dob'=>$data['dob'],'status'=>1,'created'=>time());
		if($this->db->insert('user', $insertarray)){
		   $last_id = $this->db->insert_id();
		   //~ $GuestData = $this->db->select("*")->from("users")->where(array("id"=>$last_id,"isDeleted"=>0))->get()->row_array();
		   return true;
		}else{
			return false;
		}	
	}
	
	public function check_user($email){
		 return $userdata = $this->db->select("*")->from("user")->where(array("email" => $email))->get()->row_array();
	}
	
	public function get_Users($userdata){
		return $userData = $this->db->select("*")->from("user")->where(array("email" => $userdata['email'], "password" => md5($userdata['password'])))->get()->row_array();
	}
	
	public function get_user($id){
		return $userData = $this->db->select("*")->from("user")->where(array("id"=>$id))->get()->row_array();
	}
	
	/*     * *********************************************** Admin login functions ends ************************************************* */
}	