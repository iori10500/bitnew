!function(){function H(c,b,a){return c.getAttribute(b)||a}function C(a){return document.getElementsByTagName(a)}function E(){var a=C("script"),c=a.length,b=a[c-1];return{l:c,z:H(b,"zIndex",999),o:H(b,"opacity",0.9),c:H(b,"color","255,255,255"),n:H(b,"count",300)}}function D(){K=N.width=window.innerWidth||document.documentElement.clientWidth||document.body.clientWidth,G=N.height=480}function v(){y.clearRect(0,0,K,G);var d=[z].concat(M);var e,c,a,b,g,f;M.forEach(function(h){h.x+=h.xa,h.y+=h.ya,h.xa*=h.x>K||h.x<0?-1:1,h.ya*=h.y>G||h.y<0?-1:1,y.fillRect(h.x-0.5,h.y-0.5,1,1);for(c=0;c<d.length;c++){e=d[c];if(h!==e&&null!==e.x&&null!==e.y){b=h.x-e.x,g=h.y-e.y,f=b*b+g*g;f<e.max&&(e===z&&f>=e.max/2&&(h.x-=0.03*b,h.y-=0.03*g),a=(e.max-f)/e.max,y.beginPath(),y.lineWidth=a/2,y.strokeStyle="rgba("+L.c+","+(a+0.2)+")",y.moveTo(h.x,h.y),y.lineTo(e.x,e.y),y.stroke())}}d.splice(d.indexOf(h),1)}),F(v)}var N=document.createElement("canvas"),L=E(),w="c_n"+L.l,y=N.getContext("2d"),K,G,F=window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||window.oRequestAnimationFrame||window.msRequestAnimationFrame||function(a){window.setTimeout(a,1000/45)},i=Math.random,z={x:null,y:null,max:20000};N.id=w;N.style.cssText="position:absolute;top:115px;pointer-events:none;width:100%;z-index:"+L.z+";opacity:"+L.o;C("body")[0].appendChild(N);D(),window.onresize=D;window.onmousemove=function(a){a=a||window.event,z.x=a.clientX,z.y=a.clientY-115},window.onmouseout=function(){z.x=null,z.y=null};for(var M=[],I=0;L.n>I;I++){var B=i()*K,A=i()*G,J=2*i()-1,x=2*i()-1;M.push({x:B,y:A,xa:J,ya:x,max:9000})}setTimeout(function(){v()},10)}();