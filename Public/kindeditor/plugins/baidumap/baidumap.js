KindEditor.plugin("baidumap",function(a){var f=this,e="baidumap",b=f.lang(e+".");var d=a.undef(f.mapWidth,558);var c=a.undef(f.mapHeight,360);f.clickToolbar(e,function(){var l=['<div style="padding:10px 20px;">','<div class="ke-header">','<div class="ke-left">',b.address+' <input id="kindeditor_plugin_map_address" name="address" class="ke-input-text" value="" style="width:200px;" /> ','<span class="ke-button-common ke-button-outer">','<input type="button" name="searchBtn" class="ke-button-common ke-button" value="'+b.search+'" />',"</span>","</div>",'<div class="ke-right">','<input type="checkbox" id="keInsertDynamicMap" name="insertDynamicMap" value="1" /> <label for="keInsertDynamicMap">'+b.insertDynamicMap+"</label>","</div>",'<div class="ke-clearfix"></div>',"</div>",'<div class="ke-map" style="width:'+d+"px;height:"+c+'px;"></div>',"</div>"].join("");var i=f.createDialog({name:e,width:d+42,title:f.lang(e),body:l,yesBtn:{name:f.lang("yes"),click:function(s){var t=p.map;var r=t.getCenter();var q=r.lng+","+r.lat;var v=t.getZoom();var u=[h[0].checked?f.pluginsPath+"baidumap/index.html":"http://api.map.baidu.com/staticimage","?center="+encodeURIComponent(q),"&zoom="+encodeURIComponent(v),"&width="+d,"&height="+c,"&markers="+encodeURIComponent(q),"&markerStyles="+encodeURIComponent("l,A")].join("");if(h[0].checked){f.insertHtml('<iframe src="'+u+'" frameborder="0" style="width:'+(d+2)+"px;height:"+(c+2)+'px;"></iframe>')}else{f.exec("insertimage",u)}f.hideDialog().focus()}},beforeRemove:function(){o.remove();if(k){k.write("")}m.remove()}});var j=i.div,g=a('[name="address"]',j),o=a('[name="searchBtn"]',j),h=a('[name="insertDynamicMap"]',i.div),p,k;var m=a('<iframe class="ke-textarea" frameborder="0" src="'+f.pluginsPath+'baidumap/map.html" style="width:'+d+"px;height:"+c+'px;"></iframe>');function n(){p=m[0].contentWindow;k=a.iframeDoc(m)}m.bind("load",function(){m.unbind("load");if(a.IE){n()}else{setTimeout(n,0)}});a(".ke-map",j).replaceWith(m);o.click(function(){p.search(g.val())})})});