function selectTag(d,c,b){var a=d.replace("tagContent","");if(a==0){$("#tn_change li").eq(0).addClass("selectTagbuy");$("#tn_change li").eq(1).removeClass("selectTagsell");$("#tn_change li").eq(2).removeClass("selectTagtrust")}else{if(a==1){$("#tn_change li").eq(0).removeClass("selectTagbuy");$("#tn_change li").eq(1).addClass("selectTagsell");$("#tn_change li").eq(2).removeClass("selectTagtrust")}else{if(a==2){$("#tn_change li").eq(0).removeClass("selectTagbuy");$("#tn_change li").eq(1).removeClass("selectTagsell");$("#tn_change li").eq(2).addClass("selectTagtrust")}}}$("#tagContent"+a).show().siblings().hide()}$(function(){$(".nav li:eq(1)").css({position:"relative"}).find("a").css({width:"120px","text-align":"left","text-indent":"18px",background:"url(/static/newfront/images/huadownsj.png) 93px 32px no-repeat"});$(".nav li:eq(1) a").mouseover(function(){$(this).css({background:"url(../images/huaupsj.png) #f4f4f4 93px 34px no-repeat"});$(this).next().show()});$(".nav li:eq(1)").mouseout(function(){$(this).find("a").css({background:"url(../images/huadownsj.png) 93px 32px no-repeat"});$(this).find(".typeList").hide()});$(".typeList p").each(function(){$(this).mouseover(function(){$(this).css({color:"#f8b72d"});$(this).parent().show();$(this).parent().prev().css({background:"url(../images/huaupsj.png) transparent 93px 34px no-repeat"})}).mouseout(function(){$(this).css({color:"#666"});$(this).parent().hide()})});$(".topheaderleft").mouseenter(function(){$(".hideinfoslide").show();$(".toprightsj").addClass("active")}).mouseleave(function(){$(".hideinfoslide").hide();$(".toprightsj").removeClass("active")});$(".toplanguage").mouseenter(function(){$(".hselectlang").show();$(this).addClass("active")}).mouseleave(function(){$(".hselectlang").hide();$(this).removeClass("active")});$(".hselectlang li a").each(function(){$(this).click(function(){var a=$(this).find("img").attr("src");$(".toplanguage").find(".hcurimg").attr("src",a);$(".hselectlang").hide()})});$(".topwapmap").mouseenter(function(){$(".wapmaplist").show();$(".topwapmap").css({"background-color":"#fff"})}).mouseleave(function(){$(".wapmaplist").hide();$(".topwapmap").css({"background-color":"#000"})});$(".topinfo").mouseenter(function(){$(this).css({"background-color":"#fff"});$(".infoEmail").addClass("active");$(".infoHideDiv").show()}).mouseleave(function(){$(this).css({"background-color":"#000"});$(".infoEmail").removeClass("active");$(".infoHideDiv").hide()});$(".has_huobitype li:even").css({background:"#fafafa"});$(".hello").mouseenter(function(){$(".hhide_asinfolist").show();$(".hshow_asinfort").addClass("active")}).mouseleave(function(){$(".hhide_asinfolist").hide();$(".hshow_asinfort").removeClass("active")})});