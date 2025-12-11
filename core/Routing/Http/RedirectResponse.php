<?php
namespace FW\Routing\Http;

class RedirectResponse extends Response
{
	public function __construct(string $location, int $status = 302)
	{
		parent::__construct('', $status);
		$this->header('Location', $location);
	}
}