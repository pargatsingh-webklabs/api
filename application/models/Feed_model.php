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
class Feed_model extends CI_Model {
	function __construct() {
		parent::__construct();
	}
	
	public function save_feed($data=array()){
	    $insertarray = array('title'=>$data['title'],'description'=>$data['description'],'created_at'=>$data['created_at']);
		if($this->db->insert('feed', $insertarray)){
		   $last_id = $this->db->insert_id();
		   return true;
		}else{
			return false;
		}	
	}
	
	public function count_feeds(){
		 return $userdata = $this->db->select("*")->from("feed")->get()->num_rows();
	}
	
	public function get_feeds($limit, $start){
		$userData = $this->db->select("*")->from("feed")->limit($limit, $start)->get()->result_array();
		if(!empty($userData)){
			return $userData;
		}else{
			return false;
		}
		//~ echo $this->db->last_query();die;
	}
   
	//~ public function get_user($id){
		//~ return $userData = $this->db->select("*")->from("user")->where(array("id"=>$id))->get()->row_array();
	//~ }
	
	/*     * *********************************************** Admin login functions ends ************************************************* */
}	
