//Copyright (c) 2007 osTicket.com

function getCannedResponse(idx,fObj)
{
    if(idx==0) { return false; }
    Http.get({
        url: "ajax.php?api=kbase&f=cannedResp&id="+idx,
        callback: setCannedResponse
    },[fObj]);

}

function setCannedResponse(xmlreply,fObj)
{
    if (xmlreply.status == Http.Status.OK)
    {
        var resp=xmlreply.responseText;
        if(fObj.response && resp){
            fObj.response.value=(fObj.append && fObj.append.checked)?trim(fObj.response.value+"\n\n"+resp):trim(resp)+"\n\n";
        }else {
            alert("Invalid form or tag");
        }
    }
    else{
        alert("Cannot handle the AJAX call. Error#"+ xmlreply.status);
    }
}

function getSelectedCheckbox(formObj) {
   var retArr = new Array();
   var x=0;
	for (var i= 0; i< formObj.length; i++)
    {
        fldObj = formObj.elements[i];
        if ((fldObj.type == 'checkbox') && fldObj.checked)
			retArr[x++]=fldObj.value;
   	}
   return retArr;
} 


function selectAll(formObj,task,highlight)
{
   var highlight = highlight || false;

   for (var i=0;i < formObj.length;i++)
   {
      var e = formObj.elements[i];
      if (e.type == 'checkbox')
      {
         if(task==0) {
            e.checked =false;
         }else if(task==1) {
            e.checked = true;
         }else{
            e.checked = (e.checked) ? false : true;
         }
         
	     if(highlight) {
			highLight(e.value,e.checked);
		 }
       }
   }
   //Return false..to mute submits or href.
   return false;
}

function reset_all(formObj){
    return selectAll(formObj,0,true);
}
function select_all(formObj,highlight){
    return selectAll(formObj,1,highlight);
}
function toogle_all(formObj,highlight){

	var highlight = highlight || false;
    
	return selectAll(formObj,2,highlight);
}


function checkbox_checker(formObj, min,max,sure,action)
{

	var checked=getSelectedCheckbox(formObj); 
	var total=checked.length;
    var action= action?action:"process";
 	
	if (max>0 && total > max )
 	{
 		msg="You're limited to only " + max + " selections.\n"
 		msg=msg + "You have made " + total + " selections.\n"
 		msg=msg + "Please remove " + (total-max) + " selection(s)."
 		alert(msg)
 		return (false);
 	}
 
 	if (total< min )
 	{
 		alert("Please make at least " + min + " selections. " + total + " entered so far.")
 		return (false);
 	}
   
  if(sure){
  	if(confirm("PLEASE CONFIRM\n About to "+ action +" "+ total + " record(s).")){
 		return (true);
  	}else{
        reset_all(formObj);
	 	return (false);
  	}
  }
 
  return (true);
}

function toggleLayer(whichLayer) {
    var elem, vis;

    if( document.getElementById ) // this is the way the standards work
        elem = document.getElementById( whichLayer );
    else if( document.all ) // this is the way old msie versions work
        elem = document.all[whichLayer];
    else if( document.layers ) // this is the way nn4 works
        elem = document.layers[whichLayer];
  
    vis = elem.style;
    // if the style.display value is blank we try to figure it out here
    if(vis.display==''&&elem.offsetWidth!=undefined&&elem.offsetHeight!=undefined)
        vis.display = (elem.offsetWidth!=0&&elem.offsetHeight!=0)?'block':'none';
    vis.display = (vis.display==''||vis.display=='block')?'none':'block';
}


function showHide(){

	for (var i=0; i<showHide.arguments.length; i++){
        toggleLayer(showHide.arguments[i]);
	}
    return false;
}

function visi(){	 
	for (var i=0; i<visi.arguments.length; i++){
        var element = document.getElementById(visi.arguments[i]);
        element.style.visibility=(element.style.visibility == "hidden")?"visible" : "hidden";
    }
}
function visible(id){
	var element = document.getElementById(id).style.visibility="visible";
}


function highLight(trid,checked) {

    var class_name='highlight';
    var elem;

    if( document.getElementById )
        elem = document.getElementById( trid );
    else if( document.all )
        elem = document.all[trid];
    else if( document.layers )
        elem = document.layers[trid];
    if(elem){
        var found=false;
        var temparray=elem.className.split(' ');
        for(var i=0;i<temparray.length;i++){
            if(temparray[i]==class_name){found=true;}
        }
        if(found && checked) { return; }

        if(found && checked==false){ //remove
            var rep=elem.className.match(' '+class_name)?' '+class_name:class_name;
            elem.className=elem.className.replace(rep,'');
        }
        if(checked && found==false) { //add
            elem.className+=elem.className?' '+class_name:class_name;
        }
    }
}

function highLightToggle(trid) {

    var class_name='highlight';
    var e;
    if( document.getElementById )
        e = document.getElementById(trid);
    else if( document.all )
        e = document.all[trid];
    else if( document.layers )
        e = document.layers[trid];

    if(e){
        var found=false;
        var temparray=e.className.split(' ');
        for(var i=0;i<temparray.length;i++){
            if(temparray[i]==class_name){found=true;}
        }
        if(found){ //remove
            var rep=e.className.match(' '+class_name)?' '+class_name:class_name;
            e.className=e.className.replace(rep,'');
        }else { //add
            e.className+=e.className?' '+class_name:class_name;
        }
    }
}

//trim
function trim (str) {
    str = this != window? this : str;
    return str.replace(/^\s+/,'').replace(/\s+$/,'');
}

//strcmp
function strcmp(){
    var arg1=arguments[0];
    if(arg1) {
        for (var i=1; i<arguments.length; i++){
            if(arg1==arguments[i])
                return true;
        }
    }
    return false;
}



