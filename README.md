Jsonrpc-common
==============

Common libraries for JSON-Rpc 2.0 protocol implementation

http://www.jsonrpc.org/specification

Libraries are used in jsonrpc-client and jsonrpc-server

Request
-------

class `\Moaction\Jsonrpc\Common\Request`

### Fields (getters and setters for fields):
* *method*
* *id*
* *params*

### Methods
* *toArray()*

```php
$request = new \Moaction\Jsonrpc\Common\Request
$request->setId(1);
$request->setMethod('getUserData');
$request->setParams(array('userId' => 4, 'field' => 'email'));
// here you get valid jsonrpc 2.0 request object ready for json_encode
// \InvalidArgumentException can be thrown when Request object is misconfigured (method is not set).
$data = $request->toArray();
```

Response
--------

class `\Moaction\Jsonrpc\Common\Response`

### Fields (getters and setters for fields):
* *result*
* *error*
* *id*

### Methods
* *fromArray()*
* *hasError()*

### Normal response
```php
// decoded array with jsonrpc 2.0 response
$data = array(
    'id'     => 1,
    'result' => array(
        'email' => 'user@example.com',
    ),
);
// \Moaction\Jsonrpc\Common\Exception can be thrown when object is not valid jsonrpc response
$response = \Moaction\Jsonrpc\Common\Response::fromArray($data);
```

### Error response
```php
// decoded array with jsonrpc 2.0 response
$data = array(
    'id'     => 1,
    'error' => array(
        'code' => '20',
        'message' => 'User not found',
        'data' => array('userId' => 4),
    ),
);
$response = \Moaction\Jsonrpc\Common\Response::fromArray($data);
// \Moaction\Jsonrpc\Common\Error object
$error = $response->getError();
```

Error
-----

class `\Moaction\Jsonrpc\Common\Error`

Error object. See response for example.


Exception
---------
class `\Moaction\Jsonrpc\Common\Exception`

Exception for using in jsonrpc libraries
