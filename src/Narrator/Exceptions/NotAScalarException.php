<?php
namespace Narrator\Exceptions;


class NotAScalarException extends NarratorException
{
	public function __construct()
	{
		parent::__construct("Method expects a scalar value.");
	}
}