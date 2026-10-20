---
title: template.ini [WashSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [WashSettings] in template.ini: EmailDotText, EmailAtText"
keywords: Einstellung, template.ini, WashSettings, INI, setting, EmailDotText, EmailAtText
type: generated
generated: exp:help:examples
---
# template.ini [WashSettings]

Der Block `[WashSettings]` von `settings/template.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/template.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/template.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/template.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/template.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## EmailDotText

> Replacements strings for the . and @ in an email  
> Used to display emails in a way that SPAM engines  
> cannot fetch automatically  

Standard: `.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/template.ini.append.php
[WashSettings]
EmailDotText=.
*/ ?>
```

## EmailAtText

Standard: `<span class="spamfilter">SPAMFILTER</span>@`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/template.ini.append.php
[WashSettings]
EmailAtText=<span class="spamfilter">SPAMFILTER</span>@
*/ ?>
```

## Siehe auch

- [Alle Blöcke von template.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/template.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
