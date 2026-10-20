---
title: logviewer.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von logviewer.ini.
keywords: Beispiele, Referenz, logviewer.ini, examples, reference
type: generated
generated: exp:help:examples
---
# logviewer.ini

Die ausgelieferten Einstellungen von `settings/logviewer.ini`: 10 Blöcke, 54 Einstellungen.

> Der Log-Viewer (Setup > Logdateien, setup/logs): die Logdateien der Installation, live verfolgt,  
> durchsucht, querverwiesen und, wenn hier eingeschaltet, heruntergeladen, rotiert, geleert oder entfernt.  
> Anleitung: doc/eng-us/features/6.0/log-viewer.md  
>
> Bearbeiten Sie diese Datei nicht: Überschreiben Sie sie in settings/override/logviewer.ini.append.php oder in der  
> logviewer.ini.append.php des Administrations-Siteaccess.  
>
> Wer ihn verwenden darf, entscheiden die Richtlinien der Rollen, nicht diese Datei:  
>   setup/logs        den Viewer öffnen, die Logs verfolgen und durchsuchen, einen Bereich herunterladen (nur lesen)  
>   setup/managelogs  rotieren, leeren und archivierte Rotationen entfernen (jede Aktion auch unten eingeschaltet)  
>   setup/unmasklogs  E-Mail-Adressen, IP-Adressen, Tokens und Sitzungs-IDs unmaskiert zeigen (ein Schalter pro Viewer)  
> In einer neuen Installation hat sie keine Rolle außer Administrator (die jede Richtlinie hält).  

| Block | Einstellungen |
|---|---|
| [LogViewerSettings](LogViewerSettings.md) | 12 |
| [TailSettings](TailSettings.md) | 8 |
| [SearchSettings](SearchSettings.md) | 8 |
| [RedactionSettings](RedactionSettings.md) | 5 |
| [ActionSettings](ActionSettings.md) | 7 |
| [RateLimitSettings](RateLimitSettings.md) | 2 |
| [ViewerAuditSettings](ViewerAuditSettings.md) | 1 |
| [HealthSettings](HealthSettings.md) | 1 |
| [DigestSettings](DigestSettings.md) | 6 |
| [RotationSettings](RotationSettings.md) | 4 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/logviewer.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
