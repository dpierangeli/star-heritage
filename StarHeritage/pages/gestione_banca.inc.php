<div class="pagina_gestione_abilita">
<?php /*HELP: */ 

/*Controllo permessi utente*/
if ($_SESSION['permessi']<MODERATOR){
    echo '<div class="error">'.gdrcd_filter('out',$MESSAGE['error']['not_allowed']).'</div>';
} else { ?>

<!-- Titolo della pagina -->
<div class="page_title">
   <h2><?php echo "Gestione Bancaria" ?></h2>
</div>

<!-- Corpo della pagina -->
<div class="page_body">
<?php
// Gestione operazione di conferma
if (isset($_POST['confirm'])) {
$query = gdrcd_query("SELECT * FROM bonifici WHERE ID = " . $_POST['id_record'] . " AND confermato = 0 LIMIT 1");
// Aggiorno il destinatario
gdrcd_query("UPDATE personaggio SET banca = banca + ".gdrcd_filter('num',$query['quantita'])." WHERE nome = '".$query['destinatario']."' LIMIT 1");
//
// mandiamo un messaggio ai due
gdrcd_query("INSERT INTO messaggi (mittente, destinatario, spedito, testo) VALUES ('".$query['bonificante']."','".gdrcd_capital_letter(gdrcd_filter('in',$query['destinatario']))."', NOW(), '".gdrcd_filter('in', $query['bonificante'].' '.$MESSAGE['interface']['bank']['notice'].' '.gdrcd_filter('num',$query['quantita']).' '.$PARAMETERS['names']['currency']['plur']).'. \n\n'.gdrcd_filter('in',$query['causale'])."')");
//
gdrcd_query("INSERT INTO messaggi (mittente, destinatario, spedito, testo) VALUES ('Webmaster','".gdrcd_capital_letter(gdrcd_filter('in',$query['bonificante']))."', NOW(), 'Il tuo bonifico di ".gdrcd_filter('num',$query['quantita'])." a ".gdrcd_capital_letter(gdrcd_filter('in',$query['destinatario']))." è stato effettuato dalla banca.')");
//
// Ora per finire segniamo la conferma
gdrcd_query("UPDATE bonifici SET confermato = 1, data_conferma = NOW(), confermato_da='".$_SESSION['login']."' WHERE ID = " . $_POST['id_record'] . " LIMIT 1");
}
if (isset($_POST['reject'])) {
$query = gdrcd_query("SELECT * FROM bonifici WHERE ID = " . $_POST['id_record'] . " AND confermato = 0 LIMIT 1");
// Aggiorno il bonificante
gdrcd_query("UPDATE personaggio SET banca = banca + ".gdrcd_filter('num',$query['quantita'])." WHERE nome = '".$query['bonificante']."' LIMIT 1");
//
gdrcd_query("INSERT INTO messaggi (mittente, destinatario, spedito, testo) VALUES ('Webmaster','".gdrcd_capital_letter(gdrcd_filter('in',$query['bonificante']))."', NOW(), 'Il tuo bonifico di ".gdrcd_filter('num',$query['quantita'])." a ".gdrcd_capital_letter(gdrcd_filter('in',$query['destinatario']))." è stato rifiutato, per ulteriori informazioni contattare ".$_SESSION['login']." .')");
//
// Ora per finire segniamo la conferma
gdrcd_query("UPDATE bonifici SET confermato = 2, data_conferma = NOW(), confermato_da='".$_SESSION['login']."' WHERE ID = " . $_POST['id_record'] . " LIMIT 1");
}
?>
<div class="page_title" style="margin-top:10px;">
<h2>Bonifici da confermare:</h2>
</div>
<?php
//Lettura record
	$result=gdrcd_query("SELECT * FROM bonifici where confermato=0", 'result'); 
  while ($row=gdrcd_query($result, 'fetch')){ ?>
  <div style="border:1px solid gray; margin-top:4px; padding:4px; font-size:14px">
  Bonifico di <b><?php echo gdrcd_filter('out',$row['quantita']); ?></b> eseguito da <b><?php echo gdrcd_filter('out',$row['bonificante']); ?></b> a <b><?php echo gdrcd_filter('out',$row['destinatario']); ?></b> il <?php echo gdrcd_filter('out',$row['data_bonifico']); ?> con causale: <br/> <b><?php echo gdrcd_filter('out',$row['causale']); ?></b>
  <form action="main.php?page=gestione_banca" method="post">
    <input type="hidden" name="op" value="confirm" />
    <input type="hidden" name="id_record" value="<?php echo $row['ID']?>" />
    <input type="submit" name="confirm" value="CONFERMA" />
    <input type="submit" name="reject" value="RIFIUTA" />
  </form>
  </div>
  <?php }//end while
  gdrcd_query($result, 'free');
  ?>
<div class="page_title" style="margin-top:10px;">
  <h2>Ultimi 10 bonifici confermati o rifiutati</h2>
</div>
<?php
//Lettura record
	$result=gdrcd_query("SELECT * FROM bonifici where confermato=1 or confermato=2 order by data_conferma DESC LIMIT 10", 'result'); 
  while ($row=gdrcd_query($result, 'fetch')){ 
  if ($row['confermato'] == 1) {
  ?>
  <div style="border:1px solid gray; margin-top:4px; padding:4px;">
  Bonifico di <?php echo gdrcd_filter('out',$row['quantita']); ?> eseguito da <?php echo gdrcd_filter('out',$row['bonificante']); ?> a <?php echo gdrcd_filter('out',$row['destinatario']); ?> il <?php echo gdrcd_filter('out',$row['data_bonifico']); ?> con causale:  <br/> <?php echo gdrcd_filter('out',$row['causale']); ?> <br/> CONFERMATO DA <?php echo gdrcd_filter('out',$row['confermato_da']); ?> il <?php echo gdrcd_filter('out',$row['data_conferma']); ?>
  </div>
  <?php } 
  else { 
  ?>
  <div style="border:1px solid gray; margin-top:4px; padding:4px;">
  Bonifico di <?php echo gdrcd_filter('out',$row['quantita']); ?> eseguito da <?php echo gdrcd_filter('out',$row['bonificante']); ?> a <?php echo gdrcd_filter('out',$row['destinatario']); ?> il <?php echo gdrcd_filter('out',$row['data_bonifico']); ?> con causale:  <br/> <?php echo gdrcd_filter('out',$row['causale']); ?> <br/> RIFIUTATO DA <?php echo gdrcd_filter('out',$row['confermato_da']); ?> il <?php echo gdrcd_filter('out',$row['data_conferma']); ?>
  </div>
  <?php 
  }
  }//end while
  gdrcd_query($result, 'free');
  ?>
  
</div>
<?php }//else ?>
</div>