var ChangeNum={dom:"cumulative",clkCls:"number_box",clkUnitCls:"unit",clkTopCls:"top",clkBtmCls:"btm",fCls:"add_f",wCls:"add_w",change_c:"",old_quota:"",quota:"",ClkUnit:function(d,b,a,c){arguments[3]||(c=""),this.update=function(){this.updateTxt(),this.val>this.maxVal&&(this.setVal(this.minVal),this.period()),this.val<this.minVal&&(this.setVal(this.maxVal),this.period())},this.incVal=function(){this.val++,this.update()},this.decVal=function(){this.val--,this.update()},this.updateTxt=function(){this.val>9?this.text=this.val:this.text=this.val},this.setVal=function(e){this.val=e,this.updateTxt()},this.pane=document.createElement("div"),this.pane.className=ChangeNum.clkUnitCls+" "+c,this.setVal(d),this.minVal=b,this.maxVal=a,this.topbak=document.createElement("div"),this.topbak.txt=document.createElement("span"),this.topbak.className=ChangeNum.clkTopCls,this.topfnt=document.createElement("div"),this.topfnt.txt=document.createElement("span"),this.topfnt.className=ChangeNum.clkTopCls,this.btmbak=document.createElement("div"),this.btmbak.txt=document.createElement("span"),this.btmbak.className=ChangeNum.clkBtmCls,this.btmfnt=document.createElement("div"),this.btmfnt.txt=document.createElement("span"),this.btmfnt.className=ChangeNum.clkBtmCls,this.pane.appendChild(this.topbak),this.topbak.appendChild(this.topbak.txt),this.pane.appendChild(this.topfnt),this.topfnt.appendChild(this.topfnt.txt),this.pane.appendChild(this.btmbak),this.btmbak.appendChild(this.btmbak.txt),this.pane.appendChild(this.btmfnt),this.btmfnt.appendChild(this.btmfnt.txt),this.mtx=!1,this.animateReset=function(){ChangeNum.transform(this.btmfnt,""),ChangeNum.transform(this.btmbak,""),this.btmfnt.txt.innerHTML=this.text,this.topbak.txt.innerHTML=this.text,this.topfnt.txt.innerHTML=this.text,this.btmbak.txt.innerHTML=this.text,ChangeNum.transform(this.topfnt,""),ChangeNum.transform(this.topbak,"")},this.period=null,this.turnDown=function(i){var h=this;if(!this.mtx){this.setVal(i);var g=0,e=90;this.topbak.txt.innerHTML=this.text,ChangeNum.transform(h.topfnt,"rotateX(0deg)");var f=setInterval(function(){if(ChangeNum.transform(h.topfnt,"rotateX("+g+"deg)"),g-=10,-90>=g){ChangeNum.transform(h.topfnt,"rotateX(0deg)"),h.topfnt.txt.innerHTML=h.text,ChangeNum.transform(h.btmfnt,"rotateX(90deg)"),h.btmfnt.txt.innerHTML=h.text;var j=setInterval(function(){0>=e&&(clearInterval(j),h.animateReset(),h.mtx=!1),ChangeNum.transform(h.btmfnt,"rotateX("+e+"deg)"),e-=10},30);clearInterval(f)}},30)}},this.animateReset()},Clock:function(f,g){$("#"+ChangeNum.dom).html("");this.pane=document.createElement("div"),this.pane.className=ChangeNum.clkCls;var a=new Array,h=ChangeNum.quota.toString().split(""),b=h.length,e=b%3,j=60*b+20*parseInt(b/3)+8*(b-parseInt(b/3));0==e&&(j-=20);f.style.width=j+"px";for(var d=0;b>d;d++){var c="";0==d?c=ChangeNum.fCls:(d-e)%3==0&&(c=ChangeNum.wCls),this.ng=new ChangeNum.ClkUnit(h[d],0,9,c),a.push(this.ng),this.pane.appendChild(this.ng.pane)}f.appendChild(this.pane);this.timer=null,this.make=function(){ChangeNum.quota.length!=$("input[name='amount']").val().length&&($("input[name='amount']").val(ChangeNum.quota),$("#"+ChangeNum.dom).html(""),ChangeNum.change_c=new ChangeNum.Clock(document.getElementById("cumulative"),ChangeNum.quota));var q=parseInt((ChangeNum.quota/100000000).toFixed(4)),p=parseInt((ChangeNum.quota-100000000*q)/10000),k=parseInt(ChangeNum.quota/1000000000),m=parseInt((ChangeNum.quota-1000000000*k)/1000000);if(q>0){$(".yiyi1").show();$(".yiyi2").show()}$("#yi").html(q),$("#wan").html(p),$("#bi").html(k),$("#mi").html(m);var n=ChangeNum.old_quota.toString().split("");var o=ChangeNum.quota.toString().split("");for(var l=o.length-1;l>=0;l--){if(n[l]!=o[l]){a[l].turnDown(o[l])}}ChangeNum.old_quota=ChangeNum.quota},this.start=function(){this.make();var i=this;this.timer=setInterval(function(){i.make()},5000)},this.pause=function(){clearInterval(this.timer)},this.start()},transform:function(b,c){try{b.style.WebkitTransform=c,b.style.MozTransform=c,b.style.msTransform=c,b.style.OTransform=c,b.style.transform=c}catch(a){}},};window.setInterval(function(){$.get("/Ajax/allsum?t="+Math.random(),function(a){ChangeNum.quota=a;ChangeNum.change_c=new ChangeNum.Clock(document.getElementById(ChangeNum.dom),a)},"json")},3000);