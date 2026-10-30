---
title: Muster in kernel/classes/passkey
audience: developer
category: standards
order: 509
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/passkey mit den Entwurfsmustern.
keywords: kernel/classes/passkey, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/passkey

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/passkey` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (5)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expPasskey` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/passkey/exppasskey.php:22` | Ein Passkey eines Benutzers: eine Zeile von exppasskey (doc/eng-us/guides/passkeys.md). |
| `expPasskeyLogin` | class extends eZUser | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/passkey/exppasskeylogin.php:16` | Meldet ein Konto an, dessen Passkey expPasskeyService geprüft hat, über dieselbe Nachbereitung wie jede Passwort-Anmeldung (eZUser::loginSucceeded(): last… |
| `expPasskeySchema` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/passkey/exppasskeyschema.php:17` | Die Tabelle exppasskey auf jeder Engine: ob sie existiert, und ihr Anlegen aus ihrer Definition in share/db_schema.dba über den Schema-Handler der Engine… |
| `expPasskeyService` | class | [Fassade / Dienst](fassade.md) | `kernel/classes/passkey/exppasskeyservice.php:32` | Die Passkey-Abläufe der Webseiten (doc/eng-us/guides/passkeys.md): |
| `expPasskeySettings` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/passkey/exppasskeysettings.php:19` | site.ini [PasskeySettings], für eine Anfrage gelesen (doc/eng-us/guides/passkeys.md): ob Passkeys aktiv sind, die Relying Party (ID und Name), die Orig… |
