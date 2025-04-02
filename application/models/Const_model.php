<?php 
class Const_model extends CI_Model {
  
	public function timezone() {
        return getenv('CLUB_TIMEZONE');
    }

	public function clubname() {
        return getenv('CLUB_NAME');
    }

	public function clubshortname() {
        return getenv('CLUB_SHORTNAME');
    }

	public function clubemail() {
		return getenv('CLUB_EMAIL');
	}


}