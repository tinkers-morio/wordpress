jQuery(document).ready(function() {

    jQuery(".learndash-resume-button").each(function(i,e){
        if (jQuery(e).attr('title').indexOf('Resume ') > -1){
            let msg = '前回学習したレッスンページやトピックページにリンクします。続きから始められるので迷いません！';
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