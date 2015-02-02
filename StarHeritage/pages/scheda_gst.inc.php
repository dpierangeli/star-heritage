<div class="pagina_schedam_odifica">
  <?php
  /* HELP: */

  if (isset($_REQUEST['pg']) === FALSE) {
    echo gdrcd_filter('out', $MESSAGE['error']['unknown_character_sheet']);
  } else if ($_SESSION['permessi'] < MODERATOR) {
    echo gdrcd_filter('out', $MESSAGE['error']['access_denied']);
  } else {
    if ($_POST['op'] == 'modify') {
      gdrcd_query("UPDATE personaggio SET email = '" . gdrcd_filter('in', $_POST['modifica_email']) . "', affetti = '" . gdrcd_filter('in', $_POST['modifica_affetti']) . "', descrizione = '" . gdrcd_filter('in', $_POST['modifica_background']) . "', url_media = '" . gdrcd_filter('in', $_POST['modifica_url_media']) . "', url_img = '" . gdrcd_filter('in', $_POST['modifica_url_img']) . "', car0 = " . gdrcd_filter('num', $_POST['car0']) . ", car1 = " . gdrcd_filter('num', $_POST['car1']) . ", car2 = " . gdrcd_filter('num', $_POST['car2']) . ", car3 = " . gdrcd_filter('num', $_POST['car3']) . ", car4 = " . gdrcd_filter('num', $_POST['car4']) . ",  car5 = " . gdrcd_filter('num', $_POST['car5']) . ",  Sens = " . gdrcd_filter('num', $_POST['car6']) . ", sesso = '" . gdrcd_filter('in', $_POST['modifica_sesso']) . "', id_razza = " . gdrcd_filter('num', $_POST['modifica_razza']) . ", banca=" . gdrcd_filter('num', $_POST['modifica_banca']) . ", salute_max=" . gdrcd_filter('num', $_POST['modifica_salute_max']) . ", forza_max=" . gdrcd_filter('num', $_POST['modifica_forza_max']) . " WHERE nome = '" . gdrcd_filter('in', $_REQUEST['pg']) . "' AND permessi <= " . $_SESSION['permessi'] . "");
      echo '<div class="warning">' . gdrcd_filter('out', $MESSAGE['warning']['modified']) . '</div>';
    } else if ($_POST['op'] == 'abreset') {
      //Cancello tutte le abilità
      gdrcd_query("Delete from clgpersonaggioabilita where nome ='" . gdrcd_filter('in', $_REQUEST['pg']) . "'");
      //
      $result = gdrcd_query("SELECT * FROM personaggio WHERE nome='" . gdrcd_filter('in', $_REQUEST['pg']) . "' LIMIT 1", 'result');
      $recordPg = gdrcd_query($result, 'fetch');

      $TagliaRecord = gdrcd_query("SELECT Taglia, first_step_eta, second_step_eta, bonus_car0, bonus_car1, bonus_car2, bonus_car3, bonus_car4, bonus_car5 FROM razza WHERE id_razza=" . gdrcd_filter('num', $recordPg['id_razza']) . " LIMIT 1");
      //
      // Leggo le caratteristiche
      $car0 = gdrcd_filter('num', $recordPg['car0']) + gdrcd_filter('num', $TagliaRecord['bonus_car0']);
      $car1 = gdrcd_filter('num', $recordPg['car1']) + gdrcd_filter('num', $TagliaRecord['bonus_car1']);
      $car2 = gdrcd_filter('num', $recordPg['car2']) + gdrcd_filter('num', $TagliaRecord['bonus_car2']);
      $car3 = gdrcd_filter('num', $recordPg['car3']) + gdrcd_filter('num', $TagliaRecord['bonus_car3']);
      $car4 = gdrcd_filter('num', $recordPg['car4']) + gdrcd_filter('num', $TagliaRecord['bonus_car4']);
      $car5 = gdrcd_filter('num', $recordPg['car5']) + gdrcd_filter('num', $TagliaRecord['bonus_car5']);
      $car6 = gdrcd_filter('num', $recordPg['Sens']);
      //
      // Salute: taglia + costrituzione
      $TagliaRaz = gdrcd_filter('num', $TagliaRecord['Taglia']);
      $salutecalc = $TagliaRaz + $car1;
      //echo 'calcolo '.$salutecalc;
      //echo 'pg '.$recordPg;
      //
     // update salute
      gdrcd_query("update personaggio set salute=" . $salutecalc . ", salute_max=" . $salutecalc . " where nome='" . $_REQUEST['pg'] . "'");

      /*
        echo 'forza: '.$car0;
        echo 'Costituzione: '.$car1;
        echo 'Destrezza: '.$car2;
        echo 'Intelligenza: '.$car3;
        echo 'Istruzione: '.$car4;
        echo 'Fascino: '.$car5;
        echo 'Sensibilità: '.$car6;
       */
      //
      // Ciclo sulle abilità
      $abTot = gdrcd_filter('num', $PARAMETERS['settings']['first_px']);
      $ab = gdrcd_query("SELECT id_abilita, nome, val_init FROM abilita", 'result');
      while ($record = gdrcd_query($ab, 'fetch')) {
        $abVal = 0;
        if ($record['val_init'] == "SAG+INT") {
          $abVal = $car4 + $car3;
        } else if ($record['val_init'] == "COS+DES+FOR") {
          $abVal = $car0 + $car1 + $car2;
        } else if ($record['val_init'] == "FASx2") {
          $abVal = $car5 + $car5;
        } else if ($record['val_init'] == "DES+INT") {
          $abVal = $car2 + $car3;
        } else if ($record['val_init'] == "SAGx2") {
          $abVal = $car4 + $car4;
        } else if ($record['val_init'] == "SEN+INT") {
          $abVal = $car6 + $car3;
        } else if ($record['val_init'] == "FOR+DES") {
          $abVal = $car0 + $car2;
        } else if ($record['val_init'] == "DES+INT") {
          $abVal = $car2 + $car3;
        } else if ($record['val_init'] == "SENx2") {
          $abVal = $car6 + $car6;
        } else if ($record['val_init'] == "DESx2") {
          $abVal = $car2 + $car2;
        } else if ($record['val_init'] == "DES+SAG") {
          $abVal = $car2 + $car4;
        }
        //
        $abTot = $abTot + gdrcd_rankCost($abVal);
        gdrcd_query("INSERT INTO clgpersonaggioabilita (nome, id_abilita, grado) VALUES ('" . gdrcd_filter('in', $_REQUEST['pg']) . "', " . $record['id_abilita'] . ", " . $abVal . ")");
      }
      gdrcd_query($ab, 'free');
      gdrcd_query($result, 'free');
      gdrcd_query("UPDATE personaggio SET esperienza=" . gdrcd_filter('num', $abTot) . " WHERE nome='" . gdrcd_filter('in', $_REQUEST['pg']) . "'");
      //
      echo "Abilità resettate";
    } else {
      /* Carico le informazioni del PG */

      $record = gdrcd_query("SELECT email, sesso, id_razza, descrizione, affetti, url_img, url_media, car0, car1, car2, car3, car4, car5, Sens, salute_max, forza_max, banca  FROM personaggio WHERE nome='" . gdrcd_filter('in', $_REQUEST['pg']) . "'");
    }
    ?>

    <div class="page_title">
      <h2><?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['page_name']); ?></h2>
    </div>

    <div class="page_body">
    <?php if (isset($_POST['op']) === FALSE) { ?>
        <div class="panels_box">
      <?php
      if ($_SESSION['permessi'] >= MODERATOR) {
        ?>
            <div class="form_gioco">
              <!-- Form utente modifica -->
              <form action="main.php?page=scheda_gst" method="post">

                <div class='form_label'>
        <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['modify_form']['admin']['email']); ?>
                </div>
                <div class='form_field'>
                  <input type="text" name="modifica_email" value="<?php echo $record['email']; ?>" />
                </div>


                <div class='form_label'>
        <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['modify_form']['admin']['gender']); ?>
                </div>
                <div class='form_field'>
                  <select name="modifica_sesso">
                    <option value="m" <?php if ($record['sesso'] == 'm') {
        echo 'selected';
      } ?> />m</option>
                    <option value="f" <?php if ($record['sesso'] == 'f') {
        echo 'selected';
      } ?> />f</option>
                  </select>
                </div>


      <?php $query = "SELECT id_razza, nome_razza FROM razza ORDER BY nome_razza";
      $razza_r = gdrcd_query($query, 'result'); ?>
                <div class='form_label'>
      <?php echo gdrcd_filter('out', $PARAMETERS['names']['race']['sing']); ?>
                </div>
                <div class='form_field'>
                  <select name="modifica_razza">
      <?php while ($razza_row = gdrcd_query($razza_r, 'fetch')) { ?>
                      <option value="<?php echo $razza_row['id_razza']; ?>" <?php if ($razza_row['id_razza'] == $record['id_razza']) {
          echo 'selected';
        } ?> /><?php echo $razza_row['nome_razza']; ?></option>
      <?php
      }
      gdrcd_query($razza_r, 'free');
      ?>
                  </select>
                </div>


                <div class='form_label'>
      <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['modify_form']['admin']['url_img']); ?>
                </div>
                <div class='form_field'>
                  <input type="text" name="modifica_url_img" value="<?php echo $record['url_img']; ?>" class="form_input" />
                </div>

                <div class='form_label'>
      <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['modify_form']['admin']['background']); ?>
                </div>
                <div class='form_field'>
                  <textarea type="textbox" name="modifica_background" class="form_textarea"><?php echo $record['descrizione']; ?></textarea>
                </div>
                <div class="form_info">
                    <?php echo gdrcd_filter('out', $MESSAGE['interface']['help']['bbcode']); ?>
                </div>

                <div class='form_label'>
      <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['modify_form']['admin']['relationships']); ?>
                </div>
                <div class='form_field'>
                  <textarea type="textbox" name="modifica_affetti" class="form_textarea"><?php echo $record['affetti']; ?></textarea>
                </div>
                <div class="form_info">
      <?php echo gdrcd_filter('out', $MESSAGE['interface']['help']['bbcode']); ?>
                </div>

                <div class='form_label'>
                  <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['modify_form']['admin']['url_media']); ?>
                </div>
                <div class='form_field'>
                  <input type="text" name="modifica_url_media" value="<?php echo $record['url_media']; ?>" class="form_input" />
                </div>

                <div class='form_label'>
      <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['modify_form']['admin']['bank']); ?>
                </div>
                <div class='form_field'>
                  <input name="modifica_banca" value="<?php echo $record['banca']; ?>" class="form_input" />
                </div>

                <div class='form_label'>
      <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['modify_form']['admin']['max_hp']); ?>
                </div>
                <div class='form_field'>
                  <input name="modifica_salute_max" value="<?php echo $record['salute_max']; ?>" class="form_input" />
                </div>

                <div class='form_label'>
                  Modifica Punti Forza Massimi
                </div>
                <div class='form_field'>
                  <input name="modifica_forza_max" value="<?php echo $record['forza_max']; ?>" class="form_input" />
                </div>

                <!-- Caratteristiche -->
                <div class="form_label" >
      <?php echo gdrcd_filter('out', $MESSAGE['register']['fields']['stats']); ?>
                </div>
                <div class="form_field" >
                  <table><tr>
                      <td>
                  <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car0']); ?><br />
                        <select name="car0">
      <?php for ($i = 1; $i <= $PARAMETERS['settings']['cars_cap']; $i++) { ?>
                            <option value="<?php echo $i; ?>" <?php if ($record['car0'] == $i) {
          echo 'SELECTED';
        } ?> >
        <?php echo $i; ?>
                            </option>
      <?php } ?>
                        </select>
                      </td>
                      <td>
      <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car1']); ?><br />
                        <select name="car1">
                  <?php for ($i = 1; $i <= $PARAMETERS['settings']['cars_cap']; $i++) { ?>
                            <option value="<?php echo $i; ?>" <?php if ($record['car1'] == $i) {
              echo 'SELECTED';
            } ?> >
        <?php echo $i; ?>
                            </option>
                        <?php } ?>
                        </select>
                      </td>
                      <td>
                            <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car2']); ?><br />
                        <select name="car2">
                          <?php for ($i = 1; $i <= $PARAMETERS['settings']['cars_cap']; $i++) { ?>
                            <option value="<?php echo $i; ?>" <?php if ($record['car2'] == $i) {
                      echo 'SELECTED';
                    } ?> >
                          <?php echo $i; ?>
                            </option>
                          <?php } ?>
                        </select>
                      </td>
                      <td>
                          <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car3']); ?><br />
                        <select name="car3">
      <?php for ($i = 1; $i <= $PARAMETERS['settings']['cars_cap']; $i++) { ?>
                            <option value="<?php echo $i; ?>" <?php if ($record['car3'] == $i) {
          echo 'SELECTED';
        } ?> >
                            <?php echo $i; ?>
                            </option>
                            <?php } ?>
                        </select>
                      </td>
                      <td>
      <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car4']); ?><br />
                        <select name="car4">
                        <?php for ($i = 1; $i <= $PARAMETERS['settings']['cars_cap']; $i++) { ?>
                            <option value="<?php echo $i; ?>" <?php if ($record['car4'] == $i) {
                    echo 'SELECTED';
                  } ?> >
                              <?php echo $i; ?>
                            </option>
                          <?php } ?>
                        </select>
                      </td>
                      <td>
                        <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car5']); ?><br />
                        <select name="car5">
                          <?php for ($i = 1; $i <= $PARAMETERS['settings']['cars_cap']; $i++) { ?>
                            <option value="<?php echo $i; ?>" <?php if ($record['car5'] == $i) {
                      echo 'SELECTED';
                    } ?> >
                            <?php echo $i; ?>
                            </option>
      <?php } ?>
                        </select>
                      </td>
                      <td>
                          <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car6']); ?><br />
                        <select name="car6">
                            <?php for ($i = 1; $i <= $PARAMETERS['settings']['cars_cap']; $i++) { ?>
                            <option value="<?php echo $i; ?>" <?php if ($record['Sens'] == $i) {
                        echo 'SELECTED';
                      } ?> >
        <?php echo $i; ?>
                            </option>
                        <?php } ?>
                        </select>
                      </td>
                    <tr>
                  </table>
                </div>



                <input type="hidden" name="op" value="modify" />
                <input type="hidden"
                       value="<?php echo gdrcd_filter('get', $_REQUEST['pg']); ?>"
                       name="pg" />

                <div class='form_submit'>
                  <input type="submit" value="<?php echo $MESSAGE['interface']['forms']['submit']; ?>" class="form_submit" />
                </div>

              </form>

              <!-- Form resetta abilita' -->
              <script>
                function confermaReset()
                {
                  var c = confirm("Vuoi veramente resettare le abilità del personaggio?");
                  if (c)
                    document.getElementById("resetform").submit();
                }
              </script>
              <form id='resetform' action="main.php?page=scheda_gst" method="post">
                <input type="hidden" name="op" value="abreset" />
                <input type="hidden"
                       value="<?php echo gdrcd_filter('get', $_REQUEST['pg']); ?>"
                       name="pg" />

                <div class='form_submit'>
                  <input type="button" value="Resetta Abilità" class="form_submit" onclick="confermaReset();" />
                </div>
              </form>
            </div>

          </div>

      <?php
    }//if
  }//if
}//else
?>


  </div>
  <!-- Link a piè di pagina -->
  <div class="link_back">
    <a href="main.php?page=scheda&pg=<?php echo gdrcd_filter('get', $_REQUEST['pg']); ?>"><?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['link']['back']); ?></a>
  </div>

</div><!-- pagina -->