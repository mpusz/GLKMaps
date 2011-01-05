<?php

function get_file_extension($file_name)
{
        return substr(strrchr($file_name,'.'),1);
}

function handle_request()
{
        if(isset($_REQUEST['getJSON']))
        {
                $obj = new LK8kData();
                
                if (isset($_REQUEST['filter']))
                        $obj->filter = (int)$_REQUEST['filter'];
                
                if (isset($_GET['dropcache']))
                        $obj->cacheFilename = false;
                echo json_encode($obj->getData());
        }
}

/**
 * populate, store and provide data for Google Maps
 * @author Michal migajek Gajek
 */
class LK8kData {
	private $dataDir = "/data/";
	private $allowedExts = array("txt");
        private $varNames = array("NAME", "DIR", "MAPZONE");
        private $coordNames = array("LONMIN", "LONMAX", "LATMIN", "LATMAX");
        // relative path to cache filename, provide false to avoid caching
        public $cacheFilename = "_cache.";
        public $filter = 0;
        /**
         * @param integer filter, 0 for no filtering
         * @return array
         * Parse all the files in dataDir and return an array 
         * where each item is an array containing following keys:
         * file / latmin / latmax / lonmin / lonmax
         */
        private function parseFiles()
        {                        
                $files = array();
                $dir = opendir($this->dataDir);                
                // first of all, collect the filenames
                if (!$dir)
                        throw new Exception("Unable to read directory {$this->dataDir}");                
                while (($file = readdir($dir)) !== false){                                       
                        if(in_array(strtolower(get_file_extension($file)), $this->allowedExts))
                                $files[] = $file;        
                }                
                closedir($dir);                
                        
                // each file has to be opened
                $parsed = array();        
                foreach ($files as $file)
                {
                        $content = file_get_contents($this->dataDir.$file);
                        $datas = array(
                                "file" => $file,
                                "color" => $this->getHashColor($file),
                        );
                                               
                        foreach ($this->varNames as $var)
                        {
                                $matches = array();                        
                                preg_match("/^\s*{$var}\s*=\s*(-?[\w]+)/mi", $content, $matches);
                                if (sizeof($matches))
                                        $datas[strtolower($var)] = str_replace(',', '.', $matches[1]);       
                        }
                        
                        
                        foreach ($this->coordNames as $var)
                        {
                                $matches = array();                        
                                preg_match("/^\s*{$var}\s*=\s*(-?[\d\.,]+)/mi", $content, $matches);
                                if (sizeof($matches))
                                        $datas[strtolower($var)] = str_replace(',', '.', $matches[1]);       
                        }
                        
                        $res = array(1000, 500, 250, 90);
                        foreach ($res as $r)
                        {
                                $datas['res'][$r] = preg_match("/RES{$r}=YES/i", $content);
                                if($datas['res'][$r])
                                        $datas['bestres'] = $r;    
                        }
                        // filtering stuff, to be removed / rewritten later
                        if ($this->filter != 0)
                        {                                                                       
                                        if (($datas['bestres'] < $this->filter ) || (!$datas['res'][$this->filter]))
                                                continue;                                        
                        }
                        // end of filtering        
                        $parsed[] = $datas;                      
                }
                
                return $parsed;
        }         

        /**
        * function to compute color for a specific string
        * @param string source data
        * @return string HTML color
        */
        private function getHashColor($string)
        {
                $hash = md5($string);                
                return '#'.substr($hash, 0, 6);        
        }

	public function __construct()
        {
                $this->dataDir = dirname(__FILE__).$this->dataDir;
                if ($this->cacheFilename)
                        $this->cacheFilename = dirname(__FILE__)."/".$this->cacheFilename;                
	}

        public function getData()
        {
                if ($this->cacheFilename && file_exists($this->cacheFilename.$this->filter))
                {
                        $content = file_get_contents($this->cacheFilename.$this->filter);
                        if (!empty($content))
                                return unserialize($content);
                }
                
                $data = $this->parseFiles();
                
                if ($this->cacheFilename && !empty($data))
                {                       
                        file_put_contents($this->cacheFilename.$this->filter, serialize($data));
                }
                
                return $data;
        }
}




handle_request();
