<?php session_start();

	header('Content-Type:text/html; charset=UTF-8');
	$last_message = $_SESSION['last_message'];


	//Includio i parametri, la configurazione, la lingua e le funzioni
	require 'includes/constant_values.inc.php';
	require 'config.inc.php';
	require 'vocabulary/'.$PARAMETERS['languages']['set'].'.vocabulary.php';
	require 'includes/functions.inc.php';

	//Eseguo la connessione al database
	$handleDBConnection = gdrcd_connect();
	//Ricevo il tempo di reload
	$i_ref_time = gdrcd_filter_get($_GET['ref']);


/**********************************************************************************/
if((gdrcd_filter_get($_REQUEST['chat'])=='yes')&&(empty($_SESSION['login'])===FALSE))
{
	/*Aggiornamento chat*/
	/*Se ho inviato un azione*/
	if ((gdrcd_filter('get',$_POST['op'])=='take_action')&&(($PARAMETERS['mode']['skillsystem']=='ON')||($PARAMETERS['mode']['dices']=='ON')))
	{
		$actual_healt = gdrcd_query("SELECT salute FROM personaggio WHERE nome = '".$_SESSION['login']."'");

		if (gdrcd_filter('get',$_POST['id_ab'])!='no_skill')
		{
			if ($actual_healt['salute']>0)
			{
				$skill = gdrcd_query("SELECT nome, car, tipo_forza FROM abilita WHERE id_abilita = ".gdrcd_filter('num',$_POST['id_ab'])." LIMIT 1");
        
        $carname = "car".gdrcd_filter('num',$skill['car']);
        if (gdrcd_filter('num',$skill['car']) == 6)
          $carname = 'Sens';
        
				$car = gdrcd_query("SELECT ".$carname." AS car_now, forza, forza_max, ifnull(last_for_update, date_format(adddate(now(),-1), '%Y-%m-%d')) as forz_update FROM personaggio WHERE nome = '".$_SESSION['login']."' LIMIT 1");
        
        if (gdrcd_filter('num',$skill['car']) == 6)
          $bonus = 0;
        else
				  $bonus = gdrcd_query("SELECT SUM(oggetto.bonus_car".gdrcd_filter('num',$skill['car']).") as bonus FROM oggetto JOIN clgpersonaggiooggetto ON clgpersonaggiooggetto.id_oggetto=oggetto.id_oggetto WHERE clgpersonaggiooggetto.nome='".$_SESSION['login']."' AND clgpersonaggiooggetto.posizione > 1");
				
				//$racial_bonus = gdrcd_query("SELECT bonus_car".gdrcd_filter('num',$skill['car'])." AS racial_bonus FROM razza WHERE id_razza IN (SELECT id_razza FROM personaggio WHERE nome='".$_SESSION['login']."')");
        $racial_bonus = 0;
				$rank = gdrcd_query("SELECT grado FROM clgpersonaggioabilita WHERE id_abilita=".gdrcd_filter('num',$_POST['id_ab'])." AND nome='".$_SESSION['login']."' LIMIT 1");
        //
        $potFor = 0;
        $potPen = 0;
        $potSpes = 0;
        $potErr = '';
        if (gdrcd_filter('num',$_POST['for_spes']) != 0)
        {
          if ($skill['tipo_forza']==1)
            $potFor = 1;
          //
          if ($skill['tipo_forza']==2)
          {
            $potFor = gdrcd_filter('num',$_POST['for_spes']);
            if ($potFor > (gdrcd_filter('num', $rank['grado']/20) + 1))
              $potFor = gdrcd_filter('num', $rank['grado']/20) + 1;
          }
          //
          $potSpes = $potFor;
          //
          // Verifica se il PG ha finito i punti
          if ($potFor > gdrcd_filter('num',$car['forza']))
          {
            // Depotenzio il potere
            $potSpes = gdrcd_filter('num',$car['forza']);
            //
            if ($potSpes == 0)
            {
              if ($potFor == 3)
                $potPen = 10;
              if ($potFor == 4)
                $potPen = 20;
              if ($potFor == 5)
                $potPen = 30;
            }
          } 
        }
        //
				if ($PARAMETERS['mode']['dices']=='ON')
				{
					mt_srand((double)microtime()*1000000);
					$maxD = 100;
					if ($_POST['dice'] != 'no_dice')
	          $maxD = (int)$_POST['dice'];
	        //
					$die = mt_rand(1, $maxD);
	        //
	        if ($potFor > 0 && $skill['tipo_forza'] == 1 && ($_POST['dice']=='no_dice' || gdrcd_filter('num',$_POST['dice'])==100))
	          $die = $die + 20;
	        //  
	        if ($potFor > 0 && $skill['tipo_forza'] == 2 && ($_POST['dice']=='no_dice' || gdrcd_filter('num',$_POST['dice'])==100))
	          $die = $die + $potPen;
	        //
	        if ($die <= 0)
	          $die = 1;
	        if ($die > 100)
	          $die = 100;
	        //
					$chat_dice_msg =  gdrcd_filter('in', $MESSAGE['chat']['commands']['use_skills']['die']).' '.gdrcd_filter('num',$die).',';
				}
				else
				{
					$chat_dice_msg = '';
					$die = 0;
				}

				/* gdrcd_query("INSERT INTO chat ( stanza, imgs, mittente, destinatario, ora, tipo, testo ) VALUES (".$_SESSION['luogo'].", '".$_SESSION['sesso'].";".$_SESSION['img_razza']."', '".$_SESSION['login']."', '', NOW(), 'C', '".$_SESSION['login'].' '.gdrcd_filter('in',$MESSAGE['chat']['commands']['use_skills']['uses']).' '.gdrcd_filter('in',$skill['nome']).': '.gdrcd_filter('in',$PARAMETERS['names']['stats']['car'.$skill['car'].'']).' '.gdrcd_filter('num',$car['car_now']+$racial_bonus['racial_bonus']).', '.$chat_dice_msg.' '.gdrcd_filter('in',$MESSAGE['chat']['commands']['use_skills']['ramk']).' '.gdrcd_filter('num',$rank['grado']).', '.gdrcd_filter('in',$MESSAGE['chat']['commands']['use_skills']['items']).' '.gdrcd_filter('num',$bonus['bonus']).', '.gdrcd_filter('in',$MESSAGE['chat']['commands']['use_skills']['sum']).' '.(gdrcd_filter('num',$car['car_now']+$racial_bonus['racial_bonus'])+gdrcd_filter('num',$die)+gdrcd_filter('num',$rank['grado'])+gdrcd_filter('in',$bonus['bonus']))."')"); */
        
        $nomeAB = gdrcd_filter('in',$skill['nome']);
        $gradoAB = gdrcd_filter('num',$rank['grado']);
        $messageCh = $_SESSION['login'].' '.gdrcd_filter('in',$MESSAGE['chat']['commands']['use_skills']['uses']).' '.$nomeAB;
        $hiddentext = ' ('.$gradoAB.')'.' : '.$die.'/'.$gradoAB.' ['.($gradoAB-$die).'] ';
        if ($potFor>0 || $skill['tipo_forza'] == 2)
        {
          //  
          //$hiddentext = $hiddentext.' (pow '.$potFor.')'.$potErr;
          //
          // Nel caso di abilita' di forza il messaggio sarà visibile solo ai master e a chi usa l'abilità
          $messageCh = '{'.$messageCh.' : '.$die.'}';
        }
        else 
        {
          // Add the die (visibile solo ai master ed al PG)
          $messageCh =  $messageCh.' {'.$die.'}';
        }
        gdrcd_query("INSERT INTO chat ( stanza, imgs, mittente, destinatario, ora, tipo, testo, hidden_text ) VALUES (".$_SESSION['luogo'].", '".$_SESSION['sesso'].";".$_SESSION['img_razza']."', '".$_SESSION['login']."', '', NOW(), 'C', '".$messageCh."', '".$hiddentext."')");
        //
        if ($potFor > 0 && $die < $gradoAB)
          gdrcd_query("UPDATE personaggio set forza=forza-".gdrcd_filter('num',$potSpes)." where nome = '".$_SESSION['login']."'");
        else if ($skill['tipo_forza'] == 3)
        {
          // e' passato piu' di un giorno dall'ultimo refresh dei punti forza
          //if ($car['forz_update'] < strftime("%Y-%m-%d") && $die < $gradoAB && $car['forza'] < $car['forza_max'])
          if ($die < $gradoAB && $car['forza'] < $car['forza_max'])
          {
            $rec = gdrcd_filter('num', (gdrcd_filter('num',$car['forza_max'])/100)*$gradoAB);
            if (gdrcd_filter('num',$car['forza'])+$rec > gdrcd_filter('num',$car['forza_max']))
              $rec = gdrcd_filter('num',$car['forza_max'])-gdrcd_filter('num',$car['forza']);
            //
            gdrcd_query("UPDATE personaggio set forza=forza+".gdrcd_filter('num',$rec).", last_for_update=NOW() where nome = '".$_SESSION['login']."'");
          }
        }
	   	}
	   	else
			{
	      			gdrcd_query("INSERT INTO chat ( stanza, imgs, mittente, destinatario, ora, tipo, testo ) VALUES (".$_SESSION['luogo'].", '".$_SESSION['sesso'].";".$_SESSION['img_razza']."', '".$_SESSION['login']."', '".gdrcd_capital_letter(gdrcd_filter('in', $_SESSION['login']))."', NOW(), 'S', '".
		gdrcd_filter('in',$MESSAGE['status_pg']['exausted'])."')");
		
			}
	/** * Tiro su caratteristica
		* @author Blancks
	*/
		} 
		else if (gdrcd_filter('get', $_POST['id_stats']) != 'no_stats' && gdrcd_filter('get',$_POST['dice']) != 'no_dice')
		{
			mt_srand((double)microtime()*1000000);
			$die=mt_rand(1,gdrcd_filter('num', (int)$_POST['dice']));

			$id_stats = explode('_', $_POST['id_stats']);

			$car = gdrcd_query("SELECT car".gdrcd_filter('num',$id_stats[1])." AS car_now FROM personaggio WHERE nome = '".$_SESSION['login']."' LIMIT 1");

			$racial_bonus = gdrcd_query("SELECT bonus_car".gdrcd_filter('num',$id_stats[1])." AS racial_bonus FROM razza WHERE id_razza IN (SELECT id_razza FROM personaggio WHERE nome='".$_SESSION['login']."')");

			gdrcd_query("INSERT INTO chat ( stanza, imgs, mittente, destinatario, ora, tipo, testo ) VALUES (".$_SESSION['luogo'].", '".$_SESSION['sesso'].";".$_SESSION['img_razza']."', '".$_SESSION['login']."', '', NOW(), 'C', '".$_SESSION['login'].' '.gdrcd_filter('in',$MESSAGE['chat']['commands']['use_skills']['uses']).' '.gdrcd_filter('in',$PARAMETERS['names']['stats']['car'.$id_stats[1]]).': '.gdrcd_filter('in',$PARAMETERS['names']['stats']['car'.$id_stats[1].'']).' '.gdrcd_filter('num',$car['car_now']+$racial_bonus['racial_bonus']).', '.gdrcd_filter('in', $MESSAGE['chat']['commands']['use_skills']['die']).' '.gdrcd_filter('num',$die).', '.gdrcd_filter('in',$MESSAGE['chat']['commands']['use_skills']['sum']).' '.(gdrcd_filter('num',$car['car_now']+$racial_bonus['racial_bonus'])+gdrcd_filter('num',$die)+gdrcd_filter('num',$rank['grado'])+gdrcd_filter('in',$bonus['bonus']))."')");
			
		} 
		else if (gdrcd_filter('get',$_POST['dice'])!='no_dice')
		{
		       	mt_srand((double)microtime()*1000000);
	   		$die=mt_rand(1,gdrcd_filter('num',$_POST['dice']));

	   		gdrcd_query("INSERT INTO chat ( stanza, imgs, mittente, destinatario, ora, tipo, testo ) VALUES (".$_SESSION['luogo'].", '".$_SESSION['sesso'].";".$_SESSION['img_razza']."', '".$_SESSION['login']."', '', NOW(), 'D', '".$_SESSION['login'].' '.gdrcd_filter('in',$MESSAGE['chat']['commands']['die']['cast']).gdrcd_filter('num',$_POST['dice']).': '.gdrcd_filter('in',$MESSAGE['chat']['commands']['die']['sum']).' '.gdrcd_filter('num',$die)."')");

    		} 
    		else if (gdrcd_filter('get',$_POST['id_item'])!='no_item')
    		{
			$item=explode('-', gdrcd_filter('in',$_POST['id_item']));
			if ($item[1]==1)
			{
				$query="DELETE FROM clgpersonaggiooggetto WHERE nome ='".$_SESSION['login']."' AND id_oggetto='".gdrcd_filter('num',$item[0])."' LIMIT 1";
	    		} 
	    		elseif ($item[1]>1)
	    		{
            			$query="UPDATE clgpersonaggiooggetto SET cariche = cariche -1 WHERE nome ='".$_SESSION['login']."' AND id_oggetto='".gdrcd_filter('num',$item[0])."' LIMIT 1";
			}
		 	gdrcd_query($query);

			gdrcd_query("INSERT INTO chat ( stanza, imgs, mittente, destinatario, ora, tipo, testo ) VALUES (".$_SESSION['luogo'].", '".$_SESSION['sesso'].";".$_SESSION['img_razza']."', '".$_SESSION['login']."', '', NOW(), 'O', '".$_SESSION['login'].' '.gdrcd_filter('in',$MESSAGE['chat']['commands']['die']['item']).': '.gdrcd_filter('in',$item[2])."')");
		}

	}

/*Se ho inviato un messaggio*/
/**	* Fix controllo per impedire messaggi inviati a vuoto
	* @author Blancks
*/
	if (gdrcd_filter('get',$_POST['op'])=='new_chat_message' && !empty($_POST['message']))
	{

		$actual_healt = gdrcd_query("SELECT salute FROM personaggio WHERE nome = '".$_SESSION['login']."'");
	
		$chat_message=gdrcd_filter('in', gdrcd_angs($_POST['message']));
		$tag_n_beyond=gdrcd_filter('in',$_POST['tag']);
		$type=gdrcd_filter('in',$_POST['type']);
		$first_char=substr($chat_message,0,1);


 		if($type < "5")
 		{ 
 		//E' un messaggio.
		/*Verifico il tipo di messaggio*/
   			if (($type=="4")||($first_char=="@"))
   			{ /*Sussurro*/
				$m_type='S';
				if($type!='4')
				{
 	  				$dest_end = strpos(substr($chat_message, 1), "@");
      					if ($dest_end === FALSE)
      					{
	     				/*Se il destinatario e' mal formattato lo prendo come parlato*/
         					$m_type='P';
	  				} 
	  				else 
	  				{
         					$tag_n_beyond=gdrcd_capital_letter(substr($chat_message, 1, $dest_end));
	    					 $chat_message=substr($chat_message, $dest_end+2);
	  				}
				}//if
				if ($m_type=='S')
				{/*Se il sussurro e' inviato correttamente*/
          $r_check_dest = gdrcd_query("SELECT nome FROM personaggio WHERE DATE_ADD(ultimo_refresh, INTERVAL 2 MINUTE) > NOW() AND ultimo_luogo = ".$_SESSION['luogo']." AND nome = '".$tag_n_beyond."' LIMIT 1", 'result');

          if (gdrcd_query($r_check_dest, 'num_rows') < 1 && $tag_n_beyond != "*")
          {
            $chat_message=$tag_n_beyond.' '.gdrcd_filter('in',$MESSAGE['chat']['whisper']['no']);
            $tag_n_beyond=$_SESSION['login'];
          }      
				} 
				else 
				{ 
					$tag_n_beyond=$_SESSION['tag']; 
				}
   			}
  			else if($first_char == "#")
			{ //Dado
	   			$m_type ='C';

				$chat_message = substr($chat_message, 1);

				if (eregi("^[d]+([0-9])", $chat_message))
				{
		   			$nstring = ereg_replace("[^0-9]", "", $chat_message);
		   			$die = mt_rand(1,(int)$nstring);
		   			$chat_message = "A ".$_SESSION['login']." esce ".$die." su ".$nstring;
				}
				else if (eregi("^([0-9])+(d)+([0-9])", $chat_message))
				{
					$unit = explode('d', $chat_message);
					
					$numero = $unit[0];
					$dado = $unit[1];
					$x = 0;
					$chat_message = "A ".$_SESSION['login']." esce ";
					for($x = 0; $x < $unit[0]; $x++)
					{
			   			$die = rand(1,(int)$dado);
			   			$chat_message .= $die." su ".$dado.", ";
					}
					$chat_message = substr($chat_message, 0, -2);
				}	   
			}						
   			elseif (($type=="1")||($first_char=="+"))
   			{ /*Azione*/
    				if ($actual_healt['salute']>0)
    				{
	   				if ($first_char=="+")
	   				{
	   					$chat_message=substr($chat_message, 1);
	   				}
	  				$m_type='A';
	   				$_SESSION['tag']=$tag_n_beyond;
				} 
				else 
				{
	       	$m_type='S';
					$tag_n_beyond=$_SESSION['login'];
					$chat_message=gdrcd_filter('in',$MESSAGE['status_pg']['exausted']);
				}
   			} 
   			elseif ((($type=="2")||($first_char=="§")||($first_char=="-")||($first_char=="*"))&&($_SESSION['permessi']>=GAMEMASTER)) 
   			{ /*Master*/
				$m_type='M';
				if(($first_char=="§")||($first_char=="-"))
				{
					$chat_message=substr($chat_message, 1);
				}
				if($first_char=="*")
				{
					$chat_message=substr($chat_message, 1); 
					$m_type='I';
				}
   			} 
   			elseif (($type=="3")&&($_SESSION['permessi']>=GAMEMASTER)) 
   			{ /*PNG*/
				$m_type='N';
				$_SESSION['tag']=$tag_n_beyond;
   			} 
   			else if (($type=="0") || (empty($type)===TRUE))
	   		{ /*Parlato*/
				if ($actual_healt['salute']>0)
				{
		  			$m_type='P';
		   			$_SESSION['tag']=$tag_n_beyond;
				} 
				else 
				{
	       				$m_type='S';
		   			$tag_n_beyond=$_SESSION['login'];
		  			$chat_message=gdrcd_filter('in',$MESSAGE['status_pg']['exausted']);
				}
   			} //elseif
   			/*Inserisco il messaggio*/
			gdrcd_query("INSERT INTO chat ( stanza, imgs, mittente, destinatario, ora, tipo, testo ) VALUES (".$_SESSION['luogo'].", '".$_SESSION['sesso'].";".$_SESSION['img_razza']."', '".$_SESSION['login']."', '".gdrcd_capital_letter(gdrcd_filter('in', $tag_n_beyond))."', NOW(), '".$m_type."', '".$chat_message."')");
		} 
		else 
		{ //Altrimenti e' un comando di stanza privata.
			$info = gdrcd_query("SELECT invitati, nome, proprietario FROM mappa WHERE id=".$_SESSION['luogo']."");

   			$ok_command=FALSE;
   			if($info['proprietario']==$_SESSION['login'])
   			{
   				$ok_command=TRUE;
   			}
   			if(strpos($_SESSION['gilda'],$info['proprietario'])!=FALSE)
   			{
   				$ok_command=TRUE;
   			}
	   		if (($type=="5")&&($ok_command===TRUE))
	   		{ //invita
				gdrcd_query("UPDATE mappa SET invitati = '".$info['invitati'].','.gdrcd_capital_letter(strtolower(gdrcd_filter('in', $tag_n_beyond)))."' WHERE id=".$_SESSION['luogo']." LIMIT 1");
		
				gdrcd_query("INSERT INTO chat ( stanza, mittente, destinatario, ora, tipo, testo ) VALUES (".$_SESSION['luogo'].", 'System message', '".$_SESSION['login']."', NOW(), 'S', '".gdrcd_capital_letter(gdrcd_filter('in', $tag_n_beyond)).' '.$MESSAGE['chat']['warning']['invited']."')");

				if(empty($_POST['tag'])===FALSE)
			   	{
					gdrcd_query("INSERT INTO messaggi ( mittente, destinatario, spedito, letto, testo ) VALUES ('System message', '".gdrcd_capital_letter(gdrcd_filter('in',$_POST['tag']))."', NOW(), 0,  '".$_SESSION['login'].' '.$MESSAGE['chat']['warning']['invited_message'].' '.$info['nome']."')");
			   	}
   			} 
   			else if (($type=="6")&&($ok_command===TRUE))
   			{ //caccia
       				$scaccia=str_replace(','.gdrcd_capital_letter(gdrcd_filter('in', $tag_n_beyond)), '',$info['invitati']);
	   			gdrcd_query("UPDATE mappa SET invitati = '".$scaccia."' WHERE id=".$_SESSION['luogo']." LIMIT 1");

	   			gdrcd_query("INSERT INTO chat ( stanza, mittente, destinatario, ora, tipo, testo ) VALUES (".$_SESSION['luogo'].", 'System message', '".$_SESSION['login']."', NOW(), 'S', '".gdrcd_capital_letter(gdrcd_filter('in', $tag_n_beyond)).' '.$MESSAGE['chat']['warning']['expelled']."')");

   			} 
   			else if ($ok_command===TRUE)
   			{ //elenco
			       	$ospiti=str_replace(',', '', $info['invitati']);
       				gdrcd_query("INSERT INTO chat ( stanza, mittente, destinatario, ora, tipo, testo ) VALUES (".$_SESSION['luogo'].", 'System message', '".$_SESSION['login']."', NOW(), 'S', '".$MESSAGE['chat']['warning']['list'].': '.$ospiti."')");
   			}//else
 		}//else
	}
	else//if(op)
	{
		$_SESSION['tag'] = gdrcd_filter('in',$_POST['tag']);
	}
  
	/*Carico i nuovi messaggi*/
	if(empty($last_message)) $last_message = 0;

  
  
/** * Scorrimento dei messaggi in chat, verifico se non è stato invertito il flusso, in caso modifico l'ordinamento della query
	* @author Blancks
*/
	$typeOrder = 'ASC';

	if ($PARAMETERS['mode']['chat_from_bottom']=='ON')
	{
		$typeOrder = 'DESC';
	}	
	
/** * Controllo per impedire il print in chat delle azioni dei precedenti proprietari di una stanza privata
	* Per stanze non private ora_prenotazione equivarrà ad un tempo sempre inferiore all'orario dell'azione inviata
	* facendo risultare quindi sempre veritiero il controllo in questo caso.

	* @author Blancks
*/
	$query= gdrcd_query("	SELECT chat.id, chat.imgs, chat.mittente, chat.destinatario, chat.tipo, chat.ora, chat.testo, chat.hidden_text, personaggio.url_img_chat, personaggio.url_img, mappa.ora_prenotazione
						FROM chat
						INNER JOIN mappa ON mappa.id = chat.stanza
						LEFT JOIN personaggio ON personaggio.nome = chat.mittente
						WHERE stanza = ".$_SESSION['luogo']." AND chat.ora > IFNULL(mappa.ora_prenotazione, '0000-00-00 00:00:00') AND DATE_SUB(NOW(), INTERVAL 90 MINUTE) < ora ORDER BY id ". $typeOrder, 'result');
// WHERE chat.id > ".$last_message." AND stanza = 
	while ($row = gdrcd_query($query, 'fetch'))
	{
		if ($PARAMETERS['mode']['chaticons']=='ON')
		{
			$icone_chat=explode(";",gdrcd_filter('out', $row['imgs']));
			$add_icon = '<span class="chat_icons"> <img class="presenti_ico" src="themes/'.$PARAMETERS['themes']['current_theme'].'/imgs/races/'.$icone_chat[1].'"><img class="presenti_ico" src="imgs/icons/testamini'.$icone_chat[0].'.png"> </span>';
		}

		switch ($row['tipo'])
		{
		  // PARLATO
			case 'P':
        
				/**	* Fix problema visualizzazione spazi vuoti con i sussurri
					* @author eLDiabolo
				*/
				$add_chat.= '<div class="chat_row_'.$row['tipo'].'">';

				/** * Avatar di chat
					*@author Blancks
				*/
				if ($PARAMETERS['mode']['chat_avatar']=='ON' && !empty($row['url_img_chat']))
				{
					$add_chat .='<img src="'.$row['url_img_chat'].'" class="chat_avatar" style="width:'.$PARAMETERS['settings']['chat_avatar']['width'].'px; height:'.$PARAMETERS['settings']['chat_avatar']['height'].'px;" />';
				}


				$add_chat.= '<span class="chat_time">'.gdrcd_format_time($row['ora']).'</span>';

				if ($PARAMETERS['mode']['chaticons']=='ON')
				{
					$add_chat.= $add_icon;
				}

				$add_chat.= '<span class="chat_name"><a href="#" onclick="Javascript: document.getElementById(\'tag\').value=\''.$row['mittente'].'\'; document.getElementById(\'type\')[2].selected = \'1\'; document.getElementById(\'message\').focus();">'.$row['mittente'].'</a>';

				if(empty ($row['destinatario']) === FALSE )
				{
					$add_chat.= '<span class="chat_tag"> ['.gdrcd_filter('out',$row['destinatario']).']</span>';
				}

				$add_chat.=': </span> ';
				$add_chat.= '<span class="chat_msg">'.gdrcd_chatme($_SESSION['login'], gdrcd_chatcolor(gdrcd_filter('out',$row['testo']))).'</span>';

					/**	* Fix problema visualizzazione spazi vuoti con i sussurri
						* @author eLDiabolo
					*/
					if ($PARAMETERS['mode']['chat_avatar']=='ON')
						$add_chat .= '<br style="clear:both;" />';

					$add_chat.= '</div>';

			break;

      // AZIONE
			case 'A':
				/**	* Fix problema visualizzazione spazi vuoti con i sussurri
					* @author eLDiabolo
				*/
				$add_chat.= '<div class="chat_row_'.$row['tipo'].'">';

				/** * Avatar di chat
					*@author Blancks
				*/
				if ($PARAMETERS['mode']['chat_avatar']=='ON')
				{
          if (!empty($row['url_img_chat']))
          {
					  $add_chat .='<img src="'.$row['url_img_chat'].'" class="chat_avatar" style="width:'.$PARAMETERS['settings']['chat_avatar']['width'].'px; height:'.$PARAMETERS['settings']['chat_avatar']['height'].'px;" />';
          }
          else
          {
            $add_chat .='<img src="'.$row['url_img'].'" class="chat_avatar" style="width:'.$PARAMETERS['settings']['chat_avatar']['width'].'px; height:'.$PARAMETERS['settings']['chat_avatar']['height'].'px;" />';
          }
				}


				$add_chat.= '<span class="chat_time">'.gdrcd_format_time($row['ora']).'</span>';

				if ($PARAMETERS['mode']['chaticons']=='ON')
				{
					$add_chat.= $add_icon;
				}

				$add_chat.= '<span class="chat_name"><a href="#" onclick="Javascript: document.getElementById(\'tag\').value=\''.$row['mittente'].'\';  document.getElementById(\'type\')[2].selected = \'1\'; document.getElementById(\'message\').focus();">'.$row['mittente'].'</a>';

				if(empty ($row['destinatario']) === FALSE )
				{
					$add_chat.= '<span class="chat_tag"> ['.gdrcd_filter('out',$row['destinatario']).']</span>';
				}
				$add_chat.='</span> ';
				$add_chat.= '<span class="chat_msg">'.gdrcd_chatme($_SESSION['login'], gdrcd_chatcolor(gdrcd_filter('out',$row['testo']))).'</span>';
        
        // Il mittente ed i Master vedono il testo contenuto tra { }, gli altri no
        if ($_SESSION['login']!=$row['mittente'] && $_SESSION['permessi']<=GAMEMASTER)
        {
          $add_chat = gdrcd_hidechat($add_chat);
        }

					/**	* Fix problema visualizzazione spazi vuoti con i sussurri
						* @author eLDiabolo
					*/
					if ($PARAMETERS['mode']['chat_avatar']=='ON')
						$add_chat .= '<br style="clear:both;" />';

					$add_chat.= '</div>';

			break;

      // SUSSURRO
			case 'S':
				if ($_SESSION['login']==$row['destinatario'] ) // || $row['destinatario']="*")
				{
					/**	* Fix problema visualizzazione spazi vuoti con i sussurri
						* @author eLDiabolo
					*/
					$add_chat.= '<div class="chat_row_'.$row['tipo'].'">';

					$add_chat.= '<span class="chat_name">'.$row['mittente'].' '.$MESSAGE['chat']['whisper']['by'].': </span> ';
					$add_chat.= '<span class="chat_msg">'.gdrcd_filter('out',$row['testo']).'</span>';

				/**	* Fix problema visualizzazione spazi vuoti con i sussurri
					* @author eLDiabolo
				*/
					$add_chat.= '</div>';

				} else if ($_SESSION['login']==$row['mittente'])
				{
					/**	* Fix problema visualizzazione spazi vuoti con i sussurri
						* @author eLDiabolo
					*/
					$add_chat.= '<div class="chat_row_'.$row['tipo'].'">';

					$add_chat.= '<span class="chat_msg">'.$MESSAGE['chat']['whisper']['to'].' '.gdrcd_filter('out',$row['destinatario']).': </span>';
					$add_chat.= '<span class="chat_msg">'.gdrcd_filter('out',$row['testo']).'</span>';

					/**	* Fix problema visualizzazione spazi vuoti con i sussurri
						* @author eLDiabolo
					*/
					$add_chat.= '</div>';

				} else if (($_SESSION['permessi']>=MODERATOR)&&($PARAMETERS['mode']['spyprivaterooms']=='ON'))
				{
					/**	* Fix problema visualizzazione spazi vuoti con i sussurri
						* @author eLDiabolo
					*/
					$add_chat.= '<div class="chat_row_'.$row['tipo'].'">';

					$add_chat.= '<span class="chat_msg">'.$row['mittente'].' '.$MESSAGE['chat']['whisper']['from_to'].' '.gdrcd_filter('out',$row['destinatario']).' </span>';
					$add_chat.= '<span class="chat_msg">'.gdrcd_filter('out',$row['testo']).'</span>';

					/**	* Fix problema visualizzazione spazi vuoti con i sussurri
						* @author eLDiabolo
					*/
					$add_chat.= '</div>';

				}
			break;

      // PNG
			case 'N':
				/**	* Fix problema visualizzazione spazi vuoti con i sussurri
					* @author eLDiabolo
				*/
				$add_chat.= '<div class="chat_row_'.$row['tipo'].'">';

				$add_chat.= '<span class="chat_time">'.gdrcd_format_time($row['ora']).'</span>';
				$add_chat.= '<span class="chat_name">'.$row['destinatario'].'</span> ';
				$add_chat.= '<span class="chat_msg">'.gdrcd_chatcolor(gdrcd_filter('out',$row['testo'])).'</span>';

				/**	* Fix problema visualizzazione spazi vuoti con i sussurri
					* @author eLDiabolo
				*/
				$add_chat.= '</div>';
			break;

      // MASTER
			case 'M':
				/**	* Fix problema visualizzazione spazi vuoti con i sussurri
					* @author eLDiabolo
				*/
				$add_chat.= '<div class="chat_row_'.$row['tipo'].'">';

				$add_chat.= '<span class="chat_master">'.gdrcd_chatme_master($_SESSION['login'], gdrcd_filter('out',$row['testo'])).'</span>';

				/**	* Fix problema visualizzazione spazi vuoti con i sussurri
					* @author eLDiabolo
				*/
				$add_chat.= '</div>';
			break;

      // OGGETTO ?
			case 'I':
				/**	* Fix problema visualizzazione spazi vuoti con i sussurri
					* @author eLDiabolo
				*/
				$add_chat.= '<div class="chat_row_'.$row['tipo'].'">';

				$add_chat.= '<img class="chat_img" src="'.gdrcd_filter('out',$row['testo']).'" />';

				/**	* Fix problema visualizzazione spazi vuoti con i sussurri
					* @author eLDiabolo
				*/
				$add_chat.= '</div>';
			break;

      // ABILITA'
			case 'C':
				/**	* Fix problema visualizzazione spazi vuoti con i sussurri
					* @author eLDiabolo
				*/
        $textToStamp = gdrcd_filter('out',$row['testo']);
        //
        // Il mittente ed i Master vedono il testo contenuto tra { }, gli altri no
        if ($_SESSION['login']!=$row['mittente'] && $_SESSION['permessi']<=GAMEMASTER)
        {
          $textToStamp = gdrcd_hidechat($textToStamp);
        }
        //
        // Solo i master vedono il testo nascosto
        if ($_SESSION['permessi']>=MODERATOR)
          $textToStamp = $textToStamp.gdrcd_filter('out',$row['hidden_text']);  
        //
        if ($textToStamp != "")
        {
          $add_chat.= '<div class="chat_row_'.$row['tipo'].'">';
          $add_chat.= '<span class="chat_time">'.gdrcd_format_time($row['ora']).'</span>';
          $add_chat.= '<span class="chat_msg">'.$textToStamp.'</span>';

          /**	* Fix problema visualizzazione spazi vuoti con i sussurri
            * @author eLDiabolo
          */
          $add_chat.= '</div>';
        }
			break;


			case 'D':
				/**	* Fix problema visualizzazione spazi vuoti con i sussurri
					* @author eLDiabolo
				*/
				$add_chat.= '<div class="chat_row_'.$row['tipo'].'">';

				$add_chat.= '<span class="chat_time">'.gdrcd_format_time($row['ora']).'</span>';
				$add_chat.= '<span class="chat_msg">'.gdrcd_filter('out',$row['testo']).'</span>';

				/**	* Fix problema visualizzazione spazi vuoti con i sussurri
					* @author eLDiabolo
				*/
				$add_chat.= '</div>';
			break;


			case 'O':
				/**	* Fix problema visualizzazione spazi vuoti con i sussurri
					* @author eLDiabolo
				*/
				$add_chat.= '<div class="chat_row_'.$row['tipo'].'">';

				$add_chat.= '<span class="chat_time">'.gdrcd_format_time($row['ora']).'</span>';
				$add_chat.= '<span class="chat_msg">'.gdrcd_filter('out',$row['testo']).'</span>';

				/**	* Fix problema visualizzazione spazi vuoti con i sussurri
					* @author eLDiabolo
				*/
				$add_chat.= '</div>';
			break;
		}
    
		if ($row['id'] > (int)$last_message)
				$last_message=$row['id'];
    
	}
	gdrcd_query($query, 'free');
	$_SESSION['last_message']=$last_message;
}//if
/******************************************************************************************/
?>
<html>
<head>

  <?php
if(gdrcd_filter('get',$_REQUEST['chat'])=='yes')
{
	echo '<script type="text/javascript"> function echoChat(){';

	/** * Gestione dell'ordinamento
		* @author Blancks
	*/
	if ($PARAMETERS['mode']['chat_from_bottom']=='OFF')
	{
		echo 'parent.document.getElementById(\'pagina_chat\').innerHTML= \''.str_replace(array('@n@'),'<br>',addslashes($add_chat)).'\';';
		echo 'scrolling = parent.document.getElementById(\'pagina_chat\').scrollHeight;';

	}
	elseif ($PARAMETERS['mode']['chat_from_bottom']=='ON')
	{
		echo 'parent.document.getElementById(\'pagina_chat\').innerHTML= \''.str_replace(array('@n@'),'<br>',addslashes($add_chat)).'\'+parent.document.getElementById(\'pagina_chat\').innerHTML;';
		echo 'scrolling = 0;';
	}


	/** * Gestione intelligente della scrollbar
		* Forza lo scroll solo quando ci sono nuovi messaggi
		* @author Blancks
	*/
	if (!empty($add_chat))
			echo 'parent.document.getElementById(\'pagina_chat\').scrollTop = scrolling;';


	if ((gdrcd_filter('get',$_POST['op'])=='take_action')||(gdrcd_filter('get',$_POST['op'])=='new_chat_message'))
	{
      		if($PARAMETERS['mode']['skillsystem']=='ON')
      		{
         		echo 'parent.document.getElementById(\'chat_form_actions\').reset();';
     		}
     		echo 'parent.document.getElementById(\'chat_form_messages\').reset();
	         parent.document.getElementById(\'chat_form_messages\').elements["tag"].value=\''.$_SESSION["tag"].'\';';
	}//if
	echo '}</script>';
} 
?>
   <!--meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"-->
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   <meta http-equiv="refresh" content="<?php echo $i_ref_time; ?>">

   <link rel="stylesheet" href="../themes/<?php echo $PARAMETERS['themes']['current_theme'];?>/presenti.css" TYPE="text/css">
   <link rel="stylesheet" href="../themes/<?php echo $PARAMETERS['themes']['current_theme'];?>/main.css" TYPE="text/css">
   <link rel="stylesheet" href="../themes/<?php echo $PARAMETERS['themes']['current_theme'];?>/chat.css" TYPE="text/css">
</head>
<body class="transparent_body" <?php if(gdrcd_filter('get',$_REQUEST['chat'])=='yes'){ echo 'onLoad="echoChat();"';} ?> >
<?php
    //controlla sessione
	//controlla esilio
?>
