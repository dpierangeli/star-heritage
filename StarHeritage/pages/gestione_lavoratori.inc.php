<div class="pagina_gestione_news">
<?php
if ($_SESSION['permessi'] < GAMEMASTER)
{
    echo '<div class="error">'.gdrcd_filter('out',$MESSAGE['error']['not_allowed']).'</div>';
} 
else 
{ ?>

<!-- Titolo della pagina -->
<div class="page_title">
   <h2>LAVORI</h2>
</div>
<!-- Corpo della pagina -->
<div class="page_body">
<?php
$query= "SELECT DISTINCT clgpersonaggioruolo.personaggio, ruolo.nome_ruolo,
(Select count(*) from log where log.codice_evento=10 and log.lavoro=1 and log.nome_interessato=clgpersonaggioruolo.personaggio and DATE_SUB(NOW(), INTERVAL 60 DAY) < log.data_evento) as lavori
FROM clgpersonaggioruolo
JOIN ruolo ON clgpersonaggioruolo.id_ruolo = ruolo.id_ruolo
WHERE ruolo.gilda = -1";
$result=gdrcd_query($query, 'result');
?>

<div class="elenco_record_gestione">
 <table>
  <!-- Intestazione tabella -->
    <tr>
    <td class="casella_titolo" style="text-align: justify;"><div class="titoli_elenco">Personaggio</div></td>
    <td class="casella_titolo" style="text-align: justify;"><div class="titoli_elenco">Lavoro</div></td>
    <td class="casella_titolo" style="text-align: justify;"><div class="titoli_elenco">Giocate negli ultimi 60 giorni</div></td>
    </tr>
    <!-- Record -->
    <?php while ($row=gdrcd_query($result, 'fetch')){ ?>
      <tr class="risultati_elenco_record_gestione">
      <td class="casella_elemento">
      <div class="elementi_elenco">
      <?php echo $row['personaggio']; ?>
      </div>
      </td>
      <td class="casella_elemento">
      <div class="elementi_elenco">
      <?php echo $row['nome_ruolo']; ?>
      </div>
      </td>
      <td class="casella_elemento">
      <div class="elementi_elenco">
      <?php echo $row['lavori']; ?>
      </div>
      </td>
      </tr>
    <?php } 
    gdrcd_query($result, 'free');  ?>
  </table>  
</div>
    
</div>
<?php } ?>
</div>