/*
 * main.js
 *
 *       survey
 *
 *  survey is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  survey is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with survey; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 *  For questions contact
 *  cuzi@openmail.cc
 *
 * @copyright 2010 cuzi
 * @author cuzi@openmail.cc
 * @package survey
 * @version 2.0
 * @license http://gnu.org/copyleft/gpl.html GNU GPL
 */

// TODO alles beantwortet


var Tips2;
Locale.use('de-DE');


function getcomments(str) {
    var chatmessages = $$('.chatmessage');

    if(!chatmessages[0].get('id')) {
        document.location.reload(); // Latest Post is post by function newcomment()
        return;
    }

    var latest_id = chatmessages[0].get('id').split('_')[1];
    var oldest_id = chatmessages[chatmessages.length-1].get('id').split('_')[1];

    if('f5' == str) {
        oldest_id = 1;
        if(this.get)
            var image = this.get('id');
    }

    var jsonRequest = new Request.JSON({
        url: (baseurl?baseurl:'') + 'json.php',
        onSuccess: function(obj){
            if(!obj.error && obj.result && obj.result.data) {
                var recent = false;

                obj.result.data.map(function(value) {

                    if(parseInt(value.id) > latest_id) {
                        recent = true;
                        new Element('br').inject($('refreshcomment').parentNode,'after');
                        var div = new Element('div').inject($('refreshcomment').parentNode,'after');
                    } else {
                        new Element('br').inject($('getcomments'),'before');
                        var div = new Element('div').inject($('getcomments'),'before');
                    }

                    div.set('class','chatmessage');
                    div.set('id','chatmessage_'+value.id);
                    var headline = new Element('div').inject(div);
                    headline.set('class','headline');
                    headline.set('text',value.displayname+' ');
                    var acronym = new Element('acronym').inject(headline);
                    var date = new Date();
                    date.parse(value.time);
                    acronym.set('title',date.format('%A, %d. %B %Y um %H:%M Uhr und %S Sekunden'));
                    acronym.set('text','vor 1 Sekunde');
                    acronym.set('text',date.timeDiffInWords());
                    acronym.store('data',date);
                    Tips2.attach(acronym);

                    var post = new Element('div').inject(div);
                    post.set('class','post');
                    post.set('text',value.text);
                    headline.highlight();

                });


                if(recent) {
                    var myFx = new Fx.Scroll(window).toElement('newcomment');
                } else if($('chatmessage_'+oldest_id)) {
                    var myFx = new Fx.Scroll(window).toElement('chatmessage_'+oldest_id);
                }

                if(!obj.result.more && 'f5' != str) {
                    $('getcomments').setStyle('display','none');
                }

                if(image) {
                    $(image).highlight('#67d28a','#E7F4FC');
                }


            }
            else {
                alert(obj.error);
            }
        }
    }).get({
        'q': 'getcomments',
        'latest':latest_id,
        'oldest':oldest_id
    });
}


function newcomment(ev) {

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

    div.appendChild(document.createTextNode('Name: '));

    var input = new Element('input').inject(div);
    input.set('type','text');
    input.set('styles',{
        border:'2px Silver dotted',
        background:'White'
    });

    new Element('br').inject(div);
    new Element('br').inject(div);
    div.appendChild(document.createTextNode('Text: '));
    new Element('br').inject(div);

    var textarea = new Element('textarea').inject(div);
    textarea.set('styles',{
        width:'338px',
        height:'200px',
        border:'2px Silver dotted',
        background:'White'
    });

    new Element('br').inject(div);
    new Element('br').inject(div);

    var input0 = new Element('input').inject(div);
    input0.set('type','button');
    input0.set('value','Kommentar speichern');
    input0.addEvent('click',function(a,b,textarea,input,input0) {
        return function() {

            if('' == input.get('value').trim()) {
                input.highlight('#dc143c','#FFFFFF');
                return;
            }

            if('' == textarea.get('value').trim()) {
                textarea.highlight('#dc143c','#FFFFFF');
                return;
            }

            var name = input.get('value').trim();
            var text = textarea.get('value').trim();

            var jsonRequest = new Request.JSON({
                url: (baseurl?baseurl:'') + 'json.php',
                onSuccess: function(obj){

                    if(!obj.error && obj.result == 'wait') {
                        input0.set('value','Bitte warte '+obj.wait+' Sekunden')
                        input0.set('disabled','disabled');
                        input0.setStyle('cursor','default');
                        input0.highlight('#dc143c','#f0f0f0');
                        var timer;
                        var countdown = function() {
                            var i = parseInt(input0.get('value').match(/(\d+)/)[1]);
                            if(i > 1) {
                                input0.set('value',input0.get('value').replace(/\d+/,i-1));
                            }
                            else {
                                input0.set('value','Kommentar speichern');
                                input0.erase('disabled');
                                input0.highlight('#67d28a','#f0f0f0');
                                clearInterval(timer);
                            }
                        }
                        timer = countdown.periodical(1000);


                    }

                    else if(!obj.error && obj.result == 1) {
                        new Element('br').inject($('refreshcomment').parentNode,'after');

                        var div = new Element('div').inject($('refreshcomment').parentNode,'after');
                        div.set('class','chatmessage');
                        var headline = new Element('div').inject(div);
                        headline.set('class','headline');
                        headline.set('text',name+' ');
                        var acronym = new Element('acronym').inject(headline);
                        var date = new Date();
                        acronym.set('title',date.format('%A, %d. %B %Y um %H:%M Uhr und %S Sekunden'));
                        acronym.set('text','vor 1 Sekunde');
                        acronym.set('text',date.timeDiffInWords());
                        acronym.store('data',date);
                        Tips2.attach(acronym);

                        var post = new Element('div').inject(div);
                        post.set('class','post');
                        post.set('text',text);

                        a.dispose();
                        b.dispose();

                    }
                    else {
                        input0.highlight('#dc143c','#f0f0f0');
                        input0.set('value','Fehler: Versuche es erneut!');
                    }
                }
            }).get({
                'q': 'comment',
                'name':name,
                'text':text
            });



        };

    }(overlay,div,textarea,input,input0));

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

function voteCombi(re) {
    var tr = re;
    var selects = tr.getElements('select');
    var aids = [];
    var found_an_unselected = false;
    var found_a_selected = false;
    for(var i = 0; i < selects.length; i++) {
        var aid = selects[i].options[selects[i].selectedIndex].get('value');
        if(aid == 0) {
            found_an_unselected = true;
        } else {
		        found_a_selected = true;
	      }
        aids.push( aid );
    }

    // Narzissmus?
    if(!found_an_unselected && !found_a_selected) {
        var names = {};
        for(var i = 0; i < selects.length; i++) {
            var name = selects[i].options[selects[i].selectedIndex].get('text');
           if(names[name]) {
               alert('Narzissmus?\n\nWähle doch bitte was richtiges ;)');
                return;
            } else {
               names[name] = true;
            }
        }
    }

    var selectholder = tr.getElement('.selectholder');
    var statusholder = tr.getElement('.statusholder');
    var questionholder = tr.getElement('.questionholder');


    var name = selectholder.get('class').split(' ');
    name = name[name.length-1];

    var qid = questionholder.get('class').split(' ');
    var vote_number = parseInt(qid[qid.length-1]);
    qid = parseInt(qid[qid.length-2]);


    // Unselected things? " - auswählen - "
    if(found_an_unselected && !found_a_selected) { // All fields are unselected
		// Remove Vote
		deleteVote(tr,name,qid,0,vote_number);
		return;
    }
    else if(found_an_unselected && found_a_selected) { // Some fields are unselected
		// Do nothing. You cannot remove "some" fields of a vote
		return;
    }



    var jsonRequest = new Request.JSON({
        url: (baseurl?baseurl:'') + 'json.php',
        onSuccess: function(obj){
            if(!obj.error && (obj.result == 1 || obj.result == -1 || obj.result == -2)) {
                statusholder.set('html','Beantwortet');

                if(-1 != statusholder.get('class').indexOf('error')) {
                    statusholder.set('class',statusholder.get('class').replace(' error',' done'));
                }
                else {
                    statusholder.set('class',statusholder.get('class')+' done');
                }

                $$(selectholder,statusholder,questionholder).highlight('#228b22','#D6E4EC');


                for(var i = 0; i < selects.length; i++) {
                    var select = selects[i];
                    var options = $$(select.options);
                    for(var a = 0,len = options.length; a < len;a++) {
                        if(options[a].get('value') == aids[i]) {
                            options[a].set('selected','selected');
                            options[a].setStyle('background-color','LightGreen');
                        }
                        else {
                            options[a].erase('selected');
                            options[a].setStyle('background-color','');
                        }
                    }

                }

                if($('hideOnAnswer') && $('hideOnAnswer').checked) {
                    var hide = function() {
                        tr.setStyle('display','none');
                    };
                    hide.delay(400);
                    
                }

            }
            else {
                statusholder.set('html','Es ist ein Fehler aufgetreten (ERROR#'+obj.result+')');
                if(-1 != statusholder.get('class').indexOf('done')) {
                    statusholder.set('class',statusholder.get('class').replace(' done',' error'));
                }
                else {
                    statusholder.set('class',statusholder.get('class')+' error');
                }
                for(var i = 0; i < selects.length; i++) {
                    var select = selects[i];
                    var options = $$(select.options);
                    options[0].set('selected','selected');
                }
            }
        }
    }).get({
        'q': 'votecombi',
        'name':name,
        'qid': qid,
        'aids':aids.join(','),
        'vn':vote_number
    });
}




function vote(re) {

    var tr = re;

    var select = tr.getElement('select');
    var aid = select.options[select.selectedIndex].get('value');

    var selectholder = tr.getElement('.selectholder');
    var statusholder = tr.getElement('.statusholder');
    var questionholder = tr.getElement('.questionholder');


    var name = selectholder.get('class').split(' ');
    name = name[name.length-1];

    var qid = questionholder.get('class').split(' ');
    var vote_number = parseInt(qid[qid.length-1]);
    qid = parseInt(qid[qid.length-2]);

    if(aid == 0) { // " - auswählen - "
        // Delete Vote
        deleteVote(tr,name,qid,aid,vote_number);
        return;
    }



    var jsonRequest = new Request.JSON({
        url: (baseurl?baseurl:'') + 'json.php',
        onSuccess: function(obj){
            if(!obj.error && (obj.result == 1 || obj.result == -1 || obj.result == -2)) {
                statusholder.set('html','Beantwortet');

                if(-1 != statusholder.get('class').indexOf('error')) {
                    statusholder.set('class',statusholder.get('class').replace(' error',' done'));
                }
                else {
                    statusholder.set('class',statusholder.get('class')+' done');
                }

                $$(selectholder,statusholder,questionholder).highlight('#228b22','#D6E4EC');

                var options = $$(select.options);
                for(var a = 0,len = options.length; a < len;a++) {
                    if(options[a].get('value') == aid) {
                        options[a].set('selected','selected');
                        options[a].setStyle('background-color','LightGreen');
                    }
                    else {
                        options[a].erase('selected');
                        options[a].setStyle('background-color','');
                    }
                }
                if($('hideOnAnswer') && $('hideOnAnswer').checked) {
                    var hide = function() {
                        tr.setStyle('display','none');
                    };
                    hide.delay(400);

                }

            }
            else {
                statusholder.set('html','Es ist ein Fehler aufgetreten (ERROR#'+obj.result+')');
                if(-1 != statusholder.get('class').indexOf('done')) {
                    statusholder.set('class',statusholder.get('class').replace(' done',' error'));
                }
                else {
                    statusholder.set('class',statusholder.get('class')+' error');
                }
                var options = $$(select.options);
                options[0].set('selected','selected');
            }
        }
    }).get({
        'q': 'vote',
        'name':name,
        'qid': qid,
        'aid':aid,
        'vn':vote_number
    });

}

function deleteVote(tr,name,qid,aid,vote_number) {
    var selectholder = tr.getElement('.selectholder');
    var statusholder = tr.getElement('.statusholder');
    var questionholder = tr.getElement('.questionholder');

    var jsonRequest = new Request.JSON({
        url: (baseurl?baseurl:'') + 'json.php',
        onSuccess: function(obj){
            if(!obj.error && (obj.result == -1)) {
                statusholder.set('html','Nicht beantwortet');

                if(-1 != statusholder.get('class').indexOf('error')) {
                    statusholder.set('class',statusholder.get('class').replace(' error',' done'));
                }
                else {
                    statusholder.set('class',statusholder.get('class')+' done');
                }

                $$(selectholder,statusholder,questionholder).highlight('#228b22','#D6E4EC');

            }
            else {
                statusholder.set('html','Es ist ein Fehler aufgetreten (ERROR#'+obj.result+')');
                if(-1 != statusholder.get('class').indexOf('done')) {
                    statusholder.set('class',statusholder.get('class').replace(' done',' error'));
                }
                else {
                    statusholder.set('class',statusholder.get('class')+' error');
                }
                var options = $$(select.options);
                options[0].set('selected','selected');
            }
        }
    }).get({
        'q': 'removevote',
        'name':name,
        'qid': qid,
        'vn':vote_number
    });
}




// OnLoad
window.addEvent('domready', function() {

    // IE advice
    if(Browser.ie && navigator.userAgent.indexOf('MSIE 9.0') != -1) {
        if(document.location.href.indexOf('survey') != -1 && document.location.href.indexOf('force') == -1) {
            document.location.href = document.location.href.replace(/survey/,'single');
        }

    }
    if(Browser.ie && navigator.userAgent.indexOf('MSIE 9.0') != -1 && $('youareie')) {
        $('youareie').setStyle('display','block');
    }

    // Settings Inputs
    var setting_checks = $$('input.settings');
    if(setting_checks[0]) {
        setting_checks.addEvent('click',function() {
            var name = this.get('name');
            var value = this.get('value');
            var input = this;
            if('checkbox' == this.get('type') && !this.get('checked')) {
                value = 0;
            }

            var img = new Element('img',{
                'src' : (baseurl?baseurl:'') + 'images/spinner.gif',
                'alt' : 'Speichern...',
                'id' :'settings_spinnter_'+name
            }).inject(this,'before');



            var jsonRequest = new Request.JSON({
                url: (baseurl?baseurl:'') + 'json.php',
                onSuccess: function(obj){
                    if(obj) {
                        img.dispose();
                        if($('label_'+input.get('id'))) {
                            $('label_'+input.get('id')).highlight();
                        }
                    } else {
                        alert('Unkown error: json.php?q=settings returned '+obj);
                    }

                }
            }).get({
                'q': 'settings',
                'name':name,
                'value': value
            });



        });
    }


    // Copy select and add statuscode (and request event) for each question
    if($$('.universal_select')[0]) {

        var universal_selects_obj = {};
        var universal_selects = $$('.universal_select');
        for(var i = 0,len = universal_selects.length; i < len;i++) {
            var name = universal_selects[i].get('id').replace('universal_select_','');
            universal_selects_obj[name] = universal_selects[i];
        }


        var questionTR = $$('tr.question');
        for(i = questionTR.length-1; i > -1;i--) {
            if(!questionTR[i])
                continue;
            if(questionTR[i].get('class').indexOf('combination') != -1) { // Skip Combinations
                continue;
            }

            var selectholder = questionTR[i].getElement('.selectholder');
            var statusholder = questionTR[i].getElement('.statusholder');
            var clas = selectholder.get('class');
            name = clas.split(' ');
            name = name[name.length-1];

            if(!universal_selects_obj[name])
                continue;

            var select = universal_selects_obj[name].clone(true,false);
            select.inject(selectholder);
            select.setStyle('display','');
            select.addEvent('change',function(tr) {
                return function() {
                    vote(tr);
                };
            }(questionTR[i]));
            var status = statusholder.get('html');
            if('' == status) {
                statusholder.set('html','Nicht beantwortet');
            }
            else {
                statusholder.set('html','Beantwortet');
                statusholder.set('class',statusholder.get('class')+' done');
                var options = $$(select.options);
                for(var a = 0,len = options.length; a < len;a++) {
                    if(options[a].get('value') == status) {
                        options[a].set('selected','selected');
                        options[a].setStyle('background-color','LightGreen');
                    }
                    else {
                        options[a].erase('selected');
                    }
                }
            }

        }


        // Combinations

        var universal_selects_obj = {};
        var universal_selects = $$('.universal_select_combination');
        for(var i = 0,len = universal_selects.length; i < len;i++) {
            var name = universal_selects[i].getElement('select').get('rel');
            universal_selects_obj[name] = universal_selects[i];
        }


        var questionTR = $$('tr.question.combination');
        for(i = questionTR.length-1; i > -1;i--) {
            if(!questionTR[i])
                continue;

            var selectholder = questionTR[i].getElement('.selectholder');
            var statusholder = questionTR[i].getElement('.statusholder');
            var clas = selectholder.get('class');
            name = clas.split(' ');
            name = name[name.length-1];

            if(!universal_selects_obj[name])
                continue;

            var div = universal_selects_obj[name].clone(true,false);
            div.inject(selectholder);
            div.getElements('select').setStyle('display','inline-block');
            div.getElements('select').addEvent('change',function(tr) {
                return function() {
                    voteCombi(tr);
                };
            }(questionTR[i]));
            var status = statusholder.get('html');
            status = status.split(',');
            var status_ar = [];
            for(var b = 0; b < status.length; b++) {
                if(parseInt(status[b])) {
                    status_ar.push(parseInt(status[b]));
                }
            }
            
            if(!status_ar || !status_ar.length) {
                statusholder.set('html','Nicht beantwortet');
            }
            else {
                statusholder.set('html','Beantwortet');
                statusholder.set('class',statusholder.get('class')+' done');
                var selects = div.getElements('select');
                for(var b = 0; b < selects.length; b++) {
                    var options = $$(selects[b].options);
                    for(var a = 0,len = options.length; a < len;a++) {
                        if(options[a].get('value') == status_ar[b]) {
                            options[a].set('selected','selected');
                            options[a].setStyle('background-color','LightGreen');
                        }
                        else {
                            options[a].erase('selected');
                        }
                    }
                }
            }

        }




        // hide answered
        $('hideOnAnswer').addEvent('click',function(){
            this.fireEvent('change'); // Internet Explorer Workaround
        });
        $('hideOnAnswer').addEvent('change',function() {
            if(!this.checked) {
                var questionTR = $$('tr.question');
                for(i = 0,len = questionTR.length; i < len;i++) {
                    if(!questionTR[i])
                        continue;
                    questionTR[i].setStyle('display','');
                }
            }
            else {
                var questionTR = $$('tr.question');
                for(i = 0,len = questionTR.length; i < len;i++) {
                    if(!questionTR[i])
                        continue;
                    var statusholder = questionTR[i].getElement('.statusholder');
                    var clas = statusholder.get('class');
                    if(clas.indexOf(' done') != -1) {
                        questionTR[i].setStyle('display','none');
                    }
                }
            }
        });


    }

    // Hide *descriptions*
    if($$('.questionholder')[0]) {
        var res = [];
        var td = $$('.questionholder');
        for(i = 0,len = td.length; i < len;i++) {
            if(!td[i])
                continue;
            if(td[i].get('text').indexOf('*') == -1) {
                continue;
            }
            var html = td[i].get('html');
            var matches = html.match(/\*(.*)\*/);
            if(matches[1]) {
                html = html.replace(matches[0],'');
                td[i].set('html',html);
                var img = new Element('img',{
                    alt:'Info',
                    title:matches[1],
                    src:baseurl+'images/icons/info.png'
                }).inject(td[i]);
                res.push(img);
            }

        }

        var StarTips = new Tips(res);

    }



    // Set predefined input descriptions
    if(!window.mobile) {

        if($$('.inputdec')[0]) {
            var inputdecs = $$('.inputdec');
            for(var i = 0,len = inputdecs.length; i < len;i++) {
                var id = inputdecs[i].get('id').replace('_dec','');
                if($(id)) {
                    $(id).set('class','inputWithDesc');
                    $(id).set('title',inputdecs[i].get('text')+' '+inputdecs[i].get('title'));

                    if('' == $(id).get('value')) {
                        $(id).store('color',$(id).getStyle('color'));
                        $(id).setStyle('color','Silver');
                        $(id).set('value',inputdecs[i].get('html'));
                        $(id).addEvent('focus',function(inputdec) {
                            return function() {
                                if(inputdec.get('html') == this.get('value')) {
                                    this.set('value','');
                                    $(id).setStyle('color',$(id).retrieve('color'));
                                }
                            };
                        }(inputdecs[i]));

                    }
                }
            }

            // Add tips
            var Tips1 = new Tips($$('.inputWithDesc'),{
                showDelay:10,
                hideDelay:500
            });
        }
    }

    // focus an special input
    if($$('.ffocus')[0]) {
        $$('.ffocus')[0].focus();

    }


    // Add tips to time acronyms
    var acronyms = $$('acronym');
    for(var i = 0,len = acronyms.length; i < len;i++) {
        var a = acronyms[i];
        if('time:' == a.get('title').substr(0,'time:'.length)) {
            var str = a.get('title').substr('time:'.length);
            a.set('title',str);
            a.set('class','timetip');
            var date = new Date();
            date.parse(a.get('text'));
            a.set('text',date.timeDiffInWords());
            a.store('date',date);

        }
    }
    Tips2 = new Tips($$('.timetip'));
    var updateTime = function() {
        var acronyms = $$('acronym');
        for(var i = 0,len = acronyms.length; i < len;i++) {
            var a = acronyms[i];
            if(a.retrieve('date')) {
                var date = a.retrieve('date');
                a.set('text',date.timeDiffInWords());
            }
        }
    };
    updateTime.periodical(1000*60);


    // Statistic Link
    if($('statslink')) {
        $('statslink').store('href',$('statslink').get('href'));
        $('statslink').set('href','#');
        $('statslink').addEvent('click',function() {
            this.set('text','Laden...')

            var jsonRequest = new Request.JSON({
                url: (baseurl?baseurl:'') + 'json.php',
                onSuccess: function(obj){
                    if(obj.result == 1) {
                        document.location.href = $('statslink').retrieve('href');
                    } else {
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

                        div.appendChild(new Element('h1',{
                            'text':'Statistik wird generiert'
                        }));
                        div.appendChild(new Element('img',{
                            'src':baseurl+'images/loading.gif'
                        }));
                            
                        var generateIt = new Request({
                            url: $('statslink').retrieve('href'),
                            onSuccess: function(){
                                document.location.href = $('statslink').retrieve('href');
                            }
                        }).get({});


                    }

                }

            }).get({
                'q': 'generatestats'
            });

        });
    }

    // New comment
    if($('newcomment')) {
        $('newcomment').addEvent('click',newcomment);
        $('newcomment').setStyle('cursor','pointer');
        var Tips3 = new Tips($$('#newcomment'));
    }

    // Load Comments
    if($('getcomments')) {
        $('getcomments').addEvent('click',getcomments);
        $('getcomments').setStyle('cursor','pointer');
        var Tips4 = new Tips($$('#getcomments'));
    }

    // Refresh Comments
    if($('refreshcomment')) {
        $('refreshcomment').addEvent('click',function() {
            getcomments.apply(this,['f5']);
        });
        $('refreshcomment').setStyle('cursor','pointer');
        var Tips5 = new Tips($$('#refreshcomment'));
    }

    // Sorting Links on member page
    var Tips6 = new Tips($$('.changesorting'),{
        text:'title',
        title:'rel'
    });

    // Login password field on double click
    if($('otp')) {
        if($('otp_dec')) {
            $('otp').setAttribute('value',$('otp_dec').get('text'));
        }

        $('otp').addEvent('dblclick',function() {
            this.setAttribute('type','password');
        });
        $('otp').addEvent('keyup',function() {
            if('hide' == this.get('value')) {
                this.setAttribute('type','password');
                this.setAttribute('value','');
            }
        });

    }


    // Scroll infoboxes
    $$('.infobox').set('styles',{
        maxHeight: $(document.body).getCoordinates().height * 3,
        overflow:'auto'
    });

    // SQL Commands
    if($$('.buffer')[0]) {
        highlightSQL($$('.buffer')[0]);
    }


    // Hide complete members
    if($('hideOnComplete')) {
        $('hideOnComplete').addEvent('click',function() {
            $$('#members_table .complete').setStyle('display',this.get('checked')?'none':'');
        });
    }



});

function highlightSQL(el) {
    var keys = ['ADD','ALL','ALTER',
    'ANALYZE','AND','AS',
    'ASC','ASENSITIVE','BEFORE',
    'BETWEEN','BIGINT','BINARY',
    'BLOB','BOTH','BY',
    'CALL','CASCADE','CASE',
    'CHANGE','CHAR','CHARACTER',
    'CHECK','COLLATE','COLUMN',
    'COLUMNS','CONDITION','CONNECTION',
    'CONSTRAINT','CONTINUE','CONVERT',
    'CREATE','CROSS','CURRENT_DATE',
    'CURRENT_TIME','CURRENT_TIMESTAMP','CURRENT_USER',
    'CURSOR','DATABASE','DATABASES',
    'DAY_HOUR','DAY_MICROSECOND','DAY_MINUTE',
    'DAY_SECOND','DEC','DECIMAL',
    'DECLARE','DEFAULT','DELAYED',
    'DELETE','DESC','DESCRIBE',
    'DETERMINISTIC','DISTINCT','DISTINCTROW',
    'DIV','DOUBLE','DROP',
    'DUAL','EACH','ELSE',
    'ELSEIF','ENCLOSED','ESCAPED',
    'EXISTS','EXIT','EXPLAIN',
    'FALSE','FETCH','FIELDS',
    'FLOAT','FLOAT4','FLOAT8',
    'FOR','FORCE','FOREIGN',
    'FROM','FULLTEXT','GOTO',
    'GRANT','GROUP','HAVING',
    'HIGH_PRIORITY','HOUR_MICROSECOND','HOUR_MINUTE',
    'HOUR_SECOND','IF','IGNORE',
    'IN','INDEX','INFILE',
    'INNER','INOUT','INSENSITIVE',
    'INSERT','INT','INT1',
    'INT2','INT3','INT4',
    'INT8','INTEGER','INTERVAL',
    'INTO','IS','ITERATE',
    'JOIN','KEY','KEYS',
    'KILL','LABEL','LEADING',
    'LEAVE','LEFT','LIKE',
    'LIMIT','LINES','LOAD',
    'LOCALTIME','LOCALTIMESTAMP','LOCK',
    'LONG','LONGBLOB','LONGTEXT',
    'LOOP','LOW_PRIORITY','MATCH',
    'MEDIUMBLOB','MEDIUMINT','MEDIUMTEXT',
    'MIDDLEINT','MINUTE_MICROSECOND','MINUTE_SECOND',
    'MOD','MODIFIES','NATURAL',
    'NOT','NO_WRITE_TO_BINLOG','NULL',
    'NUMERIC','ON','OPTIMIZE',
    'OPTION','OPTIONALLY','OR',
    'ORDER','OUT','OUTER',
    'OUTFILE','PRECISION','PRIMARY',
    'PRIVILEGES','PROCEDURE','PURGE',
    'READ','READS','REAL',
    'REFERENCES','REGEXP','RELEASE',
    'RENAME','REPEAT','REPLACE',
    'REQUIRE','RESTRICT','RETURN',
    'REVOKE','RIGHT','RLIKE',
    'SCHEMA','SCHEMAS','SECOND_MICROSECOND',
    'SELECT','SENSITIVE','SEPARATOR',
    'SET','SHOW','SMALLINT',
    'SONAME','SPATIAL','SPECIFIC',
    //'SQL',
    'SQLEXCEPTION','SQLSTATE',
    'SQLWARNING','SQL_BIG_RESULT','SQL_CALC_FOUND_ROWS',
    'SQL_SMALL_RESULT','SSL','STARTING',
    'STRAIGHT_JOIN','TABLE','TABLES',
    'TERMINATED','THEN','TINYBLOB',
    'TINYINT','TINYTEXT','TO',
    'TRAILING','TRIGGER','TRUE',
    'UNDO','UNION','UNIQUE',
    'UNLOCK','UNSIGNED','UPDATE',
    'UPGRADE','USAGE','USE',
    'USING','UTC_DATE','UTC_TIME',
    'UTC_TIMESTAMP','VALUES','VARBINARY',
    'VARCHAR','VARCHARACTER','VARYING',
    'WHEN','WHERE','WHILE',
    'WITH','WRITE','XOR',
    'YEAR_MONTH','ZEROFILL',
    // Operators

    ' \= ',' \> ','\< ','SOUNDS',
    ' \/ ',

    // Some Functions
    'COUNT','SUM','MAX','GROUP_CONCAT'

    ];
    var compareStringLengths = function( a, b )
    {
        if ( a.length < b.length )
            return 1;
        if ( a.length > b.length )
            return -1;
        return 0;
    }
    keys.sort ( compareStringLengths );

    if(-1 != el.get('text').indexOf('SQL:')) {
        var html = el.get('text');
        for (var i=0,len = keys.length; i<len; i++) {
            html = html.replace(new RegExp(keys[i],'g'), '<span style="color:DarkBlue; ">'+keys[i]+'</span>');
        }


        html = html.replace(new RegExp('`([\\.,]{0,1})','g'), '<span style="color:GREEN; font-weight:bold;">`$1</span>');
        html = html.replace(new RegExp('\\(','g'), '<span style="color:GREEN; font-weight:bold;">(</span>');
        html = html.replace(new RegExp('\\)','g'), '<span style="color:GREEN; font-weight:bold;">)</span>');

        html = html.replace(/([^a-z])(\d+)([^a-z])/gi, '$1<span style="color:RED;">$2</span>$3');

        html = html.replace(/SQL:/gi, '<hr /><h2>SQL: (highlighted with Javascript)</h2>');


        el.set('html',html);
    }




}
