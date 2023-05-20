<?php

class Trainer
{
    private $_id;
    private $_name;
    private $_phone;
    private $_email;
    private $_password;
    private $_address;
    private $_avatar;
    private $_birth;
    private $_gender;
    private $_certificate;
    private $_experience;
    private $_workplace;
    private $_specialize;

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
        $user['id'] = $this->get_id();
        $user['name'] = $this->get_name();
        $user['email'] = $this->get_email();
        $user['phone'] = $this->get_phone();
        $user['avatar'] = $this->get_avatar();
        $user['birth'] = $this->get_birth();
        $user['gender'] = $this->get_gender();
        $user['address'] = $this->get_address();
        $user['certificate'] = $this->get_certificate();
        $user['experience'] = $this->get_experience();
        $user['workplace'] = $this->get_workplace();
        $user['specialize'] = $this->get_specialize();

        return $user;
    }

    // public function returnReviewerAsArray()
    // {
    //     $user = array();
    //     $user['user_id'] = $this->get_id();
    //     $user['username'] = $this->get_username();
    //     $user['avatar'] = $this->get_avatar();
    //     $user['display_name'] = $this->get_displayName();
    //     $user['comment'] = $this->get_comment();
    //     $user['rate'] = $this->get_rate();
    //     $user['review_date'] = $this->get_reviewDate();
    //     return $user;
    // }

    // public function returnUserReviewAsArray()
    // {
    //     $user = array();
    //     $user['user_id'] = $this->get_id();
    //     $user['username'] = $this->get_username();
    //     $user['avatar'] = $this->get_avatar();
    //     $user['display_name'] = $this->get_displayName();
    //     $user['comment'] = $this->get_comment();
    //     $user['rate'] = $this->get_rate();
    //     $user['review_date'] = $this->get_reviewDate();
    //     return $user;
    // }

    // public function returnCustomerAsArray()
    // {
    //     $user = array();
    //     $user['user_id'] = $this->get_id();
    //     $user['username'] = $this->get_username();
    //     $user['phone'] = $this->get_phone();
    //     return $user;
    // }


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
     * Get the value of _birth
     */ 
    public function get_birth()
    {
        return $this->_birth;
    }

    /**
     * Set the value of _birth
     *
     * @return  self
     */ 
    public function set_birth($_birth)
    {
        $this->_birth = $_birth;

        return $this;
    }

    /**
     * Get the value of _gender
     */ 
    public function get_gender()
    {
        return $this->_gender;
    }

    /**
     * Set the value of _gender
     *
     * @return  self
     */ 
    public function set_gender($_gender)
    {
        $this->_gender = $_gender;

        return $this;
    }

    /**
     * Get the value of _certificate
     */ 
    public function get_certificate()
    {
        return $this->_certificate;
    }

    /**
     * Set the value of _certificate
     *
     * @return  self
     */ 
    public function set_certificate($_certificate)
    {
        $this->_certificate = $_certificate;

        return $this;
    }

    /**
     * Get the value of _experience
     */ 
    public function get_experience()
    {
        return $this->_experience;
    }

    /**
     * Set the value of _experience
     *
     * @return  self
     */ 
    public function set_experience($_experience)
    {
        $this->_experience = $_experience;

        return $this;
    }

    /**
     * Get the value of _workplace
     */ 
    public function get_workplace()
    {
        return $this->_workplace;
    }

    /**
     * Set the value of _workplace
     *
     * @return  self
     */ 
    public function set_workplace($_workplace)
    {
        $this->_workplace = $_workplace;

        return $this;
    }

    /**
     * Get the value of _specialize
     */ 
    public function get_specialize()
    {
        return $this->_specialize;
    }

    /**
     * Set the value of _specialize
     *
     * @return  self
     */ 
    public function set_specialize($_specialize)
    {
        $this->_specialize = $_specialize;

        return $this;
    }
}
