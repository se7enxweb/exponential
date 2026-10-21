---
title: Adapter / Bridge / Wrapper
audience: developer
category: standards
order: 329
summary: "Macht eine Schnittstelle über eine andere nutzbar: Wrapper um Bibliotheken, Bridges zwischen Subsystemen. 4 Instanzen in Exponential."
keywords: Entwurfsmuster, Adapter, Struktur, Musterkatalog, design pattern, adapter, pattern catalogue
type: catalogue
---
# Adapter / Bridge / Wrapper

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Macht eine Schnittstelle über eine andere nutzbar: Wrapper um Bibliotheken, Bridges zwischen Subsystemen.

|  |  |
| --- | --- |
| Gruppe | Struktur |
| Instanzen | 4 |
| Erkannt an | Der Klassenname enthält Adapter, Bridge, Wrapper oder Proxy. |

## Klassen nach Bereich

### extension/cjw_themes_jumper (1)

[Alle Muster von extension/cjw_themes_jumper](bereich-extension-cjw-themes-jumper.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `cjwOsmTileProxy` | class | `extension/cjw_themes_jumper/classes/cjwosmtileproxy.php:13` | Die Kartenkacheln des Moduls osm_proxy: Validierung, der lokale Kachel-Cache und der Abruf vom Kachelserver. | – |

### kernel/classes/audit (1)

[Alle Muster von kernel/classes/audit](bereich-kernel-classes-audit.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expAuditBridge` | class | `kernel/classes/audit/bridge/expauditbridge.php:24` | Die ezpEvent-Brücke des Audits (doc/eng-us/bc/6.0/audit.md, „The ezpEvent bridge“, Z3): bereits vorhandene Kernel- und Extension-Ereignisse (ezpEvent::getInsta… | – |

### lib/ezsession (1)

[Alle Muster von lib/ezsession](bereich-lib-ezsession.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpSessionHandlerAdapter` | class | `lib/ezsession/classes/ezpsessionhandleradapter.php:24` | Übergibt PHP einen ezpSessionHandler als Objekt, das SessionHandlerInterface implementiert. | – |

### lib/ezutils (1)

[Alle Muster von lib/ezutils](bereich-lib-ezutils.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZTrustedProxy` | class | `lib/ezutils/classes/eztrustedproxy.php:30` | Entscheidet, welchen weitergeleiteten Anfrage-Headern man glauben kann. | – |
