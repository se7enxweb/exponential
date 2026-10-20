---
title: mailpreferences.ini [BounceSettings]
audience: developer, admin
category: examples
summary: "The settings of [BounceSettings] in mailpreferences.ini: Reader, Protocol, Server, Port, User, Password, SSL, Folder ..."
keywords: mailpreferences.ini, BounceSettings, INI, setting, Reader, Protocol, Server, Port, User, Password, SSL, Folder, AfterRead, MaxMessages, HardStatusCodes, StatusFile, MessageListeners
type: generated
generated: exp:help:examples
---
# mailpreferences.ini [BounceSettings]

The block `[BounceSettings]` of `settings/mailpreferences.ini`, with 13 setting(s).

Never edit `settings/mailpreferences.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/mailpreferences.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/mailpreferences.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/mailpreferences.ini.append.php` in an extension (read when the extension is active).

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

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
Reader=disabled
*/ ?>
```

## Protocol

> imap or pop3  

Default: `imap`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
Protocol=imap
*/ ?>
```

## Server

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
Server=
*/ ?>
```

## Port

> Empty: 993 for IMAP and 995 for POP3 with SSL, 143 and 110 without.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
Port=
*/ ?>
```

## User

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
User=
*/ ?>
```

## Password

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
Password=
*/ ?>
```

## SSL

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
SSL=enabled
*/ ?>
```

## Folder

> IMAP only: the folder that is read.  

Default: `INBOX`.

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

Default: `keep`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
AfterRead=keep
*/ ?>
```

## MaxMessages

> At most this many messages per run.  

Default: `500`.

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

Default: a list of 3: `5.1.`, `5.2.1`, `5.4.4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
# an empty "HardStatusCodes[]" first replaces the shipped list; without it the items are added
HardStatusCodes[]
HardStatusCodes[]=5.1.
HardStatusCodes[]=5.2.1
HardStatusCodes[]=5.4.4
*/ ?>
```

## StatusFile

> The last run (time, counts, error) for the status page and exp:mail:status,  
> relative to the var directory.  

Default: `log/mailbounces.json`.

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

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[BounceSettings]
# an empty "MessageListeners[]" first replaces the shipped list; without it the items are added
MessageListeners[]
*/ ?>
```

## Related

- [All blocks of mailpreferences.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/mailpreferences.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
