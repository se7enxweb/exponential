---
title: Verantwortung für Inhalte
audience: government, editor, admin, project-manager
category: public-sector
order: 100
summary: Der Unterschied zwischen dem technischen Eigentümer eines Inhaltsobjekts in Exponential und der organisatorisch verantwortlichen Stelle, die eine öffentliche Stelle für jede Seite benennt, wie Sie Letztere festhalten und wie Sie Inhalte übergeben, wenn sich Personen oder Fachbereiche ändern.
keywords: Verantwortung für Inhalte, inhaltlich verantwortliche Person, Seitenverantwortliche, Rechenschaft, Einschränkung Owner, Eigentümer ändern, ezownerchange, Übergabe, Fachbereiche, Steuerung, zuständige Stelle, content ownership, content owner, page owner, accountability, Owner limitation, change owner, handover, departments, governance, responsible officer
type: help
---
# Verantwortung für Inhalte

Diese Seite richtet sich an Webverantwortliche und Chefredaktionen, die benennen müssen, wer für jede Seite einer öffentlichen Website
verantwortlich ist. Exponential hat einen technischen Eigentümer je Objekt; Organisationen brauchen meist zusätzlich eine fachlich verantwortliche
Stelle. Diese Seite erklärt beides und wie Sie beides aktuell halten. Quellen: [Den Eigentümer von Inhalten ändern](../../../../anleitungen/eigentuemer-aendern.md)
und die Einschränkung `Owner` in `kernel/content/module.php`.

## Inhalt
1. [Zwei Arten von Eigentümer](#zwei-arten-von-eigentümer)
2. [Der technische Eigentümer](#der-technische-eigentümer)
3. [Die fachlich verantwortliche Stelle festhalten](#die-fachlich-verantwortliche-stelle-festhalten)
4. [Übergabe, wenn sich Personen oder Fachbereiche ändern](#übergabe-wenn-sich-personen-oder-fachbereiche-ändern)
5. [Verwandte Seiten](#verwandte-seiten)

## Zwei Arten von Eigentümer

| | Technischer Eigentümer | Fachlich verantwortliche Stelle |
|---|---|---|
| Was | das Benutzerkonto, dem ein Inhaltsobjekt gehört | die Funktion oder der Fachbereich, die oder der für die Aussage der Seite verantwortlich ist |
| Festgelegt durch | das Konto, das das Objekt angelegt hat; geändert mit **Eigentümer ändern** | Ihre Steuerungsregeln |
| Verwendet für | die Einschränkung `Owner` in Rollen („darf eigene Inhalte bearbeiten“) | Prüfung, Richtigkeit, Beschwerden, Archivierungsentscheidungen |
| Gespeichert | an jedem Objekt | nicht eingebaut; siehe unten |

## Der technische Eigentümer

- Jedes Inhaltsobjekt hat einen Eigentümer. Rollen können einschränken, was jemand mit seinen eigenen Inhalten tun darf: die Einschränkung
  `Owner` von `content/read`, `content/edit` und `content/remove`.
- Die Extension **ezownerchange** ändert den Eigentümer eines Objekts oder eines Teilbaums, mit einer Vorschau: Kontextmenü,
  **Eigentümer ändern** (Richtlinie `owner/change`; Teilbäume brauchen `owner/subtree`). Bei neuen Installationen ist sie eingeschaltet.
  Jede Änderung wird als `content.ezownerchange.change` erfasst.
- Auf der Kommandozeile zuerst eine Vorschau:

  ```bash
  php extension/ezownerchange/bin/php/expownerchange.php --to-owner=<new owner id> --node=<node id> --from-owner=<old owner id> --dry-run --list
  ```

## Die fachlich verantwortliche Stelle festhalten

Exponential hat kein eingebautes Feld „zuständiger Fachbereich“. Möglichkeiten, von der einfachsten an:

| Möglichkeit | Wie | Hinweise |
|---|---|---|
| Der Baum | die Seiten jedes Fachbereichs liegen in einem eigenen Ordner | die Verantwortung folgt dem Teilbaum; passt zu den Rollen der Fachbereichsredaktionen ([Rollenentwurf: Redaktionen der Fachbereiche](../personen-und-berechtigungen/rollenentwurf-fachredaktionen.md)) |
| Ein Feld an der Klasse | fügen Sie Ihren Seitenklassen ein Attribut wie „Zuständige Einheit“ hinzu | wird den Redaktionen angezeigt; kann mit Fetches aufgelistet werden; eine Änderung durch die Website-Entwicklung |
| Tags | die Extension eztags, falls aktiv, mit einem Tag je Einheit | durchsuchbar; siehe [Tags](../../../../../eng-us/features/6.0/extensions/eztags.md) |
| Ein Objektzustand | eine Zustandsgruppe je verantwortlicher Stelle ist möglich, skaliert aber schlecht | bevorzugen Sie die obigen Möglichkeiten |

Eine Funktion (zum Beispiel „Leitung Kommunikation, Fachbereich Beispiel“) ist eine bessere verantwortliche Stelle als der Name einer Person:
Sie überdauert personelle Wechsel und hält personenbezogene Daten von öffentlichen Seiten fern.

## Übergabe, wenn sich Personen oder Fachbereiche ändern

1. **Eine Person tritt aus**: Deaktivieren Sie das Konto oder legen Sie es mit Deaktivieren und verschieben still; ihre Inhalte bleiben und
   können von anderen mit entsprechenden Rechten weiter bearbeitet werden. Wenn Rollen `Owner = Self` verwenden, übertragen Sie ihre Inhalte mit
   **Eigentümer ändern**, Teilbaum und „nur die Objekte des aktuellen Eigentümers“ auf eine Kollegin oder einen Kollegen
   ([Eintritt, Wechsel und Austritt](../personen-und-berechtigungen/eintritt-wechsel-austritt.md)).
2. **Ein Fachbereich wird umorganisiert**: Verschieben Sie den Teilbaum unter den Ordner des neuen Fachbereichs und passen Sie dann die
   eingeschränkten Rollenzuweisungen an; das Verschieben ändert die Sektion nur bei Objekten, die die Sektion des alten übergeordneten Elements hatten
   ([Sektionen und Teilbaum-Einschränkungen](../personen-und-berechtigungen/sektionen-und-teilbaum-einschraenkungen.md#was-geschieht-wenn-inhalte-verschoben-werden)).
3. **Halten Sie es fest**: Verschiebungen und Eigentümerwechsel stehen im Kanal `content` des Audit-Trails.

## Verwandte Seiten
- [Prüftermine mit Objektzuständen](pruefdaten-mit-objektzustaenden.md)
- [Steuerung des Stilleitfadens](steuerung-des-styleguides.md)
- [Leitfaden zum Ändern von Eigentümern](../../../../anleitungen/eigentuemer-aendern.md)
- [Versionsgeschichte und Rechenschaft](versionsverlauf-und-nachvollziehbarkeit.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
