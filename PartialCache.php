<?php

class PartialCache {
  
  /**
   * Cache system for caching the partial webpages.
   * Uses $site as folder and $key as filename
   *
   **/
  public function __construct($site, $key, $disable=false)
	{
	  
	  /* check if file exists
	     - if not start caching the output
	     - else check the timestamp if not to old server cache instead
	  */
	  
	  if (is_array($key)) {
	    $key = implode($key,"_");
	  }
	  
	  if ($disabled) 
	  {
	    $this->filename = dirname(__FILE__) ."/../cache/". $site ."/". $key .".html";
	    $this->site = $site;
	    $this->key = $key;
    }
	}
	
	public function is_cached() {
	  $config = Config::getInstance();
	  return file_exists($this->filename) && (time() - $config->application['cache_time']) > filemtime($this->filename);
	}
	
	public function start() {
	  if (!$this->is_cached()) {
	    ob_start();
	    return true;
    } else {
      return false;
    }
	}
	
	public function render() {
	  
	  if ($this->is_cached()) {
	    
	    $cache = file_get_contents($this->filename);
	    echo $cache;
	    echo '<div class="info-box">'
	       . sprintf($this->text, 
	                 strftime("%Aen den %e %B kl %H:%M", filemtime($this->filename)))
	       . sprintf($this->link, $this->site, $this->key) 
	       . '</div>';
	    
    } else {
	    $cache = ob_get_contents();
      ob_end_clean();
      
      echo $cache;
      
      /* save to file cache */
      $file = fopen($this->filename, 'w');
      
      if (is_writable($this->filename)) {
        fwrite($file, $cache);
        fclose($file);
      } else {
        echo '<div class="info-box">Kunde <strong>inte skapa cache</strong>, troligen felaktiga rättigheter på servern. Vänligen kontakta systemadministratören.</div>';
      }
      
    }
	}
	
	public function clear() {
	  unlink($this->filename);
	}
  
  protected $filename;
  protected $site;
  protected $key;
  protected $text = 'Visar sparade resultat, sidan genererades %s. ';
  protected $link = '<a href="/cache/clear/%s/%s/">Rensa och visa aktuell data</a>';
}

?>