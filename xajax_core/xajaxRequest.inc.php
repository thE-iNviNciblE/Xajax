<?php
/*
	File: xajaxRequest.inc.php

	Contains the xajaxRequest class

	Title: xajaxRequest class

	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

/*
	@package xajax
	@version $Id: xajaxRequest.inc.php 362 2007-05-29 15:32:24Z calltoconstruct $
	@copyright Copyright (c) 2005-2007 by Jared White & J. Max Wilson
	@copyright Copyright (c) 2008-2010 by Joseph Woolley, Steffen Konerow, Jared White  & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

/*
	Constant: XAJAX_FORM_VALUES
		Specifies that the parameter will consist of an array of form values.
*/
if (!defined('XAJAX_FORM_VALUES')) define ('XAJAX_FORM_VALUES', 'get form values');
/*		
	Constant: XAJAX_INPUT_VALUE
		Specifies that the parameter will contain the value of an input control.
*/
if (!defined('XAJAX_INPUT_VALUE')) define ('XAJAX_INPUT_VALUE', 'get input value');
/*		
	Constant: XAJAX_CHECKED_VALUE
		Specifies that the parameter will consist of a boolean value of a checkbox.
*/
if (!defined('XAJAX_CHECKED_VALUE')) define ('XAJAX_CHECKED_VALUE', 'get checked value');
/*		
	Constant: XAJAX_ELEMENT_INNERHTML
		Specifies that the parameter value will be the innerHTML value of the element.
*/
if (!defined('XAJAX_ELEMENT_INNERHTML')) define ('XAJAX_ELEMENT_INNERHTML', 'get element innerHTML');
/*		
	Constant: XAJAX_QUOTED_VALUE
		Specifies that the parameter will be a quoted value (string).
*/
if (!defined('XAJAX_QUOTED_VALUE')) define ('XAJAX_QUOTED_VALUE', 'quoted value');
/*		
	Constant: XAJAX_JS_VALUE
		Specifies that the parameter will be a non-quoted value (evaluated by the 
		browsers javascript engine at run time.
*/
if (!defined('XAJAX_JS_VALUE')) define ('XAJAX_JS_VALUE', 'unquoted value');

/*
	Class: xajaxRequest
	
	Used to store and generate the client script necessary to invoke
	a xajax request from the browser to the server script.
	
	This object is typically generated by the <xajax->register> method
	and can be used to quickly generate the javascript that is used
	to initiate a xajax request to the registered function, object, event
	or other xajax call.
*/
class xajaxRequest
{
	/*
		String: sName
		
		The name of the function.
	*/
	private $sName;

	/*
		String: sQuoteCharacter
		
		A string containing either a single or a double quote character
		that will be used during the generation of the javascript for
		this function.  This can be set prior to calling <xajaxRequest->printScript>
	*/
	private $sQuoteCharacter;

	/*
		Array: aParameters
	
		An array of parameters that will be used to populate the argument list
		for this function when the javascript is output in <xajaxRequest->printScript>	
	*/
	private $aParameters;

	/*
		Function: xajaxRequest
		
		Construct and initialize this request.
		
		sName - (string):  The name of this request.
	*/
	public function __construct($sName)
	{
		$this->aParameters = array();
		$this->sQuoteCharacter = '"';
		$this->sName = $sName;
	}

	/*
		Function: useSingleQuote
		
		Call this to instruct the request to use single quotes when generating
		the javascript.
	*/
	public function useSingleQuote()
	{
		$this->sQuoteCharacter = "'";
	}

	/*
		Function: useDoubleQuote
		
		Call this to instruct the request to use double quotes while generating
		the javascript.
	*/
	public function useDoubleQuote()
	{
		$this->sQuoteCharacter = '"';
	}

	/*
		Function: clearParameters
		
		Clears the parameter list associated with this request.
	*/
	public function clearParameters()
	{
		$this->aParameters = array();
	}

	/*
		Function: addParameter
		
		Adds a parameter value to the parameter list for this request.
		
		sType - (string): The type of the value to be used.
		sValue - (string: The value to be used.
		
		See Also:
		See <xajaxRequest->setParameter> for details.
	*/
	public function addParameter()
	{
		$aArgs = func_get_args();

		if (1 < count($aArgs))
			$this->setParameter(
				count($this->aParameters),
				$aArgs[0],
				$aArgs[1]);
	}

	/*
		Function: setParameter
		
		Sets a specific parameter value.
		
		Parameters:
		
		nParameter - (number): The index of the parameter to set
		sType - (string): The type of value
		sValue - (string): The value as it relates to the specified type
		
		Note:
		
		Types should be one of the following <XAJAX_FORM_VALUES>, <XAJAX_QUOTED_VALUE>,
		<XAJAX_JS_VALUE>, <XAJAX_INPUT_VALUE>, <XAJAX_CHECKED_VALUE>.  
		The value should be as follows:
			<XAJAX_FORM_VALUES> - Use the ID of the form you want to process.
			<XAJAX_QUOTED_VALUE> - The string data to be passed.
			<XAJAX_JS_VALUE> - A string containing valid javascript (either a javascript
				variable name that will be in scope at the time of the call or a 
				javascript function call whose return value will become the parameter.
				
	*/
	public function setParameter()
	{
		$aArgs = func_get_args();

		if (2 < count($aArgs)) {
			$nParameter = $aArgs[0];
			$sType = $aArgs[1];

			if (XAJAX_FORM_VALUES == $sType) {
				$sFormID = $aArgs[2];
				$this->aParameters[$nParameter] =
					"xajax.getFormValues("
						. $this->sQuoteCharacter
						. $sFormID
						. $this->sQuoteCharacter
						. ")";
			}
			else if (XAJAX_INPUT_VALUE == $sType) {
				$sInputID = $aArgs[2];
				$this->aParameters[$nParameter] =
					"xajax.$("
						. $this->sQuoteCharacter
						. $sInputID
						. $this->sQuoteCharacter
						. ").value";
			}
			else if (XAJAX_CHECKED_VALUE == $sType) {
				$sCheckedID = $aArgs[2];
				$this->aParameters[$nParameter] =
					"xajax.$("
						. $this->sQuoteCharacter
						. $sCheckedID
						. $this->sQuoteCharacter
						. ").checked";
			}
			else if (XAJAX_ELEMENT_INNERHTML == $sType) {
				$sElementID = $aArgs[2];
				$this->aParameters[$nParameter] =
					"xajax.$("
						. $this->sQuoteCharacter
						. $sElementID
						. $this->sQuoteCharacter
						. ").innerHTML";
			}
			else if (XAJAX_QUOTED_VALUE == $sType) {
				$sValue = $aArgs[2];
				$this->aParameters[$nParameter] =
					$this->sQuoteCharacter
						. $sValue
						. $this->sQuoteCharacter;
			}
			else if (XAJAX_JS_VALUE == $sType) {
				$sValue = $aArgs[2];
				$this->aParameters[$nParameter] = $sValue;
			}
		}
	}

	/*
		Function: getScript
		
		Returns a string representation of the script output (javascript) from 
		this request object.  See also:  <xajaxRequest::printScript>
	*/
	public function getScript()
	{
		ob_start();
		$this->printScript();
		return ob_get_clean();
	}

	/*
		Function: printScript
		
		Generates a block of javascript code that can be used to invoke
		the specified xajax request.
	*/
	public function printScript()
	{
		echo $this->sName;
		echo '(';

		$sSeparator = '';

		foreach ($this->aParameters as $sParameter)
		{
			echo $sSeparator;
			echo $sParameter;
			$sSeparator = ', ';
		}

		echo ')';
	}
}

/*
	Class: xajaxCustomRequest
	
	This class extends the <xajaxRequest> class such that simple javascript
	can be put in place of a xajax request to the server.  The primary purpose
	of this class is to provide simple scripting services to the <xajaxControl>
	based objects, like <clsInput>, <clsTable> and <clsButton>.
*/
class xajaxCustomRequest extends xajaxRequest
{
	/*
		Array: aVariables;
	*/
	var $aVariables;

	/*
		String: sScript;
	*/
	var $sScript;

	/*
		Function: xajaxCustomRequest
		
		Constructs and initializes an instance of the object.
		
		Parameters:
		
		sScript - (string):  The javascript (template) that will be printed
			upon request.
		aVariables - (associative array, optional):  An array of variable name, 
			value pairs that will be passed to <xajaxCustomRequest->setVariable>
	*/
	function xajaxCustomRequest($sScript)
	{
		$this->aVariables = array();
		$this->sScript = $sScript;
	}

	/*
		Function: clearVariables
		
		Clears the array of variables that will be used to modify the script before
		it is printed and sent to the client.
	*/
	function clearVariables()
	{
		$this->aVariables = array();
	}

	/*
		Function: setVariable
		
		Sets a value that will be used to modify the script before it is sent to
		the browser.  The <xajaxCustomRequest> object will perform a string 
		replace operation on each of the values set with this function.
		
		Parameters:
			$sName - (string): Variable name
			$sValue - (string): Value
		
	*/
	function setVariable($sName, $sValue)
	{
		$this->aVariables[$sName] = $sValue;
	}

	/*
		Function: printScript
	*/
	function printScript()
	{
		$sScript = $this->sScript;
		foreach ($this->aVariables as $sKey => $sValue)
			$sScript = str_replace($sKey, $sValue, $sScript);
		echo $sScript;
	}
}
