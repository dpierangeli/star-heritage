<div class="pagina_servizi_mercato">
  <?php
  /* HELP: */

  /* Verifico la liquidita' del PG */
  $row = gdrcd_query("SELECT soldi FROM personaggio WHERE nome='" . $_SESSION['login'] . "'");
  $money = $row['soldi'];
  ?>


  <!-- Titolo della pagina -->
  <div class="page_title">
    <h2><?php echo gdrcd_filter('out', $MESSAGE['interface']['market']['page_name']); ?></h2>
  </div>


  <!-- Corpo della pagina -->
  <div class="page_body">


    <?php
    /* Acquisto */
    if ($_POST['op'] == 'buy') {
      $nero = gdrcd_filter('in', $_POST['category']) == "nero" ? 1 : 0;

      /* Controllo se ha la grana */
      $costo = gdrcd_query("SELECT cariche, costo FROM oggetto WHERE id_oggetto = " . gdrcd_filter('num', $_POST['id_oggetto']) . "");

      if ($money >= $costo['costo']) {
        /* Controllo se possiede gia' oggetti analoghi */
        $query = "SELECT id_oggetto FROM clgpersonaggiooggetto WHERE id_oggetto = " . gdrcd_filter('num', $_POST['id_oggetto']) . " AND nome = '" . $_SESSION['login'] . "'";
        $result = gdrcd_query($query, 'result');
        if (gdrcd_query($result, 'num_rows') > 0) {
          $query = "UPDATE clgpersonaggiooggetto SET numero = numero + 1 WHERE id_oggetto = " . gdrcd_filter('num', $_POST['id_oggetto']) . " AND nome = '" . $_SESSION['login'] . "'";
        } else {
          $query = "INSERT INTO clgpersonaggiooggetto (nome, id_oggetto, cariche, numero, posizione) VALUES ('" . $_SESSION['login'] . "'," . gdrcd_filter('num', $_POST['id_oggetto']) . ", " . $costo['cariche'] . ", 1, 0)";
        }
        /* Eseguo l'acquisto */
        gdrcd_query($query);
        /* Esigo il quattrino */
        gdrcd_query("UPDATE personaggio SET soldi = soldi - " . $costo['costo'] . " WHERE nome = '" . $_SESSION['login'] . "' LIMIT 1");
        /* Riduco il mercato */
        //$query="SELECT numero FROM mercato WHERE id_oggetto = '".gdrcd_filter('num',$_POST['id_oggetto'])."' LIMIT 1";
        //$result=mysql_query($query);
        $check = gdrcd_query($result, 'fetch');
        gdrcd_query($result, 'free');

        if (gdrcd_filter('num', $_POST['numero']) > 1) {
          $query = "UPDATE mercato SET numero = numero - 1 WHERE id_oggetto = '" . gdrcd_filter('num', $_POST['id_oggetto']) . "' and nero=" . $nero . " LIMIT 1";
        } else {
          $query = "DELETE FROM mercato WHERE id_oggetto = '" . gdrcd_filter('num', $_POST['id_oggetto']) . "' and nero=" . $nero . " LIMIT 1";
        }
        gdrcd_query($query);
        ?>
        <div class="warning">
        <?php echo gdrcd_filter('out', $MESSAGE['warning']['buyed']); ?>
        </div>
        <?php
        } else {
          ?>
        <div class="error">
        <?php echo gdrcd_filter('out', $MESSAGE['warning']['cant_do']); ?>
        </div>
        <?php } ?>
      <!-- Link di ritorno alla visualizzazione di base -->
      <div class="link_back">
        <a href="main.php?page=servizi_mercato">
  <?php echo gdrcd_filter('out', $MESSAGE['interface']['market']['back']); ?>
        </a>
      </div>
<?php } ?>



<?php
/* Vendita */
if (gdrcd_filter('get', $_POST['op']) == 'sell') {

  $nero = gdrcd_filter('in', $_POST['category']) == "nero" ? 1 : 0;

  /* controllo che il PG abbia l'oggetto */
  $query = "SELECT clgpersonaggiooggetto.numero, oggetto.costo FROM clgpersonaggiooggetto LEFT JOIN oggetto ON clgpersonaggiooggetto.id_oggetto = oggetto.id_oggetto WHERE clgpersonaggiooggetto.id_oggetto = " . gdrcd_filter('num', $_POST['id_oggetto']) . " AND clgpersonaggiooggetto.nome = '" . $_SESSION['login'] . "'";
  #echo $query;
  $result = gdrcd_query($query, 'result');

  if (gdrcd_query($result, 'num_rows') > 0) {

    $row = gdrcd_query($result, 'fetch');
    gdrcd_query($result, 'free');

    $costo_vendita = floor(($row['costo'] / 100) * (100 - $PARAMETERS['settings']['resell_price']));

    if ($row['numero'] > 1) {
      $query = "UPDATE clgpersonaggiooggetto SET numero = numero - 1 WHERE id_oggetto = " . gdrcd_filter('num', $_POST['id_oggetto']) . " AND nome = '" . $_SESSION['login'] . "' LIMIT 1";
    } else {
      $query = "DELETE FROM clgpersonaggiooggetto WHERE id_oggetto = " . gdrcd_filter('num', $_POST['id_oggetto']) . " AND nome = '" . $_SESSION['login'] . "' LIMIT 1";
    }
    gdrcd_query($query);
    ?>
        <div class="warning">
          <?php echo gdrcd_filter('out', $MESSAGE['warning']['buyed']); ?>
        </div>
        <?php
        gdrcd_query("UPDATE mercato SET numero = numero + 1 WHERE id_oggetto = " . gdrcd_filter('num', $_POST['id_oggetto']) . " and nero=" . $nero . " LIMIT 1");

        gdrcd_query("UPDATE personaggio SET soldi = soldi + " . gdrcd_filter('num', $costo_vendita) . " WHERE nome = '" . $_SESSION['login'] . "' LIMIT 1");
      } else {
        ?>
        <div class="error">
          <?php echo gdrcd_filter('out', $MESSAGE['warning']['cant_do']); ?>
        </div>
      <?php }//else ?>
      <!-- Link di ritorno alla visualizzazione di base -->
      <div class="link_back">
        <a href="main.php?page=servizi_mercato">
          <?php echo gdrcd_filter('out', $MESSAGE['interface']['market']['back']); ?>
        </a>
      </div>
    <?php } ?>



    <?php
    /* Visualizzazione di base */
    if ((isset($_POST['op']) === FALSE) && (isset($_REQUEST['op']) === FALSE)) {
      /* Carico le categorie oggetto */
      $query = "SELECT cod_tipo, descrizione FROM codtipooggetto ORDER BY descrizione ";
      $result = gdrcd_query($query, 'result');
      ?>

      <!-- Form di inserimento/modifica -->
      <div class="elenco_esteso">
        <div class="elenco_record_gioco">
          <table>
            <!-- Intestazione tabella -->
            <tr>
              <td class="casella_titolo"><div class="capitolo_elenco">
            <?php echo gdrcd_filter('out', $MESSAGE['interface']['market']['categories']); ?>
                </div></td>
            </tr>
  <?php while ($row = gdrcd_query($result, 'fetch')) { ?>
              <tr>
                <td  class="casella_elemento"><div class="elementi_elenco">
                    <a href="main.php?page=servizi_mercato&op=visit&what=<?php echo gdrcd_filter('out', $row['cod_tipo']); ?>"><?php echo gdrcd_filter('out', $row['descrizione']); ?></a>
                  </div></td>
              </tr>
            <?php
            }//while

            gdrcd_query($result, 'free');
            ?>
            <tr>
              <td  class="casella_elemento"><div class="elementi_elenco">
                  <a href="main.php?page=servizi_mercato&op=visit&what=nero">Mercato nero</a>
                </div></td>
            </tr>
          </table>
        </div>
      </div>
    <?php }//if  ?>



    <?php
    if ($_REQUEST['op'] == 'visit') { /* Elenco oggetti */

      //Determinazione pagina (paginazione)
      $pagebegin = (int) $_REQUEST['offset'] * $PARAMETERS['settings']['records_per_page'];
      $pageend = $PARAMETERS['settings']['records_per_page'];
      //Conteggio record totali
      $record_globale = gdrcd_query("SELECT COUNT(*) AS TOT FROM mercato JOIN oggetto on oggetto.id_oggetto=mercato.id_oggetto where oggetto.tipo='" . gdrcd_filter('get', $_REQUEST['what']) . "'");
      $totaleresults = $record_globale['TOT'];
      $canOperate = true;
      //Lettura record
      if (gdrcd_filter('get', $_REQUEST['what']) == "nero") {
        $query = "SELECT mercato.numero, oggetto.id_oggetto, oggetto.nome, oggetto.descrizione, oggetto.costo, oggetto.difesa, oggetto.attacco, oggetto.cariche, oggetto.bonus_car0, oggetto.bonus_car1, oggetto.bonus_car2, oggetto.bonus_car3, oggetto.bonus_car4, oggetto.bonus_car5, oggetto.scafo, oggetto.scudi, oggetto.man, oggetto.vel, oggetto.urlimg, oggetto.disponib, oggetto.stato FROM oggetto JOIN mercato ON oggetto.id_oggetto=mercato.id_oggetto WHERE mercato.nero = 1 ORDER BY nome LIMIT " . $pagebegin . ", " . $pageend . "";
        //
        // Controllare se il personaggio può vendere o comprare merci al mercato nero
        $black_store = "select * from mercato_nero where personaggio = '" . $_SESSION['login'] . "' and accesso = 1";
        $black_result = gdrcd_query($black_store, 'result');
        if (gdrcd_query($black_result, 'num_rows') == 0)
          $canOperate = false;
        gdrcd_query($black_result, 'free');
      }
      else {
        $query = "SELECT mercato.numero, oggetto.id_oggetto, oggetto.nome, oggetto.descrizione, oggetto.costo, oggetto.difesa, oggetto.attacco, oggetto.cariche, oggetto.bonus_car0, oggetto.bonus_car1, oggetto.bonus_car2, oggetto.bonus_car3, oggetto.bonus_car4, oggetto.bonus_car5, oggetto.scafo, oggetto.scudi, oggetto.man, oggetto.vel, oggetto.urlimg, oggetto.disponib, oggetto.stato FROM oggetto JOIN mercato ON oggetto.id_oggetto=mercato.id_oggetto WHERE tipo = '" . gdrcd_filter('get', $_REQUEST['what']) . "' AND mercato.nero = 0 ORDER BY nome LIMIT " . $pagebegin . ", " . $pageend . "";
      }
      $result = gdrcd_query($query, 'result');
      $numresults = gdrcd_query($result, 'num_rows');

      /* Se esistono record */
      if ($numresults > 0) {
        ?>
        <!-- Elenco dei record paginato -->
        <div class="elenco_record_gioco">
          <table>
            <!-- Intestazione tabella -->
            <tr>
              <td class="casella_titolo"><div class="titoli_elenco"><?php echo gdrcd_filter('out', $MESSAGE['interface']['market']['item']); ?></div></td>
              <td class="casella_titolo"><div class="titoli_elenco"><?php echo gdrcd_filter('out', $MESSAGE['interface']['market']['bonus']); ?></div></td>
              <td class="casella_titolo"><div class="titoli_elenco"><?php echo gdrcd_filter('out', $MESSAGE['interface']['market']['more']); ?></div></td>
            </tr>
            <!-- Record -->
    <?php while ($row = gdrcd_query($result, 'fetch')) { ?>
              <tr>
                <td class="casella_elemento_img" style="background-color: #131313; font-size: 13px;" >
                  <div class="inventario_nome" style="font-weight: bolder;" ><?php echo $row['nome']; ?></div>
                  <div class="inventario_img">
                    <img style="max-width:200px; max-height:200px;" src="themes/<?php echo $PARAMETERS['themes']['current_theme']; ?>/imgs/items/<?php echo $row['urlimg']; ?>" />
                  </div>
                  <div class="inventario_quantita"><?php echo $MESSAGE['interface']['market']['stock'] . ': ' . $row['numero']; ?></div>
                  <div class="titoli_elenco"><?php echo gdrcd_filter('out', $MESSAGE['interface']['market']['price']); ?></div>
      <?php if ($canOperate) { ?>
                    <div class="controlli_elenco" style="width:75px;margin-left: auto;margin-right: auto;">
                      <!-- Compra -->
                      <div class="form_gioco" >
                        <form  action="main.php?page=servizi_mercato" method="post">
                          <input type="hidden" name="id_oggetto" value="<?php echo $row['id_oggetto'] ?>" />
                          <input type="hidden" name="costo" value="<?php echo $row['costo'] ?>" />
                          <input type="hidden" name="cariche" value="<?php echo $row['cariche'] ?>" />
                          <input type="hidden" name="numero" value="<?php echo $row['numero'] ?>" />
                          <input type="hidden" name="op" value="buy" />
                          <input type="hidden" name="category" value="<?php echo gdrcd_filter('get', $_REQUEST['what']); ?>" />
                          <div class='form_label'>
                                   <?php echo $row['costo'] . ' ' . $PARAMETERS['names']['currency']['short']; ?>
                          </div>
                          <div class='form_submit'>
                            <input type="submit"
                                   name="butt"
        <?php if ($money < $row['costo']) {
          echo 'disabled ';
        } ?>
                                   value="<?php echo gdrcd_filter('out', $MESSAGE['interface']['market']['buy']); ?>" />

                          </div>
                        </form>
                      </div>
                      <!-- Vendi -->
                      <div class="form_gioco" >
                        <form action="main.php?page=servizi_mercato" method="post">
                          <input type="hidden" name="id_oggetto" value="<?php echo $row['id_oggetto'] ?>" />
                          <input type="hidden" name="costo" value="<?php echo ($row['costo'] / 100) * (100 - $PARAMETERS['settings']['resell_price']) ?>" />
                          <input type="hidden" name="op" value="sell" />
                          <input type="hidden" name="category" value="<?php echo gdrcd_filter('get', $_REQUEST['what']); ?>" />
                          <div class='form_label'>
        <?php echo floor(($row['costo'] / 100) * (100 - $PARAMETERS['settings']['resell_price'])) . ' ' . $PARAMETERS['names']['currency']['short']; //decremento di costo ?>
                          </div>
                          <div class='form_submit'>
                            <input type="submit"
                                   name="butt"
                                   value="<?php echo gdrcd_filter('out', $MESSAGE['interface']['market']['sell']); ?>" />
                          </div>
                        </form>
                      </div>
                    </div>
                      <?php } ?>
                </td>
                <td class="casella_elemento">
                  <div class="inventario_riga_proprieta" style='display:<?php echo $row['attacco'] == 0 ? 'none' : ''; ?>;'>
                    <div style="float: left; clear: left;" >
      <?php echo gdrcd_filter('out', $MESSAGE['interface']['market']['attack'] . ': '); ?>
                    </div>
                    <div style="float: right; clear: right;">
                      <?php echo $row['attacco']; ?>
                    </div>
                  </div>
                  <div class="inventario_riga_proprieta" style='display:<?php echo $row['scafo'] == 0 ? 'none' : ''; ?>;'>
                    <div style="float: left; clear: left;">
                      Scafo:
                    </div>
                    <div style="float: right; clear: right;">
                      <?php echo $row['scafo']; ?>
                    </div>
                  </div>
                  <div class="inventario_riga_proprieta" style='display:<?php echo $row['scudi'] == 0 ? 'none' : ''; ?>;'>
                    <div style="float: left; clear: left;">
                      Scudi:
                    </div>
                    <div style="float: right; clear: right;">
                      <?php echo $row['scudi']; ?>
                    </div>
                  </div>
                  <div class="inventario_riga_proprieta" style='display:<?php echo $row['man'] == 0 ? 'none' : ''; ?>;'>
                    <div style="float: left; clear: left;">
                      Manovrabilità:
                    </div>
                    <div style="float: right; clear: right;">
                      <?php echo $row['man']; ?>
                    </div>
                  </div>
                  <div class="inventario_riga_proprieta" style='display:<?php echo $row['vel'] == 0 ? 'none' : ''; ?>;'>
                    <div style="float: left; clear: left;">
                      Velocità:
                    </div>
                    <div style="float: right; clear: right;">
                      <?php echo $row['vel']; ?>
                    </div>
                  </div>
                  <div class="inventario_riga_proprieta" style='display:<?php echo $row['difesa'] == 0 ? 'none' : ''; ?>;'>
                    <div style="float: left; clear: left;">
                      <?php echo gdrcd_filter('out', $MESSAGE['interface']['market']['defence'] . ': '); ?>
                    </div>
                    <div style="float: right; clear: right;">
                      <?php echo $row['difesa']; ?>
                    </div>
                  </div>
                  <div class="inventario_riga_proprieta" style='display:<?php echo $row['bonus_car0'] == 0 ? 'none' : ''; ?>;'>
                    <div style="float: left; clear: left;">
                      <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car0'] . ': '); ?>
                    </div>
                    <div style="float: right; clear: right;">
                      <?php echo $row['bonus_car0']; ?>
                    </div>
                  </div>
                  <div class="inventario_riga_proprieta" style='display:<?php echo $row['bonus_car1'] == 0 ? 'none' : ''; ?>;'>
                    <div style="float: left; clear: left;">
                      <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car1'] . ': '); ?>
                    </div>
                    <div style="float: right; clear: right;">
                      <?php echo $row['bonus_car1']; ?>
                    </div>
                  </div>
                  <div class="inventario_riga_proprieta" style='display:<?php echo $row['bonus_car2'] == 0 ? 'none' : ''; ?>;'>
                    <div style="float: left; clear: left;">
                      <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car2'] . ': '); ?>
                    </div>
                    <div style="float: right; clear: right;">
                      <?php echo $row['bonus_car2']; ?>
                    </div>
                  </div>
                  <div class="inventario_riga_proprieta" style='display:<?php echo $row['bonus_car3'] == 0 ? 'none' : ''; ?>;'>
                    <div style="float: left; clear: left;">
                      <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car3'] . ': '); ?>
                    </div>
                    <div style="float: right; clear: right;">
                      <?php echo $row['bonus_car3']; ?>
                    </div>
                  </div>
                  <div class="inventario_riga_proprieta" style='display:<?php echo $row['bonus_car4'] == 0 ? 'none' : ''; ?>;'>
                    <div style="float: left; clear: left;">
                      <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car4'] . ': '); ?>
                    </div>
                    <div style="float: right; clear: right;">
                      <?php echo $row['bonus_car4']; ?>
                    </div>
                  </div>
                  <div class="inventario_riga_proprieta" style='display:<?php echo $row['bonus_car5'] == 0 ? 'none' : ''; ?>;'>
                    <div style="float: left; clear: left;">
                      <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car5'] . ': '); ?>
                    </div>
                    <div style="float: right; clear: right;">
                      <?php echo $row['bonus_car5']; ?>
                    </div>
                  </div>
                  <div class="inventario_riga_proprieta" >
                    <div style="float: left; clear: left;">
                      <?php echo gdrcd_filter('out', $MESSAGE['interface']['market']['item_charges'] . ': '); ?>
                    </div>
                    <div style="float: right; clear: right;">
      <?php
      if ($row['cariche'] > 0) {
        echo $row['cariche'];
      } else {
        echo $MESSAGE['interface']['market']['no_charges'];
      }
      ?>
                    </div>
                  </div>
                </td>
                <td  class="casella_elemento">
                  <div style="text-align: left; font-weight: bolder;" >Disponibilità: <?php echo $row['disponib']; ?></div>
                  <br />
                  <div style="text-align: left; font-weight: bolder;" >Stato: <?php echo $row['stato']; ?></div>
                  <br />
                  <div class="inventario_riga_descrizione"><?php
        echo str_replace("\n", "<br/>", $row['descrizione']);
      ?></div>
                </td>
              </tr>
          <?php
          } //while

          gdrcd_query($result, 'free');
          ?>
          </table>
        </div>
  <?php }//if  ?>

      <!-- Paginatore elenco -->
      <div class="pager">
          <?php
          if ($totaleresults > $PARAMETERS['settings']['records_per_page']) {
            echo gdrcd_filter('out', $MESSAGE['interface']['pager']['pages_name']);
            for ($i = 0; $i <= floor($totaleresults / $PARAMETERS['settings']['records_per_page']); $i++) {
              ?>
            <a href="main.php?page=servizi_mercato&offset=<?php echo $i; ?>&op=visit&what=<?php echo gdrcd_filter('get', $_REQUEST['what']) ?>"><?php echo $i + 1; ?></a>
    <?php } //for
  }//if
  ?>
      </div>

      <!-- link back -->
      <div class="link_back">
        <a href="main.php?page=servizi_mercato">
  <?php echo gdrcd_filter('out', $MESSAGE['interface']['market']['back']); ?>
        </a>
      </div>

<?php }//else  ?>


  </div>


</div><!--Pagina-->