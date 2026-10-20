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

> The mailbox that bounces and spam complaints arrive in (the Return-Path of  
> the mail, and the address given to the feedback loops of the large mail  
> providers). Hard bounces (RFC 3464 delivery status notifications) and  
> complaints (RFC 5965 ARF reports) put the address on the suppression list.  
> Read by the cronjob part mailbounces (also in the frequent group) and by  
> ./console exp:mail:bounces (--dry-run shows what it would do).  
> Set the server, the user and the password ONLY in  
> settings/override/mailpreferences.ini.append.php, which is never committed.  
> enabled: read the mailbox; disabled (the default): nothing is read.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
Reader=disabled
*/ ?>
```

## Protocol

> imap or pop3  

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

> Empty: 993 for IMAP and 995 for POP3 with SSL, 143 and 110 without.  

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

> IMAP only: the folder that is read.  

Standard: `INBOX`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
Folder=INBOX
*/ ?>
```

## AfterRead

> keep: IMAP reads only unread messages and marks them read, POP3 reads every  
> message each time (an address is not added twice). delete: the messages  
> that were understood (bounces and complaints) are deleted after reading.  

Standard: `keep`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
AfterRead=keep
*/ ?>
```

## MaxMessages

> At most this many messages per run.  

Standard: `500`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
MaxMessages=500
*/ ?>
```

## HardStatusCodes

> The status codes of a failed delivery that make it a hard bounce; a code  
> ending in a dot matches every code that starts with it. Other failures and  
> delays are counted, not suppressed.  
> 5.1.x: the address or the domain does not exist; 5.2.1: the mailbox is  
> disabled; 5.4.4: no mail server for the domain.  

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

> The last run (time, counts, error) for the status page and exp:mail:status,  
> relative to the var directory.  

Standard: `log/mailbounces.json`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
StatusFile=log/mailbounces.json
*/ ?>
```

## MessageListeners

> Classes that get every message the reader reads, after the reader's own  
> classification and suppression: static mailMessage( $raw, $classification,  
> $dryRun ), true when the message was for them (AfterRead=delete then  
> removes it). For another system's own bounces, or mail to its own addresses  
> that arrives in this mailbox (cjw\_newsletter: subscribe and unsubscribe by  
> e-mail, soft-bounce retries). A listener must not change anything in a dry  
> run; one that fails is logged and the reading goes on. Extensions add their  
> class in their own settings.  

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
