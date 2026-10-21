---
title: RPO und RTO als Entscheidungen
audience: government, project-manager, admin, hosting
category: public-sector
order: 110
summary: Für Verantwortliche für Dienste und Kontinuitätsplanende in öffentlichen Stellen. Was ein Wiederherstellungspunktziel und ein Wiederherstellungszeitziel für eine Exponential-Website bedeuten, warum sie organisatorische Entscheidungen und keine Eigenschaften der Software sind und welche technischen Maßnahmen welche Werte erfüllen.
keywords: RPO, RTO, Wiederherstellungspunktziel, Wiederherstellungszeitziel, Kontinuität, Entscheidung, Verantwortliche für den Dienst, Sicherungshäufigkeit, Replikation, Wiederherstellungsdauer, gemessen, Kosten, Risiko, recovery point objective, recovery time objective, continuity, decision, service owner, backup frequency, replication, restore time, measured, cost, risk
type: help
---
# RPO und RTO als Entscheidungen

Diese Seite richtet sich an Verantwortliche für Dienste, Kontinuitätsplanende und IT-Leitungen in öffentlichen
Stellen. Sie erklärt das Wiederherstellungspunktziel (RPO) und das Wiederherstellungszeitziel (RTO) für eine Website,
warum es Entscheidungen Ihrer Organisation sind und welche technischen Maßnahmen welche Werte erfüllen. Sie nennt
keine Zahlen: Die richtigen Werte hängen davon ab, wozu die Website dient, und zugesagt werden dürfen nur gemessene
Wiederherstellungszeiten.

## Inhalt

1. [Zwei Fragen](#zwei-fragen)
2. [Wer entscheidet](#wer-entscheidet)
3. [Was welches Ziel erfüllt](#was-welches-ziel-erfüllt)
4. [Messen, bevor Sie zusagen](#messen-bevor-sie-zusagen)
5. [Die Entscheidung festhalten](#die-entscheidung-festhalten)
6. [Checkliste](#checkliste)

## Zwei Fragen

- **Wiederherstellungspunkt (RPO)**: Wie viel aktuelle Arbeit darf verloren gehen? Stammt die letzte brauchbare
  Datensicherung von Mitternacht und tritt der Ausfall um vier Uhr nachmittags auf, muss die Veröffentlichungsarbeit
  eines Tages wiederholt werden.
- **Wiederherstellungszeit (RTO)**: Wie lange darf die Website nicht verfügbar sein, bis sie wieder da ist?

Beides sind Zusagen, die ein Hoster in einem Vertrag anbieten kann
([SLA-Optionen](../../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md)); innerhalb einer öffentlichen Stelle sind sie Teil der
Kontinuitätsanforderungen des Dienstes.

## Wer entscheidet

Die Werte kommen vom Dienst, nicht von der IT:

| Frage an die Verantwortlichen für den Dienst | Wirkung |
|---|---|
| Was verliert die Öffentlichkeit, wenn die Website eine Stunde, einen Tag, eine Woche ausfällt? | das RTO |
| Wie viel wird pro Tag redaktionell bearbeitet, und ließe sich das wiederholen? | das RPO |
| Gibt es gesetzliche Fristen, die nur auf dieser Website veröffentlicht werden? | ein strengeres RTO in diesen Zeiträumen |
| Werden Formulare oder Einreichungen gesammelt, die sich nicht erneut eingeben lassen? | ein strengeres RPO |

Die IT nennt dann, was jeder Wert kostet, und die Organisation wählt. Halten Sie die Wahl und die verantwortliche
Person fest.

## Was welches Ziel erfüllt

| Maßnahme | Verbessert | Hinweise |
|---|---|---|
| Nächtliche Datenbank- und Dateisicherung außer Haus | RPO von bis zu einem Tag; RTO einer Wiederherstellung | die Grundlage ([Was zu sichern ist](was-gesichert-werden-muss.md)) |
| Häufigere Datenbanksicherungen | RPO | konsistente Online-Dumps gibt es für SQLite (`.backup`), PostgreSQL und InnoDB |
| Die eigene Replikation oder zeitpunktgenaue Wiederherstellung der Datenbank | RPO nahe am Ausfallzeitpunkt | PostgreSQL bringt sie selbst mit; SQLite hat keine ([Hochverfügbarkeit der Datenbank](../verfuegbarkeit/hochverfuegbarkeit-der-datenbank.md)) |
| Eine für Wiederherstellungen bereitstehende Reservemaschine | RTO | entfällt die Zeit für die Beschaffung einer Maschine |
| Mehrere Webserver und ein Datenbankserver | RTO bei Ausfall eines Webservers | [Überblick über Hochverfügbarkeit](../verfuegbarkeit/hochverfuegbarkeit-im-ueberblick.md) |
| Ein geübtes Wiederherstellungsverfahren | RTO | gemessen unter [Wiederherstellung testen](wiederherstellungstests.md) |

Beachten Sie, dass Uploads (Dateien) für eine saubere Wiederherstellung vom selben Zeitpunkt stammen müssen wie die
Datenbank; eine Datenbank mit einem besseren RPO als ihre Dateien stellt Seiten wieder her, deren neueste Bilder
fehlen.

## Messen, bevor Sie zusagen

„Nennen Sie die Wiederherstellungszeit, die Sie in einem Test gemessen haben, nicht eine, die Sie erhoffen“
([Sicherungspläne und Aufbewahrung](../../../hosting/betrieb/backup-zeitplaene-und-aufbewahrung.md)). Führen Sie einen
Wiederherstellungstest durch, messen Sie die Zeit von der Entscheidung zur Wiederherstellung bis „die Prüfungen sind
bestanden“, und verwenden Sie diese als Grundlage des RTO, mit einer Reserve, die Sie wählen. Wiederholen Sie die
Messung nach Wachstum oder größeren Änderungen.

## Die Entscheidung festhalten

| Feld | Inhalt |
|---|---|
| Website | Name, Zweck |
| RPO | der Wert und die Maßnahme, die ihn erfüllt |
| RTO | der Wert, die gemessene Wiederherstellungsdauer und das Datum der Messung |
| Verantwortlich | die verantwortliche Person für den Dienst, die entschieden hat |
| Ausnahmen | Zeiträume mit strengeren Werten (Wahlen, Fristen) |
| Überprüfung | das Datum der nächsten Überprüfung |

Tragen Sie sie in den [Notfallwiederherstellungsplan](notfallwiederherstellungsplan.md) und das
[Hosting-Entscheidungsprotokoll](../hosting/hosting-entscheidungsprotokoll.md) ein.

## Checkliste

- [ ] Die Verantwortlichen für den Dienst haben RPO und RTO schriftlich festgelegt.
- [ ] Die technischen Maßnahmen erfüllen sie, einschließlich Dateien vom selben Zeitpunkt wie die Datenbank.
- [ ] Das RTO beruht auf einer gemessenen Wiederherstellung.
- [ ] Zeiträume mit strengeren Werten sind benannt.
- [ ] Ein Überprüfungsdatum ist gesetzt.

## Verwandte Seiten

- [Datensicherung und Wiederherstellung](README.md): Übersicht dieses Ordners
- [Verfügbarkeitsziele und Service-Level](../verfuegbarkeit/verfuegbarkeitsziele-und-service-level.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
