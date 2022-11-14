jQuery(document).ready(function() {
    //申し込みページの「ホームへ戻るボタン」を一つ前のページに戻るリンクへ変更
    // let target = jQuery("#pmpro_levels-return-home");
    // target.text("戻る");
    // target.href = "#";
    // target.on('click',function(){
    //     window.history.back(); 
    //     return false;
    // });
    let target = jQuery("#pmpro_levels-return-home");
    target.after('<button id="back_btn">戻る</button>');
    jQuery("#back_btn").on('click',function(){
        window.history.back(); 
        return false;
    });
    target.remove();
    //document.getElementById("pmpro_levels-return-home").href = "#";
    //document.getElementById("pmpro_levels-return-home").addEventListener("click",function(){
         //window.history.back(); 
         //return false;
    // });
    
    
    
});