jQuery(document).ready(function() {

    var tks_baloon;
    var ua = navigator.userAgent;
    if(ua.indexOf('iPhone') > 0 || ua.indexOf('iPad') > 0 || ua.indexOf('Android') > 0){
        var tabFlag = "top"; //iPhone、iPad、Androidの場合trueに
    }else{
        var tabFlag = "right"; //それ以外の場合falseに
    }
    tks_baloon = {
        //[1]baloon表示の処理-----------------------------
        show: function(dom,text,pos=tabFlag){
            
            dom.showBalloon({
                contents: text,
                position: pos,
                showDuration:1000,
                hideDuration: 100,
                html:true,
                tipSize:15,
                offsetX: 10,
                css: {
                    "display": "inline-block",
                    "color":"black",
                    "fontSize": "0.8rem",
                    "border-radius": "5px",
                    "padding": "8px",
                    //"border": "solid 2px #ffffff",
                    //"border": "none",
                    "boxShadow": "0 10px 25px 0 rgba(0, 0, 0, .5)",
                    "background-color": "#FFFFF0",
                    "opacity":"0.9"
                    },
            }).addClass('active');
        },

        //[2]baloon非表示の処理
        hide: function(dom){
                dom.hideBalloon().removeClass('active');
        }
    }

    window.TKS_BL = tks_baloon;

});