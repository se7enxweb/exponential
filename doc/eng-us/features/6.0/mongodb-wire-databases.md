# Databases that speak the MongoDB protocol

This page is for administrators who want to run Exponential on a database that speaks the MongoDB wire protocol but
is not MongoDB, and for developers who work on its driver: **FerretDB**, **Amazon DocumentDB** and **Azure Cosmos DB
for MongoDB** (request units and vCore). From Exponential 6.0.15 the kernel has a driver for each of them. Every one is
the MongoDB adapter `expMongoDB` with a profile of what its database does differently (`expMongoWireProfile`), so the
kernel treats the database as MongoDB.

**Status.** The drivers and profiles are written from each vendor's documentation and are being tested: FerretDB 2 and
the Azure Cosmos DB emulator run in CI, Amazon DocumentDB has no emulator and is not tested. The level each database is
expected to reach is in the table below; it is updated as the tests run.

## Contents

- [The databases](#the-databases)
- [Settings](#settings)
- [What the profiles change](#what-the-profiles-change)
- [Installing a site](#installing-a-site)
- [Tests](#tests)

## The databases

| Database | DatabaseImplementation | Port | Expected level | Tested |
|---|---|---|---|---|
| MongoDB 4.0 and later | `mongodb` (`expMongoDB`) | 27017 | full site | yes |
| FerretDB 2 (PostgreSQL with the DocumentDB extension) | `ferretdb` (`expFerretDB`) | 27017 | full site | in progress |
| Amazon DocumentDB 5.0 | `documentdb` (`expDocumentDB`) | 27017, TLS | data tables | no (no emulator) |
| Azure Cosmos DB for MongoDB, request units (4.2) | `cosmosdb` (`expCosmosMongoDB`) | 10255, TLS | data tables | in progress (emulator) |
| Azure Cosmos DB for MongoDB vCore | `cosmosdbvcore` (`expCosmosVCoreDB`) | 10260, TLS | full site | no |

FerretDB 1 (over plain PostgreSQL or SQLite) has no `$lookup` and cannot hold a site.

## Settings

`site.ini [DatabaseSettings]` gives Server, Port, User, Password and Database as for MongoDB; an empty Port is the
profile's. `[MongoWireSettings]`:

| Setting | Meaning |
|---|---|
| `Profile` | empty: the driver's own profile; `mongodb`, `ferretdb`, `documentdb`, `cosmosdb` or `cosmosdbvcore` |
| `TLS` | `enabled`, `disabled`; empty: the profile's (on for DocumentDB and Cosmos DB) |
| `TLSCAFile` | the CA bundle, e.g. Amazon's `global-bundle.pem` for DocumentDB |
| `ReplicaSet` | empty: the profile's (`rs0` for DocumentDB); `none`: no replica set |
| `RetryWrites` | `enabled`, `disabled`; empty: the profile's (off for FerretDB, DocumentDB and Cosmos DB) |
| `AuthSource`, `ReadPreference` | written into the connection URI when set |
| `URIOptions[]` | more options, `name=value` each, e.g. `URIOptions[]=tlsAllowInvalidCertificates=true` for the Cosmos DB emulator |

## What the profiles change

- **Bitwise aggregation operators.** The kernel filters language masks with `$bitAnd` (and the SQL translation writes
  `$bitAnd`, `$bitOr`, `$bitXor`, `$bitNot`). MongoDB has them only from 6.3; FerretDB, DocumentDB and Cosmos DB not
  at all. Where the server lacks them, the driver writes them as arithmetic (`$mod`, `$floor`, `$divide`) whenever one
  side is a constant, which every kernel mask is. This also makes MongoDB 4.0 to 6.2 work as the wizard promises.
- **Retryable writes.** DocumentDB and Cosmos DB refuse them; the driver writes `retryWrites=false` into the URI.
- **TLS and the replica set** are written into the URI from the profile and the settings.
- **Sorting on Cosmos DB.** Cosmos DB (request units) sorts only on an indexed field: the driver indexes every column
  of each table it creates (`exp_sort_<column>`), while the collection is still empty, as Cosmos DB requires of unique
  indexes too.
- **`$lookup` with `let` and `pipeline`.** The kernel uses it in 19 places. DocumentDB and Cosmos DB (request units)
  refuse it; the driver logs a warning naming the table. This is why both are expected at the data tables level.
- **Transactions** are not used by the adapter; no profile depends on them.
- **`$bitsAnySet` on Cosmos DB.** Cosmos DB (request units) has no bitwise query operators; the driver writes
  `{ field: { $bitsAnySet: mask } }` as an `$expr` with the same arithmetic.
- **DocumentDB 8.0.** Engine 8.0 adds the bitwise aggregation operators (8.0.1), correlated subqueries and retryable
  writes (8.0.2). On such a cluster `[MongoWireSettings] Profile=mongodb` with `TLS=enabled` uses them natively.
- **FerretDB and logins.** PHP's `mongodb` extension (libmongoc) refuses the 16-byte SCRAM salt of the PostgreSQL users
  FerretDB 2 logs in ("invalid salt length of 16"). Run FerretDB without authentication (`FERRETDB_AUTH=false`) on a
  private network or a Unix socket, with the PostgreSQL login behind it.

## Installing a site

The setup wizard lists the databases right after MongoDB when PHP has the `mongodb` extension; the kickstarter takes
`ferretdb`, `documentdb`, `cosmosdb` and `cosmosdbvcore` as `Type` of `[database_choice]`.

## Tests

- `php vendor/bin/phpunit --testsuite mongowire`: the profiles, the arithmetic of the bitwise operators against PHP's
  own, the connection URI and the sort indexes, without a server.
- `mongowire-live`: against a server of `EXP_MONGOWIRE_URI` with `EXP_MONGOWIRE_PROFILE`.
