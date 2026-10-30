---
title: PDF- und Druckexporte
audience: businesses, admin, editor
category: business-solutions
order: 60
summary: Wie eine Bibliothek oder ein Archiv mit dem PDF-Export von Exponential einen Teil des Katalogs in ein PDF verwandelt - was ein Export enthält, einmalig oder bei jedem Abruf erzeugt, welche gespeicherten PDFs öffentlich sind, Fußzeilen und Schriften für jede Sprache und gute Verwendungen wie Erwerbungslisten und Sammlungsführer.
keywords: PDF-Export, gedruckter Katalog, Erwerbungsliste, Sammlungsführer, Handbuch, pdf/list, einmalig erstellen, bei jedem Abruf, Fußzeilentext, TrueType-Schrift, Druck, Bibliothek, PDF export, printed catalog, acquisition list, collection guide, handbook, generated once, on the fly, footer text, TrueType font, print, library
type: help
---
# PDF- und Druckexporte

Diese Seite ist für Mitarbeiter von Bibliotheken und Archiven, die eine druckbare oder herunterladbare Fassung eines
Teils des Katalogs möchten: eine Liste der Neuerwerbungen, einen Führer zu einer Sammlung, eine Leseliste. Sie erklärt,
was der PDF-Export von Exponential tut, die eine Entscheidung, die am meisten zählt (gespeichert oder bei jedem Abruf),
und was öffentlich wird. Die vollständige Anleitung für Administratoren ist [PDF-Exporte](../../../anleitungen/pdf-exporte.md).

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../../beispiele/webserver/nginx.md), [LiteSpeed](../../../beispiele/webserver/litespeed.md), [Caddy](../../../beispiele/webserver/caddy.md), [IIS](../../../beispiele/webserver/iis.md),
> [Velocity](../../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../../hosting/control-panels/plesk.md), [cPanel](../../hosting/control-panels/cpanel.md), [DirectAdmin](../../hosting/control-panels/directadmin.md), [ISPConfig](../../hosting/control-panels/ispconfig.md).
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../../anleitungen/seiten-cache-mit-litespeed.md).

## Inhalt

1. [Was ein PDF-Export ist](#was-ein-pdf-export-ist)
2. [Gute Verwendungen in einer Bibliothek](#gute-verwendungen-in-einer-bibliothek)
3. [Einmalig oder bei jedem Abruf erzeugt](#einmalig-oder-bei-jedem-abruf-erzeugt)
4. [Was öffentlich ist](#was-öffentlich-ist)
5. [Einen Export anlegen](#einen-export-anlegen)
6. [Fußzeile, Titelseite und Schriften](#fußzeile-titelseite-und-schriften)
7. [Wenn das PDF leer oder falsch ist](#wenn-das-pdf-leer-oder-falsch-ist)
8. [Andere Wege zu drucken](#andere-wege-zu-drucken)
9. [Checkliste für Exporte](#checkliste-für-exporte)

## Was ein PDF-Export ist

„A **PDF export** makes one PDF of a **source node**“: Ein PDF-Export erzeugt ein PDF eines **Quellknotens**, mit seinem
Namen als erster Überschrift und dann seinen Attributen. Ein Export als **Baum** fügt die Knoten darunter hinzu, auf
jeder Ebene, aber nur die der **Klassen**, die Sie markieren ([PDF-Exporte, In short](../../../anleitungen/pdf-exporte.md#kurz-gesagt)).
Die Seiten liegen unter **Setup > PDF-Export** (`/pdf/list`, Richtlinie `pdf/edit`)
([PDF-Export](../../../funktionen/6.0/pdf-export.md)).

## Gute Verwendungen in einer Bibliothek

| Verwendung | Quellknoten | Zu markierende Klassen |
|---|---|---|
| Ein Führer zu einer Sammlung | Die Sammlung (ein Container) | Die Sammlungsklasse und die Eintragsklassen |
| Eine Liste der Erwerbungen dieses Monats | Ein Ordner, der sie enthält, oder ein Monatsordner | Ordner- und Eintragsklassen |
| Eine Leseliste | Ein Ordner verwandter Einträge | Ordner- und Eintragsklassen |
| Ein Handbuch für Ehrenamtliche oder Mitarbeiter | Ein Dokumentationsordner | Ordnerklasse und Klassen der Dokumentationsseiten |

„A node of an unticked class is left out *with everything below it*“: Ein Knoten einer nicht markierten Klasse wird
*mit allem darunter* ausgelassen; markieren Sie also die Ordner, die die Inhalte enthalten, ebenso wie die Artikel
([PDF-Exporte, Creating or editing an export](../../../anleitungen/pdf-exporte.md#3-einen-export-anlegen-oder-bearbeiten)).

## Einmalig oder bei jedem Abruf erzeugt

| | Einmalig erzeugt | Bei jedem Abruf erzeugt |
|---|---|---|
| Wann das PDF entsteht | Beim Speichern des Exports und erneut mit **Neu erzeugen** | Bei jedem Download neu |
| Inhalt | Wie er damals war | Immer aktuell |
| Geschwindigkeit | Schnell herunterzuladen | Ein großer Baum braucht jedes Mal eine Weile |
| Gespeicherte Datei | `var/<site>/storage/pdf/<file name>` | Keine |
| Wer herunterladen kann | Jeder mit der öffentlichen Adresse | Benutzer mit der Richtlinie `pdf/edit`, über `/pdf/edit/<id>/generate` |

Quelle: [PDF-Exporte, In short und Opening the page](../../../anleitungen/pdf-exporte.md#1-die-seite-öffnen).

## Was öffentlich ist

> **Warnung**
> „A stored PDF is public: the web server serves `var/<site>/storage/pdf/<file name>` to anybody, signed in or not.“
> Ein gespeichertes PDF ist öffentlich; der Webserver liefert es jedem aus, angemeldet oder nicht. Verwenden Sie einen
> Export, der bei jedem Abruf erzeugt wird, „for content that is not for everybody“ (für Inhalte, die nicht für alle
> sind), und „do not give an export a file name you would not want guessed“ (geben Sie einem Export keinen Dateinamen,
> der nicht erraten werden soll, [PDF-Exporte](../../../anleitungen/pdf-exporte.md#1-die-seite-öffnen)).

Ein gespeichertes PDF passt also zu einer öffentlichen Erwerbungsliste; eine Liste, die eingeschränkte Einträge
enthält, sollte bei jedem Abruf erzeugt werden oder diese Einträge nicht enthalten. Den öffentlichen Pfad öffnet eine
Rewrite-Regel in den ausgelieferten `.htaccess`-Dateien; ein Server, der seine Regeln anderswo hält, braucht dort
denselben Pfad (dieselbe Seite). Sonst wird nichts im Speicher öffentlich: Andere Uploads bleiben hinter
`content/download` und seinen Berechtigungen.

## Einen Export anlegen

1. **Setup > PDF-Export > Neuer PDF-Export.**
2. **Titel**: der Name in der Liste (wird im PDF nicht gedruckt).
3. **Inhalt**: Wählen Sie mit **Durchsuchen** den Quellknoten; wählen Sie **Knoten** oder **Baum**, und markieren Sie
   für einen Baum die Klassen.
4. **Startseite**: eine optionale erste Seite mit einer großen und einer kleineren Textzeile.
5. **Fußzeilentext**: die Zeile neben der Seitenzahl.
6. **Art des Exports** und **Dateiname**: einmalig oder bei jedem Abruf erzeugen; Buchstaben, Ziffern, `.`, `_` und
   `-`.
7. **OK**, dann in der Liste **Herunterladen**, um das Ergebnis zu prüfen.

Quelle: [PDF-Exporte, Creating or editing an export](../../../anleitungen/pdf-exporte.md#3-einen-export-anlegen-oder-bearbeiten).
Für eine einmalig erzeugte Monatsliste drücken Sie **Neu erzeugen**, nachdem die Einträge des Monats veröffentlicht
sind, oder planen Sie das als Routineaufgabe für eine benannte Person ein.

## Fußzeile, Titelseite und Schriften

- **Fußzeile**: Markieren Sie **Show footer** und geben Sie Ihren Text ein, zum Beispiel den Namen der Bibliothek; jede
  Seite trägt ihn statt „Exponential PDF export“
  ([PDF-Export](../../../funktionen/6.0/pdf-export.md#einen-export-mit-eigener-fußzeile-erstellen)).
- **Jede Sprache**: „The bundled fonts hold one single-byte character set. For Unicode text, point `Font` at a `.ttf`
  file“ (Die mitgelieferten Schriften enthalten einen Ein-Byte-Zeichensatz; für Unicode-Text lassen Sie `Font` auf eine
  Datei `.ttf` zeigen, [PDF-Export, Print any language](../../../funktionen/6.0/pdf-export.md#jede-sprache-drucken-eine-truetype-schrift-verwenden)).
  Ein Katalog mit Titeln in mehreren Schriften braucht diese Einstellung.

## Wenn das PDF leer oder falsch ist

| Symptom | Wahrscheinliche Ursache |
|---|---|
| Das PDF hat nur die erste Inhaltsseite | Ein Baum ohne Klassen, oder die Ordner zwischen Quelle und Einträgen nicht markiert |
| Einträge fehlen | Die Klasse ihres Ordners war nicht markiert, der Ordner wurde also mit allem darunter übersprungen |
| Eine Warnung „Quelle fehlt“ | Der Quellknoten wurde entfernt; wählen Sie einen anderen |
| Falsche Zeichen | Keine TrueType-Schrift für Unicode-Text eingerichtet |
| Alte Inhalte | Ein gespeicherter Export, der seit der Änderung nicht neu erzeugt wurde: Drücken Sie Neu erzeugen, oder wechseln Sie zur Erzeugung bei jedem Abruf |
| Ein großer Baum läuft bei der Erzeugung bei jedem Abruf in eine Zeitüberschreitung | Stellen Sie ihn auf einmalige Erzeugung um und erzeugen Sie ihn außerhalb der Hauptzeiten neu |

Quellen: die Warnungen der Liste unter [PDF-Exporte, The list](../../../anleitungen/pdf-exporte.md#2-die-liste) und ihr
Abschnitt [Problems](../../../anleitungen/pdf-exporte.md#5-probleme).

## Andere Wege zu drucken

- Leser können jede Seite aus ihrem Browser drucken; ein Druck-Stylesheet in Ihrem Design bestimmt, wie sie aussieht
  ([Stylesheets und Skripte](../../ersteller/website-ersteller/stylesheets-und-skripte.md)).
- In Writer geschriebene Dokumente lassen sich mit `ezodf` zurück nach Writer exportieren
  ([ezodf](../../../funktionen/6.0/erweiterungen/ezodf.md)).
- Ein Feed neuer Einträge bedient Leser, die einen Feedreader bevorzugen
  ([Feeds für Neuerwerbungen](feeds-fuer-neuerwerbungen.md)).

## Checkliste für Exporte

- [ ] Jeder Export hat einen klaren Titel, einen Quellknoten und, für einen Baum, die richtigen markierten Klassen
      (Ordner eingeschlossen).
- [ ] Gespeicherte Exporte enthalten nur öffentliches Material und haben Dateinamen, die erraten werden dürfen.
- [ ] Eingeschränktes Material nur in Exporten, die bei jedem Abruf erzeugt werden, oder gar nicht.
- [ ] Text der Fußzeile gesetzt; eine TrueType-Schrift eingerichtet, wenn Titel nichtlateinische Schriften verwenden.
- [ ] Eine benannte Person erzeugt gespeicherte Exporte nach Plan neu.
- [ ] Die öffentliche PDF-Adresse abgemeldet getestet.

## Verwandte Seiten

- [Bibliotheken und Kataloge](README.md)
- [PDF-Exporte (Anleitung)](../../../anleitungen/pdf-exporte.md)
- [PDF-Export (Funktion)](../../../funktionen/6.0/pdf-export.md)
- [Dateien und digitale Sammlungen](dateien-und-digitale-sammlungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
