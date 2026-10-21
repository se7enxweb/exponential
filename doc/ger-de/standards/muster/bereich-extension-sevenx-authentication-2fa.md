---
title: Muster in extension/sevenx_authentication_2fa
audience: developer
category: standards
order: 468
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/sevenx_authentication_2fa mit den Entwurfsmustern.
keywords: extension/sevenx_authentication_2fa, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/sevenx_authentication_2fa

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/sevenx_authentication_2fa` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (19)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `eZFacebookUser` | class extends eZOAuthUser | [Active Record (eZPersistentObject)](active-record.md) | `extension/sevenx_authentication_2fa/login_handler/ezfacebookuser.php:17` | – |
| `eZGoogleUser` | class extends eZOAuthUser | [Active Record (eZPersistentObject)](active-record.md) | `extension/sevenx_authentication_2fa/login_handler/ezgoogleuser.php:17` | – |
| `eZIdmeUser` | class extends eZOAuthUser | [Active Record (eZPersistentObject)](active-record.md) | `extension/sevenx_authentication_2fa/login_handler/ezidmeuser.php:22` | – |
| `eZInstagramUser` | class extends eZOAuthUser | [Active Record (eZPersistentObject)](active-record.md) | `extension/sevenx_authentication_2fa/login_handler/ezinstagramuser.php:17` | – |
| `eZMetaUser` | class extends eZFacebookUser | [Active Record (eZPersistentObject)](active-record.md) | `extension/sevenx_authentication_2fa/login_handler/ezmetauser.php:17` | – |
| `eZOAuthUser` | abstract class extends eZUser | [Active Record (eZPersistentObject)](active-record.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `extension/sevenx_authentication_2fa/classes/ezoauthuser.php:26` | – |
| `eZsevenxUser2faUser` | class extends eZUser | [Active Record (eZPersistentObject)](active-record.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/sevenx_authentication_2fa/login_handler/ezsevenxuser2fauser.php:29` | – |
| `eZTwitterUser` | class extends eZOAuthUser | [Active Record (eZPersistentObject)](active-record.md) | `extension/sevenx_authentication_2fa/login_handler/eztwitteruser.php:17` | – |
| `sevenxAuthentication2fa` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2fa.php:22` | – |
| `sevenxAuthentication2faAttempts` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2faattempts.php:27` | – |
| `sevenxAuthentication2faCrypto` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2facrypto.php:27` | – |
| `sevenxAuthentication2faEmail` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2faemail.php:21` | – |
| `sevenxAuthentication2faHelper` | class | [Singleton / instance()](singleton.md) | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2fahelper.php:23` | – |
| `sevenxAuthentication2faQR` | class | – | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2faqr.php:17` | – |
| `sevenxAuthentication2faRedirect` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2faredirect.php:29` | – |
| `sevenxAuthentication2faTOTP` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2fatotp.php:24` | – |
| `sevenxAuthentication2faType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `extension/sevenx_authentication_2fa/datatypes/sevenxauthentication2fa/sevenxauthentication2fatype.php:17` | – |
| `sevenxPsyonQRCode` | class | – | `extension/sevenx_authentication_2fa/classes/thirdparty/psyonqrcode.php:29` | – |
| `sevenx_authentication_2faInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/sevenx_authentication_2fa/ezinfo.php:12` | – |

## Modulansichten (4)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/user2fa/verify` | `verify.php` | verify | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:17` |
| `/user2fa/setup` | `setup.php` | setup | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:25` |
| `/user2fa/callback` | `callback.php` | callback | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:33` |
| `/user2fa/oauth` | `oauth.php` | oauth | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:41` |

## Richtlinienfunktionen (4)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `user2fa/verify` | – | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:50` |
| `user2fa/setup` | – | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:51` |
| `user2fa/callback` | – | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:52` |
| `user2fa/oauth` | – | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:53` |

## Cronjob-Teile (1)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `sevenx2facleanup` | `extension/sevenx_authentication_2fa/cronjobs/sevenx2facleanup.php` | – |
