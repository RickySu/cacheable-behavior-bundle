Propel Cacheable Behavior Bundle
==============

Introduction
------------

This is a symfony2 bundle for propel1 and provides a smart relation cache.

Features
------------

* primary key and unique index query cache.
* one to one relation cache.
* one to many relation cache.
* many to many relation cache.
* auto detect single or composite key.

Requirements
------------

* PHP 5.3.3 above
* rickysu/tagcache 0.1 above

Installation
------------

editing the composer.json file in the root project.

### Editing the composer.json under require: {} section add

```
"rickysu/cacheable-behavior-bundle": "0.1.*",
```

### Update Composer :

```
php composer.phar update
```

How to Use
----------

### setup config

app/config.yml

```yml
propel:
    path:       "%kernel.root_dir%/../vendor/propel"
    phing_path: "%kernel.root_dir%/../vendor/phing"
    logging:    %kernel.debug%
    dbal:
        driver:               %database_driver%
        user:                 %database_user%
        password:             %database_password%
        dsn:                  %database_driver%:host=%database_host%;dbname=%database_name%;charset=UTF8
    behaviors:
        cacheable:           RickySu\CacheableBehaviorBundle\Behavior\CacheableBehavior
```

### add cacheable behavior in your schema.xml

### retrieve data with primary key or unique index

```xml
<?xml version="1.0" encoding="UTF-8"?>
<database name="myorm" defaultIdMethod="native" namespace="ORM\StoreBundle\Model">
    <table name="member">        
        <behavior name="cacheable" />
        <column name="id" type="bigint" primaryKey="true" autoIncrement="true" required="true"/>
        <column name="username" type="varchar" size="45" />
        <column name="email" type="varchar" size="80" />
        <unique>
            <unique-column name="username" />
            <unique-column name="email" />
        </unique>        
    </table>
```

```php
// filter by id =1 and build cache
$Member=MemberPeer::retrieveByPk(1);
$Member=MemberQuery::create()->findPk(1);

//filter by username , email and build cache
$Member=MemberPeer::retrieveByUsernameEmail('username','somebody@foo.com'); 
$Member=MemberQuery::create()
  ->filterByUsername('username')
  ->filterByEmail('somebody@foo.com')
  ->findOne();

//delete cache when save or delete 
$Member->save();
$Member->delete();
```

### retrieve data with one to one relation

```xml
<?xml version="1.0" encoding="UTF-8"?>
<database name="myorm" defaultIdMethod="native" namespace="ORM\StoreBundle\Model">
    <table name="member">
        <behavior name="cacheable" />
        <column name="id" type="bigint" primaryKey="true" autoIncrement="true" required="true"/>
        <column name="username" type="varchar" size="45" />
        <column name="email" type="varchar" size="80" />
    </table>
    <table name="member_info">
        <behavior name="cacheable" />
        <column name="member_id" type="bigint" primaryKey="true" required="true"/>
        <column name="realname" type="varchar" size="45" />        
        <foreign-key foreignTable="member" onDelete="cascade" onUpdate="cascade">
            <reference local="member_id" foreign="id" />
        </foreign-key>
    </table>
```

### retrieve data with one to many relation

```xml
<?xml version="1.0" encoding="UTF-8"?>
<database name="myorm" defaultIdMethod="native" namespace="ORM\StoreBundle\Model">
    <table name="category">
        <behavior name="cacheable" />
        <column name="id" type="bigint" primaryKey="true" autoIncrement="true" required="true"/>
        <column name="name" type="varchar" size="45" />        
    </table>
    <table name="article">
        <behavior name="cacheable" />
        <column name="id" type="bigint" primaryKey="true" required="true"/>
        <column name="category_id" type="bigint" />
        <column name="title" type="varchar" size="128" />
        <column name="content" type="longvarchar" />
        <foreign-key foreignTable="category" onDelete="cascade" onUpdate="cascade">
            <reference local="category_id" foreign="id" />
        </foreign-key>
    </table>
```

### retrieve data with one to many relation

```xml
<?xml version="1.0" encoding="UTF-8"?>
<database name="myorm" defaultIdMethod="native" namespace="ORM\StoreBundle\Model">
    <table name="member">
        <behavior name="cacheable" />
        <column name="id" type="bigint" primaryKey="true" autoIncrement="true" required="true"/>
        <column name="username" type="varchar" size="45" />
    </table>
    <table name="group">
        <behavior name="cacheable" />
        <column name="id" type="bigint" primaryKey="true" required="true"/>
        <column name="groupname" type="varchar" size="45" />
    </table>
    <table name="member_group" isCrossRef="true">
        <behavior name="cacheable" />
        <column name="member_id" type="bigint" primaryKey="true" required="true"/>
        <column name="group_id" type="bigint" primaryKey="true" required="true"/>
        <foreign-key foreignTable="member" onDelete="cascade" onUpdate="cascade">
            <reference local="member_id" foreign="data_id" />
        </foreign-key>
        <foreign-key foreignTable="group" onDelete="cascade" onUpdate="cascade">
            <reference local="group_id" foreign="id" />
        </foreign-key>
    </table>
```

TODO
----

clear cache when change relation mapping.

```php
//set $Article belonds to $Category1
$Article->setCategory($Category1);
$Article->save();

//set $Article belonds to $Category2
$Article->setCategory($Category2);
$Article->save();

//we need to clear $Category1 and $Category2 reference cache.

```

LICENSE
-------

MIT