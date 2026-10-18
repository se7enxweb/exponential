---
title: Patterns in extension/sevenx_authentication_2fa
audience: developer
category: standards
order: 468
summary: Every class, view, fetch function, operator and plugin point of extension/sevenx_authentication_2fa with its design patterns.
keywords: extension/sevenx_authentication_2fa, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/sevenx_authentication_2fa

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/sevenx_authentication_2fa` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (19)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `eZFacebookUser` | class extends eZOAuthUser | [Active Record (eZPersistentObject)](active-record.md) | `extension/sevenx_authentication_2fa/login_handler/ezfacebookuser.php:17` | – |
| `eZGoogleUser` | class extends eZOAuthUser | [Active Record (eZPersistentObject)](active-record.md) | `extension/sevenx_authentication_2fa/login_handler/ezgoogleuser.php:17` | – |
| `eZIdmeUser` | class extends eZOAuthUser | [Active Record (eZPersistentObject)](active-record.md) | `extension/sevenx_authentication_2fa/login_handler/ezidmeuser.php:22` | – |
| `eZInstagramUser` | class extends eZOAuthUser | [Active Record (eZPersistentObject)](active-record.md) | `extension/sevenx_authentication_2fa/login_handler/ezinstagramuser.php:17` | – |
| `eZMetaUser` | class extends eZFacebookUser | [Active Record (eZPersistentObject)](active-record.md) | `extension/sevenx_authentication_2fa/login_handler/ezmetauser.php:17` | – |
| `eZOAuthUser` | abstract class extends eZUser | [Active Record (eZPersistentObject)](active-record.md), [Template Method / abstract base class](template-method.md) | `extension/sevenx_authentication_2fa/classes/ezoauthuser.php:26` | – |
| `eZsevenxUser2faUser` | class extends eZUser | [Active Record (eZPersistentObject)](active-record.md), [Utility (static helper)](utility.md) | `extension/sevenx_authentication_2fa/login_handler/ezsevenxuser2fauser.php:29` | – |
| `eZTwitterUser` | class extends eZOAuthUser | [Active Record (eZPersistentObject)](active-record.md) | `extension/sevenx_authentication_2fa/login_handler/eztwitteruser.php:17` | – |
| `sevenxAuthentication2fa` | class | [Factory and static factory methods](factory.md) | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2fa.php:22` | – |
| `sevenxAuthentication2faAttempts` | class | [Utility (static helper)](utility.md) | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2faattempts.php:27` | – |
| `sevenxAuthentication2faCrypto` | class | [Utility (static helper)](utility.md) | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2facrypto.php:27` | – |
| `sevenxAuthentication2faEmail` | class | [Utility (static helper)](utility.md) | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2faemail.php:21` | – |
| `sevenxAuthentication2faHelper` | class | [Singleton / instance()](singleton.md) | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2fahelper.php:23` | – |
| `sevenxAuthentication2faQR` | class | – | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2faqr.php:17` | – |
| `sevenxAuthentication2faRedirect` | class | [Utility (static helper)](utility.md) | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2faredirect.php:29` | – |
| `sevenxAuthentication2faTOTP` | class | [Utility (static helper)](utility.md) | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2fatotp.php:24` | – |
| `sevenxAuthentication2faType` | class extends eZDataType | [Datatype (Strategy for content fields)](datatype.md) | `extension/sevenx_authentication_2fa/datatypes/sevenxauthentication2fa/sevenxauthentication2fatype.php:17` | – |
| `sevenxPsyonQRCode` | class | – | `extension/sevenx_authentication_2fa/classes/thirdparty/psyonqrcode.php:29` | – |
| `sevenx_authentication_2faInfo` | class | [Value object / struct](value-object.md) | `extension/sevenx_authentication_2fa/ezinfo.php:12` | – |

## Module views (4)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/user2fa/verify` | `verify.php` | verify | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:17` |
| `/user2fa/setup` | `setup.php` | setup | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:25` |
| `/user2fa/callback` | `callback.php` | callback | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:33` |
| `/user2fa/oauth` | `oauth.php` | oauth | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:41` |

## Policy functions (4)

| Function | Limitations | Defined in |
|---|---|---|
| `user2fa/verify` | – | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:50` |
| `user2fa/setup` | – | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:51` |
| `user2fa/callback` | – | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:52` |
| `user2fa/oauth` | – | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:53` |

## Cronjob parts (1)

| Cronjob | File | Description |
|---|---|---|
| `sevenx2facleanup` | `extension/sevenx_authentication_2fa/cronjobs/sevenx2facleanup.php` | – |
