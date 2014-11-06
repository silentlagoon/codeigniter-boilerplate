CodeIgniter-boilerplate
=======================

# Features:

* Simple layout support
* Simple CRUD model

```
//Model example
class User_model extends BaseModel
{
    protected $table = 'user';

    public function __construct()
    {
        parent::__construct();
    }
}
```


Extending from BaseModel gives you ability for using simple CRUDE. To make your queries like this:
	model_name - name of the model, at models directory.
	
	1) Reading example:
	a) $this->model_name->findOne(1);
	 producing SELECT * FROM tbl_name WHERE id = 1 LIMIT 1;

	b)$where = array('name' => 'Mike')
	$this->model_name->findOne(where);
	producing SELECT * FROM tbl_name WHERE name = 'Mike' LIMIT 1;

	b) $this->model_name->findAll();
	producing SELECT * FROM tbl_name;

	c) $where = array('deleted' => 0)
	$this->model_name->findAll(where);
	producing SELECT * FROM tbl_name WHERE deleted = 0

2) Create example:

	$mike = array(
		'email' => 'mike@mike.com',
		'name' => 'Mike',
		'deleted' => 0
	);
	$this->model_name->create($mike);
	producing INSERT INTO tbl_name ('email', 'name', 'deleted') VALUES('mike@mike.com', 'Mike', 0);

3) Update example:

	$where = array('name' => 'Mike');
	$set = array('deleted' => 1)
	$this->model_name->update($where, $set);
	producing UPDATE tbl_name WHERE name = 'Mike' SET deleted = 1;

4) Delete example:

	$where = array('deleted' => '1');
	$this->model_name->delete($where);
	producing DELETE FROM tbl_name WHERE deleted = 1;

5) Search table example:

	$like = array('name' => 'Mi')
	$this->model_name->search($like);
	producing SELECT * FROM tbl_name WHERE name LIKE 'Mi'
	Can use multiple fields in array


Controller CRUD example usage:

```
class User extends CI_Controller
{
    public function __construct()
    {
        parent:: __construct();
        $this->load->model('user_model');
    }

    public function create()
    {
        $user = $this->uri->segment(3);

        $this->user_model->create(array('name' => $user));
    }

    public function show()
    {
        $users = $this->user_model->findAll()->ToArray();
        $this->load->view('welcome_message', array('users' => $users)); 
    }
}
```

Existing Code First model description in a test mode, please do no use it on real project
As it can cause unpredictable data loss.
