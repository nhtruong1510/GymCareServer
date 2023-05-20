<?php

class Schedule
{
    private $_id;
    private $_class;
    private $_trainer;
    private $_address;
    private $_is_cancelled;
    private $_customer;
    private $_date;
    private $_sum_session;
    private $_day;
    private $_time;
    private $_start_date;
    private $_end_date;
    private $_change_number;
    private $_date_id;
    private $_time_id;

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
        $date['start_date'] = $this->get_start_date();
        $date['end_date'] = $this->get_end_date();
        $date['change_number'] = $this->get_change_number();
        $date['date_id'] = $this->get_date_id();
        $date['time_id'] = $this->get_time_id();

        return $date;
    }

    public function returnScheduleAsArray()
    {
        $date = array();
        $date['class'] = $this->get_class();
        $date['address'] = $this->get_address();
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
     * Get the value of _sum_session
     */ 
    public function get_sum_session()
    {
        return $this->_sum_session;
    }

    /**
     * Set the value of _sum_session
     *
     * @return  self
     */ 
    public function set_sum_session($_sum_session)
    {
        $this->_sum_session = $_sum_session;

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
     * Get the value of _start_date
     */ 
    public function get_start_date()
    {
        return $this->_start_date;
    }

    /**
     * Set the value of _start_date
     *
     * @return  self
     */ 
    public function set_start_date($_start_date)
    {
        $this->_start_date = $_start_date;

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
     * Get the value of _change_number
     */ 
    public function get_change_number()
    {
        return $this->_change_number;
    }

    /**
     * Set the value of _change_number
     *
     * @return  self
     */ 
    public function set_change_number($_change_number)
    {
        $this->_change_number = $_change_number;

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
    public function set_date_id($_date_id)
    {
        $this->_date_id = $_date_id;

        return $this;
    }

    /**
     * Get the value of _time_id
     */ 
    public function get_time_id()
    {
        return $this->_time_id;
    }

    /**
     * Set the value of _time_id
     *
     * @return  self
     */ 
    public function set_time_id($_time_id)
    {
        $this->_time_id = $_time_id;

        return $this;
    }
}