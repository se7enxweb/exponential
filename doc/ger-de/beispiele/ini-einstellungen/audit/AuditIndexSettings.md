---
title: audit.ini [AuditIndexSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditIndexSettings] in audit.ini: Index, BatchSize, IndexReads, FullText, KeepDays"
keywords: Einstellung, audit.ini, AuditIndexSettings, INI, setting, Index, BatchSize, IndexReads, FullText, KeepDays
type: generated
generated: exp:help:examples
---
# audit.ini [AuditIndexSettings]

Der Block `[AuditIndexSettings]` von `settings/audit.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Index

> F4: an index in the site's main database for the console.  enabled | disabled  
> (disabled: the console reads the files, slower, no charts).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditIndexSettings]
Index=enabled
*/ ?>
```

## BatchSize

> Rows indexed per run of the incremental indexer.  

Standard: `2000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditIndexSettings]
BatchSize=2000
*/ ?>
```

## IndexReads

> Index the read channel too.  enabled | disabled  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditIndexSettings]
IndexReads=disabled
*/ ?>
```

## FullText

> Full-text search over names, object names and before/after values (per engine; see  
> "The index").  enabled | disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditIndexSettings]
FullText=enabled
*/ ?>
```

## KeepDays

> Rows older than this are removed from the index (they stay in the archives).  
> Proposed: as long as the archives, so the console can search the whole period.  

Standard: `730`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditIndexSettings]
KeepDays=730
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
