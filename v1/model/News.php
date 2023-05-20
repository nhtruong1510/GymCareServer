<?php

class News
{
    private $_id;
    private $_title;
    private $_url;
    private $_image;
    private $_role;

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
        $class['title'] = $this->get_title();
        $class['url'] = $this->get_url();
        $class['image'] = $this->get_image();
        $class['role'] = $this->get_role();

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
     * Get the value of _title
     */ 
    public function get_title()
    {
        return $this->_title;
    }

    /**
     * Set the value of _title
     *
     * @return  self
     */ 
    public function set_title($_title)
    {
        $this->_title = $_title;

        return $this;
    }

    /**
     * Get the value of _url
     */ 
    public function get_url()
    {
        return $this->_url;
    }

    /**
     * Set the value of _url
     *
     * @return  self
     */ 
    public function set_url($_url)
    {
        $this->_url = $_url;

        return $this;
    }

    /**
     * Get the value of _role
     */ 
    public function get_role()
    {
        return $this->_role;
    }

    /**
     * Set the value of _role
     *
     * @return  self
     */ 
    public function set_role($_role)
    {
        $this->_role = $_role;

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
}