<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendServiceTest\Rackspace\Files;

use ZendService\Rackspace\Files as RackspaceFiles;
use ZendService\Rackspace\Files\ContainerList;
use Zend\Http\Client\Adapter\Test as HttpTest;

/**
 * @category   Zend
 * @package    ZendService\Rackspace\Files
 * @subpackage UnitTests
 * @group      Zend\Service
 * @group      ZendService\Rackspace
 * @group      ZendService\Rackspace\Files
 */
class OnlineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reference to Rackspace Servers object
     *
     * @var ZendService\Rackspace\Servers
     */
    protected $rackspace;

    /**
     * Socket based HTTP client adapter
     *
     * @var Zend_Http_Client_Adapter_Socket
     */
    protected $httpClientAdapterSocket;

    /**
     * Metadata for container/object test
     *
     * @var array
     */
    protected $metadata;

    /**
     * Another metadata for container/object test
     *
     * @var array
     */
    protected $metadata2;

    /**
     * Set up the test case
     *
     * @return void
     */
    public function setUp()
    {
        if (!constant('TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_ENABLED') || TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_ENABLED != true) {
            $this->markTestSkipped('ZendService\Rackspace\TFiles online tests are not enabled');
        }
        if(!defined('TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_USER') || !defined('TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_KEY')) {
            $this->markTestSkipped('Constants User and Key have to be set.');
        }

        $this->rackspace = new RackspaceFiles(TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_USER,
            TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_KEY);

        $this->httpClientAdapterSocket = new \Zend\Http\Client\Adapter\Socket();

        $this->rackspace->getHttpClient()
                ->setAdapter($this->httpClientAdapterSocket);

        $this->metadata =  array (
            'foo'  => 'bar',
            'foo2' => 'bar2'
        );

        $this->metadata2 = array (
            'hello' => 'world'
        );

        // terms of use compliance: safe delay between each test
        sleep(1);
    }

    public function testCreateContainer()
    {
        $container= $this->rackspace->createContainer(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME,$this->metadata);
        $this->assertTrue($container!==false);
        $this->assertEquals($container->getName(),TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME);
    }

    public function testGetCountContainers()
    {
        $num= $this->rackspace->getCountContainers();
        $this->assertTrue($num>0);
    }

    public function testGetContainer()
    {
        $container= $this->rackspace->getContainer(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME);
        $this->assertTrue($container!==false);
        $this->assertEquals($container->getName(),TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME);
    }

    public function testGetContainers()
    {
        $containers= $this->rackspace->getContainers();
        $this->assertTrue($containers!==false);
        $found=false;
        foreach ($containers as $container) {
            if ($container->getName()==TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME) {
                $found=true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testGetMetadataContainer()
    {
        $data= $this->rackspace->getMetadataContainer(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME);
        $this->assertTrue($data!==false);
        $this->assertEquals($data['name'],TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME);
        $this->assertEquals($data['metadata'],$this->metadata);

    }

    public function testGetInfoAccount()
    {
        $data= $this->rackspace->getInfoAccount();
        $this->assertTrue($data!==false);
        $this->assertTrue($data['tot_containers']>0);
    }

    public function testStoreObject()
    {
        $content= 'This is a test!';
        $result= $this->rackspace->storeObject(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME,
                                               TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME,
                                               $content,
                                               $this->metadata);
        $this->assertTrue($result);
    }

    public function testGetObject()
    {
        $object= $this->rackspace->getObject(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME,
                                             TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME);
        $this->assertTrue($object!==false);
        $this->assertEquals($object->getName(),TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME);
    }

    public function testCopyObject()
    {
        $result= $this->rackspace->copyObject(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME,
                                              TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME,
                                              TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME,
                                              TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME.'-copy');
        $this->assertTrue($result);
    }

    public function testGetObjects()
    {
        $objects= $this->rackspace->getObjects(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME);
        $this->assertTrue($objects!==false);

        $this->assertEquals($objects[0]->getName(),TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME);
        $this->assertEquals($objects[1]->getName(),TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME.'-copy');
    }

    public function testGetSizeContainers()
    {
        $size= $this->rackspace->getSizeContainers();
        $this->assertTrue($size!==false);
        $this->assertTrue(is_int($size));
    }

    public function testGetCountObjects()
    {
        $count= $this->rackspace->getCountObjects();
        $this->assertTrue($count!==false);
        $this->assertTrue(is_int($count));
    }

    public function testSetMetadataObject()
    {
        $result= $this->rackspace->setMetadataObject(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME,
                                                     TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME,
                                                     $this->metadata2);
        $this->assertTrue($result);
    }

    public function testGetMetadataObject()
    {
        $data= $this->rackspace->getMetadataObject(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME,
                                                   TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME);
        $this->assertTrue($data!==false);
        $this->assertEquals($data['metadata'],$this->metadata2);
    }

    public function testEnableCdnContainer()
    {
        $data= $this->rackspace->enableCdnContainer(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME);
        $this->assertTrue($data!==false);
        $this->assertTrue(is_array($data));
        $this->assertTrue(!empty($data['cdn_uri']));
        $this->assertTrue(!empty($data['cdn_uri_ssl']));
    }

    public function testGetCdnContainers()
    {
        $containers= $this->rackspace->getCdnContainers();
        $this->assertTrue($containers!==false);
        $found= false;
        foreach ($containers as $container) {
            if ($container->getName()==TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME) {
                $found= true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testUpdateCdnContainer()
    {
        $data= $this->rackspace->updateCdnContainer(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME,null,false);
        $this->assertTrue($data!==false);
    }


    public function testDeleteObject()
    {
        $this->assertTrue($this->rackspace->deleteObject(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME,
                                                         TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME));
    }

    public function testDeleteObject2()
    {
        $this->assertTrue($this->rackspace->deleteObject(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME,
                                                         TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME.'-copy'));
    }

    public function testDeleteContainer()
    {
        $this->assertTrue($this->rackspace->deleteContainer(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME));
    }

}
