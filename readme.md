**NOTE: This is a work in progress. Consider it be pre-alpha. Things will break.**

# Sqlia

Sqlia is a tool that allows you to run a temporary, in-memory version of MySQL on Linux or OS X. This would be primarily useful for testing where you may need to test against a real database server. Note: Sqlia is NOT for production use. It is a testing tool only.

## System Requirements
- OS X 10.11 or Ubuntu 14.04 LTS (others may work but have not been tested)
- PHP >=7.0.7
- MySQL

## Usage

### Starting Sqlia
`./bin/sqlia start mysql`

You may provide a different port number than the default of 3344:
`./bin/sqlia start mysql --port=3366`

You may also provide a different username (default: `sqlia`) and password (default: `sqlia`) to use for the database user:
`./bin/sqlia start mysql --username=homestead --password=secret`

### Creating or re-creating databases

To create a database you may run the following command. It will drop the database if it already exists.
`./bin/sqlia refresh mysql sqlia`

You may provide more than one database like so:
`./bin/sqlia refresh mysql one two three`

### Stopping Sqlia
`./bin/sqlia stop mysql`

## Todo
- Postgres support
- Use PDO for running all queries
- Running multiple database servers at the same time
- `kill` command to forcefully kill the running database server
- `cleanup` command to ensure there are no leftovers if an error occurs
- `console` command to drop you into the database's console (if available)
