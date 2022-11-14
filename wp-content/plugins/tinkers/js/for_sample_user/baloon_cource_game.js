jQuery(document).ready(function() {

    jQuery(".ld-item-title").each(function(i,e){
        //ゲーム教材の見せるレッスン(バルーンを表示)
        if (jQuery(e).text().indexOf('ジャンピング・モンキーを作ろう！') > -1){
            let msg = '視聴できます';
            let elem = jQuery(e).prev('div');
            
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