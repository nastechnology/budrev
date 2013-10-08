<?php // app/tests/models/UserTest.php

class UserTest extends TestCase {
	
	public function testIsInvalidWithoutAUsername()
	{
		$user = new User;
		$this->assertFalse($user->validate());

	}
}