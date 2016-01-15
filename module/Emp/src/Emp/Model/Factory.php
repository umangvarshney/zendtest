<?php


namespace Emp\Model;
/**
 *class Factory
 *
 *
 *The model factory is used to return *Table objects*.
 *-----------------------------------------------
 *This works by caching the model tables so there's only one copy out there.
 *It's a singleton.
 *
 * 
 *@todo There is a full chapter in ZF2 manual on how to instantiate and inject objects into application.
 *          The factory pattern as used here is not one of those.
 *          Replace this obsolete method of instantiating Objects with the ZF2 recommended Service Manager Injection method.
 *          
 *@todo Singleton pattern is recommended deprecated in ZF2.
 *      Unit testing not possible with Singleton pattern. 
 *      The scope of each action in ZF2, make the instantiation of more than one instance of an object, if injected correctly, 
 *      impossible.
 *      I would recommend to fully impliment the proper ZF2 method on injection an Object Instance into application.
 */
class Factory
{
    /**
     * The instance of our singleton.
     *
     * @var Factory
     */
    static protected $_instance = null;

    /**
     * Keep a dictionary mapping table names to instances.
     *
     * @var array
     */
    protected $_tableObjectMap = array();

 
    /**
     *getInstance
     *
     *Return an instance of the singleton.
     * ------------------------------------------------------
     *
     *@static
     *
     *@return Factory
     *
     *@todo See my note on Singleton pattern in ZF2
     *
     */
    static public function getInstance()
    {
        if(self::$_instance == null) {
            self::$_instance = new Factory();
        }

        return self::$_instance;
    }

  
    
    /**
     *get
     *
     *Get a table object.
     * ------------------------------------------------------
     *
     *@param string $table
     *
     *@return Object $table
     *
     *@todo  Replace with ZF2 standard Service Manager Aware injection methods.
     *        This just add overhead, and lines and lines of unneeded code. 
     *          
     *
     */
    public function get($table)
    {
        if(!array_key_exists($table, $this->_tableObjectMap)) {
            $objName = "\\Emp\\Model\\{$table}Table";
            $this->_tableObjectMap[$table] = new $objName();
        }

        return $this->_tableObjectMap[$table];
    }
}
