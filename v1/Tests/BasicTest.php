<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client as Guzzle;

class BasicTest extends TestCase{

    public function testAddingTwo(){
        $this->assertEquals(4, 2 + 2);
    }

    public function testCanGetAllUsers(){

        $client = new Guzzle();
        // API call...
        $response = $client->get('http://localhost:8888/RestfulAPI-Users/v1/users', [
            'headers' => ['Authorization' => 'MWZkMzIzYjE3MWYxMWI0YTdjZWJkYjk1N2UzZGJkN2I5YzRjOGQ3OGVjMmY1MWU3MTYxNzcyODc3OQ==']
        ]);

        // confirm response status code...
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testSearchForUser(){

        $client = new Guzzle();
        // API call...
        $response = $client->get('http://localhost:8888/RestfulAPI-Users/v1/users/1', [
            'headers' => ['Authorization' => 'MWZkMzIzYjE3MWYxMWI0YTdjZWJkYjk1N2UzZGJkN2I5YzRjOGQ3OGVjMmY1MWU3MTYxNzcyODc3OQ==']
        ]);

        // confirm response status code...
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDeleteAUser(){

        $client = new Guzzle();
        // API call...
        $response = $client->delete('http://localhost:8888/RestfulAPI-Users/v1/users/5', [
            'headers' => ['Authorization' => 'MWZkMzIzYjE3MWYxMWI0YTdjZWJkYjk1N2UzZGJkN2I5YzRjOGQ3OGVjMmY1MWU3MTYxNzcyODc3OQ==']
        ]);

        // confirm response status code...
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCreateAUser(){

        $client = new Guzzle();
        // API call...
        $response = $client->post('http://localhost:8888/RestfulAPI-Users/v1/users', [
            'headers' => ['Authorization' => 'MWZkMzIzYjE3MWYxMWI0YTdjZWJkYjk1N2UzZGJkN2I5YzRjOGQ3OGVjMmY1MWU3MTYxNzcyODc3OQ=='],
            'json' =>
                [
                    'firstName' => 'Johhny',
                    'lastName' => 'Willams',
                    'username' => 'JohnWilliams',
                    'darkMode' => 1
                ]
        ]);

        // confirm response status code...
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testUpdateAUser(){

        $client = new Guzzle();
        // API call...
        $response = $client->patch('http://localhost:8888/RestfulAPI-Users/v1/users/1', [
            'headers' => ['Authorization' => 'MWZkMzIzYjE3MWYxMWI0YTdjZWJkYjk1N2UzZGJkN2I5YzRjOGQ3OGVjMmY1MWU3MTYxNzcyODc3OQ=='],
            'json' =>
                [
                    'firstName' => 'NewJohhny',
                    'lastName' => 'NewWillams',
                ]
        ]);

        // confirm response status code...
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testToggleAUserDarkMode(){

        $client = new Guzzle();
        // API call...
        $response = $client->patch('http://localhost:8888/RestfulAPI-Users/v1/users/1/darkModeToggle', [
            'headers' => ['Authorization' => 'MWZkMzIzYjE3MWYxMWI0YTdjZWJkYjk1N2UzZGJkN2I5YzRjOGQ3OGVjMmY1MWU3MTYxNzcyODc3OQ==']
        ]);

        // confirm response status code...
        $this->assertEquals(200, $response->getStatusCode());
    }

}