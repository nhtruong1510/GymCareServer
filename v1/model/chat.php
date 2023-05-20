<?php

class Chat
{
    private $_id;
    private $_customer;
    private $_trainer;
    private $_is_read_trainer;
    private $_is_read_customer;
    private $_ins_datetime;
    private $_last_message;

    public function __construct($data)
    {
        foreach($data as $key=> $value) 
        {
            $this->$key =  $value;
        }
    }

    public function returnClassAsArray()
    {
        $class = array();
        $class['id'] = $this->get_id();
        $class['customer'] = $this->get_customer();
        $class['trainer'] = $this->get_trainer();
        $class['is_read_customer'] = $this->get_is_read_customer();
        $class['is_read_trainer'] = $this->get_is_read_trainer();
        $class['ins_datetime'] = $this->get_ins_datetime();
        $class['last_message'] = $this->get_last_message();

        return $class;
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
     * Get the value of _trainer
     */ 
    public function get_trainer()
    {
        return $this->_trainer;
    }

    /**
     * Set the value of _trainer
     *
     * @return  self
     */ 
    public function set_trainer($_trainer)
    {
        $this->_trainer = $_trainer;

        return $this;
    }

    /**
     * Get the value of _is_read_trainer
     */ 
    public function get_is_read_trainer()
    {
        return $this->_is_read_trainer;
    }

    /**
     * Set the value of _is_read_trainer
     *
     * @return  self
     */ 
    public function set_is_read_trainer($_is_read_trainer)
    {
        $this->_is_read_trainer = $_is_read_trainer;

        return $this;
    }

    /**
     * Get the value of _is_read_customer
     */ 
    public function get_is_read_customer()
    {
        return $this->_is_read_customer;
    }

    /**
     * Set the value of _is_read_customer
     *
     * @return  self
     */ 
    public function set_is_read_customer($_is_read_customer)
    {
        $this->_is_read_customer = $_is_read_customer;

        return $this;
    }

    /**
     * Get the value of _ins_datetime
     */ 
    public function get_ins_datetime()
    {
        return $this->_ins_datetime;
    }

    /**
     * Set the value of _ins_datetime
     *
     * @return  self
     */ 
    public function set_ins_datetime($_ins_datetime)
    {
        $this->_ins_datetime = $_ins_datetime;

        return $this;
    }

    /**
     * Get the value of _last_message
     */ 
    public function get_last_message()
    {
        return $this->_last_message;
    }

    /**
     * Set the value of _last_message
     *
     * @return  self
     */ 
    public function set_last_message($_last_message)
    {
        $this->_last_message = $_last_message;

        return $this;
    }
}