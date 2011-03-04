/*
Tabs Menu (mouseover)- By Dynamic Drive
For full source code and more DHTML scripts, visit http://www.dynamicdrive.com
This credit MUST stay intact for use
http://www.dynamicdrive.com/dynamicindex1/tabmouseover.htm
*/

var submenu = new Array()

//Set submenu contents. Expand as needed. For each content, make sure everything exists on ONE LINE. Otherwise, there will be JS errors.
 
submenu[0] = '<ul id="main_subnavigation" style="margin: 0px 9em 0px 0px;position: absolute; right: 1em;"><li><a href="/stream/tagged">Zugeordnet</a> <li><a href="/stream/untagged">Nicht zugeordnet</a> <li><a href="/stream/trash">Unbrauchbar</a></li></ul>'
submenu[1] = '<ul id="main_subnavigation" style="margin: 0px 8em 0px 0px;position: absolute; right: 1em;"><li><a href="/statistiken/parteien">Parteien</a> <li><a href="/statistiken/medien">Medien</a> <li><a href="/statistiken/regionen">Regionen</a></li></ul>'

//Set delay before submenu disappears after mouse moves out of it (in milliseconds)
var delay_hide = 500

/////No need to edit beyond here

var menuobj = document.getElementById ? document.getElementById("describe") : document.all ? document.all.describe : document.layers ? document.dep1.document.dep2 : ""

function showit(which) {
    clear_delayhide()
    thecontent = (which == -1) ? "" : submenu[which]
    if (document.getElementById || document.all)
        menuobj.innerHTML = thecontent
    else if (document.layers) {
        menuobj.document.write(thecontent)
        menuobj.document.close()
    }
}

function resetit(e) {
    if (document.all && !menuobj.contains(e.toElement))
        delayhide = setTimeout("showit(-1)",delay_hide)
    else if (document.getElementById && e.currentTarget != e.relatedTarget)
        delayhide = setTimeout("showit(-1)",delay_hide)
}

function clear_delayhide() {
    if (window.delayhide)
        clearTimeout(delayhide)
}

