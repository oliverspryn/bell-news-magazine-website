/*******************************************************
ACROBAT DETECT
All code by Ryan Parman, unless otherwise noted.
(c) 1997-2003, Ryan Parman
http://www.skyzyx.com
Distributed according to SkyGPL 2.1, http://www.skyzyx.com/license/
*******************************************************/

var acrobat=new Object();

// Set some base values
acrobat.installed=false;
acrobat.version='0.0';

if (navigator.plugins && navigator.plugins.length)
{
	for (x=0; x<navigator.plugins.length; x++)
	{
		if (navigator.plugins[x].description.indexOf('Adobe Acrobat') != -1)
		{
			acrobat.version=parseFloat(navigator.plugins[x].description.split('Version ')[1]);

			if (acrobat.version.toString().length == 1) acrobat.version+='.0';

			acrobat.installed=true;
			break;
		}
	}
}
else if (window.ActiveXObject)
{
	for (x=2; x<10; x++)
	{
		try
		{
			oAcro=eval("new ActiveXObject('PDF.PdfCtrl."+x+"');");
			if (oAcro)
			{
				acrobat.installed=true;
				acrobat.version=x+'.0';
			}
		}
		catch(e) {}
	}

	try
	{
		oAcro4=new ActiveXObject('PDF.PdfCtrl.1');
		if (oAcro4)
		{
			acrobat.installed=true;
			acrobat.version='4.0';
		}
	}
	catch(e) {}
}

acrobat.ver4=(acrobat.installed && parseInt(acrobat.version) >= 4) ? true:false;
acrobat.ver5=(acrobat.installed && parseInt(acrobat.version) >= 5) ? true:false;
acrobat.ver6=(acrobat.installed && parseInt(acrobat.version) >= 6) ? true:false;
acrobat.ver7=(acrobat.installed && parseInt(acrobat.version) >= 7) ? true:false;
acrobat.ver8=(acrobat.installed && parseInt(acrobat.version) >= 8) ? true:false;
acrobat.ver9=(acrobat.installed && parseInt(acrobat.version) >= 9) ? true:false; 