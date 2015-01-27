<?php 
/** * Skin Advanced
	* Markup e procedure della homepage
	* @author Blancks
*/
	
?><div id="main">

	<div id="site_width">

		<div id="header">
			<div class="login_form">
				<form action="login.php" id="do_login" method="post"<?php if ($PARAMETERS['mode']['popup_choise']=='ON'){ echo ' onsubmit="check_login(); return false;"'; } ?>>
					<div>
						<span class="form_label"><label for="username"><?php echo $MESSAGE['homepage']['forms']['username'];?></label></span>
						<input type="text" id="username" name="login1" />
					</div>
					<div>
						<span class="form_label"><label for="password"><?php echo $MESSAGE['homepage']['forms']['password'];?></label></span>
						<input type="password" id="password" name="pass1" />
					</div>
<?php 	if ($PARAMETERS['mode']['popup_choise']=='ON'){ ?>
					<div>
						<span class="form_label"><label for="allow_popup"><?php echo $MESSAGE['homepage']['forms']['open_in_popup'];?></label></span>
						<input type="checkbox" id="allow_popup" />
						<input type="hidden" value="0" name="popup" id="popup">
					</div>
<?php	}	?>
					<input type="submit" value="<?php echo $MESSAGE['homepage']['forms']['login'];?>" />
				</form>
			</div>
			
			<h1><img src="imgs/LogoSmall.png" style="width:600px; height:165px" /></h1>
		</div>
<script>
function handleWiki()
{
  var vk = document.getElementById('wikilist');
  if (vk.style.display=="none")
    showWiki(vk);
  else
    hideWiki(vk);
}
function showWiki(vk)
{
  vk.style.display = "";
}
function hideWiki(vk)
{
  vk.style.display = "none";
}
</script>

		<div id="content">
	
			<div class="sidecontent">
				<ul>
          <li><a href="index.php?page=index&content=user_regolamento"><?php echo 'Regolamento';?></a></li>
					<li>
            <a href="#" onclick="handleWiki();"><?php echo 'Wiki';?></a>
            <ul id="wikilist" style="display: <?php if ($_REQUEST['content']=='user_ambientazione' || $_REQUEST['content']=='user_razze') echo ''; else echo 'none';?>; margin:0px; margin-left:10px; padding:2px;">
              <li><a href="index.php?page=index&content=user_ambientazione" style="font-size:14px;" ><?php echo $MESSAGE['homepage']['storyline'];?></a></li>
              <li><a href="index.php?page=index&content=user_razze" style="font-size:14px;" ><?php echo $MESSAGE['homepage']['races'];?></a></li>
              <li><a href="#" onclick="window.open('Guida.pdf','Guida',true);" style="font-size:14px;" >Guida (PDF)</a></li>
            </ul>
          </li>
          <li><a href="index.php?page=index&content=iscrizione"><?php echo $MESSAGE['homepage']['registration'];?></a></li>
          <li><a href="mailto:<?php echo gdrcd_filter('out',$PARAMETERS['info']['webmaster_email']); ?>">Contattaci</a></li>
				</ul>
				
				<div class="side_modules">
					<strong><?php echo $users['online'], ' ', gdrcd_filter('out',$MESSAGE['homepage']['forms']['online_now']); ?></strong>
				</div>
				
				<div class="side_modules">
<?php	if (empty($RP_response)){ ?>
					<strong><?php echo gdrcd_filter('out',$MESSAGE['homepage']['forms']['forgot']);?></strong>
					
					<div class="pass_rec">
						<form action="index.php" method="post">
							<div>
								<span class="form_label"><label for="passrecovery"><?php echo $MESSAGE['homepage']['forms']['email'];?></label></span>
								<input type="text" id="passrecovery" name="email" />
							</div>
							<input type="submit" value="<?php echo $MESSAGE['homepage']['forms']['new_pass'];?>" />
						</form>
					</div>
<?php	}else{ ?>
					<div class="pass_rec">
						<?php echo $RP_response; ?>
					</div>
<?php	} ?>
				</div>
				
				<div class="side_modules">
						<?php include 'themes/'. $PARAMETERS['themes']['current_theme'] .'/home/user_stats.php'; ?>
				</div>
			</div>
			
			<div class="content_body">
			
<?php

		if (file_exists('themes/'. $PARAMETERS['themes']['current_theme'] .'/home/' . $content . '.php'))
				include 'themes/'. $PARAMETERS['themes']['current_theme'] .'/home/' . $content . '.php';


?>
			
			</div>
			
			<br class="blank" />
	
		</div>
	
	
		<div id="footer">
	
			<div>
      <p>Gioco di ruolo online (GdR) amatoriale, gratuito e senza scopo di lucro. L'iscrizione è subordinata al compimento del 18° anno di età. Richiede  Javascript e Cookies attivi. Non si intende ledere alcun copyright, tutti i diritti su <b>Star Wars</b> e tutti i marchi ad esso correlati sono proprietà della <b>Disney</b>.</p>
				<p><?php echo gdrcd_filter('out',$PARAMETERS['info']['site_name']),' - ', gdrcd_filter('out',$MESSAGE['homepage']['info']['email']), ': <a href="mailto:', gdrcd_filter('out',$PARAMETERS['info']['webmaster_email']), '">', gdrcd_filter('out',$PARAMETERS['info']['webmaster_email']), '</a>.'; ?></p>
				<p><?php echo $CREDITS, ' ', $LICENCE ?></p>
			</div>
			
		</div>

	</div>

</div>