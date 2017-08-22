<?php
namespace Narrator\Exceptions;


class CallbackExpectedException extends NarratorException
{
	public function __construct()
	{
		parent::__construct("Callback was not set.");
	}
}