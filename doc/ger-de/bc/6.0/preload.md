# Vorlader für den Website-Cache: `bin/php/preload.php`

> **Seit dem 6. Oktober 2026** führt der Befehl denselben Crawler aus wie Setup > Seiten vorladen, mit der
> curl-Erweiterung von PHP statt `wget` und dem Programm `curl`, mit einem Lauf zur Zeit, und er verzeichnet jeden
> Lauf. Was im Folgenden über `wget` steht, beschreibt die frühere Version. Die aktuelle Anleitung ist
> [Caches vorwärmen](../../anleitungen/caches-vorwaermen.md).

Lesen Sie diese Seite, wenn Sie beim Deploy Caches leeren und möchten, dass die ersten Besucher schnelle Seiten
erhalten. `bin/php/preload.php` (auch `php bin/php/console exp:preload --apply`, neu in Exponential 6.0.15) ruft Ihre Seiten
ab, bevor ein Besucher es tut, sodass die Seiten-Caches warm sind, und meldet jeden defekten Link mit den Seiten, die
auf ihn verweisen.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Neues Skript `bin/php/preload.php`: Phase 1 ruft die Seiten der Bereiche mit `curl` ab, Phase 2 durchsucht die Website mit `wget` (Tiefe 3), danach ein Bericht über defekte Links. Derselbe Bericht wird in **Setup → Seiten vorladen** angezeigt, das im Hintergrund läuft. Links, die nur aus einem Fragment bestehen (`#main`), werden nicht mehr als Seiten angefragt. |
| Wer betroffen ist | Niemand muss etwas ändern. Betreiber, die nach einem Deploy oder dem Leeren eines Caches warme Caches möchten. |
| Wie Sie es prüfen | `php bin/php/console list exp \| grep preload` |
| Wie Sie es beheben | Nichts zu beheben. Führen Sie es nach `exp:ezcache --clear-all` oder per Cron aus. |

Führen Sie es aus:

- nach dem Leeren aller Caches (`exp:ezcache --clear-all`);
- nach einem Deployment oder einem Code-Update;
- nach einem geplanten Ablauf des Caches;
- per Cron, um eine stark besuchte Website warm zu halten.

## Voraussetzungen

Führen Sie alle Befehle im **Wurzelverzeichnis von Exponential CMS** aus:

```bash
# You should see index.php in the listing
ls index.php
```

Sie brauchen:

| Werkzeug | Prüfen mit | Warum |
|---|---|---|
| PHP 8.0+ (7.4 ohne Support nutzbar) | `php -v` | Führt das Skript aus |
| curl | `curl --version` | Phase 1 — ruft die Seiten der Bereiche ab |
| wget | `wget --version` | Phase 2 — durchsucht die ganze Website |

Ihre Exponential-Website muss **laufen und erreichbar sein**, über HTTP/HTTPS. Das Skript liest `SiteURL` aus
`settings/site.ini`, um zu wissen, wohin es sich verbinden soll.


## Schnellstart

```bash
# Warm caches for the default siteaccess configured in site.ini
php bin/php/preload.php --apply

# Or run it through the console (identical result)
php bin/php/console exp:preload --apply
```

Mehr brauchen die meisten Websites nicht.


## Einen Siteaccess angeben

Ein Siteaccess ist ein benanntes Konfigurationsprofil in Exponential CMS (zum Beispiel eine Website für Benutzer im
Frontend, eine Administration oder eine mobile Website). Haben Sie mehr als einen, können Sie gezielt einen ansprechen:

```bash
php bin/php/preload.php --apply --siteaccess=sevenx_site_user

# Same command via the console dispatcher
php bin/php/console exp:preload --apply --siteaccess=sevenx_site_user
```

Sie wissen nicht, wie Ihre Siteaccesses heißen? Prüfen Sie:

```bash
grep AccessPath settings/siteaccess.ini
# or look in
ls settings/siteaccess/
```


## Was das Skript tut — Schritt für Schritt

### Phase 1 — Seiten der Bereiche vorwärmen

Das Skript liest `[SiteSettings] URLTranslationKeyword` aus `site.ini`, um eine Liste von URLs der Bereiche zu bilden
(zum Beispiel `/articles/`, `/products/`, `/blog/`). Jede URL wird einmal mit `curl` abgerufen. Für jede Seite gibt es
aus:

```
  [1/4]  https://example.com/articles/
         [ 200 OK ]  wall: 312ms  ok  size: 84.2 kB
         db        queries: 14  time: 28ms
         kernel    init: 42ms  run: 245ms  get-content: 190ms  total: 310ms
         memory    peak: 18.4 MB
```

- **200 OK** — Seite erfolgreich abgerufen und gecacht (grün)
- **3xx** — Weiterleitung (cyan)
- **4xx** — nicht gefunden oder Zugriff verweigert (gelbe Warnung)
- **5xx** — Serverfehler (rot, die ersten Fehlerzeilen werden direkt angezeigt)

Die **wall**-Zeit ist, wie lange Ihr Browser auf diese Seite gewartet hätte. Nach dem ersten Abruf sinkt diese Zahl
deutlich, weil der Cache warm ist.

### Phase 2 — Die ganze Website durchsuchen

Das Skript startet `wget` im Spider-Modus (es besucht Seiten, speichert sie aber nicht) mit der Rekursionstiefe 3,
beginnend beim Wurzelverzeichnis Ihrer Website. Das berührt jede Seite, auf die Ihre Startseite verweist, bis zu drei
Ebenen tief.

Für jede durchsuchte URL:

```
  ✓  /articles/my-post                 14.2 kB  #47
  ▸  print  /articles/my-post          3.1 kB   #48
  ⬇  download  /content/download/…           #49
```

| Symbol | Bedeutung |
|---|---|
| `✓` | Gewöhnliche Inhaltsseite — vollständig gecacht |
| `↻` | Seite über eine Kette von Weiterleitungen ausgeliefert |
| `▸ print` | Druckvariante (`/layout/set/print/…`) |
| `⬇ download` | Herunterladen einer Binärdatei (`/content/download/…`) |
| `⊘` | Durch Anmeldung geschützte Ressource — übersprungen (zusammengefasst) |
| `✗ BROKEN LINK` | Link auf einer Seite gefunden, aber das Ziel lieferte einen Fehler |

### Abschließende Zusammenfassung

```
  ┌─────────────────────────────────────────┐
  │  Pages cached      204                  │
  │  Auth skipped        3 resources        │
  │  Broken links        1                  │
  └─────────────────────────────────────────┘
```


## Die Ausgabe lesen

### Geschwindigkeitsstufen der Seiten

Die Spalte **wall** zeigt, wie schnell eine Seite vor dem Caching geantwortet hat:

| Farbe | Bezeichnung | Zeit | Was es bedeutet |
|---|---|---|---|
| Grün | `blazing` | < 200 ms | Ausgezeichnet — bereits gecacht oder ohnehin schnell |
| Grün | `fast` | 200–400 ms | Gut — klar im akzeptablen Bereich |
| Gelb | `ok` | 400–700 ms | Akzeptabel — mit Luft nach oben |
| Gelb | `slow` | 700–1200 ms | Braucht Aufmerksamkeit |
| Rot | `SLOW!` | > 1200 ms | Untersuchen Sie die Cache-Konfiguration oder die Abfragen |

Nach Abschluss des Vorladens sollte ein erneuter Abruf jeder dieser Seiten durchgehend `blazing` oder `fast` zeigen,
weil das gerenderte HTML jetzt gecacht ist.

### Defekte Links

Ein defekter Link bedeutet, dass eine Ihrer Seiten einen Hyperlink enthält, dessen Ziel nicht existiert oder einen
Fehler liefert. Es lohnt sich, sie zu beheben, aber sie verhindern das Caching nicht — der Rest der Website wird
trotzdem vollständig vorgewärmt.

#### Der Bericht über defekte Links

Zu wissen, dass `/careers` fehlt, ist nur die Hälfte dessen, was ein Redakteur braucht. Die andere Hälfte ist,
**welche Seiten darauf verweisen**, denn das sind die Seiten, die geöffnet und korrigiert werden müssen.

Jeder Lauf endet daher mit einem Bericht: jedes fehlende Ziel einmal, mit der vollständigen Adresse jeder Seite, die
darauf verweist.

```
1 broken link on 4 pages of this site.

Missing pages (404) (1)

  https://www.example.com/bold_eng/careers?nekisufiks
      linked from 4 pages:
        https://www.example.com/
        https://www.example.com/authors/tom-horvat
        https://www.example.com/authors/jane-smith
        https://www.example.com/authors/john-doe

Open each page listed under a broken link, correct the link, then run this again.
```

Die Adressen sind absolut, sodass sie unverändert in einen Browser eingefügt werden können. Fehler werden nach Art
gruppiert — `Missing pages (404)` getrennt von `Server errors (500)` und von `No response` —, weil es verschiedene
Probleme für verschiedene Personen sind.

Jede Zeile im laufenden Log nennt außerdem ihre Quelle:

```
/bold_eng/careers  404  linked from / and 3 other pages
```

Pro defektem Ziel werden höchstens **20** verweisende Seiten aufgeführt, mit der Zahl der übrigen. Ein defekter Link in
einem Template oder einem Menü der Website erscheint auf jeder Seite der Website, und viertausend davon aufzulisten,
hilft niemandem — die ersten zeigen das Muster.

In **Setup → Seiten vorladen** wird derselbe Bericht unter der Konsole als Tabelle anklickbarer Links dargestellt,
sodass ein defektes Ziel und seine Seiten in neuen Tabs geöffnet und behoben werden können, ohne etwas abzuschreiben.
Der reine Text bleibt auch in der Konsole, sodass er mitkommt, wenn man das ganze Log markiert.

#### Fragmente sind keine Seiten

Bisher meldete jede Seite der Website einen oder zwei defekte Links, die es nicht gab:

```
/main        404
/debug-end   404
/fitness/main       404
/tags/view/Topics/main  404
```

Eine solche Seite gibt es nicht und auch keinen Link zu beheben. Der Crawler schnitt das Fragment mit
`strtok( $href, '#' )` von einem `href` ab, und `strtok` überspringt *führende* Trennzeichen: Aus `#main` wird `main`,
keine leere Zeichenkette. So wurde der Sprunglink, den jede Seite trägt — `<a href="#main">` —, zu einem relativen
Link, der gegen die gerade gelesene Seite aufgelöst und als Seite angefragt wurde.

Fragmente werden jetzt mit `strpos`/`substr` abgeschnitten, das das leere Ergebnis behält, und ein `href`, das nur aus
einem Fragment besteht, wird wie jedes andere leere verworfen. Das entfernt außerdem eine ganze Klasse sinnloser
Anfragen aus einem begrenzten Lauf: Bei einem Budget von 250 Seiten verbrauchte der Crawler pro Seite eine Anfrage für
eine Adresse, die nie existieren konnte.


## Per Cronjob ausführen

Um Caches dauerhaft warm zu halten, führen Sie den Vorlader nach einem Zeitplan aus. Bearbeiten Sie Ihre Crontab:

```bash
crontab -e
```

Fügen Sie eine Zeile wie diese hinzu (läuft alle 6 Stunden, protokolliert die Ausgabe):

```
0 */6 * * * cd /path/to/exponential-root && php bin/php/preload.php --apply --siteaccess=sevenx_site_user >> /var/log/ezpreload.log 2>&1
```

Ersetzen Sie `/path/to/exponential-root` durch den tatsächlichen Pfad zu Ihrer Website und `sevenx_site_user` durch
den Namen Ihres Frontend-Siteaccess.


## Nach dem Leeren eines Caches ausführen

Der häufigste Ablauf:

```bash
# 1. Clear all caches
php bin/php/console exp:ezcache --clear-all --allow-root-user

# 2. Immediately re-warm them
php bin/php/console exp:preload --apply --siteaccess=sevenx_site_user --allow-root-user
```


## Tipps

**In eine Log-Datei umleiten** (nützlich in CI/CD oder bei unbeaufsichtigten Läufen):

```bash
php bin/php/preload.php --apply --siteaccess=sevenx_site_user > var/log/preload.log 2>&1
```

Farben werden automatisch unterdrückt, wenn die Standardausgabe kein Terminal ist — die Log-Datei enthält sauberen,
reinen Text.

**Nur defekte Links** aus einem früheren Lauf anzeigen:

```bash
grep "BROKEN LINK" var/log/preload.log
```

**Nur Phase 1 ausführen** (nur Seiten der Bereiche, kein vollständiges Durchsuchen):  
Dafür gibt es noch keine eigene Option. Um Phase 2 zu überspringen, unterbrechen Sie das Skript nach Abschluss von
Phase 1 mit **Strg+C** — die Ausgabe von Phase 1 wird sofort geschrieben, sodass keine Arbeit verloren geht.


## Fehlerbehebung

**`Cannot determine site URL: SiteSettings.SiteURL is not set`**  
Öffnen Sie `settings/override/site.ini.append.php` (oder das Gegenstück für Ihren Siteaccess) und stellen Sie sicher,
dass `[SiteSettings]` eine Zeile `SiteURL` enthält:

```ini
[SiteSettings]
SiteURL=example.com
```

**`Failed to launch wget — is it installed and on $PATH?`**  
Installieren Sie wget:

```bash
# Debian / Ubuntu
apt-get install wget

# RHEL / CentOS / Rocky
yum install wget
```

**`Running scripts as root may be dangerous`**  
Das Startsystem von Exponential warnt, wenn Sie als `root` arbeiten. Hängen Sie `--allow-root-user` an, um das zu
bestätigen:

```bash
php bin/php/preload.php --apply --allow-root-user
```

**Phase 2 liefert nur wenige URLs**  
Prüfen Sie, dass `wget` Ihre Website vom Server aus wirklich erreichen kann und dass keine Firewall-Regel ausgehende
HTTP/HTTPS-Verbindungen vom Webserver selbst blockiert:

```bash
curl -I https://your-site.com/
wget --spider https://your-site.com/
```

**Viele Zeilen `⊘ auth-protected`**  
Seiten hinter HTTP-Basic-Auth oder dem Anmeldesystem von Exponential lassen sich ohne Zugangsdaten nicht durchsuchen.
Der Vorlader überspringt sie sauber. Um Seiten mit Anmeldung vorzuwärmen, müssten Sie die Anmeldung gesondert
behandeln (das geht über dieses Skript hinaus).


## Technische Details

- **Phase 1** verwendet `curl` mit `-sk` (still, SSL-Zertifikatsfehler ignorieren) und erfasst `http_code`,
  `time_total` und `size_download` direkt. Es liest außerdem die optionalen HTML-Kommentare `X-Debug-*` von
  Exponential für Kernel-Zeiten und Datenbankkennzahlen aus, wenn Ihre Website sie ausgibt.
- **Phase 2** verwendet `wget --spider --recursive --level=3`, das Seiten besucht, ohne sie zu speichern. Die Ausgabe
  wird mit `popen()` zeilenweise gelesen, sodass der Fortschritt in Echtzeit erscheint statt erst nach dem ganzen
  Durchlauf.
- Binärdateien (Bilder, Schriften, Videos, PDFs, Archive) werden über `--reject` vom Durchsuchen mit wget
  ausgeschlossen, sodass sich der Durchlauf auf HTML-Seiten konzentriert.
- Zeilen zu geschützten Ressourcen (`Username/Password Authentication Failed`) werden zu einer einzigen
  zusammenfassenden Zeile zusammengelegt, statt das Terminal zu überfluten.
- Jede PHP-Ausgabe wird sofort geschrieben (`flush()`), sodass Sie den Fortschritt auch in einer lang laufenden
  Phase 2 in Echtzeit sehen.

## Verwandte Seiten

- [Seiten vorladen: läuft im Hintergrund](../../funktionen/6.0/ansicht-websites-vorladen.md#es-läuft-im-hintergrund) (28. September 2026)
- [Verhaltensänderungen, 16. bis 30. September 2026](verhaltensaenderungen-2026-09b.md)
- [Generator des statischen Caches](../../funktionen/6.0/generator-fuer-den-statischen-cache.md)
- [HTTP/2 und das Vorwärmen von Caches](http2-und-cache-vorwaermen.md)
- [Exponential-Konsole](konsole.md)
