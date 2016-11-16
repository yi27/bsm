<?php
/*
** Bsm
** Copyright (C) 2001-2016 Bsm SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/


/**
 * Class for regular expressions and Bsm global expressions.
 * Any string that begins with '@' is treated as Bsm expression.
 * Data from Bsm expressions is taken from DB, and cached in static variable.
 *
 * @throws Exception
 */
class CGlobalRegexp {

	const ERROR_REGEXP_EMPTY = 1;
	const ERROR_REGEXP_NOT_EXISTS = 2;

	/**
	 * Determine if it's Bsm expression.
	 *
	 * @var bool
	 */
	protected $isBsmRegexp;

	/**
	 * If we create simple regular expression this contains itself as a string,
	 * if we create Bsm expression this contains array of expressions taken from DB.
	 *
	 * @var array|string
	 */
	protected $expression;

	/**
	 * Cache for Bsm expressions.
	 *
	 * @var array
	 */
	private static $_cachedExpressions = [];

	/**
	 * Checks if expression is valid.
	 *
	 * @static
	 *
	 * @param $regExp
	 *
	 * @throws Exception
	 * @return bool
	 */
	public static function isValid($regExp) {
		if (bs_empty($regExp)) {
			throw new Exception('Empty expression', self::ERROR_REGEXP_EMPTY);
		}

		if ($regExp[0] == '@') {
			$regExp = substr($regExp, 1);

			$sql = 'SELECT r.regexpid'.
					' FROM regexps r'.
					' WHERE r.name='.bs_dbstr($regExp);
			if (!DBfetch(DBselect($sql))) {
				throw new Exception(_('Global expression does not exist.'), self::ERROR_REGEXP_NOT_EXISTS);
			}
		}

		return true;
	}

	/**
	 * Initialize expression, gets data from db for Bsm expressions.
	 *
	 * @param string $regExp
	 *
	 * @throws Exception
	 */
	public function __construct($regExp) {
		if ($regExp[0] == '@') {
			$this->isBsmRegexp = true;
			$regExp = substr($regExp, 1);

			if (!isset(self::$_cachedExpressions[$regExp])) {
				self::$_cachedExpressions[$regExp] = [];

				$dbRegExps = DBselect(
					'SELECT e.regexpid,e.expression,e.expression_type,e.exp_delimiter,e.case_sensitive'.
					' FROM expressions e,regexps r'.
					' WHERE e.regexpid=r.regexpid'.
						' AND r.name='.bs_dbstr($regExp)
				);
				while ($expression = DBfetch($dbRegExps)) {
					self::$_cachedExpressions[$regExp][] = $expression;
				}

				if (empty(self::$_cachedExpressions[$regExp])) {
					unset(self::$_cachedExpressions[$regExp]);
					throw new Exception('Does not exist', self::ERROR_REGEXP_NOT_EXISTS);
				}
			}
			$this->expression = self::$_cachedExpressions[$regExp];
		}
		else {
			$this->isBsmRegexp = false;
			$this->expression = $regExp;
		}
	}

	/**
	 * @param string $string
	 *
	 * @return bool
	 */
	public function match($string) {
		if ($this->isBsmRegexp) {
			$result = true;

			foreach ($this->expression as $expression) {
				$result = self::matchExpression($expression, $string);

				if (!$result) {
					break;
				}
			}
		}
		else {
			$result = (bool) preg_match('/'.$this->expression.'/', $string);
		}

		return $result;
	}

	/**
	 * Matches expression against test string, respecting expression type.
	 *
	 * @static
	 *
	 * @param array $expression
	 * @param $string
	 *
	 * @return bool
	 */
	public static function matchExpression(array $expression, $string) {
		if ($expression['expression_type'] == EXPRESSION_TYPE_TRUE || $expression['expression_type'] == EXPRESSION_TYPE_FALSE) {
			$result = self::_matchRegular($expression, $string);
		}
		else {
			$result = self::_matchString($expression, $string);
		}

		return $result;
	}

	/**
	 * Matches expression as regular expression.
	 *
	 * @static
	 *
	 * @param array $expression
	 * @param string $string
	 *
	 * @return bool
	 */
	private static function _matchRegular(array $expression, $string) {
		$pattern = self::buildRegularExpression($expression);

		$expectedResult = ($expression['expression_type'] == EXPRESSION_TYPE_TRUE);

		return preg_match($pattern, $string) == $expectedResult;
	}

	/**
	 * Combines regular expression provided as definition array into a string.
	 *
	 * @static
	 *
	 * @param array $expression
	 *
	 * @return string
	 */
	private static function buildRegularExpression(array $expression) {
		$expression['expression'] = str_replace('/', '\/', $expression['expression']);

		$pattern = '/'.$expression['expression'].'/';
		if (!$expression['case_sensitive']) {
			$pattern .= 'i';
		}

		return $pattern;
	}

	/**
	 * Matches expression as string.
	 *
	 * @static
	 *
	 * @param array $expression
	 * @param string $string
	 *
	 * @return bool
	 */
	private static function _matchString(array $expression, $string) {
		$result = true;

		if ($expression['expression_type'] == EXPRESSION_TYPE_ANY_INCLUDED) {
			$patterns = explode($expression['exp_delimiter'], $expression['expression']);
		}
		else {
			$patterns = [$expression['expression']];
		}

		$expectedResult = ($expression['expression_type'] != EXPRESSION_TYPE_NOT_INCLUDED);

		if (!$expression['case_sensitive']) {
			$string = mb_strtolower($string);
		}

		foreach ($patterns as $pattern) {
			if (!$expression['case_sensitive']) {
				$pattern = mb_strtolower($pattern);
			}

			$pos = mb_strpos($string, $pattern);
			$tmp = (($pos !== false) == $expectedResult);

			if ($expression['expression_type'] == EXPRESSION_TYPE_ANY_INCLUDED && $tmp) {
				return true;
			}
			else {
				$result = ($result && $tmp);
			}
		}

		return $result;
	}
}
