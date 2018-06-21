<?php
Class Multipart_generator {
	public function boundary () {
		$uc = uniqid ();
		$one = strtoupper ($uc[3] . $uc[1]. $uc[0]);
		$two = substr ($uc, 1, 8);
		$three = substr ($uc, -8);
		return sprintf (
			"-----Boundary-HJ=_%s%s.%s",
			$one, $two, $three
		);
	}
	
	public function msgid () {
		return '<' . date ('YmdHis') . rand () . '@' . gethostname () . '>';
	}

	public function date () {
		return date ('D, d M Y H:i:s O');
	}

	public function encode ($msg, $split = false) {
		if ( $split )
			return chunk_split (base64_encode ($msg), 76, "\r\n");
		return '=?UTF-8?B?' . base64_encode ($msg) . '?=';
	}
	
	public function addr (&$addr) {
		$addr = trim ($addr);
		if ( preg_match ('/^([^<]+)<([^@]+)@([^>]+)>$/', $addr, $matches) ) {
			$e = (object) array (
				'name' => preg_replace ('/^[\'"]|[\'"]$/', '', trim ($matches[1])),
				'user' => ltrim ($matches[2]),
				'domain' => rtrim ($matches[3])
			);
		} else if ( preg_match ('/^<?([^@<]+)@([^>]+)>?$/', $addr, $matches) ) {
			$e = (object) array (
				'name' => '',
				'user' => ltrim ($matches[1]),
				'domain' => rtrim ($matches[2])
			);
		} else {
			if ( ! strlen ($addr) )
				$this->error ('Given address is NULL', E_USER_ERROR);
			$this->error ('Invalid email address format', E_USER_ERROR);
		}
		if ( $e->name ) {
			$e->name = '"' . $e->name . '"';
			if ( preg_match ('/[^a-z0-9"\'|:;{}\[\]()!#$%&*+_=~., -]/i', $e->name) )
				$e->name = $this->encode ($e->name);
			$e->name .= ' ';
		}
		$this->check_local ($e->user);
		$this->check_domain ($e->domain);
		$addr = sprintf ('%s<%s@%s>', $e->name, trim ($e->user), ($e->domain));
		return true;
	}
	public function mime ($path) {
		if ( function_exists ('finfo_open') ) {
			$fi = finfo_open (FILEINFO_MIME_TYPE);
			$mime = finfo_file ($fi, $path);
			finfo_close ($fi);
			return $mime;
		}
		if ( preg_match ('^/.+\.([^.]+)$/', $path, $matches) ) {
			$path = $matches[1];
		} else
			return 'application/octet-stream';
		if ( $path == 'ez'  ) return 'application/andrew-inset';
		else if ( $path == 'hqx' ) return 'application/mac-binhex40';
		else if ( $path == 'cpt' ) return 'application/mac-compactpro';
		else if ( $path == 'doc' ) return 'application/msword';
		else if ( $path == 'oda' ) return 'application/oda';
		else if ( $path == 'pdf' ) return 'application/pdf';
		else if ( $path == 'rtf' ) return 'application/rtf';
		else if ( $path == 'mif' ) return 'application/vnd.mif';
		else if ( $path == 'ppt' ) return 'application/vnd.ms-powerpoint';
		else if ( $path == 'slc' ) return 'application/vnd.wap.slc';
		else if ( $path == 'sic' ) return 'application/vnd.wap.sic';
		else if ( $path == 'wmlc' ) return 'application/vnd.wap.wmlc';
		else if ( $path == 'wmlsc' ) return 'application/vnd.wap.wmlscriptc';
		else if ( $path == 'bcpio' ) return 'application/x-bcpio';
		else if ( $path == 'bz2' ) return 'application/x-bzip2';
		else if ( $path == 'vcd' ) return 'application/x-cdlink';
		else if ( $path == 'pgn' ) return 'application/x-chess-pgn';
		else if ( $path == 'cpio' ) return 'application/x-cpio';
		else if ( $path == 'csh' ) return 'application/x-csh';
		else if ( $path == 'dvi' ) return 'application/x-dvi';
		else if ( $path == 'spl' ) return 'application/x-futuresplash';
		else if ( $path == 'gtar' ) return 'application/x-gtar';
		else if ( $path == 'hdf' ) return 'application/x-hdf';
		else if ( $path == 'js' ) return 'application/x-javascript';
		else if ( $path == 'ksp' ) return 'application/x-kspread';
		else if ( $path == 'kpr' || $path == 'kpt' ) return 'application/x-kpresenter';
		else if ( $path == 'chrt' ) return 'application/x-kchart';
		else if ( $path == 'kil' ) return 'application/x-killustrator';
		else if ( $path == 'skp' || $path == 'skd' || $path == 'skt' || $path == 'skm' )
			return 'application/x-koan';
		else if ( $path == 'latex' ) return 'application/x-latex';
		else if ( $path == 'nc' || $path == 'cdf' ) return 'application/x-netcdf';
		else if ( $path == 'rpm' ) return 'application/x-rpm';
		else if ( $path == 'sh' ) return 'application/x-sh';
		else if ( $path == 'shar' ) return 'application/x-shar';
		else if ( $path == 'swf' ) return 'application/x-shockwave-flash';
		else if ( $path == 'sit' ) return 'application/x-stuffit';
		else if ( $path == 'sv4cpio' ) return 'application/x-sv4cpio';
		else if ( $path == 'sv4crc' ) return 'application/x-sv4crc';
		else if ( $path == 'tar' ) return 'application/x-tar';
		else if ( $path == 'tcl' ) return 'application/x-tcl';
		else if ( $path == 'tex' ) return 'application/x-tex';
		else if ( $path == 'texinfo' || $path == 'texi' ) return 'application/x-texinfo';
		else if ( $path == 't' || $path == 'tr' || $path == 'roff' )
			return 'application/x-troff';
		else if ( $path == 'man' ) return 'application/x-troff-man';
		else if ( $path == 'me' ) return 'application/x-troff-me';
		else if ( $path == 'ms' ) return 'application/x-troff-ms';
		else if ( $path == 'ustar' ) return 'application/x-ustar';
		else if ( $path == 'src' ) return 'application/x-wais-source';
		else if ( $path == 'zip' ) return 'application/zip';
		else if ( $path == 'gif' ) return 'image/gif';
		else if ( $path == 'ief' ) return 'image/ief';
		else if ( $path == 'wbmp' ) return 'image/vnd.wap.wbmp';
		else if ( $path == 'ras' ) return 'image/x-cmu-raster';
		else if ( $path == 'pnm' ) return 'image/x-portable-anymap';
		else if ( $path == 'pbm' ) return 'image/x-portable-bitmap';
		else if ( $path == 'pgm' ) return 'image/x-portable-graymap';
		else if ( $path == 'ppm' ) return 'image/x-portable-pixmap';
		else if ( $path == 'rgb' ) return 'image/x-rgb';
		else if ( $path == 'xbm' ) return 'image/x-xbitmap';
		else if ( $path == 'xpm' ) return 'image/x-xpixmap';
		else if ( $path == 'xwd' ) return 'image/x-xwindowdump';
		else if ( $path == 'css' ) return 't$path/css';
		else if ( $path == 'rtx' ) return 't$path/richt$path';
		else if ( $path == 'rtf' ) return 't$path/rtf';
		else if ( $path == 'tsv' ) return 't$path/tab-separated-values';
		else if ( $path == 'sl' ) return 't$path/vnd.wap.sl';
		else if ( $path == 'si' ) return 't$path/vnd.wap.si';
		else if ( $path == 'wml' ) return 't$path/vnd.wap.wml';
		else if ( $path == 'wmls' ) return 't$path/vnd.wap.wmlscript';
		else if ( $path == 'etx' ) return 't$path/x-set$path';
		else if ( $path == 'xml' ) return 't$path/xml';
		else if ( $path == 'avi' ) return 'video/x-msvideo';
		else if ( $path == 'movie' ) return 'video/x-sgi-movie';
		else if ( $path == 'wma' ) return 'audio/x-ms-wma';
		else if ( $path == 'wax' ) return 'audio/x-ms-wax';
		else if ( $path == 'wmv' ) return 'video/x-ms-wmv';
		else if ( $path == 'wvx' ) return 'video/x-ms-wvx';
		else if ( $path == 'wm' ) return 'video/x-ms-wm';
		else if ( $path == 'wmx' ) return 'video/x-ms-wmx';
		else if ( $path == 'wmz' ) return 'application/x-ms-wmz';
		else if ( $path == 'wmd' ) return 'application/x-ms-wmd';
		else if ( $path == 'ice' ) return 'x-conference/x-cooltalk';
		else if ( $path == 'ra' ) return 'audio/x-realaudio';
		else if ( $path == 'wav' ) return 'audio/x-wav';
		else if ( $path == 'png' ) return 'image/png';
		else if ( $path == 'asf' || $path == 'asx' ) return 'video/x-ms-asf';
		else if ( $path == 'html' || $path == 'htm' ) return 't$path/html';
		else if ( $path == 'smi' || $path == 'smil' ) return 'application/smil';
		else if ( $path == 'gz' || $path == 'tgz' ) return 'application/x-gzip';
		else if ( $path == 'kwd' || $path == 'kwt' ) return 'application/x-kword';
		else if ( $path == 'kpr' || $path == 'kpt' ) return 'application/x-kpresenter';
		else if ( $path == 'au' || $path == 'snd' ) return 'audio/basic';
		else if ( $path == 'ram' || $path == 'rm' ) return 'audio/x-pn-realaudio';
		else if ( $path == 'pdb' || $path == 'xyz' ) return 'chemical/x-pdb';
		else if ( $path == 'tiff' || $path == 'tif' ) return 'image/tiff';
		else if ( $path == 'igs' || $path == 'iges' ) return 'model/iges';
		else if ( $path == 'wrl' || $path == 'vrml' ) return 'model/vrml';
		else if ( $path == 'asc' || $path == 'txt' || $path == 'php' ) return 't$path/plain';
		else if ( $path == 'sgml' || $path == 'sgm' ) return 't$path/sgml';
		else if ( $path == 'qt' || $path == 'mov' ) return 'video/quicktime';
		else if ( $path == 'ai' || $path == 'eps' || $path == 'ps' ) return 'application/postscript';
		else if ( $path == 'dcr' || $path == 'dir' || $path == 'dxr' ) return 'application/x-director';
		else if ( $path == 'mid' || $path == 'midi' || $path == 'kar' ) return 'audio/midi';
		else if ( $path == 'mpga' || $path == 'mp2' || $path == 'mp3' ) return 'audio/mpeg';
		else if ( $path == 'aif' || $path == 'aiff' || $path == 'aifc' ) return 'audio/x-aiff';
		else if ( $path == 'jpeg' || $path == 'jpg' || $path == 'jpe' ) return 'image/jpeg';
		else if ( $path == 'msh' || $path == 'mesh' || $path == 'silo' ) return 'model/mesh';
		else if ( $path == 'mpeg' || $path == 'mpg' || $path == 'mpe' ) return 'video/mpeg';
		else return 'application/octet-stream';
	}
	public function header ($message_id, $from_val, $to_val, $bound, $cc = NULL, $bcc = NULL ) {
		if ( !empty($cc) ){
			if ( !empty($bcc) ) { //cc, bcc
				$buf = 'MIME-Version: 1.0' . "\r\n" .
				"Message-ID: $message_id" . "\r\n" .
				"Cc: $cc" . "\r\n" .
				"Bcc: $bcc" . "\r\n" .
				"Content-Type: multipart/mixed;" .  "boundary=" . '"' . $bound . '"' . "\r\n\r\n";
			} else { // cc
				$buf = 'MIME-Version: 1.0' . "\r\n" .
                                "Message-ID: $message_id" . "\r\n" .
				"Cc: $cc" . "\r\n" .
                                "Content-Type: multipart/mixed;" .  "boundary=" . '"' . $bound . '"' . "\r\n\r\n";
			}
				
		} else {
			if( !empty ($bcc) ){
	        	$buf = 'MIME-Version: 1.0' . "\r\n" .
                	"Message-ID: $message_id" . "\r\n" .
                	"Bcc: $bcc" . "\r\n" .
                	"Content-Type: multipart/mixed;" .  "boundary=" . '"' . $bound . '"' . "\r\n\r\n";
			}
			else {
                        $buf = 'MIME-Version: 1.0' . "\r\n" .
                        "Message-ID: $message_id" . "\r\n" .
                        "Content-Type: multipart/mixed;" .  "boundary=" . '"' . $bound . '"' . "\r\n\r\n";
			}
		}
		return $buf;	
	} 
}
?>
