<?php

namespace Astronphp\FrontView;

class Template{

	private $nameTemplate 			= 'default';
	private $replaceAllHtml     	= array();
	
	private $useCache 				= false;
	private $compress 				= false;
	private $twig 			 		= null;
	private $fileTemplate 			= '404.tpl';
	private $pathTemplate 			= 'templates/';
	private $pathCache 				= 'tmp/apps/cache/';

	
	public function __construct(){
		return $this;
	}

	public function generateHtml(){
		if(!empty($this->fileTemplate()) && file_exists($this->getPathTemplate().$this->fileTemplate())){
			$timer=\Performace::getInstance('Timer');
			$timer->register('twig',microtime(true));
			$loader = new \Twig\Loader\FilesystemLoader($this->getPathTemplate());
			$this->twig = new \Twig\Environment($loader, [
				'cache' => ($this->useCache()==false?false:PATH_ROOT.$this->pathCache.$this->nameTemplate())
			]);
			
			if($this->compress()==true){
				$search = array(
					'/\>[^\S ]+/s',  // remove whitespaces after tags
					'/[^\S ]+\</s',  // remove whitespaces before tags
					'/(\s)+/s'       // remove multiple whitespace sequences
				);
				$replace = array('>','<','\\1');
				$ret = preg_replace($search, $replace, $this->twig->render($this->fileTemplate(), $this->replaceAllHtml));
			}else{
				$ret = $this->twig->render($this->fileTemplate(), $this->replaceAllHtml);
			}
			$timer->register('twig',microtime(true));
			return $ret;
		}else{
			throw new \Exception('Template '.$this->fileTemplate().' not found '.$this->getPathTemplate().$this->fileTemplate(), 1);
			
		}
	}
	
	public function render(){
		echo $this->generateHtml();
	}
	public function returnHtml(){
		return $this->generateHtml();
	}
	
	public function nameTemplate($v=null){
		if($v==null){
			return $this->nameTemplate;
		}else{
			$this->nameTemplate = $v;
			return $this;
		}
	}

	public function fileTemplate($v=null){
		if($v==null){
			return $this->fileTemplate;
		}else{
			$this->fileTemplate = $v;
			return $this;
		}
	}

 	public function content(array $parms){
		foreach ($parms as $key => $value) {
			$this->replaceAllHtml[$key] = $value;
		}
		return $this;
	}
	
	public function getPathTemplate(){
		return PATH_ROOT.$this->pathTemplate.$this->nameTemplate().'/src/views/';
	}

	public function useCache($v=null){
		if(is_bool($v)){
			$this->useCache=$v;
			return $this;
		}else{
			return $this->useCache;
		}
	}
	
	public function compress($v=null){
		if(is_bool($v)){
			$this->compress=$v;
			return $this;
		}else{
			return $this->compress;
		}
	}
}