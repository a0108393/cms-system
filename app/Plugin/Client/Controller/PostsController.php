<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
App::uses('AppController', 'Controller');
class PostsController extends AppController {
	var $name = 'Posts';
	public $paginate = array(
		'limit' => ROWPERPAGE
    );
	function index(){
		$data = $this->paginate('Post');
		// prd($data);
        $this->set('posts', $data);
    }
	function view($id = null) {
        $this->Post->id = $id;
		$post = $this->Post->read();
		if(!$this->Permissionable->checkRowAccess($this,$post)) die;
        $this->set('post', $this->Post->read());
    }
	
	function add(){
        if ($this->request->is('post')) {
			// $this->data['Post']['user_id'] = 1;
            if ($this->Post->save($this->request->data)) {
                $this->Session->setFlash('Your post has been saved.');
                $this->redirect(array('action' => 'index'));
            }
        }
    }
	
	function delete($id) {
		$this->Post->id = $id;
		$post = $this->Post->read();
		 
		if(!$this->Permissionable->checkRowAccess($this,$post)) die;
		if ($this->Post->delete($id)) {
			$this->Session->setFlash('The post with id: ' . $id . ' has been deleted.');
			$this->redirect(array('action' => 'index'));
		}
	}
	
	function edit($id = null) {
		$this->Post->id = $id;
		if (empty($this->data)) {
			$this->data = $this->Post->read();
		} else {
			if ($this->Post->save($this->data)) {
				$this->Session->setFlash('Your post has been updated.');
				$this->redirect(array('action' => 'index'));
			}
		}
	}
	
	public function test1() {
		die('hihi');
		
	}
	
	function test_map($id = null) {
        $this->Post->id = $id;
		$post = $this->Post->read();
		if(!$this->Permissionable->checkRowAccess($this,$post)) die('dnny');
        $this->set('post', $this->Post->read());
		print_r($post);die;
		die;
    }
	
}
