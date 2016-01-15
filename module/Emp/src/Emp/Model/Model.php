<?php


namespace Emp\Model;
/**
 *Model
 *
 *Base model file that will allow the automation of getters / setters.
 *-----------------------------------------------
 *##### Implements \JsonSerializable 
 *
 * 
 * @author sconley
 * @copyright Land O' Lakes
 * @version V1.0.0.1
 * @namespace  LoL\Model
 * @name datasilo/module/LoL/src/LoL/Model/ Model.php
 *
 *
 */
abstract class Model implements \JsonSerializable
{
    /**
     * Array of valid field names.  May be empty if you want to
     * not have field name checking.
     *
     * @var array
     */
    protected static $_fields = array();

    /**
     * Field values, a mapping of field keys to values.
     *
     * @var array
     */
    protected $_values = array();

    /**
     * Loaded permissions that were loaded alongside this object.
     *
     * @var array
     */
    protected $_permissions = null;

    /**
     * Constructor -- by default can take an array which it will
     * push into values after validation.
     *
     * This also copies over the client and gateway statics into GDS.
     */
    public function __construct($options)
    {
        $this->fromArray($options);
    }


    /**
     *getPermissionLevel
     *
     *Get our permission level for this object.
     * ------------------------------------------------------
     * 
     *@throws an exception if permissions were not loaded in an appropriate way for this object.
     *@param user - If a user is passed, we'll load permissions if we don't already have them.
     *
     *@return string
     *
     *
     *
     */
    public function getPermissionLevel($user = false)
    {
        if(!empty($this->_permissions)) {
            return $this->_permissions;
        }

        if(!is_object($user)) {
            throw new \Zend\Db\Exception\InvalidArgumentException('getPermissionLevel called when permissions have not been loaded yet.');
        }

        // Let's try to load permissions for this object.
        return ($this->_permissions = Factory::get('ResourcesAccess')->hasResourceAccessTo($user, $this));
    }


    /**
     *setPermissionLevel
     *
     * Set our permission level
     * ------------------------------------------------------
     *
     *@param string  $level
     *
     *@return self $this
     *
     *
     *
     */
    public function setPermissionLevel($level)
    {
        $this->_permissions = $level;
        return $this;
    }

  
    /**
     *__call
     *
     *Catchall to handle getters / setters
     * ------------------------------------------------------
     *
     *
     *@throws \Zend\Db\Exception\RuntimeException  if invalid method.
     *@param string $func
     *@param array $args
     *
     * @return void | string
     *
     *
     *
     */
    public function __call($func, $args = array())
    {
        $return = NULL;

        if(substr($func, 1, 2) != 'et') { // should be get or set
            // Throw exception
            throw new \Zend\Db\Exception\RuntimeException("Invalid method : {$func}");
        }

        $field = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', substr($func, 3)));

        if((!empty($this->_fields)) && (!in_array($field, $this->_fields))) {
            // Throw exception
            throw new \Zend\Db\Exception\RuntimeException("Invalid method : {$func}");
        }

        if($func[0] == 's') {
            $this->_values[$field] = $args[0];
            $return = $this;
        } elseif(array_key_exists($field, $this->_values)) { // yes, this would return on 'bet', 'vet', etc.
                 // but exact checking is more compute cycles.
                 
            $return = $this->_values[$field];
        }
 
        return $return;
    }

  
    /**
     *toArray
     *
     *Return model object as array
     * ------------------------------------------------------
     *
     * 
     *
     *@return  object _values
     *
     *
     *
     */
    public function toArray()
    {
        return $this->_values;
    }


    /**
     *fromArray
     *
     *Set from an array.
     * ------------------------------------------------------
     *'Permissions' key will be put in the permission level and not set as a parameter.
     * This is "fatal" to the ResourcesAccess class, so that class will override this method.
     * If you have a column named 'permissions' in your table, you should consider doing the same.
     *
     *@throws \Zend\Db\Exception\UnexpectedValueException  If an invalid field is passed.
     *
     *
     *@param array $options
     *
     *
     *
     *
     */
    public function fromArray($options)
    {
        // Set permissions if it exists
        if(array_key_exists('permissions', $options)) {
            $this->setPermissionLevel($options['permissions']);
        }

        foreach(array_keys($options) as $key) {
            // skip permissions
            if($key == 'permissions') {
                continue;
            }

            if((!empty($this->_fields)) && (!in_array($key, $this->_fields))) {
                throw new \Zend\Db\Exception\UnexpectedValueException("Unknown field: $key");
            }
        }
        
        $this->_values = $options;
    }

 
    /**
     *exchangeArray
     *
     *Implements Zend TableGateway
     * ------------------------------------------------------
     *
     *@param array $data
     *
     *@return self $this
     *
     *
     *
     */
    public function exchangeArray($data)
    {
        // Set permissions if it exists
        if(array_key_exists('permissions', $data)) {
            $this->setPermissionLevel($data['permissions']);
            unset($data['permissions']);
        }

        $this->_values = $data;
        return $this;
    }

   
    /**
     *getClassName
     *
     *This strips away the namespace garbage from the class and returns it.
     * ------------------------------------------------------
     *
     *
     *
     *@return string
     *
     *
     *
     */
    public function getClassName()
    {
        return substr(get_class($this), 10);
    }

  
    /**
     *jsonSerialize
     *
     *To allow JSON serialization.
     * ------------------------------------------------------
     *
     *@param
     *
     *@return array _values
     *
     *
     *
     */
    public function jsonSerialize()
    {
        return $this->_values;
    }
}
