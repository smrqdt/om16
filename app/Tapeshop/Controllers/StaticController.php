<?php
namespace Tapeshop\Controllers;

use ActiveRecord\ActiveRecordException;
use Tapeshop\Controller;

/**
 * Handle all the static pages
 */
class StaticController extends Controller {

	/**
	 * Show Static pages.
	 */
	public function renderStaticPage($pageName) {
		$this->render('static/'.$pageName.'.html');
	}

}