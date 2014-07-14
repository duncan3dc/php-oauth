php-oauth
=========

Handles basic OAuth/OAuth2 authentication along with classes for common services

__I really don't think you will find these classes useful, if you proceed then don't say I didn't warn you__


Requirements
------------

* These classes require use of the sql-class project
* A connection to a database must be initiated using the global variable $sql
* The active database must contain a table called oauth (or know about it via the definitions within the Sql class)
* The table must have the following schema
```
`type`      varchar(20),
`username`  varchar(100),
`state`     int(11),
`token`     varchar(100),
`secret`    varchar(100),
UNIQUE KEY `type, username` (`type`,`username`)
```


OAuth2
------
The OAuth2 classes require manual intervention to get authorised initially, but once your token/secret are in the database they work well.
