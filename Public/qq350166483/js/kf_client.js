$(document).ready(function(){$(".side ul li").hover(function(){$(this).find(".sidebox").stop().animate({width:"200px"},200).css({opacity:"1",filter:"Alpha(opacity=100)",background:"#e74e19"})},function(){$(this).find(".sidebox").stop().animate({width:"84px"},200).css({opacity:"0.8",filter:"Alpha(opacity=80)",background:"#5e5e5e"})});$(".im_client").click(function(){var e="http://btc9.udesk.cn/im_client?web_plugin_id=23806";var d=536;var a=566;var c=(window.screen.availHeight-30-a)/2;var b=(window.screen.availWidth-10-d)/2;window.open(e,"","height="+a+",scrollbars=yes, width="+d+", top="+c+", left="+b)})});function goTop(){$("html,body").animate({scrollTop:0},500)};