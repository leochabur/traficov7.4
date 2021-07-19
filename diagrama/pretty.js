
/*
 * Superfish v1.4.8 - jQuery menu widget
 * Copyright (c) 2008 Joel Birch
 *
 * Dual licensed under the MIT and GPL licenses:
 * 	http://www.opensource.org/licenses/mit-license.php
 * 	http://www.gnu.org/licenses/gpl.html
 *
 * CHANGELOG: http://users.tpg.com.au/j_birch/plugins/superfish/changelog.txt
 */

function ini() {
  tab=document.getElementById('tabla');
  for (i=0; ele=tab.getElementsByTagName('td')[i]; i++) {
    ele.onmouseover = function() {iluminar(this,true)}
    ele.onmouseout = function() {iluminar(this,false)}
  }
}

function iluminar(obj,valor) {
  fila = obj.parentNode;

    for (i=0; ele = fila.getElementsByTagName('td')[i]; i++)
      ele.style.background = (valor) ? '#B9F8F8' : '';
}