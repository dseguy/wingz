<?php
abstract class Wingz_Service_Joindin_Abstract
{
    /**
     * @var 	Wingz_Service_Joindin
     */
    protected $_joindin;
    
    /**
     * Constructor for the various service layers of joindin
     * 
     * @param null|Wingz_Service_Joindin $joindin
     */
    public function __construct($joindin = null)
    {
        if (null !== $joindin && $joindin instanceof Wingz_Service_Joindin) {
            $this->setJoindin($joindin);
        }
    }
    /**
     * Attaches the main joindin class to this service
     * 
     * @param 	Wingz_Service_Joindin $joindin
     * @return	Wingz_Service_Joindin_Site
     */
    public function setJoindin(Wingz_Service_Joindin $joindin)
    {
        $this->_joindin = $joindin;
        return $this;
    }
    /**
     * Retrieves the main joindin class from this service
     * 
     * @return	Wingz_Service_Joindin
     */
    public function getJoindin()
    {
        return $this->_joindin;
    }
}