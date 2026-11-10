---
title: URLs migrieren
audience: admin, website-creator
category: website-creation
order: 445
summary: Die alten Adressen einer Website nach dem Umzug zu Exponential erhalten - sammeln, eine Zuordnungsdatei bauen, mit Vorschau und Probelauf importieren, testen, bei Bedarf rückgängig machen und für den Webserver oder als Sicherung exportieren.
keywords: URLs migrieren, Migration, Relaunch, Umzug, alte Adressen, Weiterleitung, 301, URL-Zuordnung, Zuordnungsdatei, Import, Export, rückgängig, Probelauf, Sitemap, 404, CSV, Excel, htaccess, nginx, URL-Übersetzer, URL-Platzhalter, exp:url:import, exp:url:export, exp:url:from-sitemap, exp:url:from-404, exp:url:suggest, exp:url:test, Ziele vorschlagen, 404-Protokoll, migrating URLs, redirect, URL map, mapping file, dry run, undo
type: help
---
# URLs migrieren

Wenn Sie eine Website zu Exponential umziehen oder ihr eine neue Struktur geben, funktionieren ihre alten Adressen
nicht mehr, solange Sie sie nicht irgendwohin weiterleiten. Links auf anderen Websites, Lesezeichen und
Suchergebnisse enden dann bei „Seite nicht gefunden“, und Suchmaschinen verlieren Seiten, die gut platziert waren.
Dieses Tutorial führt Sie durch das Erhalten dieser Adressen, von der Liste der alten Adressen bis zu einem getesteten
Import, den Sie jederzeit zurücknehmen können.

Sie brauchen ein Administratorkonto (oder eine Rolle mit `content/urltranslator_import`) und für die Schritte auf
der Kommandozeile eine Shell im Wurzelverzeichnis der Installation. Jeder Schritt geht in der Administration ebenso
wie auf der Kommandozeile.

[Hilfe](README.md) · Hintergrund: [URL-Zuordnungen importieren und exportieren](../funktionen/6.0/url-zuordnungen-importieren-und-exportieren.md) ·
Beispieldateien: [URL-Zuordnungen als Beispiele](../beispiele/url-zuordnungen/README.md)

## Inhalt

1. [Die alten Adressen sammeln](#1-die-alten-adressen-sammeln)
2. [Die Zuordnungsdatei bauen](#2-die-zuordnungsdatei-bauen)
3. [Mit Vorschau und Probelauf importieren](#3-mit-vorschau-und-probelauf-importieren)
4. [Die alten Adressen testen](#4-die-alten-adressen-testen)
5. [Einen Import rückgängig machen](#5-einen-import-rückgängig-machen)
6. [Für den Webserver oder als Sicherung exportieren](#6-für-den-webserver-oder-als-sicherung-exportieren)

## 1. Die alten Adressen sammeln

Beginnen Sie mit jeder Adresse, die die alte Website hatte, bevor Sie sie abschalten. Zwei Stellen haben sie, und
Exponential macht aus beiden den Anfang einer Zuordnungsdatei: eine Zeile je alter Adresse, die neue Adresse noch
leer.

**Die Sitemap der alten Website**, meist `https://www.alte-seite.de/sitemap.xml`, listet die Seiten, die die Website
Suchmaschinen zeigen wollte.

- In der Administration: **Setup** > **URL-Übersetzer** > **Importieren**, Reiter **Aus der alten Sitemap**. Geben
  Sie die **Adresse der Sitemap** ein, oder laden Sie die Sitemap oder eine Liste mit einer Adresse je Zeile unter
  **Oder die Sitemap oder die Liste hochladen** hoch. Klicken Sie dann auf **Als CSV herunterladen**, um die Zuordnung
  in einer Tabellenkalkulation auszufüllen, oder auf **Vorschau**, um mit dem Import weiterzumachen.
- Auf der Kommandozeile:

  ```bash
  ./console exp:url:from-sitemap https://www.alte-seite.de/sitemap.xml --output=var/tmp/old-site.csv
  ```

  Ein Sitemap-Index und gzip-komprimierte Sitemaps werden verfolgt. Nur die Adressen des eigenen Hosts der Sitemap
  bleiben, außer Sie geben `--any-host` an (im Reiter: **Auch die Adressen anderer Hosts**). `--limit=500` hört nach
  500 Zeilen auf.

**Das 404-Protokoll**, nach dem Umstellen: die Adressen, die Besucher auf der neuen Website weiter aufrufen und nicht
finden, die häufigsten zuerst. Es findet, was die Sitemap nicht kennt: Adressen, auf die andere Websites verlinken,
Dateien, alte Adressen mit Tippfehler.

- In der Administration: Reiter **Aus dem 404-Protokoll**, mit **Mindestens so viele Aufrufe** und **In den letzten
  Tagen**.
- Auf der Kommandozeile:

  ```bash
  ./console exp:url:from-404 -s site --min-hits=3 --since=30d --output=var/tmp/not-found.csv
  ```

  `-s` behält die Anfragen eines Siteaccess. `--log=<datei>` liest statt des eigenen Protokolls von Exponential ein
  Zugriffsprotokoll eines Webservers (Format common oder combined, auch `.gz`), zum Beispiel das des alten Servers vor
  dem Umstellen.

Das 404-Protokoll von Exponential (`var/log/404.log`, standardmäßig an: `site.ini [RequestLogSettings]
NotFoundLog`) verbirgt die Werte einer Abfrage als `***` und liefert daher nur die **Pfade**. Alte Adressen mit
Abfrage, etwa `/index.php?id=12`, kommen aus der Sitemap oder aus dem Zugriffsprotokoll des alten Servers (`--log`).
Bilder, Skripte, Stylesheets und Anfragen von Bots bleiben außen vor. Sehen Sie einige Tage nach dem Umstellen noch
einmal ins 404-Protokoll.

Hatte der Webserver der alten Website eigene Weiterleitungen (eine `.htaccess`-Datei oder eine nginx-Konfiguration),
heben Sie diese Datei auf: Exponential liest sie in Schritt 3 direkt.

## 2. Die Zuordnungsdatei bauen

Eine Zuordnungsdatei listet jede alte Adresse und ihre neue. Am einfachsten ist eine CSV-Datei mit zwei Spalten, die
jedes Tabellenprogramm öffnet und speichert:

```text
old_url,new_url
/about-us.html,/fitness
/recipes/hummus.html,/recipes/home-made-hummus
/index.php?id=12,/recipes
/old-recipes/*,/recipes/{1}
```

Die Datei aus Schritt 1 hat die alten Adressen; die Spalte `new_url` ist noch leer. Lassen Sie Exponential **Ziele
vorschlagen**: Es vergleicht jede alte Adresse (vor allem ihren letzten Teil, ohne Endungen wie `.html`) mit den
Adressen und Namen der Seiten der neuen Website und bewertet die besten mit 0 bis 100.

- In der Administration: Reiter **Ziele vorschlagen**. Laden Sie die Zuordnung hoch und setzen Sie **Einen Vorschlag
  eintragen, der mindestens so bewertet ist**, zum Beispiel auf 85. Die Reiter **Aus der alten Sitemap** und **Aus dem
  404-Protokoll** können gleich Ziele vorschlagen.
- Auf der Kommandozeile:

  ```bash
  ./console exp:url:suggest var/tmp/old-site.csv -s site --fill-above=85 --output=var/tmp/old-site-suggested.csv
  ```

Ein Vorschlag, der mindestens so bewertet ist (und nicht gleichauf mit einem anderen liegt), wird in `new_url`
eingetragen; die übrigen stehen in der Spalte `note` der Zeile, zum Beispiel:

```text
old_url,new_url,kind,siteaccess,language,note,status
/recipes/kale-salad.html,/recipes/kale-salad,,,,"suggested target, score 100",
/contact-us.html,,,,,suggested: /contact (70),
/hummus-recipe.php,,,,,"suggested: /recipes (55), /recipes/home-made-hummus (45)",
```

Öffnen Sie die Datei dann in einem Tabellenprogramm, prüfen Sie die eingetragenen Ziele und füllen Sie den Rest aus.
Die neue Adresse einer Seite finden Sie, indem Sie die Seite auf der neuen Website öffnen und den Pfad aus der
Adresszeile kopieren, oder Sie nehmen die Knoten-ID der Seite (`node:163`), die auch dann weiter funktioniert, wenn
die Seite später verschoben wird. Importiert wird erst in Schritt 3.

Tipps für die Zuordnung:

- **Fassen Sie zusammen, was gemeinsam umgezogen ist, in einem Platzhalter.** `/old-recipes/*` nach `/recipes/{1}`
  erfasst jedes alte Rezept, dessen letzter Teil gleich geblieben ist; dann listen Sie nur noch die, die sich geändert
  haben.
- **Lassen Sie weg, was keine neue Seite hat.** Eine Weiterleitung auf die Startseite für jede verlorene Seite
  verwirrt Besucher und Suchmaschinen; „Seite nicht gefunden“ ist die ehrliche Antwort für Inhalte, die es nicht mehr
  gibt.
- **Kennzeichnen Sie vorübergehende Umzüge.** Für eine Seite, die zurückkommt, setzen Sie die Spalte `kind` auf
  `redirect-temporary`, damit Suchmaschinen die alte Adresse behalten.
- **Alte Adressen mit Abfrage** (`/index.php?id=12`) funktionieren so, wie sie sind: Sie werden an Pfad und Abfrage
  erkannt.
- **Vollständige alte Adressen** (`https://www.alte-seite.de/about-us.html`) gehen ebenso; Sie sagen dem Import, für
  welchen Siteaccess der alte Host steht.

Die Spalten und Arten erklärt [URL-Zuordnungen importieren und exportieren](../funktionen/6.0/url-zuordnungen-importieren-und-exportieren.md#arten-und-status).
Excel (`.xlsx`), LibreOffice (`.ods`), JSON und die Apache- oder nginx-Regeln der alten Website werden ebenfalls
gelesen. Fertige Beispiele: [URL-Zuordnungen als Beispiele](../beispiele/url-zuordnungen/README.md). In der
Administration zeigt **Importieren** > **Formate und Beispiele** eine Beispieldatei jedes Formats mit den Spalten und
Arten, zum Kopieren, Herunterladen oder zum Öffnen im Import.

## 3. Mit Vorschau und Probelauf importieren

### In der Administration

1. Öffnen Sie **Setup** > **URL-Übersetzer** und klicken Sie auf **Importieren**.
2. Wählen Sie die Datei (oder fügen Sie die Zeilen ein) und den **Siteaccess für reine Pfade**: die Website, zu der
   die alten Adressen gehören. Hat die Datei vollständige Adressen des alten Hosts, tragen Sie ihn unter **Alte
   Hosts** ein, zum Beispiel `www.alte-seite.de = site`.
3. Lassen Sie beim ersten Mal **Wenn eine alte Adresse schon einen Eintrag hat** auf dem Behalten des Eintrags.
4. Klicken Sie auf **Vorschau**. Jede Zeile steht mit ihrem Zustand da. Sehen Sie sich jeden Zustand außer **Neu** an:
   - **Ziel fehlt**: Die neue Adresse gibt es nicht. Korrigieren Sie sie in der Datei und importieren Sie erneut.
   - **Konflikt**: Die alte Adresse ist eine Seite der neuen Website oder eine Adresse des Systems. Sie wird nie
     importiert und muss es auch nicht: Die Adresse funktioniert schon.
   - **Nicht importierbar**: Die Hinweise der Zeile sagen, was an ihr falsch ist.
   - **Unbekannter Host**: Ordnen Sie den Host unter **Neu planen** zu.
5. Klicken Sie auf **Probelauf**. Er prüft jede Zeile genau so gegen die Website, wie der Import es täte, und zählt,
   was er tun würde, ohne etwas zu schreiben.
6. Klicken Sie auf **Importieren** und bestätigen Sie Ihre Identität („Identität bestätigen“). Der Import bekommt
   eine Nummer; Sie brauchen sie, um ihn rückgängig zu machen.

Die Seiten im Einzelnen beschreibt [URL-Zuordnungen: Import, Export und Rückgängig](../anleitungen/url-zuordnungen.md).

### Auf der Kommandozeile

```bash
./console exp:url:import var/tmp/old-site.csv -s site --dry-run --report=var/tmp/plan.csv
```

Das plant jede Zeile, schreibt nichts, gibt die Zahlen und die Zeilen aus, die nicht importiert würden, und schreibt
den ganzen Plan nach `var/tmp/plan.csv`, zum Öffnen in einem Tabellenprogramm. `-s` ist der Siteaccess, zu dem die
reinen Pfade gehören. Korrigieren Sie die Datei und wiederholen Sie den Probelauf, bis nur noch die Zeilen fehlen, die
Sie erwarten. Dann importieren Sie:

```bash
./console exp:url:import var/tmp/old-site.csv -s site
```

Die Regeln einer alten Website geben Sie unverändert an: `./console exp:url:import old-site.htaccess -s site`. Hat
die Datei vollständige Adressen eines Hosts, der nicht zu dieser Installation gehört, fügen Sie
`--host=www.alte-seite.de=site` hinzu.

## 4. Die alten Adressen testen

Fragen Sie direkt nach dem Import jede alte Adresse bei der Website an, folgen Sie ihren Weiterleitungen und prüfen
Sie, dass sie mit der richtigen Art von Weiterleitung auf ihrer neuen Adresse ankommt.

- In der Administration: Die Seite mit dem Ergebnis des Imports hat den Abschnitt **Die alten Adressen prüfen**.
  Klicken Sie auf **Prüfen**: Es fragt die alten Adressen (die ersten 500) unter der Website-Adresse des Siteaccess
  (`site.ini [SiteSettings] SiteURL`) an und listet die Zeilen, die nicht angekommen sind. **Erneut prüfen**
  wiederholt das nach einer Korrektur.
- Auf der Kommandozeile, gegen jede Adresse, auch gegen eine Staging-Website vor dem Umstellen:

  ```bash
  ./console exp:url:test var/tmp/old-site.csv --base=https://www.example.com -s site --report=var/tmp/test.csv
  ```

  ```text
  URL map test of var/tmp/old-site.csv on https://www.example.com: 3 rows in 1.2 s
    2 landed, 0 wrong-status, 0 wrong-target, 1 not-redirected, 0 error, 0 skipped
    not-redirected line 4     /recipes.html -> /recipes: answered 404 without a redirect
  ```

Jede Zeile ist **landed** (angekommen), **wrong-status** (dauerhaft, wo eine vorübergehende Weiterleitung erwartet
war, oder umgekehrt), **wrong-target** (falsche Seite), **not-redirected** (nicht weitergeleitet) oder **error**;
Zeilen ohne neue Adresse werden übersprungen, ein Platzhalter wird mit einer Beispieladresse geprüft. Der Befehl endet
mit dem Exit-Code 1, wenn eine Zeile nicht angekommen ist, und eignet sich so auch für ein Deployment-Skript.
`--insecure` nimmt das selbst signierte Zertifikat eines Testhosts an. Eine Zeile, die nicht angekommen ist, fehlt
oder wurde nicht importiert: Schlagen Sie sie im Plan von Schritt 3 nach. Auf der Seite URL-Platzhalter zeigt **Eine
Adresse ausprobieren**, wohin eine einzelne Adresse führt.

Nehmen Sie nach einigen Tagen erneut **Aus dem 404-Protokoll** aus Schritt 1 und importieren Sie eine zweite Datei
für das, was dabei auftaucht.

## 5. Einen Import rückgängig machen

Ein Import lässt sich jederzeit als Ganzes zurücknehmen:

- In der Administration: **Setup** > **URL-Übersetzer** > **Importhistorie**, den Import öffnen und auf **Import
  rückgängig machen** klicken.
- Auf der Kommandozeile: `./console exp:url:batches` listet die Importe mit ihren Nummern;
  `./console exp:url:undo 3` macht Import 3 rückgängig.

Rückgängig machen entfernt jeden Eintrag, den der Import angelegt hat, und stellt jeden ersetzten Eintrag wieder her.
Ein Eintrag, der nach dem Import von Hand geändert wurde, bleibt erhalten; haken Sie **Auch die nach dem Import
geänderten Einträge zurücknehmen** an (`--force` auf der Kommandozeile), um auch ihn zurückzunehmen.

## 6. Für den Webserver oder als Sicherung exportieren

Die URL-Zuordnung der Website lässt sich wieder herausschreiben, in jedem Format, das der Import liest:

- In der Administration: **Setup** > **URL-Übersetzer** > **Exportieren**, das Format wählen und die Datei
  herunterladen.
- Auf der Kommandozeile:

  ```bash
  ./console exp:url:export --output=var/tmp/url-map.xlsx
  ./console exp:url:export --kind=wildcard,redirect --format=nginx > var/tmp/redirects.nginx
  ```

Nutzen Sie einen Export:

- **als Sicherung** vor einem großen Import oder einer Änderung der Struktur der Website;
- **zum Bearbeiten** der Zuordnung in einer Tabelle, um sie wieder zu importieren (mit Ersetzen der vorhandenen
  Einträge);
- **für den Webserver**: Die Apache- und nginx-Exporte sind Regeln, die die Weiterleitungen beantworten, bevor PHP
  läuft. Einträge, die nur Exponential beantworten kann (Aliase, Ziele `node:`), werden als `#exp:`-Kommentare
  geschrieben, die der Server übergeht.

## Verwandte Seiten

- [URL-Zuordnungen importieren und exportieren](../funktionen/6.0/url-zuordnungen-importieren-und-exportieren.md)
- [URL-Zuordnungen als Beispiele](../beispiele/url-zuordnungen/README.md)
- [URL-Zuordnungen: Import, Export und Rückgängig](../anleitungen/url-zuordnungen.md)
- [Wie leite ich eine Gruppe alter Adressen um (URL-Platzhalter)?](handbuch/wie-leite-ich-alte-adressen-um.md)
- [URL-Platzhalter](ersteller/website-ersteller/url-platzhalter.md)
- [Weiterleitungen und verschobene Inhalte](ersteller/website-ersteller/weiterleitungen-und-verschobene-inhalte.md)
- [Die Dateiformate für URL-Zuordnungen](../spezifikationen/6.0/url-zuordnungsdateiformate.md)
