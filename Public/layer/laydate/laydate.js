!function(i){var j={path:"",defSkin:"default",format:"YYYY-MM-DD",min:"1900-01-01 00:00:00",max:"2099-12-31 23:59:59",isv:!1},k={},l=document,m="createElement",n="getElementById",o="getElementsByTagName",p=["laydate_box","laydate_void","laydate_click","LayDateSkin","skins/","/laydate.css"];i.laydate=function(a){a=a||{};try{p.event=i.event?i.event:laydate.caller.arguments[0]}catch(c){}return k.run(a),laydate},laydate.v="1.1",k.getPath=function(){var b=document.scripts,d=b[b.length-1].src;return j.path?j.path:d.substring(0,d.lastIndexOf("/")+1)}(),k.use=function(c,d){var e=l[m]("link");e.type="text/css",e.rel="stylesheet",e.href=k.getPath+c+p[5],d&&(e.id=d),l[o]("head")[0].appendChild(e),e=null},k.trim=function(b){return b=b||"",b.replace(/^\s|\s$/g,"").replace(/\s+/g," ")},k.digit=function(b){return 10>b?"0"+(0|b):b},k.stopmp=function(a){return a=a||i.event,a.stopPropagation?a.stopPropagation():a.cancelBubble=!0,this},k.each=function(e,f){for(var g=0,h=e.length;h>g&&f(g,e[g])!==!1;g++){}},k.hasClass=function(c,d){return c=c||{},new RegExp("\\b"+d+"\\b").test(c.className)},k.addClass=function(c,d){return c=c||{},k.hasClass(c,d)||(c.className+=" "+d),c.className=k.trim(c.className),this},k.removeClass=function(c,e){if(c=c||{},k.hasClass(c,e)){var f=new RegExp("\\b"+e+"\\b");c.className=c.className.replace(f,"")}return this},k.removeCssAttr=function(d,e){var f=d.style;f.removeProperty?f.removeProperty(e):f.removeAttribute(e)},k.shde=function(c,d){c.style.display=d?"none":"block"},k.query=function(c){var f,d,g,q,r;return c=k.trim(c).split(" "),d=l[n](c[0].substr(1)),d?c[1]?/^\./.test(c[1])?(q=c[1].substr(1),r=new RegExp("\\b"+q+"\\b"),f=[],g=l.getElementsByClassName?d.getElementsByClassName(q):d[o]("*"),k.each(g,function(e,h){r.test(h.className)&&f.push(h)}),f[0]?f:""):(f=d[o](c[1]),f[0]?d[o](c[1]):""):d:void 0},k.on=function(a,c,f){return a.attachEvent?a.attachEvent("on"+c,function(){f.call(a,i.even)}):a.addEventListener(c,f,!1),k},k.stopMosup=function(c,d){"mouseup"!==c&&k.on(d,"mouseup",function(b){k.stopmp(b)})},k.run=function(c){var q,r,t,h=k.query,s=p.event;try{t=s.target||s.srcElement||{}}catch(u){t={}}if(q=c.elem?h(c.elem):t,s&&t.tagName){if(!q||q===k.elem){return}k.stopMosup(s.type,q),k.stopmp(s),k.view(q,c),k.reshow()}else{r=c.event||"click",k.each((0|q.length)>0?q:[q],function(a,e){k.stopMosup(r,e),k.on(e,r,function(d){k.stopmp(d),e!==k.elem&&(k.view(e,c),k.reshow())})})}},k.scroll=function(b){return b=b?"scrollLeft":"scrollTop",l.body[b]|l.documentElement[b]},k.winarea=function(b){return document.documentElement[b?"clientWidth":"clientHeight"]},k.isleap=function(b){return 0===b%4&&0!==b%100||0===b%400},k.checkVoid=function(c,f,g){var h=[];return c=0|c,f=0|f,g=0|g,c<k.mins[0]?h=["y"]:c>k.maxs[0]?h=["y",1]:c>=k.mins[0]&&c<=k.maxs[0]&&(c==k.mins[0]&&(f<k.mins[1]?h=["m"]:f==k.mins[1]&&g<k.mins[2]&&(h=["d"])),c==k.maxs[0]&&(f>k.maxs[1]?h=["m",1]:f==k.maxs[1]&&g>k.maxs[2]&&(h=["d",1]))),h},k.timeVoid=function(c,d){if(k.ymd[1]+1==k.mins[1]&&k.ymd[2]==k.mins[2]){if(0===d&&c<k.mins[3]){return 1}if(1===d&&c<k.mins[4]){return 1}if(2===d&&c<k.mins[5]){return 1}}else{if(k.ymd[1]+1==k.maxs[1]&&k.ymd[2]==k.maxs[2]){if(0===d&&c>k.maxs[3]){return 1}if(1===d&&c>k.maxs[4]){return 1}if(2===d&&c>k.maxs[5]){return 1}}}return c>(d?59:23)?1:void 0},k.check=function(){var c=k.options.format.replace(/YYYY|MM|DD|hh|mm|ss/g,"\\d+\\").replace(/\\$/g,""),g=new RegExp(c),h=k.elem[p.elemv],q=h.match(/\d+/g)||[],r=k.checkVoid(q[0],q[1],q[2]);if(""!==h.replace(/\s/g,"")){if(!g.test(h)){return k.elem[p.elemv]="",k.msg("日期不符合格式，请重新选择。"),1}if(r[0]){return k.elem[p.elemv]="",k.msg("日期不在有效期内，请重新选择。"),1}r.value=k.elem[p.elemv].match(g).join(),q=r.value.match(/\d+/g),q[1]<1?(q[1]=1,r.auto=1):q[1]>12?(q[1]=12,r.auto=1):q[1].length<2&&(r.auto=1),q[2]<1?(q[2]=1,r.auto=1):q[2]>k.months[(0|q[1])-1]?(q[2]=31,r.auto=1):q[2].length<2&&(r.auto=1),q.length>3&&(k.timeVoid(q[3],0)&&(r.auto=1),k.timeVoid(q[4],1)&&(r.auto=1),k.timeVoid(q[5],2)&&(r.auto=1)),r.auto?k.creation([q[0],0|q[1],0|q[2]],1):r.value!==k.elem[p.elemv]&&(k.elem[p.elemv]=r.value)}},k.months=[31,null,31,30,31,30,31,31,30,31,30,31],k.viewDate=function(c,e,h){var q=(k.query,{}),r=new Date;c<(0|k.mins[0])&&(c=0|k.mins[0]),c>(0|k.maxs[0])&&(c=0|k.maxs[0]),r.setFullYear(c,e,h),q.ymd=[r.getFullYear(),r.getMonth(),r.getDate()],k.months[1]=k.isleap(q.ymd[0])?29:28,r.setFullYear(q.ymd[0],q.ymd[1],1),q.FDay=r.getDay(),q.PDay=k.months[0===e?11:e-1]-q.FDay+1,q.NDay=1,k.each(p.tds,function(f,s){var v,t=q.ymd[0],u=q.ymd[1]+1;s.className="",f<q.FDay?(s.innerHTML=v=f+q.PDay,k.addClass(s,"laydate_nothis"),1===u&&(t-=1),u=1===u?12:u-1):f>=q.FDay&&f<q.FDay+k.months[q.ymd[1]]?(s.innerHTML=v=f-q.FDay+1,f-q.FDay+1===q.ymd[2]&&(k.addClass(s,p[2]),q.thisDay=s)):(s.innerHTML=v=q.NDay++,k.addClass(s,"laydate_nothis"),12===u&&(t+=1),u=12===u?1:u+1),k.checkVoid(t,u,v)[0]&&k.addClass(s,p[1]),k.options.festival&&k.festival(s,u+"."+v),s.setAttribute("y",t),s.setAttribute("m",u),s.setAttribute("d",v),t=u=v=null}),k.valid=!k.hasClass(q.thisDay,p[1]),k.ymd=q.ymd,p.year.value=k.ymd[0]+"年",p.month.value=k.digit(k.ymd[1]+1)+"月",k.each(p.mms,function(f,g){var s=k.checkVoid(k.ymd[0],(0|g.getAttribute("m"))+1);"y"===s[0]||"m"===s[0]?k.addClass(g,p[1]):k.removeClass(g,p[1]),k.removeClass(g,p[2]),s=null}),k.addClass(p.mms[k.ymd[1]],p[2]),q.times=[0|k.inymd[3]||0,0|k.inymd[4]||0,0|k.inymd[5]||0],k.each(new Array(3),function(b){k.hmsin[b].value=k.digit(k.timeVoid(q.times[b],b)?0|k.mins[b+3]:0|q.times[b])}),k[k.valid?"removeClass":"addClass"](p.ok,p[1])},k.festival=function(d,e){var f;switch(e){case"1.1":f="元旦";break;case"3.8":f="妇女";break;case"4.5":f="清明";break;case"5.1":f="劳动";break;case"6.1":f="儿童";break;case"9.10":f="教师";break;case"10.1":f="国庆"}f&&(d.innerHTML=f),f=null},k.viewYears=function(c){var e=k.query,f="";k.each(new Array(14),function(a){f+=7===a?"<li "+(parseInt(p.year.value)===c?'class="'+p[2]+'"':"")+' y="'+c+'">'+c+"年</li>":'<li y="'+(c-7+a)+'">'+(c-7+a)+"年</li>"}),e("#laydate_ys").innerHTML=f,k.each(e("#laydate_ys li"),function(d,g){"y"===k.checkVoid(g.getAttribute("y"))[0]?k.addClass(g,p[1]):k.on(g,"click",function(b){k.stopmp(b).reshow(),k.viewDate(0|this.getAttribute("y"),k.ymd[1],k.ymd[2])})})},k.initDate=function(){var a=(k.query,new Date),b=k.elem[p.elemv].match(/\d+/g)||[];b.length<3&&(b=k.options.start.match(/\d+/g)||[],b.length<3&&(b=[a.getFullYear(),a.getMonth()+1,a.getDate()])),k.inymd=b,k.viewDate(b[0],b[1]-1,b[2])},k.iswrite=function(){var c=k.query,d={time:c("#laydate_hms")};k.shde(d.time,!k.options.istime),k.shde(p.oclear,!("isclear" in k.options?k.options.isclear:1)),k.shde(p.otoday,!("istoday" in k.options?k.options.istoday:1)),k.shde(p.ok,!("issure" in k.options?k.options.issure:1))},k.orien=function(c,f){var g,h=k.elem.getBoundingClientRect();c.style.left=h.left+(f?0:k.scroll(1))+"px",g=h.bottom+c.offsetHeight/1.5<=k.winarea()?h.bottom-1:h.top>c.offsetHeight/1.5?h.top-c.offsetHeight+1:k.winarea()-c.offsetHeight,c.style.top=g+(f?0:k.scroll())+"px"},k.follow=function(b){k.options.fixed?(b.style.position="fixed",k.orien(b,1)):(b.style.position="absolute",k.orien(b))},k.viewtb=function(){var c,d=[],e=["日","一","二","三","四","五","六"],g={},q=l[m]("table"),r=l[m]("thead");return r.appendChild(l[m]("tr")),g.creath=function(f){var h=l[m]("th");h.innerHTML=e[f],r[o]("tr")[0].appendChild(h),h=null},k.each(new Array(6),function(a){d.push([]),c=q.insertRow(0),k.each(new Array(7),function(b){d[a][b]=0,0===a&&g.creath(b),c.insertCell(b)})}),q.insertBefore(r,q.children[0]),q.id=q.className="laydate_table",c=d=null,q.outerHTML.toLowerCase()}(),k.view=function(b,c){var e,d=k.query,h={};c=c||b,k.elem=b,k.options=c,k.options.format||(k.options.format=j.format),k.options.start=k.options.start||"",k.mm=h.mm=[k.options.min||j.min,k.options.max||j.max],k.mins=h.mm[0].match(/\d+/g),k.maxs=h.mm[1].match(/\d+/g),p.elemv=/textarea|input/.test(k.elem.tagName.toLocaleLowerCase())?"value":"innerHTML",k.box?k.shde(k.box):(e=l[m]("div"),e.id=p[0],e.className=p[0],e.style.cssText="position: absolute;",e.setAttribute("name","laydate-v"+laydate.v),e.innerHTML=h.html='<div class="laydate_top"><div class="laydate_ym laydate_y" id="laydate_YY"><a class="laydate_choose laydate_chprev laydate_tab"><cite></cite></a><input id="laydate_y" readonly><label></label><a class="laydate_choose laydate_chnext laydate_tab"><cite></cite></a><div class="laydate_yms"><a class="laydate_tab laydate_chtop"><cite></cite></a><ul id="laydate_ys"></ul><a class="laydate_tab laydate_chdown"><cite></cite></a></div></div><div class="laydate_ym laydate_m" id="laydate_MM"><a class="laydate_choose laydate_chprev laydate_tab"><cite></cite></a><input id="laydate_m" readonly><label></label><a class="laydate_choose laydate_chnext laydate_tab"><cite></cite></a><div class="laydate_yms" id="laydate_ms">'+function(){var f="";return k.each(new Array(12),function(a){f+='<span m="'+a+'">'+k.digit(a+1)+"月</span>"}),f}()+"</div></div></div>"+k.viewtb+'<div class="laydate_bottom"><ul id="laydate_hms"><li class="laydate_sj">时间</li><li><input readonly>:</li><li><input readonly>:</li><li><input readonly></li></ul><div class="laydate_time" id="laydate_time"></div><div class="laydate_btn"><a id="laydate_clear">清空</a><a id="laydate_today">今天</a><a id="laydate_ok">确认</a></div>'+(j.isv?'<a href="http://sentsin.com/layui/laydate/" class="laydate_v" target="_blank">laydate-v'+laydate.v+"</a>":"")+"</div>",l.body.appendChild(e),k.box=d("#"+p[0]),k.events(),e=null),k.follow(k.box),c.zIndex?k.box.style.zIndex=c.zIndex:k.removeCssAttr(k.box,"z-index"),k.stopMosup("click",k.box),k.initDate(),k.iswrite(),k.check()},k.reshow=function(){return k.each(k.query("#"+p[0]+" .laydate_show"),function(c,d){k.removeClass(d,"laydate_show")}),this},k.close=function(){k.reshow(),k.shde(k.query("#"+p[0]),1),k.elem=null},k.parse=function(b,c,f){return b=b.concat(c),f=f||(k.options?k.options.format:j.format),f.replace(/YYYY|MM|DD|hh|mm|ss/g,function(){return b.index=0|++b.index,k.digit(b[b.index])})},k.creation=function(c,d){var g=(k.query,k.hmsin),h=k.parse(c,[g[0].value,g[1].value,g[2].value]);k.elem[p.elemv]=h,d||(k.close(),"function"==typeof k.options.choose&&k.options.choose(h))},k.events=function(){var a=k.query,c={box:"#"+p[0]};k.addClass(l.body,"laydate_body"),p.tds=a("#laydate_table td"),p.mms=a("#laydate_ms span"),p.year=a("#laydate_y"),p.month=a("#laydate_m"),k.each(a(c.box+" .laydate_ym"),function(d,e){k.on(e,"click",function(f){k.stopmp(f).reshow(),k.addClass(this[o]("div")[0],"laydate_show"),d||(c.YY=parseInt(p.year.value),k.viewYears(c.YY))})}),k.on(a(c.box),"click",function(){k.reshow()}),c.tabYear=function(b){0===b?k.ymd[0]--:1===b?k.ymd[0]++:2===b?c.YY-=14:c.YY+=14,2>b?(k.viewDate(k.ymd[0],k.ymd[1],k.ymd[2]),k.reshow()):k.viewYears(c.YY)},k.each(a("#laydate_YY .laydate_tab"),function(d,e){k.on(e,"click",function(f){k.stopmp(f),c.tabYear(d)})}),c.tabMonth=function(b){b?(k.ymd[1]++,12===k.ymd[1]&&(k.ymd[0]++,k.ymd[1]=0)):(k.ymd[1]--,-1===k.ymd[1]&&(k.ymd[0]--,k.ymd[1]=11)),k.viewDate(k.ymd[0],k.ymd[1],k.ymd[2])},k.each(a("#laydate_MM .laydate_tab"),function(d,e){k.on(e,"click",function(f){k.stopmp(f).reshow(),c.tabMonth(d)})}),k.each(a("#laydate_ms span"),function(d,e){k.on(e,"click",function(b){k.stopmp(b).reshow(),k.hasClass(this,p[1])||k.viewDate(k.ymd[0],0|this.getAttribute("m"),k.ymd[2])})}),k.each(a("#laydate_table td"),function(d,e){k.on(e,"click",function(b){k.hasClass(this,p[1])||(k.stopmp(b),k.creation([0|this.getAttribute("y"),0|this.getAttribute("m"),0|this.getAttribute("d")]))})}),p.oclear=a("#laydate_clear"),k.on(p.oclear,"click",function(){k.elem[p.elemv]="",k.close()}),p.otoday=a("#laydate_today"),k.on(p.otoday,"click",function(){k.elem[p.elemv]=laydate.now(0,k.options.format),k.close()}),p.ok=a("#laydate_ok"),k.on(p.ok,"click",function(){k.valid&&k.creation([k.ymd[0],k.ymd[1]+1,k.ymd[2]])}),c.times=a("#laydate_time"),k.hmsin=c.hmsin=a("#laydate_hms input"),c.hmss=["小时","分钟","秒数"],c.hmsarr=[],k.msg=function(b,e){var g='<div class="laydte_hsmtex">'+(e||"提示")+"<span>×</span></div>";"string"==typeof b?(g+="<p>"+b+"</p>",k.shde(a("#"+p[0])),k.removeClass(c.times,"laydate_time1").addClass(c.times,"laydate_msg")):(c.hmsarr[b]?g=c.hmsarr[b]:(g+='<div id="laydate_hmsno" class="laydate_hmsno">',k.each(new Array(0===b?24:60),function(d){g+="<span>"+d+"</span>"}),g+="</div>",c.hmsarr[b]=g),k.removeClass(c.times,"laydate_msg"),k[0===b?"removeClass":"addClass"](c.times,"laydate_time1")),k.addClass(c.times,"laydate_show"),c.times.innerHTML=g},c.hmson=function(b,g){var h=a("#laydate_hmsno span"),q=k.valid?null:1;k.each(h,function(d,f){q?k.addClass(f,p[1]):k.timeVoid(d,g)?k.addClass(f,p[1]):k.on(f,"click",function(){k.hasClass(this,p[1])||(b.value=k.digit(0|this.innerHTML))})}),k.addClass(h[0|b.value],"laydate_click")},k.each(c.hmsin,function(d,e){k.on(e,"click",function(f){k.stopmp(f).reshow(),k.msg(d,c.hmss[d]),c.hmson(this,d)})}),k.on(l,"mouseup",function(){var b=a("#"+p[0]);b&&"none"!==b.style.display&&(k.check()||k.close())}).on(l,"keydown",function(e){e=e||i.event;var f=e.keyCode;13===f&&k.creation([k.ymd[0],k.ymd[1]+1,k.ymd[2]])})},k.init=function(){k.use("need"),k.use(p[4]+j.defSkin,p[3]),k.skinLink=k.query("#"+p[3])}(),laydate.reset=function(){k.box&&k.elem&&k.follow(k.box)},laydate.now=function(c,e){var f=new Date(0|c?function(b){return 86400000>b?+new Date+86400000*b:b}(parseInt(c)):+new Date);return k.parse([f.getFullYear(),f.getMonth()+1,f.getDate()],[f.getHours(),f.getMinutes(),f.getSeconds()],e)},laydate.skin=function(b){k.skinLink.href=k.getPath+p[4]+b+p[5]}}(window);