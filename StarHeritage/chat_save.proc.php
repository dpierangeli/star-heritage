<?php

#################################################################################
#                                                                               ##
#                Save Chat HTML 1.3 - Author eLDiabolo                          ##
#                                                                               ##
#      e-mail: http://www.gdr-online.com/email.asp?email=eldiabolo              ##
#                                                                               ##
##################################################################################
#################################################################################

session_start();

/* Includo i file necessari */
include('includes/constant_values.inc.php');
include('config.inc.php');
include('vocabulary/' . $PARAMETERS['languages']['set'] . '.vocabulary.php');
include('includes/functions.inc.php');

/* Eseguo la connessione al database */
$handleDBConnection = gdrcd_connect();

$typeOrder = 'ASC';

if ($PARAMETERS['mode']['chat_from_bottom'] == 'ON')
  $typeOrder = 'DESC';

/* Query per caricamento dati dalla chat corrente, carica le azioni degli ultimi 240 min - 4 ore !! NON SALVA LE CHAT PRIVATE !! */

if ($PARAMETERS['mode']['chatsavepvt'] == 'ON') {
  $query = gdrcd_query("	SELECT chat.id, chat.imgs, chat.mittente, chat.destinatario, chat.tipo, chat.ora, chat.testo, personaggio.url_img_chat, mappa.ora_prenotazione, mappa.privata
	FROM chat
	INNER JOIN mappa ON mappa.id = chat.stanza
	LEFT JOIN personaggio ON personaggio.nome = chat.mittente
	WHERE stanza = " . $_SESSION['luogo'] . " AND DATE_SUB(NOW(), INTERVAL 240 MINUTE) < ora ORDER BY id " . $typeOrder, 'result');
} else {
  $query = gdrcd_query("	SELECT chat.id, chat.imgs, chat.mittente, chat.destinatario, chat.tipo, chat.ora, chat.testo, personaggio.url_img_chat, mappa.ora_prenotazione, mappa.privata
	FROM chat
	INNER JOIN mappa ON mappa.id = chat.stanza
	LEFT JOIN personaggio ON personaggio.nome = chat.mittente
	WHERE stanza = " . $_SESSION['luogo'] . " AND mappa.privata = 0 AND DATE_SUB(NOW(), INTERVAL 240 MINUTE) < ora AND chat.ora > IFNULL(mappa.ora_prenotazione, '0000-00-00 00:00:00') ORDER BY id " . $typeOrder, 'result');
}
/* Inizio a preparare il testo da inserire poi nel file da salvare. */
$add_chat = '';


$i = 0;
/* Eseguo la query e le formattazioni */
while ($row = gdrcd_query($query, 'fetch')) {
  $addSpaces = true;
  $add_icon = '';
  //
  switch ($row['tipo']) {
    case 'P':
      $addSpaces = false;
      break;

    case 'A':
      $add_chat.= gdrcd_format_time($row['ora']) . ' ';
      $add_chat.= $row['mittente'] . ' ';
      //
      if (empty($row['destinatario']) === FALSE) {
        $add_chat.= '[' . gdrcd_filter('out', $row['destinatario']) . '] ';
      }
      $add_chat.=' ';
      $add_chat.= gdrcd_hidechat(gdrcd_chatcolorsave($row['testo']));
      break;

    // SUSSURRO
    case 'S':
      $addSpaces = false;
      break;

    // PNG
    case 'N':
      $add_chat.= gdrcd_format_time($row['ora']) . ' ';
      $add_chat.= $row['destinatario'] . '  ';
      $add_chat.= gdrcd_hidechat(gdrcd_chatcolorsave($row['testo'])) . ' ';
      break;

    // MASTER
    case 'M':
      $add_chat.= 'MASTER: [color=red]' . $row['testo'] . '[/color] ';
      break;

    // MASTER 2 (?)
    case 'I':
      /*
        $add_chat.= '<div class="chat_row_'.$row['tipo'].'">';

        $add_chat.= '<img class="chat_img" src="'.gdrcd_filter('out',$row['testo']).'" />';

        $add_chat.= '</div>';
       */
      $add_chat.= 'MASTER: [color=red]' . $row['testo'] . '[/color] ';
      break;

    // ABILITA'
    case 'C':
      $az = gdrcd_hidechat($row['testo']);
      //
      if ($az != "") {
        $add_chat.= gdrcd_format_time($row['ora']) . ' ';
        $add_chat.= ' [color=blue]' . $az . '[/color] ';
      } else
        $addSpaces = false;
      break;

    // DADO
    case 'D':
      $add_chat.= gdrcd_format_time($row['ora']) . ' ';
      $add_chat.= ' [color=blue]' . $row['testo'] . '[/color] ';
      break;

    // OGGETTO
    case 'O':
      $addSpaces = false;
      /*
        $add_chat.= '<div class="chat_row_'.$row['tipo'].'">';
        $add_chat.= '<span class="chat_time">'.gdrcd_format_time($row['ora']).'</span>';
        $add_chat.= '<span class="chat_msg">'.gdrcd_filter('out',$row['testo']).'</span>';
        $add_chat.= '</div>';
       */
      break;
  }
  $i++;
  if ($addSpaces)
    $add_chat.='#stop##stop#';
}
$add_chat.='';

/* Scrivo tutto in un file di testo */
$file = gdrcd_format_datetime_cat(date("Y-m-d H:i:s"));
$file = $file . ".txt";

if ($PARAMETERS['mode']['chatsave_link'] == 'ON') {
  $file = "giocate/" . $file;
  $fp = fopen($file, 'wb');
  $message = str_replace("#stop#", "\r\n", $add_chat);
  fwrite($fp, $message, strlen($message));
  fclose($fp);
  echo '<a href="' . $file . '">Link giocata</a>';
}
if ($PARAMETERS['mode']['chatsave_download'] == 'ON') {
  $fp = fopen($file, "wb");
  $message = str_replace("#stop#", "\r\n", $add_chat);
  fwrite($fp, $message, 65536);
  fclose($fp);

  /* Do le informazioni di download */
  header("Content-Disposition: attachment; filename=" . urlencode($file));
  header("Content-Type: application/force-download");
  header("Content-Type: application/octet-stream");
  header("Content-Type: application/download");
  header("Content-Description: File Transfer");
  header("Content-Length: " . filesize($file));

  /* Passo le info del file al browser */
  $fp = fopen($file, "r");
  while (!feof($fp)) {
    print fread($fp, 65536);
    flush();
  }
  fclose($fp);

  /* Elimino il file temporaneo */
  unlink($file);

  /* Chiudo la finestra aperta */
}
if ($PARAMETERS['mode']['chatsave_download'] == 'ON' && $PARAMETERS['mode']['chatsave_link'] == 'OFF') {
  ?>
  <script language="JavaScript1.2">
    self.close();
  </script>
<?php } ?>