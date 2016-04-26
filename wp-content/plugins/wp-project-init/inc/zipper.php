<?php
if ( !class_exists('Zipper') ){
  class Zipper extends ZipArchive {

    public function addDir( $path, $parent = '' ) {
      $this->addEmptyDir( $parent );
      $nodes = glob($path . '/*');
      foreach ($nodes as $node) {
        if (is_dir($node)) {
          $pi = pathinfo($node);
          $this->addDir( $node, $parent . DIRECTORY_SEPARATOR . $pi['basename'] );
        } else if (is_file($node))  {
          $pi = pathinfo($node);
          $this->addFile($node, $parent . DIRECTORY_SEPARATOR . $pi['basename']);
        }
      }
    }

  }
}