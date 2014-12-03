Simple Roles [![Build Status](https://travis-ci.org/bradbonkoski/simpleRoles.svg?branch=master)](https://travis-ci.org/bradbonkoski/simpleRoles) [![Code Climate](https://codeclimate.com/github/bradbonkoski/simpleRoles/badges/gpa.svg)](https://codeclimate.com/github/bradbonkoski/simpleRoles)
===================
## This is a simple web service to manage a roles database.  

## Overview
A roles database helps group users usually in order to grant access permissions.  For example, there might be a "reading" role for you web application and anyone who should be able to "read" would be added to that role.  The purpose of the roles system is not to enforce the roles (which is left to the application) but to have a centralized place where roles can be defined and potentially reused across multiple systems requiring different access levels and permissions.

If Applications chose to use the roles system for access gating functionality it should ensure that it is the one enforcing the roles.

## Setup

### Requirements
- PHP 5.3 or higher (not including hhvm)

### Deploying locally

### Building and deploying within a Docker container
A docker file has been included which layers on top of to a general purpose php/apache web container.  The container built herein does not include a database while the application does require it.  This was done on purpose to allow flexibility based on the environment for which this application will run.  

Reference database container using mariadb can be found here: <https://registry.hub.docker.com/u/bradbonkoski/ubuntu14.04-mariadb/>

The Base container for this Docker file is here: <https://registry.hub.docker.com/u/bradbonkoski/php-apache/>

In order to build simply type (from the project base dir):
```
docker build -t "<namespace>/<name>"
```
This will build your container, now in order to run it linked to the mariadb container mentioned earlier do this:

```
docker run -d -p 3306 --name mariadb bradbonkoski/ubuntu14.04-mariadb
docker run -p 80 --name simple-roles --link mariadb:db -d <name of container>:<tag>
```


### The Web Service Access Points

#### GET /roles
Will list all known roles within the system

#### GET /role/[pattern]
Will List all roles known within the system that match the pattern defined

#### GET /roles/[user]/[role]
A Boolean True/False to see is [user] is in [role]
Returns 200 for True and a 404 if the user is not found in that role

#### GET /users/[role]
Returns a list of all users within [role]

#### POST /roles
Creates [a] new role(s).  New roles to be created should be passed in as a json blob with the name of the role and a description
```
{"roles":{"name":"newRole","description":"Description of a new role"}}
```

#### POST /users
Creates new users within the system.  This should not be used as a user source of truth, more for user importing.  The data consists of the Name of the user, the user's 'username' which serves as the primary key for uniqueness, and a reference or ref to the actual user system where the user's data is stored.  This ref can either be a entire link, or a portion of a link.  i.e.
```
{"users":{"name":"Frank Frawn","username":"frawnf","ref":"?user=frawnf"},"0":{"name":"Shawn Young","username":"youngs","ref":"?user=younfs"}}
```
#### PUT /roles
Add a user to a role.  Should have JSON body with a series of username/role pairs i.e.
```
[{"role":"read","user":"poolea"},{"role":"read","user":"leev"},{"role":"read","user":"kingr"},{"role":"read","user":"grays"}]
```

#### DELETE /roles
Removes user(s) from role(s)

#### DELETE /role/[role]
Delete a role