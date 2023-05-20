<?php

class Time
{
    private $_id;
    private $_time;
    private $_is_cancelled;
    private $_trainer_id;
    private $_class;
    private $_address;
    private $_max_participate;
    private $_current_participate;
    private $_customer;
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
        $date['time'] = $this->get_time();
        $date['is_cancelled'] = $this->get_is_cancelled();
        $date['trainer_id'] = $this->get_trainer_id();
        $date['class'] = $this->get_class();
        $date['address'] = $this->get_address();
        $date['max_participate'] = $this->get_max_participate();
        $date['current_participate'] = $this->get_current_participate();

        return $date;
    }

    public function returnTimeAsClass()
    {
        $date = array();
        $date['id'] = $this->get_id();
        $date['time'] = $this->get_time();
        $date['date'] = $this->get_date();
        $date['customer'] = $this->get_customer();

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

    /**
     * Get the value of _is_cancelled
     */ 
    public function get_is_cancelled()
    {
        return $this->_is_cancelled;
    }

    /**
     * Set the value of _is_cancelled
     *
     * @return  self
     */ 
    public function set_is_cancelled($_is_cancelled)
    {
        $this->_is_cancelled = $_is_cancelled;

        return $this;
    }

    /**
     * Get the value of _trainer_id
     */ 
    public function get_trainer_id()
    {
        return $this->_trainer_id;
    }

    /**
     * Set the value of _trainer_id
     *
     * @return  self
     */ 
    public function set_trainer_id($_trainer_id)
    {
        $this->_trainer_id = $_trainer_id;

        return $this;
    }

    /**
     * Get the value of _class
     */ 
    public function get_class()
    {
        return $this->_class;
    }

    /**
     * Set the value of _class
     *
     * @return  self
     */ 
    public function set_class($_class)
    {
        $this->_class = $_class;

        return $this;
    }

    /**
     * Get the value of _address
     */ 
    public function get_address()
    {
        return $this->_address;
    }

    /**
     * Set the value of _address
     *
     * @return  self
     */ 
    public function set_address($_address)
    {
        $this->_address = $_address;

        return $this;
    }

    /**
     * Get the value of _max_participate
     */ 
    public function get_max_participate()
    {
        return $this->_max_participate;
    }

    /**
     * Set the value of _max_participate
     *
     * @return  self
     */ 
    public function set_max_participate($_max_participate)
    {
        $this->_max_participate = $_max_participate;

        return $this;
    }

    /**
     * Get the value of _current_participate
     */ 
    public function get_current_participate()
    {
        return $this->_current_participate;
    }

    /**
     * Set the value of _current_participate
     *
     * @return  self
     */ 
    public function set_current_participate($_current_participate)
    {
        $this->_current_participate = $_current_participate;

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