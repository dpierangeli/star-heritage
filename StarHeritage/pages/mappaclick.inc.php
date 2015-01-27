<?php /*HELP: */ 
# include('../ref_header.inc.php'); /*Header comune*/ 

/*Aggiorno la mappa corrente del PG*/
if (isset($_GET['map_id'])===TRUE)
{

	/** * Questa query va spostata in main, anche per dare maggior stabilità al sistema di spostamento fra mappe
		* @author Blancks
	*/
	#gdrcd_query("UPDATE personaggio SET ultima_mappa=".gdrcd_filter('num',$_GET['map_id']).", ultimo_luogo=-1 WHERE nome = '".$_SESSION['login']."'");

   $current_map=gdrcd_filter('num',$_GET['map_id']);
}//if
else {$current_map=$_SESSION['mappa'];}
$redirect_pc=0;
/*Se ho richiesto di far partire o arrivare una mappa mobile*/
if ( (isset($_POST['op'])===TRUE) && ( ($_POST['op']==gdrcd_filter('out',$MESSAGE['interface']['maps']['leave']))||($_POST['op']==gdrcd_filter('out',$MESSAGE['interface']['maps']['arrive'])) ) ){
   /*Aggiorno la sua posizione*/
   gdrcd_query("UPDATE mappa_click SET posizione = ".gdrcd_filter('num',$_POST['destination'])." WHERE id_click = ".gdrcd_filter('num',$_REQUEST['map_id'])." LIMIT 1");
}

if ( (isset($_POST['op'])===TRUE) && 
	 ($_POST['op']==gdrcd_filter('out',$MESSAGE['interface']['maps']['set_meteo'])) ){
   /*Aggiorno la sua posizione*/
   gdrcd_query("UPDATE mappa_click SET meteo = '".gdrcd_filter('num',$_POST['temperature'])."°C - ".gdrcd_filter('in',$_POST['climate'])."' WHERE id_click = ".gdrcd_filter('num',$_REQUEST['map_id'])." LIMIT 1");

}

/*Seleziono le voci della mappa*/
$result=gdrcd_query("SELECT mappa.id, mappa.nome, mappa.chat, mappa.x_cord, mappa.y_cord, mappa.id_mappa, mappa_click.nome AS nome_mappa, mappa_click.immagine, mappa_click.posizione, mappa_click.id_click, mappa_click.mobile, mappa_click.larghezza, mappa_click.altezza FROM mappa_click LEFT JOIN mappa ON mappa.id_mappa = mappa_click.id_click WHERE mappa_click.id_click = ".$current_map."", 'result');

if (gdrcd_query($result, 'num_rows')==0)
{
    $result = gdrcd_query("SELECT id_click FROM mappa_click LIMIT 1", 'result');
}

if (gdrcd_query($result, 'num_rows')==0){
       echo '<div class="error">'.gdrcd_filter('out',$MESSAGE['error']['can_t_find_any_map']).'</div>';
} else {

$just_one_click=gdrcd_query($result, 'fetch');
gdrcd_query($result, 'free');


	$result=gdrcd_query("SELECT mappa.id, mappa.nome, mappa.chat, mappa.link_immagine, mappa.descrizione, mappa.link_immagine_hover, mappa.id_mappa_collegata, mappa.x_cord, mappa.y_cord, mappa.id_mappa, mappa.pagina, mappa_click.nome AS nome_mappa, mappa_click.immagine, mappa_click.posizione, mappa_click.id_click, mappa_click.mobile, mappa_click.larghezza, mappa_click.altezza FROM mappa_click LEFT JOIN mappa ON mappa.id_mappa = mappa_click.id_click WHERE mappa_click.id_click = ".$just_one_click['id_click']."", 'result');
	$redirect_pc=1;

/*Stampo la mappa cliccabile*/
echo '<div class="pagina_mappaclick">';
   
   $echoed_title=FALSE;
   $echo_bottom=FALSE;
   $vicinato=0;
   $self=0;
   $mobile=0;
   
    if ($current_map == 1)
	  {
	    $row=gdrcd_query($result, 'fetch');
	    $echoed_title=TRUE;  
		  $echo_bottom=TRUE;
		  $vicinato=$row['posizione'];
		  $self=$row['id_click'];
		  $mobile=$row['mobile'];  
	    ?>
	    <script>
        var animating = false;
        
        function showArea (area)
        {
          if (animating)
            return;
          //
          var imgmap = document.getElementById(area);
          //
          imgmap.style.display = '';
        }
        
        function hideArea(area)
        {
           if (animating)
            return;
          //
          var imgmap = document.getElementById(area);
          imgmap.style.display = 'none';
        }
        
        function clickArea(area)
        {
          if (animating)
            return;
          //
          var starE = document.getElementById('EMPIRE');
          var starR = document.getElementById('REPUBLIC');
          var starV = document.getElementById('VONG');
          var starH = document.getElementById('HUTT');
          //
          starE.style.display = 'none';
          starR.style.display = 'none';
          starV.style.display = 'none';
          starH.style.display = 'none';
          //
          var imgmap = document.getElementById(area);
          imgmap.style.display = '';
          //          
          document.getElementById(area).addEventListener("transitionend", OnEndTransition); 
          document.getElementById(area).addEventListener("webkitTransitionEnd", OnEndTransition); 
          animating = true;
          window.setTimeout('document.getElementById("'+area+'").style.opacity = 1;', 200);
        }
        
        function OnEndTransition()
        {
          animating = false;
        }
        
        function backArea(area)
        {
          if (animating)
            return;
          //
          var imgmap = document.getElementById(area);
          //
          switch(area)
          {
            case 'EMPIRE_MAIN':
              imgmap.addEventListener("transitionend", EmpOnEndTransition); 
              imgmap.addEventListener("webkitTransitionEnd", EmpOnEndTransition); 
            break;
            
            case 'REPUBLIC_MAIN':
              imgmap.addEventListener("transitionend", RepOnEndTransition); 
              imgmap.addEventListener("webkitTransitionEnd", RepOnEndTransition); 
            break;
            
            case 'HUTT_MAIN':
              imgmap.addEventListener("transitionend", HuttOnEndTransition); 
              imgmap.addEventListener("webkitTransitionEnd", HuttOnEndTransition); 
            break;
            
            case 'VONG_MAIN':
              imgmap.addEventListener("transitionend", VongOnEndTransition); 
              imgmap.addEventListener("webkitTransitionEnd", VongOnEndTransition); 
            break;
          }
          //    
          animating = true;
          imgmap.style.opacity = 0;
        }
        
        function EmpOnEndTransition()
        {
          var m = document.getElementById('EMPIRE_MAIN');
          //
          m.style.display="none";
          m.removeEventListener("transitionend", EmpOnEndTransition); 
          m.removeEventListener("webkitTransitionEnd", EmpOnEndTransition); 
          animating = false;
        }
        
        function RepOnEndTransition()
        {
          var m = document.getElementById('REPUBLIC_MAIN');
          //
          m.style.display="none";
          m.removeEventListener("transitionend", RepOnEndTransition); 
          m.removeEventListener("webkitTransitionEnd", RepOnEndTransition); 
          animating = false;
        }
        
        function HuttOnEndTransition()
        {
          var m = document.getElementById('HUTT_MAIN');
          //
          m.style.display="none";
          m.removeEventListener("transitionend", HuttOnEndTransition); 
          m.removeEventListener("webkitTransitionEnd", HuttOnEndTransition); 
          animating = false;
        }
        
        function VongOnEndTransition()
        {
          var m = document.getElementById('VONG_MAIN');
          //
          m.style.display="none";
          m.removeEventListener("transitionend", VongOnEndTransition); 
          m.removeEventListener("webkitTransitionEnd", VongOnEndTransition); 
          animating = false;
        }
        
      </script>
    
	    <div class="mappaclick_map" style="width:800px; height:637px; overflow:hidden">
	    <img id='starmap' src='themes/advanced/imgs/maps/SWLandMap_Base.jpg' style='' usemap='#zonemap' />
	    <img id= 'HUTT' src='themes/advanced/imgs/maps/SWLandMap_Hutt.png' style='position:relative; top:-637px; display:none;' usemap='#zonemap' />
	    <img id= 'EMPIRE' src='themes/advanced/imgs/maps/SWLandMap_Impero.png' style='position:relative; top:-637px; display:none;' usemap='#zonemap' />
	    <img id= 'REPUBLIC' src='themes/advanced/imgs/maps/SWLandMap_Repubblica.png' style='position:relative; top:-637px; display:none;' usemap='#zonemap' />
	    <img id= 'VONG' src='themes/advanced/imgs/maps/SWLandMap_Vong.png' style='position:relative; top:-637px; display:none;' usemap='#zonemap' />
	    <map name='zonemap'>
	      <area  name='EMPIRE' onmouseover='showArea("EMPIRE")' onmouseout='hideArea("EMPIRE")' onclick='clickArea("EMPIRE_MAIN")' href='#' shape='poly' coords='370,243,368,232,368,224,371,214,378,204,389,202,400,199,412,202,426,207,441,210,467,219,479,223,500,233,508,246,529,264,542,282,557,304,567,323,579,341,581,358,578,368,568,378,560,387,547,393,536,398,527,403,521,404,527,396,534,388,538,382,544,373,544,367,544,360,530,356,518,351,507,346,496,341,490,336,491,321,489,306,489,296,485,281,476,272,458,255,450,247,431,236,419,230,402,224,392,221,381,222,376,238' style='outline:none;' target='_self'     />
	      <area  name='VONG' onmouseover='showArea("VONG")' onmouseout='hideArea("VONG")' onclick='clickArea("VONG_MAIN")' href='#' shape='poly' coords='414,450,432,444,454,436,466,431,486,420,492,416,502,408,510,400,520,391,528,381,536,371,532,363,522,358,508,351,494,350,489,345,484,336,482,320,480,303,477,290,467,273,456,267,449,259,436,249,427,243,417,235,406,230,398,227,394,226,399,234,414,240,417,247,429,259,438,268,445,281,451,297,456,308,460,330,459,342,459,351,461,362,468,369,474,381,469,397,461,407,451,413,442,427,431,435,420,444' style='outline:none;' target='_self'     />
        <area  name='HUTT' onmouseover='showArea("HUTT")' onmouseout='hideArea("HUTT")' onclick='clickArea("HUTT_MAIN")' href='#' shape='poly' coords='345,386,342,395,340,408,341,419,358,426,372,430,391,432,405,434,421,434,432,428,437,420,442,412,445,406,447,398,448,389,449,383,445,393,434,404,426,407,414,409,400,409,372,398' style='outline:none;' target='_self'     />
        <area  name='REPUBLIC' onmouseover='showArea("REPUBLIC")' onmouseout='hideArea("REPUBLIC")' onclick='clickArea("REPUBLIC_MAIN")' href='#' shape='poly' coords='292,304,293,295,297,290,299,283,305,277,311,269,317,264,321,258,328,254,335,250,338,248,364,252,374,254,377,250,381,244,386,238,389,233,401,245,412,257,424,270,437,283,444,298,452,318,451,334,451,350,452,366,447,372,442,383,435,393,431,396,425,402,413,404,401,402,387,399,373,392,359,389,350,383,342,376,336,372,329,368,322,362,325,354,325,347,324,334,322,327,310,316' style='outline:none;' target='_self'     />
	    </map>
	    
	    <img id='EMPIRE_MAIN' src='themes/advanced/imgs/maps/aree/Base_Impero.jpg' style='position:relative; top:-637px; display:none; opacity: 0; transition: opacity 750ms; -webkit-transition: opacity 750ms;' usemap='#empmap' />
	    <img id='EMPIRE_BASTION' src='themes/advanced/imgs/maps/aree/Base_Bastion.jpg' style='position:relative; top:-1237px; display:none;' usemap='#empmap' />
	    <img id='EMPIRE_KORRIBAN' src='themes/advanced/imgs/maps/aree/Base_Korriban.jpg' style='position:relative; top:-1237px; display:none;' usemap='#empmap' />
	    <img id='EMPIRE_TARIS' src='themes/advanced/imgs/maps/aree/Base_Taris.jpg' style='position:relative; top:-1237px; display:none;' usemap='#empmap' />
	    
	    <map name="empmap" id="empmap">
	      <area name="BACK" onclick='backArea("EMPIRE_MAIN")' alt="" title="" href="#" shape="poly" coords="724,71,709,58,728,36,710,19,722,5,743,22,763,4,780,18,759,39,781,58,765,75,743,53" style="outline:none;" target="_self"     />
        <area name="BASTION" onmouseover='showArea("EMPIRE_BASTION")' onmouseout='hideArea("EMPIRE_BASTION")' href="main.php?page=mappaclick&map_id=4" shape="poly" coords="207,196,242,190,264,178,284,164,299,142,305,117,307,101,303,73,298,59,286,40,277,30,261,17,247,12,230,6,215,5,199,5,181,11,161,20,149,26,134,46,122,55,115,81,110,102,111,125,120,145,131,163,149,176,173,190" style="outline:none;" target="_self"     />
        <area name="TARIS" onmouseover='showArea("EMPIRE_TARIS")' onmouseout='hideArea("EMPIRE_TARIS")' href="main.php?page=mappaclick&map_id=11" shape="poly" coords="399,209,416,232,441,247,475,257,498,260,515,257,537,251,550,244,564,238,573,230,583,219,590,207,598,191,601,163,601,150,593,129,574,105,566,95,553,88,534,80,520,77,498,75,475,78,457,83,435,92,423,101,403,116,397,129,389,153,391,179" style="outline:none;" target="_self"     />
        <area name="KORRIBAN" onmouseover='showArea("EMPIRE_KORRIBAN")' onmouseout='hideArea("EMPIRE_KORRIBAN")' href="main.php?page=mappaclick&map_id=5" shape="poly" coords="550,474,544,452,541,428,546,414,547,400,551,381,564,367,578,355,591,346,600,339,613,333,630,330,646,328,666,331,686,339,708,357,720,371,732,392,737,418,736,445,721,472,721,485,707,501,694,515,671,523,651,526,630,530,602,524,576,510" style="outline:none;" target="_self"     />
      </map>
	    
	    <img id='REPUBLIC_MAIN' src='themes/advanced/imgs/maps/aree/Base_Repubblica.jpg' style='position:relative; top:-637px; display:none; opacity: 0; transition: opacity 750ms; -webkit-transition: opacity 750ms;' usemap='#repmap' />
	    <img id='REPUBLIC_CORUSCANT' src='themes/advanced/imgs/maps/aree/Base_Coruscant.jpg' style='position:relative; top:-1237px; display:none;' usemap='#repmap' />
	    <img id='REPUBLIC_CORELLIA' src='themes/advanced/imgs/maps/aree/Base_Corellia.jpg' style='position:relative; top:-1237px; display:none;' usemap='#repmap' />
	    <img id='REPUBLIC_TYTHON' src='themes/advanced/imgs/maps/aree/Base_Tython.jpg' style='position:relative; top:-1237px; display:none;' usemap='#repmap' />
	    
	    <map name="repmap" id="repmap">
	      <area name="BACK" onclick='backArea("REPUBLIC_MAIN")' alt="" title="" href="#" shape="poly" coords="724,71,709,58,728,36,710,19,722,5,743,22,763,4,780,18,759,39,781,58,765,75,743,53" style="outline:none;" target="_self"     />
        <area name="CORUSCANT" onmouseover='showArea("REPUBLIC_CORUSCANT")' onmouseout='hideArea("REPUBLIC_CORUSCANT")' alt="" title="" shape="poly" coords="251,208,283,202,308,204,342,221,356,236,367,255,372,277,372,306,363,319,358,331,341,347,329,354,316,361,301,368,274,367,255,364,242,353,227,340,215,331,210,305,205,289,207,265,216,244,225,226" style="outline:none;" target="_self" href="main.php?page=mappaclick&map_id=3" />
        <area name="TYTHON" onmouseover='showArea("REPUBLIC_TYTHON")' onmouseout='hideArea("REPUBLIC_TYTHON")' alt="" title="" href="main.php?page=mappaclick&map_id=7" shape="poly" coords="450,185,435,187,412,185,400,179,392,171,381,163,370,155,364,139,359,122,359,106,362,88,373,73,385,60,396,52,416,49,440,45,461,45,476,55,491,74,506,92,507,114,503,139,496,153,488,163,473,175" style="outline:none;" target="_self"     />
        <area name="CORELLIA" onmouseover='showArea("REPUBLIC_CORELLIA")' onmouseout='hideArea("REPUBLIC_CORELLIA")' alt="" title="" href="main.php?page=mappaclick&map_id=8" shape="poly" coords="464,407,506,393,529,395,562,408,586,434,598,457,597,483,586,515,571,532,555,542,541,550,526,551,503,553,482,548,466,541,449,525,433,513,428,483,428,462,433,445,441,427" style="outline:none;" target="_self"     />
      </map>
	    
	    <img id='HUTT_MAIN' src='themes/advanced/imgs/maps/aree/Base_Hutt.jpg' style='position:relative; top:-637px; display:none; opacity: 0; transition: opacity 750ms; -webkit-transition: opacity 750ms;' usemap='#huttmap' />
	    <img id='HUTT_NALHUTTA' src='themes/advanced/imgs/maps/aree/Base_NalHutta.jpg' style='position:relative; top:-1237px; display:none;' usemap='#huttmap' />
	    <img id='HUTT_HOTH' src='themes/advanced/imgs/maps/aree/Base_Hoth.jpg' style='position:relative; top:-1237px; display:none;' usemap='#huttmap' />
	    
	    <map name="huttmap" id="huttmap">
	      <area name="BACK" onclick='backArea("HUTT_MAIN")' alt="" title="" href="#" shape="poly" coords="724,71,709,58,728,36,710,19,722,5,743,22,763,4,780,18,759,39,781,58,765,75,743,53" style="outline:none;" target="_self"     />
        <area name="NALHUTTA" onmouseover='showArea("HUTT_NALHUTTA")' onmouseout='hideArea("HUTT_NALHUTTA")' alt="" title="" href="main.php?page=mappaclick&map_id=6" shape="poly" coords="488,171,527,162,567,165,593,171,614,185,630,197,640,216,649,243,648,276,648,304,630,337,617,351,590,371,569,376,549,383,523,384,504,374,484,367,463,348,451,334,438,303,431,278,434,248,445,214,458,192" style="outline:none;" target="_self"     />
        <area name="HOTH" onmouseover='showArea("HUTT_HOTH")' onmouseout='hideArea("HUTT_HOTH")' alt="" title="" href="main.php?page=mappaclick&map_id=9" shape="poly" coords="195,268,244,276,285,303,302,322,317,367,314,394,309,426,298,453,283,469,267,482,243,497,219,503,200,504,158,497,143,486,120,465,105,454,95,431,84,399,79,374,87,349,104,316,117,297,143,280" style="outline:none;" target="_self"     />
      </map>
	    
	    <img id='VONG_MAIN' src='themes/advanced/imgs/maps/aree/Base_Vong.jpg' style='position:relative; top:-637px; display:none; opacity: 0; transition: opacity 750ms; -webkit-transition: opacity 750ms;' usemap='#vongmap' />
	    <img id='VONG_ORD' src='themes/advanced/imgs/maps/aree/Base_Mantell.jpg' style='position:relative; top:-1237px; display:none;' usemap='#vongmap' />
	    
	    <map name="vongmap" id="vongmap">
	      <area name="BACK" onclick='backArea("VONG_MAIN")' alt="" title="" href="#" shape="poly" coords="724,71,709,58,728,36,710,19,722,5,743,22,763,4,780,18,759,39,781,58,765,75,743,53" style="outline:none;" target="_self"     />
        <area name="ORD" onmouseover='showArea("VONG_ORD")' onmouseout='hideArea("VONG_ORD")' alt="" title="" href="main.php?page=mappaclick&map_id=10" shape="poly" coords="479,348,522,314,540,300,572,288,607,293,641,299,664,315,682,332,698,354,702,384,707,407,703,433,681,471,672,489,656,505,638,514,613,523,590,528,559,522,524,513,504,494,487,475,475,443,466,423,464,387" style="outline:none;" target="_self"     />
      </map>
	    </div>
	    
	    <div class="page_title">
       <h2>...</h2>
      </div>
	    <div class="mappaclick_more">
	    <a href="main.php?dir=6" target="_top" >Anello Interno</a>
	    </div>
	    <div class="mappaclick_more">
	    <a href="main.php?dir=7" target="_top" >Anello Intermedio</a>
	    </div>
	    <div class="mappaclick_more">
	    <a href="main.php?dir=8" target="_top" >Anello Esterno</a>
	    </div>
	    <div class="mappaclick_more">
	    <a href="main.php?dir=9" target="_top" >Chat OFF</a>
	    </div>
	    <div class="mappaclick_more">
	    <a href="main.php?page=mappaclick&map_id=12" target="_top" >Chat Master</a>
	    </div>
	    <?php
	  }
	  else
	  {

   
   while ($row=gdrcd_query($result, 'fetch')){

	  /*Se il personaggio si trovava in una mappa inesistente o cancellata aggiorno la sua posizione*/
	  if ($redirect_pc==1){
		  gdrcd_query("UPDATE personaggio SET ultima_mappa=".gdrcd_filter('get',$row['id_click'])." WHERE nome = '".$_SESSION['login']."'");
	  }
	  
	  
	  
	  
      /*Stampo il titolo, se non l'ho gia' fatto*/
	  if($echoed_title===FALSE){
	     echo '<div class="page_title">'; 
         echo '<h2>'.$row['nome_mappa'].'</h2>';
         echo '</div>';
         
         
			/** * Abilitazione tooltip
				* @author Blancks
			*/
			if ($PARAMETERS['mode']['map_tooltip'] == 'ON')
					echo '<div id="descriptionLoc"></div>';
					
         
		 echo '<div class="mappaclick_map" style="background:url(\'themes/', $PARAMETERS['themes']['current_theme'], '/imgs/maps/', $row['immagine'], '\') top left no-repeat; width:', $row['larghezza'], 'px; height:', $row['altezza'], 'px;">';
		 $echoed_title=TRUE;  
		 $echo_bottom=TRUE;
		 $vicinato=$row['posizione'];
		 $self=$row['id_click'];
		 $mobile=$row['mobile'];
 	  }//if
      
	 
	  
		/*Stampo i link della mappa corrente*/
		/** * Bug Fix: i link sono ora posizionati in relazione alla mappa
			* Features: link a sottomappe e link immagine
			* @author Blancks
		*/
		echo '<div style="position:absolute; margin:', $row['y_cord'], 'px 0 0 ', $row['x_cord'], 'px;">';
		
		$qstring_link = '';
		$label_link = '';
		
		if ($row['chat'] == 1)
		{
			$qstring_link = 'dir='. $row['id'];
		
		}elseif ($row['id_mappa_collegata'] != 0)
		{
			$qstring_link = 'page=mappaclick&map_id='. $row['id_mappa_collegata'];
		
		}else
		{
			$qstring_link = 'page='. $row['pagina'];
		}
		
		
		if (empty($row['link_immagine']))
		{
			$label_link = $row['nome'];
		
		}else
		{
			$baseimg_link = 'themes/'. $PARAMETERS['themes']['current_theme'] .'/imgs/maps/';
		
			if (!empty($row['link_immagine_hover']))
					$switchimg_link = 'onmouseover="this.src=\''. $baseimg_link . $row['link_immagine_hover'] .'\';" onmouseout="this.src=\''. $baseimg_link . $row['link_immagine'] .'\'"';
			else
					$switchimg_link = '';
		
			$label_link = '<img src="'. $baseimg_link . $row['link_immagine'] .'" alt="'. $row['nome'] .'" '. $switchimg_link .' />';
		}
		
		
		$fadedesc_link = '';
		
		/** * Abilitazione tooltip
			* @author Blancks
		*/
		if ($PARAMETERS['mode']['map_tooltip'] == 'ON')
		{
			if (!empty($row['descrizione']))
			{
				$descrizione = trim(nl2br(gdrcd_filter('in',$row['descrizione'])));
				$descrizione = strtr($descrizione, array("\n\r" => '', "\n" => '', "\r" => '', '"' => '&quot;'));
		
				$fadedesc_link = 'onmouseover="show_desc(event, \''.$descrizione.'\');" onmouseout="hide_desc();"';
			}
		}
	
		echo '<a href="main.php?', $qstring_link, '" target="_top"', $fadedesc_link,'>', $label_link, '</a>';
    if ($row['nome_mappa'] == 'Hoth')
      echo '<div style="font-weight: bold; background-color:white; color:rgba(137, 116, 22, 1);">'.$row['nome'].'</div>';
    else
      echo '<div style="font-weight: bold;">'.$row['nome'].'</div>';
		
		echo '</div>';
		
           
   }//while 
   if($echo_bottom===TRUE){
	   echo '</div>';
	   $echo_bottom=FALSE;		      
   }//if

}

/* Se la mappa non è in viaggio */
if($vicinato!=INVIAGGIO && $current_map != 1){ 
?>

<div class="page_title">
   <h2><?php echo gdrcd_filter('out',$MESSAGE['interface']['maps']['more']); ?></h2>
</div>
<div class="mappaclick_more">
<?php /* Carico le mappe dell'eventuale vicinato */
	$result = gdrcd_query("SELECT id_click, nome FROM mappa_click WHERE posizione = ".$vicinato." AND id_click <> ".$self." ORDER BY nome", 'result');
	
	if (gdrcd_query($result, 'num_rows')>0)
	{
	   while ($record=gdrcd_query($result, 'fetch')){ ?>
         <a href="main.php?page=mappaclick&map_id=<?php echo $record['id_click']; ?>" target="_top">
		    <?php echo gdrcd_filter('out',$record['nome']); ?>
		 </a>
	   <?php }//while
	   
		gdrcd_query($result, 'free');

    } else {
       echo gdrcd_filter('out',$MESSAGE['interface']['maps']['no_more']);
	}//else
?>
</div>

<?php /* se la mappa è in viaggio */ 
} else if ($current_map != 1) { ?>
    
<div class="page_title">
   <h2><?php echo gdrcd_filter('out',$MESSAGE['interface']['maps']['traveling']); ?></h2>
</div>

<?php }//else ?>
  
<?php /*Controlli partenza mappe mobili/meteo*/ 
      if ($_SESSION['permessi']>=GAMEMASTER){ ?>
          
<div class="form_box">
          <?php if ($mobile==1){?>
<form class="form_gioco" 
      action="mappaclick_inner.inc.php?map_id=<?php echo $_SESSION['mappa'];?>"
	  method="post" > 
   <?php if ($vicinato!=INVIAGGIO) { ?>
        <div class="form_submit">
        <input type="hidden"
	           name="destination"
		       value="<?php echo INVIAGGIO; ?>" 
			   class="game_form_input"/>
	    <input type="submit"
	           value="<?php echo gdrcd_filter('out',$MESSAGE['interface']['maps']['leave']); ?>""
			   name="op" />
		</div>
   <?php } else { 
		    /*Genero la lista delle possibili destinazioni*/
			$result=gdrcd_query("SELECT posizione, nome FROM mappa_click WHERE posizione <> -1 AND id_click <> ".$_SESSION['mappa']." ORDER BY nome", 'result');
			/*Se esistono altre mappe*/
			if (gdrcd_query($result, 'num_rows')>0){ ?>
		<div class="form_submit">
		<select name="destination" class="game_form_selectbox">
			   <?php while($record=gdrcd_query($result, 'fetch')){ ?>
			<option value="<?php echo $record['posizione']; ?>">
                <?php echo gdrcd_filter('out',$record['nome']); ?>
			</option>
			   <?php }
			   
					gdrcd_query($result, 'free');
			   ?>
        </select>
			<?php } else { ?>
		<input type="hidden"
	         name="destination"
			 value="0" 
			 class="game_form_input"/>
			<?php }//else ?>
        <input type="submit"
	         value="<?php echo gdrcd_filter('out',$MESSAGE['interface']['maps']['arrive']); ?>"
			 name="op" />
        </div>
   <?php } //else  ?>
</form>
<?php } 
      if($PARAMETERS['mode']['auto_meteo']=='OFF'){ ?>
<form class="form_gioco" 
      action="mappaclick_inner.inc.php?map_id=<?php echo $_SESSION['mappa'];?>"
	  method="post" >
      <div class="form_submit">
	  <select name="temperature" class="game_form_selectbox">
			<?php for ($i=45; $i>=-45; $i--){ ?>
			<option value="<?php echo $i; ?>" <?php if($i==0){echo ' selected ';}?>>
                <?php echo $i; ?>&ordm; C
			</option>
			<?php }?>
      </select>
	  <select name="climate" class="game_form_selectbox">
			<?php foreach ($MESSAGE['interface']['meteo']['status'] as $climate){ ?>
			<option value="<?php echo $climate; ?>">
                <?php echo $climate; ?>
			</option>
			<?php }?>
      </select>
      <input type="hidden"
	         name="meteo"
	         value="meteo_change" 
	         class="game_form_input"/>
	  <input type="submit"
	         value="<?php echo gdrcd_filter('out',$MESSAGE['interface']['maps']['set_meteo']); ?>"
	         name="op" />
	  </div>
</form>
<?php } ?>
</div>
<?php }//else ?> 
			 

<?php echo '</div>';//Pagina

 }//else 

 #include('../footer.inc.php');  /*Footer comune*/
?>