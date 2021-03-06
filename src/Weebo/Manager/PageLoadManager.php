<?php

namespace Yakovmeister\Weebo\Manager;

use Yakovmeister\Weebo\Component\Net;
use Yakovmeister\Weebo\Application;

class PageLoadManager
{
	protected $net;

	protected $page;

	protected static $instance;

	public function __construct(Net $net)
	{
		$this->net = $net;
	}

	/**
	 * @param  String $url
	 * @return Yakovmeister\Weebo\Manager\PageLoadManager::page
	 */
	public function load($url)
	{
		$page = $this->net->load($url);

		switch ($page->getResponseStatus()) 
		{
			case Net::HTTP_NOT_FOUND:
				$this->page = [
					"status" => Net::HTTP_NOT_FOUND,
					"message" => "Page not found."
				];

				break;
			case Net::HTTP_OK:
			case Net::HTTP_FOUND:
				$this->page = [
					"status" => Net::HTTP_OK,
					"message" => normalizeHTML($page->getResponse())
				];
	
				break;
		}

		return $this->page;
	}

	public function getStatus()
	{
		return $this->page["status"];
	}

	public function getContent()
	{
		return $this->page["message"];
	}

	public static function getInstance()
	{
		return is_null(static::$instance)
		? Application::getInstance()->make(PageLoadManager::class)
		: static::$instance;
	}
}