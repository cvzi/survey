/** 
 *  stats.js
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

var chartparameterQArray = ['chart2','chart'];
var chartparameterQ = chartparameterQArray[0];


function getChart(ev) {
    if(!iOs) {
        var overlay = new Element('div').inject(document.body);
        overlay.set('styles',{
            position:'fixed',
            top:'0px',
            left:'0px',
            right:'0px',
            bottom:'0px',
            background:'url('+baseurl+'images/33.png)'
        });
    }

    var coords = $(document.body).getCoordinates();

    var div = new Element('div').inject(document.body);
    div.set('styles',{
        position:'fixed',
        minHeight:'200px',
        minWidth:'400px',
        left:(coords.width - 400) /2,
        right:(coords.width - 400) /2,
        top: (coords.height - 400) /2,
        background:'White',
        padding:30,
        border:'solid 10px #1e466f',
        MozBorderRadius:'40px',
        WebkitBorderRadius:'40px',
        borderRadius:'40px'
    });
    
    if(iOs) {
        div.set('styles',{
            'position' : 'absolute',
            'heigth' : 'auto',
            'width' : 'auto',
            'top' : $(document.body).getScroll().y,
            'left' : 0,
            'right' : 0,
            'bottom' : 0,
            'border' : 0,
            'MozBorderRadius':'0px',
            'WebkitBorderRadius':'0px',
            'borderRadius':'0px'      
        });      
    }



    var img = new Element('img',{
        src : baseurl+'images/empty.png',
        styles : {
            width: 400,
            height: 200,
            background: 'url('+baseurl+'images/loading.gif) no-repeat center center'
        }
    }).inject(div,'top');
    new Element('br').inject(img,'after');



    var input1 = new Element('input').inject(div);
    input1.set('type','button');
    input1.set('value','Schließen');
    input1.addEvent('click',function(a,b) {
        return function() {
            if (a) a.dispose();
            if (b) b.dispose();
        };

    }(overlay,div));

    var infodiv = new Element('div',{
        styles:{
            whiteSpace:'pre-wrap'
        }
    }).inject(div);

    var id = this.retrieve('id');
    var title = this.retrieve('title');
    var name = this.retrieve('name');
    var label = this.get('text');
    label = label?label:title;


    //{"errornumber":0,"filetime":62,"serializetime":206,"totaltime":296}

    var MiB = function(n,p) {
        var a = (n/1024)/1024;
        var b = Math.pow(10,p?p:2);
        return Math.round(a*b)/b;
    }



    var jsonRequest = new Request.JSON({
        url: (baseurl?baseurl:'') + 'json.php',
        onSuccess: function(obj){
            if(obj.result) {
                img.set('src',obj.result);
                infodiv.set('text','\nDateizugriffe:\t\t\t'+obj.filetime+'ms ('+obj.filenumber+' Dateien)\nParsing:\t\t\t\t'+obj.serializetime+'ms\nGesamtzeit:\t\t\t'+obj.totaltime+'ms\nVerarbeitete Daten:\t\t'+MiB(obj.filesize)+' MiB\nSpeicherverbrauch:\t'+MiB(obj.memory)+' MiB');
            }
            else {
                alert('Unkown error in JSON result');
                infodiv.set('text',JSON.encode(obj));
            }
        }
    }).get({
        'q': chartparameterQ,
        'id':id,
        'title' : title,
        'label' : label,
        'name' : name
    });


}


// iOs?
var iOs = false;
if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i))) {
    iOs = true;
}



// OnLoad
window.addEvent('domready', function() {
    var tables = $$('.stats');
    if(tables[0]) {
        for(var i = 0; i < tables.length; i++) {
            var title = tables[i].getElement('th').get('text');
            var for_ids = tables[i].getElements('.for_id');
            var name = tables[i].getElement('th').get('id').substring('stats_name_'.length);
            for(var a = 0; a < for_ids.length; a++) {
                if(iOs) {
                    for_ids[a].addEvent('click',function() {
                        if(this.retrieve('dblclick') === true) {
                            getChart.apply(this);
                        } else {
                            this.store('dblclick',true);
                            var reset = function(el) {
                                return function() {
                                    el.store('dblclick',false);
                                }
                            }(this);
                            reset.delay(2000);
                        }

                    });
                } else {
                    for_ids[a].addEvent('dblclick',getChart);
                }
                for_ids[a].store('title',title);
                for_ids[a].store('id',for_ids[a].get('class').split(' ')[1]);
                for_ids[a].store('name',name);
            }
        }

        var dia_desc = new Element('span',{
            text:'Diagramme: '
        }).inject(tables[0],'before');
        var span = new Element('span',{
            text:chartparameterQ,
            styles:{
                color:'#367B98',
                cursor:'pointer'
            }
        }).inject(dia_desc,'after');
        span.addEvent('click',function() {
            for(var i = 0; i < chartparameterQArray.length; i++) {
                if(chartparameterQ == chartparameterQArray[i]) {
                    break;
                }
            }
            i++;
            if(!chartparameterQArray[i]) {
                chartparameterQ = chartparameterQArray[0];
            } else {
                chartparameterQ = chartparameterQArray[i];
            }
            this.set('text',chartparameterQ);
            
        });



    }

});