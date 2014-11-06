CodeIgniter-boilerplate
=======================

# Features:

## Simple layout support
## Simple CRUD model

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

2) Creating example:

	$mike = array(
		'email' => 'mike@mike.com',
		'name' => 'Mike',
		'deleted' => 0
	);
	$this->model_name->create($mike);
	producing INSERT INTO tbl_name ('email', 'name', 'deleted') VALUES('mike@mike.com', 'Mike', 0);

3) Updating example:

	$where = array('name' => 'Mike');
	$set = array('deleted' => 1)
	$this->model_name->update($where, $set);
	producing UPDATE tbl_name WHERE name = 'Mike' SET deleted = 1;

4) Deleting example:

	$where = array('deleted' => '1');
	$this->model_name->delete($where);
	producing DELETE FROM tbl_name WHERE deleted = 1;

5) Searching table:
	$like = array('name' => 'Mi')
	$this->model_name->search($like);
	producing SELECT * FROM tbl_name WHERE name LIKE 'Mi'
	Can use multiple fields in array
