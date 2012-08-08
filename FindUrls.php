<?php
class FindUrls {

private $_akcja = array();

function findUrl($url){                     

	$homepage = file_get_contents($url);
							
	$wyniki = array();
	$offset = 1;	
	$p = true;
	while ($p!==false){
	$p = strpos($homepage, "href=", $offset);
	if ($p!==false){
	$wynik = substr($homepage,$p+6, strpos($homepage,"\"",$p+7)-$p-6);
		if (substr($wynik,0,7)!=='http://'&& (substr($wynik,strlen($wynik)-3,3)!=='css') ){	
					$parse = parse_url($url);
					$wynik = 'http://'.$parse['host'].$wynik;
		}
		if (substr($wynik,strlen($wynik)-3,3)!=='css'){
			if (substr($wynik,strlen($wynik)-1,1)!=='#'){
			$wyniki[] = $wynik;
			}
		}			
	}	
	$offset = $p+1;
	}
	// ---------hrefy w tablicy $wyniki ↑  
	echo '<pre>';
	print_r($wyniki);echo '<hr/>';
	
	set_time_limit(0);
	$count = count($wyniki); 

$i = 0;	
while($i<$count){
	set_time_limit(0);
	$url = $wyniki[$i];	
	@$homepage = file_get_contents($url); 
		if(false!==strpos($homepage,"<form")){
									// dla każdego kolejnego hrefa, jeśli jest form
									// ucinamy form i action
		$forma = substr($homepage,strpos($homepage,"<form"),strpos($homepage,"</form>")-strpos($homepage,"<form"));
		$akcja = substr($forma, 6 , strpos($forma,">")-6 );	
			
		$explode_action = explode("\"",$akcja);
		$key = array_search(' action=', $explode_action);
		if($key == false) { 
			$key = array_search('action=', $explode_action);
		}	
		$key++;
		$action = $explode_action[$key];
		$base = parse_url($url);
		$host = $base['host'];
			if (substr($action,0,4)!=='http'){ $action = 'http://'.$host.$action;
			}
		echo 'akcja = '.$action;            // string $action aktualna akcja
		$names = array();
		$name=explode("\"",$forma);
		$counter = count($name);
			for ($k=0;$k<$counter;$k++){
				if ($name[$k]=='name=' || $name[$k]==' name='){
				$j = $k+1;
				$names[]=$name[$j]; unset($j);
				}			
			}
	
	echo '<hr/>'.'z adresu  '.$i.'<br>';
	echo 'na adres : '.$action.'<br>';
	echo 'zostały weysłane parametry o nazwach :'; 
	print_r($names); 
	echo '<br>'.'<hr/>';
		
	$ile = count($names);
	$postFields = array();
	$m = 0;
	while($m<$ile){ 
	$nam = $names[$m];
	$postFields[$nam] = 'http://strony-intrnetowe.host.org';
	$m++;
	}
	$ch = curl_init($action);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
	curl_exec($ch);
	//if (curl_errno($ch)) {echo 'Błąd #' . curl_errno($ch) . ': ' . curl_error($ch);}	
	curl_close($ch);	
		
		
		}		
	$i++;
	}
}


// the end


/* 	$client = new Zend_Http_Client($action);  
	$licznik = count($names);

	for($i=0;$i<$licznik;$i++){
	$client->setParameterPost($names[$i],'http://www.strony-internetowe.host.org/'); 
	}
	$client->request('POST');
	
*/

}
	

// koniec klasy	
	

	

