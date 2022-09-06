# rekves
Express like request-response for php

To create `Request` and `Response` objects. Navigate to your PHP file that will take care of given ajax and to start of the script add this line:
```php
require_once("./path/to/rekves/directory/rekves.php");

// example in project
// directory struture:
//   project
//     - lib
//       - rekves
//         - src
//         - rekves.php

require_once("./lib/rekves/rekves.php");
```

This script will create `$res` variable for `Response` and `$req` variable for `Request`.

### Request

`class WriteRegistry extends class RequestRegistry extends abstract class StrictRegistry`

Class `StrictRegistry` strictly checks that getting values are not null. To bypass this restriction use `get` method.
```php
/* (class extending StrictRegistry) */->myProp; // throws somesort of Exception difined by extending class
/* (class extending StrictRegistry) */->get("myProp"); // -> null

// if myParam was defined both would return its value
```
This class also provides `load` and `unset` method. Load method loads entries from string to string maps bypassing setting restriction on extending class and unset method unsets the property on given property name.

`RequestRegistry` adds restriction to getting a property. If the property is null it will send response to client with status code 400 - Bad request and message: "{property name} is required for this operation.".

`WriteRegistry` adds restriction to setting a property. Setting value must pass through assigning function.

##### Requst object properties:

- method: HTTP method that was used to access the script
- host: url part of the host (https://www.example.com)
- uri: url part after host (/rekves/user.php?id=4&theme=dark)
- fullUrl: host and uri parts combined (https://www.example.com/rekves/user.php?id=4&theme=dark)
- res: pointer to `Response` object for the `Request`
- body: `RequestRegistry` object, combines `$_GET`, `$_POST` and `$_FILES` super global arrays
```php
// url: /body.php
$req->body->id; // -> will send response to client with status code 400 - Bad request and message: "id is required for this operation."
$req->body->get("id"); // -> null

// url: body.php?id=5
$req->body->id; // -> "5"
$req->body->get("id"); // -> "5"
```
- session: `WriteRegistry` object setting restriction: Setting value must be set to super global array `$_SESSION`
```php
$req->session->user = $userID;
// same as:
$_SESSION["user"] = $userID;
```
- cookies: `WriteRegistry` object setting restriction: Setting value must be class of Cookie (this class is automatically required by `rekves.php`). Cookie object does not have the name of the cookie. Values of cookies are automatically serialized and deserialized.
```php
$req->cookies->myCookie = "my cookie value"; // -> throws Exception
$req->cookies->myCookie = new Cookie("my cookie value"); // sets cookie for the user
```

### Response

Contains most of the status codes as static constants.

To set headers use `setHeader($name, $value)` to set multiple use `setHeaders($headers)` headers is array of header arrays.

```php
$res->setHeaders([
  ["Content-Description", "File Transfer"], // [0] -> name of the header; [1] -> value of the header
  ["Content-Type", 'application/octet-stream']
]);

if ($res->hasHeader("Content-Type")) {
  # code
}

$res->removeHeader("Content-Description"); // -> removes "Content-Description" from headers

$res->setStatusCode(Response::NOT_FOUND); // sets 404

// these methods exits the code
// send plain text
$res->send("Hello world!");

// send JSON
$res->json(/* JSON encodeable */(object)["user" => 4, "isAdmin" -> false]); // -> { user: 4, isAdmin: false }

// download file
$res->download("/.img/cat.jpg");
```

The browser still needs to be redirected to this script to download the file.

download.php:
```php
// added rekves library
$res->download("./img/" . $req->body->file);
```
script.js:
```js
// some logic, time to download cat.jpg
window.location.replace("./download.php?file=cat.jpg");
```

```php
// send error message to client
$res->setStatusCode(Response::INTERNAL_SERVER_ERROR);
$res->error("Somethig went wrong... but we are trying hard to fix it!");
```
