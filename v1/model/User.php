<?php

class User
{
    private $_id;
    private $_username;
    private $_phone;
    private $_email;
    private $_password;
    private $_address;
    private $_avatar;
    private $_displayName;
    private $_comment;
    private $_rate;
    private $_reviewDate;
    private $_cartId;

    public function __construct($data)
    {
        foreach($data as $key=> $value) 
        {
            $this->$key =  $value;
        }
    }

    public function returnUserInforAsArray()
    {
        $user = array();
        $user['user_id'] = $this->get_id();
        $user['cart_id'] = $this->get_cartId();
        $user['username'] = $this->get_username();
        $user['email'] = $this->get_email();
        $user['phone'] = $this->get_phone();
        $user['avatar'] = $this->get_avatar();
        $user['display_name'] = $this->get_displayName();
        return $user;
    }

    public function returnReviewerAsArray()
    {
        $user = array();
        $user['user_id'] = $this->get_id();
        $user['username'] = $this->get_username();
        $user['avatar'] = $this->get_avatar();
        $user['display_name'] = $this->get_displayName();
        $user['comment'] = $this->get_comment();
        $user['rate'] = $this->get_rate();
        $user['review_date'] = $this->get_reviewDate();
        return $user;
    }

    public function returnUserReviewAsArray()
    {
        $user = array();
        $user['user_id'] = $this->get_id();
        $user['username'] = $this->get_username();
        $user['avatar'] = $this->get_avatar();
        $user['display_name'] = $this->get_displayName();
        $user['comment'] = $this->get_comment();
        $user['rate'] = $this->get_rate();
        $user['review_date'] = $this->get_reviewDate();
        return $user;
    }

    public function returnCustomerAsArray()
    {
        $user = array();
        $user['user_id'] = $this->get_id();
        $user['username'] = $this->get_username();
        $user['phone'] = $this->get_phone();
        return $user;
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
     * Get the value of _username
     */ 
    public function get_username()
    {
        return $this->_username;
    }

    /**
     * Set the value of _username
     *
     * @return  self
     */ 
    public function set_username($_username)
    {
        $this->_username = $_username;

        return $this;
    }

    /**
     * Get the value of _phone
     */ 
    public function get_phone()
    {
        return $this->_phone;
    }

    /**
     * Set the value of _phone
     *
     * @return  self
     */ 
    public function set_phone($_phone)
    {
        $this->_phone = $_phone;

        return $this;
    }

    /**
     * Get the value of _email
     */ 
    public function get_email()
    {
        return $this->_email;
    }

    /**
     * Set the value of _email
     *
     * @return  self
     */ 
    public function set_email($_email)
    {
        $this->_email = $_email;

        return $this;
    }

    /**
     * Get the value of _password
     */ 
    public function get_password()
    {
        return $this->_password;
    }

    /**
     * Set the value of _password
     *
     * @return  self
     */ 
    public function set_password($_password)
    {
        $this->_password = $_password;

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
     * Get the value of _displayName
     */ 
    public function get_displayName()
    {
        return $this->_displayName;
    }

    /**
     * Set the value of _displayName
     *
     * @return  self
     */ 
    public function set_displayName($_displayName)
    {
        $this->_displayName = $_displayName;

        return $this;
    }

    /**
     * Get the value of _avatar
     */ 
    public function get_avatar()
    {
        return $this->_avatar;
    }

    /**
     * Set the value of _avatar
     *
     * @return  self
     */ 
    public function set_avatar($_avatar)
    {
        $this->_avatar = $_avatar;

        return $this;
    }

    /**
     * Get the value of _comment
     */ 
    public function get_comment()
    {
        return $this->_comment;
    }

    /**
     * Set the value of _comment
     *
     * @return  self
     */ 
    public function set_comment($_comment)
    {
        $this->_comment = $_comment;

        return $this;
    }

    /**
     * Get the value of _rate
     */ 
    public function get_rate()
    {
        return $this->_rate;
    }

    /**
     * Set the value of _rate
     *
     * @return  self
     */ 
    public function set_rate($_rate)
    {
        $this->_rate = $_rate;

        return $this;
    }

    /**
     * Get the value of _reviewDate
     */ 
    public function get_reviewDate()
    {
        return $this->_reviewDate;
    }

    /**
     * Set the value of _reviewDate
     *
     * @return  self
     */ 
    public function set_reviewDate($_reviewDate)
    {
        $this->_reviewDate = $_reviewDate;

        return $this;
    }

    /**
     * Get the value of _cartId
     */ 
    public function get_cartId()
    {
        return $this->_cartId;
    }

    /**
     * Set the value of _cartId
     *
     * @return  self
     */ 
    public function set_cartId($_cartId)
    {
        $this->_cartId = $_cartId;

        return $this;
    }
}
