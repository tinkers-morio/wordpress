jQuery(document).ready(function() {
    
    var find1 = 0;
    var find2 = 0;

    jQuery(".menu-link").each(function(i,e){
        //if (jQuery(e).text().indexOf('生徒管理') > -1){
        if (jQuery(e).text().indexOf('お子さま') > -1){
            if (find1 == 0){
                let msg = '生徒様の登録や進捗状況を見るにはこちら';
                let elem = jQuery(e);
                
                elem.on({'mouseenter' : function(){
                    TKS_BL.show(jQuery(this),msg,"top"); //[1]baloon表示の処理を着火
                },'mouseleave' : function(){
                    TKS_BL.hide(jQuery(this)); //[2]baloon非表示の処理を着火
                }
                });
                TKS_BL.show(elem,msg,"top");
                find1++;
            }
            //return false;
        }
        if (jQuery(e).text().indexOf('コース') > -1){
        //if (jQuery(e).text().indexOf('コース一覧') > -1){
            if (find2 == 0){
                let msg = '受講開始するならはこちら';
                let elem = jQuery(e);
                
                elem.on({'mouseenter' : function(){
                    TKS_BL.show(jQuery(this),msg,"bottom"); //[1]baloon表示の処理を着火
                },'mouseleave' : function(){
                    TKS_BL.hide(jQuery(this)); //[2]baloon非表示の処理を着火
                }
                });
                TKS_BL.show(elem,msg,"bottom");
                find2++;
            }
            //return false;
        }

        if (find1 == 1 && find2 == 1) return false;
    }); 
});