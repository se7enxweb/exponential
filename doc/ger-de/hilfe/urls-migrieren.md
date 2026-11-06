---
title: URLs migrieren
audience: admin, website-creator
category: website-creation
order: 445
summary: Die alten Adressen einer Website nach dem Umzug zu Exponential erhalten - sammeln, eine Zuordnungsdatei bauen, mit Vorschau und Probelauf importieren, testen, bei Bedarf rückgängig machen und für den Webserver oder als Sicherung exportieren.
keywords: URLs migrieren, Migration, Relaunch, Umzug, alte Adressen, Weiterleitung, 301, URL-Zuordnung, Zuordnungsdatei, Import, Export, rückgängig, Probelauf, Sitemap, 404, CSV, Excel, htaccess, nginx, URL-Übersetzer, URL-Platzhalter, exp:url:import, exp:url:export, migrating URLs, redirect, URL map, mapping file, dry run, undo
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

Beginnen Sie mit jeder Adresse, die die alte Website hatte, bevor Sie sie abschalten. Zwei Stellen haben sie:

- **Die Sitemap der alten Website**, meist `https://www.alte-seite.de/sitemap.xml`. Sie listet die Seiten, die die
  Website Suchmaschinen zeigen wollte. Speichern Sie die Adressen in einer Textdatei:

  ```bash
  curl -s https://www.alte-seite.de/sitemap.xml | grep -o '<loc>[^<]*</loc>' \
      | sed -e 's#<loc>##' -e 's#</loc>##' > var/tmp/old-urls.txt
  ```

- **Das Zugriffsprotokoll des Webservers**, nach dem Umstellen: die Adressen, die Besucher weiter aufrufen und die
  jetzt „404 Not Found“ antworten, die häufigsten zuerst. Mit dem üblichen Protokollformat von Apache oder nginx:

  ```bash
  awk '$9 == 404 {print $7}' /pfad/zum/access_log | sort | uniq -c | sort -rn | head -50
  ```

  Das findet, was die Sitemap nicht kennt: alte Adressen mit Abfrage (`/index.php?id=12`), Dateien und Adressen, auf
  die andere Websites verlinken. Sehen Sie einige Tage nach dem Umstellen noch einmal hinein.

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

Machen Sie aus der Liste von Schritt 1 den Anfang einer solchen Datei, ohne Host und mit leerer neuer Adresse:

```bash
{ echo 'old_url,new_url'; sed -e 's#^https\{0,1\}://[^/]*##' -e 's#$#,#' var/tmp/old-urls.txt; } > var/tmp/old-site.csv
```

Öffnen Sie sie dann in einem Tabellenprogramm und füllen Sie die Spalte `new_url` aus. Die neue Adresse einer Seite
finden Sie, indem Sie die Seite auf der neuen Website öffnen und den Pfad aus der Adresszeile kopieren, oder Sie
nehmen die Knoten-ID der Seite (`node:163`), die auch dann weiter funktioniert, wenn die Seite später verschoben wird.

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
gelesen. Fertige Beispiele: [URL-Zuordnungen als Beispiele](../beispiele/url-zuordnungen/README.md).

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

Fragen Sie direkt nach dem Import jede alte Adresse bei der Website an und sehen Sie sich die Antwort an. Eine
dauerhafte Weiterleitung antwortet mit `301` und der neuen Adresse:

```bash
while IFS=, read -r old new rest; do
    [ "$old" = old_url ] && continue
    printf '%s %s\n' "$(curl -s -o /dev/null -w '%{http_code} %{redirect_url}' "https://www.example.com$old")" "$old"
done < var/tmp/old-site.csv
```

Ersetzen Sie `https://www.example.com` durch die Adresse der neuen Website. Jede Zeile sollte mit `301` beginnen
(oder `302`, `307` bei vorübergehenden Weiterleitungen) oder mit `200` bei einem `alias`. Ein `404` heißt, dass die
Adresse fehlt oder nicht importiert wurde: Schlagen Sie sie im Plan von Schritt 3 nach. Auf der Seite URL-Platzhalter
zeigt **Eine Adresse ausprobieren**, wohin eine einzelne Adresse führt.

Sehen Sie sich die 404-Liste des Webservers aus Schritt 1 nach einigen Tagen erneut an und importieren Sie eine
zweite Datei für das, was dabei auftaucht.

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
