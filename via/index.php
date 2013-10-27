<?php 
//Detect when the user has submitted the form
	if(!empty($_GET) && is_array($_GET) && isset($_GET['URL']) && !empty($_GET['URL'])) {
	//Configuration
		$rootURL = "http://viaforward.co.cc/index.php";
		
	//Common functions
		function imgURL($matches) {
			global $rootURL;
			return $matches[1] . $rootURL . "?type=image&URL=" . base64_encode($matches['2']);
		}
		
		function CSSURL($matches) {
			global $rootURL;
			return $matches[1] . $rootURL . "?type=CSS&URL=" . base64_encode($matches['2']);
		}
		
		function JSURL($matches) {
			global $rootURL;
			return $matches[1] . $rootURL . "?type=JS&URL=" . base64_encode($matches['2']);
		}
		
	//Return the MIME type of a file
		function getMimeType($filename) {
			if (function_exists("mime_content_type")) {
				return mime_content_type($filename);
			} elseif (function_exists("finfo_open")) {
				$fileInfo = finfo_open(FILEINFO_MIME);
	            $mimetype = finfo_file($fileInfo, $filename);
	            finfo_close($fileInfo);
	            return $mimetype;
			} else {
				$mimeTypes = array(
					"ai" => "application/postscript",
					"aif" => "audio/x-aiff",
					"aifc" => "audio/x-aiff",
					"aiff" => "audio/x-aiff",
					"asc" => "text/plain",
					"asf" => "video/x-ms-asf",
					"asx" => "video/x-ms-asf",
					"au" => "audio/basic",
					"avi" => "video/x-msvideo",
					"bcpio" => "application/x-bcpio",
					"bin" => "application/octet-stream",
					"bmp" => "image/bmp",
					"bz2" => "application/x-bzip2",
					"cdf" => "application/x-netcdf",
					"chrt" => "application/x-kchart",
					"class" => "application/octet-stream",
					"cpio" => "application/x-cpio",
					"cpt" => "application/mac-compactpro",
					"csh" => "application/x-csh",
					"css" => "text/css",
					"dcr" => "application/x-director",
					"dir" => "application/x-director",
					"djv" => "image/vnd.djvu",
					"djvu" => "image/vnd.djvu",
					"dll" => "application/octet-stream",
					"dms" => "application/octet-stream",
					"dvi" => "application/x-dvi",
					"dxr" => "application/x-director",
					"eps" => "application/postscript",
					"etx" => "text/x-setext",
					"exe" => "application/octet-stream",
					"ez" => "application/andrew-inset",
					"flv" => "video/x-flv",
					"gif" => "image/gif",
					"gtar" => "application/x-gtar",
					"gz" => "application/x-gzip",
					"hdf" => "application/x-hdf",
					"hqx" => "application/mac-binhex40",
					"htm" => "text/html",
					"html" => "text/html",
					"ice" => "x-conference/x-cooltalk",
					"ief" => "image/ief",
					"iges" => "model/iges",
					"igs" => "model/iges",
					"img" => "application/octet-stream",
					"iso" => "application/octet-stream",
					"jad" => "text/vnd.sun.j2me.app-descriptor",
					"jar" => "application/x-java-archive",
					"jnlp" => "application/x-java-jnlp-file",
					"jpe" => "image/jpeg",
					"jpeg" => "image/jpeg",
					"jpg" => "image/jpeg",
					"js" => "application/x-javascript",
					"kar" => "audio/midi",
					"kil" => "application/x-killustrator",
					"kpr" => "application/x-kpresenter",
					"kpt" => "application/x-kpresenter",
					"ksp" => "application/x-kspread",
					"kwd" => "application/x-kword",
					"kwt" => "application/x-kword",
					"latex" => "application/x-latex",
					"lha" => "application/octet-stream",
					"lzh" => "application/octet-stream",
					"m3u" => "audio/x-mpegurl",
					"man" => "application/x-troff-man",
					"me" => "application/x-troff-me",
					"mesh" => "model/mesh",
					"mid" => "audio/midi",
					"midi" => "audio/midi",
					"mif" => "application/vnd.mif",
					"mov" => "video/quicktime",
					"movie" => "video/x-sgi-movie",
					"mp2" => "audio/mpeg",
					"mp3" => "audio/mpeg",
					"mp4" => "video/mp4",
					"mpe" => "video/mpeg",
					"mpeg" => "video/mpeg",
					"mpg" => "video/mpeg",
					"mpga" => "audio/mpeg",
					"ms" => "application/x-troff-ms",
					"msh" => "model/mesh",
					"mxu" => "video/vnd.mpegurl",
					"nc" => "application/x-netcdf",
					"odb" => "application/vnd.oasis.opendocument.database",
					"odc" => "application/vnd.oasis.opendocument.chart",
					"odf" => "application/vnd.oasis.opendocument.formula",
					"odg" => "application/vnd.oasis.opendocument.graphics",
					"odi" => "application/vnd.oasis.opendocument.image",
					"odm" => "application/vnd.oasis.opendocument.text-master",
					"odp" => "application/vnd.oasis.opendocument.presentation",
					"ods" => "application/vnd.oasis.opendocument.spreadsheet",
					"odt" => "application/vnd.oasis.opendocument.text",
					"ogg" => "application/ogg",
					"otg" => "application/vnd.oasis.opendocument.graphics-template",
					"oth" => "application/vnd.oasis.opendocument.text-web",
					"otp" => "application/vnd.oasis.opendocument.presentation-template",
					"ots" => "application/vnd.oasis.opendocument.spreadsheet-template",
					"ott" => "application/vnd.oasis.opendocument.text-template",
					"pbm" => "image/x-portable-bitmap",
					"pdb" => "chemical/x-pdb",
					"pdf" => "application/pdf",
					"pgm" => "image/x-portable-graymap",
					"pgn" => "application/x-chess-pgn",
					"png" => "image/png",
					"pnm" => "image/x-portable-anymap",
					"ppm" => "image/x-portable-pixmap",
					"ps" => "application/postscript",
					"qt" => "video/quicktime",
					"ra" => "audio/x-realaudio",
					"ram" => "audio/x-pn-realaudio",
					"ras" => "image/x-cmu-raster",
					"rgb" => "image/x-rgb",
					"rm" => "audio/x-pn-realaudio",
					"roff" => "application/x-troff",
					"rpm" => "application/x-rpm",
					"rtf" => "text/rtf",
					"rtx" => "text/richtext",
					"sgm" => "text/sgml",
					"sgml" => "text/sgml",
					"sh" => "application/x-sh",
					"shar" => "application/x-shar",
					"silo" => "model/mesh",
					"sis" => "application/vnd.symbian.install",
					"sit" => "application/x-stuffit",
					"skd" => "application/x-koan",
					"skm" => "application/x-koan",
					"skp" => "application/x-koan",
					"skt" => "application/x-koan",
					"smi" => "application/smil",
					"smil" => "application/smil",
					"snd" => "audio/basic",
					"so" => "application/octet-stream",
					"spl" => "application/x-futuresplash",
					"src" => "application/x-wais-source",
					"stc" => "application/vnd.sun.xml.calc.template",
					"std" => "application/vnd.sun.xml.draw.template",
					"sti" => "application/vnd.sun.xml.impress.template",
					"stw" => "application/vnd.sun.xml.writer.template",
					"sv4cpio" => "application/x-sv4cpio",
					"sv4crc" => "application/x-sv4crc",
					"swf" => "application/x-shockwave-flash",
					"sxc" => "application/vnd.sun.xml.calc",
					"sxd" => "application/vnd.sun.xml.draw",
					"sxg" => "application/vnd.sun.xml.writer.global",
					"sxi" => "application/vnd.sun.xml.impress",
					"sxm" => "application/vnd.sun.xml.math",
					"sxw" => "application/vnd.sun.xml.writer",
					"t" => "application/x-troff",
					"tar" => "application/x-tar",
					"tcl" => "application/x-tcl",
					"tex" => "application/x-tex",
					"texi" => "application/x-texinfo",
					"texinfo" => "application/x-texinfo",
					"tgz" => "application/x-gzip",
					"tif" => "image/tiff",
					"tiff" => "image/tiff",
					"torrent" => "application/x-bittorrent",
					"tr" => "application/x-troff",
					"tsv" => "text/tab-separated-values",
					"txt" => "text/plain",
					"ustar" => "application/x-ustar",
					"vcd" => "application/x-cdlink",
					"vrml" => "model/vrml",
					"wav" => "audio/x-wav",
					"wax" => "audio/x-ms-wax",
					"wbmp" => "image/vnd.wap.wbmp",
					"wbxml" => "application/vnd.wap.wbxml",
					"wm" => "video/x-ms-wm",
					"wma" => "audio/x-ms-wma",
					"wml" => "text/vnd.wap.wml",
					"wmlc" => "application/vnd.wap.wmlc",
					"wmls" => "text/vnd.wap.wmlscript",
					"wmlsc" => "application/vnd.wap.wmlscriptc",
					"wmv" => "video/x-ms-wmv",
					"wmx" => "video/x-ms-wmx",
					"wrl" => "model/vrml",
					"wvx" => "video/x-ms-wvx",
					"xbm" => "image/x-xbitmap",
					"xht" => "application/xhtml+xml",
					"xhtml" => "application/xhtml+xml",
					"xml" => "text/xml",
					"xpm" => "image/x-xpixmap",
					"xsl" => "text/xml",
					"xwd" => "image/x-xwindowdump",
					"xyz" => "chemical/x-xyz",
					"zip" => "application/zip",
					"doc" => "application/msword",
					"dot" => "application/msword",
					"docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
					"dotx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.template",
					"docm" => "application/vnd.ms-word.document.macroEnabled.12",
					"dotm" => "application/vnd.ms-word.template.macroEnabled.12",
					"xls" => "application/vnd.ms-excel",
					"xlt" => "application/vnd.ms-excel",
					"xla" => "application/vnd.ms-excel",
					"xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
					"xltx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.template",
					"xlsm" => "application/vnd.ms-excel.sheet.macroEnabled.12",
					"xltm" => "application/vnd.ms-excel.template.macroEnabled.12",
					"xlam" => "application/vnd.ms-excel.addin.macroEnabled.12",
					"xlsb" => "application/vnd.ms-excel.sheet.binary.macroEnabled.12",
					"ppt" => "application/vnd.ms-powerpoint",
					"pot" => "application/vnd.ms-powerpoint",
					"pps" => "application/vnd.ms-powerpoint",
					"ppa" => "application/vnd.ms-powerpoint",
					"pptx" => "application/vnd.openxmlformats-officedocument.presentationml.presentation",
					"potx" => "application/vnd.openxmlformats-officedocument.presentationml.template",
					"ppsx" => "application/vnd.openxmlformats-officedocument.presentationml.slideshow",
					"ppam" => "application/vnd.ms-powerpoint.addin.macroEnabled.12",
					"pptm" => "application/vnd.ms-powerpoint.presentation.macroEnabled.12",
					"potm" => "application/vnd.ms-powerpoint.presentation.macroEnabled.12",
					"ppsm" => "application/vnd.ms-powerpoint.slideshow.macroEnabled.12"
				);
				
				$extension = strtolower(end(explode(".", $filename)));
				
				if (array_key_exists($extension, $mimeTypes)) {
					return $mimeTypes[$extension];
				} else {
					return "application/octet-stream";
				}
			}
		}
		
		function open($file) {
			$mimeType = getMimeType($file);
			
			header('Content-Description: File Transfer');
			header("Content-type: " . $mimeType);
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			ob_clean();
			flush();
			readfile($file);
			exit;
		}
	
	//Return images to the user
		if (isset($_GET['type']) && $_GET['type'] == "image") {
			open(base64_decode($_GET['URL']));
		}
		
	//Return CSS to the user
		if (isset($_GET['type']) && $_GET['type'] == "CSS") {
			open(base64_decode($_GET['URL']));
		}
		
	//Return JavaScript to the user
		if (isset($_GET['type']) && $_GET['type'] == "JS") {
			open(base64_decode($_GET['URL']));
		}
	
	//Fetch the contents of the requested page
		$contents = file_get_contents(base64_decode($_GET['URL']));
		
	//Should the images be stripped?
		if ($_GET['imagesStrip'] == "on") {
			$contents = preg_replace("/<img[^>]+\>/i", "", $contents); 
	//If not, route them through this server
		} else {
			$contents = preg_replace_callback("/(<img[^>]*src *= *[\"']?)([^\"']*)/i", imgURL, $contents);
		}

	//Should the CSS be stripped?
		if ($_GET['CSSStrip'] == "on") {
			$contents = preg_replace("/<link[^>]+\>/i", "", $contents); 
	//If not, route them through this server
		} else {
			$contents = preg_replace_callback("/(<link[^>]*href*= *[\"']?)([^\"']*)/i", CSSURL, $contents);
		}
		
	//Should the JavaScript be stripped?
		if ($_GET['CSSStrip'] == "on") {
			$contents = preg_replace("/<script\b[^>]*>(.*?)<\/script>/i", "", $contents); 
	//If not, route them through this server
		} else {
			$contents = preg_replace_callback("/(<script[^>]*src *= *[\"']?)([^\"']*)/i", JSURL, $contents);
		}
		
	//Add a back link
		echo "<div style=\"position:fixed; top:0; left:0; width 200px; display:block; background-color: #CCC; text-align:center;\"><a href=\"index.php\">&lt;&lt; Back</a></div>";
		
	//Output the generated contents to the user
		echo $contents;
		exit;
	}
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
<title>Extract the Contents of a Proxied Page</title>
<script src="cipher.js"></script>
</head>

<body>
<section>
<header>
<h1>Extract the Contents of a Proxied Page</h1>
</header>

<p>Enter the URL of the page you wish to view.</p>
<form action="index.php" name="URLForm" method="GET">
<p>
URL: <input type="text" name="URLEntry" autocomplete="off" id="URLEntryField" /><br/>
<label><input type="checkbox" name="imagesStrip" checked="checked" /> Strip images</label><br />
<label><input type="checkbox" name="CSSStrip" checked="checked" /> Strip CSS</label><br />
<label><input type="checkbox" name="JSStrip" checked="checked" /> Strip JavaScript</label><br />
<input type="hidden" name="URL" id="URLDesitination" />
<br />
<input type="button" onclick="updateURL()" value="Submit" />
</p>
</form>
</section>
</body>
</html>