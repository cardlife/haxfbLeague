<?php
/**
 * Created by IntelliJ IDEA.
 * User:
 * Date: 8/1/13
 * Time: 10:13 PM
 * To change this template use File | Settings | File Templates.
 */

//namespace hfl\Beans\Statistic;


interface Statistic
{
    /**
     * This is a Statistic interface that defines what each one should implement
     */

    public function getLogicalName();

    public function getDisplayName();

    public function getShortCode();

    public function getSelectPartWithLabel();

    public function getSelectPart();

    public function getFromPart();

    public function getWherePart();

    public function getValue();

    public function setValue($value);

}