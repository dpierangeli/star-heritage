<div class="pagina_gestione_abilita">
  <?php
  /* HELP: */

  /* Controllo permessi utente */
  if ($_SESSION['permessi'] < MODERATOR) {
    echo '<div class="error">' . gdrcd_filter('out', $MESSAGE['error']['not_allowed']) . '</div>';
  } else {
    ?>

    <!-- Titolo della pagina -->
    <div class="page_title">
      <h2>Gestione mercato nero</h2>
    </div>

    <!-- Corpo della pagina -->
    <div class="page_body">
      <?php
// Gestione operazione di conferma
      if (isset($_POST['op'])) {
        if ($_POST['op'] == 'add') {
          $query_load = "SELECT accesso FROM mercato_nero WHERE personaggio = '" . gdrcd_filter('in', $_POST['add_pg']) . "'";
          $result_load = gdrcd_query($query_load, 'result');
          if (gdrcd_query($result_load, 'num_rows') > 0) {
            // Update to set accesso = 1
            gdrcd_query("update mercato_nero set accesso = 1 where personaggio = '" . gdrcd_filter('in', $_POST['add_pg']) . "'");
          } else {
            // Insert into table with accesso = 1
            gdrcd_query("insert into mercato_nero (personaggio, accesso) values ('" . gdrcd_filter('in', $_POST['add_pg']) . "', 1)");
          }
          gdrcd_query($result_load, 'free');
        } else if ($_POST['op'] == 'remove') {
          gdrcd_query("delete from mercato_nero where personaggio = '" . gdrcd_filter('in', $_POST['del_pg']) . "' ");
        }
      }
      ?>
      <div>
        <h2>Aggiungi personaggio:</h2>
        <?php
        $characters = gdrcd_query("SELECT nome FROM personaggio ORDER BY nome", 'result');
        ?>
        <div class='form_field'>
          <?php if (gdrcd_query($characters, 'num_rows') > 0) { ?>
            <form action="main.php?page=gestione_mercatonero" method="post">
              <input type="hidden" name="op" value="add" />
              <select name="add_pg">
                <?php while ($option = gdrcd_query($characters, 'fetch')) { ?>
                  <option value="<?php echo $option['nome']; ?>">
                    <?php echo gdrcd_filter('out', $option['nome']); ?>
                  </option>
                  <?php
                }
                ?>
              </select>
              <input type="submit" name="add" value="AGGIUNGI" />
            </form>
          <?php
          }
          gdrcd_query($characters, 'free');
          ?>
        </div>
      </div>
      <div class="page_title" style="margin-top:10px;">
        <h2>Personaggi con accesso</h2>
      </div>
      <table>
        <?php
        $result = gdrcd_query("SELECT * FROM mercato_nero where accesso = 1", 'result');
        while ($row = gdrcd_query($result, 'fetch')) {
          ?>
          <tr>
            <td>
    <?php echo $row["personaggio"]; ?>
            </td>
            <td>
              <form action="main.php?page=gestione_mercatonero" method="post">
                <input type="hidden" name="op" value="remove" />
                <input type="hidden" name="del_pg" value="<?php echo $row["personaggio"]; ?>" />
                <input type="submit" name="del" value="RIMUOVI" />
              </form>
            </td>
          </tr>
          <?php
        }
        gdrcd_query($result, 'free');
        ?>
      </table>
    </div>
<?php } ?>
</div>