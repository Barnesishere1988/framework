<?php

namespace FW\Routing\Http;

use FW\Auth\UserStub;

class Request
{
	public string $method;
	public string $uri;
	public array  $query;
	public array  $post;
	public array  $cookies;
	public array  $headers;
	public mixed  $body;
	public string $ip;

	public function __construct()
	{
		$this->method  = $_SERVER['REQUEST_METHOD'] ?? 'GET';
		$this->uri		 = $this->detectUri();
		$this->query	 = $_GET;
		$this->post		 = $_POST;
		$this->cookies = $_COOKIE;
		$this->headers = $this->getHeaders();
		$this->body		 = file_get_contents('php://input');
		$this->ip			 = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
	}

	private function detectUri(): string
	{
		$uri = $_SERVER['REQUEST_URI'] ?? '/';
		$uri = parse_url($uri, PHP_URL_PATH);

		// doppelte Slashes entfernen
		return rtrim($uri, '/') === '' ? '/' : rtrim($uri, '/');
	}

	private function getHeaders(): array
	{
		if (function_exists('getallheaders')) {
			return getallheaders();
		}

		// Fallback für nicht-Apache Server
		$headers = [];
		foreach ($_SERVER as $key => $value) {
			if (str_starts_with($key, 'HTTP_')) {
				$name = str_replace('_', '-', substr($key, 5));
				$headers[$name] = $value;
			}
		}
		return $headers;
	}

	public function json(bool $assoc = true): mixed
	{
		return json_decode($this->body, $assoc);
	}

	public function user(): ?UserStub
	{
		// Phase 4: Stub aus Session (oder Default)
		if (!isset($_SESSION['__fw_user_stub_roles'])) {
			return null;
		}

		// Default: eingeloggt ohne Rollen (oder null, wenn du auth strikt willst)
		return new UserStub($_SESSION['__fw_user_stub_roles']);
	}

	public function wantsJson(): bool
	{
		$accept = $_SERVER['HTTP_ACCEPT'] ?? '';
		$xhr		= $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';

		return str_contains($accept, 'application/json')
			|| $xhr === 'XMLHttpRequest';
	}
}
