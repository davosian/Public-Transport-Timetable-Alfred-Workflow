<?php
namespace Utils\I18N;

require_once 'src/Utils/I18N/Dictionary.php';

/**
 * Utility for doing internationalization stuff.
 * All methods have to be static.
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */
abstract class I18NUtil
{
	/**
	 * Location of all translation files.
	 */
	const DICTIONARY_FILEPATH = "i18n/";

	/**
	 * Extension of all translation files.
	 */
	const DICTIONARY_EXTENSION = ".ini";

	/**
	 * Dictionary filename of fallback.
	 * TODO: change it to "en" after it was created.
	 */
	const DICTIONARY_DEFAULT = "de";

	/**
	 * Cached locale string.
	 * eg. de_CH, en_UK
	 * use #getLocale()
	 */
	private static $locale = null;

	/**
	 * Cached dictionary for the current locale.
	 * use #getDictionary()
	 */
	private static $dictionary = null;

	/**
	 * Caches the locale after the first call.
	 * Works: defaults read -g AppleLocale
	 * Doesn't work: locale, env, $LANG
	 *
	 * @return the default locale of the user. (eg. de_CH, en_UK)
	 */
	public static function getLocale()
	{
		if( self::$locale == null )
		{
			self::$locale = exec( "defaults read -g AppleLocale" ); // eg. de_CH
		}

		return self::$locale;
	}

	/**
	 * Caches the dictionary after the first call.
	 * @return the dictionary with the user locale (or only language) or the fallback locale.
	 */
	public static function getDictionary()
	{
		if( self::$dictionary == null )
		{
			$locale = self::getLocale();

			// try with full locale: eg. i18n/de_CH.ini
			$file = self::DICTIONARY_FILEPATH . $locale . self::DICTIONARY_EXTENSION;

			if( !file_exists( $file ) )
			{
				// try with language: eg. i18n/de.ini
				$file = self::DICTIONARY_FILEPATH . substr( $locale, 0, stripos( $locale, "_" ) ) . self::DICTIONARY_EXTENSION;

				if( !file_exists( $file ) )
				{
					// fallback to English: i18n/en.ini
					$file = self::DICTIONARY_FILEPATH . DICTIONARY_DEFAULT . self::DICTIONARY_EXTENSION;
				}
			}

			self::$dictionary = new Dictionary( parse_ini_file( $file, true ) );
		}

		return self::$dictionary;
	}
}