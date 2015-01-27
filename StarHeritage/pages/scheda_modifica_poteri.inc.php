<div class="pagina_scheda">
<div id="descriptionLoc"></div>

<?php /*HELP: E' possibile modificare la scheda agendo su scheda.css nel tema scelto, oppure sostituendo il codice che segue la voce "Scheda del personaggio"*/ ?>

<?php 
/********* CARICAMENTO PERSONAGGIO ***********/
//Se non e' stato specificato il nome del pg
if (isset($_REQUEST['pg'])===FALSE)
{
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
    //
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
				 <input type="submit" value="<?php echo gdrcd_filter('out',$MESSAGE['interface']['forms']['submit']); ?>" />
				</div>
			  </form>
			  </div></div>
           <?php } 
		} 
		else 
		{
      // Variabili di gestione
      $px_totali_pg = $record['esperienza']; // PX totali
      //
      //carico le sole abilità del pg - Calcolo PX spesi
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
      // Controllo il livello di potere massimo della gilda di appartenenza
      $guilds=gdrcd_query("SELECT ruolo.nome_ruolo, ruolo.gilda, ruolo.immagine, gilda.visibile, gilda.nome AS nome_gilda, gilda.Anonimo, gilda.force_user FROM clgpersonaggioruolo LEFT JOIN ruolo ON ruolo.id_ruolo = clgpersonaggioruolo.id_ruolo LEFT JOIN gilda ON ruolo.gilda = gilda.id_gilda WHERE clgpersonaggioruolo.personaggio = '".gdrcd_filter('in',$record['nome'])."'", 'result');
      $force_level = 0;
      //
      while ($row_guilds = gdrcd_query($guilds, 'fetch'))
      {
        if ($row_guilds['force_user'] > $force_level)
        {  
          $force_level = $row_guilds['force_user'];
          switch ($row_guilds['nome_ruolo'])
          {
            case 'Padawan' : $force_level=1; break;
            case 'Dread Warrior' : $force_level=1; break;
            case 'Cavaliere' : $force_level=2; break;
            case 'Dread Assassin' : $force_level=2; break;
            case 'Dread Master' : $force_level=2; break;
            case 'Maestro' : $force_level=3; break;
          }
        }   
      }
      gdrcd_query($guilds, 'free');
      //
      if((gdrcd_filter('get',$_REQUEST['op'])=='conf') && (($_SESSION['login']==gdrcd_filter('out',$_REQUEST['pg']))||($_SESSION['permessi']>=MODERATOR)))
      {
        // Gestisco operazione
        $px_disp = $px_totali_pg - $px_spesi;
        $px_usati = 0;
        //
        // Carico l'elenco dei poteri, in Join con il livello
        $result=gdrcd_query("SELECT P.id_potere, P.nome_potere, P.id_ab, P.descrizione, C.livello from poteri P left join clgpersonaggiopoteri C on P.id_potere=C.id_pot and C.pgname='".gdrcd_filter('in',$_REQUEST['pg'])."' where P.nascosto=0 order by P.id_ab", 'result');
        while($row=gdrcd_query($result, 'fetch'))
        {
          // echo $row[nome_potere].' '.$row['livello'];
          $lv = $row['livello'];
          if ($lv == null)
            $lv = 0;
          //
          // Bene, cerchiamo di capire quanto ci ha messo l'utente
          $newVal = gdrcd_filter('out',$_REQUEST['pow'.$row['id_potere']]);
          // DEBUG echo 'valore potere '.$row['nome_potere'].' '.$newVal;
          //
          // Lo saltiamo, ha cercato di fregarci
          if ($newVal>$force_level)
            continue;
          if ($newVal > $lv)
          {
            // vediamo se aveva i px, se non li aveva passiamo avanti
            if ($px_disp < ($newVal-$lv)*10)
              continue;
            //
            // Bene, aveva i PX.. allora inserisco/aggiorno il potere al nuovo valore e decremento i PX disponibili
            $px_usati = $px_usati +($newVal-$lv)*10;
            $px_disp = $px_disp - ($newVal-$lv)*10;
            //
            if ($lv == 0)
              gdrcd_query("INSERT INTO clgpersonaggiopoteri (id_pot, pgname, livello) VALUES (".$row['id_potere'].", '".gdrcd_filter('in',$_REQUEST['pg'])."', ".$newVal.")");
            else
              gdrcd_query("UPDATE clgpersonaggiopoteri SET livello = ".$newVal." WHERE id_pot = ".$row['id_potere']." AND pgname = '".gdrcd_filter('in',$_REQUEST['pg'])."'");
          }
          //
          // Non ho più punti disponibili.. mi fermo comunque
          if ($px_disp == 0)
            break;
        }
        gdrcd_query($result, 'free');
        //
        if ($px_usati > 0)
        {
          // decremento i px su db
          $px_totali_pg = $px_totali_pg - $px_usati;
          gdrcd_query("UPDATE personaggio SET esperienza = ".$px_totali_pg." WHERE nome = '".gdrcd_filter('in',$_REQUEST['pg'])."'");
        }
        //
        echo '<div style="color:red; text-decoration: underline;">MODIFICHE CONFERMATE</div>';
      }
      if ((isset($_REQUEST['op'])===FALSE || 1==1) && $force_level > 0)
      {
        ?>
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
        
        function resetRank(idPow)
        {
          var orig = document.getElementById('origlv'+idPow);
          var orgVal = parseInt(orig.value);
          //
          var pxo = document.getElementById('avail');
          var px = parseInt(pxo.innerHTML);
          //
          // scopriamo a che livello siamo
          var radiolv0 = document.getElementById('pw'+idPow);
          var lv = parseInt(radiolv0.value);
          //
          var inc = lv - orgVal;
          if (inc>0)
          {
            var spanlv0 = document.getElementById('pwval'+idPow+'lv0');
            var spanlv1 = document.getElementById('pwval'+idPow+'lv1');
            var spanlv2 = document.getElementById('pwval'+idPow+'lv2');
            var spanlv3 = document.getElementById('pwval'+idPow+'lv3');
            spanlv0.style.fontWeight = "";
            spanlv0.style.color = "";
            spanlv1.style.fontWeight = "";
            spanlv1.style.color = "";
            spanlv2.style.fontWeight = "";
            spanlv2.style.color = "";
            spanlv3.style.fontWeight = "";
            spanlv3.style.color = "";
            //
            px = px + (inc * 10);
            radiolv0.value = orgVal;
            if (orgVal == 0)
            {
              spanlv0.style.fontWeight = "bold";
              spanlv0.style.color = "red";
            }
            if (orgVal == 1)
            {
              spanlv1.style.fontWeight = "bold";
              spanlv1.style.color = "red";
            }
            if (orgVal == 2)
            {
              spanlv2.style.fontWeight = "bold";
              spanlv2.style.color = "red";
            }
            if (orgVal == 3)
            {
              spanlv3.style.fontWeight = "bold";
              spanlv3.style.color = "red";
            }
            //
            pxo.innerHTML = px;
          }
        }
        
        function addRank(idPow)
        {
          var pxo = document.getElementById('avail');
          var px = parseInt(pxo.innerHTML);
          if (px<10)
            return;
          //
          var max_lvo = document.getElementById('maxlv');
          var max_lv = parseInt(max_lvo.value);
          //
          // per prima cosa scopriamo a che livello siamo
          var radiolv0 = document.getElementById('pw'+idPow);
          var lv = parseInt(radiolv0.value);
          //
          if (lv == 3 || lv == max_lv)
            return;
          //
          var spanlv0 = document.getElementById('pwval'+idPow+'lv0');
          var spanlv1 = document.getElementById('pwval'+idPow+'lv1');
          var spanlv2 = document.getElementById('pwval'+idPow+'lv2');
          var spanlv3 = document.getElementById('pwval'+idPow+'lv3');
          spanlv0.style.fontWeight = "";
          spanlv0.style.color = "";
          spanlv1.style.fontWeight = "";
          spanlv1.style.color = "";
          spanlv2.style.fontWeight = "";
          spanlv2.style.color = "";
          spanlv3.style.fontWeight = "";
          spanlv3.style.color = "";
          //
          lv++;
          px = px - 10;
          radiolv0.value = lv;
          if (lv == 1)
          {
            spanlv1.style.fontWeight = "bold";
            spanlv1.style.color = "red";
          }
          if (lv == 2)
          {
            spanlv2.style.fontWeight = "bold";
            spanlv2.style.color = "red";
          }
          if (lv == 3)
          {
            spanlv3.style.fontWeight = "bold";
            spanlv3.style.color = "red";
          }
          //
          pxo.innerHTML = px;
        }
        </script>
        <div class="form_info"><?php echo gdrcd_filter('out',$MESSAGE['interface']['sheet']['avalaible_xp']).': <span  id="avail" >'.($px_totali_pg-$px_spesi).'</span>';?></div>
        <span>MAX LEVEL : <?php echo $force_level?> </span>
        <form action="main.php?page=scheda_modifica_poteri&pg=<?php echo gdrcd_filter('get',$_REQUEST['pg']) ?>&op=conf" method="post">
        <input type="hidden" id="maxlv" name="maxlv" value="<?php echo $force_level?>" />
        <table>
        <tr>
        <td>Potere</td>
        <td style="width: 100px;" >Abilità</td>
        <td style="width: 60px;" ></td>
        <td></td>
        <td></td>
        </tr>
        <?php
        // Carico l'elenco dei poteri, in Join con il livello
        $result=gdrcd_query("SELECT P.id_potere, P.nome_potere, P.id_ab, P.descrizione, C.livello from poteri P left join clgpersonaggiopoteri C on P.id_potere=C.id_pot and C.pgname='".gdrcd_filter('in',$_REQUEST['pg'])."' where P.nascosto=0 order by P.id_ab,P.nome_potere", 'result');
        while ($row_pow = gdrcd_query($result, 'fetch'))
        {
          $abt = "";
          switch (gdrcd_filter('num',$row_pow['id_ab']))
          {
            case 40 : $abt = '(Alterazione)'; break;
            case 39 : $abt = '(Senso)'; break;
            case 38 : $abt = '(Controllo)'; break;
            case 37 : $abt = '(Meditazione)'; break;
          }
          $lv = $row_pow['livello'];
          if ($lv == null)
            $lv = 0;
          ?>
          <tr>
          <td><span><?php echo gdrcd_filter('out',$row_pow['nome_potere']); ?></span></td>
          <td><span><?php echo gdrcd_filter('out',$abt); ?></span></td>
          <td>
            <input type="hidden" id="origlv<?php echo $row_pow['id_potere']?>" name="origlv<?php echo $row_pow['id_potere']?>" value="<?php echo $lv?>" />
            <input type='hidden' id='pw<?php echo $row_pow['id_potere']?>' name='pow<?php echo $row_pow['id_potere']?>' value='<?php echo $lv; ?>' /> 
            <span id='pwval<?php echo $row_pow['id_potere']?>lv0' style='<?php echo ($lv==0 ? 'font-weight:bold; color:red;' : ''); ?>'>0</span>
            <span id='pwval<?php echo $row_pow['id_potere']?>lv1' style='<?php echo ($lv==1 ? 'font-weight:bold; color:red;' : ''); ?>'>1</span>
            <span id='pwval<?php echo $row_pow['id_potere']?>lv2' style='<?php echo ($lv==2 ? 'font-weight:bold; color:red;' : ''); ?>'>2</span>
            <span id='pwval<?php echo $row_pow['id_potere']?>lv3' style='<?php echo ($lv==3 ? 'font-weight:bold; color:red;' : ''); ?>'>3</span>
          </td>
          <td><div><span id="add<?php echo $row_pow['id_potere']?>" style="border:1px solid gray; padding:2px; cursor:pointer; margin-bottom:2px;" onmouseover="hover('add<?php echo $row_pow['id_potere']?>');" onmouseout="out('add<?php echo $row_pow['id_potere']?>');" onclick="addRank('<?php echo $row_pow['id_potere']?>');" >Level UP</span></div></td>
          <td><div><span id = "reset<?php echo $row_pow['id_potere']?>" style="border:1px solid gray; padding:2px; cursor:pointer; margin-bottom:2px;" onmouseover="hover('reset<?php echo $row_pow['id_potere']?>');" onmouseout="out('reset<?php echo $row_pow['id_potere']?>');" onclick="resetRank('<?php echo $row_pow['id_potere']?>');" >Reset</span></div></td>
          <?php
        }
        gdrcd_query($result, 'free');
        ?>
        </table>
        <input type="submit" value="Conferma" />
        </form>
        <!-- Link a piè di pagina -->
        <div class="link_back">
           <a href="main.php?page=scheda&pg=<?php echo gdrcd_filter('get',$_REQUEST['pg']); ?>"><?php echo gdrcd_filter('out',$MESSAGE['interface']['sheet']['link']['back']); ?></a>
        </div>
        <?php
      }
    }
  }
}    
  ?>
  </div><!-- Pagina -->