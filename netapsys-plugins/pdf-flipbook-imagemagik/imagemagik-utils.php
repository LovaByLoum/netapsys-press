<?php
//class utilitaire pdf / imagemagik

class PDFFLIP{
	public $filename;
	public $url;
	public $path;
	public $image_url;
	public $image_path;
	public $width;
	public $height;
	public $nombre_de_page;
	public $vignette_width;
	public $vignette_height;
	public $imagemagikpath;
	
	function __construct($pdfURL){
		$this->url = $pdfURL;
		$this->imagemagikpath = "/usr/local/bin/";
		$this->path = $this->getPdfPath();
		
		$dimensions = $this->getDimension();
		$this->width = $dimensions['w'];
		$this->height = $dimensions['h'];
		
		$this->nombre_de_page = $this->getNumberPage();
		
		$image_info = $this->getPdfImageInfo();
		$this->image_url = $image_info['url'];
		$this->image_path = $image_info['path'];
		
		$urlinfo = pathinfo($this->url);
		$this->filename = $urlinfo['filename'];
		
		$this->vignette_height = 88;
		$ratio = $this->width/$this->height;
		$this->vignette_width = ceil($this->vignette_height*$ratio);
	}
	
	/**
	 * obtenir le path du pdf
	 *
	 */
	function getPdfPath(){
		$uploadUrl = wp_upload_dir();
		$path = $uploadUrl['path'];
		$urlinfo = pathinfo($this->url);
		$path = $path . '/' . $urlinfo['filename'] . '.' . $urlinfo['extension'];
		return $path;
	}
	
	/**
	 * retourne la taille des pages
	 *
	 */
	function getDimension(){
		$width = exec($this->imagemagikpath . "identify -format %w ".$this->path."[0]");
		$height = exec($this->imagemagikpath . "identify -format %h ".$this->path."[0]");
		return array(
			'w'=> $width,
			'h'=> $height
		);
	}
	
	/**
	 * obtenir le nombre de page
	 *
	 */
	function getNumberPage(){
		$numberPage = exec($this->imagemagikpath . "identify -format %n $this->path");
		return $numberPage;
	}
	
	/**
	 * retourne l'url et le path vers les images du pdf
	 */
	function getPdfImageInfo(){
		$urlinfo = pathinfo($this->url);
		$uploadUrl = wp_upload_dir();
		$dossier = 'image_pdf';
		$PDFImageUrl = $uploadUrl['url']  . '/'. $dossier .'/'. $urlinfo['filename'];
		$PDFImageDir = $uploadUrl['path'] . '/'. $dossier .'/'. $urlinfo['filename'] ;
		return array(
			'url' =>$PDFImageUrl,
			'path' => $PDFImageDir
		);
	}
	
	/**
	 * convertit un pdf en image
	 *
	 */
	function convertPdf(){
		if(!file_exists($this->image_path)){
			exec("mkdir ". $this->image_path);	
			exec("chmod 0777 " .$this->image_path);	
			exec($this->imagemagikpath . "convert -density 100 -quality 100 ".$this->path." " . $this->image_path . "/%0d.jpg");	
			$this->doVignette();
		}
	}
	
	/**
	 * generer les vignettes
	 *
	 */
	function doVignette(){
		exec($this->imagemagikpath . "convert ". $this->path ." -resize ". $this->vignette_width ."x". $this->vignette_height ." " . $this->image_path . "/%0d-vignette.jpg");	
	}
	
	function removeImages(){
		if(file_exists($this->image_path)){
			exec("rm -R ". $this->image_path);
		}
	}
}
?>