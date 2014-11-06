<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller
{
    public function index()
    {
        $this->load->view('welcome_message');
    }

    public function check()
    {
        $data = intval($this->input->post());
        $this->db
            ->from('test')
            ->where('id', $data['id'])
            ->where('option', $data['option'])
            ->get()
            ->row_array();
    }
}