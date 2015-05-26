<?php
/**
 * Created by PhpStorm.
 * User: bthomas
 * Date: 5/19/15
 * Time: 11:21 PM
 */

namespace Framework\Controller;

use Doctrine\ORM\EntityManager;
use Framework\Controller\AbstractBaseController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DoctrineEntityRestControllerTransformer
 *
 * Set static::$entityClass to the name of your doctrine entity, and create the routes you want to expose and viola
 * you now have a rest controller, so now grab your pillow it's time for some rest yourself.
 *
 * TODO:
 *   - Extract entity into an interface
 *   - Convert entity concrete implementation into a trait
 *   - create straight PDO implementation of entity
 *   - create pure doctrine implementation of entity
 *
 * @package Controller
 */
class EntityRestControllerTransformer extends AbstractBaseController
{
    /**
     * Fill this dude out and it will automatically wire up a REST interface for your Entity
     * @var  entityClass
     */
    protected static $entityClass;

    /** @var  EntityManager $em */
    protected $em;
    /** @var  db */
    protected $db;

    /**
     * ListingController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $helper = include_once("src/cli_config.php");
        $this->em = $helper->get("em")->getEntityManager();
        $this->db = $helper->get("db");
    }

    /**
     * @return array
     */
    public function getAction(){
        $limit = $this->getRequest()->query->getInt("limit", 20);
        $offset = $this->getRequest()->query->getInt("offset", 0);
        $listings = $this->em->getRepository(static::$entityClass)->findBy([], ["id" => "ASC"], $limit, $offset);
        return $listings;
    }

    /**
     * PUT is idempotent so always over-write with new resource (default properties)
     * @return Array
     */
    public function putAction(){
        $item = new static::$entityClass($this->getRequest()->request->all());
        $item->save(false);
        return $item;
    }

    /**
     * GET a single item by id
     * @param $id
     * @return null|object
     */
    public function getOneAction($id){
        return $this->em->getRepository(static::$entityClass)->find($id);
    }

    /**
     * PUT is idempotent so always over-write with new resource (default properties)
     * @param $id
     * @return null|object
     */
    public function putOneAction($id){
        $item = new static::$entityClass($this->getRequest()->request->all());
        $item->setId((int)$id);
        $item->save(false);
        return $item;
    }

    /**
     * POST is used to modify and update a resource
     * (overwriting the entire resource, old contents are replaced with defaults when not sent)
     * @param $id
     * @return null|object
     * @throws \Exception
     */
    public function postAction($id){
        if(!$item = $this->em->getRepository(static::$entityClass)->find((int)$id)){
            throw new \Exception("Item not found, unable to update!");
        }
        $item = new static::$entityClass($this->getRequest()->request->all());
        $item->setId((int)$id);
        $item->save(false);
        return $item;
    }

    /**
     * PATCH is used to modify a resource by sending only partial parameters
     * @param $id
     * @return null|object
     * @throws \Exception
     */
    public function patchAction($id){
        if(!$item = $this->em->getRepository(static::$entityClass)->find((int)$id)){
            throw new \Exception("Item not found, unable to update!");
        }
        $item->exchangeArray($this->getRequest()->request->all());
        $item->setId((int)$id);
        $item->save(false);
        return $item;
    }

    /**
     * DELETE a single resource
     * @param $id
     * @throws \Exception
     */
    public function deleteAction($id){
        if(!$item = $this->em->getRepository(static::$entityClass)->find((int)$id)){
            throw new \Exception("Item not found, unable to update!");
        }
        $item->remove();
    }
}