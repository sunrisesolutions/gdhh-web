<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class SiteService extends BaseService {
	
	private $hostParams;
	
	public function getLocale() {
		if( ! empty($hostParams = $this->getHostParams())) {
			if(array_key_exists('locale', $hostParams)) {
				return $hostParams['locale'];
			}
		}
		
		return $this->container->getParameter('locale');
	}
	
	public function getHostName() {
		return str_replace('www.', '', $this->container->get('request_stack')->getCurrentRequest()->getHost());
	}
	
	public function getHostParams() {
		if($this->hostParams === null) {
			$host = $this->container->get('request_stack')->getCurrentRequest()->getHost();
			if($this->container->hasParameter($host)) {
				$this->hostParams = $this->container->getParameter($host);
			} else {
				$this->hostParams = $this->container->getParameter('default_site');
			}
		}
		
		return $this->hostParams;
	}
	
	public function getHostCountry() {
		if( ! empty($hostParams = $this->getHostParams())) {
			if(array_key_exists('country', $hostParams)) {
				return $hostParams['country'];
			}
		}
		
		return null;
	}
	
	public function getFileServerURL($type = 'file') {
		$hostParams = $this->getHostParams();
		$fileServer = $hostParams['file_server'];
		$subdomain  = substr($fileServer, 0, strpos($fileServer, '.'));
		$domain     = substr($fileServer, strpos($fileServer, '.'));
		
		return $type . '-' . $subdomain . $domain;
	}
	
	public function getFileServerURLWithScheme($type = 'file') {
		$scheme = $this->container->get('request_stack')->getCurrentRequest()->getScheme() . '://';
		
		return $scheme . $this->getFileServerURL($type);
	}
}