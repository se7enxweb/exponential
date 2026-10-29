---
title: Muster in kernel/classes/reauth
audience: developer
category: standards
order: 511
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/reauth mit den Entwurfsmustern.
keywords: kernel/classes/reauth, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/reauth

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/reauth` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (5)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expReauth` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/reauth/expreauth.php:37` | „Identität bestätigen“ (doc/eng-us/guides/verify-its-you.md): Vor einer heiklen Aktion bestätigt der angemeldete Benutzer erneut, wer er ist, mit seinem Passwor… |
| `expReauthCodeHandler` | interface | [Schnittstelle (Vertrag)](schnittstelle.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/reauth/expreauthcodehandler.php:14` | Eine Klasse, die den Code einer zweistufigen Anmeldemethode für „Identität bestätigen“ prüft (expReauth). |
| `expReauthRules` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/reauth/expreauthrules.php:28` | Die Regeln von „Identität bestätigen“ (site.ini [ReauthSettings], [ReauthGroup_] Rules[]) und die Entscheidung, ob eine Anfrage zu einer passt (doc/eng-us/guides/… |
| `expReauthSettings` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/reauth/expreauthsettings.php:18` | Die Einstellungen von „Identität bestätigen“, wie die Seite setup/reauth sie bearbeitet (doc/eng-us/guides/verify-its-you.md): der Schalter der gesamten Funktion, die min… |
| `expReauthTotpCode` | class implements expReauthCodeHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/reauth/expreauthtotpcode.php:19` | „Identität bestätigen“ mit dem Code einer Authenticator-App, eingerichtet in sevenx_authentication_2fa (wenn diese Extension aktiv ist): das TOTP-Geheimnis des Kontos… |
