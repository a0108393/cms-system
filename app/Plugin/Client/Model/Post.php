<?php
/* SVN FILE: $Id$ */
/**
 * Short description for permission_cache.php
 *
 * Long description for permission_cache.php
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2008, Andy Dawson
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright (c) 2008, Andy Dawson
 * @link          www.ad7six.com
 * @package       acl
 * @subpackage    acl.models
 * @since         v 1.0
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * PermissionCache class
 *
 * Note this class extends Model, and not AppModel, so as not to inherit any unnecessary logic
 *
 * @uses          Model
 * @package       acl
 * @subpackage    acl.models
 */

class Post extends AppModel {
	var $name = 'Post';
	var $validate = array(
        'title' => array(
            'rule' => 'notEmpty'
        ),
        'body' => array(
            'rule' => 'notEmpty'
        )
    );
	
	var $actsAs  = array('Permissionable.Permissionable');
		
	public  function beforeSave($options = array()){
		$this->data[$this->alias]['user_id'] = AuthComponent::user('id');
		return true;
    }
}