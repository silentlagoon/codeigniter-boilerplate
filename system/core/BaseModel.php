<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class BaseModel
 */
class BaseModel extends CI_Model
{
    /**
     * Define table name at the database
     */
    protected $table;

    /**
     * Holds fields information. Must be declared according to DBFORGE syntax.
     */
    protected $fields;

    /**
     * Defines if the CodeFirst function activated or not.
     */
    protected $codeFirst = FALSE;

    /**
     * Remove fields from the list which should not be updated
     */
    protected $filterAttributes = FALSE;

    /**
     * Used for collecting relations
     */
    private $_with;

    /**
     * Used to detect if it is findOne or findAll
     */
    private $_isSingleRow;

    /**
     * Holds the parameters and options of the query which passed to model.
     */
    private $settings;

    public function __construct()
    {
        parent::__construct();
        if($this->codeFirst && isset($this->fields))
        {
            $this->load->dbforge();
            $this->_createTable();
        }
    }

    /* --------------------------------------------------------------
     * INTERNAL METHODS
     * ------------------------------------------------------------ */

    private function _createTable()
    {
        if( ! $this->_exists($this->table))
        {
            $this->_makeTable();
        }
        else
        {
            $dbFields = $this->db->list_fields($this->table);
            $this->_checkFieldsParamsSame($dbFields, $this->fields);
        }
    }

    /**
     * @param $dbFields
     * @param $modelFields
     */
    private function _checkFieldsParamsSame($dbFields, $modelFields)
    {
        foreach ($dbFields as $field)
        {
            if( ! in_array($field, $modelFields) )
            {
                $fieldsData = $this->db->field_data($this->table);

                if($fieldsData)
                {
                    if($this->_compareFieldsResult($field, $fieldsData) === FALSE)
                    {
                        $this->_alter_modify_column($modelFields[$field]);
                    }
                }
                else
                {
                    $this->_recreateTable();
                }
            }
            else
            {
                $this->_alter_add_column($modelFields[$field]);
            }
        }
    }

    /**
     * @param $modelField
     * @param $dbFields
     * @return bool
     */
    private function _compareFieldsResult($modelField, $dbFields)
    {
        foreach($dbFields as $field)
        {
            if($field->name == $modelField)
            {
                foreach($field as $param=>$value)
                {
                    if($param != 'name' && $param != 'max_length' && $param != 'primary_key' && $param != 'default')
                    {
                        if(isset($this->fields[$modelField][$param]))
                        {
                            if(strtolower($value) != strtolower($this->fields[$modelField][$param]))
                            {
                                return FALSE;
                            }
                        }
                        else
                        {
                            return FALSE;
                        }
                    }
                    elseif($param == 'max_length')
                    {
                        if( ! isset($this->fields[$modelField]['primary_key']) )
                        {
                            if($value != $this->fields[$modelField]['constraint'])
                            {
                                return FALSE;
                            }
                        }
                    }
                    elseif($param == 'primary_key')
                    {
                        if( ! isset($this->fields[$modelField]['primary_key']))
                        {
                            if( ! $field->primary_key === 0)
                            {
                                return FALSE;
                            }
                        }
                    }
                    elseif($param == 'default')
                    {
                        if($field->primary_key === 0)
                        {
                            if(isset($this->fields[$modelField]['default']))
                            {
                                if(strtolower($value) != strtolower($this->fields[$modelField][$param]))
                                {
                                    return FALSE;
                                }
                            }
                        }
                    }
                }
            }
            else
            {
                $this->_recreateTable();
            }
        }
        return TRUE;
    }

    /**
     * @param $column
     */
    private function _alter_modify_column($column)
    {
        $this->dbforge->modify_column($this->table, $column);
    }

    /**
     * @param $column
     */
    private function _alter_add_column($column)
    {
        $this->dbforge->add_column($this->table, $column);
    }

    /**
     * @param $table
     * @return bool
     */
    private function _exists($table)
    {
        $dbFields = "SHOW TABLES LIKE '".$table."'";
        return ($this->db->query($dbFields)->result()) ? TRUE : FALSE;
    }

    /**
     * @param $fields
     */
    private function _setFields($fields)
    {
        $this->dbforge->add_field($fields);

        foreach($fields as $field)
        {
            if(isset($field['primary_key']))
            {
                $this->dbforge->add_key($field['primary_key'], TRUE);
            }
        }
    }

    private function _recreateTable()
    {
        $this->dbforge->drop_table($this->table);
        $this->_setFields($this->fields);
        $this->dbforge->create_table($this->table, TRUE);
    }

    private function _makeTable()
    {
        $this->_setFields($this->fields);
        $this->dbforge->create_table($this->table, TRUE);
    }

    /**
     * @param null $params
     * @param array $options
     * @param int $limit
     * @param string $field
     * @param string $sortType
     * @return $this
     */
    private function _find($params = null, $options = array(), $limit = 1, $field='id', $sortType='ASC')
    {
        $options = array_merge(
            array(
                'table' => $this->table,
                'limit' => $limit,
                'field' => $field,
                'sort_type' => $sortType
            ), $options
        );
        $this->settings =  array(
            'options' => $options,
            'params' => $params
        );
        return $this;
    }

    /**
     * @return mixed
     */
    private function _makeQuery()
    {
        $params  = $this->settings['params'];
        $options = $this->settings['options'];

        if(is_array($params))
        {
            return $this->db
                ->order_by($options['field'], $options['sort_type'])
                ->get_where($options['table'], $params, $options['limit']);
        }
        elseif(intval($params))
        {
            return $this->db
                ->order_by($options['field'], $options['sort_type'])
                ->get_where($options['table'], array('id' => $params), $options['limit']);
        }
        elseif(is_null($params))
        {
            return $this->db
                ->order_by($options['field'], $options['sort_type'])
                ->get($options['table']);
        }
    }

    /**
     * Remove attributes which is filtered
     * @param $params
     * @return mixed
     */
    private function _filterAttributes($params)
    {
        if($this->$filterAttributes)
        {
            foreach($this->$filterAttributes as $attribute)
            {
                unset($params[$attribute]);
            }
        }
        return $params;
    }

    /* --------------------------------------------------------------
     * EXTERNAL METHODS
     * ------------------------------------------------------------ */

    public function with()
    {
        $has_many = array(
            'posts' => array(
                'users_id' => 1,
                'date' => 2
            )
        );
        foreach($has_many as $table=>$key)
        {
            $options['table'] = $table;
            $this->_with[] = $this->findAll($key);
        }
        return $this;
    }

    /**
     * @param string $field
     * @param string $sortType
     * @return $this
     */
    public function orderBy($field = 'id', $sortType = 'ASC')
    {
        $this->settings['options']['field'] = $field;
        $this->settings['options']['sort_type'] = $sortType;
        return $this;
    }

    /**
     * @param $params
     * @param array $options
     * @return $this
     */
    public function findOne($params, $options = array())
    {
        $this->_find($params, $options);
        $this->_isSingleRow = true;
        return $this;
    }

    /**
     * @param $params
     * @param array $options
     * @return $this
     */
    public function findAll($params = null, $options = array())
    {
        $options['limit'] = null;
        $this->_isSingleRow = false;
        $this->_find($params, $options);
        return $this;
    }

    /**
     * @return array|null
     */
    public function ToArray()
    {

        $result = $this->_makeQuery()->result('array');
        return $this->_format($result);
    }

    /**
     * @return array|null
     */
    public function ToObject()
    {
        $result = $this->_makeQuery()->result('object');
        return $this->_format($result);
    }

    /**
     * @param $result
     * @return array|null
     */
    private function _format($result)
    {
        if($this->_isSingleRow)
        {
            return count($result) > 0 ? $result[0] : NULL;
        }
        return count($result) > 0 ? $result : array();
    }

    /**
     * @param $params
     * @return mixed
     */
    function create($params)
    {
        if (is_int($params))
        {
            throw new InvalidArgumentException('options can be array or object only. Input was: '.$params);
        }
        $this->db->insert($this->table, $params);
        return $this->db->insert_id();
    }

    /**
     * @param $params
     */
    function delete($params)
    {
        if (is_int($params))
        {
            $this->db->delete($this->table, array('id' => $params));
        }
        else if (is_array($params))
        {
            $this->db->delete($this->table, $params);
        } else
        {
            throw new InvalidArgumentException('Options can be integer or array only. Input was: '.$params);
        }
    }

    /**
     * @param $params
     * @param array $data
     */
    public function update($params, $data=array())
    {
        if (is_int($params))
        {
            $this->db->where('id', $params)->update($this->table, $data);
        }
        else if (is_array($params))
        {
            $this->db->where($params)->update($this->table, $data);
        } else
        {
            throw new InvalidArgumentException('Options can be integer or array only. Input was: '.$params);
        }
    }

    /**
     * @param $params
     * @return mixed
     */
    function search($params)
    {
        if ( ! is_array($params))
        {
            throw new InvalidArgumentException('params can be array only. Input was: '.$params);
        }
        return $this->db->from($this->table)->or_like($params)->get()->result_array();
    }
}