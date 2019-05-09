<?php
/**
 * Created by PhpStorm.
 * User: rakib
 * Date: 06-May-19
 * Time: 2:58 PM
 */

namespace R4kib\Cloudbeds\Test\Provider\CloudbedsTest;

use PHPUnit\Framework\TestCase;
use R4kib\Cloudbeds\Provider\CLoudbedsResourceOwner;

class CloudbedsResourceOwnerTest extends TestCase
{
    protected $owner;

    protected function setUp()
    {
        $this->owner = new CLoudbedsResourceOwner([
            'user_id' => 'mock_user_id',
            'first_name' => 'mock_first_name',
            'last_name' => 'mock_last_name',
            'email'=>'mock_email'
        ]);
    }

    public function testGetID()
    {
        $id = $this->owner->getID();
        $this->assertEquals($id,'mock_user_id');
    }
    public function testGetFirstName()
    {
              $this->assertEquals($this->owner->getFirstName(),'mock_first_name');
    }

    public function testGetLastName()
    {

        $this->assertEquals($this->owner->getLastName(),'mock_last_name');
    }

    public function testGetEmail()
    {

        $this->assertEquals($this->owner->getEmail(),'mock_email');
    }



}