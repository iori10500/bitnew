KindEditor.plugin("code",function(a){var c=this,b="code";c.clickToolbar(b,function(){var f=c.lang(b+"."),e=['<div style="padding:10px 20px;">','<div class="ke-dialog-row">','<select class="ke-code-type">','<option value="js">JavaScript</option>','<option value="html">HTML</option>','<option value="css">CSS</option>','<option value="php">PHP</option>','<option value="pl">Perl</option>','<option value="py">Python</option>','<option value="rb">Ruby</option>','<option value="java">Java</option>','<option value="vb">ASP/VB</option>','<option value="cpp">C/C++</option>','<option value="cs">C#</option>','<option value="xml">XML</option>','<option value="bsh">Shell</option>','<option value="">Other</option>',"</select>","</div>",'<textarea class="ke-textarea" style="width:408px;height:260px;"></textarea>',"</div>"].join(""),d=c.createDialog({name:b,width:450,title:c.lang(b),body:e,yesBtn:{name:c.lang("yes"),click:function(j){var l=a(".ke-code-type",d.div).val(),i=g.val(),h=l===""?"":" lang-"+l,k='<pre class="prettyprint'+h+'">\n'+a.escape(i)+"</pre> ";if(a.trim(i)===""){alert(f.pleaseInput);g[0].focus();return}c.insertHtml(k).hideDialog().focus()}}}),g=a("textarea",d.div);g[0].focus()})});