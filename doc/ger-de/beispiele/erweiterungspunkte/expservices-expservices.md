---
title: Erweiterungspunkte in expservices.ini
audience: developer, admin
category: examples
summary: Einstellungen von expservices.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (7, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, expservices.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in expservices.ini

Einstellungen von `extension/expservices/settings/expservices.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/expservices.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## Community ForumClass

Ausgeliefert: `forum`. [Die Einstellung](../ini-einstellungen/expservices-expservices/Community.md#forumclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/expservices.ini.append.php
[Community]
ForumClass=forum    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Community ForumContainerClass

Ausgeliefert: `forums`. [Die Einstellung](../ini-einstellungen/expservices-expservices/Community.md#forumcontainerclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/expservices.ini.append.php
[Community]
ForumContainerClass=forums    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Community TopicClass

Ausgeliefert: `forum_topic`. [Die Einstellung](../ini-einstellungen/expservices-expservices/Community.md#topicclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/expservices.ini.append.php
[Community]
TopicClass=forum_topic    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Community ReplyClass

Ausgeliefert: `forum_reply`. [Die Einstellung](../ini-einstellungen/expservices-expservices/Community.md#replyclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/expservices.ini.append.php
[Community]
ReplyClass=forum_reply    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Community CommentClass

Ausgeliefert: `comment`. [Die Einstellung](../ini-einstellungen/expservices-expservices/Community.md#commentclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/expservices.ini.append.php
[Community]
CommentClass=comment    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Community PollClass

Ausgeliefert: `poll`. [Die Einstellung](../ini-einstellungen/expservices-expservices/Community.md#pollclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/expservices.ini.append.php
[Community]
PollClass=poll    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Community ReviewClass

Ausgeliefert: `review`. [Die Einstellung](../ini-einstellungen/expservices-expservices/Community.md#reviewclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/expservices.ini.append.php
[Community]
ReviewClass=review    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../../../eng-us/examples/cookbook/extension-skeleton.md)
- [Die Blöcke von expservices.ini](../ini-einstellungen/expservices-expservices/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/expservices/settings/expservices.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
