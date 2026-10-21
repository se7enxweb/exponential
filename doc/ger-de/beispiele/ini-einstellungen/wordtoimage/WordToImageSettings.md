---
title: wordtoimage.ini [WordToImageSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [WordToImageSettings] in wordtoimage.ini: IconRoot, ReplaceText, ReplaceIcon"
keywords: Einstellung, wordtoimage.ini, WordToImageSettings, INI, setting, IconRoot, ReplaceText, ReplaceIcon
type: generated
generated: exp:help:examples
---
# wordtoimage.ini [WordToImageSettings]

Der Block `[WordToImageSettings]` von `settings/wordtoimage.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/wordtoimage.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/wordtoimage.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/wordtoimage.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/wordtoimage.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## IconRoot

Standard: `/share/icons`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/wordtoimage.ini.append.php
[WordToImageSettings]
IconRoot=/share/icons
*/ ?>
```

## ReplaceText

Standard: eine Liste mit 17: `:)`, `:-)`, `;)`, `;-)`, `:(`, `:-(`, `:@`, `:-@`, `:|`, `:-|`, `:D`, `:-D` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/wordtoimage.ini.append.php
[WordToImageSettings]
# ein leeres "ReplaceText[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ReplaceText[]
ReplaceText[]=:)
ReplaceText[]=:-)
ReplaceText[]=;)
ReplaceText[]=;-)
ReplaceText[]=:(
ReplaceText[]=:-(
ReplaceText[]=:@
ReplaceText[]=:-@
ReplaceText[]=:|
ReplaceText[]=:-|
ReplaceText[]=:D
ReplaceText[]=:-D
ReplaceText[]=:O
ReplaceText[]=:o
ReplaceText[]=:-O
ReplaceText[]=:-o
ReplaceText[]=:-/
*/ ?>
```

## ReplaceIcon

Standard: eine Liste mit 17: `happy.gif`, `happy.gif`, `blunk.gif`, `blunk.gif`, `sad.gif`, `sad.gif`, `mad.gif`, `mad.gif`, `confused.gif`, `confused.gif`, `big-smile.gif`, `big-smile.gif` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/wordtoimage.ini.append.php
[WordToImageSettings]
# ein leeres "ReplaceIcon[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ReplaceIcon[]
ReplaceIcon[]=happy.gif
ReplaceIcon[]=happy.gif
ReplaceIcon[]=blunk.gif
ReplaceIcon[]=blunk.gif
ReplaceIcon[]=sad.gif
ReplaceIcon[]=sad.gif
ReplaceIcon[]=mad.gif
ReplaceIcon[]=mad.gif
ReplaceIcon[]=confused.gif
ReplaceIcon[]=confused.gif
ReplaceIcon[]=big-smile.gif
ReplaceIcon[]=big-smile.gif
ReplaceIcon[]=omg.gif
ReplaceIcon[]=omg.gif
ReplaceIcon[]=omg.gif
ReplaceIcon[]=omg.gif
ReplaceIcon[]=suspicious.gif
*/ ?>
```

## Siehe auch

- [Alle Blöcke von wordtoimage.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/wordtoimage.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
