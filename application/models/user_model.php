<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends BaseModel
{
    protected $table = 'user';
    protected $fields = array(
        'id' => array(
            'type' => 'INT',
            'unsigned' => true,
            'auto_increment' => true,
            'primary_key' => 'id',
        ),
        'name' => array(
            'type' => 'VARCHAR',
            'constraint' => 50,
            'default' => 'unknown'
        ),
        'password' => array(
            'type' => 'VARCHAR',
            'constraint' => 50,
        )
    );

    public function __construct()
    {
        parent::__construct();
    }
}