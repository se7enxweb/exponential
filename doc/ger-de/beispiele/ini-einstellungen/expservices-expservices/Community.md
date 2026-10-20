---
title: expservices.ini [Community]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Community] in expservices.ini (expservices): ForumClass, ForumContainerClass, TopicClass, ReplyClass, CommentClass, PollClass, ReviewClass, TopicStickyAttribute"
keywords: Einstellung, expservices.ini, Community, INI, setting, ForumClass, ForumContainerClass, TopicClass, ReplyClass, CommentClass, PollClass, ReviewClass, TopicStickyAttribute
type: generated
generated: exp:help:examples
---
# expservices.ini [Community]

Der Block `[Community]` von `extension/expservices/settings/expservices.ini` (Erweiterung expservices), mit 8 Einstellung(en).

Bearbeiten Sie `extension/expservices/settings/expservices.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/expservices.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/expservices.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/expservices.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ForumClass

> The community services (forum, topic, reply, comment, poll) work on content classes, by identifier.  
> Change them to run the services over the classes of another site.  

Standard: `forum`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expservices.ini.append.php
[Community]
ForumClass=forum
*/ ?>
```

## ForumContainerClass

Standard: `forums`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expservices.ini.append.php
[Community]
ForumContainerClass=forums
*/ ?>
```

## TopicClass

Standard: `forum_topic`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expservices.ini.append.php
[Community]
TopicClass=forum_topic
*/ ?>
```

## ReplyClass

Standard: `forum_reply`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expservices.ini.append.php
[Community]
ReplyClass=forum_reply
*/ ?>
```

## CommentClass

Standard: `comment`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expservices.ini.append.php
[Community]
CommentClass=comment
*/ ?>
```

## PollClass

Standard: `poll`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expservices.ini.append.php
[Community]
PollClass=poll
*/ ?>
```

## ReviewClass

Standard: `review`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expservices.ini.append.php
[Community]
ReviewClass=review
*/ ?>
```

## TopicStickyAttribute

> The attribute that says a topic stays on top  

Standard: `sticky`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expservices.ini.append.php
[Community]
TopicStickyAttribute=sticky
*/ ?>
```

## Siehe auch

- [Alle Blöcke von expservices.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/expservices/settings/expservices.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
