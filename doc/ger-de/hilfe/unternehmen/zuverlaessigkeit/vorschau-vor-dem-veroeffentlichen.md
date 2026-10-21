---
title: Vorschau vor dem Veröffentlichen
audience: businesses, editor, project-manager, admin
category: business-solutions
order: 190
summary: Wie Entwürfe, Versionen, Vorschau, Freigabe-Workflows und der Papierkorb redaktionelle Fehler von Kunden fernhalten und wie ein Unternehmen Regeln dafür festlegt, wer was veröffentlicht, damit Änderungen an Inhalten so sicher sind wie Änderungen am Code.
keywords: Vorschau, Entwurf, Versionen, Freigabe, Workflow, wartend, veröffentlichen, zurückrollen, Versionen verwalten, Versionen vergleichen, Papierkorb, Versionsverlauf, redaktionelle Sicherheit, preview, draft, versions, approval, workflow, pending, publish, rollback, manage versions, compare versions, trash, version history, editorial safety
type: help
---
# Vorschau vor dem Veröffentlichen

Diese Seite richtet sich an Führungskräfte, die für das verantwortlich sind, was Kunden auf der Website sehen, und an
Redakteure und Administratoren, die die Regeln einrichten. Bei Zuverlässigkeit geht es nicht nur um Server: Ein falscher
Preis, eine defekte Landingpage oder eine zu früh veröffentlichte Seite lassen Kunden ebenfalls im Stich. Das
Inhaltsmodell von Exponential wurde so gebaut, dass das schwer passiert. Diese Seite erklärt, wie, und welche Regeln ein
Unternehmen festlegen sollte. Die Anleitung für Redakteure ist
[Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md).

## Inhalt

1. [Inhaltliche Fehler sind auch Ausfälle](#inhaltliche-fehler-sind-auch-ausfälle)
2. [Entwürfe: Arbeit, die Kunden nicht sehen](#entwürfe-arbeit-die-kunden-nicht-sehen)
3. [Vorschau](#vorschau)
4. [Freigabe vor dem Veröffentlichen](#freigabe-vor-dem-veröffentlichen)
5. [Zurückgehen](#zurückgehen)
6. [Große Änderungen im Hintergrund](#große-änderungen-im-hintergrund)
7. [Regeln für das Unternehmen](#regeln-für-das-unternehmen)
8. [Checkliste](#checkliste)

## Inhaltliche Fehler sind auch Ausfälle

Für einen Kunden ist eine Bestellseite mit falschem Preis oder eine Kampagnenseite mit defekten Links genauso schlimm wie
ein Serverfehler. Solche Fehler fallen oft schwerer auf, weil das Monitoring grün bleibt. Der Schutz ist ein
Veröffentlichungsprozess, in dem nichts zu Kunden gelangt, bevor jemand es angesehen hat, und in dem sich jede Änderung
rückgängig machen lässt.

## Entwürfe: Arbeit, die Kunden nicht sehen

„Jedes Speichern eines Elements ist eine **Version**. Wenn Sie auf **Bearbeiten** klicken, entsteht eine neue Version
als **Entwurf**: Nur Sie sehen ihn, und die Website zeigt weiterhin die veröffentlichte Version, bis Sie veröffentlichen“
([Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md#entwürfe-und-versionen)).

| Zustand | Was er bedeutet |
|---|---|
| **Entwurf** | Wird geschrieben; nur sein Ersteller sieht und bearbeitet ihn |
| **Wartend** | Zur Veröffentlichung geschickt, aber ein Workflow hält ihn an, meist eine Freigabe |
| **Veröffentlicht** | Die Version, die die Website zeigt; immer nur eine |
| **Archiviert** | Eine frühere veröffentlichte Version, aufbewahrt, damit Sie zurückgehen können |

Der Entwurf wird alle paar Minuten automatisch gespeichert, während ein Redakteur arbeitet, „sodass ein geschlossener
Browser-Tab wenig kostet“ ([Inhalte erstellen und bearbeiten](../../inhalte-bearbeiten.md)). **Entwurf speichern und
beenden** hebt einen Entwurf für später auf; **Meine Entwürfe** im Dashboard listet die Entwürfe auf, die ein Redakteur
noch nicht veröffentlicht hat ([Meine Entwürfe und meine ausstehenden Elemente](../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)).

## Vorschau

Die linke Spalte des Editors bietet **Vorschau**, um „den Entwurf so zu sehen, wie er aussehen wird“
([Inhalte erstellen und bearbeiten](../../inhalte-bearbeiten.md)). Die Vorschau zeigt die Version im Design der Website und
lässt Sie Übersetzung, Platzierung und Siteaccess wählen, sodass eine mehrsprachige Website oder eine Website mit
mehreren Marken jede Variante prüfen kann.

Wo ein Unternehmen einen Entwurf auch so sehen muss, wie er gedruckt oder exportiert wird (zum Beispiel ein PDF-Layout
eines Rundschreibens), kann die Vorschau weitere View-Modi anbieten, die in `content.ini [VersionView] ViewModes[]`
aufgeführt sind ([Versionsvorschau in anderen View-Modi](../../../../eng-us/features/6.0/version-preview-view-modes.md)). Um eine
Version zu sehen, braucht es die Richtlinie `content/versionread`.

## Freigabe vor dem Veröffentlichen

Richten Sie für Inhalte, die eine zweite Person prüfen muss (Preise, Rechtstexte, Ankündigungen), einen
Freigabe-Workflow ein
([Workflows, durchgerechnetes Beispiel: Freigabe vor dem Veröffentlichen](../../../anleitungen/workflows.md#3-durchgerechnetes-beispiel-freigabe-vor-dem-veröffentlichen)):

- Ein **Trigger** verbindet einen Workflow mit einer Operation: „Vor dem Veröffentlichen von Inhalten den
  Freigabe-Workflow ausführen.“
- Wenn ein Redakteur einen Entwurf zur Veröffentlichung schickt, wird die Version **wartend**, bis ein Freigebender
  entscheidet.
- Der Freigebende sieht die Anfrage mit einer Vorschau und gibt sie frei oder lehnt sie ab.
- Der **Workflow-Cronjob** (`workflow.php`, standardmäßig im Teil `frequent`) veröffentlicht dann die freigegebene
  Version. „Ohne diesen Cronjob werden freigegebene Inhalte nie veröffentlicht“
  ([Workflows, kurz gesagt](../../../anleitungen/workflows.md#kurz-gesagt)).

**Setup > Workflow-Prozesse** zeigt jeden wartenden Prozess in Worten, und eine Statusleiste sagt, ob der
Workflow-Cronjob eingeplant ist. Das Abbrechen eines Prozesses macht aus einer wartenden Version wieder einen Entwurf;
„Es werden keine Inhalte gelöscht“ (dieselbe Quelle).

> **Tipp**
> Die Freigabe muss nicht die ganze Website abdecken. Nutzen Sie sie für die Sektionen, in denen ein Fehler am meisten
> kostet, und lassen Sie vertrauenswürdige Redakteure anderswo direkt veröffentlichen. Sektionen und Rollen entscheiden,
> wer durch welchen Workflow geht; siehe
> [Abteilungen, Rollen und Sektionen](../intranets/abteilungen-rollen-und-sektionen.md).

## Zurückgehen

Jede veröffentlichte Änderung lässt sich ohne Wiederherstellung aus einem Backup rückgängig machen
([Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md#zu-einer-früheren-version-zurückkehren)):

1. Klicken Sie beim Element auf **Versionen verwalten**.
2. **Vergleichen** Sie zwei Versionen, um die Unterschiede Feld für Feld zu sehen.
3. Wählen Sie die frühere Version und klicken Sie auf **Neuen Entwurf machen**; prüfen Sie ihn und veröffentlichen Sie
   ihn wie gewohnt.

Entfernte Elemente landen im **Papierkorb** und lassen sich von dort wiederherstellen
([Veröffentlichen, Versionen und der Papierkorb, der Papierkorb](../../veroeffentlichen-und-versionen.md#der-papierkorb)). Die
vollständige Anleitung zur Versionsseite ist
[Versionen eines Objekts: vergleichen, wiederherstellen, aufräumen](../../../anleitungen/inhaltsverlauf.md).

Wie viele Versionen aufbewahrt werden, ist eine Einstellung: `content.ini [VersionManagement] DefaultVersionHistoryLimit`,
ab Werk 45 pro Element; wird die Grenze erreicht, wird die älteste archivierte Version entfernt, um Platz zu schaffen
(`settings/content.ini`; [Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md#entwürfe-und-versionen)).
Muss Ihr Unternehmen für bestimmte Inhalte einen längeren Verlauf aufbewahren, bitten Sie Ihren Administrator, die
Grenze für diese Klassen anzuheben.

Das Prüfprotokoll zeichnet auf, wer was veröffentlicht, verschoben, entfernt oder wiederhergestellt hat, sodass „Wer hat
das geändert?“ immer eine Antwort hat ([Prüfprotokoll für den Betrieb](audit-trail-fuer-den-betrieb.md)).

## Große Änderungen im Hintergrund

Das Entfernen, Kopieren oder Verschieben eines großen Teilbaums lief früher innerhalb einer einzigen Webanfrage und
konnte an Zeit- oder Speicherlimits scheitern. Ab 6.0.15 bietet die Administration an, es als **Inhaltsjob** auszuführen,
der in Stapeln arbeitet, nach jedem Stapel einen Checkpoint setzt und dort weitermacht, wo er aufgehört hat. Die
Bestätigungsseite zeigt vor dem Start, was die Operation betrifft, und schlägt ab 50 Knoten den Hintergrund vor
([Inhaltsjobs](../../../../eng-us/features/6.0/content-jobs.md)). Siehe
[Cronjobs und Hintergrundjobs](cronjobs-und-hintergrundjobs.md).

## Regeln für das Unternehmen

Halten Sie schriftlich fest und teilen Sie:

- **Wer wo veröffentlichen darf**: welche Rollen direkt veröffentlichen und welche durch eine Freigabe gehen.
- **Was in der Vorschau geprüft werden muss**: mindestens Landingpages, Preise, Rechtstexte und alles, worauf ein
  Newsletter oder eine Kampagne verlinkt.
- **Wer die zweite Sprache oder Marke prüft**, auf einer mehrsprachigen Website oder einer Website mit mehreren Marken.
- **Wann große Änderungen stattfinden**: umfangreiche Entfernungen, Verschiebungen und Importe außerhalb der
  Spitzenzeiten, am besten als Inhaltsjobs.
- **Wie Fehler gemeldet und rückgängig gemacht werden**: Redakteure kennen **Versionen verwalten** und den Papierkorb.
- **Vor einer Spitze**: Kampagneninhalte sind Tage vorher veröffentlicht und geprüft
  ([Lastspitzen planen](lastspitzen-planen.md)).

## Checkliste

- [ ] Redakteure kennen den Unterschied zwischen Entwurf, wartend und veröffentlicht.
- [ ] Wichtige Inhalte werden in jeder Sprache und jedem Siteaccess, in dem sie erscheinen, in der Vorschau geprüft.
- [ ] Sektionen, in denen Fehler teuer sind, gehen durch einen Freigabe-Workflow, und der Workflow-Cronjob läuft.
- [ ] Redakteure wissen, wie sie aus einer früheren Version einen neuen Entwurf machen und wie sie den Papierkorb nutzen.
- [ ] Die Grenze für den Versionsverlauf passt zu den Anforderungen Ihres Unternehmens.
- [ ] Umfangreiche Entfernungen und Verschiebungen laufen als Inhaltsjobs, außerhalb der Spitzenzeiten.

## Verwandte Seiten

- [Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md)
- [Freigabe-Workflows](../intranets/freigabe-workflows.md)
- [Workflows](../../../anleitungen/workflows.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
