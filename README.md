PNut
==========

A PHP client library for [Network UPS Tools](https://networkupstools.org/).

***This library is under heavy development.***

# Development

## Test

The project use PHP Unit for testing the library.
A virtual UPS device is available through a container for testing the library.  
Its uses Docker.

```bash
composer prepare-test # Initialize and launch the test environment
composer test # execute tests with PHP Units
composer stop-test # Shutdown the test environment
```
