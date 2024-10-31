<?php

/*
Plugin Name: Säsongsmat
Plugin URI: http://xn--ssongsmat-v2a.nu
Description: Lägger till semantiska taggar och information från Säsongsmats databas i dina recept
Version: 0.13
Author: Säsongsmat.nu / Leo Wallentin
Author URI: http://xn--ssongsmat-v2a.nu
License: GPL3
*/

/*  Copyright 2011 Säsongsmat.nu  (email : mail@sasongsmat.nu)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 3, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
att göra:
 -singleton för api-anrop
 -spara råvaror både i egen tabell och vid posterna
 -widget för recept på den här sajten
 -flytta ut all js o css 
 -design
 -
*/

/* INCLUDES */
require_once ( 'ssmDatum.php');
require_once ( 'widget.php' );

/* KONSTANTER */
/* Tidstyp, som komplement till PHP:s DATE_ISO8601 i diverse funktionsanrop*/
define ('DURATION_ISO8601',521);

/* Semantiska definitioner */
define ('ITEMTYPE_RECIPE','http://data-vocabulary.org/Recipe'); //googles
define ('ITEMTYPE_NUTRITION','http://data-vocabulary.org/Nutrition'); //googles
define ('ITEMTYPE_INGREDIENT','http://data-vocabulary.org/RecipeIngredient'); //googles
define ('ITEMTYPE_DEFAULT','http://data-vocabulary.org/'); //googles

/* Konstanter för inställningar */
define ('ALDRIG',0);
define ('ALLTID',1);
define ('DEFAULT_ON',3);
define ('DEFAULT_OFF',2);

/* Ladda vår plugin */
new sasongsmat();

/* SHORTCODES */
/* */
class ssmMicrodataShortcode {
	var $_property;		//* Motsvarar vilken microdata itemprop?
	var $_namn;		//Namn i tooltips, hjälptexter, etc 
	var $_function;		//Funktion för att omvandla shortcodes till microdatataggar
	var $_itemscope = false;//
	var $_itemtype;		//Se konstanter ovan
	var $_datetime = false; //False eller typ av datum: DATE_ISO8601 eller DURATION_ISO8601
	var $_knapp;		//Ska det finnas en knapp i editorn?
	var $_knappgenv;	//Snabbtangent för knapp i editorn
	var $_parent;		//Shortcode som den här shortcoden alltid måste omges av
	var $_desc;		//Beskrivning
	var $_style;		//Stilmallar till editorn. Ej implementerat
	var $_children;		//Kan den här shortcoden kapsla in andra shortcodes? Ge akt på oändliga loopar tillsammans med parent när du tillåter detta!

	function ssmMicrodataShortcode ( $options = array() ) {

		if (!is_array($options))
			return false;

		if ( !isset($options['property']) )
			return false;
		$this->_property = $options['property'];

		if ( isset($options['itemscope']) ) {

			$this->_itemscope = true;
			$this->_itemtype = isset($options['itemtype']) ? $options['itemtype'] : ITEMTYPE_DEFAULT;

		}

		if (isset($options['datetime'])) {

			if ( $options['datetime'] == DURATION_ISO8601 )
				$this->_datetime = DURATION_ISO8601;
			else
				$this->_datetime = DATE_ISO8601;
		}

		$this->_namn = isset($options['namn']) ? $options['namn'] : ucfirst($this->_property);
		$this->_knapp = isset($options['knapp']) ? $options['knapp'] : DEFAULT_OFF;
		$this->_knappgenv = isset($options['knappgenv']) ? $options['knappgenv'] : '';
		$this->_parent = isset($options['parent']) ? $options['parent'] : null;
		$this->_desc = isset($options['desc']) ? $options['desc'] : '';
		$this->_style = isset($options['style']) ? $options['style'] : array( 'border' => '1px dotted yellow' );
		$this->_children = isset($options['children']) ? $options['children'] : false;

		/*  Skapa standardfunktion om ingen annan angetts. Funktionen skriver ut microdata-tagggar.*/
		/*Itemprop*/
		$s = 'itemprop="' . $this->_property . '"';

		/*Itemscope och Itemtype*/
		if ($this->_itemscope) {
			$s .= ' itemscope itemtype="' . $this->_itemtype .'"';
		}

		/*Tagg som kan innehålla andra taggar?*/
		$nested = $this->_children;

		/*Anonym funktion med create_function, för att klara äldre PHP-versioner*/
		$defaultf = create_function('$atts,$content=null', "
						   if ( '$nested' == 1 ) {
							".'$content'." = do_shortcode(".'$content'.");
						   }
						   extract( shortcode_atts( array(
						      'datetime' => '',
						      'itemtype' => '',
						      ), ".'$atts'." ) );
						".'$p'."='';
						if (".'$datetime'.") {
							".'$p'." .= ' datetime=\"' . esc_attr(".'$datetime'.") . '\"';
						}
						if (".'$itemtype'.") {
							".'$p'." .= ' itemtype=\"' . esc_attr(".'$itemtype'.") . '\"';
						}
						return '<span $s' . ".'$p'." . '>' . ".'$content'." . '</span>';");

		$this->_function = isset($options['function']) ? $options['function'] : $defaultf;


	}
}

/* KNAPPAR 
 Knappar till TinyMce-editorn */
class ssmKnapp {

	var $_label;
	var $_shortcode;
	var $_shortcut;
	var $_imagename;
	var $_function;
	var $_bgcolor;//stilelement till texten i wysiwyg-editorn, om vi byter tillbaka till span class=-lösningen 
	var $_border; //do

	var $_defaultimage = 'code.png'; //standardbild för knappar

	function ssmKnapp ($options) {
	/*
		options är en array med
			*label:     knappens label / text i html-läget
			shortcode: vilken shortcode knappen ska infoga
			shortcut:  snabbtangenter till knappen
			imagename: knappbild i wysiwyg
			function:  funktion som ska anropas när knappen klickas, istället för default
			style:     stilmallar som ska användas i wysiwyg-redigering om vi byter tillbaka till span class=-lösningen 
	*/
		if (!is_array($options))
			return false;
		if (!isset($options['label']))	//Label är obligatoriskt
			return false;

		$this->_label = $options['label'];
		$this->_shortcode = isset($options['shortcode']) ? $options['shortcode'] : $this->_label;
		$this->_shortcut = isset($options['shortcut']) ? $options['shortcut'] : $this->_label[0];
		$this->_imagename = isset($options['image']) ? $options['image'] : $this->_shortcode . '.png';

		if ( !file_exists(dirname(__FILE__)."/images/".$this->_imagename) )
			$this->_imagename = $this->_defaultimage;
		$this->_function = isset($options['function']) ? $options['function'] : '';
		$style = isset($options['style']) ? $options['style'] : array();
		$this->_bgcolor = isset($style['background-color']) ? $style['background-color'] : '';
		$this->_border = isset($style['border']) ? $style['border'] : '';
		
	}

}

/***************************************************************************************************************/

class sasongsmat {

	/*Sökväg till den här pluginens url*/
	var $plugin_url;

	/*Knappar*/
	var $ssmKnappar;

	/*Shortcodes*/
	var $ssmShortcodes;

	/*Håller reda på vad som gjorts vid utskrift, när flera hooks kan sätta samma tagg*/
	var $_alreadySet = array();

	/* Variabler som vi kan tvingas skriva genom att parsa sluttexten */
	var $_pubDatum;
	var $_author;
	var $_title;

	/* Lista över råvaror att skicka vidare till onload-javascript */
	var $_rvlista;

	var $bildkat = 'images';  //Katalog för knappbilder, relativ sökväg

	/* Options */
	var $ssmopt;

	/* CONSTRUCTOR */
	function sasongsmat() {

		$this->plugin_url = trailingslashit(plugins_url(basename(dirname(__FILE__))));

		//hämta inställningar
		$this->ssmopt = get_option('ssm');

		/* I18n */
		if ( !load_plugin_textdomain ( 'sasongsmat', null, '/wp-content/languages/' ) )
			load_plugin_textdomain ( 'sasongsmat', null, $this->plugin_url . 'languages/' );

		/* Bygg shortcodes. Härifrån styrs allt! */
		/* Detta är pseudotaggar som ska omvandlas till semantiska taggar */
		/* Här finns också defaultinställningar för de olika propsen */
		/* Property: Microdataprop */
		/* Datetime: Typ av datetime som ska skapas utifrån innehållet */
		/* Parent: Krävs en omgivande tag? Vilken? */
		/* Children: Är det tillåtet med undertaggar? */
		/* Knapp: Ska en knapp skrivas ut i editorn? DEFAULT, ALLTID, ALDRIG */
		/* Knappgenv: Defaultvärde för snabbtangent t knapp */
		/* Namn: Namn som används på knappar, i hjälptexter, etc. */
		/* Desc: Beskrivning till hjälptexter och liknande */
		/* Style: formatering av markerad text i Wysiwyg-editorn */
		/* Nycklarna: id som används internt här och i databasen. Får bara innehålla engelska bokstäver  */

		//shortcode-function till bildtaggen nedan
		$bildfunktion = create_function('$atts,$content=null', '
								$content = preg_replace("/\<img /","<img itemprop=\'photo\' ",$content);
								return $content;');
		$rvfunktion = create_function('$atts,$content=null', '
								$content = "<span itemprop=\'name\' class=\'ssm-ravara\'>$content</span>";
								return $content;');

		$this->ssmShortcodes = array ( 
			'receptnamn' =>       new ssmMicrodataShortcode ( array ( 'property' => 'name',
										  'namn'     => __('Receptnamn'),
										  'desc'     => __('Normalt används inläggets titel, men du kan ange ett annat namn på receptet med den här taggen.'),
									)),
			'recepttyp' =>        new ssmMicrodataShortcode ( array ( 'property' => 'recipeType',
										  'namn'     => __('Recepttyp'),
										  'desc'     => __('Normalt används inläggets kategorier för att ange recepttyp. Under de avancerade inställningarna kan du ange vilka kategorier som är giltiga recepttyper. Du kan också använda den här taggen för att ange typ manuellt.'),
									)),
			'receptbild' =>       new ssmMicrodataShortcode ( array ( 'property' => 'photo',
										  'namn'     => __('Bild'),
										  'desc'     => __('Den här taggen ska sättas runt hela bildkoden för den bild som ska anges som receptfoto. Används den inte så blir den första bilden i inlägget illustration.'),
										  'function' => $bildfunktion,
									)),
			'ingress' =>	      new ssmMicrodataShortcode ( array ( 'property' => 'summary',
										  'knapp'    => DEFAULT_OFF,
										  'children' => true,
									)),
			'forberedelsetid' =>  new ssmMicrodataShortcode ( array ( 'property' => 'prepTime',
									          'datetime' => DURATION_ISO8601,
									)),
			'tillagningstid' =>   new ssmMicrodataShortcode ( array ( 'property' => 'cookTime',
									          'datetime' => DURATION_ISO8601,
									)),
			'tidsatgang' =>       new ssmMicrodataShortcode ( array ( 'property' => 'totalTime',
									          'datetime' => DURATION_ISO8601,
										  'knapp'    => DEFAULT_ON,
										  'namn'     => __('Tidsåtgång'),
										  'desc'     => __('Här avses total tidsåtgång, det som oftast anges i svenska recept. Pluginen försöker översätta innehållet i den här taggen till en tidskod som datorer kan läsa. Det går att sätta maskinkoden (ISO8601) manuellt, genom att lägga till ”datetime=xxx” i taggen'),
										  'style'    => array (
										      'background-color' => '#88f'
										   ),
									)),
			'kalorier' =>         new ssmMicrodataShortcode ( array ( 'property' => 'calories',
									          'parent'   => 'näringsvärde',
										  'namn'     => __('Energivärde, kalorier'),
										  'desc'     => __('Den här taggen ska i sin tur ingå i ett större sjok markerat med ”näringsvärde”-taggen. Den läggs till automatiskt.'),
										  'parent'   => 'naringsvarde',
									)),
			'naringsvarde' =>     new ssmMicrodataShortcode ( array ( 'property' => 'nutrition',
										  'itemscope'=> false,
										  'itemtype' => ITEMTYPE_NUTRITION,
										  'namn'     => __('Näringsvärde'),
										  'desc'     => __('Ett avsnitt med information om näringsvärde.'),
										  'children' => true,
									)),
			'tillagning' =>       new ssmMicrodataShortcode ( array ( 'property' => 'instructions',
										  'desc'     => __('Själva tillagningsinstruktionerna. Om inte den här taggen används så markeras hela brödtexten utom ingredienser och ingress som instruktion.'),
										  'children' => true,
									)),
			'receptstorlek' =>    new ssmMicrodataShortcode ( array ( 'property' => 'yield',
										  'knapp'    => DEFAULT_ON,
										  'namn'     => __('Receptstorlek'),
										  'desc'     => __('T.ex. ”4 portioner” eller ”1 sats”.'),
										  'style'    => array (
										      'background-color' => '#f88'
										   ),
									)),
			'receptforfattare' => new ssmMicrodataShortcode ( array ( 'property' => 'author',
										  'namn'     => __('Receptförfattare'),
										  'desc'     => __('Anges ingen författare så används signaturen under blogginlägget.'),
									)),
			'ingrediens' =>       new ssmMicrodataShortcode ( array ( 'property' => 'ingredient',
										  'itemscope'=> false,
										  'itemtype' => ITEMTYPE_INGREDIENT,
										  'namn'     => __('Ingrediens'),
										  'desc'     => __('Ingredienstaggen kapslar in en råvara och eventuell mängd. Pluginen försöker göra det automatiskt, men du kan också lägga till taggen själv, så här: ”[ingrediens][mangd]2 msk[/mangd] mald [råvara]muskotnöt[/råvara][/ingrediens].'),
										  'style'    => array (
										      'border' => '1px dotted grey'
										   ),
										  'children' => true,
									)),
			'ravara' =>           new ssmMicrodataShortcode ( array ( 'property' => 'name',
										  'knapp'    => DEFAULT_ON,
										  'namn'     => __('Råvara'),
										  'desc'     => __('Anger själva namnet på råvaran. I en rad som ”2 deciliter strimlade morötter” är det alltså bara ”morötter” som ska markeras.'),
										  'knappgenv'=> 'r',
										  'style'    => array (
										      'background-color' => '#ff8'
										   ),
										  'parent'   => 'ingrediens',
										  'function' => $rvfunktion,
									)),
			'mangd' =>            new ssmMicrodataShortcode ( array ( 'property' => 'amount',
										  'knapp'    => DEFAULT_ON,
										  'namn'     => __('Mängd'),
										  'desc'     => __('T.ex. ”2 msk”'),
										  'knappgenv'=> 'm',
										  'style'    => array (
										      'background-color' => '#8f8'
										   ),
									)),
					   );


		/* Admin-gränssnitt för pluginen */
		if (is_admin()) {

			//aktivering
			register_activation_hook(__FILE__, array($this, 'activate_plugin'));
			
			//avaktivering
			register_deactivation_hook(__FILE__, array($this, 'deactivate_plugin'));
			
			//inställningslänk
			add_filter('plugin_action_links_'.plugin_basename(__FILE__), array($this, 'plugin_actions'));
			
			//inställningslänk, pluginmeny
			add_action('admin_menu',  array($this, 'plugin_menu'));
			
			//Vitlista options
			add_action( 'admin_init', array( $this, 'ssm_options_init' ) );

			/*För colorpicker i settings*/
			add_action( 'admin_init', array( $this, 'ilc_farbtastic_script' ) );

			/*Script för d.o.*/
//			add_action('admin_footer',  array($this, 'admin_js'));
//			add_action('admin_head',  array($this, 'admin_js'));

			/* Bygg knappar */
			$this->ssmKnappar = array ();

			foreach ($this->ssmShortcodes as $namn => $sc) {

				if ($sc->_knapp !== ALDRIG) { //är knappar tillåtna för denna shortcode?

					if ( isset($this->ssmopt[$namn][0]) && $this->ssmopt[$namn][0] ) //är denna knapp aktiverad i inställningarna?
						$this->ssmKnappar[$namn] = new ssmKnapp ( array (	'label'=>$sc->_namn,
													'shortcode'=>$namn,
													'shortcut'=>$this->ssmopt[$namn][1],
													'image'=>"$namn.png",
													'edstyle'=>$sc->_style,
										));
				}		

			} //Bygg knappar


			/*Editor-hooks*/

			/*Registrera mce-plugins*/
			add_action('init', array($this,'ssmMcePlugin_add_buttons'));

			/*Hook för att städa, bearbeta o lägga till shortcodetaggarna innan artikeln sparas i databasen*/
			add_filter('content_save_pre', array($this, 'ssmMceShortcodes_cleanup'));
		}

		/*Registrera shortcodes*/
		foreach ( $this->ssmShortcodes as $key => $sc) {
			add_shortcode($key, $sc->_function);
		}

		/*Extra shortcode för säsongsstapel*/
		add_shortcode('sasong', array( $this, 'skrivSasongsstapel'));


		/*Utskriftshooks*/
		if ( !is_admin() ) {

			/*Hook för det sista putsarbetet innan innehållet skriv ut för besökarna*/
			add_filter( "the_content", array($this, 'ssmTags_last') );

			/*Hook för att tagga titeln, om det inte redan finns en titeltagg */
			add_filter( "the_title", array($this, 'ssmTitle') );

			/*Lägg till datum*/
			add_filter( "the_date", array($this, 'ssmDate') );

			/*Lägg till författare*/
			add_filter( "the_author", array($this, 'ssmAuthor') ); 

			/*Lägg till kategorier*/
			add_filter( "the_category", array($this, 'ssmCat') ); 

			/*Hämta diverse data*/
			add_action( "wp", array($this, 'ssmWp') ); 

			/**/
			add_action( "shutdown", array($this, 'ssmShutdown') ); 

			/* JS */
			add_action( "wp_print_scripts", array($this, 'ssmJS') );  

			/* Utskriftsbuffert, för att kunna kolla efter saknade taggar */
			add_action('wp_head', array($this,'buffer_start'));
			add_action('wp_footer', array($this,'buffer_end'));

		}

		// Säsongswidget
		add_action('widgets_init', create_function('', 'return register_widget("SsmWidget");'));


	}
/********************************************************************************************************************/
/********************************************* SLUT function sasongsmat SLUT ****************************************/
/********************************************************************************************************************/


/**************************************************** Hooks *********************************************************/

	/* Gå igenom hela utskriftsbufferten och försök komplettera med saknade taggar */
	//TODO bara om det redan finns minst en tagg
	function callback($buffer) {

		/* Sätt ut namn om det inte redan är gjort */
		if ( !$this->_alreadySet['receptnamn'] && !$this->_opt['manuellt'] )
			/* Sök efter titeltext mellan h1-, h2-, h3- eller h4-taggar */
			$buffer = preg_replace('/((\<h[1|2|3|4])(.+)?>)'.$this->_title.'(\<\/h[1|2|3|4]\>)/', '$2 itemprop="name" $3>'.$this->_title.'$4', $buffer);

		/* Sätt ut författare om det inte redan är gjort */
		if ( !$this->_alreadySet['receptforfattare']  && !$this->_opt['manuellt'] && $this->_author )
			/* Sök efter author i en tagg, vilken som helst. Ingen tågkrasch om vi taggar flera förekomster, bara vi inte tar author inne i t.ex. klass-namn.  */
			$buffer = preg_replace('/\>'.$this->_author.'\</', '><span itemprop="author">'.$this->_author.'</span><', $buffer,1);

		/* Sätt ut bild om det inte redan är gjort */
		if ( !$this->_alreadySet['receptbild']  && !$this->_opt['manuellt'] )
			$buffer = preg_replace('/\<img /','<img itemprop="photo" ', $buffer);

		/* Lägg till omgivande container */
		$buffer = '<div itemscope itemtype="http://data-vocabulary.org/Recipe">' . $buffer . '</div>';
		return $buffer;
	}

	function buffer_start() {
		ob_start(array($this,"callback"));
	}

	function buffer_end() {
		ob_end_flush();
	}
	/* SLUT - utskriftsbuffert*/

	/* Färgväljare till optionssida */
	function ilc_farbtastic_script() {
		wp_enqueue_style( 'farbtastic' );
		wp_enqueue_script( 'farbtastic' );
	}

	/* Javascript på adminsidor */
	function admin_js() {

	}

	/*Lägg till Mce-plugins*/
	function ssmMcePlugin_add_buttons( $buttons ) {

		/* Rich-text-knappar */
		if ( get_user_option('rich_editing') == 'true') {
			add_filter('mce_external_plugins', array($this, 'ssmMcePlugin_add_plugin'));
			//FIXME: pluginen måste kolla var det finns plats för fler knappar!
			add_filter('mce_buttons_3', array($this, 'ssmMcePlugin_register_buttons'), 0);

		}
	}

	/*Städa och bearbeta shortcodetaggarna innan artikeln sparas i databasen, lägg till så mkt som möjligt automatiskt*/
	function ssmMceShortcodes_cleanup ( $in ) {

		foreach ( $this->ssmShortcodes as $key => $sc) {

			/*Lägg till tidskoder till alla taggar som kräver det:*/
			if ( $sc->_datetime ) {
				
				$m = null;
				preg_match_all('/\['.$key.'\](.+)?\[\/'.$key.'\]/m',$in,$m);

				for ($i=0,$count=count($m[1]);$i<$count;$i++) { 

						if (  $sc->_datetime == DURATION_ISO8601 ) {
							/* Konvertera tidsspann */
							$dt = ssmDatum::newDuration($m[1][$i]);
						} else {
							/* Konvertera datum */
							$dt = $m[1][$i]; //TODO: konvertera!
						}

						$in = str_replace($m[0][$i],"[$key datetime='$dt']".$m[1][$i]."[/$key]",$in);

				}

			}

			/* Kapsla in taggar som kräver föräldrar (råvaror, kalorier, etc) */
			/*TODO: Söker just nu bara i samma omgivande tagg och i samma rad. Behöver bli smartare om vi ska ha kvar den här funktionen, till att börja med kolla rekursivt uppåt efter lager som innehåller parent med inte sig själv, för att täcka udda dispositioner. */

			if ($sc->_parent) {

				$parent = $sc->_parent;

				/* Hämta taggar som innehåller den här shortcoden*/
				$m = null;
				preg_match_all('/[p|i|r]\>(([^\<]+)?(\['.$key.'\])(.+)?(\[\/'.$key.'\])(.+)?)\</m',$in,$m);
				$n = null;
				/* Hämta rader som innehåller den här shortcoden*/
				preg_match_all('/^(([^\<]+)?(\['.$key.'\])(.+)?(\[\/'.$key.'\])(.+)?)$/m',$in,$n);

				$o = array_merge( $m[1], $n[1] );

				foreach( $o as $str ) {

					/* Kolla om de redan innehåller obligatorisk parent-tagg */
					if ( !preg_match( '/\['.$parent.'\]/', $str ) ) {

						$in = str_replace($str,'['.$parent.']'.$str.'[/'.$parent.']',$in);

					}
				}				
			}

		}

		/*Gissa ingress (ej på sidor)*/
		if (!is_page_template()) {

			if (!preg_match( "/\[ingress\]/", $in )) {

				//ta första rad som börjar med en bokstav. P-taggar har ännu inte satts ut när den här hooken körs
				$in = preg_replace("/^(\w.+)$/m","[ingress]$1[/ingress]",$in,1);

			}
		}

		return ($in);
	}

	function ssmTags_last ($in) {
		//här kan vi göra en sista koll av taggarna. Om vi växlar från WP-shortcodes till span class så byter vi alla pseudotaggar mot riktiga taggar här

		/* Kolla om taggar som annars ska sättas i andra hooks redan är utplacerade */
		$this->_alreadySet['receptbild'] = strpos($in,'[receptbild]');
		$this->_alreadySet['receptnamn'] = strpos($in,'[receptnamn]');
		$this->_alreadySet['receptforfattare'] = strpos($in,'[receptforfattare]');

		/* Hämta lista över råvaror att använda i API-anrop (js) när resten av sidan är laddad */
		$rvmatch = array();
		preg_match_all( '/\[ravara\](.+)?\[\/ravara\]/', $in, &$rvmatch );
		$this->_rvlista = isset($rvmatch[1]) ? array_unique($rvmatch[1]) : array();
		$rv_str = is_array($this->_rvlista) ? implode(',',$this->_rvlista) : '';
		$rv_str = strip_tags($rv_str);
		echo '<script type="text/javascript">var ssmrvlista = "' . $rv_str . '"</script>';

		return $in;
	}

	function ssmWp ($in) {
		return $in;
	}

	function ssmShutdown () {
	}

	/* Spara titeln för att kunna tagga inlägget vid publicering*/
	function ssmTitle ($in) {
		$this->_title = $in;
		return $in;
	}

	/* Tagga upp kategorier */
	function ssmCat ($in) {
		/* TODO Kolla om det redan finns recepttyp */
		/* TODO Endast valda kategorier om admin gjort ett sådant val i inställningarna */
		/*if ( !$this->_alreadySet['receptkategori'] )*/

		/* Ladda lokala textsträngar för ”okategoriserat” o likn */
		$undantagskat = array ( get_cat_name(1), 'Uncategorized', 'Okategoriserat');	//tag_id 1=Uncategorized. Lägg till på sv o en ifall användaren bytt språk sedan bloggstart
		/* Tagga */ 
		if ( !in_array(strtolower(strip_tags($in)), $undantagskat) )
			$in = "<span itemprop='recipeType'>".$in.'</span>';

		return $in;
	}

	/* Spara författare för att kunna tagga inlägget vid publicering*/
	function ssmAuthor ($in) {
		$this->_author = $in;
		return $in;
	}

	/*TODO: ska flyttas till ssmDatum*/
	function ssmDate ($in) {

		//FIXME:: ssmDatum::iso($in)
		$in = "<span itemprop='published' datetime='$in'>".$in.'</span>';
		return $in;

	}

	function ssmJS () {

		/* Hämta inställningar för att se vilken råvaruinfo vi ska skriva ut */
		if ( isset($this->ssmopt["lankarv"]) && $this->ssmopt["lankarv"] )
			echo '<script type="text/javascript">ssmlankarv = true;</script>';

		/* Skapa kod för råvaror i säsong */
		$optfarg = isset ($this->ssmopt["sasongssymbolfarg"]) ? $this->ssmopt["sasongssymbolfarg"] : '#090';
		switch ($this->ssmopt['sasongssymbol']) {
			case 'ALDUS': $optsymbol = '❦';
					break;
			case 'ALDUSR': $optsymbol = '❧';
					break;
			case 'TEXT':
					$optsymbol = isset ($this->ssmopt["sasongssymboltext"]) ? $this->ssmopt["sasongssymboltext"] : '';
					break;
			default :
					$optsymbol = '●';
		}

		echo '<script type="text/javascript">isasongstr = "&nbsp;&nbsp;<span title=' . "'Den här råvaran är i säsong just nu!' class='isasong' style='color:$optfarg;cursor:default;'>$optsymbol</span>" . '";</script>';

		/* JQUERY */
		wp_enqueue_script('jquery');

		/* JSON */
		wp_enqueue_script('json2');

		/*Hämta resten av JS*/
		$jsdir = 'http://s3-eu-west-1.amazonaws.com/sasongsmat-1/plugins';
		echo "<script type='text/javascript' src='$jsdir/wordpress-0_2.js'></script>";
		

	}

	function skrivSasongsstapel () {

		return '';

	}


	function ssm_options_init() {

		register_setting( 'ssm', 'ssm' );

	}

	
	/*Mce-knappar*/
	function ssmMcePlugin_register_buttons($buttons) {

		array_push($buttons, "separator");

		foreach ( $this->ssmKnappar as $knapp ) {

			array_push($buttons, $knapp->_shortcode);

		}

		return $buttons;
	}
	 
	/*Mce-kod*/
	function ssmMcePlugin_add_plugin ( $plugin_array )
	{

		$url = $this->plugin_url . "editorplugin.php";

		foreach ( $this->ssmKnappar as $knapp ) {

			$label = $knapp->_label;
			$shortcode = $knapp->_shortcode;
			$image =  $knapp->_imagename;
			$bgcolor =  $knapp->_bgcolor;
			$border =  $knapp->_border;			

			$plugin_array[$shortcode] = "$url?label=$label&shortcode=$shortcode&image=$image&border=$border&bgcolor=$bgcolor";//shortcode-namnet blir också namnet på pluginen

		}

		return $plugin_array;
	}

	
	/* När pluginen aktiveras */ 
	function activate_plugin() {

		/* Registrera default-inställningar */

		$defopt = array ();
		/* Knappar */
		foreach ( $this->ssmShortcodes as $key => $sc) {

			/* Hämta knappstatus (on/off) per default*/
			$on = 0;
			if ( isset($sc->_knapp) ) {
				if ($sc->_knapp == DEFAULT_ON || $sc->_knapp == ALLTID)
					$on = 1;
			}

			/* Hämta snabbtangent per default */
			$genv = isset($sc->_knappgenv) ? $sc->_knappgenv : '';

			$defopt[$key] = array ( $on, $genv );
		}

		/* Länka råvaror till ssm? */
		$defopt["lankarv"] = true;

		/* Avancerat */

		/* Vilka språk förväntas recept vara skrivna på? Att användas för att extrahera tidkoder, etc */
		//TODO hämta lista över tillgängliga språk från ssmDatum
		$defopt["sprak"] = array ( 'sv'=>true, 'en'=>true, 'jä́'=>false );

		/* Skippa alla försök att lägga till taggar automatiskt? */
		$defopt["manuellt"] = false;

		/* Visa knappar även i html-redigeringen? */
		$defopt["htmlknappar"] = true;

		/* Vilka kategorier ska användas för att kategorisera recept? Kommaseparerad lista. '' = alla */
		$defopt["receptkategorier"] =  array();

		/*Symbol att visa bredvid recept som är i säsong*/
		$defopt['sasongssymbol'] = 'CIRKEL';
		$defopt['sasongssymbolfarg'] = '#090';
		$defopt['sasongssymboltext'] = __(' i säsong');

		/*Sätt defaultoptions*/
		update_option('ssm', $defopt);
	}
	
	// on deactivation
	function deactivate_plugin() {

		delete_option( 'ssm' );

	}
	
	// add settings link to plugin page
	function plugin_actions($links) {
		array_unshift($links, '<a href="options-general.php?page=sasongsmat">' . __('Inställningar') . '</a>');
		return $links;
	}
	
	// add plugin menu options
	function plugin_menu() {
		add_options_page('Säsongsmat', 'Säsongsmat', 'manage_options', 'sasongsmat', array($this, 'manage_sasongsmat'));
	}		
	
	// manage html editor options
	function manage_sasongsmat() {
	
		?>
		
		<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2><?php _e('Inställningar för Säsongsmat'); ?></h2>
			
			<p><?php _e("Säsongsmat hjälper dig att märka upp dina recept med metadata, så att de kan visas på exempelvis Googles receptsök, Google Recipies, eller Säsongsmat.nu. Den här pluginen skapar några nya knappar i editorn, för att markera t.ex. vad som är en råvara.\n\nDu kan också välja att automatiskt hämta information ur Säsongsmats databaser att visa i dina recept."); ?></p>
			
			<form method="post" action="options.php">

			<?php settings_fields('ssm'); ?>
			<?php //do_settings_sections(__FILE__); ?>
			<?php $opt = get_option("ssm"); ?>


			<h3><?php _e('Allmänt'); ?></h3>
			<table class="form-table">
				<tr valign="top">
				<td><input type="checkbox" name="ssm[lankarv]" <?php if ( isset ( $opt['lankarv']) && $opt['lankarv'] ) { echo 'checked';}; ?> /></td><td>Länka säsongsråvaror till Säsongsmat.nu?</td>
				</tr>
			</table>

			<h3><?php _e('Knappar'); ?></h3>
			<p class="description"><?php _e('Följande snabbtangenter används redan redan i editorn:'); ?> <em><?php _e('strong (b), em (i), href (a), blockquote (q), ins (s), img (m), ul (u), ol (o), li (l), code (c), more (t)'); ?></em></p>

				<table id="ssm-knappar-table" class="form-table" style="width:850px;">
				
					<thead>
						<tr valign="top">
							<th scope="row" style="width:50px;"><?php _e('Visa knapp?') ?></th>
							<th scope="row"><?php _e('Tagg- / knappnamn') ?></th>
							<th scope="row"><?php _e('Snabbtangent') ?> <span class="description">(Ctrl+...)</span></th>
							<th scope="row"></th>
						</tr>
					</thead>
					
					<tbody>

						<?php
						foreach ( $this->ssmShortcodes as $key => $sc) { 
?>
						<tr valign="top">
							<td style="width:50px;"><input type="checkbox" name="ssm[<?php echo $key; ?>][0]" <?php if ( isset ( $opt[$key][0]) && $opt[$key][0] ) { echo 'checked';}; ?> /></td>
							<td><?php echo $key; ?></td>
							<td><input type="text" name="ssm[<?php echo $key; ?>][1]" value="<?php echo $opt[$key][1]; ?>" /></td>
							<td><?php echo $sc->_desc; ?></td>
						</tr>
<?php
						} ?>
						
					</tbody>
					
				</table>

			<h3><?php _e('Avancerat'); ?></h3>
			<table class="form-table">
				<tr valign="top">
				<td><input type="checkbox" name="ssm[manuellt]" <?php if ( isset ( $opt['manuellt']) && $opt['manuellt'] ) { echo 'checked';}; ?> /></td><td><?php _e('Skippa alla försök att lägga till taggar automatiskt?'); ?></td>
				</tr>
				<tr valign="top">
				<td><input type="checkbox" name="ssm[htmlknappar]" <?php if ( isset ( $opt['htmlknappar']) && $opt['htmlknappar'] ) { echo 'checked';}; ?> /></td><td><?php _e('Visa knappar även i html-redigeringen? (ej implementerat)'); ?></td>
				</tr>
				<tr valign="top">
				<td colspan="2"><?php _e('Vilken symbol eller textska användas för att visa att en råvara är i säsong?'); ?> <br />
<input type="radio" name="ssm[sasongssymbol]" value="CIRKEL" <?php if ( isset ( $opt['sasongssymbol']) && $opt['sasongssymbol'] == 'CIRKEL') { echo 'checked';}; ?>> ●<br />
<input type="radio" name="ssm[sasongssymbol]" value="ALDUS" <?php if ( isset ( $opt['sasongssymbol']) && $opt['sasongssymbol']  == 'ALDUS') { echo 'checked';}; ?>> ❦<br />
<input type="radio" name="ssm[sasongssymbol]" value="ALDUSR" <?php if ( isset ( $opt['sasongssymbol']) && $opt['sasongssymbol']  == 'ALDUSR') { echo 'checked';}; ?>> ❧<br />
<input type="radio" name="ssm[sasongssymbol]" value="TEXT" <?php if ( isset ( $opt['sasongssymbol']) && $opt['sasongssymbol'] == 'TEXT' ) { echo 'checked';}; ?>> <input type="TEXT" name="ssm[sasongssymboltext]" value="<?php if ( isset ( $opt['sasongssymboltext']) ) { echo $opt['sasongssymboltext'];}; ?>"><br />
<label for="sasongssymbolfarg"><?php _e('Färg på symbolen:'); ?><br /><input id ="sasongssymbolfarg" type="text" name="ssm[sasongssymbolfarg]" value="<?php if ( isset ($opt['sasongssymbolfarg']) ) { echo $opt['sasongssymbolfarg'];}; ?>"></label><div id="ilctabscolorpicker"></div>

				</tr>
			</table>

				
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php esc_attr_e('Spara') ?>" />
				</p>
			</form>
			
		</div>
<?php
		/* Colorpicker på options-sida*/
		/*JQuery*/
		wp_enqueue_script('jquery');
?>
		<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function() {
    jQuery('#ilctabscolorpicker').hide();
    jQuery('#ilctabscolorpicker').farbtastic("#sasongssymbolfarg");
    jQuery("#sasongssymbolfarg").click(function(){jQuery('#ilctabscolorpicker').slideDown()});
    jQuery("#sasongssymbolfarg").blur(function(){jQuery('#ilctabscolorpicker').slideUp()});
  });
//]]>		 
		</script>
<?php
		
		
	}
	
}
