<?php
  	/***
  	* Begin patch by eLDiabolo ed Eriannen
  	*	01/09/2012
	*   	
	* Effettuato modifiche per adattamento a gdrcd 5.2 by Eriannen
	*   20/08/2013
  	* modificato div class per la pagina per renderla visibile
  	* e aggiunto title page con messaggio vocabolario
  	**/
  	?>
<div class="pagina_news_info">
  <marquee behavior="scroll" direction="up" scrollamount="1" scrolldelay="1" onmouseover="this.stop()" onmouseout="this.start()" style="height:100%;width:100%" height="100%" width="100%">
<?php /*HELP: */
	 /***
  	* End patch by eLDiabolo ed Eriannen
  	**/


  	    $query="SELECT data, titolo, testo, tipo FROM news ORDER BY tipo ASC, data";
	    $result=gdrcd_query($query, 'result');
      $newsgr = -1; ?>
	    <?php 
	    while($row=gdrcd_query($result, 'fetch'))
	    { 
	    ?>
        <?php
        if ($newsgr != gdrcd_filter('num',$row['tipo']))
        {
        $newsgr = $row['tipo'];
        ?>
        </div></div>
        <div class="page_title">
         <h2><?php echo ($row['tipo']==1 ? "News Off":" News"); ?></h2>
        </div>
        <div class="panels_box">
        <div class="elenco_record_gioco">
        <?php
        }
        ?>
        <div class="panels_box_news">
        	<?php
					/**
					*	Patch by eLDiabolo ed Eriannen
					* 01/09/2012
					* se non si vuol utilizzare nessuna icona accanto ad ogni titolo di news per questo box
					* sostituire la riga sottostante

					<img src="../imgs/icons/news2.gif">

					con la la seguente:

					<!--img src="../imgs/icons/news2.gif"-->

					e vice versa per renderla nuovamente visibile una volta creata l'icona e posizionata secondo istruzioni.
					***/
        	?>
		<!-- <img src="imgs/icons/news2.gif"> -->

		<?php 
    if ($row['tipo']!=1)
      echo '<span style="color:#97e0fb;">';
    echo gdrcd_bbcoder(gdrcd_filter('out',$row['testo'])); 
    if ($row['tipo']!=1)
      echo '</span>';
    ?>
        </div>
        <?php 
        }//while 
        ?>
		</div><!--elenco_record_gioco-->
        </div><!--panels_box-->
</marquee>
</div>


