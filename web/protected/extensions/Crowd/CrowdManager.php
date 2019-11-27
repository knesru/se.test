<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * CrowdManager provide all functionality for STMS and Crowd
 *
 * @author Advantex
 */
class CrowdManager
{
    private $_app_token = null;
    private $_princ_token = null;
    private $_lastError = null;
    private $_crowd = null;

    private function authenticateApplication()
    {
      //Get crowd configuration settings

      //Load CROWD Config from custom yml, because app.yml show in debug mode in browser
      $crowdcfg = sfYaml::load(dirname(__FILE__).'/../../../config/crowd.yml');
      $app_name = $crowdcfg['all']['crowd_config']['app_name'];
      $app_password = $crowdcfg['all']['crowd_config']['app_password'];
      $service_url = $crowdcfg['all']['crowd_config']['service_url'];

        try
        {
            $config = array('app_name'   => $app_name,
                            'app_credential' => $app_password,
                            'service_url'    => $service_url);
            $this->_crowd = new Crowd($config);
            $this->_app_token = $this->_crowd->authenticateApplication();
            $this->_lastError = $this->_crowd->GetLastError();
        }
        catch(Exception $e)
        {
            $this->_lastError = $e->getMessage();
        }
        return $this->_app_token != null && (strlen($this->_app_token) > 1) ? true : false;
    }

    private function checkAppAuth()
    {
        if($this->_app_token == null )
            if(!$this->authenticateApplication())
                return false;
        return true;
    }

    /**
     * authenticate crowd principal
     */
    public function authenticatePrincipal($username, $password)
    {
        //
        if(!$this->checkAppAuth())
                return false;
        //
        try
        {
            $this->_princ_token = $this->_crowd->authenticatePrincipal($username, $password, $_SERVER['HTTP_USER_AGENT'], $_SERVER['REMOTE_ADDR']);
            $this->_lastError = $this->_crowd->GetLastError();
            return $this->_princ_token != null && (strlen($this->_princ_token) > 1) ? true : false;
        }
        catch(Exception $e)
        {
            $this->_lastError = $e->getMessage();
        }
        return false;
    }    

    /**
     * Get Roles for principal
     */
    public function getPrincipalRoles($username)
    {
        //
        if(!$this->checkAppAuth())
                return false;
        $roles =  $this->_crowd->findRoleMemberships($username);
        $this->_lastError = $this->_crowd->GetLastError();
        return $roles;
    }

    /*
     * Return true is crowd user with specified name exist
     */
    public function IsCrowdPrincipalExist($username, &$userRet)
    {
        //
        if(!$this->checkAppAuth())
                return false;
        //
        $userRet =  $this->_crowd->findPrincipalByName($username);
        $this->_lastError = $this->_crowd->GetLastError();
        return ($userRet != null && $userRet->getName() == $username) ? true : false;
    }

    /*
     * Return all crowd users
     */
    public function GetAllCrowdPrincipals()
    {        
        if(!$this->checkAppAuth())
        {            
            return false;
        }
        $userRet =  $this->_crowd->findAllPrincipalNames();
        $this->_lastError = $this->_crowd->GetLastError();
        return $userRet;
    }


    /**
     * Return last occured error.
     */
    public function GetLastError()
    {
        return $this->_lastError;
    }
}
?>
