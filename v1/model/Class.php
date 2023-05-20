<?php

class ClassModel
{
    private $_id;
    private $_date_id;
    private $_name;
    private $_max_participate;
    private $_end_date;
    private $_current_participate;
    private $_description;
    private $_benefit;
    private $_money;
    private $_image;
    private $_address;
    private $_date;
    private $_time;

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
        $class['date'] = $this->get_date_id();
        $class['name'] = $this->get_name();
        $class['end_date'] = $this->get_end_date();
        $class['max_participate'] = $this->get_max_participate();
        $class['current_participate'] = $this->get_current_participate();
        $class['description'] = $this->get_description();
        $class['benefit'] = $this->get_benefit();
        // $class['sum_month'][] = $this->get_sum_month();
        $class['money'] = $this->get_money();

        return $class;
    }


    public function returnOnlyClassAsArray()
    {
        $class = array();
        $class['id'] = $this->get_id();
        $class['name'] = $this->get_name();
        $class['end_date'] = $this->get_end_date();
        $class['current_participate'] = $this->get_current_participate();
        $class['description'] = $this->get_description();
        $class['benefit'] = $this->get_benefit();
        $class['image'] = $this->get_image();

        return $class;
    }

    public function returnListClassAsArray()
    {
        $class = array();
        $class['id'] = $this->get_id();
        $class['name'] = $this->get_name();
        $class['current_participate'] = $this->get_current_participate();
        $class['max_participate'] = $this->get_max_participate();
        $class['address'] = $this->get_address();
        $class['image'] = $this->get_image();
        $class['money'] = $this->get_money();
        $class['date'] = $this->get_date();
        $class['time'] = $this->get_time();

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
     * Get the value of _name
     */ 
    public function get_name()
    {
        return $this->_name;
    }

    /**
     * Set the value of _name
     *
     * @return  self
     */ 
    public function set_name($_name)
    {
        $this->_name = $_name;

        return $this;
    }

    /**
     * Get the value of _date_id
     */ 
    public function get_date_id()
    {
        return $this->_date_id;
    }

    /**
     * Set the value of _date_id
     *
     * @return  self
     */ 
    public function set_date_id($_date_id )
    {
        $this->_date_id = $_date_id;

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
     * Get the value of _description
     */ 
    public function get_description()
    {
        return $this->_description;
    }

    /**
     * Set the value of _description
     *
     * @return  self
     */ 
    public function set_description($_description)
    {
        $this->_description = $_description;

        return $this;
    }

    /**
     * Get the value of _benefit
     */ 
    public function get_benefit()
    {
        return $this->_benefit;
    }

    /**
     * Set the value of _benefit
     *
     * @return  self
     */ 
    public function set_benefit($_benefit)
    {
        $this->_benefit = $_benefit;

        return $this;
    }

    /**
     * Get the value of _end_date
     */ 
    public function get_end_date()
    {
        return $this->_end_date;
    }

    /**
     * Set the value of _end_date
     *
     * @return  self
     */ 
    public function set_end_date($_end_date)
    {
        $this->_end_date = $_end_date;

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
     * Get the value of _image
     */ 
    public function get_image()
    {
        return $this->_image;
    }

    /**
     * Set the value of _image
     *
     * @return  self
     */ 
    public function set_image($_image)
    {
        $this->_image = $_image;

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