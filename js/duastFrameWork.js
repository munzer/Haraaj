function D(){


//======== append style or js file To head =============//
this.LinkFile = function(FileLink){
var extintion = FileLink.split('.').pop().trim();
if(extintion=="css"){
  var head = document.head
  var link = document.createElement('link');
  link.type = 'text/css';
  link.rel = 'stylesheet';
  link.href = FileLink;
  head.appendChild(link);
}
}

//======== Change Page Title  =============//
this.PageTitle = function(Title){document.title=Title;}

};

D = new D();
