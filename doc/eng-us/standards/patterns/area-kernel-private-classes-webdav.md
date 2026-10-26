---
title: Patterns in kernel/private/classes/webdav
audience: developer
category: standards
order: 539
summary: Every class, view, fetch function, operator and plugin point of kernel/private/classes/webdav with its design patterns.
keywords: kernel/private/classes/webdav, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/private/classes/webdav

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/private/classes/webdav` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (3)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expWebDAVPathFactory` | class extends ezcWebdavAutomaticPathFactory | [Factory and static factory methods](factory.md) | `kernel/private/classes/webdav/expwebdavpathfactory.php:19` | The path factory of webdav.php: paths inside the server are plain (decoded) text, URIs are encoded. |
| `eZWebDAVContentBackend` | class extends ezcWebdavSimpleBackend implements ezcWebdavLockBackend | [Strategy / handler](strategy-handler.md) | `kernel/private/classes/webdav/ezwebdavcontentbackend.php:30` | WebDAV backend for Exponential, based on eZ Components Webdav component. |
| `eZWebDAVContentBackendAuth` | class implements ezcWebdavAnonymousAuthenticator, ezcWebdavBasicAuthenticator, ezcWebdavAuthorizer, ezcWebdavLockAuthorizer | – | `kernel/private/classes/webdav/ezwebdavcontentbackendauth.php:22` | Basic authentication for WebDAV. |
