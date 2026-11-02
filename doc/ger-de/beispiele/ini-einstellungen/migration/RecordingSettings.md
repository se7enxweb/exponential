---
title: migration.ini [RecordingSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RecordingSettings] in migration.ini: Recording, EventList, UserKeepAttributeList, MaxEvents"
keywords: Einstellung, migration.ini, RecordingSettings, INI, setting, Recording, EventList, UserKeepAttributeList, MaxEvents
type: generated
generated: exp:help:examples
---
# migration.ini [RecordingSettings]

Der Block `[RecordingSettings]` von `settings/migration.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/migration.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/migration.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/migration.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/migration.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Recording

> Aufzeichnungen von Änderungen (./console exp:migration:record:start, Einrichtung > Migrationen > Aufzeichnung): was in  
> der Administration, der Website-Seitenleiste oder mit einem Befehl geändert wird, während eine Aufzeichnung läuft, wird als Schritte einer YAML-  
> Migration festgehalten, für ./console exp:migration:generate --from-recording=<id>. Eine Sitzungsaufzeichnung hält die eigenen  
> Änderungen eines Benutzers fest; eine websiteweite Aufzeichnung jede Änderung jedes Benutzers. Die Aufzeichnung sieht die Operationen des Kernels  
> (Inhalt veröffentlicht, ein Knoten verschoben, eine Klasse gespeichert, eine Rolle geändert, eine Einstellung geschrieben ...), keine Web-Anfragen.  
>
> disabled: nichts wird aufgezeichnet und keine Aufzeichnung kann gestartet werden.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RecordingSettings]
Recording=enabled
*/ ?>
```

## EventList

> Die Audit-Ereignisse, die eine Aufzeichnung festhält, nach Name (die Audit-Taxonomie des Kernels). Leer: jedes Ereignis, das ein Migrationsschritt  
> wiederholen kann (die Liste von expMigrationRecordingEvent::STEPS, in guides/migrations-generators-and-recording.md).  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RecordingSettings]
# ein leeres "EventList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
EventList[]
*/ ?>
```

## UserKeepAttributeList

> Die Attribute von Benutzerobjekten, die keine personenbezogenen Daten sind und aufgezeichnet werden dürfen (Bezeichner, z. B. eine "signature",  
> die eine Website für ein Teamkonto nutzt). Leer: keine. Das Konto (Login, E-Mail, Passwort) wird nie aufgezeichnet, was immer  
> hier steht: eine erzeugte Migration nennt es über eine Geheimnis-Referenz.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RecordingSettings]
# ein leeres "UserKeepAttributeList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
UserKeepAttributeList[]
*/ ?>
```

## MaxEvents

> Die meisten Ereignisse, die eine Aufzeichnung festhält; die Aufzeichnung läuft weiter, fügt aber nichts mehr hinzu und sagt das. 0: keine Grenze.  

Standard: `10000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RecordingSettings]
MaxEvents=10000
*/ ?>
```

## Siehe auch

- [Alle Blöcke von migration.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
