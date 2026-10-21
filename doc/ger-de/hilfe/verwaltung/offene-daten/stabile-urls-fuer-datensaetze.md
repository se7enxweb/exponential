---
title: Stabile URLs für Datensätze
audience: government, website-creator, editor, admin
category: public-sector
order: 80
summary: Wie Sie jedem Datensatz eine Adresse geben, die jahrelang funktioniert - was Exponential nach Umbenennungen und Verschiebungen automatisch tut, was eine Adresse bricht, und die Regeln, auf die sich ein Datenteam einigen sollte.
keywords: stabile URL, dauerhafte URL, Permalink, URL-Alias, Weiterleitung, Verlaufsalias, content/view/full, Download-Adresse, Knoten-ID, verschobene Inhalte, offene Daten, stable URL, persistent URL, redirect, history alias, download address, node ID, moved content, open data
type: help
---
# Stabile URLs für Datensätze

Diese Seite richtet sich an Websiteersteller, Redaktionen und Administratoren, die Daten veröffentlichen, auf die
andere verlinken, die sie per Skript herunterladen oder zitieren werden. Nachnutzer bauen auf Ihren Adressen auf; eine
defekte Adresse macht ihre Arbeit kaputt. Die Angaben zu Aliassen und Weiterleitungen stammen aus
[Weiterleitungen und verschobene Inhalte](../../../../eng-us/help/creators/website-creators/redirects-and-moved-content.md) und
[URL-Aliasse und sprechende URLs](../../../../eng-us/help/creators/website-creators/url-aliases-and-nice-urls.md).

## Inhalt

1. [Die Adressen eines Datensatzes](#die-adressen-eines-datensatzes)
2. [Was Exponential funktionsfähig hält](#was-exponential-funktionsfähig-hält)
3. [Was eine Adresse bricht](#was-eine-adresse-bricht)
4. [Regeln für das Datenteam](#regeln-für-das-datenteam)
5. [Eine kurze, dauerhafte Adresse pro Datensatz](#eine-kurze-dauerhafte-adresse-pro-datensatz)
6. [Adressen prüfen](#adressen-prüfen)

## Die Adressen eines Datensatzes

| Adresse | Beispiel | Stabil, solange |
|---|---|---|
| URL-Alias der Seite | `/Open-data/Street-lighting-requests` | der Knoten existiert; alte Aliasse leiten nach Umbenennen oder Verschieben weiter |
| Systemadresse des Knotens | `/content/view/full/<node ID>` | der Knoten existiert |
| Download einer Datei | `/content/download/<object ID>/<attribute ID>/file/<file name>` | das Objekt existiert und der Dateiname gleich bleibt |
| Feed des Katalogs | `/rss/feed/<address>` | der RSS-Export existiert und aktiv ist |
| REST-Adresse | `/api/ezp/v1/content/node/<node ID>` | der Knoten existiert und die API eingeschaltet bleibt |

Knoten-ID und Objekt-ID ändern sich während der Lebensdauer des Objekts nie, was auch immer die Redaktion mit Namen und
Orten macht.

## Was Exponential funktionsfähig hält

| Ereignis | Ergebnis |
|---|---|
| Die Seite wird umbenannt | ein neuer Alias entsteht; der alte bleibt und leitet weiter |
| Die Seite wird verschoben | der alte Pfad leitet weiter |
| Eine Übersetzung wird umbenannt | dasselbe, pro Sprache |
| Die Datei wird durch eine mit demselben Namen ersetzt | die Download-Adresse bleibt gleich |

Redaktionen können also Titel korrigieren und Ordner umorganisieren, ohne Links zu brechen.

## Was eine Adresse bricht

| Ereignis | Ergebnis | Vorbeugung |
|---|---|---|
| Der Knoten wird entfernt | „nicht gefunden“; nichts leitet weiter | Entfernen Sie keine Datensätze; kennzeichnen Sie sie als zurückgezogen (siehe „Datensätze versionieren und aktualisieren“) |
| Die Datei wird durch eine mit anderem Namen ersetzt | die alte Download-Adresse passt nicht mehr | Halten Sie Dateinamen stabil oder verlinken Sie auf die Seite |
| Der Katalog zieht auf einen anderen Siteaccess oder Host um | Adressen auf dem alten Host funktionieren nicht mehr | Leiten Sie im Webserver weiter ([Livegang](../../../../eng-us/help/creators/website-creators/going-live-hosts-https.md)) |
| Ein globaler Alias oder Platzhalter wird entfernt | seine Adresse funktioniert nicht mehr | Führen Sie eine Liste der Aliasse, die Sie zugesagt haben |

## Regeln für das Datenteam

1. Verlinken Sie in Ankündigungen und Katalogen auf die **Seite** des Datensatzes, nicht auf eine Datei.
2. Entfernen Sie nie einen veröffentlichten Datensatz; ziehen Sie ihn stattdessen mit einem Hinweis zurück.
3. Halten Sie Dateinamen über Aktualisierungen hinweg stabil oder veröffentlichen Sie jeden Zeitraum als eigene
   Distribution.
4. Geben Sie jedem Datensatz einen kurzen, dauerhaften Alias (siehe unten) und geben Sie ihn auf der Seite als
   „Zitieren als“ aus.
5. Halten Sie verschobene oder ausgemusterte Adressen vor einem Relaunch in einer Weiterleitungstabelle fest
   ([Relaunch einer Website: ein Weiterleitungsplan](../../../../eng-us/help/creators/website-creators/redirects-and-moved-content.md#relaunching-a-site-a-redirect-plan)).

## Eine kurze, dauerhafte Adresse pro Datensatz

Geben Sie jedem Datensatz einen zusätzlichen Alias wie `/data/street-lighting`:

1. Öffnen Sie die Datensatzseite, Reiter **URL-Aliase**.
2. Fügen Sie den Alias hinzu. Er zeigt auf den Knoten und funktioniert deshalb auch nach Umbenennungen und
   Verschiebungen.

Für ein Muster alter Adressen verwenden Sie **Setup > URL Platzhalter** (zum Beispiel `datasets/*` auf `data/{1}`),
mit angekreuztem „URL umleiten“, und testen Sie ihn mit **Eine Adresse ausprobieren**
([URL-Platzhalter](../../../anleitungen/urls-und-aliase.md#4-url-platzhalter)).

## Adressen prüfen

- `curl -sI https://www.example.gov/data/street-lighting` sollte mit `200` antworten oder mit `301` auf die aktuelle
  Seite.
- **Setup > URL-Verwaltung** listet die Links in Ihren Inhalten und ihren Zustand auf
  ([Linkliste](../../../anleitungen/urls-und-aliase.md#1-die-linkliste)).
- `./console exp:verify_aliases` prüft die Tabelle der URL-Aliasse; `--fix` repariert, was sicher repariert werden
  kann ([Inhaltssprachen, Abschnitt 10](../../../anleitungen/inhaltssprachen.md#10-befehle-für-die-shell)).

## Verwandte Seiten

- [Weiterleitungen und verschobene Inhalte](../../../../eng-us/help/creators/website-creators/redirects-and-moved-content.md)
- [URL-Platzhalter und eigene Aliasse](../../../../eng-us/help/creators/website-creators/url-wildcards.md)
- [Links, URL-Aliasse, Platzhalter und Suchstatistiken](../../../anleitungen/urls-und-aliase.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
