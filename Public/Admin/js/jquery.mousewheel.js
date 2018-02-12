/* Copyright (c) 2013 Brandon Aaron (http://brandonaaron.net)
 * Licensed under the MIT License (LICENSE.txt).
 *
 * Thanks to: http://adomas.org/javascript-mouse-wheel/ for some pointers.
 * Thanks to: Mathias Bank(http://www.mathias-bank.de) for a scope bug fix.
 * Thanks to: Seamus Leahy for adding deltaX and deltaY
 *
 * Version: 3.1.3
 *
 * Requires: 1.2.2+
 */
(function(a){if(typeof define==="function"&&define.amd){define(["jquery"],a)}else{if(typeof exports==="object"){module.exports=a}else{a(jQuery)}}}(function(a){var g=["wheel","mousewheel","DOMMouseScroll","MozMousePixelScroll"];var f="onwheel" in document||document.documentMode>=9?["wheel"]:["mousewheel","DomMouseScroll","MozMousePixelScroll"];var d,e;if(a.event.fixHooks){for(var c=g.length;c;){a.event.fixHooks[g[--c]]=a.event.mouseHooks}}a.event.special.mousewheel={setup:function(){if(this.addEventListener){for(var h=f.length;h;){this.addEventListener(f[--h],b,false)}}else{this.onmousewheel=b}},teardown:function(){if(this.removeEventListener){for(var h=f.length;h;){this.removeEventListener(f[--h],b,false)}}else{this.onmousewheel=null}}};a.fn.extend({mousewheel:function(h){return h?this.bind("mousewheel",h):this.trigger("mousewheel")},unmousewheel:function(h){return this.unbind("mousewheel",h)}});function b(n){var p=n||window.event,j=[].slice.call(arguments,1),k=0,l=0,m=0,h=0,i=0,o;n=a.event.fix(p);n.type="mousewheel";if(p.wheelDelta){k=p.wheelDelta}if(p.detail){k=p.detail*-1}if(p.deltaY){m=p.deltaY*-1;k=m}if(p.deltaX){l=p.deltaX;k=l*-1}if(p.wheelDeltaY!==undefined){m=p.wheelDeltaY}if(p.wheelDeltaX!==undefined){l=p.wheelDeltaX*-1}h=Math.abs(k);if(!d||h<d){d=h}i=Math.max(Math.abs(m),Math.abs(l));if(!e||i<e){e=i}o=k>0?"floor":"ceil";k=Math[o](k/d);l=Math[o](l/e);m=Math[o](m/e);j.unshift(n,k,l,m);return(a.event.dispatch||a.event.handle).apply(this,j)}}));