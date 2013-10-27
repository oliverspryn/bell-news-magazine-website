/*******************************************************
QUICKTIME DETECT
All code by Ryan Parman, unless otherwise noted.
(c) 1997-2003, Ryan Parman
http://www.skyzyx.com
Distributed according to SkyGPL 2.1, http://www.skyzyx.com/license/
*******************************************************/

var quicktime=new Object();

// Set some base values
quicktime.installed=false;
quicktime.version='0.0';

if (navigator.plugins && navigator.plugins.length)
{
	for (x=0; x<navigator.plugins.length; x++)
	{
		if (navigator.plugins[x].name.indexOf('QuickTime Plug-in') != -1)
		{
			quicktime.installed=true;
			quicktime.version=navigator.plugins[x].name.split('QuickTime Plug-in ')[1].split(' ')[0];
			break;
		}
	}
}
else if (window.ActiveXObject)
{
	try
	{
		oQTime=new ActiveXObject('QuickTimeCheckObject.QuickTimeCheck.1');
		if (oQTime)
		{
			quicktime.installed=oQTime.IsQuickTimeAvailable(0);
			quicktime.version=parseInt(oQTime.QuickTimeVersion.toString(16).substring(0,3))/100;
		}
	}
	catch(e) {}
}

quicktime.ver2=(quicktime.installed && parseInt(quicktime.version) >= 2) ? true:false;
quicktime.ver3=(quicktime.installed && parseInt(quicktime.version) >= 3) ? true:false;
quicktime.ver4=(quicktime.installed && parseInt(quicktime.version) >= 4) ? true:false;
quicktime.ver5=(quicktime.installed && parseInt(quicktime.version) >= 5) ? true:false;
quicktime.ver6=(quicktime.installed && parseInt(quicktime.version) >= 6) ? true:false;
quicktime.ver7=(quicktime.installed && parseInt(quicktime.version) >= 7) ? true:false;
quicktime.ver8=(quicktime.installed && parseInt(quicktime.version) >= 8) ? true:false;
quicktime.ver9=(quicktime.installed && parseInt(quicktime.version) >= 9) ? true:false;
