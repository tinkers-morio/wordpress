jQuery(document).ready(function() {

    var tks_baloon;
    
    tks_baloon = {
        //[1]baloon表示の処理-----------------------------
        show: function(dom,text,pos="left"){
            
            dom.showBalloon({
                contents: text,
                position: pos,
                html:true,
                tipSize:15,
                offsetY: 5,
                css: {
                    "display": "inline-block",
                    //"fontSize": ".9rem",
                    "fontSize": "1.0em",
                    "border-radius": "10px",
                    "padding": "10px",
                    //"border": "solid 2px #ffffff",
                    //"border": "none",
                    "boxShadow": "0 10px 25px 0 rgba(0, 0, 0, .5)",
                    "background-color": "#e71075",
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