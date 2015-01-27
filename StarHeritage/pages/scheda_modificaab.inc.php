<div class="pagina_scheda">
<div id="descriptionLoc"></div>

<?php /*HELP: E' possibile modificare la scheda agendo su scheda.css nel tema scelto, oppure sostituendo il codice che segue la voce "Scheda del personaggio"*/ ?>

<?php 
/********* CARICAMENTO PERSONAGGIO ***********/
//Se non e' stato specificato il nome del pg
if (isset($_REQUEST['pg'])===FALSE){
    echo '<div class="error">'.gdrcd_filter('out',$MESSAGE['error']['unknown_character_sheet']).'</div>';
} 
else 
{
	$query = "SELECT personaggio.*, razza.sing_m, razza.sing_f, razza.id_razza, razza.bonus_car0, razza.bonus_car1, razza.bonus_car2, razza.bonus_car3, razza.bonus_car4, razza.bonus_car5 FROM personaggio LEFT JOIN razza ON personaggio.id_razza=razza.id_razza WHERE personaggio.nome = '".gdrcd_filter('in',$_REQUEST['pg'])."'";
	$result = gdrcd_query($query, 'result');
	//Se non esiste il pg
	if (gdrcd_query($result, 'num_rows')==0)
	{
	  echo '<div class="error">'.gdrcd_filter('out',$MESSAGE['error']['unknown_character_sheet']).'</div>';
	}
	else 
	{
		$record = gdrcd_query($result, 'fetch');
		gdrcd_query($result, 'free');

		
    /*Controllo esilio, se esiliato non visualizzo la scheda*/
		if($record['esilio']>strftime('%Y-%m-%d'))
		{
           echo '<div class="warning">'.gdrcd_filter('out',$record['nome']).' '.gdrcd_filter('out',$record['cognome']).' '.gdrcd_filter('out',$MESSAGE['warning']['character_exiled']).' '.gdrcd_format_date($record['esilio']).' ('.$record['motivo_esilio'].' - '.$record['autore_esilio'].')</div>';
           if ($_SESSION['permessi']>=GAMEMASTER){?>
              <div class="panels_box"><div class="form_gioco">
              <form action="main.php?page=scheda_modifica&pg=<?php echo gdrcd_filter('get',$_REQUEST['pg']) ?>" method="post">
			      <input type="hidden" value="<?php echo strftime('%Y'); ?>" name="year" />
			      <input type="hidden" value="<?php echo strftime('%m'); ?>" name="month" />
			      <input type="hidden" value="<?php echo strftime('%d'); ?>" name="day" />
				  <input type="hidden" value="<?php gdrcd_filter('out',$MESSAGE['interface']['sheet']['modify_form']['unexile']); ?>" name="causale" />
			      <input type="hidden" value="exile" name="op" />
				  <div class="form_label">
				      <?php echo gdrcd_filter('out',$MESSAGE['interface']['sheet']['modify_form']['unexile']); ?>
				  </div>
				  <div class="form_submit">
				      <input type="submit" 
					         value="<?php echo gdrcd_filter('out',$MESSAGE['interface']['forms']['submit']); ?>" />
				  </div>
			  </form>
			  </div></div>
           <?php } 
		} 
		else 
		{
      // Variabili di gestione
      $px_totali_pg=$record['esperienza']; // PX totali
      //
      //carico le sole abilità del pg
      $result=gdrcd_query("SELECT id_abilita, grado FROM clgpersonaggioabilita WHERE nome='".gdrcd_filter('in',$_REQUEST['pg'])."'", 'result');
      $px_spesi=0;
      while ($row=gdrcd_query($result, 'fetch'))
      {
        $px_abi = gdrcd_rankCost($row['grado']);
        $px_spesi+=$px_abi;
        $ranks[$row['id_abilita']]=$row['grado'];
      }
		  gdrcd_query($result, 'free');
      //
      // Conferma dell'invio, applico le modifiche (chiaramente facendo i dovuti controlli)
      if((gdrcd_filter('get',$_REQUEST['op'])=='conf') && (($_SESSION['login']==gdrcd_filter('out',$_REQUEST['pg']))||($_SESSION['permessi']>=MODERATOR)))
      {
        $px_disp = $px_totali_pg - $px_spesi;
        //
        //carico l'elenco di tutte le abilità
        $result=gdrcd_query("SELECT DISTINCT nome, car, descrizione, id_abilita, ifnull(id_ab, 0) as limited FROM abilita left join clgabilitagilda on abilita.id_abilita=clgabilitagilda.id_ab WHERE id_razza=-1 OR id_razza= ".$record['id_razza']." ORDER BY id_razza DESC, nome", 'result');
        while($row=gdrcd_query($result, 'fetch'))
        {
          $ok = true;
          // 
          // Se l'abilità è limitata ignoro qualunque cosa abbia fatto l'utente..
          if ($row['limited'] != 0 && $_SESSION['permessi']<MODERATOR)
          {
            // Cerco di capire se l'utente aveva il permesso per modificarla
            $resultAB=gdrcd_query("SELECT clgpersonaggioruolo.personaggio, ruolo.gilda, clgabilitagilda.id_ab FROM clgpersonaggioruolo  join ruolo on clgpersonaggioruolo.id_ruolo=ruolo.id_ruolo join clgabilitagilda on ruolo.gilda=clgabilitagilda.id_gil where personaggio='".gdrcd_filter('out',$_REQUEST['pg'])."' and id_ab=".$row['id_abilita'], 'result');
            //
            // L'utente non aveva il permesso (non fa parte di una corporazione per cui l'abilità è sbloccata)
            if (gdrcd_query($resultAB, 'num_rows') == 0)
             $ok = false;
            gdrcd_query($resultAB, 'free');
          }
          if ($ok == true)
          {
            // Bene, cerchiamo di capire quanto ci ha messo l'utente
            $newVal = gdrcd_filter('out',$_REQUEST['newRankVal'.$row['id_abilita']]);           
            if ($newVal>0 && $newVal>$ranks[$row['id_abilita']])
            {
              // L'utente ha incrementato il valore, vediamo di quanto
              $inc = $newVal - $ranks[$row['id_abilita']];
              $incremented_val = $ranks[$row['id_abilita']];
              $totalCost = 0;
              for($i=0; $i<$inc; $i++)
              { 
                $newRank = $incremented_val + 1;
                //
                // Quanto costa il nuovo grado?
                $cost_new_rank = gdrcd_singleRankCost($newRank);
                //
                if ($cost_new_rank<=$px_disp)
                {
                  // Posso comprarlo
                  $incremented_val = $newRank;
                  $px_disp = $px_disp - $cost_new_rank;  // Decremento i px disponibili
                  $totalCost = $totalCost + $cost_new_rank;
                }
                else
                {
                  // Non posso comprarlo, esco dal ciclo
                  break;
                }
              }
              //
              // Applico le modifiche
              if ($incremented_val>0 && $incremented_val>$ranks[$row['id_abilita']] && $totalCost>0)
              {
                $queryAbIns = "";
                //
                if ($incremented_val==1 || $ranks[$row['id_abilita']]==0)
                {
                  // Per il primo grado o se partiamo da 0 dobbiamo fare un insert (se necessario..)
                  $queryAbIns = "SELECT grado FROM clgpersonaggioabilita WHERE nome = '".gdrcd_filter('in',$_REQUEST['pg'])."' and id_abilita=".$row['id_abilita'];
                  $resCheck = gdrcd_query($queryAbIns, 'result');
                  if (gdrcd_query($resCheck, 'num_rows')==0)
                    $queryAbIns="INSERT INTO clgpersonaggioabilita (id_abilita, nome, grado) VALUES (".$row['id_abilita'].", '".gdrcd_filter('in',$_REQUEST['pg'])."', ".$incremented_val.")";
                  else
                    $queryAbIns="UPDATE clgpersonaggioabilita SET grado = ".$incremented_val." WHERE id_abilita = ".$row['id_abilita']." AND nome = '".gdrcd_filter('in',$_REQUEST['pg'])."'";
                  //
                  gdrcd_query($resCheck, 'free');
                }
                else
                {
                  // Qui dobbiamo fare un update
                  $queryAbIns="UPDATE clgpersonaggioabilita SET grado = ".$incremented_val." WHERE id_abilita = ".$row['id_abilita']." AND nome = '".gdrcd_filter('in',$_REQUEST['pg'])."'";
                }
                //
                // Aggiorno l'abilità sul DB
                gdrcd_query($queryAbIns);
                //
                // Aggiorno le variabili dell'abilità e dei punti spesi dal PG
                $ranks[$row['id_abilita']] = $incremented_val;
                $px_spesi = $px_spesi + $totalCost;
              }
            }
          }
          //
          // Non ho più punti disponibili.. mi fermo comunque
          if ($px_disp == 0)
            break;
        }
        gdrcd_query($result, 'free');
        //
        // A questo punto il DB è stato aggiornato così anche le variabili dei gradi attuali e dei px disponibili.. ristampo la videata.. però mostrando almeno una conferma..
        echo '<div style="color:red; text-decoration: underline;">MODIFICHE CONFERMATE</div>';
      }
      //
      // Gestione stampa scheda delle abilita'
      if (isset($_REQUEST['op'])===FALSE || 1==1)
      {	
      ?>
      <div class="elenco_abilita"><!-- Elenco abilità -->

      <div class="titolo_box">
         <?php echo gdrcd_filter('out',$MESSAGE['interface']['sheet']['box_title']['skills']); ?>
      </div>
      <?php 
      //conteggio le abilità
      $row=gdrcd_query("SELECT COUNT(*) FROM abilita WHERE id_razza=-1 OR id_razza= ".$record['id_razza']."");
      $num=$row['COUNT(*)'];

      //carico l'elenco delle abilità
      $result=gdrcd_query("SELECT DISTINCT nome, car, descrizione, id_abilita, ifnull(id_ab, 0) as limited FROM abilita left join clgabilitagilda on abilita.id_abilita=clgabilitagilda.id_ab WHERE id_razza=-1 OR id_razza= ".$record['id_razza']." ORDER BY id_razza DESC, nome", 'result');
      $count=0;
      $total=0;?>
      <script>
      function hover(id) 
      {
        var d = document.getElementById(id);
        if (d)
          d.style.opacity = 0.6;
      }
      function out(id) 
      {
        var d = document.getElementById(id);
        if (d)
          d.style.opacity = 1;
      }
      function rankCost(val)
      {
        if (val <= 20)
         return 1;
        else if (val <= 36)
         return 1;
        else if (val <= 48)
         return 1;
        else if (val <= 56)
         return 2;
        else if (val <= 62)
         return 3;
        else if (val <= 67)
         return 4;
        else if (val <= 100)
         return 5;
        //
        return 1;
      }
      
      function resetRank(idAb)
      {
        var orig = document.getElementById('origRank'+idAb);
        var actVid = document.getElementById('newRank'+idAb);
        var act = document.getElementById('newRankVal'+idAb);
        var pxo = document.getElementById('avail');
        //
        var orgVal = parseInt(orig.innerHTML);
        var newVal = parseInt(act.value);
        var px = parseInt(pxo.innerHTML);
        //
        if (newVal>orgVal)
        {
          var cost = 0;
          for (var i=newVal; i>orgVal; i--)
            cost += rankCost(i);
          px = px + cost;
          //
          actVid.innerHTML = orgVal;
          act.value = orgVal;
          pxo.innerHTML = px;
        }
        else
        {
          actVid.innerHTML = orgVal;
          act.value = orgVal;
        }
      }
      
      function addRank(idAb, toInc)
      {
        var actVid = document.getElementById('newRank'+idAb);
        var act = document.getElementById('newRankVal'+idAb);
        var pxo = document.getElementById('avail');
        //
        var newVal = parseInt(act.value);
        var px = parseInt(pxo.innerHTML);
        //
        if (px<=0)
          return;
        //
        for (var i=0; i<toInc; i++)
        {
         var newR = newVal + 1;
         var cost = rankCost(newR);
         //
         if (cost <= px)
         {
           px = px - cost;
           newVal = newVal + 1;
         }
         else
         { 
           break;
         }
        }
        //
        actVid.innerHTML = newVal;
        act.value = newVal;
        pxo.innerHTML = px;
      }
      </script>
      <div class="form_info"><?php echo gdrcd_filter('out',$MESSAGE['interface']['sheet']['avalaible_xp']).': <span  id="avail" >'.($px_totali_pg-$px_spesi).'</span>';?></div>
      <form action="main.php?page=scheda_modificaab&pg=<?php echo gdrcd_filter('get',$_REQUEST['pg']) ?>&op=conf" method="post">
      <table>
      <tr>
      <td><div class="abilita_scheda_nome">Abilità</div></td>
      <td><div>Valore Originale</div></td>
      <td><div>Valore Attuale</div></td>
      <td><div></div></td>
      <td><div></div></td>
      <td><div></div></td>
      <td><div></div></td>
      </tr>
      <?php while($row=gdrcd_query($result, 'fetch')){
       $ok = true;
       if ($row['limited'] != 0 && $_SESSION['permessi']<MODERATOR)
       {
         $resultAB=gdrcd_query("SELECT clgpersonaggioruolo.personaggio, ruolo.gilda, clgabilitagilda.id_ab FROM clgpersonaggioruolo  join ruolo on clgpersonaggioruolo.id_ruolo=ruolo.id_ruolo join clgabilitagilda on ruolo.gilda=clgabilitagilda.id_gil where personaggio='".gdrcd_filter('out',$_REQUEST['pg'])."' and id_ab=".$row['id_abilita'], 'result');
         if (gdrcd_query($resultAB, 'num_rows') == 0)
          $ok = false;
         gdrcd_query($resultAB, 'free');
       }
       if ($ok==true)
       {
       ?>
       <tr>
       <td><div class="abilita_scheda_nome" onmouseover="show_desc(event,'<?php 
         $desc = trim(nl2br(gdrcd_filter('in',$row['descrizione'])));
         $desc = strtr($desc, array("\n\r" => '', "\n" => '', "\r" => '', '"' => '&quot;', 'E\'' => '&egrave;', '\'' => '&apos;'));
         echo $desc;
       ?>');" onmouseout="hide_desc();">
       <?php echo gdrcd_filter('out',$row['nome']); ?></div></td>
       <td><div><span  id="origRank<?php echo $row['id_abilita']?>" ><?php echo 0+gdrcd_filter('out',$ranks[$row['id_abilita']]); ?></span></div></td>
       <td><div ><span id="newRank<?php echo $row['id_abilita']?>" ><?php echo 0+gdrcd_filter('out',$ranks[$row['id_abilita']]); ?></span></div></td>
       <td><div><input type="hidden" id="newRankVal<?php echo $row['id_abilita']?>" name="newRankVal<?php echo $row['id_abilita']?>" value="<?php echo 0+gdrcd_filter('out',$ranks[$row['id_abilita']]); ?>"/></div></td>
       <td><div><span id = "reset<?php echo $row['id_abilita']?>" style="border:1px solid gray; padding:2px; cursor:pointer; margin-bottom:2px;" onmouseover="hover('reset<?php echo $row['id_abilita']?>');" onmouseout="out('reset<?php echo $row['id_abilita']?>');" onclick="resetRank('<?php echo $row['id_abilita']?>');" >Reset</span></div></td>
       <td><div class="abilita_scheda_tank"><span id="add1<?php echo $row['id_abilita']?>" style="border:1px solid gray; padding:2px; cursor:pointer; margin-bottom:2px;" onmouseover="hover('add1<?php echo $row['id_abilita']?>');" onmouseout="out('add1<?php echo $row['id_abilita']?>');" onclick="addRank('<?php echo $row['id_abilita']?>',1);" >+1</span></div></td>
       <td><div class="abilita_scheda_tank"><span id="add5<?php echo $row['id_abilita']?>" style="border:1px solid gray; padding:2px; cursor:pointer; margin-bottom:2px;" onmouseover="hover('add5<?php echo $row['id_abilita']?>');" onmouseout="out('add5<?php echo $row['id_abilita']?>');" onclick="addRank('<?php echo $row['id_abilita']?>',5);" >+5</span></div></td>
       <td><div class="abilita_scheda_tank"><span id="add10<?php echo $row['id_abilita']?>" style="border:1px solid gray; padding:2px; cursor:pointer; margin-bottom:2px;" onmouseover="hover('add10<?php echo $row['id_abilita']?>');" onmouseout="out('add10<?php echo $row['id_abilita']?>');" onclick="addRank('<?php echo $row['id_abilita']?>', 10);" >+10</span></div></td>
       </tr>
       <?php 
       } // End gestione abilita' limitate 
       } // Eng gestione abilita' ?>
       </table>
       <input type="submit" value="Conferma" />
       </form>
      <div class="form_info"><?php echo gdrcd_filter('out',$MESSAGE['interface']['sheet']['info_skill_cost']);?></div>
      </div><!-- Fine Elenco abilità -->
        <!-- Link a piè di pagina -->
        <div class="link_back">
           <a href="main.php?page=scheda&pg=<?php echo gdrcd_filter('get',$_REQUEST['pg']); ?>"><?php echo gdrcd_filter('out',$MESSAGE['interface']['sheet']['link']['back']); ?></a>
        </div>
      <?php }//if?>
    <?php }//else?>
  <?php }//else?>
<?php }//else?>
</div><!-- Pagina -->