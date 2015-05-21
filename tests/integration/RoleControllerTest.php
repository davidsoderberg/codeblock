<?php namespace integration;

use Illuminate\Support\Facades\Auth;

class RoleControllerTest extends \IntegrationCase {

	public function setUp() {
		parent::setUp();
		$this->setUpDb();
		Auth::loginUsingId(1);
	}

	public function create_role(){
		$this->visit('roles')
			->submitForm('Create', ['name' => 'test'])
			->see('The role has been created.');
		return $this;
	}

	public function test_create_role(){
		$this->create_role()->onPage('roles');
	}

	public function test_edit_role(){
		$this->create_role();

		$this->visit('roles/1')
			->fill('hej', 'name')
			->press('Update')
			->see('The role has been updated.');
	}

	public function test_set_default(){
		$this->visit('roles')
			->submitForm('Set default', ['default' => 2])
			->see('The default role has been updated.');
	}

	public function test_delete_role(){
		$this->visit('roles')
			->click('Delete')
			->see('The role has been deleted.');

		$this->assertEquals(count(\App\Role::all()), 1);
	}

	public function test_delete_none_existing_role(){
		$this->visit('roles/delete/3')
			->see('The role could not be deleted.');

		$this->assertEquals(count(\App\Role::all()), 2);
	}
}