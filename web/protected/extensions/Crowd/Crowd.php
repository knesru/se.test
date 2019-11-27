<?php
/**
 * PHP Client Library for Atlassian Crowd
 *
 * Copyright (C) 2008 Infinite Campus, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Crowd SecurityServer SOAP Client
 */
class Crowd
{
    private $last_error;
    private $crowd_client;
    private $crowd_config;
    private $crowd_app_token;

    /**
     * Create an application client using the passed in configuration parameters.
     * @param $config
     * @throws CrowdConnectionException
     */
    function __construct($config)
    {
        $this->crowd_config = $config;

        // Create the Crowd SOAP client
        try
        {
            $this->crowd_client = new SoapClient($this->crowd_config['service_url']);
        }
        catch (SoapFault $fault)
        {
            $this->last_error =  "SOAP Fault: faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring}";
            throw new CrowdConnectionException("Unable to connect to Crowd.  Verify the service_url property is defined and Crowd is running.");
        }
    }

    /**
     * Authenticates an application client to the Crowd security server.
     */
    function authenticateApplication()
    {
        $param = array('in0' => array('credential' => array('credential' => $this->crowd_config['app_credential']),
                                      'name'       => $this->crowd_config['app_name']));
        try
        {
            $resp = $this->crowd_client->authenticateApplication($param);
        }
        catch (SoapFault $fault)
        {
           $this->last_error = "SOAP Fault: faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring}";
           return null;
        }

        $this->crowd_app_token = $resp->out->token;

        if ( empty($this->crowd_app_token) )
        {
            $this->last_error =  "Unable to login to Crowd.  Verify the app_name and app_credential properties are defined and valid.";
            return null;
        }
        else
        {
            return $this->crowd_app_token;
        }
    }

    /**
     * Authenticates a principal to the Crowd security server for the application client.
     */
    function authenticatePrincipal($name, $credential, $user_agent, $remote_address) {

        // Build the parameter used to authenticate the principal
        $param = array('in0' => array('name'        => $this->crowd_config['app_name'],
                                      'token'       => $this->crowd_app_token),
                       'in1' => array('application' => $this->crowd_config['app_name'],
                                      'credential'  => array('credential' => $credential),
                                      'name'        => $name,
                                      'validationFactors' => array( array('name'  => 'User-Agent',
                                                                          'value' => $user_agent),
                                                                    array('name'  => 'remote_address', 
                                                                          'value' => $remote_address) ) ) );

        // Attempt to authenticate the user (principal) via Crowd.
        try
        {
            $resp = $this->crowd_client->authenticatePrincipal($param);
        }
        catch (SoapFault $fault)
        {
            $this->last_error = "SOAP Fault: faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring}";
            return null;
        }

        // Get the principal's token
        $princ_token = $resp->out;

        return $princ_token;
    }

    /**
     * Determines if the principal's current token is still valid in Crowd.
     */
    function isValidPrincipalToken($princ_token, $user_agent, $remote_address)
   {

        // Determine if the principal is still valid in Crowd
        $param = array('in0' => array('name'        => $this->crowd_config['app_name'],
                                      'token'       => $this->crowd_app_token),
                       'in1' => $princ_token,
                       'in2' => array( array('name'  => 'User-Agent',
                                             'value' => $user_agent),
                                       array('name'  => 'remote_address', 
                                             'value' => $remote_address) ) );

        try
        {
            $resp = $this->crowd_client->isValidPrincipalToken($param);
        }
        catch (SoapFault $fault)
        {
            $this->last_error =  "SOAP Fault: faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring}";
            return null;
        }

        $valid_token = $resp->out;
        
        return $valid_token;
    }

    /**
     * Invalidates a token for for this princpal for all application clients in Crowd.
     */
    function invalidatePrincipalToken($princ_token)
    {

        // Invalidate the principal's token in Crowd
        $param = array('in0' => array('name'  => $this->crowd_config['app_name'],
                                      'token' => $this->crowd_app_token),
                       'in1' => $princ_token);

        try
        {
            $resp = $this->crowd_client->invalidatePrincipalToken($param);
            return true;
        } 
        catch (SoapFault $fault)
        {
            echo "SOAP Fault: faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring}"; 
        }
        return false;
    }

    /**
     * Finds a principal by token.
     */
    function findPrincipalByToken($princ_token)
    {

        $param = array('in0' => array('name'  => $this->crowd_config['app_name'],
                                      'token' => $this->crowd_app_token),
                       'in1' => $princ_token);

        try
        {
            $resp = $this->crowd_client->findPrincipalByToken($param);
            return $resp->out;
        } 
        catch (SoapFault $fault)
        {
            $this->last_error =  "SOAP Fault: faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring}";
            return null;
        }
    }

    /**
     * Finds all of the groups the specified principal is in.
     */
    function findGroupMemberships($princ_name)
    {

        $param = array('in0' => array('name'  => $this->crowd_config['app_name'],
                                      'token' => $this->crowd_app_token),
                       'in1' => $princ_name);

        try
        {
            $resp = $this->crowd_client->findGroupMemberships($param);
            return $resp->out;
        } 
        catch (SoapFault $fault)
        {
            $this->last_error = "SOAP Fault: faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring}";
            return null;
        }
    }


    /**
     * Finds all of the principals who are visable in the application's assigned directory.
     *
     * @return array
     */
    public function findAllPrincipalNames()
    {
        $param = array('in0' => array('name'  => $this->crowd_config['app_name'],
                                      'token' => $this->crowd_app_token));
        try
        {
            $resp = $this->crowd_client->findAllPrincipalNames($param);
        }
        catch (SoapFault $fault)
        {
            $this->last_error = "SOAP Fault: faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring}";
            return null;
        }
        if (isset($resp->out->string))
        {
            return array_values((array) $resp->out->string);
        }
        else
        {
            return array_values((array) $resp->out);
        }
    }


  /**
     * Finds all of the groups who are visible in the application's assigned directory.
     *
     * @return array
     */
    public function findAllGroupNames()
    {
        $param = array('in0' => array('name'  => $this->crowd_config['app_name'],
                                      'token' => $this->crowd_app_token));
        try
        {
            $resp = $this->crowd_client->findAllGroupNames($param);
        }
        catch (SoapFault $fault)
        {
            $this->last_error = "SOAP Fault: faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring}";
            return null;
        }
        
        if (isset($resp->out->string))
        {
            return array_values((array) $resp->out->string);
        }
        else
        {
            return array_values((array) $resp->out);
        }
    }

  /**
     * Finds a principal by name who is in the application's assigned directory.
     *
     * @param string $name
     * @return Atlassian_Crowd_Entity_Principal
     */
    public function findPrincipalByName($name)
    {
        $param = array('in0' => array('name'  => $this->crowd_config['app_name'],
                                      'token' => $this->crowd_app_token),
                       'in1' => (string) $name);
        try
        {
            $principalReturn = $this->crowd_client->findPrincipalByName($param);
        }
        catch (SoapFault $e)
        {
            $this->last_error = "SOAP Fault: faultcode: {$e->faultcode}, faultstring: {$e->faultstring}";
            return null;
        }

        $principal = new Atlassian_Crowd_Entity_Principal();
        $principal->setFromArray((array) $principalReturn->out);
        return $principal;  
    }


    /**
     * Find a group by name for the application's assigned directory.
     *
     * @param string $name
     * @return Zym_Service_Atlassian_Crowd_Entity_Group
     */
    public function findGroupByName($name)
    {
        $param = array('in0' => array('name'  => $this->crowd_config['app_name'],
                                      'token' => $this->crowd_app_token),
                       'in1' => (string) $name);

        try
        {
            $groupReturn = $this->crowd_client->findGroupByName($param);
        }
        catch (SoapFault $e)
        {
            $this->last_error = "SOAP Fault: faultcode: {$e->faultcode}, faultstring: {$e->faultstring}";
            return null;
        }

        $group = new Atlassian_Crowd_Entity_Group();
        $group->setFromArray((array) $groupReturn->out);
        return $group;
    }


   /**
     * Finds all of the principals who are members of a role that is in the application's assigned directory.
     *
     * @param string $principalName
     * @return array
     */
    public function findRoleMemberships($principalName)
    {
        $param = array('in0' => array('name'  => $this->crowd_config['app_name'],
                                      'token' => $this->crowd_app_token),
                       'in1' => (string) $principalName);

        try
        {
            $return = $this->crowd_client->findRoleMemberships($param);
        }
        catch (SoapFault $e)
        {
            $this->last_error = "SOAP Fault: faultcode: {$e->faultcode}, faultstring: {$e->faultstring}";
            return null;
        }

        if (isset($return->out->string))
        {
            return array_values((array) $return->out->string);
        }
        else
        {
            return array_values((array) $return->out);
        }
    }

    /**
     * Return last occured error.
     */
    public function GetLastError()
    {
        return $this->last_error;

    }
}

/**
 * Exception used to indicate a problem connecting to the Crowd server.
 */
class CrowdConnectionException extends Exception
{

}

/**
 * Exception used to incidate a problem authenticating to the Crowd server.
 */
class CrowdLoginException extends Exception
{

}


?>
