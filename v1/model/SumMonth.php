<?php

class SumMonth
{
    private $_id;
    private $_number_month;

    public function __construct($data)
    {
        foreach($data as $key=> $value) 
        {
            $this->$key =  $value;
        }
    }

    public function returnSumMonthAsArray()
    {
        $date = array();
        $date['id'] = $this->get_id();
        $date['number_month'] = $this->get_number_month();
        return $date;
    }

    /**
     * Get the value of _id
     */ 
    public function get_id()
    {
        return $this->_id;
    }

    /**
     * Set the value of _id
     *
     * @return  self
     */ 
    public function set_id($_id)
    {
        $this->_id = $_id;

        return $this;
    }

    /**
     * Get the value of _number_month
     */ 
    public function get_number_month()
    {
        return $this->_number_month;
    }

    /**
     * Set the value of _number_month
     *
     * @return  self
     */ 
    public function set_number_month($_number_month)
    {
        $this->_number_month = $_number_month;

        return $this;
    }
}