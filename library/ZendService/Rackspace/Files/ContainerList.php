<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendService\Rackspace\Files;

use ArrayAccess;
use Countable;
use Iterator;
use ZendService\Rackspace\Files as RackspaceFiles;

/**
 * List of containers retrived from the Rackspace CDN web service
 *
 * @category   Zend
 * @package    ZendService\Rackspace
 * @subpackage Files
 */
class ContainerList implements
    Countable,
    Iterator,
    ArrayAccess
{
    /**
     * @var array Array of ZendService\Rackspace\Files\Container
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
     * Constructor
     *
     * @param RackspaceFiles $service
     * @param  array $list
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(RackspaceFiles $service, array $list = array())
    {
        $this->service= $service;
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
        foreach ($list as $container) {
            $this->_addObject(new Container($this->service,$container));
        }
    }
    /**
     * Add an object
     *
     * @param  Container $obj
     * @return ContainerList
     */
    protected function _addObject (Container $obj)
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
     * @return Container
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
     * @throws  Exception\OutOfBoundsException
     * @return  Container
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
     * @throws  Exception\RuntimeException
     */
    public function offsetSet($offset, $value)
    {
        throw new Exception\RuntimeException('You are trying to set read-only property');
    }

    /**
     * Throws exception because all values are read-only
     *
     * Implement ArrayAccess::offsetUnset()
     *
     * @param   int     $offset
     * @throws  Exception\RuntimeException
     */
    public function offsetUnset($offset)
    {
        throw new Exception\RuntimeException('You are trying to unset read-only property');
    }
}
