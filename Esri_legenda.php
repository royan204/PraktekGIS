<html>
<head>
<style>
/* width */
::-webkit-scrollbar {
  width: 10px;
};

/* Track */
::-webkit-scrollbar-track {
  background: #f1f1f1;
};

/* Handle */
::-webkit-scrollbar-thumb {
  background: #888;
};

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: #555;
};
</style>
</head>
<body>
<span>
 <?php error_reporting(0);
$url = str_replace("^","/",$_GET['url']);
$arrContextOptions=array(
	"ssl"=>array(
		"verify_peer"=>false,
		"verify_peer_name"=>false,
	),
);
 $urlJson = $url.'/legend?f=pjson';
 $konten = file_get_contents($urlJson, false, stream_context_create($arrContextOptions));
 $data = json_decode($konten, true);
 if(count($data['layers'])> 0){
	echo "<table border='0' style='color:#FFFFFF;font-family:Tahoma, Geneva, sans-serif; font-size:12px;'>";
	if($_GET['n']!= "" or $_GET['n']== "0"){
		   	$a = $_GET['n']-1;
			echo "<tr><td colspan='2'><b>".$data['layers'][$a]['layerName']."</b></td></tr>"; 
				  for($i=0; $i < count($data['layers'][$a]['legend']); $i++) {
					   echo '<tr><td width="20"><img src="data:image/png;base64,'.$data["layers"][$a]["legend"][$i]["imageData"].'" width="20" height="20" /></td>';   
					   echo "<td>".$data['layers'][$a]['legend'][$i]['label'].'</td></tr>'; 				
	  }	  
	}else{	
		for($a=0; $a < count($data['layers']); $a++) {
			echo "<tr><td colspan='2'><b>".$data['layers'][$a]['layerName']."</b></td></tr>"; 
				  for($i=0; $i < count($data['layers'][$a]['legend']); $i++) {
					   echo '<tr><td width="20"><img src="data:image/png;base64,'.$data["layers"][$a]["legend"][$i]["imageData"].'" width="20" height="20" /></td>';   
					   echo "<td>".$data['layers'][$a]['legend'][$i]['label'].'</td></tr>'; 				
				}
			  }
	}
	echo "</tabel>";
	}?>
</span>
</body>
</html>