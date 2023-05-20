<?php

class Message
{
    private $_id;
    private $_content;
    private $_is_customer;
    private $_image;
    private $_ins_datetime;
    private $_chat_id;

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
        $class['content'] = $this->get_content();
        $class['is_customer'] = $this->get_is_customer();
        $class['image'] = $this->get_image();
        $class['ins_datetime'] = $this->get_ins_datetime();

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
     * Get the value of _content
     */ 
    public function get_content()
    {
        return $this->_content;
    }

    /**
     * Set the value of _content
     *
     * @return  self
     */ 
    public function set_content($_content)
    {
        $this->_content = $_content;

        return $this;
    }

    /**
     * Get the value of _is_customer
     */ 
    public function get_is_customer()
    {
        return $this->_is_customer;
    }

    /**
     * Set the value of _is_customer
     *
     * @return  self
     */ 
    public function set_is_customer($_is_customer)
    {
        $this->_is_customer = $_is_customer;

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
     * Get the value of _chat_id
     */ 
    public function get_chat_id()
    {
        return $this->_chat_id;
    }

    /**
     * Set the value of _chat_id
     *
     * @return  self
     */ 
    public function set_chat_id($_chat_id)
    {
        $this->_chat_id = $_chat_id;

        return $this;
    }
}