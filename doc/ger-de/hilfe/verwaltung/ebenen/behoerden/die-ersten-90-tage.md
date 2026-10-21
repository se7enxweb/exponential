---
title: Die ersten 90 Tage für Behörden
audience: government, project-manager, admin
category: public-sector
order: 60
summary: Ein Plan für 90 Tage für eine Regulierungsbehörde, Aufsichtsbehörde oder nachgeordnete Behörde, die mit Exponential beginnt - Bestandsaufnahme der Leitfäden und ihrer Verantwortlichen, Workflow der rechtlichen Prüfung, Zustände für den Status, Migration und Start - mit Prüfungen.
keywords: Projektplan, 90 Tage, Behörde, Regulierungsbehörde, Bestandsaufnahme der Leitfäden, rechtliche Prüfung, Objektzustände, Migration, Start, Prüfungen, project plan, 90 days, agency, regulator, guidance inventory, legal review, object states, migration, launch, checks
type: help
---
# Die ersten 90 Tage für Behörden

Diese Seite richtet sich an Projektverantwortliche und Administratorinnen und Administratoren, die den Start der
Website einer Behörde mit Exponential leiten. Leitfäden sind das wichtigste Gut, daher beginnt der Plan dort. Er setzt
voraus, dass eine Installation existiert ([Erste Schritte](../../../../anleitungen/erste-schritte.md)).

## Inhalt

1. [Tage 1 bis 30: Bestandsaufnahme der Leitfäden und Steuerung](#tage-1-bis-30-bestandsaufnahme-der-leitfäden-und-steuerung)
2. [Tage 31 bis 60: Modell, Prüfung und Zustände](#tage-31-bis-60-modell-prüfung-und-zustände)
3. [Tage 61 bis 90: Migration und Start](#tage-61-bis-90-migration-und-start)
4. [Risiken](#risiken)

## Tage 1 bis 30: Bestandsaufnahme der Leitfäden und Steuerung

| Schritt | Ergebnis |
|---|---|
| Jeden Leitfaden mit Verantwortlichen, Fundstelle, Geltungsdatum und Status auflisten | das Leitfadenregister (eine Tabelle) |
| Entscheiden, welche Leitfäden aufgehoben, zusammengeführt oder neu geschrieben werden | eine Entscheidung je Eintrag |
| Die Regel für die rechtliche Prüfung vereinbaren: welche Änderungen geprüft werden, wer prüft, wie schnell | die Prüfregel |
| Die Veröffentlichungsrichtlinie für Durchsetzungsentscheidungen mit Recht und Datenschutz vereinbaren | eine schriftliche Richtlinie |
| Veröffentlichte Register und Daten und ihre Quellsysteme ermitteln | eine Datenliste |

Prüfung: Jeder Leitfaden hat eine verantwortliche Person; die Prüfregel nennt Vertretungen.

## Tage 31 bis 60: Modell, Prüfung und Zustände

| Schritt | Prüfung im Produkt |
|---|---|
| Die Klassen Leitfaden, Durchsetzungsbescheid und Programm anlegen ([Inhaltsmodell](inhaltsmodell.md)) | **Setup > Klassen** listet sie auf |
| Die Zustandsgruppe `guidance_status` mit In Kraft, Ersetzt, Zurückgezogen anlegen | **Setup > Zustände**; ein Testobjekt zeigt *In Kraft* |
| Sektionen und Rollen anlegen ([Struktur](struktur-sektionen-und-rollen.md)) | eine Autorin oder ein Autor von Leitfäden kann *Zurückgezogen* nicht setzen |
| Den Workflow der rechtlichen Prüfung und seinen Trigger einrichten | eine Teständerung wartet unter **Setup > Workflow-Prozesse** |
| Die Grenze des Versionsverlaufs anheben, wenn jede Version eines Leitfadens erhalten bleiben muss | `[VersionManagement]` in einem Override; INI-Cache geleert |
| `hide.php` für Durchsetzungsbescheide mit einem Ende der Veröffentlichung einrichten | ein Testbescheid mit einem vergangenen Datum wird verborgen |
| Benachrichtigungen oder RSS für die Leitfadenbibliothek einrichten | ein Testabonnent erhält nach dem Lauf der Benachrichtigungen eine E-Mail ([Benachrichtigungen](../../../../anleitungen/benachrichtigungen-administrator.md)) |

## Tage 61 bis 90: Migration und Start

| Schritt | Lesen |
|---|---|
| Zuerst die geltenden Leitfäden mit Fundstellen und Geltungsdaten migrieren; ersetzte Leitfäden als eigene Objekte mit *Ersetzt* kennzeichnen | [Objektzustände](../../../../anleitungen/objektzustaende.md) |
| Autorinnen und Autoren in Änderungshinweisen schulen, Prüfende im Posteingang der Zusammenarbeit und in **Vergleichen** | [Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md) |
| Alte Adressen von Leitfäden auf die neuen Objekte umleiten; regulierte Akteure haben sie als Lesezeichen gespeichert | [Umleitungen und verschobene Inhalte](../../../ersteller/website-ersteller/weiterleitungen-und-verschobene-inhalte.md) |
| Start | [Start-Checkliste](../../../ersteller/website-ersteller/checkliste-fuer-den-start.md) |

Prüfungen beim Start:

- [ ] Eine Stichprobe von 20 alten Adressen von Leitfäden leitet auf die richtige neue Seite um.
- [ ] Jede Leitfadenseite zeigt Fundstelle, Geltungsdatum, Status und Änderungshinweis.
- [ ] **Setup > Audit** zeigt die Prüfentscheidungen der Migrationswoche.

## Risiken

| Risiko | Antwort |
|---|---|
| Die rechtliche Prüfung bremst alles | **Betroffene Versionen** oder Sektionen eingrenzen; kleine Korrekturen durch das Webteam mit Nachweis erlauben |
| Alte Versionen gehen an der Versionsgrenze verloren | die Grenze vor der Migration anheben; die Versionsseite eines viel bearbeiteten Leitfaden-Objekts prüfen |
| Ersetzte Leitfäden wirken aktuell | Zustand im Template gut sichtbar anzeigen; ersetzte Einträge aus der Suche nehmen oder kennzeichnen |

## Verwandte Seiten

- [Behörden](README.md)
- [Leitfäden für regulierte Akteure](leitfaeden-fuer-regulierte-akteure.md)
- [Ihre Website planen](../../../ersteller/website-ersteller/ihre-website-planen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
