function MM_findObj(n, d) {
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function flvFTFO1(){//v1.11
// Copyright 2003, Marja Ribbers-de Vroed, FlevOOware (www.flevooware.nl/dreamweaver/)
if (!document.layers){var v1=arguments,v2=MM_findObj(v1[0]),v3,v4,v5,v6,v7,v8,v9,v10;if (v2){for (v3=1;v3<v1.length;v3++){v6=v1[v3].split(",");v7=v6[0];v8=v6[1];v10=false;for (v4=0;v4<v2.length;v4++){v5=v2[v4];if (v5.id==v7||v5.name==v7){v10=true;break;}}if (!v10){v5=MM_findObj(v7);v10=(!v5)?false:true;}if (v10){if (v8=="t"){v5.disabled=!v5.disabled;}else {v9=(v8=="e")?false:true;v5.disabled=v9;}}}}}}