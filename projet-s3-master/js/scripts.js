function viderSelect(sel){
    for (i = sel.length; i > 0 ; i--){
        sel.remove(1);
    }
}

function ajouterOption(sel, txt, val){
    option = document.createElement("option");
    option.text = txt;
    option.value = val;
    sel.add(option);
}

function viderNoeud(noeud){
    while(noeud.lastElementChild()){
        noeud.removeChild(noeud.firstChild);
    }
}

function charge(url, str, sel){
     var ajax = null;
        //création requête ajax
        ajax = new AjaxRequest(
        {
            asynchronous : true,
            url        : url,
            method     : 'get',
            handleAs   : 'json',   
            parameters : {
                q : str,
                wait : 1
            },
            onSuccess  : function(res) {
                viderSelect(sel);
                for(i = 0; i < res.length; i++){
                    ajouterOption(sel, res[i].txt, res[i].id);
                }
                ajax = null;    
            },
            onError    : function(status, message) {
                window.alert('Error ' + status + ': ' + message) ;
                ajax = null;                            
            }
       });
}


