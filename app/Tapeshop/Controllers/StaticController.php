<?php
namespace Tapeshop\Controllers;

use SmartyException;
use Tapeshop\Controller;

/**
 * Handle all the static pages
 */
class StaticController extends Controller {

	/**
	 * Show Static pages.
	 */
	public function renderStaticPage($pageName) {
		try{
			$this->render('static/'.$pageName.'.html');
		}catch(SmartyException $e){
			$this->redirect("home");
		}
	}

}