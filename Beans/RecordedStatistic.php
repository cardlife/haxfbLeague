<?php
/**
 * Created by IntelliJ IDEA.
 * User:
 * Date: 8/1/13
 * Time: 10:13 PM
 * To change this template use File | Settings | File Templates.
 */

//namespace hfl\Beans\RecordedStatistic;
include_once("Statistic.php");

class RecordedStatistic implements  Statistic{
    private $displayName;
    private $logicalName;
    private $value;
    private $shortCode;

    //Construct Stat here
    function __construct($logicalName, $shortCode, $displayName)
    {
       $this->logicalName = $logicalName;
        $this->displayName = $displayName;
        $this->shortCode = $shortCode;
    }


    /**
     * This is a Statistic interface that defines what each one should implement
     */
    public function getSelectPart()
    {
        return "IFNULL(SUM(stats.$this->logicalName),0)";
    }

    public function getFromPart()
    {
        // TODO: Implement getFromPart() method.
    }

    public function getWherePart()
    {
        // TODO: Implement getWherePart() method.
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * This is a Statistic interface that defines what each one should implement
     */
    public function getLogicalName()
    {
       return $this->logicalName;
    }

    public function getDisplayName()
    {
       return $this->displayName;
    }

    public function getSelectPartWithLabel()
    {
        return "{$this->getSelectPart()} as $this->logicalName";
    }

    public function getShortCode()
    {
       return $this->shortCode;
    }
}