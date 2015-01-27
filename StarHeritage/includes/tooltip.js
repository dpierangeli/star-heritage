/** * Script per tooltip
	* @author Blancks
*/

/** * Gestore del box di descrizione
*/
function show_desc(e, txt)
{
	curX = (document.all)? event.clientX + document.body.scrollLeft : e.pageX;
	curY = (document.all)? event.clientY + document.body.scrollTop : e.pageY;
	
	set_fade('descriptionLoc', 0);
	$('descriptionLoc').style.display='block';
        var lft = (parseInt(curX)-parseInt($('maincontent').offsetLeft)+tooltip_offsetX);
	$('descriptionLoc').style.left= (lft)+'px';
	$('descriptionLoc').style.top=(parseInt(curY)-parseInt($('maincontent').offsetTop)+tooltip_offsetY+parseInt($('mainoutput').scrollTop))+'px';
	$('descriptionLoc').innerHTML=txt;
	var rgh = lft+$('descriptionLoc').offsetWidth;
        var space = parseInt($('maincontent').offsetWidth);
        if (rgh>space)
          lft = lft - (rgh-space) - 20;
        $('descriptionLoc').style.left= (lft)+'px';

	start_fade('descriptionLoc', '+');
}


function hide_desc()
{
	$('descriptionLoc').style.display='none';
}
