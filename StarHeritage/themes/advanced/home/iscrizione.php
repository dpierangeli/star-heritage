<div class="pagina_iscrizione">
  <div class="page_title"><h2>
      <?php echo gdrcd_filter('out', $MESSAGE['register']['page_name']); ?>
    </h2></div>
  <div class="page_body">

    <?php /*     * ** Fase 0 *** */
    if (isset($_POST['fase']) === FALSE) {
      ?>

      <div class="panels_box">
        <!-- condizioni d'uso -->
        <div class="disclaimer_iscrizione">
  <?php echo gdrcd_filter('out', $MESSAGE['register']['disclaimer']); ?>
        </div>
        <div class="disclaimer_iscrizione" style="height:300px; overflow-y:scroll; margin: 4px; margin-top:10px; border: 1px solid #D9D9D9; padding: 4px;">
          <?php
          $queryS = "SELECT articolo, titolo, testo FROM regolamento ORDER BY articolo";
          $resultS = gdrcd_query($queryS, 'result');
          while ($row = gdrcd_query($resultS, 'fetch')) {
            ?>
            <div style="padding-top: 6px; font-weight: bold;"><?php echo gdrcd_filter('out', $row['titolo']); ?></div>
            <div style="padding-top: 4px;"><?php echo gdrcd_bbcoder(gdrcd_filter('out', $row['testo'])); ?></div>
            <?php
          }
          gdrcd_query($resultS, 'free');
          ?>
        </div>
        <div class="disclaimer_iscrizione" style="padding-top: 6px; font-weight: bold; padding-bottom: 6px;">
          <?php echo gdrcd_filter('out', $MESSAGE['register']['rules_read']); ?>
        </div>
        <!-- Accetto le condizioni -->
        <div class="form_gioco">
          <form action = "<?php echo $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING']; ?>" method="post">
            <div class="form_submit">
              <input type="hidden" name="fase" value="1" />
              <input type="submit" value="<?php echo gdrcd_filter('out', $MESSAGE['register']['forms']['accept']); ?>" />
            </div>
          </form>
        </div>
        <!-- Non accetto le condizioni -->
        <div class="form_gioco">
          <form action = "index.php" method="post">
            <div class="form_submit">
              <input type="submit" value="<?php echo gdrcd_filter('out', $MESSAGE['register']['forms']['refuse']); ?>" />
            </div>
          </form>
        </div>
      </div>

    <?php } ?>



    <?php /*     * ** Fase 1 *** */
    if (gdrcd_filter('get', $_POST['fase']) == 1) {
      ?>

      <div class="panels_box">
        <div class="form_gioco">
          <div class="form_info" style="padding-top: 10px; font-weight: bold; padding-bottom: 6px; font-size: 15px;">
            <a href="#" target="_new">Guida alla creazione</a>
          </div>
          <form action = "<?php echo $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING']; ?>" method="post">
            <!-- EMail -->
            <div class="form_label" >
  <?php echo gdrcd_filter('out', $MESSAGE['register']['fields']['email']); ?>
            </div>
            <div class="form_field" >
              <input name="email" value="<?php echo gdrcd_filter('email', $_POST['email']); ?>" />
            </div>
            <div class="form_info" >
  <?php echo gdrcd_filter('out', $MESSAGE['register']['fields']['email_info']); ?>
            </div>
            <!-- Nome PG -->
            <div class="form_label" >
  <?php echo gdrcd_filter('out', $MESSAGE['register']['fields']['name']); ?>
            </div>
            <div class="form_field" >
              <input name="nome" value="<?php echo gdrcd_filter('get', $_POST['nome']); ?>" />
            </div>
            <div class="form_info" >
  <?php echo gdrcd_filter('out', $MESSAGE['register']['fields']['name_info']); ?>
            </div>
            <!-- Cognome PG -->
            <div class="form_label" >
  <?php echo gdrcd_filter('out', $MESSAGE['register']['fields']['lastname']); ?>
            </div>
            <div class="form_field" >
              <input name="cognome" value="<?php echo gdrcd_filter('get', $_POST['cognome']); ?>" />
            </div>
            <div class="form_info" >
  <?php echo gdrcd_filter('out', $MESSAGE['register']['fields']['name_info']); ?>
            </div>
            <!-- Genere -->
            <div class="form_label" >
  <?php echo gdrcd_filter('out', $MESSAGE['register']['fields']['gender']); ?>
            </div>
            <div class="form_field" >
              <select name="genere">
                <option value="f" <?php if (gdrcd_filter('get', $_POST['genere']) == 'f') {
    echo 'SELECTED';
  } ?> >
                  <?php echo gdrcd_filter('out', $MESSAGE['register']['fields']['gender_f']); ?>
                </option>
                <option value="m" <?php if (gdrcd_filter('get', $_POST['genere']) == 'm') {
                  echo 'SELECTED';
                } ?> >
              <?php echo gdrcd_filter('out', $MESSAGE['register']['fields']['gender_m']); ?>
                </option>
              </select>
            </div>
            <!-- Razza -->
            <div class="form_label" >
                <?php echo gdrcd_filter('out', $PARAMETERS['names']['race']['sing'] . ' ' . $MESSAGE['register']['fields']['race']); ?>
            </div>
                  <?php $result = gdrcd_query("SELECT id_razza, nome_razza FROM razza WHERE iscrizione=1 ORDER BY nome_razza", 'result'); ?>
            <div class="form_field" >
              <select name="razza">
  <?php while ($row = gdrcd_query($result, 'fetch')) { ?>
                  <option value="<?php echo $row['id_razza']; ?>" <?php if (gdrcd_filter('get', $_POST['razza']) == $row['id_razza']) {
      echo 'SELECTED';
    } ?>>
                  <?php echo gdrcd_filter('out', $row['nome_razza']); ?>
                  </option>
  <?php } ?>
              </select>
            </div>
  <?php if ($PARAMETERS['mode']['racialinfo'] == 'ON') { ?>
              <div class="form_info" >
                <a href="ambientazione.php?page=user_razze" target="_new">
    <?php echo gdrcd_filter('out', $MESSAGE['register']['fields']['race_info']); ?>
                </a>
              </div>
  <?php } ?>
            <!-- Età -->
            <div class="form_label" >
  <?php echo gdrcd_filter('out', $MESSAGE['register']['fields']['years']); ?>
            </div>
            <div class="form_field" >
              <input name="years" value="<?php echo gdrcd_filter('get', $_POST['years']); ?>" />
            </div>
            <div class="form_info" >
  <?php echo gdrcd_filter('out', $MESSAGE['register']['fields']['years_info']); ?>
            </div>
            <!-- Caratteristiche -->
            <div class="form_label" >
  <?php echo gdrcd_filter('out', $MESSAGE['register']['fields']['stats']); ?> (punti spesi: <span id="totalPoints">6</span>)
            </div>
            <script>
              function sumPoints()
              {
                var car0 = document.getElementById('car0');
                var car1 = document.getElementById('car1');
                var car2 = document.getElementById('car2');
                var car3 = document.getElementById('car3');
                var car4 = document.getElementById('car4');
                var car5 = document.getElementById('car5');
                var car6 = document.getElementById('car6');
                var sum = document.getElementById('totalPoints');
                try
                {
                  var somma = parseInt(car0.value) + parseInt(car1.value) + parseInt(car2.value) + parseInt(car3.value) + parseInt(car4.value) + parseInt(car5.value) + parseInt(car6.value);
                  sum.innerHTML = somma;
                }
                catch (e)
                {
                }
              }
            </script>
            <div class="form_field" >
              <table><tr>
                  <td>
  <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car0']); ?><br />
                    <select id="car0" name="car0" style="width:70px;" onchange="sumPoints();">
                    <?php for ($i = 1; $i <= $PARAMETERS['settings']['initial_cars_cap']; $i++) { ?>
                        <option value="<?php echo $i; ?>" <?php if (gdrcd_filter('num', $_POST['car0']) == $i) {
                    echo 'SELECTED';
                  } ?> >
                          <?php echo $i; ?>
                        </option>
                      <?php } ?>
                    </select>
                  </td>
                  <td>
                    <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car1']); ?><br />
                    <select id="car1" name="car1" style="width:70px;" onchange="sumPoints();">
                      <?php for ($i = 1; $i <= $PARAMETERS['settings']['initial_cars_cap']; $i++) { ?>
                        <option value="<?php echo $i; ?>" <?php if (gdrcd_filter('num', $_POST['car1']) == $i) {
                      echo 'SELECTED';
                    } ?> >
                        <?php echo $i; ?>
                        </option>
  <?php } ?>
                    </select>
                  </td>
                  <td>
                      <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car2']); ?><br />
                    <select id="car2" name="car2" style="width:70px;" onchange="sumPoints();">
                        <?php for ($i = 1; $i <= $PARAMETERS['settings']['initial_cars_cap']; $i++) { ?>
                        <option value="<?php echo $i; ?>" <?php if (gdrcd_filter('num', $_POST['car2']) == $i) {
                        echo 'SELECTED';
                      } ?> >
    <?php echo $i; ?>
                        </option>
                    <?php } ?>
                    </select>
                  </td>
                  <td>
                        <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car3']); ?><br />
                    <select id="car3" name="car3" style="width:70px;" onchange="sumPoints();">
                      <?php for ($i = 1; $i <= $PARAMETERS['settings']['initial_cars_cap']; $i++) { ?>
                        <option value="<?php echo $i; ?>" <?php if (gdrcd_filter('num', $_POST['car3']) == $i) {
                      echo 'SELECTED';
                    } ?> >
                      <?php echo $i; ?>
                        </option>
                      <?php } ?>
                    </select>
                  </td>
                  <td>
                      <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car4']); ?><br />
                    <select id="car4" name="car4" style="width:70px;" onchange="sumPoints();">
  <?php for ($i = 1; $i <= $PARAMETERS['settings']['initial_cars_cap']; $i++) { ?>
                        <option value="<?php echo $i; ?>" <?php if (gdrcd_filter('num', $_POST['car4']) == $i) {
      echo 'SELECTED';
    } ?> >
                        <?php echo $i; ?>
                        </option>
                        <?php } ?>
                    </select>
                  </td>
                  <td>
  <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car5']); ?><br />
                    <select id="car5" name="car5" style="width:70px;" onchange="sumPoints();">
  <?php for ($i = 1; $i <= $PARAMETERS['settings']['initial_cars_cap']; $i++) { ?>
                        <option value="<?php echo $i; ?>" <?php if (gdrcd_filter('num', $_POST['car5']) == $i) {
      echo 'SELECTED';
    } ?> >
                <?php echo $i; ?>
                        </option>
  <?php } ?>
                    </select>
                  </td>
                  <td>
  <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car6']); ?><br />
                    <select id="car6" name="car6" style="width:70px;" onchange="sumPoints();">
  <?php for ($i = 1; $i <= $PARAMETERS['settings']['initial_cars_cap']; $i++) { ?>
                        <option value="<?php echo $i; ?>" <?php if (gdrcd_filter('num', $_POST['car6']) == $i) {
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
            <div class="form_info" >
  <?php echo gdrcd_filter('out', $MESSAGE['register']['fields']['stats_info'] . ' ' . $PARAMETERS['settings']['cars_sum']); ?>
            </div>
            <!-- Background opzionale -->
            <div class="form_label" >
              Background
            </div>
            <div class="form_info" >
              Questo campo è opzionale, se non specificato in creazione il Background va inviato in seguito per mail all'indirizzo <?php echo $PARAMETERS['info']['webmaster_email']; ?> per l'approvazione da parte dello staff.
            </div>
            <div class="form_field" >
              <textarea name="bg" ><?php echo gdrcd_filter('get', $_POST['bg']); ?></textarea>
            </div>

            <!-- Invio -->
            <div class="form_submit">
              <input type="hidden" name="fase" value="2" />
              <input type="submit" value="<?php echo gdrcd_filter('out', $MESSAGE['register']['forms']['next']); ?>" />
            </div>


          </form>
        </div>
        <div class="form_gioco">
          <form action = "index.php" method="post">
            <div class="form_submit">
              <input type="submit" value="<?php echo gdrcd_filter('out', $MESSAGE['register']['forms']['abort']); ?>" />
            </div>
          </form>
        </div>
      </div>

      <?php } ?>



      <?php
      /*       * *** Fase 2 **** */
      if (gdrcd_filter('get', $_POST['fase']) == 2) {

        $ok = TRUE;
        ?>

      <div class="panels_box">
        <!-- controllo -->
        <!-- ok/form precompilato -->

        <?php
        //controlli validità
        $result = gdrcd_query("SELECT email, doppioni FROM personaggio WHERE email='" . gdrcd_filter('in', $_POST['email']) . "' LIMIT 1", 'result');
        $dopp = 0;
        while ($row = gdrcd_query($result, 'fetch'))
          $dopp = $row['doppioni'];
//
        if (gdrcd_query($result, 'num_rows') > 0 && $dopp == 0) {
          gdrcd_query($result, 'free');
          $ok = FALSE;
          echo '<div class="error">' . gdrcd_filter('out', $MESSAGE['register']['error']['email_taken']) . '</div>';
        }

        if ((gdrcd_filter('get', $_POST['email']) == '') || (strpos(gdrcd_filter('get', $_POST['email']), '@') == FALSE) || (strpos(gdrcd_filter('get', $_POST['email']), '.') == FALSE)) {
          $ok = FALSE;
          echo '<div class="error">' . gdrcd_filter('out', $MESSAGE['register']['error']['email_needed']) . '</div>';
        }


        $result = gdrcd_query("SELECT nome FROM personaggio WHERE nome='" . gdrcd_capital_letter(gdrcd_filter('get', $_POST['nome'])) . "' LIMIT 1", 'result');

        if (gdrcd_query($result, 'num_rows') > 0) {
          gdrcd_query($result, 'free');
          $ok = FALSE;
          echo '<div class="error">' . gdrcd_filter('out', $MESSAGE['register']['error']['name_taken']) . '</div>';
        }

        if ((gdrcd_filter('num', $_POST['car0']) + gdrcd_filter('num', $_POST['car1']) + gdrcd_filter('num', $_POST['car2']) + gdrcd_filter('num', $_POST['car3']) + gdrcd_filter('num', $_POST['car4']) + gdrcd_filter('num', $_POST['car5']) + gdrcd_filter('num', $_POST['car6'])) != $PARAMETERS['settings']['cars_sum']) {
          $ok = FALSE;
          echo '<div class="error">' . gdrcd_filter('out', $MESSAGE['register']['fields']['stats_info'] . ' ' . $PARAMETERS['settings']['cars_sum']) . '</div>';
        }

        $result = gdrcd_query("SELECT min_eta,max_eta FROM razza WHERE id_razza='" . gdrcd_filter('num', $_POST['razza']) . "' LIMIT 1");
        if (gdrcd_filter('num', $_POST['years']) < gdrcd_filter('num', $result['min_eta'])) {
          $ok = FALSE;
          echo '<div class="error">' . gdrcd_filter('out', $MESSAGE['register']['error']['years_min']) . gdrcd_filter('num', $result['min_eta']) . '</div>';
        }
        if (gdrcd_filter('num', $_POST['years']) > gdrcd_filter('num', $result['max_eta'])) {
          $ok = FALSE;
          echo '<div class="error">' . gdrcd_filter('out', $MESSAGE['register']['error']['years_max']) . gdrcd_filter('num', $result['max_eta']) . '</div>';
        }

        if ($ok == FALSE) {
          ?>

          <div class="form_gioco">
            <form action = "<?php echo $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING']; ?>" method="post">
              <div class="form_submit">
                <input type="hidden" name="fase" value="1" />
                <input type="hidden" name="email" value="<?php echo gdrcd_filter('out', $_POST['email']) ?>" />
                <input type="hidden" name="nome" value="<?php echo gdrcd_filter('out', $_POST['nome']) ?>" />
                <input type="hidden" name="cognome" value="<?php echo gdrcd_filter('out', $_POST['cognome']) ?>" />
                <input type="hidden" name="genere" value="<?php echo gdrcd_filter('out', $_POST['genere']) ?>" />
                <input type="hidden" name="razza" value="<?php echo gdrcd_filter('num', $_POST['razza']) ?>" />
                <input type="hidden" name="years" value="<?php echo gdrcd_filter('num', $_POST['years']) ?>" />
                <input type="hidden" name="car0" value="<?php echo gdrcd_filter('num', $_POST['car0']) ?>" />
                <input type="hidden" name="car1" value="<?php echo gdrcd_filter('num', $_POST['car1']) ?>" />
                <input type="hidden" name="car2" value="<?php echo gdrcd_filter('num', $_POST['car2']) ?>" />
                <input type="hidden" name="car3" value="<?php echo gdrcd_filter('num', $_POST['car3']) ?>" />
                <input type="hidden" name="car4" value="<?php echo gdrcd_filter('num', $_POST['car4']) ?>" />
                <input type="hidden" name="car5" value="<?php echo gdrcd_filter('num', $_POST['car5']) ?>" />
                <input type="hidden" name="car6" value="<?php echo gdrcd_filter('num', $_POST['car6']) ?>" />
                <input type="hidden" name="bg" value="<?php echo gdrcd_filter('out', $_POST['bg']) ?>" />
                <input type="submit" value="<?php echo gdrcd_filter('out', $MESSAGE['register']['forms']['try_again']); ?>" />
              </div>
            </form>
          </div>
    <?php
  } else {

    if ($_POST['genere'] == 'm') {
      $r_gen = 'm';
    } else {
      $r_gen = 'f';
    }


    $razza = gdrcd_query("SELECT sing_" . gdrcd_filter('in', $r_gen) . " AS nome_razza FROM razza WHERE id_razza = " . (0 + gdrcd_filter('num', $_POST['razza'])) . " LIMIT 1");
    ?>
          <div class="elenco_gioco">
            <table>
              <tr><td class='casella_titolo'><div class='titoli_elenco'><?php echo gdrcd_filter('out', $MESSAGE['register']['summary']) ?></div></td></tr>
              <tr><td class='casella_elemento'><div class='elementi_elenco'>
                    <?php echo gdrcd_filter('out', $_POST['email']) ?>
                  </div></td></tr>
              <tr><td class='casella_elemento'><div class='elementi_elenco'>
                    <?php echo gdrcd_filter('out', $_POST['nome']) ?>
                  </div></td></tr>
              <tr><td class='casella_elemento'><div class='elementi_elenco'>
                    <?php echo gdrcd_filter('out', $_POST['cognome']) ?>
                  </div></td></tr>
              <tr><td class='casella_elemento'><div class='elementi_elenco'>
                    <?php echo gdrcd_filter('out', $_POST['genere']) ?>
                  </div></td></tr>
              <tr><td class='casella_elemento'><div class='elementi_elenco'>
                    <?php echo gdrcd_filter('out', $razza['nome_razza']) . '&nbsp;' ?>
                  </div></td></tr>
              <tr><td class='casella_elemento'><div class='elementi_elenco'>
                    <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car0'] . ' ' . $_POST['car0']) ?>
                  </div></td></tr>
              <tr><td class='casella_elemento'><div class='elementi_elenco'>
                    <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car1'] . ' ' . $_POST['car1']) ?>
                  </div></td></tr>
              <tr><td class='casella_elemento'><div class='elementi_elenco'>
    <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car2'] . ' ' . $_POST['car2']) ?>
                  </div></td></tr>
              <tr><td class='casella_elemento'><div class='elementi_elenco'>
    <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car3'] . ' ' . $_POST['car3']) ?>
                  </div></td></tr>
              <tr><td class='casella_elemento'><div class='elementi_elenco'>
    <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car4'] . ' ' . $_POST['car4']) ?>
                  </div></td></tr>
              <tr><td class='casella_elemento'><div class='elementi_elenco'>
    <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car5'] . ' ' . $_POST['car5']) ?>
                  </div></td></tr>
              <tr><td class='casella_elemento'><div class='elementi_elenco'>
    <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car6'] . ' ' . $_POST['car6']) ?>
                  </div></td></tr>
              <tr><td class='casella_elemento'><div class='elementi_elenco'>
    <?php echo gdrcd_filter('out', 'Background ' . $_POST['bg']) ?>
                  </div></td></tr>
            </table>
          </div>
          <div class="form_gioco">
            <form action = "<?php echo $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING']; ?>" method="post">
              <div class="form_submit">
                <input type="hidden" name="fase" value="3" />
                <input type="hidden" name="email" value="<?php echo gdrcd_filter('out', $_POST['email']) ?>" />
                <input type="hidden" name="nome" value="<?php echo gdrcd_filter('out', $_POST['nome']) ?>" />
                <input type="hidden" name="cognome" value="<?php echo gdrcd_filter('out', $_POST['cognome']) ?>" />
                <input type="hidden" name="genere" value="<?php echo gdrcd_filter('out', $_POST['genere']) ?>" />
                <input type="hidden" name="razza" value="<?php echo gdrcd_filter('num', $_POST['razza']) ?>" />
                <input type="hidden" name="years" value="<?php echo gdrcd_filter('num', $_POST['years']) ?>" />
                <input type="hidden" name="car0" value="<?php echo gdrcd_filter('num', $_POST['car0']) ?>" />
                <input type="hidden" name="car1" value="<?php echo gdrcd_filter('num', $_POST['car1']) ?>" />
                <input type="hidden" name="car2" value="<?php echo gdrcd_filter('num', $_POST['car2']) ?>" />
                <input type="hidden" name="car3" value="<?php echo gdrcd_filter('num', $_POST['car3']) ?>" />
                <input type="hidden" name="car4" value="<?php echo gdrcd_filter('num', $_POST['car4']) ?>" />
                <input type="hidden" name="car5" value="<?php echo gdrcd_filter('num', $_POST['car5']) ?>" />
                <input type="hidden" name="car6" value="<?php echo gdrcd_filter('num', $_POST['car6']) ?>" />
                <input type="hidden" name="taglia" value="<?php echo gdrcd_filter('num', $_POST['taglia']) ?>" />
                <input type="hidden" name="bg" value="<?php echo gdrcd_filter('out', $_POST['bg']) ?>" />
                <input type="submit" value="<?php echo gdrcd_filter('out', $MESSAGE['register']['forms']['ok']); ?>" />
              </div>
            </form>
            <form action = "<?php echo $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING']; ?>" method="post">
              <div class="form_submit">
                <input type="hidden" name="fase" value="1" />
                <input type="hidden" name="email" value="<?php echo gdrcd_filter('out', $_POST['email']) ?>" />
                <input type="hidden" name="nome" value="<?php echo gdrcd_filter('out', $_POST['nome']) ?>" />
                <input type="hidden" name="cognome" value="<?php echo gdrcd_filter('out', $_POST['cognome']) ?>" />
                <input type="hidden" name="genere" value="<?php echo gdrcd_filter('out', $_POST['genere']) ?>" />
                <input type="hidden" name="razza" value="<?php echo gdrcd_filter('num', $_POST['razza']) ?>" />
                <input type="hidden" name="years" value="<?php echo gdrcd_filter('num', $_POST['years']) ?>" />
                <input type="hidden" name="car0" value="<?php echo gdrcd_filter('num', $_POST['car0']) ?>" />
                <input type="hidden" name="car1" value="<?php echo gdrcd_filter('num', $_POST['car1']) ?>" />
                <input type="hidden" name="car2" value="<?php echo gdrcd_filter('num', $_POST['car2']) ?>" />
                <input type="hidden" name="car3" value="<?php echo gdrcd_filter('num', $_POST['car3']) ?>" />
                <input type="hidden" name="car4" value="<?php echo gdrcd_filter('num', $_POST['car4']) ?>" />
                <input type="hidden" name="car5" value="<?php echo gdrcd_filter('num', $_POST['car5']) ?>" />
                <input type="hidden" name="car6" value="<?php echo gdrcd_filter('num', $_POST['car6']) ?>" />
                <input type="hidden" name="taglia" value="<?php echo gdrcd_filter('num', $_POST['taglia']) ?>" />
                <input type="hidden" name="bg" value="<?php echo gdrcd_filter('out', $_POST['bg']) ?>" />
                <input type="submit" value="<?php echo gdrcd_filter('out', $MESSAGE['register']['forms']['back']); ?>" />
              </div>
            </form>
          </div>
      <?php } ?>
      </div>

    <?php } ?>



    <?php
    /*     * *** Fase 3 **** */
    if ($_POST['fase'] == 3) {

      if ((gdrcd_filter('num', $_POST['car0']) + gdrcd_filter('num', $_POST['car1']) + gdrcd_filter('num', $_POST['car2']) + gdrcd_filter('num', $_POST['car3']) + gdrcd_filter('num', $_POST['car4']) + gdrcd_filter('num', $_POST['car5']) + gdrcd_filter('num', $_POST['car6'])) != $PARAMETERS['settings']['cars_sum']) {
        echo '<div class="error">' . gdrcd_filter('out', $MESSAGE['register']['fields']['stats_info'] . ' ' . $PARAMETERS['settings']['cars_sum']) . '</div>';
      } else {

        $pass = gdrcd_genera_pass();


        /**         * Se deve scattare l'avviso di cambio password fin dall'iscrizione non segno cambiamenti
         * @author Blancks
         */
        $lastpasschange_field = "";
        $lastpasschange_value = "";

        /**         * Se NON deve scattare l'avviso di cambio password fin dall'iscrizione aggiorno la data di ultimo cambio ad ora
         * @author Blancks
         */
        if ($PARAMETERS['mode']['alert_password_change'] == 'ON' && $PARAMETERS['settings']['alert_password_change']['alert_from_signup'] == 'OFF') {
          $lastpasschange_field = ", ultimo_cambiopass";
          $lastpasschange_value = ", NOW()";
        }

        $TagliaRecord = gdrcd_query("SELECT Taglia, first_step_eta, second_step_eta, bonus_car0, bonus_car1, bonus_car2, bonus_car3, bonus_car4, bonus_car5 FROM razza WHERE id_razza=" . gdrcd_filter('num', $_POST['razza']) . " LIMIT 1");
        $TagliaRaz = gdrcd_filter('num', $TagliaRecord['Taglia']);
        $etaRaz = gdrcd_filter('num', $_POST['years']);
//
        $car0 = gdrcd_filter('num', $_POST['car0']) + gdrcd_filter('num', $TagliaRecord['bonus_car0']);
        $car1 = gdrcd_filter('num', $_POST['car1']) + gdrcd_filter('num', $TagliaRecord['bonus_car1']);
        $car2 = gdrcd_filter('num', $_POST['car2']) + gdrcd_filter('num', $TagliaRecord['bonus_car2']);
        $car3 = gdrcd_filter('num', $_POST['car3']) + gdrcd_filter('num', $TagliaRecord['bonus_car3']);
        $car4 = gdrcd_filter('num', $_POST['car4']) + gdrcd_filter('num', $TagliaRecord['bonus_car4']);
        $car5 = gdrcd_filter('num', $_POST['car5']) + gdrcd_filter('num', $TagliaRecord['bonus_car5']);
        $car6 = gdrcd_filter('num', $_POST['Sens']);
//
        $punti_iniz = gdrcd_filter('num', $PARAMETERS['settings']['first_px']);
        if ($etaRaz > gdrcd_filter('num', $TagliaRecord['second_step_eta'])) {
          $car1 = $car1 - 1;
          $car2 = $car2 - 1;
          $punti_iniz = $punti_iniz * 3;
        } else if ($etaRaz > gdrcd_filter('num', $TagliaRecord['first_step_eta'])) {
          $car1 = $car1 - 1;
          $punti_iniz = $punti_iniz * 2;
        }
//
// Salute: taglia + costrituzione
        $salutecalc = $TagliaRaz + $car1;
//
        gdrcd_query("INSERT INTO personaggio (nome, cognome, pass, data_iscrizione, email, sesso, id_razza, car0, car1, car2, car3, car4, car5, salute, salute_max, soldi, esperienza, Taglia, eta, Sens, forza, forza_max $lastpasschange_field) VALUES ('" . gdrcd_capital_letter(gdrcd_filter('in', $_POST['nome'])) . "', '" . gdrcd_filter('in', $_POST['cognome']) . "', '" . gdrcd_encript($pass) . "', NOW(), '" . gdrcd_filter('in', $_POST['email']) . "', '" . gdrcd_filter('in', $_POST['genere']) . "', " . gdrcd_filter('num', $_POST['razza']) . ", " . gdrcd_filter('num', $_POST['car0']) . ", " . gdrcd_filter('num', $_POST['car1']) . ", " . gdrcd_filter('num', $_POST['car2']) . ", " . gdrcd_filter('num', $_POST['car3']) . ", " . gdrcd_filter('num', $_POST['car4']) . ", " . gdrcd_filter('num', $_POST['car5']) . ", " . $salutecalc . ", " . $salutecalc . ", " . gdrcd_filter('num', $PARAMETERS['settings']['first_money']) . ", " . gdrcd_filter('num', $punti_iniz) . ", " . $TagliaRaz . ", " . $etaRaz . ", " . gdrcd_filter('num', $_POST['car6']) . ", " . gdrcd_filter('num', $_POST['car6']) . ", " . gdrcd_filter('num', $_POST['car6']) . " $lastpasschange_value)");
//
// Ora ci occupiamo delle abilita' di default
//$ab = gdrcd_query("SELECT * FROM clsabilitarazza where ID_RAZ=".gdrcd_filter('num',$_POST['razza']));
        $abTot = gdrcd_filter('num', $PARAMETERS['settings']['first_px']);
        $ab = gdrcd_query("SELECT id_abilita, nome, val_init, ifnull(PRED_VAL, 0) as offs  FROM abilita left join clsabilitarazza on (id_abilita=ID_AB and ID_RAZ=" . gdrcd_filter('num', $_POST['razza']) . ") where (id_razza=-1 or id_razza=" . gdrcd_filter('num', $_POST['razza']) . ")", 'result');
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
          $abVal = $abVal + $record['offs'];
          $abTot = $abTot + gdrcd_rankCost($abVal);
          gdrcd_query("INSERT INTO clgpersonaggioabilita (nome, id_abilita, grado) VALUES ('" . gdrcd_capital_letter(gdrcd_filter('in', $_POST['nome'])) . "', " . $record['id_abilita'] . ", " . $abVal . ")");
        }
        gdrcd_query($ab, 'free');
        gdrcd_query("UPDATE personaggio SET esperienza=" . gdrcd_filter('num', $abTot) . " WHERE nome='" . gdrcd_capital_letter(gdrcd_filter('in', $_POST['nome'])) . "'");
//
        if ($PARAMETERS['mode']['emailconfirmation'] == 'ON') {
          echo '<div class="page_title"><h2>' . gdrcd_filter('out', $MESSAGE['register']['welcome']['message']['ok']) . '</h2></div>';
          echo '<div class="panels_box"><div class="welcome_message">' . gdrcd_filter('out', $MESSAGE['register']['welcome']['message'][0]) . ' <b>' . gdrcd_filter('out', $PARAMETERS['info']['site_name']) . '</b> ' . gdrcd_filter('out', $MESSAGE['register']['welcome']['message'][1]) . '</div><div class="welcome_message">&nbsp;</div><div class="username">' . gdrcd_filter('out', $MESSAGE['register']['welcome']['message'][3]) . ' <b>' . gdrcd_filter('get', $_POST['email']) . '</b></div>';

          $text = $MESSAGE['register']['welcome']['message'][0] . ' ' . $PARAMETERS['info']['site_name'] . "\n\n " . $MESSAGE['register']['welcome']['message'][1] . "\n     " . $MESSAGE['register']['welcome']['message'][2] . "\n\n    " . $MESSAGE['register']['welcome']['message']['user'] . ' ' . gdrcd_filter('get', $_POST['nome']) . "\n" . $MESSAGE['register']['welcome']['message']['pass'] . ' ' . $pass . "\n\n    " . $PARAMETERS['info']['webmaster_name'];

          $subject = $PARAMETERS['info']['site_name'] . ' - Registrazione di ' . gdrcd_filter('get', $_POST['nome']) . ' ' . gdrcd_filter('get', $_POST['cognome']);

          mail(gdrcd_filter('get', $_POST['email']), $subject, $text, 'From: ' . gdrcd_filter('out', $PARAMETERS['info']['webmaster_email']));
        } else {

          echo '<div class="page_title"><h2>' . gdrcd_filter('out', $MESSAGE['register']['welcome']['message']['ok']) . '</h2></div>';
          echo '<div class="panels_box"><div class="welcome_message">' . gdrcd_filter('out', $MESSAGE['register']['welcome']['message'][0]) . ' <b>' . gdrcd_filter('out', $PARAMETERS['info']['site_name']) . '</b> ' . gdrcd_filter('out', $MESSAGE['register']['welcome']['message'][1]) . '</div><div class="welcome_message">' . gdrcd_filter('out', $MESSAGE['register']['welcome']['message'][2]) . '</div><div class="username">' . gdrcd_filter('out', $MESSAGE['register']['welcome']['message']['user']) . ' <b>' . gdrcd_filter('get', $_POST['nome']) . '</b></div><div class="username">' . gdrcd_filter('out', $MESSAGE['register']['welcome']['message']['pass']) . ' <b>' . $pass . '</b></div></div>';
        }

// INVIO BACKGROUND ALLA CONFERMA
        if (gdrcd_filter('out', $_POST['bg']) != "")
          mail($PARAMETERS['info']['webmaster_email'], "Background di " . gdrcd_filter('in', $_POST['nome']), gdrcd_filter('out', $_POST['bg']), 'From: ' . gdrcd_filter('get', $_POST['email']));

        gdrcd_query("INSERT INTO messaggi (mittente, destinatario, spedito, testo) VALUES ('" . gdrcd_filter('out', $PARAMETERS['info']['webmaster_name']) . "', '" . gdrcd_filter('get', $_POST['nome']) . "', NOW(), '" . gdrcd_filter('out', $MESSAGE['register']['welcome']['message'][4]) . "')");
      }//else
      ?>


      <!-- welcome message -->
      <!-- random pass -->
      <!-- invio mail -->
      <!-- inserimento -->

    </div>

    <!-- Torna alla home -->
    <div class="link_back">
      <a href="index.php">
      <?php echo gdrcd_filter('out', $MESSAGE['register']['welcome']['back'] . ' ' . gdrcd_filter('out', strtolower($PARAMETERS['info']['homepage_name']))); ?>
      </a>
    </div>


    <?php } ?>




  <!-- Chiudura finestra iscizione -->
</div>
</div>