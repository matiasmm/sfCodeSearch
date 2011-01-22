<?php

namespace Application\IndexBundle\Document;

/**
 * @mongodb:Document(collection="file")
 */
class File
{
    /**
     * @mongodb:Id
     */
    public $id;

    /**
     * @mongodb:String
     */
    public $path;

    /**
    * @mongodb:String
    */
    public $source;

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($d){
	return $this->id = $id;
    }

}
