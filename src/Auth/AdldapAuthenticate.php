<?php
namespace ActiveDirectoryAuthenticateMock\Auth;

use Cake\Auth\FormAuthenticate;
use Cake\Controller\ComponentRegistry;
use Cake\Network\Request;
use Cake\Network\Response;

class AdldapAuthenticateMock extends FormAuthenticate
{
    /**
     * Constructor
     *
     * AdldapAuthenticateMock uses a configuration array which matches the configuration
     * values from the Adldap2 library. For more specific information on these settings
     * see the Adldap2 documentation: https://github.com/Adldap2/Adldap2
     *
     * @param \Cake\Controller\ComponentRegistry $registry The Component registry
     *   used on this request.
     * @param array $config Array of config to use.
     */
    public function __construct(ComponentRegistry $registry, $config)
    {
        $this->registry = $registry;

        $this->config([
            'config' => [],
            'ignored' => [
                'distinguishedname',
                'dn',
                'objectcategory',
                'objectclass'
            ],
            'select' => null
        ]);
        $this->config($config, null, false);
    }

    /**
     * Authenticate user
     *
     * @param \Cake\Network\Request $request The request that contains login information.
     * @param \Cake\Network\Response $response Unused response object.
     * @return mixed False on login failure. An array of User data on success.
     */
    public function authenticate(Request $request, Response $response)
    {
        $fields = $this->_config['fields'];
        if (!$this->_checkFields($request, $fields)) {
            return false;
        }
        return $this->findAdUser($request->data[$fields['username']], $request->data[$fields['password']]);
    }

    /**
     * Connect to Active Directory on behalf of a user and return that user's data.
     *
     * @param string $username The username (samaccountname).
     * @param string $password The password.
     * @return mixed False on failure. An array of user data on success.
     */
    public function findAdUser($username, $password)
    {
    	$users = $this->config('users');

    	foreach($users as $user) {
    		if ($user['samaccountname'] === $username && 
    				$user['password'] === $password) {
    			return $user;
    		}
    	}
    	return false;
    }
}
