# RestfulAPI-Users
Fun project of a Users API.

### <ins>Set up</ins>
<ol>
<li>Edit config/config.php file for the correct database credentials.</li>
<li>Run the following curl command to create tables in your database:</li>
</ol>

` curl http://localhost:8888/RestfulAPI-Users/v1/tables`

<br>

### <ins>**API Curl Commands**</ins>
List All Users: 
<br>

`curl --request GET http://localhost:8888/RestfulAPI-Users/v1/users --header 'Authorization: MWZkMzIzYjE3MWYxMWI0YTdjZWJkYjk1N2UzZGJkN2I5YzRjOGQ3OGVjMmY1MWU3MTYxNzcyODc3OQ=='`

<br>
Search For User By ID:
<br>

`curl --request GET http://localhost:8888/RestfulAPI-Users/v1/users/1 --header 'Authorization: MWZkMzIzYjE3MWYxMWI0YTdjZWJkYjk1N2UzZGJkN2I5YzRjOGQ3OGVjMmY1MWU3MTYxNzcyODc3OQ=='`

<br>
Delete User:
<br>

`curl --request DELETE http://localhost:8888/RestfulAPI-Users/v1/users/5 --header 'Authorization: MWZkMzIzYjE3MWYxMWI0YTdjZWJkYjk1N2UzZGJkN2I5YzRjOGQ3OGVjMmY1MWU3MTYxNzcyODc3OQ=='`

<br>
Create A User:
<br>

`curl --request POST http://localhost:8888/RestfulAPI-Users/v1/users --header 'Authorization: MWZkMzIzYjE3MWYxMWI0YTdjZWJkYjk1N2UzZGJkN2I5YzRjOGQ3OGVjMmY1MWU3MTYxNzcyODc3OQ=='
--header 'Content-Type: application/json'
--data-raw '{
"firstName": "Johhny",
"lastName": "Willams",
"username": "JohnWilliams",
"darkMode": 1
}'`

<br>
Update A User (first and last name):
<br>

`curl --request PATCH http://localhost:8888/RestfulAPI-Users/v1/users/1 --header 'Authorization: MWZkMzIzYjE3MWYxMWI0YTdjZWJkYjk1N2UzZGJkN2I5YzRjOGQ3OGVjMmY1MWU3MTYxNzcyODc3OQ=='
--header 'Content-Type: application/json'
--data-raw '{
"firstName": "John",
"lastName": "Willams"
}'`

<br>
Toggle DarkMode:
<br>

`curl --request PATCH 'http://localhost:8888/RestfulAPI-Users/v1/users/2/darkModeToggle' --header 'Authorization: MWZkMzIzYjE3MWYxMWI0YTdjZWJkYjk1N2UzZGJkN2I5YzRjOGQ3OGVjMmY1MWU3MTYxNzcyODc3OQ=='`

<br>
<br>

### <ins>**Unit tests**</ins>
<br>
Test All endpoints
<br>

`./vendor/bin/phpunit`

<br>
Test CanGetAllUsers method:
<br>

`./vendor/bin/phpunit --filter=testCanGetAllUsers`

<br>
Test SearchForUser method:
<br>

`./vendor/bin/phpunit --filter=testSearchForUser`

<br>
Test DeleteAUser method:
<br>

`./vendor/bin/phpunit --filter=testDeleteAUser`

<br>
Test CreateAUser method:
<br>

`./vendor/bin/phpunit --filter=testCreateAUser`

<br>
Test UpdateAUser method:
<br>

`./vendor/bin/phpunit --filter=testUpdateAUser`

<br>
Test ToggleAUserDarkMode method:
<br>

` ./vendor/bin/phpunit --filter=testToggleAUserDarkMode`

