<?php /* HELP: Frame della chat */
/* Tipi messaggio: (A azione, P parlato, N PNG, M Master, I Immagine, S sussurro, D dado, C skill check, O uso oggetto) */


/*Seleziono le info sulla chat corrente*/
$info = gdrcd_query("SELECT nome, stanza_apparente, invitati, privata, proprietario, scadenza FROM mappa WHERE id=".$_SESSION['luogo']." LIMIT 1");

?>

<div class="pagina_frame_chat">

<div class="page_title"><h2><?php echo $info['nome']; ?></h2></div>

<div class="page_body"> 

<?php 
//e' una stanza privata?
if ($info['privata']==1) 
{
	$allowance=FALSE;

    if ( (($info['proprietario']==gdrcd_capital_letter($_SESSION['login'])) || (strpos($_SESSION['gilda'], $info['proprietario'])!=FALSE) || (strpos($info['invitati'], gdrcd_capital_letter($_SESSION['login']))!=FALSE) ||
	   (($PARAMETERS['mode']['spyprivaterooms']=='ON')&&($_SESSION['permessi']>MODERATOR))) && ($info['scadenza']>strftime('%Y-%m-%d %H:%M:%S')) ) {$allowance=TRUE;}


} else {$allowance=TRUE;}
//se e' privata e l'utente non ha titolo di leggerla
if ($allowance === FALSE) {
	echo '<div class="warning">'.$MESSAGE['chat']['whisper']['privat'].'</div>';

//echo $info['invitati']; echo gdrcd_capital_letter($_SESSION['login']);
} else {

?>

<?php $_SESSION['last_message']=0; ?>
<div style="height: 1; width: 1;">
<iframe src ="pages/chat.inc.php?ref=30&chat=yes" class="iframe_chat" id="chat_frame" name="chat_frame" frameborder="0" allowtransparency="true">
</iframe>
</div>


<div id='pagina_chat' class="chat_box">

</div>

<div class="panels_box"><div class="form_chat">

<!-- Form messaggi -->
<div class="form_row">
 <form action="pages/chat.inc.php?ref=10&chat=yes" method="post" target="chat_frame" id="chat_form_messages">
  
	<div class="casella_chat">
    <select name="type"  id="type" style="width:100px; height:22px;">
<!--        <option value="0"><?php echo gdrcd_filter('out',$MESSAGE['chat']['type'][0]);//parlato ?></option> -->
       <option value="1"><?php echo gdrcd_filter('out',$MESSAGE['chat']['type'][1]);//azione ?></option> 
	   <option value="4"><?php echo gdrcd_filter('out',$MESSAGE['chat']['type'][4]);//sussurro ?></option>
	   <?php if($_SESSION['permessi']>=GAMEMASTER){ ?>
	   <option value="2"><?php echo gdrcd_filter('out',$MESSAGE['chat']['type'][2]);//master ?></option>  
	   <option value="3"><?php echo gdrcd_filter('out',$MESSAGE['chat']['type'][3]);//png ?></option>
	   <?php } ?>
	   <?php if(($info['privata']==1)&&(($info['proprietario']==$_SESSION['login'])||((is_numeric($info['proprietario'])===TRUE)&&(strpos($_SESSION['gilda'], ''.$info['proprietario']))))){ ?>
       <option value="5"><?php echo gdrcd_filter('out',$MESSAGE['chat']['type'][5]);//invita ?></option>
	   <option value="6"><?php echo gdrcd_filter('out',$MESSAGE['chat']['type'][6]);//caccia ?></option>
	   <option value="7"><?php echo gdrcd_filter('out',$MESSAGE['chat']['type'][7]);//elenco ?></option>
	   <?php }//if ?>
    </select>
	<br/><span class="casella_info"><?php echo gdrcd_filter('out',$MESSAGE['chat']['type']['info']);?></span>
	</div>
	
	<div class="casella_chat" style="padding-left:4px;">
	<input name="tag" id="tag" value="" style="margin-top:0px;" />
    <br/><span class="casella_info">
	<?php echo gdrcd_filter('out',$MESSAGE['chat']['tag']['info']['tag'].$MESSAGE['chat']['tag']['info']['dst']);
	      if($_SESSION['permessi']>=GAMEMASTER){echo gdrcd_filter('out',$MESSAGE['chat']['tag']['info']['png']);} ?>
	</span>
	</div>

  <div class="casella_chat" style="padding-left:4px; width: 450px; width: calc(100% - 380px);">
	 <input name="message"  id="message" value="" style="margin-top:0px;" />
  <?php //<textarea name="message"  id="message" style="margin-top:0px; height:50px"></textarea>  ?>
	<br/><span class="casella_info">
	<?php echo gdrcd_filter('out',$MESSAGE['chat']['tag']['info']['msg']); ?>
	</span>  
	</div>

    <div class="casella_chat" style="padding-left:4px;">
	<input type="submit" value="<?php echo gdrcd_filter('out',$MESSAGE['interface']['forms']['submit']); ?>" style="margin-top:0px;" />
	<input type="hidden" name="op" value="new_chat_message" />
	</div>
	<?php if($PARAMETERS['mode']['chatsave']=='ON'){ ?>
  <div class="casella_chat" style="margin-left:30px;">
    <input type="button" value="Salva Chat" onClick="window.open('chat_save.proc.php','Log','width=1,height=1,toolbar=no');" style="width:100%; margin-top:0px;" /> 
  </div>
<?php } ?> 

</form>
</div>

<!-- Form messaggi -->
<?php if(($PARAMETERS['mode']['skillsystem']=='ON')||($PARAMETERS['mode']['dices']=='ON')){ ?>
<div class="form_row">
 <form action="pages/chat.inc.php?ref=30&chat=yes" method="post" target="chat_frame"  id="chat_form_actions">
  
  <?php if($PARAMETERS['mode']['skillsystem']=='ON')
  { ?>

	<div class="casella_chat">
    <?php 
    $result = gdrcd_query("SELECT abilita.id_abilita, abilita.nome, tipo_forza, grado FROM abilita join clgpersonaggioabilita on (abilita.id_abilita=clgpersonaggioabilita.id_abilita and clgpersonaggioabilita.nome='".$_SESSION['login']."' and clgpersonaggioabilita.grado>0) WHERE id_razza=-1 OR id_razza IN (SELECT id_razza FROM personaggio WHERE nome = '".$_SESSION['login']."') ORDER BY nome", 'result');
    ?>
  <script>
    function changeAb()
    {
      var selab = document.getElementById('id_ab');
      var setpoint = document.getElementById('for_spes');
      var setpointinfo = document.getElementById('for_spes_info');
      if (selab)
      {
        if (selab.value == 'no_skill')
        {
          setpoint.style.visibility = 'hidden';
          setpointinfo.style.visibility = 'hidden';
          setpoint.innerHTML = "<option value='0' SELECTED >0</option>";
        }
        <?php 
        while($row = gdrcd_query($result, 'fetch'))
		    { ?>
		      if (selab.value == <?php echo $row['id_abilita'] ?>)
		      {
		        <?php 
		        $disp = '"hidden"';
		        if ($row['tipo_forza'] == 1 || $row['tipo_forza']==2)
		          $disp =  '"visible"';
		        //
		        $maxGrad = 0;
		        if ($row['tipo_forza'] == 1)
		          $maxGrad = 1;
		        if ($row['tipo_forza'] == 2)
		          $maxGrad = gdrcd_filter('num', $row['grado']/20)+1;
		        ?>
		        setpoint.style.visibility = <?php echo $disp ?>;
		        setpointinfo.style.visibility = <?php echo $disp ?>;
		        var html = "<option value='0' SELECTED >0</option>";
		        for (var i=1; i<= <?php echo $maxGrad ?> ;i++)
		          html += "<option value='"+i+"'>"+i+"</option>";
		        setpoint.innerHTML = html;
		      }
		    <?php 
  	    }//while 
  		  //
  		  gdrcd_query($result, 'free');
  		  $result = gdrcd_query("SELECT abilita.id_abilita, abilita.nome, tipo_forza, grado FROM abilita join clgpersonaggioabilita on (abilita.id_abilita=clgpersonaggioabilita.id_abilita and clgpersonaggioabilita.nome='".$_SESSION['login']."' and clgpersonaggioabilita.grado>0) WHERE id_razza=-1 OR id_razza IN (SELECT id_razza FROM personaggio WHERE nome = '".$_SESSION['login']."') ORDER BY nome", 'result');
  	    ?>
      }
    }
  </script>  
	<select name="id_ab" id="id_ab" <!--onchange="changeAb();" --> style="margin-top:0px;" >
	   <option value="no_skill"></option>
     <?php 
     while($row = gdrcd_query($result, 'fetch'))
		 { ?>
          <option value="<?php echo $row['id_abilita']; ?>">
		     <?php echo gdrcd_filter('out',$row['nome']);  ?>
		  </option>
	   <?php 
	   }//while 
		//
		 gdrcd_query($result, 'free');
	   ?>
    </select>
	<br/><span class="casella_info"><?php echo gdrcd_filter('out',$MESSAGE['chat']['commands']['skills']);?></span>
	</div>
  
  <div class="casella_chat">
    <select name="for_spes" id="for_spes" style="visibility:hidden; display:none; width:40px; margin-top:0px;">
      <option value="0" SELECTED >0</option>
    </select>
    <br/><span class="casella_info" id="for_spes_info" style="visibility:hidden; display:none;" >Potenzia</span>
  </div>  
	
	<div class="casella_chat" style="display:none;">
		<select name="id_stats" id="id_stats">
			<option value="no_stats"></option>
			<?php
				/** * Questo modulo aggiunge la possibilità di eseguire prove col dado e caratteristica.
					* Pertanto sono qui elencate tutte le caratteristiche del pg.
					
					* @author Blancks
				*/
				foreach ($PARAMETERS['names']['stats'] as $id_stats => $name_stats)
				{
				
					if (is_numeric(substr($id_stats, 3)))
					{
			?>
					<option value="stats_<?php echo substr($id_stats, 3); ?>"><?php echo $name_stats; ?></option>
			<?php
			
					}
			
				}
			?>
		</select>
		<br/><span class="casella_info"><?php echo gdrcd_filter('out',$MESSAGE['chat']['commands']['stats']);?></span>
	</div>
	
  <?php 
  } 
  else 
  { 
    echo '<input type="hidden" name="id_ab" id="id_ab" value="no_skill">';
  }?>
	
  <?php if($PARAMETERS['mode']['dices']=='ON')
  { ?>

    <div class="casella_chat">
    <select name="dice" id="dice" style="width:80px; margin-top:0px;" >
			<option value="no_dice"></option>
<?php
		/** * Tipi di dado personalizzati da config
			* @author Blancks
		*/
		
		foreach ($PARAMETERS['settings']['skills_dices'] as $dice_name => $dice_value)
		{
?>
			<option value="<?php echo $dice_value; ?>"><?php echo $dice_name; ?></option>
<?php
		}
?>
	</select>
	<br/><span class="casella_info"><?php echo gdrcd_filter('out',$MESSAGE['chat']['commands']['dice']);?></span>
	</div>
	
	<?php 
	}  
	else 
	{ 
	  echo '<input type="hidden" name="dice" id="dice" value="no_dice">';
	}?>
	
	<?php if($PARAMETERS['mode']['skillsystem']=='ON')
	{ ?>


	<div class="casella_chat">
    <?php
	      $result = gdrcd_query("SELECT clgpersonaggiooggetto.id_oggetto, oggetto.nome, clgpersonaggiooggetto.cariche FROM clgpersonaggiooggetto JOIN oggetto ON clgpersonaggiooggetto.id_oggetto = oggetto.id_oggetto WHERE clgpersonaggiooggetto.nome = '".$_SESSION['login']."' AND posizione > 0 ORDER BY oggetto.nome", 'result'); ?>
	   <select name="id_item" id="id_item" style="width:100px; height:20px; margin-top:0px;" >
	   <option value="no_item"></option>
	   
     <?php while($row=gdrcd_query($result, 'fetch'))
     { ?>
          <option value="<?php echo $row['id_oggetto'].'-'.$row['cariche'].'-'.gdrcd_filter('out',$row['nome']); ?>">
		     <?php echo $row['nome'];  ?>
		  </option>
	   <?php 
	   }//while 
	   //
		 gdrcd_query($result, 'free');
	   ?>
    </select>
	<br/><span class="casella_info"><?php echo gdrcd_filter('out',$MESSAGE['chat']['commands']['item']);?></span>
	</div>
	
	<?php 
	}  
	else 
	{ 
	  echo '<input type="hidden" name="id_item" id="id_item" value="no_item">';
	} ?>

	<div class="casella_chat">
	  <input type="submit" value="<?php echo gdrcd_filter('out',$MESSAGE['interface']['forms']['submit']); ?>" style="margin-top:-1px; height:22px; margin-left:2px;" />
      <input type="hidden" name="op" value="take_action">	
	</div>

</form>
</div>
<?php } ?>
</div></div>
<?php }//else?>




</div><!-- Page-Body -->

</div><!-- Pagina -->

