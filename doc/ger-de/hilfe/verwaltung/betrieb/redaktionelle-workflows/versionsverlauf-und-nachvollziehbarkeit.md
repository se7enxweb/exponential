---
title: Versionsgeschichte und Rechenschaft
audience: government, editor, admin
category: public-sector
order: 140
summary: Was die Versionsgeschichte einer Seite und das Prüfprotokoll einer öffentlichen Stelle darüber sagen, wer was wann geändert hat, wie viele Versionen aufbewahrt werden (standardmäßig 45) und wie Sie frühere Versionen länger aufbewahren, wenn Regeln zum Schriftgut es verlangen.
keywords: Versionsgeschichte, Versionen, Rechenschaft, wer hat was geändert, Versionen vergleichen, wiederherstellen, archivierte Versionen, DefaultVersionHistoryLimit, Schriftgut, Prüfprotokoll, Beleg, version history, versions, accountability, who changed what, compare versions, restore, archived versions, records, audit trail, evidence
type: help
---
# Versionsgeschichte und Rechenschaft

Diese Seite richtet sich an Redaktionen, Webverantwortliche und Schriftgutverantwortliche, die zeigen müssen, was eine öffentliche Seite zu einem
bestimmten Zeitpunkt aussagte und wer sie geändert hat. Zwei Quellen beantworten das: die **Versionsseite** jedes Objekts und das **Prüfprotokoll**.
Quellen: [Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md), `settings/content.ini [VersionManagement]` und
[Prüfprotokoll](../../../../../eng-us/features/6.0/audit-trail.md).

## Inhalt
1. [Die Versionsseite](#die-versionsseite)
2. [Wie viele Versionen aufbewahrt werden](#wie-viele-versionen-aufbewahrt-werden)
3. [Das Prüfprotokoll](#das-prüfprotokoll)
4. [Typische Fragen beantworten](#typische-fragen-beantworten)
5. [Grenzen](#grenzen)
6. [Verwandte Seiten](#verwandte-seiten)

## Die Versionsseite

`/content/history/<object id>`, geöffnet mit **Versionen verwalten** im Editor oder über das Kontextmenü:

- Jedes Speichern ist eine Version: Entwurf, veröffentlicht, archiviert (die vor der aktuellen), ausstehend oder abgelehnt.
- **Vergleichen** zeigt die Unterschiede zwischen zwei Versionen (mit `content/diff`).
- **Neuen Entwurf machen** aus einer alten Version stellt alten Text wieder her: Der Entwurf gehört Ihnen und wird wie üblich veröffentlicht (und
  durchläuft erneut den Freigabe-Workflow).
- **Ausgewählte Versionen entfernen** entfernt Entwürfe und archivierte oder abgelehnte Versionen endgültig (`content/versionremove`); die
  veröffentlichte Version und Versionen in einem laufenden Workflow werden nie entfernt.
- Wer das Objekt bearbeiten darf, öffnet seine Versionen; der Entwurf oder die ausstehende Version einer anderen Person braucht
  `content/versionread` ([Wer was sieht](../../../../anleitungen/inhaltsverlauf.md#6-wer-was-sieht)).

## Wie viele Versionen aufbewahrt werden

`settings/content.ini`:

```ini
[VersionManagement]
DefaultVersionHistoryLimit=45
VersionHistoryClass[1]=75
```

Erreicht ein Objekt seine Grenze, macht die älteste archivierte Version Platz für die nächste. `VersionHistoryClass[]` setzt eine Grenze je
Klasse (nach ID oder, seit 4.1.1, nach Kennung). Wenn Ihre Regeln zum Schriftgut für bestimmte Inhalte (Verordnungen, Entscheidungen) mehr
Geschichte verlangen, erhöhen Sie die Grenze für diese Klassen in `settings/override/content.ini.append.php`, im Bewusstsein, dass die Datenbank
entsprechend wächst. Nach dem Senken einer Grenze entfernt `bin/php/cleanupversions.php` die überzähligen archivierten Versionen
(`php bin/php/cleanupversions.php --help --allow-root-user` listet seine Optionen).

## Das Prüfprotokoll

Der Kanal `content` erfasst Veröffentlichen, Verschieben, Kopieren, Entfernen, Verbergen, Sektionen, Zustände, Papierkorb und Versionen, mit
Benutzer und Uhrzeit; der Kanal `access` erfasst Anmeldungen. Die Einträge sind hashverkettet, werden standardmäßig 90 Tage live gehalten und
730 Tage archiviert (`settings/audit.ini`, Kanäle `content`, `access`, `system`, `commerce`), was Sie je Kanal ändern können
([Prüfprotokoll](../../../../../eng-us/features/6.0/audit-trail.md#what-is-recorded)).

## Typische Fragen beantworten

| Frage | Wo |
|---|---|
| Was stand am 1. März auf dieser Seite? | Versionsseite: die vor und nach diesem Datum veröffentlichte Version; öffnen Sie sie mit **Ansehen** |
| Wer hat sie geändert? | die Person, die die Version erstellt hat, auf ihrer Karte; die Ereignisse `content.*` des Prüfprotokolls für das Objekt |
| Wer hat diese Seite entfernt? | Prüfprotokoll: Das Entfernen eines Teilbaums ist ein übergeordnetes Ereignis mit einem untergeordneten je Knoten |
| Wer hat sie freigegeben? | während des Laufs: die Freigabeanfrage; danach: die Aufzeichnungen Ihres Workflows und das Prüfprotokoll |
| Wann ist sie live gegangen? | das Veröffentlichungsdatum; das Audit-Ereignis der Veröffentlichung |

## Grenzen

- Die Versionsgeschichte ist kein Archiv: Versionen über der Grenze werden entfernt, und das Entfernen eines Objekts entfernt seine Versionen
  (nachdem der Papierkorb geleert wurde). Die Langzeitaufbewahrung gehört in Ihren Prozess für das Schriftgut.
- Wer eine Version freigegeben hat, wird nicht an der Version gespeichert; bewahren Sie Freigabenachweise dort auf, wo Ihre Regeln es verlangen.
- Nur Inhalte werden versioniert: Einstellungen, Templates und Rollen nicht; deren Änderungen stehen in den Kanälen `system` und `access` des
  Prüfprotokolls und in der Versionsverwaltung, wenn Einstellungen durch Bereitstellung verwaltet werden.

## Verwandte Seiten
- [Entwürfe und ausstehende Elemente](entwuerfe-und-wartende-elemente.md)
- [Vier-Augen-Prinzip beim Veröffentlichen](vier-augen-prinzip-beim-veroeffentlichen.md)
- [Rollenentwurf: Prüfer](../personen-und-berechtigungen/rollenentwurf-pruefende.md)
- [Leitfaden zu Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
