<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model
{
    function __construct() {
        $this->primaryKey = 'id';
    }
    public function checkUser($data = array()){
	    
	  $prevQuery =  $this->db->select($this->primaryKey)->get_where("fb_users", array('oauth_provider'=>$data['oauth_provider'],'oauth_uid'=>$data['oauth_uid']));
        $prevCheck = $prevQuery->num_rows();
        
        if($prevCheck > 0){
            $prevResult = $prevQuery->row_array();
            $data['modified'] = date("Y-m-d H:i:s");
            $update = $this->db->update('fb_users', $data, array('id'=>$prevResult['id']));
            $userID = $prevResult['id'];
        }else{
            $data['created'] = date("Y-m-d H:i:s");
            $data['modified'] = date("Y-m-d H:i:s");
            $insert = $this->db->insert('fb_users' ,$data);
            $userID = $this->db->insert_id();
        }

        return $userID?$userID:FALSE;
    }
}