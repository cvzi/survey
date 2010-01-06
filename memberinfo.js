/** 
 *  memberinfo.js
 * 
 *        survey
 * 
 *   UTF-8 encoded
 * 
 *   survey is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 * 
 *   survey is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 * 
 *   You should have received a copy of the GNU General Public License
 *   along with survey; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * 
 *   For questions contact
 *   cuzi@openmail.cc
 * 
 *  @copyright 2010 cuzi
 *  @author cuzi@openmail.cc
 *  @package survey
 *  @version 2.0
 *  @license http://gnu.org/copyleft/gpl.html GNU GPL

 */

function memberinfo(ev) {

    var overlay = new Element('div').inject(document.body);
    overlay.set('styles',{
        position:'fixed',
        top:'0px',
        left:'0px',
        right:'0px',
        bottom:'0px',
        background:'url('+baseurl+'images/33.png)'
    });

    var coords = $(document.body).getCoordinates();

    var div = new Element('div').inject(document.body);
    div.set('styles',{
        position:'fixed',
        minHeight:'200px',
        minWidth:'400px',
        left:(coords.width - 400) /2,
        right:(coords.width - 400) /2,
        top:(coords.height - 400) /2,
        background:'White',
        padding:30,
        border:'solid 10px #1e466f',
        MozBorderRadius:'40px',
        WebkitBorderRadius:'40px',
        borderRadius:'40px'

    });

    div.appendChild(document.createTextNode('Info: '));
    div.appendChild(new Element('br'));
    var textarea = new Element('textarea',{
        styles: {
            height:200,
            width:350,
            fontFamily:'monospace'
        }

    }).inject(div);
    div.appendChild(new Element('br'));

    var uid = parseInt(this.get('class'));


    var jsonRequest = new Request.JSON({
        url: (baseurl?baseurl:'') + 'json.php',
        onSuccess: function(obj){
            if(obj.result && obj.result.name) {
                var str = 'Name:\t\t'+obj.result.name;
                str += '\nPasswort:\t'+obj.result.password;
                str += '\nRechte:\t\t'+obj.result.group;

                str += '\nLetzter Login:\t'+obj.result.lastlogin;
                str += '\nLetzte Antwort:\t'+obj.result.lastvote;

                textarea.set('value',str);
            }
            else {
                if(obj.error == 2) {
                    textarea.set('value','Fehler: Kein Zugriff auf diese Daten');
                } else {
                    textarea.set('value','Fehler: Versuche es erneut!');
                }
            }
        }
    }).get({
        'q': 'memberinfo',
        'id':uid
    });


    var input1 = new Element('input').inject(div);
    input1.set('type','button');
    input1.set('value','Abbrechen');
    input1.addEvent('click',function(a,b) {
        return function() {
            a.dispose();
            b.dispose();
        };

    }(overlay,div));

}


// OnLoad
window.addEvent('domready', function() {
    var table = $('members_table');
    if(table) {
        var td = table.getElements('td');
        for(var i = 0; i < td.length; i++) {
            if(td[i].get('class')) {
                var uid = parseInt(td[i].get('class'));
                td[i].addEvent('click',memberinfo);
            }
        }



    }




});
