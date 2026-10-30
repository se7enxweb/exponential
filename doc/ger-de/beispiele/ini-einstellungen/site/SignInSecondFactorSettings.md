---
title: site.ini [SignInSecondFactorSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SignInSecondFactorSettings] in site.ini: Handlers, CodeHeader"
keywords: Einstellung, site.ini, SignInSecondFactorSettings, INI, setting, Handlers, CodeHeader
type: generated
generated: exp:help:examples
---
# site.ini [SignInSecondFactorSettings]

Der Block `[SignInSecondFactorSettings]` von `settings/site.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Handlers

> Der zweite Anmeldeschritt auf den Pfaden, die ein Passwort ohne das Web-Anmeldeformular prüfen:  
> die REST-Schnittstelle mit Basic-Authentifizierung, WebDAV, SOAP und die expservices-Anmeldung.  
> Eine Erweiterung für die zweistufige Anmeldung (sevenx\_authentication\_2fa) fügt hier ihre Klasse hinzu; sie  
> entscheidet dann, ob ein Passwort allein für ein Konto genügt, und welche App-Passwörter  
> (vom Benutzer auf der Setup-Seite der Erweiterung erzeugt) stattdessen zur Anmeldung dienen. Ohne eine Klasse  
> ändert sich nichts. Konsolenbefehle werden nie gefragt.  
> Anleitung: doc/eng-us/features/6.0/second-factor-on-other-sign-in-paths.md  
>
> Klassen, die expSignInSecondFactorHandler implementieren (die Einstellungen der Erweiterung fügen ihre hinzu).  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SignInSecondFactorSettings]
# ein leeres "Handlers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Handlers[]
*/ ?>
```

## CodeHeader

> Der Request-Header, der den Code einer Authenticator-App trägt, wo die Erweiterung  
> einen akzeptiert (die expservices-Anmeldung liest auch das POST-Feld otp).  

Standard: `X-Exp-OTP`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SignInSecondFactorSettings]
CodeHeader=X-Exp-OTP
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
