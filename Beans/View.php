<?php
/**
 * Created by IntelliJ IDEA.
 * User: Night
 * Date: 9/7/13
 * Time: 10:58 PM
 * To change this template use File | Settings | File Templates.
 */
class View
{

    /** @var array $statList */
    private $statList;

    private $displayName;

    private $shortCode;

    private $defaultSortValue;


    //Construct Stat here
    public function __construct($name, array $stats = null, $shortCode = false, Statistic $defaultSort = null)
    {
        $this->displayName = $name;
        $this->shortCode = $shortCode;
        if (null == $stats || !is_array($stats)) {
            $this->statList = array();
        } else {
            $this->statList = $stats;
        }
        $this->defaultSortValue = $this->calculateSortValue($defaultSort);
    }


    public function getStatList()
    {
        return $this->statList;
    }

    public function addStat(Statistic $stat)
    {
        array_push($this->statList, $stat);
    }

    public function displayTableHeader()
    {
        $statTableHeader = "";
        /** @var Statistic $stat */
        foreach ($this->statList as $stat) {
            if ($this->shortCode) {
                $statName = $stat->getShortCode();
                $toolTip = $stat->getDisplayName();
            } else {
                $statName = $stat->getDisplayName();
                $toolTip = "";
            }
            $statTableHeader .= "<th title=\"{$toolTip}\">{$statName}</th>";
        }
        return $statTableHeader;
    }

    public function displayTableValues()
    {
        $statTableValue = "";
        /** @var Statistic $stat */
        foreach ($this->statList as $stat) {

            $statTableValue .= "<td>{$stat->getValue()}</td>";
        }
        return $statTableValue;
    }

    public function setShortCode($shortCode)
    {
        $this->shortCode = $shortCode;
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @return mixed
     */
    public function getDefaultSortValue()
    {
        return $this->defaultSortValue;
    }

    private function getIndex(Statistic $stat)
    {
        for ($i = 0; $i < count($this->statList); $i++) {
            if ($stat->getLogicalName() == $this->statList[$i]->getLogicalName()) {
                return $i;
            }
        }
        return null;
    }

    private function calculateSortValue($defaultSort)
    {
        if (null != $defaultSort) {
            $index = $this->getIndex($defaultSort);
            if (!(null === $index)) {
                $index++;
                return array("aaSorting" => "[[{$index}, \"desc\"]]");
            }
        }

        return array();

    }


}