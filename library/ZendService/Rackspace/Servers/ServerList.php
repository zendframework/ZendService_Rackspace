<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendService\Rackspace\Servers;

use ZendService\Rackspace\Servers as RackspaceServers;

/**
 * List of servers of Rackspace
 *
 * @category   Zend
 * @package    ZendService\Rackspace
 * @subpackage Servers
 */
class ServerList implements \Countable, \Iterator, \ArrayAccess
{
    /**
     * @var array of ZendService\Rackspace\Servers\Server
     */
    protected $servers = array();
    /**
     * @var int Iterator key
     */
    protected $iteratorKey = 0;
    /**
     * @var ZendService\Rackspace\Servers
     */
    protected $service;
    /**
     * Construct
     *
     * @param  RackspaceServers $service
     * @param  array $list
     */
    public function __construct(RackspaceServers $service,$list = array())
    {
        if (!($service instanceof RackspaceServers) || !is_array($list)) {
            throw new Exception\InvalidArgumentException("You must pass a ZendService\Rackspace\Servers object and an array");
        }
        $this->service= $service;
        $this->constructFromArray($list);
    }
    /**
     * Transforms the array to array of Server
     *
     * @param  array $list
     * @return void
     */
    private function constructFromArray(array $list)
    {
        foreach ($list as $server) {
            $this->addServer(new Server($this->service,$server));
        }
    }
    /**
     * Add a server
     *
     * @param  ZendService\Rackspace\Servers\Server $server
     * @return ZendService\Rackspace\Servers\ServerList
     */
    protected function addServer (Server $server)
    {
        $this->servers[] = $server;
        return $this;
    }
    /**
     * To Array
     *
     * @return array
     */
    public function toArray()
    {
        $array= array();
        foreach ($this->servers as $server) {
            $array[]= $server->toArray();
        }
        return $array;
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
        return count($this->servers);
    }
    /**
     * Return the current element
     *
     * Implement Iterator::current()
     *
     * @return ZendService\Rackspace\Servers\Server
     */
    public function current()
    {
        return $this->servers[$this->iteratorKey];
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
     * @return  ZendService\Rackspace\Servers\Server
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->servers[$offset];
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
