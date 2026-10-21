---
title: Aufbewahrungssperren
audience: government, admin, project-manager
category: public-sector
order: 230
summary: Für öffentliche Stellen, die Website-Inhalte und Systemaufzeichnungen während eines Rechtsstreits, einer Untersuchung oder eines Antrags auf Informationszugang erhalten müssen - Exponential hat keine Funktion für Aufbewahrungssperren, daher beschreibt diese Seite einen Ablauf aus Objektzuständen, Rollen, ausgesetzten Aufräumaufträgen, verlängerter Aufbewahrung des Audits und Exporten.
keywords: Aufbewahrungssperre, Legal Hold, Litigation Hold, Sicherung, Untersuchung, Antrag auf Informationszugang, Objektzustände, Rollen, StateGroup, Papierkorb leeren, unpublish, hide, Aufräumen von Versionen, Aufbewahrung des Audits, Export, Schriftgut, öffentlicher Sektor, legal hold, litigation hold, preservation, investigation, freedom of information request, object states, roles, trash purge, version cleanup, audit retention, records, public sector
type: help
---
# Aufbewahrungssperren

Diese Seite richtet sich an Zuständige für Schriftgut, Administratoren und Mitarbeitende der Rechtsabteilung. Eine
Aufbewahrungssperre (englisch „legal hold“) setzt die normale Löschung von Informationen aus, die für einen Fall
relevant sind. **Exponential hat keine Funktion für Aufbewahrungssperren.** Eine Sperre lässt sich mit vorhandenen
Werkzeugen umsetzen: die Inhalte kennzeichnen, die Personen und Aufträge stoppen, die sie ändern oder entfernen
könnten, die Aufbewahrung von Systemaufzeichnungen verlängern und eine Kopie exportieren.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Was Informationen vernichten kann](#was-informationen-vernichten-kann)
2. [Eine Sperre, Schritt für Schritt](#eine-sperre-schritt-für-schritt)
3. [Die Sperre aufheben](#die-sperre-aufheben)
4. [Verwandte Seiten](#verwandte-seiten)

## Was Informationen vernichten kann

| Mechanismus | Maßnahme der Sperre |
|---|---|
| Redaktionen, die bearbeiten oder entfernen | Objektzustand `hold/Legal hold` und Rollen, die in diesem Zustand weder bearbeiten noch entfernen können |
| Grenze der Versionsgeschichte entfernt alte Versionen | die Versionen jetzt exportieren; die Grenze der Klasse anheben |
| `unpublish.php` verschiebt Einträge an einem Datum in den Papierkorb | das Datum leeren oder den Teilbaum aus `[UnpublishSettings] RootNodeList[]` entfernen |
| `hide.php` versteckt an einem Datum | meist unschädlich (Verstecken behält die Inhalte); das Datum leeren, wenn die Sichtbarkeit zählt |
| `trashpurge` leert den Papierkorb | den Teil aussetzen oder den Papierkorb zuerst auf gesperrte Einträge prüfen |
| Aufräumen alter Entwürfe | `old_drafts_cleanup.php` aussetzen, wenn Entwürfe zählen |
| Aufbewahrung des Audits bereinigt Einträge | `ArchiveDays` für die betroffenen Kanäle anheben |
| Bereinigung von Protokollen | `MaxAgeDays` für die betroffenen Protokolle anheben oder sie kopieren |
| Abläufe zum Entfernen von Formulareinsendungen | für die betroffenen Formulare anhalten |

## Eine Sperre, Schritt für Schritt

1. **Umfang**: die Inhalte (Teilbäume, Klassen, Benutzer), der Zeitraum, die Systemaufzeichnungen.
2. **Kennzeichnen** Sie die Inhalte: eine Zustandsgruppe `hold` (Keine, Aufbewahrungssperre); setzen Sie
   Aufbewahrungssperre auf den Teilbäumen mit „Also for everything below this node“ (auch für alles unterhalb dieses
   Knotens) ([Objektzustände](../../../anleitungen/objektzustaende.md)).
3. **Beschränken** Sie: `content/edit` und `content/remove` der Redaktion auf `StateGroup_hold = None`; `state/assign`
   für die Sperrgruppe nur für die Schriftgutverwaltung.
4. **Setzen Sie die oben genannten Aufträge aus**, in `settings/override/` (Cronjob-Teile, content.ini, audit.ini,
   logviewer.ini); halten Sie jede Änderung fest (das Schreiben von Einstellungen steht im Audit als
   `system.setting.write`).
5. **Exportieren Sie eine Kopie**: Inhalte (REST-API, Pakete oder PDF), die benötigten Versionen, Audit-Einträge als
   signiertes Bündel (`./console exp:audit export ... --format=bundle`) und die Ausgabe der Prüfung. Bewahren Sie sie
   mit Vermerken zur Beweiskette auf.
6. **Informieren** Sie die betroffenen Personen; dokumentieren Sie die Sperre (Umfang, Datum, Maßnahmen,
   Verantwortliche).

## Die Sperre aufheben

Machen Sie die Maßnahmen in umgekehrter Reihenfolge rückgängig, setzen Sie den Zustand zurück auf Keine und halten Sie
das Datum fest. Danach gilt wieder die normale Aufbewahrung; Einträge, deren Aufbewahrung während der Sperre endete,
können sofort zur Aussonderung anstehen.

## Verwandte Seiten

- [Objektzustände für Schriftgut](objektzustaende-fuer-schriftgut.md)
- [Audit-Einträge als Nachweis](audit-eintraege-als-nachweis.md)
- [Aufbewahrungspläne für Website-Inhalte](aufbewahrungsplaene.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
