<?php
App::uses('PaginatorHelper', 'View/Helper');

class BootstrapPaginatorHelper extends PaginatorHelper {

	public function pagination($options = array()) {
		$default = array(
			'div' => array(
				'class' => 'pagination'
			)
		);

		$model = (empty($options['model'])) ? $this->defaultModel() : $options['model'];

		$pageCount = $this->request->params['paging'][$model]['pageCount'];
		if ($pageCount < 2) {
			// Don't display pagination if there is only one page
			return '';
		}else{
			$default['units'] = array('prev', 'numbers', 'next');
		}
		
		$options = array_merge($default, $options);

		$units = $options['units'];
		unset($options['units']);
		$div = $options['div'];
		unset($options['div']);
		$ul = (isset($options['ul'])) ? array('class' => $options['ul']) : array();
		unset($options['ul']);

		$out = array();
		foreach ($units as $unit) {
			if ($unit === 'numbers') {
				$out[] = $this->{$unit}($options);
			} else {
				$out[] = $this->{$unit}(null, $options);
			}
		}
		$out = $this->Html->tag('ul', implode("\n", $out), $ul);
		if ($div !== false) {
			$out = $this->Html->div($div, $out);
		}
		return $out;
	}

	public function pager($options = array()) {
		$default = array(
			'ul' => 'pager',
			'prev' => 'Previous',
			'next' => 'Next',
			'disabled' => 'hide',
		);
		$options = array_merge($default, $options);

		$class = $options['ul'];
		unset($options['ul']);
		$prev = $options['prev'];
		unset($options['prev']);
		$next = $options['next'];
		unset($options['next']);

		$out = array();
		$out[] = $this->prev($prev, array_merge($options, array('class' => 'previous')));
		$out[] = $this->next($next, array_merge($options, array('class' => 'next')));

		return $this->Html->tag('ul', implode("\n", $out), compact('class'));
	}

	public function prev($title = null, $options = array(), $disabledTitle = null, $disabledOptions = array()) {
		$default = array(
			'title' => '',
			'tag' => 'li',
			'model' => $this->defaultModel(),
			'class' => 'previous',
			'disabled' => 'disabled',
		);
		$options = array_merge($default, $options);
		
		if (empty($title)) {
			$title = $options['title'];
		}
		unset($options['title']);

		$disabled = $options['disabled'];
		$params = (array)$this->params($options['model']);
		if ($disabled === 'hide' && !$params['prevPage']) {
			return null;
		}
		unset($options['disabled']);

		$return = parent::prev($title, $options, $this->link($title), array_merge($options, array(
			'escape' => false,
			'class' => 'previous '. $disabled,
		)));
		return str_replace('href="', 'class="fui-arrow-left" href="', $return);
	}

	public function next($title = null, $options = array(), $disabledTitle = null, $disabledOptions = array()) {
		$default = array(
			'title' => '',
			'tag' => 'li',
			'model' => $this->defaultModel(),
			'class' => 'next',
			'disabled' => 'disabled',
		);
		
		$options = array_merge($default, $options);
		
		if (empty($title)) {
			$title = $options['title'];
		}
		unset($options['title']);

		$disabled = $options['disabled'];
		$params = (array)$this->params($options['model']);
		if ($disabled === 'hide' && !$params['nextPage']) {
			return null;
		}
		unset($options['disabled']);

		$return = parent::next($title, $options, $this->link($title), array_merge($options, array(
			'escape' => false,
			'class' => 'next '. $disabled,
		)));
		return str_replace('href="', 'class="fui-arrow-right" href="', $return);
	}

	public function numbers($options = array()) {
		$defaults = array(
			'tag' => 'li',
			'before' => null,
			'after' => null,
			'model' => $this->defaultModel(),
			'class' => null,
			'modulus' => 4,
			'separator' => false,
			'first' => null,
			'last' => null,
			'ellipsis' => '<li><a href="#">…</a></li>',
			'currentClass' => 'active'
		);
		$options = array_merge($defaults, $options);
		$return = parent::numbers($options);
		return preg_replace('@<li class="active">(.*?)</li>@', '<li class="active disabled"><a href="#">\1</a></li>', $return);
	}

	public function first($title = null, $options = array()) {
		$default = array(
			'title' => '<<',
			'tag' => 'li',
			'after' => null,
			'model' => $this->defaultModel(),
			'separator' => null,
			'ellipsis' => null,
			'class' => null,
		);
		$options = array_merge($default, $options);
		if (empty($title)) {
			$title = $options['title'];
		}
		unset($options['title']);

		return (parent::first($title, $options)) ? (parent::first($title, $options)) : $this->Html->tag(
			$options['tag'],
			$this->link($title, array(), $options),
			array('class' => 'disabled')
		);
	}

	public function last($title = null, $options = array()) {
		$default = array(
			'title' => '>>',
			'tag' => 'li',
			'after' => null,
			'model' => $this->defaultModel(),
			'separator' => null,
			'ellipsis' => null,
			'class' => null,
		);
		$options = array_merge($default, $options);
		if (empty($title)) {
			$title = $options['title'];
		}
		unset($options['title']);

		$params = (array)$this->params($options['model']);

		return (parent::last($title, $options)) ? (parent::last($title, $options)) : $this->Html->tag(
			$options['tag'],
			$this->link($title, array(), $options),
			array('class' => 'disabled')
		);
	}
	public function limitbox(){
		if(isset($this->request->query['limit'])){
			$limit = $this->request->query['limit'];
		}else{
			$limit = ROWPERPAGE;
		}
		
		$html = '';
		$html = '<form method="GET" class="limitbox">';
		$html .= '<span></span>';
		$html .= '<select name="limit">';
		for($i = 5; $i <= 50; $i = $i+5){
			if($i == $limit){
				$html .= '<option value="'. $i .'" selected="selected">'. $i . '</option>';
			}else{
				$html .= '<option value="'. $i .'">'. $i . '</option>';
			}
		}
		$html .= '</select>';
		$html .= '</form>';
		return $html;
	}

}
