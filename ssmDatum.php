<?php

class ssmDatum {

	var $_ISODatum;
	var $_SsmHumanDatum;
	var $_dagpaaret;
	var $_ISODur;

public static $ssm_dagar = array ("1 januari","2 januari","3 januari","4 januari","5 januari","6 januari","7 januari","8 januari","9 januari","10 januari","11 januari","12 januari","13 januari","14 januari","15 januari","16 januari","17 januari","18 januari","19 januari","20 januari","21 januari","22 januari","23 januari","24 januari","25 januari","26 januari","27 januari","28 januari","29 januari","30 januari","31 januari",
"1 februari","2 februari","3 februari","4 februari","5 februari","6 februari","7 februari","8 februari","9 februari","10 februari","11 februari","12 februari","13 februari","14 februari","15 februari","16 februari","17 februari","18 februari","19 februari","20 februari","21 februari","22 februari","23 februari","24 februari","25 februari","26 februari","27 februari","28 februari","29 februari",
"1 mars","2 mars","3 mars","4 mars","5 mars","6 mars","7 mars","8 mars","9 mars","10 mars","11 mars","12 mars","13 mars","14 mars","15 mars","16 mars","17 mars","18 mars","19 mars","20 mars","21 mars","22 mars","23 mars","24 mars","25 mars","26 mars","27 mars","28 mars","29 mars","30 mars","31 mars",
"1 april","2 april","3 april","4 april","5 april","6 april","7 april","8 april","9 april","10 april","11 april","12 april","13 april","14 april","15 april","16 april","17 april","18 april","19 april","20 april","21 april","22 april","23 april","24 april","25 april","26 april","27 april","28 april","29 april","30 april",
"1 maj","2 maj","3 maj","4 maj","5 maj","6 maj","7 maj","8 maj","9 maj","10 maj","11 maj","12 maj","13 maj","14 maj","15 maj","16 maj","17 maj","18 maj","19 maj","20 maj","21 maj","22 maj","23 maj","24 maj","25 maj","26 maj","27 maj","28 maj","29 maj","30 maj","31 maj",
"1 juni","2 juni","3 juni","4 juni","5 juni","6 juni","7 juni","8 juni","9 juni","10 juni","11 juni","12 juni","13 juni","14 juni","15 juni","16 juni","17 juni","18 juni","19 juni","20 juni","21 juni","22 juni","23 juni","24 juni","25 juni","26 juni","27 juni","28 juni","29 juni","30 juni",
"1 juli","2 juli","3 juli","4 juli","5 juli","6 juli","7 juli","8 juli","9 juli","10 juli","11 juli","12 juli","13 juli","14 juli","15 juli","16 juli","17 juli","18 juli","19 juli","20 juli","21 juli","22 juli","23 juli","24 juli","25 juli","26 juli","27 juli","28 juli","29 juli","30 juli","31 juli",
"1 augusti","2 augusti","3 augusti","4 augusti","5 augusti","6 augusti","7 augusti","8 augusti","9 augusti","10 augusti","11 augusti","12 augusti","13 augusti","14 augusti","15 augusti","16 augusti","17 augusti","18 augusti","19 augusti","20 augusti","21 augusti","22 augusti","23 augusti","24 augusti","25 augusti","26 augusti","27 augusti","28 augusti","29 augusti","30 augusti","31 augusti",
"1 september","2 september","3 september","4 september","5 september","6 september","7 september","8 september","9 september","10 september","11 september","12 september","13 september","14 september","15 september","16 september","17 september","18 september","19 september","20 september","21 september","22 september","23 september","24 september","25 september","26 september","27 september","28 september","29 september","30 september",
"1 oktober","2 oktober","3 oktober","4 oktober","5 oktober","6 oktober","7 oktober","8 oktober","9 oktober","10 oktober","11 oktober","12 oktober","13 oktober","14 oktober","15 oktober","16 oktober","17 oktober","18 oktober","19 oktober","20 oktober","21 oktober","22 oktober","23 oktober","24 oktober","25 oktober","26 oktober","27 oktober","28 oktober","29 oktober","30 oktober","31 oktober",
"1 november","2 november","3 november","4 november","5 november","6 november","7 november","8 november","9 november","10 november","11 november","12 november","13 november","14 november","15 november","16 november","17 november","18 november","19 november","20 november","21 november","22 november","23 november","24 november","25 november","26 november","27 november","28 november","29 november","30 november",
"1 december","2 december","3 december","4 december","5 december","6 december","7 december","8 december","9 december","10 december","11 december","12 december","13 december","14 december","15 december","16 december","17 december","18 december","19 december","20 december","21 december","22 december","23 december","24 december","25 december","26 december","27 december","28 december","29 december","30 december","31 december");

public static $ssm_dagar_SMW = array ("1 januari 1912 00:00:00","2 January 1912 00:00:00","3 January 1912 00:00:00","4 January 1912 00:00:00","5 January 1912 00:00:00","6 January 1912 00:00:00","7 January 1912 00:00:00","8 January 1912 00:00:00","9 January 1912 00:00:00","10 January 1912 00:00:00","11 January 1912 00:00:00","12 January 1912 00:00:00","13 January 1912 00:00:00","14 January 1912 00:00:00","15 January 1912 00:00:00","16 January 1912 00:00:00","17 January 1912 00:00:00","18 January 1912 00:00:00","19 January 1912 00:00:00","20 January 1912 00:00:00","21 January 1912 00:00:00","22 January 1912 00:00:00","23 January 1912 00:00:00","24 January 1912 00:00:00","25 January 1912 00:00:00","26 January 1912 00:00:00","27 January 1912 00:00:00","28 January 1912 00:00:00","29 January 1912 00:00:00","30 January 1912 00:00:00","31 January 1912 00:00:00",
"1 February 1912 00:00:00","2 February 1912 00:00:00","3 February 1912 00:00:00","4 February 1912 00:00:00","5 February 1912 00:00:00","6 February 1912 00:00:00","7 February 1912 00:00:00","8 February 1912 00:00:00","9 February 1912 00:00:00","10 February 1912 00:00:00","11 February 1912 00:00:00","12 February 1912 00:00:00","13 February 1912 00:00:00","14 February 1912 00:00:00","15 February 1912 00:00:00","16 February 1912 00:00:00","17 February 1912 00:00:00","18 February 1912 00:00:00","19 February 1912 00:00:00","20 February 1912 00:00:00","21 February 1912 00:00:00","22 February 1912 00:00:00","23 February 1912 00:00:00","24 February 1912 00:00:00","25 February 1912 00:00:00","26 February 1912 00:00:00","27 February 1912 00:00:00","28 February 1912 00:00:00","29 February 1912 00:00:00", "1 March 1912 00:00:00","2 March 1912 00:00:00","3 March 1912 00:00:00","4 March 1912 00:00:00","5 March 1912 00:00:00","6 March 1912 00:00:00","7 March 1912 00:00:00","8 March 1912 00:00:00","9 March 1912 00:00:00","10 March 1912 00:00:00","11 March 1912 00:00:00","12 March 1912 00:00:00","13 March 1912 00:00:00","14 March 1912 00:00:00","15 March 1912 00:00:00","16 March 1912 00:00:00","17 March 1912 00:00:00","18 March 1912 00:00:00","19 March 1912 00:00:00","20 March 1912 00:00:00","21 March 1912 00:00:00","22 March 1912 00:00:00","23 March 1912 00:00:00","24 March 1912 00:00:00","25 March 1912 00:00:00","26 March 1912 00:00:00","27 March 1912 00:00:00","28 March 1912 00:00:00","29 March 1912 00:00:00","30 March 1912 00:00:00","31 March 1912 00:00:00",
"1 April 1912 00:00:00","2 April 1912 00:00:00","3 April 1912 00:00:00","4 April 1912 00:00:00","5 April 1912 00:00:00","6 April 1912 00:00:00","7 April 1912 00:00:00","8 April 1912 00:00:00","9 April 1912 00:00:00","10 April 1912 00:00:00","11 April 1912 00:00:00","12 April 1912 00:00:00","13 April 1912 00:00:00","14 April 1912 00:00:00","15 April 1912 00:00:00","16 April 1912 00:00:00","17 April 1912 00:00:00","18 April 1912 00:00:00","19 April 1912 00:00:00","20 April 1912 00:00:00","21 April 1912 00:00:00","22 April 1912 00:00:00","23 April 1912 00:00:00","24 April 1912 00:00:00","25 April 1912 00:00:00","26 April 1912 00:00:00","27 April 1912 00:00:00","28 April 1912 00:00:00","29 April 1912 00:00:00","30 April 1912 00:00:00",
"1 May 1912 00:00:00","2 May 1912 00:00:00","3 May 1912 00:00:00","4 May 1912 00:00:00","5 May 1912 00:00:00","6 May 1912 00:00:00","7 May 1912 00:00:00","8 May 1912 00:00:00","9 May 1912 00:00:00","10 May 1912 00:00:00","11 May 1912 00:00:00","12 May 1912 00:00:00","13 May 1912 00:00:00","14 May 1912 00:00:00","15 May 1912 00:00:00","16 May 1912 00:00:00","17 May 1912 00:00:00","18 May 1912 00:00:00","19 May 1912 00:00:00","20 May 1912 00:00:00","21 May 1912 00:00:00","22 May 1912 00:00:00","23 May 1912 00:00:00","24 May 1912 00:00:00","25 May 1912 00:00:00","26 May 1912 00:00:00","27 May 1912 00:00:00","28 May 1912 00:00:00","29 May 1912 00:00:00","30 May 1912 00:00:00","31 May 1912 00:00:00",
"1 June 1912 00:00:00","2 June 1912 00:00:00","3 June 1912 00:00:00","4 June 1912 00:00:00","5 June 1912 00:00:00","6 June 1912 00:00:00","7 June 1912 00:00:00","8 June 1912 00:00:00","9 June 1912 00:00:00","10 June 1912 00:00:00","11 June 1912 00:00:00","12 June 1912 00:00:00","13 June 1912 00:00:00","14 June 1912 00:00:00","15 June 1912 00:00:00","16 June 1912 00:00:00","17 June 1912 00:00:00","18 June 1912 00:00:00","19 June 1912 00:00:00","20 June 1912 00:00:00","21 June 1912 00:00:00","22 June 1912 00:00:00","23 June 1912 00:00:00","24 June 1912 00:00:00","25 June 1912 00:00:00","26 June 1912 00:00:00","27 June 1912 00:00:00","28 June 1912 00:00:00","29 June 1912 00:00:00","30 June 1912 00:00:00",
"1 July 1912 00:00:00","2 July 1912 00:00:00","3 July 1912 00:00:00","4 July 1912 00:00:00","5 July 1912 00:00:00","6 July 1912 00:00:00","7 July 1912 00:00:00","8 July 1912 00:00:00","9 July 1912 00:00:00","10 July 1912 00:00:00","11 July 1912 00:00:00","12 July 1912 00:00:00","13 July 1912 00:00:00","14 July 1912 00:00:00","15 July 1912 00:00:00","16 July 1912 00:00:00","17 July 1912 00:00:00","18 July 1912 00:00:00","19 July 1912 00:00:00","20 July 1912 00:00:00","21 July 1912 00:00:00","22 July 1912 00:00:00","23 July 1912 00:00:00","24 July 1912 00:00:00","25 July 1912 00:00:00","26 July 1912 00:00:00","27 July 1912 00:00:00","28 July 1912 00:00:00","29 July 1912 00:00:00","30 July 1912 00:00:00","31 July 1912 00:00:00",
"1 August 1912 00:00:00","2 August 1912 00:00:00","3 August 1912 00:00:00","4 August 1912 00:00:00","5 August 1912 00:00:00","6 August 1912 00:00:00","7 August 1912 00:00:00","8 August 1912 00:00:00","9 August 1912 00:00:00","10 August 1912 00:00:00","11 August 1912 00:00:00","12 August 1912 00:00:00","13 August 1912 00:00:00","14 August 1912 00:00:00","15 August 1912 00:00:00","16 August 1912 00:00:00","17 August 1912 00:00:00","18 August 1912 00:00:00","19 August 1912 00:00:00","20 August 1912 00:00:00","21 August 1912 00:00:00","22 August 1912 00:00:00","23 August 1912 00:00:00","24 August 1912 00:00:00","25 August 1912 00:00:00","26 August 1912 00:00:00","27 August 1912 00:00:00","28 August 1912 00:00:00","29 August 1912 00:00:00","30 August 1912 00:00:00","31 August 1912 00:00:00",
"1 September 1912 00:00:00","2 September 1912 00:00:00","3 September 1912 00:00:00","4 September 1912 00:00:00","5 September 1912 00:00:00","6 September 1912 00:00:00","7 September 1912 00:00:00","8 September 1912 00:00:00","9 September 1912 00:00:00","10 September 1912 00:00:00","11 September 1912 00:00:00","12 September 1912 00:00:00","13 September 1912 00:00:00","14 September 1912 00:00:00","15 September 1912 00:00:00","16 September 1912 00:00:00","17 September 1912 00:00:00","18 September 1912 00:00:00","19 September 1912 00:00:00","20 September 1912 00:00:00","21 September 1912 00:00:00","22 September 1912 00:00:00","23 September 1912 00:00:00","24 September 1912 00:00:00","25 September 1912 00:00:00","26 September 1912 00:00:00","27 September 1912 00:00:00","28 September 1912 00:00:00","29 September 1912 00:00:00","30 September 1912 00:00:00",
"1 October 1912 00:00:00","2 October 1912 00:00:00","3 October 1912 00:00:00","4 October 1912 00:00:00","5 October 1912 00:00:00","6 October 1912 00:00:00","7 October 1912 00:00:00","8 October 1912 00:00:00","9 October 1912 00:00:00","10 October 1912 00:00:00","11 October 1912 00:00:00","12 October 1912 00:00:00","13 October 1912 00:00:00","14 October 1912 00:00:00","15 October 1912 00:00:00","16 October 1912 00:00:00","17 October 1912 00:00:00","18 October 1912 00:00:00","19 October 1912 00:00:00","20 October 1912 00:00:00","21 October 1912 00:00:00","22 October 1912 00:00:00","23 October 1912 00:00:00","24 October 1912 00:00:00","25 October 1912 00:00:00","26 October 1912 00:00:00","27 October 1912 00:00:00","28 October 1912 00:00:00","29 October 1912 00:00:00","30 October 1912 00:00:00","31 October 1912 00:00:00",
"1 November 1912 00:00:00","2 November 1912 00:00:00","3 November 1912 00:00:00","4 November 1912 00:00:00","5 November 1912 00:00:00","6 November 1912 00:00:00","7 November 1912 00:00:00","8 November 1912 00:00:00","9 November 1912 00:00:00","10 November 1912 00:00:00","11 November 1912 00:00:00","12 November 1912 00:00:00","13 November 1912 00:00:00","14 November 1912 00:00:00","15 November 1912 00:00:00","16 November 1912 00:00:00","17 November 1912 00:00:00","18 November 1912 00:00:00","19 November 1912 00:00:00","20 November 1912 00:00:00","21 November 1912 00:00:00","22 November 1912 00:00:00","23 November 1912 00:00:00","24 November 1912 00:00:00","25 November 1912 00:00:00","26 November 1912 00:00:00","27 November 1912 00:00:00","28 November 1912 00:00:00","29 November 1912 00:00:00","30 November 1912 00:00:00",
"1 December 1912 00:00:00","2 December 1912 00:00:00","3 December 1912 00:00:00","4 December 1912 00:00:00","5 December 1912 00:00:00","6 December 1912 00:00:00","7 December 1912 00:00:00","8 December 1912 00:00:00","9 December 1912 00:00:00","10 December 1912 00:00:00","11 December 1912 00:00:00","12 December 1912 00:00:00","13 December 1912 00:00:00","14 December 1912 00:00:00","15 December 1912 00:00:00","16 December 1912 00:00:00","17 December 1912 00:00:00","18 December 1912 00:00:00","19 December 1912 00:00:00","20 December 1912 00:00:00","21 December 1912 00:00:00","22 December 1912 00:00:00","23 December 1912 00:00:00","24 December 1912 00:00:00","25 December 1912 00:00:00","26 December 1912 00:00:00","27 December 1912 00:00:00","28 December 1912 00:00:00","29 December 1912 00:00:00","30 December 1912 00:00:00","31 December 1912 00:00:00");

	function __construct($d=NULL) {
	}

	function newFromSsmHuman($d) {

		$d = trim($d,"0 \t\n\r\0\x0B");
		$d = str_replace(array('den ','Den '),'',$d);
		$d = str_replace(array('+','_'),' ',$d);

		global $basartal;
		$_dagpaaret = array_search($d, ssmDatum::$ssm_dagar);
		if (!isset($_dagpaaret))
			return false;
	
		
		$_ISODatum = date('Y-m-d',strtotime(date($basartal).'-01-01 +'.($_dagpaaret).' days'));
		return $_ISODatum;
	}

	function newFromSmw($d) {
		global $basartal;
		$matches = array('');
		if (preg_match("/^($basartal)(-([0-1][0-9])(-([0-3][0-9]))?)?/i",$d, &$matches)) {
			$_ISODatum =  $basartal . '-' . $matches[3] . '-' . $matches[5];
			return $_ISODatum;
		} else {
			return null;
		}
	}

	function taltillsiffror($s, $sprak='sv') {
		$bs['sv'] = array(',','noll','ingen','inga','en','ett','två','tre','fyra','fem','sex','sju','åtta','nio','tio','elva','tolv','hundra','½','¼');
		$bt['sv'] = array('.','0','0','0','1','1','2','3','4','5','6','7','8','9','10','11','12','100','0.5','0.25');

		$ut = str_replace($bs[$sprak],$bt[$sprak],$s);

		return trim($ut);
	}

	function newDuration($dur) {
		if (!$dur)
			return 0;
		/*FIXME kolla så att det nite redan är ISOformat*/
		$dur = str_replace('en kvart','15 m',$dur);
		$dur = str_replace('en halvtimme','30 m',$dur);
		$dur = str_replace('en halvtimma','30 m',$dur);
		$dur = self::taltillsiffror($dur);

	 	$m = array();
		$dagar = 0;
		$timmar = 0;
		$minuter = 0;
		$rest = 0;
		if (preg_match("/([0-9|\.]+)\Wmån/iu",$dur, &$m)) //första förekomsten av ”x mån[ader]”
			$dagar = floor($m[1] * 30);
		if (preg_match("/([0-9|\.]+)\Wv/iu",$dur, &$m)) //första förekomsten av ”x v[eckor]”
			$dagar += floor($m[1] * 7);
		if (preg_match("/([0-9|\.]+)\Wd/iu",$dur, &$m)) {//första förekomsten av ”x d[agar]”
			$dagar += floor($m[1]);
			$rest = strstr ( $m[1], '.' );
		}
		if (preg_match("/([0-9|\.]+)(\Wt|\Wh)/i",$dur, &$m)) {//första förekomsten av ”x h|t[immar]”
			$timmar = floor($m[1]);
			$rest = strstr ( $m[1], '.' );
		}
		$timmar += $rest ? $rest * 24 : 0;
		$rest = 0;
		if (preg_match("/([0-9|\.]+)\Wm/iu",$dur, &$m)) { //första förekomsten av ”x m[inuter]”
			$minuter = floor($m[1]);
		}
		$minuter += $rest ? $rest * 60 : 0;

		$_ISODur =  'P' . $dagar . 'DT' . $timmar . 'H' . $minuter .'M';
//var_dump($_ISODur);
		return $_ISODur;
	}


	function getISODatum() {
		return ($this->_ISODatum);
	}

}
?>
