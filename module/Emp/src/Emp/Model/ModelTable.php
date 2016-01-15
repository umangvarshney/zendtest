<?php


namespace Emp\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\Feature;

/**
 *ModelTable
 *
 *A typical model table which can be used as a base class for other model tables.
 *-----------------------------------------------
 * ##### Extends Zend\Db\TableGateway\AbstractTableGateway
 *
 * 
 * @author sconley
 * @copyright Land O' Lakes
 * @version V1.0.0.1
 * @namespace  LoL\Model
 * @name datasilo/module/LoL/src/LoL/Model/ ModelTable.php
 *
 * @todo This is re inventing the wheel. By implimenting Doctrine, most of these pseudo Classes not needed.
 *          I would recommend that developers look at replacing all these with a simple DB abstraction engne like Doctrine.
 *          Doctrine is very well integrated with ZF2.
 */
abstract class ModelTable extends AbstractTableGateway
{
    /*
     * What is my table name?
     *
     * This is the 'table' variable defined in the parent.
     *
     * @var string
     */

    /**
     * What is my model class name?
     *
     * If not set, the constructor will attempt to 'figure out'
     * our class name.
     *
     * @var string
     */
    protected $_modelClass = false;

    /**
     * What order-by criteria have been provided?
     *
     * @param array
     */
    protected $_orderBy = array();

    /**
     * Are we returning as an array instead of as objects?
     *
     * @param boolean
     */
    protected $_returnArrays = false;

   
    /**
     *__construct
     *
     *
     *Construtor
     * ------------------------------------------------------
     * Set _modelClass and add feature to featureset
     *
     *
     */
    public function __construct()
    {
        if($this->_modelClass === false) {
            $this->_modelClass = substr(get_class($this), 0, -5);
        }

        $this->featureSet = new Feature\FeatureSet();
        $this->featureSet->addFeature(new Feature\GlobalAdapterFeature());
        $this->initialize();
    }


  
    /**
     *insert
     *
     * Wrapper for 'insert'.
     * ---------------------
     *Handle the insertion of model objects in addition to the traditional stuff you could pass into TableGateway's insert.
     *
     *
     *
     *
     *
     * @param array|object $set
     *
     *@return int 
     *
     *
     *
     */
    public function insert($set)
    {
        if(is_object($set)) {
            return parent::insert($set->toArray());
        } else {
            return parent::insert($set);
        }
    }


    /**
     *update
     *
     *Wrapper for 'update'
     * --------------------
     * Handle the insertion of model objects in addition to the traditional stuff you could pass into TableGateway's insert.
     * 
     * 
     * 
     *
     *@param array|object $set
     *@param string $where
     *
     *@return int
     *
     *
     *
     */
    public function update($set, $where = null)
    {
        if(is_object($set)) {
            // clear out 'id' if we need to
            $data = $set->toArray();

            if(array_key_exists('id', $data)) {
                unset($data['id']);
            }

            return parent::update($set->toArray(), $where);
        } else {
            return parent::update($set, $where);
        }
    }

   
    /**
     *beginTransaction
     *
     *Wrapper to start a transaction.
     * ------------------------------
     * Transactions work across differnet DB tables, you can start with one table and end with any other table.
     * 
     * 
     *
     */
    public function beginTransaction()
    {
        $this->getAdapter()->getDriver()->getConnection()->beginTransaction();
    }


    /**
     *inTransaction
     *
     *
     * ------------------------------------------------------

     *
     *@return boolean
     *
     *
     */
    public function inTransaction()
    {
        return $this->getAdapter()->getDriver()->getConnection()->inTransaction();
    }

 
    /**
     *commitTransaction
     *
     *Wrapper to end a transaction.
     * ------------------------------------------------------
     *Commits a transaction.
     *
     *
     *
     */
    public function commitTransaction()
    {
        $this->getAdapter()->getDriver()->getConnection()->commit();
    }

   
    /**
     *rollbackTransaction
     *
     * Wrapper to rollback a transaction.
     * ------------------------------------------------------
     *
     *
     */
    public function rollbackTransaction()
    {
        $this->getAdapter()->getDriver()->getConnection()->rollback();
    }


    /**
     *fetchById
     *
     *Fetches a single row by primary key
     * ----------------------------------
     * Or multiple rows by primary keys if array is passed 
     * Returns null on failure.
     * ALL Ids must load for success.
     * ##### THIS RESPECTS SORT ORDER
     * 
     * 
     *@param integer|array $id
     *
     *@return Grower
     *
     *@todo
     *
     */
    public function fetchById($id)
    {
        $order = $this->getOrderBy();

        $rowset = $this->select(function($select) use ($order, $id) {
            $select->where(array('id' => $id));

            if(!empty($order)) {
                $select->order($order);
            }

            return $select;
        });

        if(is_array($id)) {
            if(count($id) != $rowset->count()) {
                // we didn't fetch all ID's.
                return null;
            }

            $ret = array();

            foreach($rowset as $row) {
                $ret[] = new $this->_modelClass($row->getArrayCopy());
            }
            
            return $ret;
        }

        $row = $rowset->current();

        if(!$row) {
            return null;
        }

        return new $this->_modelClass($row->getArrayCopy());
    }

   
    /**
     *fetchAll
     *
     *Fetches everything from a given table.
     * --------------------------------------
     *  No permissions check.
     * ##### THIS RESPECTS SORT ORDER
     *
     *
     *
     *@return array of model objects.
     *
     *
     *
     */
    public function fetchAll()
    {
        $order = $this->getOrderBy();

        $rowset = $this->select(function($select) use ($order) {
            if(!empty($order)) {
                $select->order($order);
            }

            return $select;
        });

        $ret = array();

        foreach($rowset as $row) {
            $ret[] = new $this->_modelClass($row->getArrayCopy());
        }

        return $ret;
    }

   
    /**
     *fetchNamesByIds
     *
     *Fetch an array of names from an array of ID's, associating ID to name.
     * ---------------------------------------------------------------------
     * This is used mostly by select boxes.
     * ##### THIS RESPECTS SORT ORDER
     * 
     * 
     * 
     *
     *@param array $ids
     *
     *@return array $ret (rowset)
     *
     *
     *
     */
    public function fetchNamesByIds($ids)
    {
        $order = $this->getOrderBy();

        $rowset = $this->select(function($select) use ($ids, $order) {
            $select->columns(array('id', 'name'))
                   ->where(array('id' => $ids));

            if(!empty($order)) {
                $select->order($order);
            }

            return $select;
        });

        $ret = array();

        foreach($rowset as $row) {
            $ret[$row->id] = $row->name;
        }

        return $ret;
    }

 
    /**
     *fetchByName
     *
     *Fetch by name,used as a primary key
     * -------------------------------------
     * Not all tables have a 'name' column, but enough do that it made sense to centralize.
     * ##### THIS RESPECTS SORT ORDER
     * 
     *
     * @param string $name
     *
     *@return array (rowset)
     *
     *
     *
     */
    public function fetchByName($name)
    {
        $order = $this->getOrderBy();

        $rowset = $this->select(function($select) use ($name, $order) {
            $select->where(array('name' => $name));

            if(!empty($order)) {
                $select->order($order);
            }

            return $select;
        });

        return $this->_returnArray($rowset);
    }


    /**
     *enableArrayReturn
     *
     *Set ourselves to use array returns instead.
     * ------------------------------------------------------
     *
     *
     *@return self $this
     *
     *
     *
     */
    public function enableArrayReturn()
    {
        $this->_returnArrays = true;
        return $this;
    }

  
    /**
     *disableArrayReturn
     *
     *Set ourselves to use objects instead.
     * ------------------------------------------------------
     *
     *
     *
     *@return self $this
     *
     *
     *
     */
    public function disableArrayReturn()
    {
        $this->_returnArrays = false;
        return $this;
    }

 
    /**
     *_boil
     *
     *Boil a model object down to an ID and a type.
     * ------------------------------------------------------
     * This does the often-repeated processing used all over the place for arguments that may be an object or an ID.
     * IF the second parameter is false (literal), we will just return ID and completely ignore type.
     * 
     * 
     * 
     * Returns it as an array, designed to be used with list, ie:
     *  * list($id, $type) = $this->_boil($object, $type);
     * 
     * 
     * 
     * 
     *
     * @param integer|object $obj
     * @param string  $type -- Only used if first parameter is not an object.
     *
     *@return array($id, $type)
     *
     *@todo This is none standard,(allow for bad coding). We are suppose to always know whether we are dealing with an Object or an id(int/string) 
     *          We need to look at the whole architecture, and refactor to eliminate the use of this type of method.
     *
     */
    protected function _boil($obj, $type = '')
    {
        if(is_object($obj)) {
            
            if($type === false) {
                return $obj->getId();
            } else {
                return array($obj->getId(), $obj->getClassName());
            }
        } else {
            if($type === false) {
                return $obj;
            } else {
                return array($obj, $type);
            }
        }
    }


    /**
     *_returnArray
     *
     * Convert a Zend rowset to an array of model objects.
     * ------------------------------------------------------
     * This code is used everywhere, let's reduce duplication.
     * 
     *
     *@param \Zend\Db\ResultSet
     *
     *@return array of model objects
     *
     *
     *
     */
    protected function _returnArray($rowset)
    {
        $ret = array();

        foreach ($rowset as $row) {
            if($this->_returnArrays) {
                $ret[] = $row->getArrayCopy();
            } else {
                $ret[] = new $this->_modelClass($row->getArrayCopy());
            }
        }

        return $ret;
    }


    /**
     *_returnSingle
     *
     * Convert a Zend rowset to a single return object.
     * ------------------------------------------------------
     * This is also used everywhere.
     * 
     * 
     *
     *@param \Zend\Db\ResultSet $rowset
     *
     *@return model object or null
     *
     *@
     *
     */
    protected function _returnSingle($rowset)
    {
        $row = $rowset->current();

        if(!$row) {
            return null;
        }

        if($this->_returnArrays) {
            return $row->getArrayCopy();
        } else {
            return new $this->_modelClass($row->getArrayCopy());
        }
    }

  
    /**
     *getLastInsertValue
     *
     *Override getLastInsertValue to work with PGSQL.
     * ------------------------------------------------------
     * This is a 'real' way to do this but I can't figure it out and I'm on a time crunch.(Stephen)
     *
     *
     *
     *@return integer
     *
     *@todo use Zend SequenceFeature instead of this
     *@todo See my notes on Doctrine. None of all the custom calls would have been needed, if we used Doctrine. Recommended to make the change.
     *
     */
    public function getLastInsertValue()
    {
        $statement = $this->adapter->createStatement();
        $statement->prepare("SELECT CURRVAL('{$this->table}_id_seq')");
        $result = $statement->execute();
        $sequence = $result->getResource()->fetch(\PDO::FETCH_ASSOC);
        return $sequence['currval'];
    }


    /**
     *setOrderBy
     *
     *SET the "order by" for the queries.
     * ------------------------------------------------------
     * ##### PLEASE NOTE :
     * * This is "advisory", meaning, each fetch implementation may or may not ignore the the order by provided and it's implemented per-method.
     * * Why?  Because some methods do some pretty wacky things, and order-by doesn't always make sense. 
     * * However, I wanted to provide a means to support it if desired.
     * 
     *
     * @param array|string
     *
     * @return $this
     *
     *@todo Consider supporting this in a more consistent/global way. 
     *@todo See my notes on Doctrine, Zend DB only for very lightweight applications.
     *
     */
    public function setOrderBy($order)
    {
        $this->_orderBy = $order;
        return $this;
    }

  
    /**
     *getOrderBy
     *
     * GET the currently set "order by" for the queries.
     * ------------------------------------------------------
     *  Will be an empty array if unset.
     *  
     *
     *
     *
     * @return array|string
     *
     *
     *
     */
    public function getOrderBy()
    {
        return $this->_orderBy;
    }
}
