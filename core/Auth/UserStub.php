<?php
namespace FW\Auth;

class UserStub
{
	private array $roles;

	public function __construct(array $roles = [])
	{
		$this->roles = $roles;
	}

	public function hasRole(?string $role): bool
	{
		if ($role === null) {
			return true;
		}
		return in_array($role, $this->roles, true);
	}
}