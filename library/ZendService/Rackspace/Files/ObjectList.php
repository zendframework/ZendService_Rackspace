<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendService\Rackspace\Files;

use ZendService\Rackspace\Files as RackspaceFiles;
use ZendService\Rackspace\Files\Object;

/**
 * List of objects retrived from the Rackspace CDN web service
 *
 * @category   Zend
 * @package    ZendService\Rackspace
 * @subpackage Files
 */
class ObjectList implements \Countable, \Iterator, \ArrayAccess
{
    /**
     * @var array of ZendService\Rackspace\Files\Object
     */
    protected $objects = array();
    /**
     * @var int Iterator key
     */
    protected $iteratorKey = 0;
    /**
     * @var RackspaceFiles
     */
    protected $service;
    /**
     * The container name of the object list
     *
     * @var string
     */
    protected $container;
    /**
     * __construct()
     *
     * @param RackspaceFiles $service
     * @param array $list
     * @param string $container
     * @return boolean
     */
    public function __construct(RackspaceFiles $service,$list,$container)
    {
        if (!($service instanceof RackspaceFiles)) {
            throw new Exception\InvalidArgumentException("You must pass a RackspaceFiles object");
        }
        if (empty($list)) {
            throw new Exception\InvalidArgumentException("You must pass an array of data objects");
        }
        if (empty($container)) {
            throw new Exception\InvalidArgumentException("You must pass the container of the object list");
        }
        $this->service= $service;
        $this->container= $container;
        $this->_constructFromArray($list);
    }
    /**
     * Transforms the Array to array of container
     *
     * @param  array $list
     * @return void
     */
    private function _constructFromArray(array $list)
    {
        foreach ($list as $obj) {
            $obj['container']= $this->container;
            $this->_addObject(new Object($this->service,$obj));
        }
    }
    /**
     * Add an object
     *
     * @param  ZendService\Rackspace\Files\Object $obj
     * @return ZendService\Rackspace\Files\ObjectList
     */
    protected function _addObject (Object $obj)
    {
        $this->objects[] = $obj;
        return $this;
    }
    /**
     * Return number of servers
     *
     * Implement Countable::count()
     *
     * @return int
     */
    public function count()
    {
        return count($this->objects);
    }
    /**
     * Return the current element
     *
     * Implement Iterator::current()
     *
     * @return ZendService\Rackspace\Files\Object
     */
    public function current()
    {
        return $this->objects[$this->iteratorKey];
    }
    /**
     * Return the key of the current element
     *
     * Implement Iterator::key()
     *
     * @return int
     */
    public function key()
    {
        return $this->iteratorKey;
    }
    /**
     * Move forward to next element
     *
     * Implement Iterator::next()
     *
     * @return void
     */
    public function next()
    {
        $this->iteratorKey += 1;
    }
    /**
     * Rewind the Iterator to the first element
     *
     * Implement Iterator::rewind()
     *
     * @return void
     */
    public function rewind()
    {
        $this->iteratorKey = 0;
    }
    /**
     * Check if there is a current element after calls to rewind() or next()
     *
     * Implement Iterator::valid()
     *
     * @return bool
     */
    public function valid()
    {
        $numItems = $this->count();
        if ($numItems > 0 && $this->iteratorKey < $numItems) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Whether the offset exists
     *
     * Implement ArrayAccess::offsetExists()
     *
     * @param   int     $offset
     * @return  bool
     */
    public function offsetExists($offset)
    {
        return ($offset < $this->count());
    }
    /**
     * Return value at given offset
     *
     * Implement ArrayAccess::offsetGet()
     *
     * @param   int     $offset
     * @throws  OutOfBoundsException
     * @return  ZendService\Rackspace\Files\Object
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->objects[$offset];
        } else {
            throw new Exception\OutOfBoundsException('Illegal index');
        }
    }

    /**
     * Throws exception because all values are read-only
     *
     * Implement ArrayAccess::offsetSet()
     *
     * @param   int     $offset
     * @param   string  $value
     * @throws  ZendService\Rackspace\Exception
     */
    public function offsetSet($offset, $value)
    {
        throw new Exception('You are trying to set read-only property');
    }

    /**
     * Throws exception because all values are read-only
     *
     * Implement ArrayAccess::offsetUnset()
     *
     * @param   int     $offset
     * @throws  ZendService\Rackspace\Exception
     */
    public function offsetUnset($offset)
    {
        throw new Exception('You are trying to unset read-only property');
    }
}
