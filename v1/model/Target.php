<?php

class Target
{
    private $_id;
    private $_customer;
    private $_walk_number;
    private $_sleep;
    private $_distance;
    private $_excercise;
    private $_heart_rate;
    private $_customer_id;
    private $_date;

    public function __construct($data)
    {
        foreach($data as $key=> $value) 
        {
            $this->$key =  $value;
        }
    }

    public function returnTimeAsArray()
    {
        $date = array();
        $date['id'] = $this->get_id();
        // $date['customer'] = $this->get_customer();
        $date['walk_number'] = $this->get_walk_number();
        $date['sleep'] = $this->get_sleep();
        $date['distance'] = $this->get_distance();

        return $date;
    }

    public function returnHealthAsArray()
    {
        $date = array();
        $date['id'] = $this->get_id();
        // $date['customer'] = $this->get_customer();
        $date['walk_number'] = $this->get_walk_number();
        $date['sleep'] = $this->get_sleep();
        $date['distance'] = $this->get_distance();
        $date['excercise'] = $this->get_excercise();
        $date['heart_rate'] = $this->get_heart_rate();
        $date['customer_id'] = $this->get_customer_id();
        $date['date'] = $this->get_date();

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
     * Get the value of _walk_number
     */ 
    public function get_walk_number()
    {
        return $this->_walk_number;
    }

    /**
     * Set the value of _walk_number
     *
     * @return  self
     */ 
    public function set_walk_number($_walk_number)
    {
        $this->_walk_number = $_walk_number;

        return $this;
    }

    /**
     * Get the value of _sleep
     */ 
    public function get_sleep()
    {
        return $this->_sleep;
    }

    /**
     * Set the value of _sleep
     *
     * @return  self
     */ 
    public function set_sleep($_sleep)
    {
        $this->_sleep = $_sleep;

        return $this;
    }

    /**
     * Get the value of _distance
     */ 
    public function get_distance()
    {
        return $this->_distance;
    }

    /**
     * Set the value of _distance
     *
     * @return  self
     */ 
    public function set_distance($_distance)
    {
        $this->_distance = $_distance;

        return $this;
    }

    /**
     * Get the value of _customer
     */ 
    public function get_customer()
    {
        return $this->_customer;
    }

    /**
     * Set the value of _customer
     *
     * @return  self
     */ 
    public function set_customer($_customer)
    {
        $this->_customer = $_customer;

        return $this;
    }

    /**
     * Get the value of _excercise
     */ 
    public function get_excercise()
    {
        return $this->_excercise;
    }

    /**
     * Set the value of _excercise
     *
     * @return  self
     */ 
    public function set_excercise($_excercise)
    {
        $this->_excercise = $_excercise;

        return $this;
    }

    /**
     * Get the value of _heart_rate
     */ 
    public function get_heart_rate()
    {
        return $this->_heart_rate;
    }

    /**
     * Set the value of _heart_rate
     *
     * @return  self
     */ 
    public function set_heart_rate($_heart_rate)
    {
        $this->_heart_rate = $_heart_rate;

        return $this;
    }

    /**
     * Get the value of _customer_id
     */ 
    public function get_customer_id()
    {
        return $this->_customer_id;
    }

    /**
     * Set the value of _customer_id
     *
     * @return  self
     */ 
    public function set_customer_id($_customer_id)
    {
        $this->_customer_id = $_customer_id;

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
}