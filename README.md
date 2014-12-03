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
