<?php
namespace Tapeshop\Controllers;

use SmartyException;
use Tapeshop\Controller;

/**
 * Handle all the static pages
 */
class StaticController extends Controller
{

	/**
	 * Show Static pages.
	 */
	public function renderStaticPage($pageName)
	{
		try {
			$templatePath = $this->app->view()->getTemplatesDirectory() . '/static/' . $pageName . '.tpl';
			$this->render($templatePath);
		} catch (SmartyException $e) {
			$this->redirect("home");
		}
	}

	public function renderIndex()
	{
		$this->renderStaticPage("index");
	}

}
