<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent:: __construct();
        $this->load->model('user_model');
        $this->load->helper('url');
        $this->layout->setLayout('/layouts/main');
    }

    public function create()
    {
        $user = $this->uri->segment(3);
        $password = $this->uri->segment(4);

        $this->user_model->create(
            array(
                'name' => $user,
                'password' => $password
            )
        );
    }

    public function show()
    {
        $users = $this->user_model->findAll()->orderBy('id')->ToArray();
        $this->layout->view('welcome_message', array('users' => $users));
    }
}