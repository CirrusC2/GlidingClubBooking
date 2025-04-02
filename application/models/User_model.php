<?php 
class User_model extends CI_Model {
    protected $User_table_name = "members";
    /**
     * Insert User Data in Database
     * @param: {array} userData
     */
    public function insert_user($userData) {
        return $this->db->insert($this->User_table_name, $userData);
    }
    /**
     * Check User Login in Database
     * @param: {array} userData
     */
    public function check_login($userData) {
        /**
         * First Check Email is Exists in Database
         */
        $query = $this->db->get_where($this->User_table_name, array('email' => $userData['email']));
        if ($this->db->affected_rows() > 0) {
            $password = $query->row('password');
            /**
             * Check Password Hash 
             */
            if (password_verify($userData['password'], $password) === TRUE) {
                /**
                 * Password and Email Address Valid
                 */
                return [
                    'status' => TRUE,
                    'data' => $query->row(),
                ];
            } else {
                return ['status' => FALSE,'data' => FALSE];
            }
        } else {
            return ['status' => FALSE,'data' => FALSE];
        }
    }
    
    public function record_login($user_id) {
        date_default_timezone_set($this->ConstModel->timezone());
        $now = date('Y-m-d h:i:s a', time());
        $data = array('last_login'=>$now);
        $this->db->where('id', $user_id);
        $update = $this->db->update('members', $data);
        $this->log($user_id, "logged in");
    }
    
    public function log($user_id, $action) {
        $member = $this->get_member($user_id);
        if ($member === false) {
            return;
        }
        $user = $member->email;
        $insert = array('user'=>$user, 'action'=>$action, 'datetime'=>date('Y-m-d h:i:s a', time()));
        $this->db->insert('log', $insert);
    }
    
    public function check_unique($data) {
        $email = $data['email'];
        $this->db->where('email', $email);
        $query = $this->db->get('members');
        if($query->num_rows() > 0) {
            return 0;
        } else {
            return 1;
        }
    }
    
    public function registration_key() {
        $key = getenv('NEW_MEMBER_REGISTRATION_KEY');
        log_message('debug', 'NEW_MEMBER_REGISTRATION_KEY value: ' . $key);
        return $key;
    }
    
    public function check_email($userData) {
        /**
         * First Check Email is Exists in Database
         */
        $query = $this->db->get_where($this->User_table_name, array('email' => $userData['email']));
        if ($this->db->affected_rows() > 0) {
            return array('status'=>true, 'id'=>$query->row()->id);
        } else {
            return array('status'=>false);
        }
    }
    
    public function get_member($member_id) {
        $query = $this->db->query("SELECT * FROM `members` WHERE `id`='$member_id'");
        if($query->num_rows()) {
            return $query->row();
        } else {
            return false;
        } 
        
    }
    
    public function get_all_qualifications() {
        $query = $this->db->query("SELECT * FROM `quals_meta`");
        if($query->num_rows() > 0) {
            return $query->result();
        } else {
            log_message('info', 'No qualifications found in quals_meta table');
            return array();
        }
    }
    
    public function get_unselected_qualifications($member_id) {
        $query = $this->db->query("SELECT * FROM `quals_meta`");
        $result = array();
        if($query->num_rows() > 0) {
            foreach($query->result() as $row) {
                $this_qual_id = $row->id;
                $check = $this->db->query("SELECT * FROM `quals` WHERE `member_id`='$member_id' AND `qual_id`='$this_qual_id'");
                if($check->num_rows() == 0) {
                    $result[] = "<option value='$this_qual_id'>$row->title</option>";
                }
            }
        }
        return $result;
    }

    public function get_qualifications($member_id) {
        $query = $this->db->query("SELECT * FROM `quals` WHERE `member_id`='$member_id'");
        if($query->num_rows() > 0) {
            $result = array();
            foreach($query->result() as $row) {
                $this_qual = $row->qual_id;
                $qual_query = $this->db->query("SELECT * FROM `quals_meta` WHERE `id`='$this_qual'");
                if($qual_query->num_rows() > 0) {
                    $result[] = "<div class='alert alert-dark qual' qual_row='$row->id'>" . $qual_query->row()->title . "<button type='button' class='close' data-dismiss='alert' aria-label='Remove Qualification'><span aria-hidden='true'>&times;</span></button></div>";
                }
            }
            return $result;
        } else {
            return false;    
        }
    }
}