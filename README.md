CodeIgniter-boilerplate
=======================

# Features:

* CodeIgniter 2.2.0
* Ion Auth as authentication Library
* Simple layout support
* Simple CRUD model

### Ion Auth
To prepare database for using Ion Auth run the migration

```
localhost/migrate
```
Where the localhost is the address of the application downloaded.
Form more information or settings options visit (http://benedmunds.com/ion_auth/)

If you do not want to use Ion Auth just do not run a migration.

### Layout

For layout usage you must specify the layout name. By default the layouts are kept at the 
```
application/view/layouts/
```
The layout name being specified using by
```
setLayout();
```

Layout usage example:

```php
class User extends CI_Controller
{
    public function __construct()
    {
        parent:: __construct();
        $this->layout->setLayout('/layouts/main');
    }

    public function index()
    {
        $data = $this->input->post();
        $this->layout->view('welcome', $data);
    }
}
```

### CRUD

Model example:

```php
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
	
* 1) Reading example:

```php
	a) $this->model_name->findOne(1);
	 producing SELECT * FROM model_name WHERE id = 1 LIMIT 1;

	b)$where = array('name' => 'Mike');
	$this->model_name->findOne($where);
	producing SELECT * FROM model_name WHERE name = 'Mike' LIMIT 1;

	c) $this->model_name->findAll();
	producing SELECT * FROM model_name;

	d) $where = array('deleted' => 0);
	$this->model_name->findAll($where);
	producing SELECT * FROM model_name WHERE deleted = 0
```

* 2) Create example:

```php
	$mike = array(
		'email' => 'mike@mike.com',
		'name' => 'Mike',
		'deleted' => 0
	);
	$this->model_name->create($mike);
	producing INSERT INTO model_name ('email', 'name', 'deleted') VALUES('mike@mike.com', 'Mike', 0);
```

* 3) Update example:

```php
	$where = array('name' => 'Mike');
	$set = array('deleted' => 1)
	$this->model_name->update($where, $set);
	producing UPDATE model_name WHERE name = 'Mike' SET deleted = 1;
```

* 4) Delete example:

```php
	$where = array('deleted' => '1');
	$this->model_name->delete($where);
	producing DELETE FROM model_name WHERE deleted = 1;
```

* 5) Search table example:

```php
	$like = array('name' => 'Mi')
	$this->model_name->search($like);
	producing SELECT * FROM model_name WHERE name LIKE 'Mi'
	Can use multiple fields in array
```

* 6) Controller CRUD example usage:

```php
class User extends CI_Controller
{
    public function __construct()
    {
        parent:: __construct();
        $this->load->helper('url');
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

* 7) Existing Code First model description in a test mode, please do no use it on real project
As it can cause unpredictable data loss.
