---
title: site.ini [UserSessionSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [UserSessionSettings] in site.ini: Tracking, TouchInterval, RecordIP, GeoIPDatabase, GeoIPLanguage, RetentionDays, NewDeviceMail, NewDeviceAdminMail ..."
keywords: Einstellung, site.ini, UserSessionSettings, INI, setting, Tracking, TouchInterval, RecordIP, GeoIPDatabase, GeoIPLanguage, RetentionDays, NewDeviceMail, NewDeviceAdminMail, AdminMailReceivers, FirstSignInMail, SecurityAlertMail, FailedSignInAlertThreshold
type: generated
generated: exp:help:examples
---
# site.ini [UserSessionSettings]

Der Block `[UserSessionSettings]` von `settings/site.ini`, mit 12 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Tracking

> "Ihre Geräte": jede Anmeldung ist eine Zeile der Tabelle expuser\_session (wenn die Tabelle da ist), mit Beginn und letzter  
> Nutzung, der Adresse, dem Browser und System, der Anmeldemethode und Angemeldet bleiben. Der Benutzer sieht  
> sie unter user/sessions und meldet eine Sitzung oder alle anderen ab; ein Administrator mit der Richtlinie user/sessions  
> tut dasselbe für jeden Benutzer (user/sessions/<user id>). Eine dort abgemeldete Sitzung endet bei ihrer nächsten Anfrage, bei jedem  
> Session-Handler. Anleitung: doc/eng-us/features/6.0/user-sessions.md  
> disabled: es werden keine Zeilen geschrieben und keine Sitzung geprüft (die Seiten sagen es).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
Tracking=enabled
*/ ?>
```

## TouchInterval

> Die letzte Nutzung und Adresse einer Sitzung werden höchstens so oft geschrieben (Sekunden), oder wenn sich die Adresse ändert.  

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
TouchInterval=60
*/ ?>
```

## RecordIP

> Wie die Adressen von Sitzungen aufbewahrt werden: full, truncated (nur das Netzwerk: 203.0.113.x, 2001:db8:85a3::/48),  
> disabled (keine) oder audit: wie audit.ini es für das Audit sagt (\[UserAuditSettings\] RecordIP=disabled: keine,  
> \[AuditPrivacySettings\] Field\[actor.ip\]=off: keine, truncate oder hash: das Netzwerk, full: die Adresse).  

Standard: `audit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
RecordIP=audit
*/ ?>
```

## GeoIPDatabase

> Der ungefähre Ort einer Anmeldung, aus einer GeoIP-Datenbank auf diesem Server im MaxMind-DB-Format (.mmdb, z. B.  
> GeoLite2-City.mmdb), relativ zur Installation oder absolut; leer: keine Orte. Braucht die PHP-Erweiterung maxminddb  
> oder das Composer-Paket maxmind-db/reader. Es wird nie ein anderer Server gefragt. Damit ist eine Anmeldung aus einem  
> Land, aus dem sich das Konto innerhalb von RetentionDays nicht angemeldet hat, ein "neuer Ort" (Ereignis und Mail).  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
GeoIPDatabase=
*/ ?>
```

## GeoIPLanguage

> Die Sprache der Ortsnamen, die die Datenbank enthält (en, wenn ein Name nicht darin steht).  

Standard: `en`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
GeoIPLanguage=en
*/ ?>
```

## RetentionDays

> Beendete Sitzungen (abgemeldet, widerrufen, abgelaufen) werden so viele Tage aufbewahrt, für die Liste der letzten Anmeldungen und um  
> ein zuvor gesehenes Gerät zu erkennen; der Cronjob usersessions.php entfernt sie danach. Sitzungen, die für \[Session\]  
> SessionTimeout nicht benutzt wurden, beendet derselbe Cronjob.  

Standard: `90`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
RetentionDays=90
*/ ?>
```

## NewDeviceMail

> "Neue Anmeldung bei Ihrem Konto" an den Benutzer, wenn eine Anmeldung von einem Browser, System und Gerätetyp kommt, die für das Konto  
> innerhalb von RetentionDays nicht gesehen wurden (Kategorie security). Jeder Benutzer kann es auf der Seite ausschalten.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
NewDeviceMail=enabled
*/ ?>
```

## NewDeviceAdminMail

> Dasselbe als Hinweis an die Administratoren: AdminMailReceivers\[\], sonst \[MailSettings\] AdminEmail (Kategorie admin).  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
NewDeviceAdminMail=disabled
*/ ?>
```

## AdminMailReceivers

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
# ein leeres "AdminMailReceivers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AdminMailReceivers[]
*/ ?>
```

## FirstSignInMail

> Ob die allererste Anmeldung, die ein Konto hier hat, als neues Gerät zählt (es gibt nichts, womit man sie vergleichen könnte).  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
FirstSignInMail=disabled
*/ ?>
```

## SecurityAlertMail

> "Sicherheitswarnung" an den Eigentümer eines Kontos, wenn die zweistufige Anmeldung ein- oder ausgeschaltet wird, ein Wiederherstellungscode benutzt wird, Anmeldungen  
> FailedSignInAlertThreshold-mal in Folge fehlschlagen (0: nie) oder das Konto gesperrt wird (Kategorie security, design:user/  
> security\_alert\_mail.tpl, verlinkt auf user/security). Ein Benutzer kann diese nicht ausschalten; disabled schaltet sie für die Site aus.  
> Gesendet aus dem Benutzer-Audit-Log (audit.ini \[UserAuditSettings\]): ohne es wird keine Warnung gesendet.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
SecurityAlertMail=enabled
*/ ?>
```

## FailedSignInAlertThreshold

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSessionSettings]
FailedSignInAlertThreshold=5
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
