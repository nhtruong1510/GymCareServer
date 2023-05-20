<?php

class Payment
{
    private $_id;
    private $_class;
    private $_trainer;
    private $_address;
    private $_customer;
    private $_date;
    private $_day;
    private $_time;
    private $_money;
    private $_date_create;
    private $_register;

    public function __construct($data)
    {
        foreach($data as $key=> $value) 
        {
            $this->$key =  $value;
        }
    }

    public function returnClassAsArray()
    {
        $date = array();
        $date['id'] = $this->get_id();
        $date['date'] = $this->get_date();
        $date['trainer'] = $this->get_trainer();
        $date['customer'] = $this->get_customer();
        $date['class'] = $this->get_class();
        $date['address'] = $this->get_address();
        $date['day'] = $this->get_day();
        $date['time'] = $this->get_time();
        $date['money'] = $this->get_money();
        $date['date_create'] = $this->get_date_create();

        return $date;
    }

    public function returnStatisticAsArray()
    {
        $date = array();
        $date['money'] = $this->get_money();
        $date['date_create'] = $this->get_date_create();
        $date['class'] = $this->get_class();

        return $date;
    }

    public function returnRegisterStatisticAsArray()
    {
        $date = array();
        $date['register'] = $this->get_register();
        $date['date_create'] = $this->get_date_create();
        $date['class'] = $this->get_class();

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
     * Get the value of _class_id
     */ 
    public function get_class()
    {
        return $this->_class;
    }

    /**
     * Set the value of _class_id
     *
     * @return  self
     */ 
    public function set_class($_class)
    {
        $this->_class = $_class;

        return $this;
    }

    /**
     * Get the value of _trainer_id
     */ 
    public function get_trainer()
    {
        return $this->_trainer;
    }

    /**
     * Set the value of _trainer_id
     *
     * @return  self
     */ 
    public function set_trainer($_trainer)
    {
        $this->_trainer = $_trainer;

        return $this;
    }

    /**
     * Get the value of _customer_id
     */ 
    public function get_customer()
    {
        return $this->_customer;
    }

    /**
     * Set the value of _customer_id
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

    /**
     * Get the value of _money
     */ 
    public function get_money()
    {
        return $this->_money;
    }

    /**
     * Set the value of _money
     *
     * @return  self
     */ 
    public function set_money($_money)
    {
        $this->_money = $_money;

        return $this;
    }

    /**
     * Get the value of _date_create
     */ 
    public function get_date_create()
    {
        return $this->_date_create;
    }

    /**
     * Set the value of _date_create
     *
     * @return  self
     */ 
    public function set_date_create($_date_create)
    {
        $this->_date_create = $_date_create;

        return $this;
    }

    /**
     * Get the value of _register
     */ 
    public function get_register()
    {
        return $this->_register;
    }

    /**
     * Set the value of _register
     *
     * @return  self
     */ 
    public function set_register($_register)
    {
        $this->_register = $_register;

        return $this;
    }
}