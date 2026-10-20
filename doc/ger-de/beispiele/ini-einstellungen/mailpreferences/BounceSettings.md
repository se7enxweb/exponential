---
title: mailpreferences.ini [BounceSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [BounceSettings] in mailpreferences.ini: Reader, Protocol, Server, Port, User, Password, SSL, Folder ..."
keywords: Einstellung, mailpreferences.ini, BounceSettings, INI, setting, Reader, Protocol, Server, Port, User, Password, SSL, Folder, AfterRead, MaxMessages, HardStatusCodes, StatusFile, MessageListeners
type: generated
generated: exp:help:examples
---
# mailpreferences.ini [BounceSettings]

Der Block `[BounceSettings]` von `settings/mailpreferences.ini`, mit 13 Einstellung(en).

Bearbeiten Sie `settings/mailpreferences.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/mailpreferences.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/mailpreferences.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/mailpreferences.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Reader

> Das Postfach, in dem Rückläufer und Spam-Beschwerden ankommen (der Return-Path  
> der Mail und die Adresse, die den Feedback-Schleifen der großen Mail-  
> Anbieter angegeben ist). Hard Bounces (Zustellungsberichte nach RFC 3464) und  
> Beschwerden (ARF-Berichte nach RFC 5965) setzen die Adresse auf die Sperrliste.  
> Gelesen vom Cronjob-Teil mailbounces (auch in der Gruppe frequent) und von  
> ./console exp:mail:bounces (--dry-run zeigt, was er täte).  
> Setzen Sie Server, Benutzer und Passwort NUR in  
> settings/override/mailpreferences.ini.append.php, die nie committet wird.  
> enabled: das Postfach lesen; disabled (der Standard): Es wird nichts gelesen.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
Reader=disabled
*/ ?>
```

## Protocol

> imap oder pop3  

Standard: `imap`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
Protocol=imap
*/ ?>
```

## Server

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
Server=
*/ ?>
```

## Port

> Leer: 993 für IMAP und 995 für POP3 mit SSL, 143 und 110 ohne.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
Port=
*/ ?>
```

## User

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
User=
*/ ?>
```

## Password

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
Password=
*/ ?>
```

## SSL

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
SSL=enabled
*/ ?>
```

## Folder

> Nur IMAP: der Ordner, der gelesen wird.  

Standard: `INBOX`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
Folder=INBOX
*/ ?>
```

## AfterRead

> keep: IMAP liest nur ungelesene Nachrichten und markiert sie als gelesen, POP3 liest jedes Mal jede  
> Nachricht (eine Adresse wird nicht zweimal hinzugefügt). delete: Die Nachrichten,  
> die verstanden wurden (Rückläufer und Beschwerden), werden nach dem Lesen gelöscht.  

Standard: `keep`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
AfterRead=keep
*/ ?>
```

## MaxMessages

> Höchstens so viele Nachrichten pro Lauf.  

Standard: `500`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
MaxMessages=500
*/ ?>
```

## HardStatusCodes

> Die Statuscodes einer fehlgeschlagenen Zustellung, die sie zum Hard Bounce machen; ein Code,  
> der auf einen Punkt endet, passt auf jeden Code, der damit beginnt. Andere Fehler und  
> Verzögerungen werden gezählt, nicht gesperrt.  
> 5.1.x: die Adresse oder die Domain existiert nicht; 5.2.1: das Postfach ist  
> deaktiviert; 5.4.4: kein Mailserver für die Domain.  

Standard: eine Liste mit 3: `5.1.`, `5.2.1`, `5.4.4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
# ein leeres "HardStatusCodes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
HardStatusCodes[]
HardStatusCodes[]=5.1.
HardStatusCodes[]=5.2.1
HardStatusCodes[]=5.4.4
*/ ?>
```

## StatusFile

> Der letzte Lauf (Zeit, Zahlen, Fehler) für die Statusseite und exp:mail:status,  
> relativ zum var-Verzeichnis.  

Standard: `log/mailbounces.json`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
StatusFile=log/mailbounces.json
*/ ?>
```

## MessageListeners

> Klassen, die jede vom Leser gelesene Nachricht erhalten, nach dessen eigener  
> Einordnung und Sperrung: static mailMessage( $raw, $classification,  
> $dryRun ), true, wenn die Nachricht für sie war (AfterRead=delete entfernt sie  
> dann). Für die eigenen Rückläufer eines anderen Systems oder Mail an dessen eigene Adressen,  
> die in diesem Postfach ankommt (cjw\_newsletter: An- und Abmelden per  
> E-Mail, erneute Versuche bei Soft Bounces). Ein Listener darf in einem Probelauf  
> nichts ändern; einer, der scheitert, wird protokolliert, und das Lesen geht weiter. Erweiterungen fügen ihre  
> Klasse in ihren eigenen Einstellungen hinzu.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
# ein leeres "MessageListeners[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
MessageListeners[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von mailpreferences.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/mailpreferences.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
