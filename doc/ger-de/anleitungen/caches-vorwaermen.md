# Caches vorwärmen: eine Site aufwärmen, bevor Besucher kommen

Diese Anleitung erklärt das Vorladen von Caches in Exponential: **Setup > Vorladen** (`/setup/preload`) und den Befehl
`php bin/php/preload.php`. Beide sind derselbe Lauf. Er ruft die Seiten einer Site so ab, wie es ein anonymer Besucher
täte, damit die Caches warm sind, bevor der erste Besucher kommt, und er listet die defekten Links mit den Seiten auf,
die auf sie verweisen.

Sie richtet sich an Administratoren und Betreiber. Jede Seite, jedes Feld, jede Option und jede Datei unten wurde am
6. Oktober 2026 am Code dieses Repositorys geprüft; die Dateien sind unter [Quellen](#quellen) genannt.

[Anleitungen](README.md) · Siehe auch: [Betrieb einer Site](betrieb-einer-website.md) · [Cronjobs](cronjobs.md) ·
[Bereitstellung](bereitstellung.md) · [Vorladen des Site-Caches (Upgrade-Hinweise)](../../eng-us/bc/6.0/preload.md)

## Kurz gesagt

- Ein Lauf beginnt bei den **Bereichsseiten** einer Site (ihrer Wurzel und den Bereichen aus `site.ini [SiteSettings]
  URLTranslationKeyword`) und folgt dann den Links der Site, bis zu einem **Seitenlimit** und einer **Link-Tiefe**.
- Er wärmt vor, was das Rendern einer Seite erzeugt: den **Seitenansichts-Cache**, die **Bildvarianten**, die die Seite
  zeigt, und die **kompilierten Templates**. Das sind Dateien der Installation, die Apache mit PHP-FPM und Exponential
  Velocity gemeinsam nutzen.
- **Immer nur ein Lauf.** Ein Lauf hält eine Sperre, solange er läuft, ob er von der Seite, der Shell oder per Cron
  gestartet wurde. Ein zweiter wird abgewiesen und sagt das.
- Von der Seite aus läuft ein Lauf **nie innerhalb der Web-Anfrage**: Er wird im Hintergrund gestartet, und die Seite
  verfolgt ihn.
- Jeder Lauf wird **protokolliert**: Dauer, vorgewärmte Seiten, erzeugte Bildvarianten, defekte Links mit ihren
  Adressen, Statuscodes und den Seiten, die auf sie verweisen. Die Seite listet die letzten Läufe auf, gleich ob sie
  von der Seite, aus der Shell oder per Cron kamen.
- Ein **Probelauf** zeigt die Adresse, die Startseiten und die Grenzen und ruft nichts ab.

## 1. Die Seite

Öffnen Sie **Setup > Seiten vorladen** (`/setup/preload`). Die Seite enthält von oben nach unten:

| Teil | Was er zeigt |
|---|---|
| Statusleiste | **Läuft** mit der Site, wann und von wo aus der Lauf gestartet wurde, einem Fortschrittsbalken (vorgewärmte Seiten vom Seitenlimit), den bisherigen defekten Links und der gerade abgerufenen Adresse; oder **Bereit** mit dem letzten Lauf und wie er endete. **Anhalten** beendet einen Lauf nach der Seite, die er gerade abruft. |
| Ausgabe | Während ein Lauf andauert, seine Ausgabe Zeile für Zeile, dann die Zahlen des Laufs (vorgewärmte Seiten, erzeugte Bildvarianten, defekte Links, geprüfte Bilder, Sekunden) und die Tabelle der defekten Links. |
| Vorladen starten | Das Formular: Site, Seitenlimit, Link-Tiefe, was vorgewärmt wird, **Vorladen starten** und **Probelauf**. |
| Probelauf | Nach **Probelauf**: der Siteaccess, die Adresse, das Präfix, das den Siteaccess auswählt, die Grenzen, die Startseiten und derselbe Befehl für die Shell. |
| Welche Caches vorgewärmt werden | Welcher Server die Anfragen des Laufs beantwortet und was das für Apache/PHP-FPM und Velocity bedeutet. |
| In der Shell oder per Cron | Der Befehl für die Auswahl im Formular, eine Crontab-Zeile und wo Läufe aufbewahrt werden. |
| Letzte Läufe | Die letzten zehn Läufe, die neuesten zuerst. Ein Lauf mit Fehlern hat eine Zeile, die sich zu den fehlgeschlagenen Adressen, ihren Statuscodes und den Seiten öffnet, die auf sie verweisen. |

Die Seite funktioniert ohne JavaScript: Starten, Probelauf und Anhalten senden das Formular, und **Aktualisieren**
zeigt, wie ein Lauf steht. Mit JavaScript verfolgt die Seite einen Lauf von selbst, einmal pro Sekunde, und Sie können
sie verlassen und zurückkehren: Ein laufender Lauf wird wieder aufgegriffen, wenn die Seite geöffnet wird.

### Das Formular

| Feld | Standard | Bedeutung |
|---|---|---|
| Vorzuwärmende Website | der erste aus `RelatedSiteAccessList` | Die Siteaccesses aus `site.ini [SiteAccessSettings] RelatedSiteAccessList`, ohne den, aus dem die Seite ausgeliefert wird (ein Siteaccess der Administration wärmt nichts vor, was ein Besucher sieht). Jeder wird mit der Adresse angezeigt, bei der der Lauf beginnt. |
| Seitenlimit | 250 | Die meisten Seiten, die ein Lauf abruft, 1 bis 5000. Ein Lauf, der es erreicht, sagt, wie viele Adressen noch in der Warteschlange standen. |
| Link-Tiefe | 3 | Wie viele Links ab einer Startseite verfolgt werden, 0 bis 10. 0 ruft nur die Startseiten ab. |
| Auch die Bilder jeder Seite prüfen | aus | Nach den Seiten wird jedes Bild, das die vorgewärmten Seiten zeigen, einmal abgerufen (eine `HEAD`-Anfrage), und die fehlenden werden mit den Seiten aufgeführt, die sie zeigen. |

Nur ein Siteaccess, den die Installation ausliefert, kann gewählt werden, und die Grenzen sind ganze Zahlen innerhalb
ihres Bereichs; sonst gelangt nichts aus dem Formular in eine Kommandozeile. Der Befehl wird mit einer Argumentliste
gestartet, nie über eine Shell.

## 2. Was ein Lauf vorwärmt, und auf welchem Server

Ein Lauf ruft jede Seite unter der **Adresse der Site** ab: `SiteURL` des Siteaccess, mit dem Präfix, das auf diesem
Host den Siteaccess auswählt (Abschnitt 4). Der Server, der diese Adresse beantwortet, rendert die Seite: auf dem
Demonstrationsserver Apache mit PHP-FPM.

- Der Seitenansichts-Cache (`var/<var dir>/cache/content`), die Bildvarianten (`var/<var dir>/storage/images`) und die
  kompilierten Templates sind Dateien der Installation. Apache/PHP-FPM und Velocity nutzen sie gemeinsam, daher wärmt
  ein Lauf sie für beide vor, gleich welcher Server antwortet.
- Exponential Velocity hat einen eigenen Antwort-Cache vor seinen Workern. Er behält eine anonyme Seite
  `velocity.ini [CacheSettings] DefaultTtl` Sekunden lang (standardmäßig 30), daher hält ein Vorwärmen vor den
  Besuchern nicht an. Die Seite sagt, wenn Velocity läuft und wie lange sein Cache eine Seite behält.
- Seiten für angemeldete Benutzer werden nicht vorgewärmt: Der Lauf ist ein anonymer Besucher.
- Nur das eigene Markup der Seite wird gelesen: Links und Bilder innerhalb von `<script>`, `<template>`, `<style>` oder
  Kommentaren sowie Werte, die ein Anführungszeichen, einen JavaScript- oder Template-Ausdruck oder Leerraum enthalten,
  sind keine Adressen und werden übersprungen.
- Anfragen an Adressen außerhalb der Site werden nie gestellt: Links zu einem anderen Host oder zu einem anderen
  Siteaccess auf demselben Host werden nicht verfolgt, ebenso wenig Modul-Views (`/content/edit`, `/setup`,
  `/user/login` ...), das eigene Panel von Velocity (`/Q/...`) oder Dateien
  (Bilder, Skripte, Dokumente).

**Erzeugte Bildvarianten** ist die Zahl der Bilddateien, die während des Laufs im Speicher erschienen sind: die
Varianten, die die gerenderten Seiten erzeugt haben. Sie wird auf der Festplatte gezählt, daher wird sie bei einem
geclusterten Datei-Handler (`file.ini [ClusteringSettings] FileHandler` anders als `eZFSFileHandler` oder
`eZFS2FileHandler`) als *nicht gezählt* angezeigt. Eine Site, die bereits warm war, erzeugt keine.

## 3. Aus der Shell und per Cron

```bash
# what a run would do, without requesting anything
php bin/php/preload.php --siteaccess=site --dry-run

# warm the site of the siteaccess "site"
php bin/php/preload.php --siteaccess=site

# a small run: the starting pages and the pages they link to, at most 20
php bin/php/preload.php --siteaccess=site --max-pages=20 --max-depth=1

# with the image check
php bin/php/preload.php --siteaccess=site --images
```

| Option | Bedeutung |
|---|---|
| `--siteaccess=<name>` | Der Siteaccess, dessen Site vorgewärmt wird (die Standardoption jedes Befehls). |
| `--target=<name>` | Ein anderer Siteaccess als der, unter dem der Befehl läuft. |
| `--max-pages=<n>` | Das Seitenlimit, aus der Shell standardmäßig **1000** (250 auf der Seite), höchstens 5000. |
| `--max-depth=<n>` | Die Link-Tiefe, standardmäßig 3. |
| `--images` | Auch die Bilder prüfen, die die vorgewärmten Seiten zeigen. |
| `--dry-run` | Die Adresse, die Startseiten und die Grenzen ausgeben; nichts abrufen. |
| `-q` | Nichts ausgeben. Der Lauf wird trotzdem protokolliert und auf der Seite aufgeführt. |

Der Befehl gibt jede Seite aus, während sie abgerufen wird, und am Ende die defekten Links. Seine **letzten drei
Zeilen** sagen, was der Lauf getan hat, für ein Protokoll, das nur sein Ende behält:

```
Preload finished in 182.4s: run 1c9f49b30d7a51ac, listed in Setup > Preload.
Pages warmed 167, broken 0, denied 0, skipped 3.
Image aliases made 480.
```

Exit-Codes: 0, wenn der Lauf beendet oder angehalten wurde (defekte Links sind ein Befund, kein Fehler), 1, wenn er
fehlgeschlagen ist (keine `SiteURL`, ein unbekannter Siteaccess, ein Fehler), 2, wenn bereits ein anderes Vorladen
lief und dieses nicht gestartet wurde.

Für Cron, zum Beispiel nach dem nächtlichen Leeren der Caches:

```
30 4 * * * cd /path/to/exponential && php bin/php/preload.php --siteaccess=site -q
```

Die Seite zeigt diese Zeile für die Auswahl in ihrem Formular. Der Befehl braucht die PHP-Kommandozeile und die
curl-Extension; er braucht weder `wget` noch das Programm `curl` mehr und schreibt nichts nach `/tmp`.

## 4. Die Adresse eines Siteaccess

Ein Lauf muss den gewählten Siteaccess erreichen, nicht nur dessen Host. Die Adresse wird allein aus `site.ini`
ermittelt, so wie eine Anfrage zugeordnet wird (`expPreloadAddress`):

1. Die Basis ist `SiteURL` des Siteaccess (seine eigene `settings/siteaccess/<name>/site.ini.append.php`, sonst die
   globale), mit vorangestelltem `https://`, wenn sie kein Schema hat.
2. Zuerst wird die Adresse ohne Präfix versucht. Dann jedes Präfix, das die Zuordnung anbietet: `/<name>` für
   `URIMatchType=element`, die URI eines Eintrags `URIMatchMapItems[]` für `URIMatchType=map`, die URI eines Eintrags
   `HostUriMatchMapItems[]`.
3. Jede Adresse wird so zugeordnet, wie es eine Anfrage würde: der Reihe nach die Methoden aus
   `[SiteAccessSettings] MatchOrder`, dann `DefaultAccess`. Die erste Adresse, die den Siteaccess erreicht, wird
   verwendet.

Einträge der Host-Zuordnung werden mit ihrem optionalen dritten Feld gelesen, `HostMatchMapItems[]=host;siteaccess;method`,
wobei die Methode `strict`, `start`, `end` oder `part` ist, und `HostMatchMethod` (standardmäßig `strict`) gilt für einen
Eintrag ohne Methode. So schickt `example.com;site;start` einen Testhost `example.com.test.local` für das Vorladen
genauso zu `site` wie für einen Besucher.

Erreicht keine Adresse den Siteaccess, verwendet der Lauf wie bisher `/<name>` (wenn `uri` in `MatchOrder` steht), und
der Probelauf und die Ausgabe warnen, dass die vorgewärmten Seiten die eines anderen Siteaccess sein können. Prüfen Sie
die `SiteURL` und die Zuordnungseinstellungen dieses Siteaccess.

Eine `SiteURL`, die bereits auf das Präfix endet (`demo.example/site`), erhält es nicht noch einmal. Die Site, in der
ein Lauf bleibt, ist alles unterhalb des Pfads von `SiteURL` plus Präfix (dort `/site/...`, `/bold/...` für ein
URI-Präfix, der ganze Host außer den Präfixen anderer Siteaccesses für eine über den Host zugeordnete Site); Links zu
`/admin/...` und zu anderen Siteaccesses werden nicht verfolgt.

Die Bereichsseiten aus Phase 1 sind die Wurzel, die Bereiche aus `URLTranslationKeyword` und die Seiten der obersten
Ebene, auf die die Startseiten verweisen (`/site/fitness`, `/fitness`).

## 5. Läufe, die Sperre und die Dateien

Läufe werden in `var/<var dir>/preload/` aufbewahrt:

| Datei | Inhalt |
|---|---|
| `<id>.json` | Der Status des Laufs: Zustand, Siteaccess, Adresse, Grenzen, Prozess, Start und Ende, Dauer, Zähler, erzeugte Bildvarianten, die gerade abgerufene Adresse, die ersten 50 Fehler. |
| `<id>.jsonl` | Die Ereignisse des Laufs, je eine JSON-Zeile: das, was die Ausgabe zeigt. |
| `<id>.stop` | Fordert den Lauf zum Anhalten auf; sie wird nach jeder Anfrage geprüft. |
| `run.lock` | Die Sperre. Ein Lauf hält eine exklusive Sperre darauf, solange er läuft. |

Die letzten 20 Läufe werden aufbewahrt; ein laufender Lauf wird nie entfernt. Die Sperre hängt am Prozess: Ein Lauf,
der abstürzt, hinterlässt keine Sperre, und nichts muss von Hand entfernt werden. Ein Lauf, dessen Prozess
verschwunden ist, wird als **Ohne Abschluss beendet** aufgeführt, und einer, dessen Prozess nie gestartet ist, nach
einer Minute als **Fehlgeschlagen**.

Zustände: **Startet**, **Läuft**, **Abgeschlossen**, **Angehalten** (von der Seite aus), **Fehlgeschlagen** (mit dem
Grund) und **Ohne Abschluss beendet**.

## 6. Zugriff

Geben Sie einer Rolle die Richtlinie **setup / preload**. Die Views sind `setup/preload` (die Seite), `setup/preloadjob`
(das JSON, das die Seite liest: Fortschritt und Status eines Laufs, die Liste der Läufe sowie Start, Anhalten und
Probelauf für Skripte) und `setup/preloadstream` (der ältere Fortschrittsdatenstrom, beibehalten; er startet nichts).
Jedes `POST` trägt das Formular-Token.

`setup/preloadjob` antwortet:

| Anfrage | Antwort |
|---|---|
| `POST Action=start, SiteAccess, MaxPages, MaxDepth[, Images]` | `{"id": "..."}`, 409, während ein Lauf andauert, 400 für einen unbekannten Siteaccess |
| `POST Action=dryrun, SiteAccess, MaxPages, MaxDepth[, Images]` | `{"plan": {...}}` |
| `POST Action=stop, JobID` | `{"stopping": true|false}` |
| `GET setup/preloadjob/status` | `{"running": status or false, "runs": [...]}` |
| `GET setup/preloadjob/<id>/<offset>` | `{"events": [...], "offset": n, "done": bool, "status": {...}}` |

## 7. Wenn sich etwas nicht wie erwartet verhält

| Was Sie sehen | Warum, und was zu tun ist |
|---|---|
| **Es läuft bereits ein Vorladen** | Es läuft immer nur eines. Warten Sie oder klicken Sie auf **Anhalten**. Ein Lauf aus der Shell oder per Cron zählt ebenfalls. |
| Der Lauf wärmt die falsche Site vor | Der Probelauf zeigt die Adresse. Wenn er warnt, dass die Zuordnung den Siteaccess nicht erreicht, korrigieren Sie `SiteURL` oder die Zuordnungseinstellungen dieses Siteaccess. |
| **Fehlgeschlagen**: Das Vorladen kann nicht starten | Der Webserver kann die PHP-Kommandozeile nicht starten (`proc_open`, `setsid` oder die PHP-Binärdatei fehlt oder ist durch `open_basedir` verborgen). Führen Sie den Befehl stattdessen in der Shell aus. |
| **Ohne Abschluss beendet** | Der Prozess wurde beendet (ein Neustart der Maschine, ein Abbruch wegen Speichermangels). Starten Sie ihn erneut. |
| Viele **verweigerte** Seiten | 401 und 403 werden getrennt gezählt: Seiten hinter einer Anmeldung. Sie sind keine defekten Links. |
| Erzeugte Bildvarianten: *nicht gezählt* | Ein geclusterter Datei-Handler hält die Dateien anderswo; der Lauf wärmt sie trotzdem vor. |
| Ein Lauf ist langsam | Jede Seite wird vom Server gerendert; eine kalte Site braucht so lange, wie das Rendern ihrer Seiten dauert. Senken Sie das Seitenlimit oder die Tiefe, oder lassen Sie ihn zu einer ruhigen Stunde per Cron laufen. |

## Quellen

- `kernel/classes/exppreloadjob.php` (`expPreloadJob`): das Starten eines Laufs im Hintergrund, der Lauf selbst
  (`execute()`), der Probelauf (`plan()`), die angebotenen Sites, die Zählung der Bildvarianten, die Server.
- `kernel/classes/exppreloadaddress.php` (`expPreloadAddress`): Adresse und Präfix eines Siteaccess, die Zuordnung,
  die Startseiten.
- `kernel/classes/exppreloadlock.php` (`expPreloadLock`), `kernel/classes/exppreloadhistory.php`
  (`expPreloadHistory`): die Sperre und die Läufe.
- `kernel/setup/exppreloadrunner.php` (`expPreloadRunner`): der Crawler, den auch der Generator des statischen Caches
  verwendet.
- `kernel/private/classes/views/setup/preload.php`, `preloadjob.php`, `preloadstream.php`: die Views.
- `kernel/private/classes/commands/preload.php`, `preloadjob.php`: `bin/php/preload.php` und der Lauf im Hintergrund.
- `design/admin/templates/setup/preload.tpl` und `stylesheets/modules/setup.css` (und dasselbe in `design/admin4`).
- Tests: `tests/tests/kernel/classes/preload/` (keine Datenbank), `expPreloadRunnerLinksTest`, `ExpPreloadStartUrlsTest`.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
