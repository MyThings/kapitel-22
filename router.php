<?php
/**
 * Joomla! 2.5 - Erweiterungen programmieren
 * Frontend Router.
 * @package    Frontend
 * @subpackage MyThings
 * @author     webmechanic.biz, chmst.de
 * @license	   GNU/GPL
 */

/**
 * Wird vom JRoute::_() für jeden Link aufgerufen der "option=com_mythings"
 * enthält und erwartet ein Array mit geordneten URL-Pfadsegmenten die
 * zu einer hübschen URL werden.
 *
 * Die benannten und bekannten Einträge aus $query werden hierzu entnommen.
 * Was in $query übrig bleibt wird zu URL-Parameter, weshalb die "Klassiker"
 * (view, layout, format) hier aus dem Eingangsarray entfernt werden müssen.
 * Reste wie z.B. $query['foo']="bar" werden zu "?foo=bar" usw.
 *
 * Link: option=com_mythings&view=mything&layout=default&id=1
 *
 * 'option' wurde bereits entnommen, denn J! weiß ja, dass es um den
 * Router dieser Komponente geht.
 * $query Elemente:
 * ['view']   => 'mything'
 * ['layout'] => 'default'
 * ['id']     => 1
 *
 * @param  array $query assoz. Array mit URL Parametern
 * @return array Sortiertes Array mit URL-Segmenten
 */
function mythingsBuildRoute(&$query)
{
	$segments = array();

	/* die Pfad-Segmente werden der Reihe nach aufgebaut */
	if ( count($query) ) {
		if (isset($query['view'])) {
			$segments[] = $query['view'];
			unset($query['view']);
		}
		if (isset($query['layout'])) {
			$segments[] = $query['layout'];
			unset($query['layout']);
		}
		if (isset($query['id'])) {
			$segments[] = $query['id'];
			unset($query['id']);
		}
	}

	return $segments;
}

/**
 * URL ohne eine Itemdid: /component/mythings/mything/3
 * Segmente: [0]="mything", [1]="3"
 *
 * URL mit Item hat am Anfang den Alias: /verleih/mything/1-fachbuch
 * Segmente: [0]="mything", [1]="1:fachbuch"
 *
 * @param  array $segments URL in ihre Teile zerlegt
 * @return array  assoz. Array
 */
function mythingsParseRoute(array $segments)
{
	$vars = array();

	/* die Segmente werden der Reihe nach abgearbeitet und was
	 * uns dabei bekannt vor kommt, wird einfach zurückgegeben */
	while ( $segment = array_shift($segments) )
	{
		if ($segment == 'mythings' || $segment == 'mything') {
			$vars['view'] = $segment;
		}
		elseif (is_numeric($segment)) {
			$vars['id'] = $segment;
		}
		elseif (strpos($segment, ':') !== false) {
			// ein "slug" in der Form "n:ssss" nehmen wir  als Alias
			list($id, $title) = explode(':', $segment);
			$vars['id'] = $id;
		}
	}

	/* Listenansicht erzwingen, wenn bei Einzelansicht die ID fehlt */
	if ($vars['view'] == 'mything' && !isset($vars['id'])) {
		$vars['view'] = 'mythings';
	}

	return $vars;
}

