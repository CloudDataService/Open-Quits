<?php



function clean_html($html = '')
{
	require_once(APPPATH . '/third_party/htmlpurifier/HTMLPurifier.auto.php');

	static $config, $purifier;

	if ( ! $config || ! $purifier)
	{
		$config = HTMLPurifier_Config::createDefault();
		$config->set('HTML.Allowed', 'p,b,a[href],i,u,strong,em,h1,h2,h3,h4,h5,h6,div,del,address,ul,ol,li,img[src|alt|title|style],br,hr,table[border|cellpadding|cellspacing|width|style],tr,td,th,tbody,thead,tfoot');
		$config->set('AutoFormat.AutoParagraph', TRUE);
		$config->set('AutoFormat.RemoveEmpty', TRUE);
		$config->set('HTML.SafeEmbed', TRUE);
		$config->set('HTML.SafeObject', TRUE);
		$config->set('HTML.SafeIframe', TRUE);

		$purifier = new HTMLPurifier($config);
	}

	return $purifier->purify($html);

	/*$clean = strip_tags($html, '<p><a><div><iframe><embed><object><span><b><i><u><del><address><quote><strong><em>');
	$clean = preg_replace("/\n+/", " ", $clean);
	$clean = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $clean);

	return mb_convert_encoding($clean, 'HTML-ENTITIES', 'UTF-8');*/
}

