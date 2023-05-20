<?php

class Date
{
    private $_id;
    private $_date;
    private $_day;
    private $_time;

    public function __construct($data)
    {
        foreach($data as $key=> $value) 
        {
            $this->$key =  $value;
        }
    }

    public function returnDateAsArray()
    {
        $date = array();
        $date['id'] = $this->get_id();
        $date['date'] = $this->get_date();
        $date['day'] = $this->get_day();
        $date['time'] = $this->get_time();
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
     * Get the value of _date
     */ 
    public function get_date()
    {
        return $this->_date;
    }

    /**
     * Set the value of _date
     *
     * @return  self
     */ 
    public function set_date($_date)
    {
        $this->_date = $_date;

        return $this;
    }

    /**
     * Get the value of _day
     */ 
    public function get_day()
    {
        return $this->_day;
    }

    /**
     * Set the value of _day
     *
     * @return  self
     */ 
    public function set_day($_day)
    {
        $this->_day = $_day;

        return $this;
    }

    /**
     * Get the value of _time
     */ 
    public function get_time()
    {
        return $this->_time;
    }

    /**
     * Set the value of _time
     *
     * @return  self
     */ 
    public function set_time($_time)
    {
        $this->_time = $_time;

        return $this;
    }
}