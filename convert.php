<?php
class Resources {
    public $_genetic_map = array(  );
    public $_genetic_map_name = "";
    public $_cytoBand_hg19 = array(  );
    public $_knownGene_hg19 = array(  );
    public $_kgXref_hg19 = array(  );
    public $resources_dir="";
    function __construct(){
        $this->_genetic_map = array(  );
        $this->_genetic_map_name = "";
        $this->_cytoBand_hg19 = array(  );
        $this->_knownGene_hg19 = array(  );
        $this->_kgXref_hg19 = array(  );
        $this->resources_dir="resources";
    }
    public function  _load_genetic_map_HapMapII_GRCh37($filename){
        $genetic_map = array(  );
        $archive = new PharData($filename);
        foreach($archive as $file) {
            if (strpos("genetic_map",$file["name"])===true){
                $df = array(  );
                if (($handle = fopen($file, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle,"\t")) !== FALSE) {
                        $df["Position(bp)"]=$data["pos"];
                        $df["Rate(cM/Mb)"]=$data["rate"];
                        $df["Map(cM)"]=$data["map"];
                    }
                    fclose($handle);
                }
                $start_pos = strpos($file["name"],"chr") + 3;
                $end_pos = strpos($file["name"],".");
                $genetic_map[substr($file["name"],$start_pos,$end_pos)] = $df;
            }
        }
        $genetic_map["X"] = array_merge(
            $genetic_map["X_par1"], $genetic_map["X"], $genetic_map["X_par2"]
        );
        $genetic_map["X_par1"]=array( );
        $genetic_map["X_par2"]=array( );
        return $genetic_map;
    } 
    
    public function _download_file($url, $filename, $compress=False, $timeout=30){
        if(strpos($url, "ftp://") !== false) {
            $url=str_replace($url,"ftp://", "http://");
        } 
        if ($compress && substr($filename,strlen($filename)-3,strlen($filename)) != ".gz"){
            $filename = $filename+".gz";
        }
        $destination = join($this->resources_dir, $filename);

        if (!mkdir($destination)){
            return "";
        }
        if (file_exists($destination)){            
            $file_url = $destination;
            header('Content-Type: application/octet-stream');
            header('Content-Description: File Transfer');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_url));
            readfile($file_url);

            // if $compress
            //     $this->_write_data_to_gzip(f, data)
            // else
            //     f.write(data)           
        }   
        return $destination;
    }
    
    
    public function get_all_resources(){
        $resources = array( );
        $resources[
            "genetic_map_HapMapII_GRCh37"
        ] = $this->get_genetic_map_HapMapII_GRCh37();
        $resources["cytoBand_hg19"] = $this->get_cytoBand_hg19();
        $resources["knownGene_hg19"] = $this->get_knownGene_hg19();
        $resources["kgXref_hg19"] = $this->get_kgXref_hg19();
        return $resources;
    }
    public function download_example_datasets(){
        return [
            $this->_download_file(
                "https://opensnp.org/data/662.23andme.340",
                "662.23andme.340.txt.gz",
                $compress=True
            ),
            $this->_download_file(
                "https://opensnp.org/data/662.ftdna-illumina.341",
                "662.ftdna-illumina.341.csv.gz",
                $compress=True
            ),
            $this->_download_file(
                "https://opensnp.org/data/663.23andme.305",
                "663.23andme.305.txt.gz",
                $compress=True
            ),
            $this->_download_file(
                "https://opensnp.org/data/4583.ftdna-illumina.3482",
                "4583.ftdna-illumina.3482.csv.gz"
            ),
            $this->_download_file(
                "https://opensnp.org/data/4584.ftdna-illumina.3483",
                "4584.ftdna-illumina.3483.csv.gz"
            ),
        ];
    }
}
 
?>
       