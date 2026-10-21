---
title: Was ein Land oder eine Region veröffentlicht
audience: government, project-manager, website-creator
category: public-sector
order: 20
summary: Die Inhalte, die Länder und Regionen typischerweise veröffentlichen - Dienstleistungen nach Thema, Beschlüsse der Volksvertretung, Pläne, Förderprogramme, Nachrichten, Informationen für Kommunen - und was jede Art von Exponential verlangt.
keywords: regionale Website, Website eines Landes, Dienstleistungen nach Thema, Beschlüsse der Volksvertretung, regionale Pläne, Förderprogramme, Zuwendungen, Aufrufe, Kommunen, Umfang, regional website, state website, services by theme, assembly decisions, regional plans, funding programs, grants, calls, municipalities, scope
type: help
---
# Was ein Land oder eine Region veröffentlicht

Diese Seite richtet sich an Projektverantwortliche und Website-Ersteller eines Bundeslands, einer Provinz, einer Region
oder eines Kreises, die den Umfang einer Website festlegen. Sie listet auf, was solche Organisationen typischerweise
veröffentlichen (allgemeine Praxis im öffentlichen Sektor; Zuständigkeiten unterscheiden sich zwischen Ländern), und was
jede Art von Inhalt vom System braucht.

## Inhalt

1. [Die Inhalte](#die-inhalte)
2. [Was das CMS können muss](#was-das-cms-können-muss)
3. [Inhalte für Kommunen und Partner](#inhalte-für-kommunen-und-partner)
4. [Vor dem Bauen zu klärende Fragen](#vor-dem-bauen-zu-klärende-fragen)

## Die Inhalte

| Inhalt | Zielgruppe | Form |
|---|---|---|
| Dienstleistungen nach Thema (Verkehr, Umwelt, Gesundheit, Bildung, Kultur, Wirtschaft ...) | Bürgerinnen und Bürger, Unternehmen | stabile Seiten, jährlich überprüft, mit Links zu Onlineformularen |
| Die regionale Volksvertretung: Informationen zur öffentlichen Rolle der Mitglieder, Ausschüsse, Tagesordnungen, Niederschriften, Beschlüsse | Bürgerinnen und Bürger, Presse, Kommunen | Dokumente in einem Sitzungskalender, dauerhaft aufbewahrt |
| Pläne und Strategien: Raumordnungspläne, Mobilitätspläne, Umweltprogramme | Fachleute, Bürgerinnen und Bürger | lange Dokumente mit Karten, Beteiligungszeiträume |
| Förderprogramme: Aufrufe, Kriterien, Fristen, Ergebnisse | Antragstellende | eine Seite je Aufruf mit Daten und Dokumenten |
| Nachrichten und Pressemitteilungen | Presse, Bürgerinnen und Bürger | häufig |
| Baustellen, Sperrungen, Störungen | Reisende | kurzlebig, dringend |
| Dienststellen und Anlaufstellen | alle | Anschriften, Öffnungszeiten, Barrierefreiheit des Gebäudes |
| Informationen und Arbeitshilfen für Kommunen | kommunale Beschäftigte | Leitfäden, Vorlagen, manchmal hinter einer Anmeldung |
| Stellen und Vergaben | Bewerberinnen und Bewerber, Lieferanten | mit Fristen |

## Was das CMS können muss

| Bedarf | Funktion von Exponential | Lesen |
|---|---|---|
| Eine Seite je Aufruf mit Beginn- und Enddatum | eine Klasse mit `ezdatetime`-Attributen und nach Datum sortierten Listen | [Datentypen wählen](../../../ersteller/website-ersteller/datentypen-waehlen.md) |
| Beschlüsse und Niederschriften als dauerhafte Dokumente | Objekte der Klasse File, Beziehungen, Versionen | [Dateien und Downloads](../../../ersteller/website-ersteller/dateien-und-downloads.md) |
| Kurzlebige Hinweise, die von selbst verschwinden | der Cronjob `unpublish.php` oder `hide.php` mit `content.ini [UnpublishSettings]` oder `[HideSettings]` | [Cronjobs](../../../../anleitungen/cronjobs.md) |
| Themen quer über Fachbereiche | Tags | [Verschlagworten mit eztags](../../../ersteller/website-ersteller/verschlagworten-mit-eztags.md) |
| Ein Punkt auf einer Karte für eine Dienststelle oder einen Standort | der Datentyp `ezgmaplocation` (Google Maps, ein Dritter) | [ezgmaplocation](../../../../funktionen/6.0/erweiterungen/ezgmaplocation.md) |
| Ein Bereich nur für kommunale Beschäftigte | eine Sektion, die Anonymous nicht lesen kann, und eine Rolle für kommunale Konten | [Registrierung von Mitgliedern und Mitgliederbereiche](../../../ersteller/website-ersteller/mitgliederregistrierung-und-mitgliederbereiche.md) |
| Ein Feed von Störungen für andere Websites und Apps | RSS-Export eines Ordners | [RSS-Feeds](../../../../anleitungen/rss-feeds.md) |
| Mehrere Websites (Region, Tourismus, ein Programm) | Siteaccesses auf einer Installation | [Multisite einrichten](../../../ersteller/website-ersteller/multisite-einrichten.md) |

Die beiden Cronjobs arbeiten unterschiedlich: `unpublish.php` verschiebt Objekte der Klassen aus
`[UnpublishSettings] ClassList[]`, deren Attribut `unpublish_date` vorbei ist, unterhalb der Knoten aus `RootNodeList[]`
in den Papierkorb; `hide.php` verbirgt Knoten, deren Datumsattribut (je Klasse in `[HideSettings]
HideDateAttributeList[]` genannt) vorbei ist. Verbergen behält die Inhalte für die Akten; ziehen Sie es für alles vor,
wonach später gefragt werden kann.

## Inhalte für Kommunen und Partner

Regionale Stellen veröffentlichen oft für die Kommunen ihres Gebiets: Musterdokumente, Leitfäden, Daten. Entscheiden Sie,
ob das öffentlich ist (oft kann es das sein, und es sollte es sein, wenn es öffentliche Entscheidungen erklärt) oder
eingeschränkt. Ein eingeschränkter Bereich verwendet eine Sektion und eine Rolle, und jeder kommunale Benutzer erhält ein
Konto in einer eigenen Benutzergruppe ([Sektionen, 13.1](../../../../anleitungen/sektionen.md#131-ein-mitgliederbereich)). Konten
von Personen außerhalb Ihrer Organisation brauchen eine verantwortliche Person und einen Überprüfungszyklus:
**Benutzer > Benutzerkonten prüfen** listet Konten, die seit einer Anzahl von Tagen nicht gesehen wurden
([Benutzerkonten prüfen](../../../../funktionen/6.0/benutzerpflege.md)).

## Vor dem Bauen zu klärende Fragen

- [ ] Welche Dienstleistungen gehören der Region und welche den Kommunen oder der nationalen Ebene? Verlinken Sie die
  anderen.
- [ ] Wo liegen die Dokumente der Volksvertretung heute, und muss die Website ihr amtlicher Veröffentlichungsort sein?
- [ ] Welche Sprachen haben in der Region einen rechtlichen Status?
- [ ] Welche Inhalte betreffen Orte, und genügt ein Link auf ein bestehendes Kartenportal?
- [ ] Wird die Region Websites für Kommunen hosten (siehe die Seite zu gemeinsamen Diensten)?

## Verwandte Seiten

- [Länder und Regionen](README.md)
- [Inhaltsmodell für Länder und Regionen](inhaltsmodell.md)
- [Sitemap und Inhaltsinventar](../../../ersteller/website-ersteller/sitemap-und-inhaltsbestandsaufnahme.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
