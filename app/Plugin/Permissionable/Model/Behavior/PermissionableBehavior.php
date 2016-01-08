<?php

/**
 * PermissionableBehavior
 *
 * An implementation of *NIX-like bitwise permissions for row-level operations.
 *
 * @package     permissionable
 * @subpackage  permissionable.models.behaviors
 * @author      Joshua McNeese <jmcneese@gmail.com>
 */
App::import('Lib', 'Permissionable.Permissionable');
class PermissionableBehavior extends ModelBehavior {

    /**
     * Permission bits, don't touch!
     */
    const   OWNER_READ      = OWNER_READ_BIT,  GROUP_READ   = GROUP_READ_BIT,  OTHER_READ      = OTHER_READ_BIT,
            OWNER_WRITE     = OWNER_WRITE_BIT,  GROUP_WRITE  = GROUP_WRITE_BIT,  OTHER_WRITE     = OTHER_WRITE_BIT,
            OWNER_DELETE    = OWNER_DELETE_BIT,   GROUP_DELETE = GROUP_DELETE_BIT,   OTHER_DELETE    = OTHER_DELETE_BIT;

    /**
     * configured actions
     *
     * @var array
     */
    private $_actions = array(
        'read',
        'write',
        'delete'
    );
    /**
     * settings defaults
     *
     * @var array
     */
    private $_defaults = array(
        'defaultBits'   => 416 // owner_read + owner_write + group_read
    );
	
	
    /**
     * disable Permissionable
     *
     * @var boolean
     */
    private $_disabled = false;

    /**
     * bind the permission model to the model in question
     *
     * @param  object	$Model
     * @return boolean
     */
    private function _bind(Model $Model, $conditions = array(), $type = 'INNER') {

        $this->_unbind($Model);

        $alias = $this->getPermissionAlias($Model);
	
        return $Model->bindModel(array(
            'hasOne' => array(
                $alias => array(
                    'className'		=> 'Permissionable.PermissionBit',
                    'foreignKey'	=> 'foreign_id',
                    'dependent'		=> true,
                    'type'			=> $type,
                    'conditions'	=> array_merge($conditions, array(
                        "{$alias}.module_id" =>  $this->settings[$Model->alias]['defaultModuleIds'],
						"{$alias}.model" => $Model->name
                    ))
                )
            )
        ), false);

    }

    /**
     * Convenience method for getting the permission bit integer for an action
     *
     * @param   mixed    $action
     * @return  integer
     */
    private function _getPermissionBit($action = null) {
	
        $action = strtoupper($action);

        return (empty($action) || !defined("self::$action"))
            ? 0
            : constant("self::$action");

    }

    /**
     * helper to build the query for permission checks
     *
     * @param  object  $Model
     * @param  string  $action
     * @return array
     */
    private function _getPermissionQuery(Model $Model, $action = 'read') {

        $alias	= $this->getPermissionAlias($Model);
        $action	= strtoupper($action);
		$gids	= Permissionable::getGroupIds();
		
        return array(
            // first check if "other" has the requested action
            "$alias.perms&{$this->_getPermissionBit('OTHER_' . $action)} <> 0",
            // otherwise, if the user has a group tht the row has, && the
            // "group" action is allowed
            array(
                "$alias.perms&{$this->_getPermissionBit('GROUP_' . $action)} <> 0",
                "$alias.group_id" => (count($gids) == 1) ? $gids[0] : $gids
            ),
            // otherwise, if the user is the row owner, && the "owner" action
            // is allowed
            array(
                "$alias.perms&{$this->_getPermissionBit('OWNER_' . $action)} <> 0",
                "$alias.user_id" => Permissionable::getUserId()
            )
        );

    }

    /**
     * helper to determine if the user
     * is the root user or member of the root group
     *
     * This method is deprecated, and will be removed in a future release
     *
     * @return boolean
     */
    private function _isRoot() {

        trigger_error(__('Model->_isRoot() is deprecated. Please use the static method Permissionable::isRoot() instead.', true), E_USER_WARNING);
        return Permissionable::isRoot();

    }

    /**
     * unbind the permission model from the model in question
     *
     * @param  object	$Model
     * @return boolean
     */
    private function _unbind(Model $Model) {

        return $Model->unbindModel(array(
            'hasOne' => array(
                $this->getPermissionAlias($Model)
            )
        ), false);

    }

    /**
     * settings
     *
     * @var     array
     */
    public $settings = array();

    /**
     * afterSave model callback
     *
     * cleanup any related permission rows
     *
     * @param  object  $Model
     * @param  boolean $created
     * @return boolean
     */
    public function afterSave(Model $Model, $created, $options = array()) {

    	if ($this->_disabled) {

    		return true;

    	}
		
        if ($created) {
			$user_id	= Permissionable::getUserId();
			$group_ids	= Permissionable::getGroupIds();
			$alias = $this->getPermissionAlias($Model);
			foreach($group_ids as $group_id){
				$data[] = array(
					'module_id'     => $this->settings[$Model->alias]['defaultModuleIds'],
					'model'     => $Model->alias,
					'foreign_id'=> $Model->id,
					'user_id'       => $user_id,
					'group_id'		=> $group_id,
					'perms'		=> $this->settings[$Model->alias]['defaultBits']
				);
			}
			 			 
			$this->_bind($Model);
			$Model->{$alias}->create();
			return $Model->{$alias}->saveAll($data);
        }

    }

    /**
     * beforeDelete model callback
     *
     * direct the callback to determine if user has delete permission on the row
     *
     * @param  object $Model
     * @return boolean
     */
    public function beforeDelete(Model $Model, $cascade = true) {

        if ($this->_disabled) {

        	return true;

        }

        return $this->hasPermission($Model, 'delete');

    }

    /**
     * beforeFind model callback
     *
     * if we are checking permissions, then the appropriate modifications are
     * made to the original query to filter out denied rows
     *
     * @param  object  $Model
     * @param  array   $queryData
     * @return mixed
     */
    public function beforeFind(Model $Model, $queryData) {
		$checked = false;
		if(isset(Router::getRequest()->params))
			$url = Router::getRequest()->params;
		if(isset($url['controller']) && isset($url['action']) && strtolower($url['controller']) == strtolower(Configure::read('Core.LoginAction.controller')) && strtolower($url['action']) == strtolower(Configure::read('Core.LoginAction.action'))){
			$checked = true;
		}
		
        if ($checked == true || $this->_disabled || (isset($queryData['permissionable']) && $queryData['permissionable'] == false ) || (isset($queryData['conditions']['permissionable']) && $queryData['conditions']['permissionable'] == false)) {
			unset($queryData['permissionable']);
            unset($queryData['conditions']['permissionable']);

            $this->_unbind($Model);
            return $queryData;
        }

        $alias = $this->getPermissionAlias($Model);
	
        if(!in_array($Model->findQueryType, array('avg', 'count', 'max', 'min', 'sum'))) {
			
            if (empty($queryData['fields'])) {

                $queryData['fields'] = array("{$Model->alias}.*");

            }

            $queryData['fields'] = Set::merge(
                    $queryData['fields'],
                    array(
                        "{$alias}.*, BIT_OR({$alias}.perms) as perms_merge"
                    )
            );
        }
		$que = array(
			"{$alias}.model" => "{$Model->alias}",
			"{$alias}.module_id" => "{$this->settings[$Model->alias]['defaultModuleIds']}",
			"{$alias}.foreign_id = {$Model->alias}.{$Model->primaryKey}",
			'or' => $this->_getPermissionQuery($Model)
		);
		$type = 'INNER';
		
		$read = Access::__getPermissionCurrentModule($Model->plugin);
		
		if(!empty($read) && $read['perms']['_read'] == GLOBAL_P){
			$que = array(
				"{$alias}.model" => "{$Model->alias}",
				"{$alias}.module_id" => "{$this->settings[$Model->alias]['defaultModuleIds']}",
				"{$alias}.foreign_id = {$Model->alias}.{$Model->primaryKey}"
			);
			$type = 'LEFT';
		}
        $this->_bind($Model, $que, $type);
		$queryData['group'] = "{$alias}.foreign_id";
        return $queryData;

    }

    /**
     * beforeSave model callback
     *
     * @param  object $Model
     * @return boolean
     */
    public  function beforeSave(Model $Model, $options = array()) {

        if ($this->_disabled) {

        	return true;

        }

        $user_id	= Permissionable::getUserId();
        // $group_id	= Permissionable::getGroupId();
        $group_ids	= Permissionable::getGroupIds();

        // if somehow we don't know who the logged-in user is, don't save!
        if (empty($user_id) || empty($group_ids)) {

            return false;

        }

        return (!empty($Model->id))
            ? $this->hasPermission($Model, 'write')
            : true;

    }

    /**
     * get the permissions for the record
     *
     * @param  object  $Model
     * @param  mixed   $id
     * @return mixed
     */
    public function getPermission(Model $Model, $id = null) {

        $id = (empty($id))
            ? $Model->id
            : $id;

        if (empty($id)) {

            return false;

        }

        $alias = $this->getPermissionAlias($Model);

        $this->_bind($Model);
        return $Model->{$alias}->find('first', array(
            'conditions' => array(
				"{$alias}.model"		=> $Model->alias,
                "{$alias}.module_id"		=> $this->settings[$Model->alias]['defaultModuleIds'],
                "{$alias}.foreign_id"	=> $id
            )
        ));

    }

    /**
     * get alias for the Permissionable model
     *
     * @param  object  $Model
     * @return mixed
     */
    public function getPermissionAlias(Model $Model) {

        return "{$Model->alias}PermissionBit";

    }

    /**
     * Determine whether or not a user has a certain permission on a row
     *
     * @param  object  $Model
     * @param  string  $action
     * @param  mixed   $id
     * @return boolean
     */
    public function hasPermission(Model $Model, $action = 'read', $id = null) {

        if ($this->_disabled) {

            return true;

        }

        $user_id	= Permissionable::getUserId();
        $group_ids	= Permissionable::getGroupIds();
        $id         = (empty($id)) ? $Model->id : $id;

        $this->_unbind($Model);

        // if somehow we don't know who the logged-in user is, don't save!
        if (!in_array($action, $this->_actions) || empty($id) || empty($user_id) || empty($group_ids)) {

            return false;

        } elseif(Permissionable::isRoot()) {

            return true;

        }

        $this->_bind($Model);

        // do a quick count on the row to see if that permission exists
		
        $alias	= $this->getPermissionAlias($Model);
        $perm	= $Model->{$alias}->find('count', array(
            'conditions' => array(
				"{$alias}.model"		=> $Model->alias,
                "{$alias}.module_id"		=> $this->settings[$Model->alias]['defaultModuleIds'],
                "{$alias}.foreign_id"	=> $id,
                'or'					=> $this->_getPermissionQuery($Model, $action)
            )
        ));

        return !empty($perm);

    }

    /**
     * disable Permissionable for the model
     *
     * @param  object   $Model
     * @param  boolean  $disable
     * @return null
     */
    public function disablePermissionable(Model $Model, $disable = true) {

        $this->_disabled = $disable;

    }

    /**
     * getter to determine if Permissionable is enabled
     *
     * @return boolean
     */
    public function isPermissionableDisabled() {

        return $this->_disabled;

    }

    /**
     * Behavior configuration
     *
     * @param   object  $Model
     * @param   array   $config
     * @return  void
     */
    public function setup(Model $Model, $config = array()) {		
        $config = (is_array($config) && !empty($config))
            ? Set::merge($this->_defaults, $config)
            : $this->_defaults;
		$config['defaultModuleIds'] = 0;
		$model = $Model->plugin;
		if($model == ''){
			$model = 'Core';
		}
		$act_ov = ClassRegistry::init('Permissionable.Module')->findByName($model);
		if($act_ov){
			$config['defaultModuleIds'] = $act_ov['Module']['id'];
			$config['defaultBits'] = $act_ov['Module']['total_bit'];
		}
		
        $this->settings[$Model->alias] = $config;
    }

}

?>