jQuery(document).ready(function() {
    
    var find = 0;

    jQuery(".ld-text").each(function(i,e){
        
        //ハイレベルレッスン教材の見せるレッスン(バルーンを表示)
        if (jQuery(e).text().indexOf('ティーチャー') > -1){
            
            let msg = '講師様専用タブ\n生徒様には表示されません。';
            let elem = jQuery(e);
            
            elem.on({'mouseenter' : function(){
                TKS_BL.show(jQuery(this),msg,"top"); //[1]baloon表示の処理を着火
            },'mouseleave' : function(){
                TKS_BL.hide(jQuery(this)); //[2]baloon非表示の処理を着火
            }
            });
            TKS_BL.show(elem,msg,"top");
            find++; 
        }

        //ハイレベルレッスン教材の見せるレッスン(バルーンを表示)
        if (jQuery(e).text().indexOf('解説') > -1){
    
            let msg2 = '講師様専用タブ\n生徒様には表示されません。';
            let elem2 = jQuery(e);
            
            elem2.on({'mouseenter' : function(){
                TKS_BL.show(jQuery(this),msg2,"right"); //[1]baloon表示の処理を着火
            },'mouseleave' : function(){
                TKS_BL.hide(jQuery(this)); //[2]baloon非表示の処理を着火
            }
            });
            TKS_BL.show(elem2,msg2,"right");
            find++;
        }
        
        if (find > 1) return false;
    }); 
});