<?php

class Address
{
    private $_id;
    private $_lattitude;
    private $_longitude;
    private $_image;
    private $_address;
    private $_class;

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
        $class['lattitude'] = $this->get_lattitude();
        $class['longitude'] = $this->get_longitude();
        $class['image'] = $this->get_image();
        $class['class'] = $this->get_class();
        $class['address'] = $this->get_address();

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
     * Get the value of _lattitude
     */ 
    public function get_lattitude()
    {
        return $this->_lattitude;
    }

    /**
     * Set the value of _lattitude
     *
     * @return  self
     */ 
    public function set_lattitude($_lattitude)
    {
        $this->_lattitude = $_lattitude;

        return $this;
    }

    /**
     * Get the value of _longitude
     */ 
    public function get_longitude()
    {
        return $this->_longitude;
    }

    /**
     * Set the value of _longitude
     *
     * @return  self
     */ 
    public function set_longitude($_longitude)
    {
        $this->_longitude = $_longitude;

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
     * Get the value of _classes
     */ 
    public function get_class()
    {
        return $this->_class;
    }

    /**
     * Set the value of _classes
     *
     * @return  self
     */ 
    public function set_class($_class)
    {
        $this->_class = $_class;

        return $this;
    }
}