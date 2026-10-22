---
title: Versionen und das Audit-Protokoll
audience: non-profit, admin, editor, government
category: non-profits
order: 50
summary: Wie eine Non-Profit-Organisation den Versionsverlauf und das manipulationssichere Audit-Protokoll von Exponential für ihre Rechenschaft nutzt - was beide aufzeichnen, wie viele Versionen aufbewahrt werden (standardmäßig 45, pro Klasse einstellbar), Versionen vergleichen und wiederherstellen, was das Audit-Protokoll aufzeichnet und schützt, wer es lesen darf (audit/read), wie man es prüft und wo beide keine vollständige Aufzeichnung mehr sind.
keywords: Versionen, Versionsverlauf, vergleichen, wiederherstellen, VersionManagement, 45 Versionen, Audit-Protokoll, manipulationssicher, Hash-Kette, audit/read, exp:audit verify, Rechenschaft, Nachweis, wer was geändert hat, versions, version history, compare, restore, audit trail, tamper-evident, hash chain, accountability, evidence, who changed what
type: help
---
# Versionen und das Audit-Protokoll

Diese Seite richtet sich an Administratoren, Vorstandsmitglieder und alle, die die Frage beantworten müssen: „Was
stand auf dieser Seite, und wer hat sie geändert?“ In Exponential beantworten zwei Funktionen diese Frage: der
**Versionsverlauf** jedes Objekts und das **Audit-Protokoll** der gesamten Installation. Zusammen machen sie die
Website einer Non-Profit-Organisation rechenschaftsfähig, ohne zusätzliches Werkzeug.

## Inhalt

1. [Zwei Aufzeichnungen, zwei Zwecke](#zwei-aufzeichnungen-zwei-zwecke)
2. [Der Versionsverlauf](#der-versionsverlauf)
3. [Wie viele Versionen aufbewahrt werden](#wie-viele-versionen-aufbewahrt-werden)
4. [Das Audit-Protokoll](#das-audit-protokoll)
5. [Wer sie lesen darf](#wer-sie-lesen-darf)
6. [Wo sie enden](#wo-sie-enden)
7. [Sie für die Rechenschaft nutzen](#sie-für-die-rechenschaft-nutzen)
8. [Checkliste](#checkliste)

## Zwei Aufzeichnungen, zwei Zwecke

| | Versionsverlauf | Audit-Protokoll |
|---|---|---|
| Zeichnet auf | Den Inhalt jeder Version eines Objekts | Ereignisse in der ganzen Installation: Veröffentlichen, Entfernen, Anmeldungen, Einstellungen, Exporte |
| Beantwortet | „Was stand an diesem Tag auf der Seite?“ | „Wer hat was wann getan?“ |
| Veränderbar? | Alte Versionen können von Berechtigten entfernt werden | Manipulationssicher: eine Hash-Kette, `verify` erkennt Änderungen |
| Einzusehen in | Der Versionsseite eines Objekts | Dem Reiter **Audit** oder `exp:audit` auf der Kommandozeile |

## Der Versionsverlauf

Jede Veröffentlichung eines Objekts ist eine Version. Auf der Versionsseite können Sie zwei Versionen vergleichen, aus
einer alten Version einen neuen Entwurf erstellen und ihn veröffentlichen sowie alte Versionen entfernen
([Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md)). Zur Rechenschaft siehe
[Der Versionsverlauf als Rechenschaft](../../verwaltung/transparenz-und-informationsfreiheit/versionsgeschichte-als-rechenschaft.md).

Verwenden Sie ihn, um:

- zu zeigen, was die Spendenseite oder eine Richtlinie an einem bestimmten Tag aussagte;
- eine versehentliche oder böswillige Änderung rückgängig zu machen;
- Korrekturen an Finanzdokumenten zu dokumentieren ([Jahresveröffentlichungen als Downloads](jahresveroeffentlichungen-als-downloads.md#korrekturen)).

## Wie viele Versionen aufbewahrt werden

Exponential bewahrt **standardmäßig 45 Versionen pro Objekt** auf, pro Klasse einstellbar; ist die Grenze erreicht,
werden die ältesten entfernt ([Grenze des Versionsverlaufs](../../verwaltung/schriftgut-und-archivierung/grenze-der-versionsgeschichte.md)).
Für Dokumente, die Sie über Jahre nachvollziehen können müssen, kann ein Administrator die Grenze für diese Klassen
anheben ([Versionen länger aufbewahren](../../verwaltung/transparenz-und-informationsfreiheit/versionen-laenger-aufbewahren.md)).

## Das Audit-Protokoll

Aus [Das Audit-Protokoll](../../../funktionen/6.0/pruefprotokoll.md):

- Es zeichnet „Anmeldungen und Ablehnungen, Inhaltsänderungen, Schreibvorgänge in Einstellungen, Cache-Leerungen,
  Deployments, Bestellungen und Änderungen der Mehrwertsteuer“ in einem manipulationssicheren Protokoll auf, das
  standardmäßig eingeschaltet ist und keine Einrichtung erfordert.
- Jeder Eintrag enthält den Hash des vorherigen Eintrags; `exp:audit verify` zeigt Ihnen, ob etwas bearbeitet oder
  entfernt wurde.
- Es achtet auf den Datenschutz: Adressen werden gekürzt, Sitzungen gehasht; Passwörter, Tokens und Geheimnisse werden
  nie aufgezeichnet.
- Kanäle: `content`, `access`, `system`, `commerce` und `read` (ausgeschaltet, sofern nicht aktiviert).
- Standardmäßige Aufbewahrung: 90 Tage aktiv und 730 Tage archiviert für die Hauptkanäle.

Einmalig auf einer neuen Installation: Stellen Sie sicher, dass die Cronjob-Gruppe `frequent` läuft, prüfen Sie, wer
Warnmails erhält, und sichern Sie `settings/override/audit.ini.append.php`, das die Signaturschlüssel enthält
(gleiche Quelle).

## Wer sie lesen darf

| Aufzeichnung | Berechtigung | Empfehlung |
|---|---|---|
| Versionen | Wer das Objekt bearbeiten darf | Für Redakteure normal |
| Audit-Protokoll | `audit/read`; auf einer neuen Installation hat nur Administrator diese Berechtigung | Eine Berichtsrolle, die auf die benötigten Kanäle beschränkt ist, lässt sich ohne Rechte zum Ändern anlegen ([Analysen und was zu messen ist](../../kunden/alltag/analyse-und-was-sie-messen-sollten.md)) |

## Wo sie enden

- Versionen jenseits der Grenze sind verloren; entfernte Versionen sind verloren.
- Das Audit-Protokoll bewahrt Einträge für den eingestellten Zeitraum auf, danach verfallen sie.
- Keines von beiden ersetzt Ihre offiziellen Unterlagen, Protokolle oder Abschlüsse.
- Änderungen, die direkt in der Datenbank oder in Dateien auf dem Server vorgenommen werden, außerhalb von
  Exponential, sind keine Inhaltsversionen.

Siehe [Wo der Verlauf endet](../../verwaltung/transparenz-und-informationsfreiheit/versionsgeschichte-als-rechenschaft.md) und
[Audit-Einträge als Nachweis](../../verwaltung/schriftgut-und-archivierung/audit-eintraege-als-nachweis.md).

## Sie für die Rechenschaft nutzen

| Situation | Verwenden |
|---|---|
| Ein Spender sagt, die Spendenseite habe etwas anderes versprochen | Versionen der Seite für dieses Datum vergleichen |
| Jemand hat den Spendenlink geändert | Kanal `content` des Audit-Protokolls, danach die frühere Version wiederherstellen |
| Ein Geldgeber fragt, was während der Förderung veröffentlicht wurde | Veröffentlichungsereignisse im Audit-Protokoll und Versionen ([Förderergebnisse für Geldgeber](../foerdermittel/foerderergebnisse-fuer-geldgeber.md)) |
| Ein Ehrenamtlicher geht, und Sie müssen wissen, was er geändert hat | Audit-Protokoll, nach Benutzer gefiltert ([Wenn Ehrenamtliche gehen](../betrieb/wenn-ehrenamtliche-gehen.md)) |
| Sie müssen nachweisen, dass Bewerbungen fristgerecht gelöscht wurden | Audit-Ereignisse `data.infocollection.remove` ([Bewerbungen von Ehrenamtlichen bearbeiten](../ehrenamtliche/bewerbungen-von-ehrenamtlichen-bearbeiten.md)) |

## Checkliste

- [ ] Die Cronjob-Gruppe `frequent` läuft (Indizierung und Rotation des Audit-Protokolls).
- [ ] Audit-Schlüssel zusammen mit den Einstellungen gesichert.
- [ ] Versionsgrenze für Klassen angehoben, die Sie über Jahre nachvollziehen müssen.
- [ ] `audit/read` nur an diejenigen vergeben, die es brauchen.
- [ ] `exp:audit verify` mindestens jährlich ausgeführt ([Jährliche Checkliste](../checklisten/jaehrliche-checkliste.md)).

## Verwandte Seiten

- [Transparenz für Non-Profit-Organisationen](README.md)
- [Das Audit-Protokoll: wer was geändert hat](../../verwaltung/transparenz-und-informationsfreiheit/der-audit-trail-fuer-wer-was-geaendert-hat.md)
- [Compliance-Nachweise aus dem Audit-Protokoll](../../unternehmen/sicherheit/compliance-nachweise-aus-dem-audit-trail.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
