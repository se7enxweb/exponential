---
title: Entwürfe, Versionen und Rückgängigmachen
audience: customer, editor
category: customer-success
order: 13
summary: Was ein Website-Betreiber über Entwürfe und Versionen in Exponential wissen sollte - wie viele Versionen aufbewahrt werden, wie man einen veröffentlichten Fehler rückgängig macht, was sich nicht rückgängig machen lässt, und ein kleines monatliches Aufräumen alter Entwürfe - mit Verweisen auf die Seiten für Redakteure mit den Klicks.
keywords: Entwürfe, Versionen, rückgängig machen, wiederherstellen, Versionshistorie, Versionen vergleichen, archiviert, alte Entwürfe, Fehler, zurücksetzen, drafts, versions, undo, restore, version history, compare versions, archived, old drafts, mistakes, rollback
type: help
---
# Entwürfe, Versionen und Rückgängigmachen

Diese Seite ist für den Betreiber und die Chefredaktion, die wissen wollen, wie sicher die tägliche Arbeit des Teams
ist. In Exponential ist jedes Speichern eine **Version**; die Website zeigt die veröffentlichte, während ein neuer
**Entwurf** geschrieben wird, und ältere veröffentlichte Versionen werden als **archiviert** aufbewahrt, sodass Sie
zurückkehren können ([Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md)). Dadurch lassen
sich die meisten Fehler mit wenig Aufwand rückgängig machen.

## Inhalt

1. [Was aufbewahrt wird](#was-aufbewahrt-wird)
2. [Einen veröffentlichten Fehler rückgängig machen](#einen-veröffentlichten-fehler-rückgängig-machen)
3. [Was sich nicht rückgängig machen lässt](#was-sich-nicht-rückgängig-machen-lässt)
4. [Alte Entwürfe](#alte-entwürfe)
5. [Regeln für das Team](#regeln-für-das-team)

## Was aufbewahrt wird

| Was | Wie lange aufbewahrt | Quelle |
|---|---|---|
| Versionen eines Elements | bis zu einer Grenze pro Element; danach wird die älteste archivierte Version entfernt, um Platz zu schaffen. Der ausgelieferte Standard ist 45 (`[VersionManagement] DefaultVersionHistoryLimit=45`), eine eigene Grenze pro Klasse ist möglich | `settings/content.ini` |
| Die veröffentlichte Version | immer; sie lässt sich auf der Versionsseite nie entfernen | [Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md) |
| Entwürfe | bis sie veröffentlicht, verworfen oder entfernt werden | [Meine Entwürfe und meine ausstehenden Elemente](../../../anleitungen/entwuerfe-und-ausstehende-objekte.md) |
| Entfernte Elemente | im Papierkorb, bis ihn jemand leert | [Archivieren und der Papierkorb](archivieren-und-der-papierkorb.md) |

Ihre Website verwendet möglicherweise andere Grenzen; fragen Sie Ihren Administrator nach dem Wert in Ihrer `content.ini`.

## Einen veröffentlichten Fehler rückgängig machen

1. Öffnen Sie das Element und drücken Sie **Versionen verwalten** (die runde Schaltfläche mit einer Uhr).
2. **Vergleichen** Sie die aktuelle Version mit der vorherigen: Änderungen werden Feld für Feld markiert.
3. Wählen Sie die Übersetzung der guten Version unter **Neuer Entwurf aus dieser Version, in** und drücken Sie
   **Neuen Entwurf machen**.
4. Prüfen Sie den Entwurf und veröffentlichen Sie ihn. Die aktuelle Version bleibt unverändert, bis Sie veröffentlichen.

Vollständige Schritte: [Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md#zu-einer-früheren-version-zurückkehren) und
[Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md).

> **Tipp**
> Das funktioniert auch mit der Version einer anderen Person: Der neue Entwurf gehört Ihnen. So „übernimmt“ ein
> Kollege eine Seite.

## Was sich nicht rückgängig machen lässt

| Aktion | Warum sie endgültig ist | Sicherere Alternative |
|---|---|---|
| **Papierkorb leeren**, oder Elemente aus dem Papierkorb entfernen | löscht endgültig | im Zweifel zuerst wiederherstellen |
| Die **Übersetzung** eines Elements entfernen | löscht alle seine Felder in dieser Sprache, in jeder Version ([Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)) | zuerst übersetzen, die Hauptsprache ändern, dann entfernen |
| Einen **Benutzer** entfernen | Benutzer kommen nie in den Papierkorb ([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)) | das Konto deaktivieren |
| **Formulareinsendungen** entfernen | die Sammlungen werden gelöscht; die Formulare bleiben ([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)) | zuerst als CSV exportieren |
| Änderungen an einem **Layout** nach dem Veröffentlichen | Layouts haben Entwürfe, aber nicht die Versionshistorie der Elemente; zwei Personen, die dasselbe Layout bearbeiten, überschreiben sich gegenseitig ([Landingpages mit Layouts](../../../../eng-us/help/creators/website-creators/landing-pages-with-layouts.md)) | jeweils nur eine Person pro Layout |

## Alte Entwürfe

Entwürfe sammeln sich an: begonnen und vergessen, oder von jemandem zurückgelassen, der weitergezogen ist. *Meine
Entwürfe* zeigt jeder Person ihre eigenen und bietet **Alte Entwürfe entfernen** nach 7, 30, 90 oder 365 Tagen ohne
Änderung an ([Meine Entwürfe und meine ausstehenden Elemente](../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)). Das Entfernen des
Entwurfs eines **neuen** Elements entfernt auch dieses neue Element, das nie veröffentlicht wurde; ein Entwurf eines
veröffentlichten Elements lässt die veröffentlichte Version unverändert.

Fünf Minuten im Monat:

- [ ] Jeder Redakteur öffnet *Meine Entwürfe*, filtert nach einem Alter von 30 Tagen, stellt fertig oder entfernt.
- [ ] Die Chefredaktion bittet Mitarbeitende, die gehen, das vor ihrem letzten Tag zu tun ([Ausscheiden von Mitarbeitenden](ausscheiden-von-mitarbeitenden.md)).
- [ ] Ihr Administrator kann alte Entwürfe auch für die ganze Website per Cronjob aufräumen: Das Skript
      `old_drafts_cleanup.php` entfernt Entwürfe, die älter als `[VersionManagement] DraftsDuration` sind (ausgeliefert
      90 Tage), höchstens `DraftsCleanUpLimit=100` pro Lauf (`settings/content.ini`). Es ist nicht im ausgelieferten
      globalen Teil enthalten; fragen Sie, ob Ihre Website es einplant ([Cronjobs](../../../anleitungen/cronjobs.md)).

## Regeln für das Team

- Bei einer Unterbrechung als Entwurf speichern (**Entwurf speichern und beenden**), das Formular nicht offen lassen.
- Jeweils nur eine Person bearbeitet ein Element; das Bearbeitungsformular warnt, wenn jemand anderes gerade bearbeitet
  ([Inhalte anlegen und bearbeiten](../../inhalte-bearbeiten.md)).
- Notieren Sie vor einer großen Änderung die aktuelle Versionsnummer. Der Weg zurück ist dann ein einziger Schritt.

## Verwandte Seiten

- [Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md)
- [Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md)
- [Archivieren und der Papierkorb](archivieren-und-der-papierkorb.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
