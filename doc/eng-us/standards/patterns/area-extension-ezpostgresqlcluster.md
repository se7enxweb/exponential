---
title: Patterns in extension/ezpostgresqlcluster
audience: developer
category: standards
order: 455
summary: Every class, view, fetch function, operator and plugin point of extension/ezpostgresqlcluster with its design patterns.
keywords: extension/ezpostgresqlcluster, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezpostgresqlcluster

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezpostgresqlcluster` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (2)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `eZDFSFileHandlerPostgresqlBackend` | class | [Strategy / handler](strategy-handler.md) | `extension/ezpostgresqlcluster/clustering/dfs/ezpostsgresqlbackend.php:16` | This class allows DFS based clustering using PostgreSQL |
| `ezpDfsPostgresqlClusterGateway` | class extends ezpClusterGateway | – | `extension/ezpostgresqlcluster/clustering/dfs/gateway.php:13` | DFS/PostgreSQL cluster gateway |
