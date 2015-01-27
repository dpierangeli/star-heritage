<div class="pagina_info_location">
<?php /* HELP: Il box delle informazioni carica l'immagine del luogo corrente, lo stato e la descrizione. Genera, inoltre, il meteo */


$result = gdrcd_query("SELECT mappa.nome, mappa.descrizione, mappa.stato, mappa.immagine, mappa.stanza_apparente, mappa.scadenza, mappa_click.meteo, mappa_click.id_click, mappa_click.MeteoID, mappa_click.MeteoLastRefresh FROM  mappa_click LEFT JOIN mappa ON mappa_click.id_click = mappa.id_mappa WHERE id = ".$_SESSION['luogo']."", 'result'); 
$record_exists = gdrcd_query($result, 'num_rows');
$record = gdrcd_query($result, 'fetch');

/** * Fix: quando non si � in una mappa visualizza il nome della chat
	* Quando si � in una mappa si visualizza il nome della mappa

	* @author Blancks
*/
if (empty($record['nome']))
{
	$nome_mappa = gdrcd_query("SELECT nome FROM mappa_click WHERE id_click = ". (int)$_SESSION['mappa']);
	$nome_luogo = $nome_mappa['nome'];

}else
{
	$nome_luogo = $record['nome'];
}

?>
<div class="page_title">
   <h2><?php echo gdrcd_filter('out', $nome_luogo); ?></h2>
</div>

<div class="page_body">


<?php 
	
if($record_exists>0 || $_SESSION['luogo']==-1){

gdrcd_query($result, 'free');

?>

 
<!--Nome luogo-->
<?php	
   if (empty($record['nome'])===FALSE) { $nome_luogo=$record['nome']; }
   elseif ($_SESSION['mappa']>=0) { $nome_luogo=$PARAMETERS['names']['maps_location']; }
   else { $nome_luogo=$PARAMETERS['names']['base_location']; }
?>

<!--Immagine/descrizione -->
<div class="info_image">
<?php	
   if (empty($record['immagine'])===FALSE) { $immagine_luogo=$record['immagine']; }
   else { $immagine_luogo='standard_luogo.png'; }
?>
   <img src="themes/<?php echo gdrcd_filter('out',$PARAMETERS['themes']['current_theme']);?>/imgs/locations/<?php echo $immagine_luogo?>" class="immagine_luogo" alt="<?php echo gdrcd_filter('out',$record['descrizione']); ?>" title="<?php echo gdrcd_filter('out',$record['descrizione']); ?>" >
</div>
<?php if ((isset($record['stato'])===TRUE)||(isset($record['descrizione'])===TRUE)){
         	 
		  echo '<div class="box_stato_luogo"><marquee onmouseover="this.stop()" onmouseout="this.start()" direction="left" scrollamount="3" class="stato_luogo">&nbsp;'.$MESSAGE['interface']['maps']['Status'].': '.gdrcd_filter('out',$record['stato']).' -  '.gdrcd_filter('out',$record['descrizione']).'</marquee></div>'; } else { echo '<div class="box_stato_luogo">&nbsp;</div>';
		 		 
		 }?>





<?php 
if($PARAMETERS['mode']['auto_metar']=='ON')
{
  if ($_SESSION['mappa'] == 1)
      $meteo = ' ';
   else
   {
     $lastMeteo = $record['meteo'];
     $meteoID = $record['MeteoID'];
     $meteoRefresh = $record['MeteoLastRefresh'];
     //
     if (empty($meteoRefresh))
     {
       $meteoquery = gdrcd_query("SELECT meteo, MeteoLastRefresh, MeteoID FROM mappa_click WHERE id_click = ". (int)$_SESSION['mappa']);
       //
       $lastMeteo = $meteoquery['meteo'];
       $meteoRefresh = $meteoquery['MeteoLastRefresh'];
       $meteoID = $meteoquery['MeteoID'];
      }
      
     $my_date = new DateTime($meteoRefresh);
     
     if($my_date->format('Y-m-d') != date('Y-m-d') )
     { 
       // Need to update meteo info
       $BASE_URL = "http://api.openweathermap.org/data/2.5/group?units=metric&APPID=6ba1a72c6943b25f1924c1ae3948047b&lang=it";

       $yql_query = 'id='.$meteoID;
       $yql_query_url = $BASE_URL."&".$yql_query;
       
       // Make call with cURL
       $sess = curl_init($yql_query_url);
       curl_setopt($sess, CURLOPT_RETURNTRANSFER,true);
       $json = curl_exec($sess);
       // Convert JSON to PHP object
       $phpObj =  json_decode($json);
        
       // Cicliamo su ogni citta'
       for ($i=0; $i < 1; $i++)
       {
         $wh = ''.$phpObj->list[$i]->weather[0]->description;
         $tmp = $phpObj->list[$i]->main->temp;
         $meteo = ''.$wh.' '.$tmp.'°'; 
       }
       
       // Update the meteo for future use
       gdrcd_query("UPDATE mappa_click SET meteo = '".$meteo."' WHERE id_click = '".(int)$_SESSION['mappa']."'");
     }
     else 
     {
       // The meteo is updated
       $meteo=gdrcd_filter('out',$lastMeteo); 
     }
  }
}
else 
{
  if($PARAMETERS['mode']['auto_meteo']=='ON')
  {

    /* Meteo */
    $ore=strftime("%H");
    $minuti=strftime("%M");
    
    $mese=strftime("%m");
    $giorno=strftime("%j");
    $caso=((floor($giorno/3))%2)+1;
    
    /**	* Bug FIX: corretta l'assegnazione della $minima
      * @author Blancks
    */
    switch ($mese)
    {
      case 1: $minima = $PARAMETERS['date']['base_temperature']+0; break; 
      case 2: $minima = $PARAMETERS['date']['base_temperature']+4; break; 
      case 3: $minima = $PARAMETERS['date']['base_temperature']+8; break;
      case 4: $minima = $PARAMETERS['date']['base_temperature']+14; break;
      case 5: $minima = $PARAMETERS['date']['base_temperature']+20; break; 
      case 6: $minima = $PARAMETERS['date']['base_temperature']+28; break;
      case 7: $minima = $PARAMETERS['date']['base_temperature']+30; break; 
      case 8: $minima = $PARAMETERS['date']['base_temperature']+28; break;  
      case 9: $minima = $PARAMETERS['date']['base_temperature']+20; break;
      case 10: $minima = $PARAMETERS['date']['base_temperature']+14; break; 
      case 11: $minima = $PARAMETERS['date']['base_temperature']+8; break;  
      case 12: $minima = $PARAMETERS['date']['base_temperature']+0; break;
    }
    
    /**	* Fine fix */
    
    if($ore<14){$gradi=$minima+(floor($ore/3)*$caso);}
    else{ $gradi=$minima+(4*$caso)-((floor($ore/3)*$caso))+(3*$caso);}
    
    
    
    $caso=($giorno+($ora/4))%12;
    switch ($caso)
    {
        case 0: $meteo_cond=$MESSAGE['interface']['meteo']['status'][0]; break; 
        case 1: $meteo_cond=$MESSAGE['interface']['meteo']['status'][0]; break;
        case 2: $meteo_cond=$MESSAGE['interface']['meteo']['status'][1]; break; 
        case 3: $meteo_cond=$MESSAGE['interface']['meteo']['status'][2]; break; 
        case 4: if($minima<4){$meteo_cond=$MESSAGE['interface']['meteo']['status'][4];} else {$meteo_cond=$MESSAGE['interface']['meteo']['status'][3];} break; 
        case 5: $meteo_cond=$MESSAGE['interface']['meteo']['status'][1]; break; 
        case 6: $meteo_cond=$MESSAGE['interface']['meteo']['status'][0]; break; 
        case 7: $meteo_cond=$MESSAGE['interface']['meteo']['status'][1]; break;
        case 8: if($minima<4){$meteo_cond=$MESSAGE['interface']['meteo']['status'][4];} else {$meteo_cond=$MESSAGE['interface']['meteo']['status'][3];} break; 
        case 9: $meteo_cond=$MESSAGE['interface']['meteo']['status'][2]; break; 
      case 10: $meteo_cond=$MESSAGE['interface']['meteo']['status'][0]; break; 
        case 11: $meteo_cond=$MESSAGE['interface']['meteo']['status'][0]; break;
    }
    
    $meteo=$meteo_cond." ". $gradi."&deg;C "; //.Tempo();
    
    //
    if ($_SESSION['mappa'] == 1)
      $meteo = ' ';
  }
  else 
  { 
    $meteo=gdrcd_filter('out',$record['meteo']); 
  }
}

?>

<?php if(empty($meteo)===FALSE){ ?>
<div class="page_title">
 <h2><?php echo gdrcd_filter('out',$MESSAGE['interface']['meteo']['title']);?></h2>
</div>
<div class="meteo_date">
   <?php echo  strftime('%d').'/'.strftime('%m').'/'.((strftime('%Y')-$PARAMETERS['date']['baseyear'])+$PARAMETERS['date']['offset']).' ABY';?>
</div>
<div class="meteo">
<?php echo $meteo;?>
</div>
<?php } ?>

<?php } else {echo '<div class="error">'.gdrcd_filter('out',$MESSAGE['error']['location_doesnt_exist']).'</div>';} ?>

</div><!-- page_body -->

</div><!-- Pagina -->