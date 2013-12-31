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
use ZendService\Rackspace\Files\Container as RackspaceContainer;
use Zend\Http\Client\Adapter\Test as HttpTest;

/**
 * @category   Zend
 * @package    ZendService\Rackspace\Files
 * @subpackage UnitTests
 * @group      Zend\Service
 * @group      ZendService\Rackspace
 * @group      ZendService\Rackspace\Files
 */
class OfflineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reference to RackspaceFiles
     *
     * @var ZendService\Rackspace\Files
     */
    protected $rackspace;
    /**
     * HTTP client adapter for testing
     *
     * @var Zend\Http\Client\Adapter\Test
     */
    protected $httpClientAdapterTest;
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
     * Reference to Container
     *
     * @var ZendService\Rackspace\Files\Container
     */
    protected $container;
    /**
     * Set up the test case
     *
     * @return void
     */
    public function setUp()
    {
        $this->rackspace = new RackspaceFiles('foo','bar');
        $this->container = new RackspaceContainer($this->rackspace, array('name' => TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME));

        $this->httpClientAdapterTest = new HttpTest();

        $this->rackspace->getHttpClient()->setAdapter($this->httpClientAdapterTest);

        // authentication (from a file)
        $this->httpClientAdapterTest->setResponse(self::loadResponse('../../_files/testAuthenticate'));
        $this->assertTrue($this->rackspace->authenticate(),'Authentication failed');

        $this->metadata =  array (
            'foo'  => 'bar',
            'foo2' => 'bar2'
        );

        $this->metadata2 = array (
            'hello' => 'world'
        );

        // load the HTTP response (from a file)
        $this->httpClientAdapterTest->setResponse($this->loadResponse($this->getName()));
    }

    /**
     * Utility method for returning a string HTTP response, which is loaded from a file
     *
     * @param  string $name
     * @return string
     */
    protected function loadResponse($name)
    {
        return file_get_contents(__DIR__ . '/_files/' . $name . '.response');
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
        $this->assertEquals($object->getSize(),15);
        $this->assertEquals($object->getMetadata(),$this->metadata);
    }

    public function testCopyObject()
    {
        $result= $this->rackspace->copyObject(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME,
                                              TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME,
                                              TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME,
                                              TESTS_ZEND_SERVICE_RACKSPACE_OBJECT_NAME.'-copy');
        $this->assertTrue($result);
        $this->assertNotContains('application/x-www-form-urlencoded', $this->rackspace->getHttpClient()->getLastRawRequest());
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

    public function testGetInfoCdnContainer()
    {
        $info = $this->rackspace->getInfoCdnContainer(TESTS_ZEND_SERVICE_RACKSPACE_CONTAINER_NAME);
        $this->assertTrue($info!==false);
        $this->assertTrue(is_array($info));
        $this->assertTrue(!empty($info['ttl']));
        $this->assertTrue(!empty($info['cdn_uri']));
        $this->assertTrue(!empty($info['cdn_uri_ssl']));
        $this->assertTrue($info['cdn_enabled']===true);
        $this->assertTrue($info['log_retention']===true);
    }

    public function testGetCdnTtl()
    {
        $ttl = $this->container->getCdnTtl();
        $this->assertTrue($ttl!==false);
    }

    public function testGetCdnUri()
    {
        $uri = $this->container->getCdnUri();
        $this->assertTrue($uri!==false);
    }

    public function testGetCdnUriSsl()
    {
        $uri = $this->container->getCdnUriSsl();
        $this->assertTrue($uri!==false);
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
