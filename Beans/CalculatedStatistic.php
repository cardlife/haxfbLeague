<?php
/**
 * Created by IntelliJ IDEA.
 * User:
 * Date: 8/1/13
 * Time: 10:14 PM
 * To change this template use File | Settings | File Templates.
 */

//namespace hfl\Beans\CalculatedStatistic;


class CalculatedStatistic implements Statistic
{

    private $logicalName,
        $shortCode,
        $displayName,
        $selectPart,
        $fromPart,
        $wherePart,
        $value;


    function __construct($logicalName, $shortCode, $displayName, $selectPart = "", $fromPart = "", $wherePart = "")
    {
        $this->logicalName = $logicalName;
        $this->shortCode = $shortCode;
        $this->displayName = $displayName;
        $this->selectPart = trim($selectPart);
        $this->fromPart = trim($fromPart);
        $this->wherePart = trim($wherePart);
    }

    /**
     * This is a Statistic interface that defines what each one should implement
     */
    public function getSelectPart()
    {
        return "IFNULL({$this->selectPart}, 0)";
    }

    public function getFromPart()
    {
        return $this->fromPart;
    }

    public function getWherePart()
    {
        return $this->wherePart;
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
        return "{$this->getSelectPart()} as {$this->logicalName}";
    }

    public function getShortCode()
    {
        return $this->shortCode;
    }
}