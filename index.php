<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Groovin Bootstrap Template - Index</title>
  <meta content="" name="description">
  <meta content="" name="keywords">


 <!-- Google Fonts -->
 <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

 <!-- Vendor CSS Files -->
 <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
 <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
 <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
 <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
 <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
 <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

 <!-- Template Main CSS File -->
 <link href="assets/css/style.css" rel="stylesheet">
 <link href="Lib/ol/ol.css" rel="stylesheet">
 <style>
    .map {
      width: 100%;
      height:500px;
    }
    .boxTool{
     z-index: 0;
     position:absolute;
    }
    .map .ol-custom-overviewmap,
      .map .ol-custom-overviewmap.ol-uncollapsible {
        bottom: auto;
        left: auto;
        right: 0;
        top: 0;
      }

      .map .ol-custom-overviewmap:not(.ol-collapsed)  {
        border: 1px solid black;
      }

      .map .ol-custom-overviewmap .ol-overviewmap-map {
        border: none;
        width: 300px;
      }

      .map .ol-custom-overviewmap .ol-overviewmap-box {
        border: 2px solid red;
      }

      .map .ol-custom-overviewmap:not(.ol-collapsed) button{
        bottom: auto;
        left: auto;
        right: 1px;
        top: 1px;
      }

      .map .ol-rotate {
        top: 170px;
        right: 0;
      }
  </style>
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="d-flex align-items-center">
    <div class="container d-flex align-items-center justify-content-between">

      <h1 class="logo"><a href="index.html"><img src="images/uniska-bjm.png"> Perkuliahan GIS</a></h1>
      <!-- Uncomment below if you prefer to use an image logo -->
      <!-- <a href="index.html" class="logo"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>-->

      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto active" href="#hero">Home</a></li>
          <li><a class="nav-link scrollto" href="#about">About</a></li>
          <li><a class="nav-link scrollto" href="#services">Services</a></li>
          
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->

    </div>
  </header><!-- End Header -->
 <!-- ======= Hero Section ======= -->
 <main id="main"></main>
    <div class="hero-container">
        <!-- Map -->
        <div id="map" class="map"></div>
        <div><label><input type="checkbox" id="rotateWithView"> Rotate with view</label></div>
        <!-- Menu -->
        <div id="MenuMap" class="boxTool" style="bottom: 10px;">
            <button type="button" id="cmdBasemap" class="btn btn-outline-success" title="Basemap"><i class="bi bi-globe bi-x2"></i></button>
            <button type="button" id="cmdLayers" class="btn btn-outline-success" title="Layers"><i class="bi bi-layers"></i></button>
            <button type="button" class="btn btn-outline-success" title="Pengukuran"><i class="bi bi-rulers"></i></button>
        </div>
        <!-- Basemap -->
        <div class="boxTool card col-5" id="BoxBasemap" style="display:none;">
          <div class="card-header" id="BoxBasemapheader">Basemaps</div>
          <div class="card-body row" id="Basemaps"></div>
        </div>
        
        <!-- Daftar Layers -->
        <div class="boxTool card col-3" id="BoxLayers">
          <div class="card-header" id="BoxLayersheader">Daftar Layers</div>
          <div class="card-body row" id="BoxLayers">
            <ul id="LayerTreeList"></ul>
          </div>
        </div>

    </div>

</main><!-- End #main -->    

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
<script src="Lib/ol/ol.js"></script>
<script src="js/dragmove.js"></script>
<script src="js/jquery-3.6.0.min.js"></script>
<script>
// pengaturan posisi  
document.getElementById("map").style.height = screen.height -200 + "px";
document.getElementById("MenuMap").style.left = screen.width/2 - 100 + "px";
document.getElementById("BoxBasemap").style.left = screen.width/2 - 300 + "px";
document.getElementById("BoxBasemap").style.top = screen.height/2 -200 + "px";
document.getElementById("BoxLayers").style.top = '150px'
//mengaktifkan mode move
dragElement(document.getElementById("BoxBasemap"));
dragElement(document.getElementById("BoxLayers"));


document.getElementById("cmdBasemap").onclick = function(){
  if (document.getElementById("BoxBasemap").style.display == 'none'){
    document.getElementById("BoxBasemap").style.display = 'block';
  }else{
    document.getElementById("BoxBasemap").style.display = 'none';
  }
};

document.getElementById("cmdLayers").onclick = function(){
  if (document.getElementById("BoxLayers").style.display == 'none'){
    document.getElementById("BoxLayers").style.display = 'block';
  }else{
    document.getElementById("BoxLayers").style.display = 'none';
  }
};

//global variabel
var Basemap;
var Group =  ol.layer.LayerGroup;
var marking = false;
var coordinates;
var layerP;
var BasmemapLayer;
var map;
var ZIdx = 0;

// fungsi definisi peta dasar
Basemap = new ol.layer.Tile({
      source: new ol.source.OSM(),
      name : "Basemap",
      LegenUrl:'',
      LegentType :'ESRI',
    });

// web gis controller
var attribution = new ol.control.Attribution({
	collapsible: true
});

//overview
var overviewMapControl = new ol.control.OverviewMap({
  className: 'ol-overviewmap ol-custom-overviewmap',
  layers: [
  new ol.layer.Tile({
    source: new ol.source.XYZ({
      url: 'https://services.arcgisonline.com/arcgis/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
    })
  })],
  collapseLabel: '\u00BB',
  label: '\u00AB',
  collapsed:true,
});

// untuk map preview
// var overlay = new ol.Overlay({
//   element: container,
//   autoPan: true,
//   autoPanAnimation: {
// 	duration: 250,
//   },
// });

//waribael utama SIG
map = new ol.Map({
	controls: ol.control.defaults({attribution: true}).extend([attribution,overviewMapControl]),
	//overlays: [overlay],
	layers: [Basemap,
		new  ol.layer.Tile({
			source: new ol.source.TileArcGISRest({
				url: 'https://services.arcgisonline.com/arcgis/rest/services/Canvas/World_Dark_Gray_Reference/MapServer',
			}),
		name: 'World Label',
		LegenUrl:'',
		LegentType :'ESRI',
		InfoPopup : false,
		metadata : 'https://services.arcgisonline.com/arcgis/rest/services/Canvas/World_Dark_Gray_Reference/MapServer/info/iteminfo',
		visible: true
	 }),
	 new  ol.layer.Tile({
			source: new ol.source.TileArcGISRest({
				url: 'http://server.arcgisonline.com/arcgis/rest/services/Reference/World_Transportation/MapServer',
				}),
			name: 'Trasportation',
			LegenUrl: '',
			LegentType :'ESRI',
			InfoPopup : false,
			metadata : 'http://server.arcgisonline.com/arcgis/rest/services/Reference/World_Transportation/MapServer/info/iteminfo',
			visible: true
	 }),
     new ol.layer.Tile({
            source: new ol.source.TileWMS({
                    url: 'http://localhost:8080/geoserver/wms?',
                    params: { 'LAYERS': 'KOTA_ADMINISTRASI_PT_25K' }
                }),
        name : "Toponimi",
        LegenUrl: '',
			  LegentType :'Geoserver',
    }),
    new ol.layer.Tile({
            source: new ol.source.TileWMS({
                    url: 'http://localhost:8080/geoserver/wms?',
                    params: { 'LAYERS': 'KOTA_ADMINISTRASI_PT_25K' }
                }),
        name : "Demografi",
        LegenUrl: '',
			  LegentType :'Geoserver',
    })
	 ],
	target: 'map',
	view: new ol.View({
		center: ol.proj.fromLonLat([115.32424, -2.2323177]),
		zoom:6
	})
}); 


//-------LOad Layernme Tree Layer-----------
function LoadTreLayerList(){	
  var n=0;		
  var lyrTreL="";
  $('#LayerTreeList').html()	
	map.getLayers().forEach(function(layer) {
	if(layer.get('name')){
	 var lgd = layer.get('LegenUrl');
		 if(lgd){
			 if(layer.get('LegentType') == 'ESRI'){
			 var ListLegenTree = '<li class="list-group-item list-group-item" id="layer'+n+'_LI"  onClick="resizeIframe('+n+')"><div class="btn-group dropright btn-block" draggable="true"><button type="button" class="btn btn-sm"><input id="layer'+n+'_vsb" class="visible" type="checkbox"> </button><button type="button" id="layer'+n+'_NM" class="btn btn-sm btn-block" style="text-align:left;">'+layer.get('name')+'</button><button type="button" class="btn btn-sm dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button><div class="dropdown-menu"><a class="dropdown-item" href="#"><label class="checkbox text-info" for="layer'+n+'popup"><i class="far fa-comment-alt"></i> Popup <input id="layer'+n+'popup" class="popup" type="checkbox"/ checked></label></a><a class="dropdown-item" href="#"><label class="text-warning"> <i class="fa fa-adjust" aria-hidden="true"></i> Opacity <input class="opacity" id="layer'+n+'_opc" type="range" min="0" max="1" step="0.01"/></label></a><a class="dropdown-item" href="#" id="layer'+n+'_zm"><i class="fa fa-search-plus"></i> Zoom to Layer</a><div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#" id="layer'+n+'_rmv"><i class="fa fa-trash"></i> Remove</a><a class="dropdown-item text-success" href="'+layer.get('metadata')+'" target="_blank"><i class="fa fa-info-circle"></i> Metadata</a></div></div> <fieldset id="layer'+n+'_FS"><iframe id="layer'+n+'_LGN" class="style-5" width="300px"  src="/Prektek_WebGIS-GeoServis/Esri_legenda.php?url='+lgd+'" style="border:none;"></iframe></fieldset></li>';
			 }else {
			var ListLegenTree = '<li class="list-group-item list-group-item" id="layer'+n+'_LI" ><div class="btn-group dropright btn-block" draggable="true"><button type="button" class="btn btn-sm"><input id="layer'+n+'_vsb" class="visible" type="checkbox"> </button><button type="button" id="layer'+n+'_NM" class="btn btn-sm btn-block" style="text-align:left;">'+layer.get('name')+'</button><button type="button" class="btn btn-sm dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button><div class="dropdown-menu"><a class="dropdown-item" href="#"><label class="checkbox text-info" for="layer'+n+'popup"><i class="far fa-comment-alt"></i> Popup <input id="layer'+n+'popup" class="popup" type="checkbox"/ checked></label></a><a class="dropdown-item" href="#"><label class="text-warning"> <i class="fa fa-adjust" aria-hidden="true"></i> Opacity <input class="opacity" id="layer'+n+'_opc" type="range" min="0" max="1" step="0.01"/></label></a><a class="dropdown-item" href="#" id="layer'+n+'_zm"><i class="fa fa-search-plus"></i> Zoom to Layer</a><div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#" id="layer'+n+'_rmv"><i class="fa fa-trash"></i> Remove</a><a class="dropdown-item text-success" href="https://geoservice.kalselprov.go.id/ServisDerektory/?IdSearch='+layer.get('metadata')+'" target="_blank"><i class="fa fa-info-circle"></i> Metadata</a></div></div> <fieldset id="layer'+n+'_FS"><img src="'+lgd+'"></fieldset></li>';	
			 }
		 }else{
			var ListLegenTree = '<li class="list-group-item list-group-item" id="layer'+n+'_LI" ><div class="btn-group dropright btn-block" draggable="true"><button type="button" class="btn btn-sm"><input id="layer'+n+'_vsb" class="visible" type="checkbox"> </button><button type="button" id="layer'+n+'_NM" class="btn btn-sm btn-block" style="text-align:left;">'+layer.get('name')+'</button><button type="button" class="btn btn-sm dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button><div class="dropdown-menu"><a class="dropdown-item" href="#"><label class="checkbox text-info" for="layer'+n+'popup"><i class="far fa-comment-alt"></i> Popup <input id="layer'+n+'popup" class="popup" type="checkbox"/ checked></label></a><a class="dropdown-item" href="#"><label class="text-warning"> <i class="fa fa-adjust" aria-hidden="true"></i> Opacity <input class="opacity" id="layer'+n+'_opc" type="range" min="0" max="1" step="0.01"/></label></a><a class="dropdown-item" href="#" id="layer'+n+'_zm"><i class="fa fa-search-plus"></i> Zoom to Layer</a><div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#" id="layer'+n+'_rmv"><i class="fa fa-trash"></i> Remove</a><a class="dropdown-item text-success" href="https://geoservice.kalselprov.go.id/ServisDerektory/?IdSearch='+layer.get('metadata')+'" target="_blank"><i class="fa fa-info-circle"></i> Metadata</a></div></div> <fieldset id="layer'+n+'_FS">-</li>';	
		 }
		lyrTreL = ListLegenTree+lyrTreL;
		n++;
		}
	 });
	 
	 $('#LayerTreeList').html(lyrTreL)
};


//membuat HTML list tree
 LoadTreLayerList();

//====================FUNGSI TREE LAYER MENU==================
function bindInputs(layerid, layer) {
	
  var visibilityInput = $(layerid + '_vsb');
  visibilityInput.on('change', function () {
  layer.setVisible(this.checked);
  });
      
  visibilityInput.prop('checked', layer.getVisible());
  var opacityInput = $(layerid + '_opc');
  opacityInput.on('input', function () {
  layer.setOpacity(parseFloat(this.value));
  });
  opacityInput.val(String(layer.getOpacity()));
  
  var LgndInput = $(layerid + '_NM');
  var LgndDIV = $(layerid + '_FS');
  LgndDIV.hide();
    LgndInput.on('click', function () {
      LgndDIV.show();
   });
   LgndInput.on('dblclick', function () {
      LgndDIV.hide();
   });
  //------------REMOVE LAYER--------------------
   var CmdRemoveLayer = $(layerid + '_rmv');	   
   CmdRemoveLayer.on('click', function () {
    map.removeLayer(layer);
    var LayrLI = $(layerid + '_LI');
    LayrLI.remove();
   });
   //-----------MOVE Z Index LAYER--------------------
  layer.setZIndex(ZIdx);
  ZIdx++;
  //------ ZOOM TO EXTEND LAYER---------------
  var CmdMenuZoomLyr = $(layerid + '_zm');	   
      CmdMenuZoomLyr.on('click', function () {			  
     //map.getView().fit(layer.getExtent());
     var extent = layer.getExtent();
    map.getView().fit(extent);		   		   
   });
   //------POPUP LAYER---------------
  var Elmpopup = $(layerid + 'popup');
    Elmpopup.on('change', function () {
   layer.set('InfoPopup', this.checked);
  });
      
   
}

//===  FUNGSI LOAD DATA LAYER =====	
function setup(id, group) {
  group.getLayers().forEach(function (layer, i) {
  var layerid = id + i;
  bindInputs(layerid, layer);
  if (layer instanceof ol.layer.Group) {
      setup(layerid, layer);
  }
  });
}
//=== MEMANGGIL FUNGSI LOAD DATA LAYER ====
setup('#layer', map.getLayerGroup());


 
function UpdateBaseMap(u,t,p){
  if(t== "ESRI"){
    var NewBaseMap = new ol.source.TileArcGISRest({
      url : u,
      attributions  : ['Open Layer, Esri, Hidayatul Rahman']
    });
    Basemap.setSource(NewBaseMap); 
  }else{
    var NewBaseMap = new ol.source.TileWMS({
      url : u,
      params : {LAYERS :p, VERSION : '1.1.1'}
    });
    Basemap.setSource(NewBaseMap);
  }
}
</script> 
 
<!-- Basemap JS function -->
<script>
function LoadBasemap(){
  $.ajax({
    url : "datas/basemap.json",
    async : false,
    success : function(data){
      for (i=0; i<data.length; i++){
        var u = "'"+data[i]["url"]+"'";
        var t = "'"+data[i]["type"]+"'";
        var p = "'"+data[i]["param"]+"'";
        var BsMapHTML = '<div class="card" style="width: 10rem;" onclick="UpdateBaseMap('+u+','+t+','+p+')"><img class="card-img-top" src="images/basemaps/'+data[i]["thumimg"]+'" alt="'+data[i]["nama"]+'"> <small class="card-title">'+data[i]["nama"]+'</small> </div></div>';
        document.getElementById("Basemaps").innerHTML = BsMapHTML + document.getElementById("Basemaps").innerHTML;
      }
    }
  })
};
LoadBasemap();
</script>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/purecounter/purecounter.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>