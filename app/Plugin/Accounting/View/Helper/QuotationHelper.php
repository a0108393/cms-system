<?php

class QuotationHelper extends AppHelper {
	public $helpers = array('Html', 'Form');
	
	function render($number) {
		return substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $number);
	}
}
	