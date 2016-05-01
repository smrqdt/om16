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
		$templatePath = $this->app->view()->getTemplatesDirectory() . '/static/' . $pageName . '.tpl';
		try {
			$this->render($templatePath);
		} catch (SmartyException $e) {
			error_log("Could not reder template " . $templatePath);
			error_log($e);
			$this->redirect("home");
		}
	}

	public function renderIndex()
	{
		$this->renderStaticPage("index");
	}

}
