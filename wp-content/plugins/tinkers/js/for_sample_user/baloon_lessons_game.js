jQuery(document).ready(function() {
    
    jQuery(".ld-button").each(function(i,e){
        
        //gemeレッスン教材の見せるレッスン(バルーンを表示)
        if (jQuery(e).text().indexOf('Scratchを開く') > -1){
            
            let msg = '作品作りに必要なパーツ(スプライト(キャラクター)、BGMなど)が予め用意されたスクラッチを開きます。';
            let elem = jQuery(e);
            
            elem.on({'mouseenter' : function(){
                TKS_BL.show(jQuery(this),msg); //[1]baloon表示の処理を着火
            },'mouseleave' : function(){
                TKS_BL.hide(jQuery(this)); //[2]baloon非表示の処理を着火
            }
            });
            TKS_BL.show(elem,msg);
            return false;
        }
    }); 
});