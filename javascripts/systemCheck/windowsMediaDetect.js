/*******************************************************
WINDOWS MEDIA DETECT
All code by Ryan Parman, unless otherwise noted.
(c) 1997-2003, Ryan Parman
http://www.skyzyx.com
Distributed according to SkyGPL 2.1, http://www.skyzyx.com/license/
*******************************************************/

var windowsmedia=new Object();

// Set some base values
windowsmedia.installed=false;
windowsmedia.version='0.0'; // Internet Explorer-only

// Check for GeckoActiveXObject and co-inciding Plug-In
var gkoaxwma=false;
if (navigator.plugins && navigator.plugins.length) { for (x=0; x<navigator.plugins.length; x++) { if (navigator.plugins[x].name.indexOf('ActiveX') != -1 && window.GeckoActiveXObject) { gkoaxwma=true; break; } } }

// Create an ActiveX/GeckoActiveX constructor
function AXO(id)
{
	var error; var control = null;
	try {
		if (window.ActiveXObject && navigator.userAgent.indexOf('Win') != -1) control = new ActiveXObject(id);
		else if (gkoaxwma) control = new GeckoActiveXObject(id);
	}
	catch (error) {}
	return control;
}

if (window.ActiveXObject || gkoaxwma)
{
	try
	{
		oWMP=new AXO('WMPlayer.OCX.7');
		if (oWMP)
		{
			windowsmedia.installed=true;

			// A wierd bug in the Gecko ActiveX plug-in will return
			// undefined at the first call, but the correct value on the second.
			// This "fix" doesn't seem to hurt IE at all.
			parseFloat(oWMP.versionInfo);

			windowsmedia.version=parseFloat(oWMP.versionInfo);
			if (windowsmedia.version.toString().length == 1) windowsmedia.version+='.0';
		}
	}
	catch(e) {}
}
else if (navigator.plugins && navigator.plugins.length)
{
	for (x=0; x<navigator.plugins.length; x++)
	{
		if (navigator.plugins[x].name.indexOf('Windows Media') != -1)
		{
			windowsmedia.installed=true;
			break;
		}
	}
}

// Internet Explorer or GeckoActiveXObject-compatible browsers only.
windowsmedia.ver7=(windowsmedia.installed && parseInt(windowsmedia.version) >= 7) ? true:false;
windowsmedia.ver8=(windowsmedia.installed && parseInt(windowsmedia.version) >= 8) ? true:false;
windowsmedia.ver9=(windowsmedia.installed && parseInt(windowsmedia.version) >= 9) ? true:false;
