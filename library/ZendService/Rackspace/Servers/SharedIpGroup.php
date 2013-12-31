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

class SharedIpGroup
{
    const ERROR_PARAM_CONSTRUCT  = 'You must pass a ZendService\Rackspace\Servers object and an array';
    const ERROR_PARAM_NO_NAME    = 'You must pass the image\'s name in the array (name)';
    const ERROR_PARAM_NO_ID      = 'You must pass the image\'s id in the array (id)';
    const ERROR_PARAM_NO_SERVERS = 'The servers parameter must be an array of Ids';
    /**
     * Name of the shared IP group
     *
     * @var string
     */
    protected $name;
    /**
     * Id of the shared IP group
     *
     * @var string
     */
    protected $id;
    /**
     * Array of servers of the shared IP group
     *
     * @var array
     */
    protected $serversId = array();
    /**
     * The service that has created the image object
     *
     * @var ZendService\Rackspace\Servers
     */
    protected $service;
    /**
     * Construct
     *
     * @param RackspaceServers $service
     * @param array $data
     */
    public function __construct(RackspaceServers $service, $data)
    {
        if (!($service instanceof RackspaceServers) || !is_array($data)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_CONSTRUCT);
        }
        if (!array_key_exists('name', $data)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME);
        }
        if (!array_key_exists('id', $data)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_ID);
        }
        if (isset($data['servers']) && !is_array($data['servers'])) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_SERVERS);
        }
        $this->service= $service;
        $this->name = $data['name'];
        $this->id = $data['id'];
        if (isset($data['servers'])) {
            $this->serversId= $data['servers'];
        }
    }
    /**
     * Get the name of the shared IP group
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Get the id of the shared IP group
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Get the server's array of the shared IP group
     *
     * @return string
     */
    public function getServersId()
    {
        if (empty($this->serversId)) {
            $info= $this->service->getSharedIpGroup($this->id);
            if (($info!==false)) {
                $info= $info->toArray();
                if (isset($info['servers'])) {
                    $this->serversId= $info['servers'];
                }
            }
        }
        return $this->serversId;
    }
    /**
     * Get the server
     *
     * @param integer $id
     * @return ZendService\Rackspace\Servers\Server|boolean
     */
    public function getServer($id)
    {
        if (empty($this->serversId)) {
            $this->getServersId();
        }
        if (in_array($id,$this->serversId)) {
            return $this->service->getServer($id);
        }
        return false;
    }
    /**
     * Create a server in the shared Ip Group
     *
     * @param array $data
     * @param array $metadata
     * @param array $files
     * @return ZendService\Rackspace\Servers\Server|boolean
     */
    public function createServer(array $data, $metadata=array(),$files=array())
    {
        $data['sharedIpGroupId']= (integer) $this->id;
        return $this->service->createServer($data,$metadata,$files);
    }
    /**
     * To Array
     *
     * @return array
     */
    public function toArray()
    {
        return array (
            'name'    => $this->name,
            'id'      => $this->id,
            'servers' => $this->serversId
        );
    }
}
