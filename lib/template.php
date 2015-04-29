<?php


class Template {
    var $vorlage;
    var $templateUrl = 'pages/';

    // Template setzten
    function Template($vorlage,$static=false,$standard=true) {  
        $this->setVorlage($vorlage,$static, $standard);     
    }

    /**
     * Entscheidet, ob der übergebene String ein URL ist und bindet die richtige Vorlage ein.
     * 
     * @param String $vorlage URL zur Vorlage-Datei oder die Vorlage als String
     * @access private
     */
    
    function setVorlage($vorlage,$static,$standard) {       
        if ($standard) {
            $basic = new Template('basic.html', false, false);          
            $content = $this->persistVorlage($vorlage);
            $basic->setContent('content', $content);
            $this->vorlage = $basic->vorlage;                   
        } else {
            
            if (is_array($vorlage)) {               
                foreach ($vorlage as $v) {
                    $this->vorlage .= $this->persistVorlage($v);
                }
            } else $this->vorlage = $this->persistVorlage($vorlage);            

        }
    }

    function persistVorlage($vorlage) {     
        if(is_file($this->templateUrl . $vorlage) && file_exists($this->templateUrl . $vorlage)) {          
            $fp = fopen($this->templateUrl . $vorlage, "r");
            $text = fread($fp, filesize($this->templateUrl . $vorlage));
            fclose($fp);
            return $text;
        } else return $vorlage;
    }


    
    /**
     * Mögliche Aufrufe:
     * - String/String => suchWort, substitution
     * - Array/ - => Array(suchWort => substitution)
     * - String/Array => SchleifenName/2D-Array
     *
     * @internal Überladung simulieren: diese Methode entscheidet anhand der Parameter, an welche private Methode delegiert wird
     * @param String_oder_Array param1
     * @param String_oder_Array param2
     * @access public
     */
    function setContent($param1, $param2="") {
        if(!is_array($param1) && $param2 && !is_array($param2))  $this->setOne($param1, $param2);
        elseif (is_array($param1) && !$param2)                   $this->setArray($param1);
        elseif (!is_array($param1) && is_array($param2))         $this->setLoop($param1, $param2);
        elseif (!is_array($param1) && empty($param2))            $this->setLoop($param1, '');
        else die("Parameter in der Klasse Vorlage wurden falsch übergeben.");
    }

    /**
     * AufrufBeispiel:   
     * $vorlage->setOne("TITEL", "Titel der WebSite");
     *
     * @param String suchWort "Der String, der ersetzt wird"
     * @param String substitution "Der String, der eingebunden wird"
     * @access private
     */
    function setOne($suchWort, $substitution) {
        $this->vorlage = str_replace("{".$suchWort."}",
        $substitution,
        $this->vorlage);
    }
    
    /**
     * AufrufBeispiel:
     * $vorlage->setArray(array("MELDUNG" => $meldung,
     * "NAME" => $_POST['name'],
     * "EMAIL" => $_POST['eMail'],
     * "TEXT" => $_POST['text'],
     * "KOPIE" => $_POST['kopie']));
     *
     * @param mixed $Array enthält Variable/Substitution-Paare
     * @access private
     */
    function setArray($Array) {     
        foreach ($Array as $suchWort => $substitution) {
             $this->setOne($suchWort, $substitution);   
        }
    }
    
    /**
     * Aufrufbeispiel:
     * $vorlage->setLoop($nameDerSchleife, array(array("var1" => "konst1",
     * "var2" => "konst2"),
     * array("var1" => "konst3",
     * "var2" => "konst4")));
     *
     * @param String $schleife Bezeichnung der Schleife
     * @param mixed $Array Array von assoziativen Arrays, die jeweils die Schlüssel/Werte enthalten (siehe Aufrufbeispiel)
     * @access private   
     */
    function setLoop($schleife, $Array) {
        // echo $this->vorlage;
        $str = explode("<!--START:".$schleife."!-->", str_replace("<!--END:".$schleife."!-->", "<!--START:".$schleife."!-->", $this->vorlage));     
        $teilStr = "";  
        if (empty($Array)){
            $this->vorlage = $str[0] . $teilStr . $str[2];
            return;
        }
        foreach ($Array as $element) {
            $teilVorlage = new Template($str[1], false , false);
            $teilVorlage->setArray($element);
            $teilStr .= $teilVorlage->vorlage;
        }
        $this->vorlage = $str[0] . $teilStr . $str[2];
    }

}
    
?>