# Der Request Shield (exp_request_shield)

Lesen Sie diese Seite, wenn Sie wissen möchten, was der Request Shield vor einer Exponential-Website tut, warum es
ihn gibt, wie er eine Anfrage unter PHP-FPM und unter Exponential Velocity entscheidet, welche Dateien er anlegt,
was die Administration von ihm zeigt und ab welcher Version eine Website ihn bekommt.

Verwandt: [Den Request Shield einrichten](../../anleitungen/request-shield.md) (Leitfaden),
[Aktualisierung auf 6.0.15: der Request Shield](../../bc/6.0/request-shield.md) (Hinweise zur Aktualisierung),
[Request Shield: hier beginnen](../../hilfe/request-shield/README.md) (Hilfe für Administratoren),
[Der Log-Betrachter](log-betrachter.md), [Anfrageprotokolle](anfrageprotokolle.md).

## Kurz gesagt

- **exp_request_shield** ist eine kleine Web Application Firewall vor der Website. Er entscheidet jede Anfrage,
  **bevor der Kernel startet**: Er weist Scanner, Anfragefluten und verirrte Formularsendungen ab, verweigert
  System-URLs und interne Dateien, hält die Seiten-Caches sauber und verlangt von verdächtigen Clients eine
  **Browserprüfung**. Eine Anfrage kostet wenige Mikrosekunden; er läuft auf jedem Hosting, auch auf Shared Hosting.
- Der Filter selbst ist [request-shield](https://github.com/cjw-network/request-shield) von JAC Systeme GmbH (CJW
  Network), MIT-Lizenz. Die Extension liefert **request-shield 0.4.0** als einzelne PHP-Dateien mit, dazu einen Build
  derselben Dateien für PHP 7.4. Sonst ist nichts zu installieren, ein Composer-Autoloader ist nicht nötig.
- Paket: [`se7enxweb/exp_request_shield`](https://packagist.org/packages/se7enxweb/exp_request_shield) auf
  Packagist, Version **0.0.1.0**. Unterstützt **PHP 7.4 bis 8.5**. Die Extension steht unter der
  **GPL-2.0-or-later**; die mitgelieferte Bibliothek und die drei daraus kopierten Exponential-Regeldateien behalten
  ihre **MIT**-Lizenz.
- **Eine Zeile in `config.php`** schaltet ihn ein, oberhalb des frühen Ausstiegs des HTTP-Caches, sodass auch
  zwischengespeicherte Seiten geschützt sind. Er läuft unter Apache oder nginx mit **PHP-FPM** und unter
  **Exponential Velocity**, mit identischen Antworten.
- Die Regeln werden im **Modus enforce** ausgeliefert: Der Schutz ist nach der Installation aktiv. `set mode monitor`
  protokolliert jede Entscheidung, ohne jemanden abzuweisen; die Datei `var/request-shield/PAUSED` schaltet den
  Shield sofort aus.
- **Setup > Request Shield** in der Administration (direkt nach **Logdateien**): eine Übersicht mit einem Diagramm
  der Entscheidungen pro Stunde, das Log, die Regeldateien und Schalter zum Pausieren, Einschalten, Durchsetzen,
  Beobachten und Ausschalten.
- Die Kommandozeile der Bibliothek, `extension/exp_request_shield/bin/request-shield`, prüft, testet und verfolgt
  die Regeln.

## Inhalt

1. [Warum ein Filter vor dem Kernel](#warum-ein-filter-vor-dem-kernel)
2. [Was er entscheidet](#was-er-entscheidet)
3. [Wie eine Anfrage ihn durchläuft](#wie-eine-anfrage-ihn-durchläuft)
4. [PHP-FPM und Velocity](#php-fpm-und-velocity)
5. [Regeldateien](#regeldateien)
6. [Modi und Ausschalten](#modi-und-ausschalten)
7. [Dateien auf der Festplatte](#dateien-auf-der-festplatte)
8. [In der Administration](#in-der-administration)
9. [Die Kommandozeile](#die-kommandozeile)
10. [Lizenzen](#lizenzen)
11. [Wer ihn bekommt und wann](#wer-ihn-bekommt-und-wann)
12. [Verwandte Seiten](#verwandte-seiten)

## Warum ein Filter vor dem Kernel

Die meisten unerwünschten Anfragen an eine öffentliche Website stammen nicht von Besuchern: Scanner, die
`/wp-login.php` und `/.env` ausprobieren, Bots, die jede Seite mit jedem Abfrageparameter abrufen, Formularsendungen
von nirgendwo, Anfragefluten aus einem einzigen Netz. Jede davon startete bisher den ganzen Kernel: den Autoloader,
die INI-Dateien, die Datenbankverbindung, oft das Rendern einer Seite und manchmal einen neuen Eintrag im
Seiten-Cache unter einer Adresse, die kein echter Besucher je verwendet.

Der Request Shield entscheidet diese Anfragen, bevor etwas davon geschieht, anhand von Regeln, die einmal in eine
PHP-Datei übersetzt werden, die OPcache vorhält. Eine abgewiesene Anfrage kostet wenige Mikrosekunden statt eines
Seitenaufbaus; eine Anfrage, die der Shield durchlässt, läuft unverändert weiter. Weil er vor dem frühen Ausstieg
des HTTP-Caches läuft, hält er den Cache auch frei von erfundenen Adressen.

## Was er entscheidet

Die mitgelieferten Regeln sind der Regelsatz der Bibliothek für Exponential (`exponential.rules` und eine der beiden
Admin-Dateien). Unter anderem:

- verweigern sie **System-URLs** auf dem öffentlichen Host (`/content/view/full/2`, Moduladressen, die ein Besucher
  nicht aufzurufen braucht) und **interne Dateien** (`settings/`, `var/` außerhalb des ausgelieferten Speichers,
  `.git`, `.env` ...);
- nehmen sie **Formularsendungen** nur für die Module an, die Formulare verarbeiten, und nur von den eigenen Seiten
  der Website;
- begrenzen sie das **Tempo** pro Netz, das Budget der **Suche** und die Versuche an der **Anmeldung**;
- verweigern sie Anfragen an einen **Hostnamen**, den die Website nicht hat;
- verlangen sie die **Browserprüfung** dort, wo ein echter Browser zu erwarten ist: bei der Anmeldung, in der
  Administration einmal pro Pass und bei Clients, die zu oft anfragen. Ein echter Browser löst sie von selbst in
  einem Bruchteil einer Sekunde, ohne Rätsel, und erhält danach ein Pass-Cookie;
- benennen sie jede Entscheidung im Log mit einer **Regel-ID** (`EXP-SYSVIEW`, `EXP-LOGIN`, `SITE-DASH` ...).

Warum es jede Regel gibt, erklärt der
[Anwendungsfall Exponential](https://github.com/cjw-network/request-shield/blob/main/docs/use-cases/exponential.md)
der Bibliothek, die Browserprüfung
[The browser check, explained](https://github.com/cjw-network/request-shield/blob/main/docs/explained/browser-check.md)
(beides auf Englisch).

## Wie eine Anfrage ihn durchläuft

```
config.php
  └─ extension/exp_request_shield/bootstrap/hook.php
       ├─ PHP-FPM (Apache, nginx): prepend.php läuft sofort
       ├─ Exponential Velocity: EXP_REQUEST_PREPEND_FILE = prepend.php,
       │    das index.php zu Beginn jeder Anfrage ausführt
       └─ jede andere Kommandozeile: nichts
prepend.php
  └─ expRequestShield::protect()
       ├─ var/request-shield/PAUSED existiert: nichts
       ├─ lib/requestshield/*.php laden (einmal pro Prozess)
       └─ die Anfrage mit der Hauptregeldatei entscheiden
            ├─ erlauben: weiter zum frühen Ausstieg des HTTP-Caches, dann zum Kernel
            └─ abweisen, Browserprüfung, eigene Seite des Shields: der Shield antwortet, die Anfrage endet
```

Jeder Einstiegspunkt, der `config.php` liest, ist abgedeckt: `index.php`, die Einstiegspunkte für REST und das
Baummenü, ezjscore-Aufrufe.

Die Regeldateien werden einmal nach `var/request-shield/cache/` übersetzt und neu übersetzt, wenn sich eine
Regeldatei ändert (geprüft alle `recheck` Sekunden, standardmäßig 10). Eine Anfrage kostet dann ein Include der
übersetzten Einstellungen und die Prüfungen selbst.

**Im Fehlerfall lässt er durch.** Wenn der Shield selbst scheitert (eine Regeldatei, die sich nicht übersetzen lässt,
ein Speicher, den er nicht beschreiben kann), geht die Anfrage ungecacht an die Website weiter, und das Fehlerlog von
PHP erhält eine Zeile pro Minute. Ein defekter Shield legt die Website nie lahm; Setup > Request Shield zeigt den
Fehler.

## PHP-FPM und Velocity

| | PHP-FPM (Apache, nginx) | Exponential Velocity |
|---|---|---|
| Wo der Shield läuft | in `config.php`, bei jeder Anfrage | `config.php` läuft einmal pro Worker; deshalb nennt der Hook `bootstrap/prepend.php` in `EXP_REQUEST_PREPEND_FILE`, und `index.php` führt die Datei zu Beginn jeder Anfrage aus |
| Voraussetzung | nichts vom Kernel | ein `index.php`, das `EXP_REQUEST_PREPEND_FILE` ausführt (die Linie 6.0.15); ein älteres `index.php` führt sie nie aus, Velocity bleibt dann ungefiltert, PHP-FPM nicht |
| Nach Hinzufügen oder Entfernen des Hooks | kein Neustart nötig | `./console exp:velocity restart` |
| Nach einer Regeländerung | nichts: innerhalb von `recheck` Sekunden neu gelesen | ebenso |
| Zähler | im Speicher | `set store file` teilt sie zwischen allen Workern und PHP-FPM |

Die Antworten sind **identisch**: derselbe Status, dieselben Header, dieselbe Logzeile. In einem Velocity-Worker
löst `exit()` das Exit-Signal von Velocity aus, damit die Anfrage endet und nicht der Worker; die Extension lässt
dieses Signal durch, sodass eine Abweisung, die Browserprüfung und die eigenen Seiten des Shields genau wie unter
PHP-FPM antworten, ohne falsche Zeile „shield failed“ im Fehlerlog von PHP. Die Einzelheiten stehen in
[doc/velocity.md](https://github.com/se7enxweb/exp_request_shield/blob/main/doc/velocity.md) der Extension.

## Regeldateien

Der Shield wird über **Regeldateien** konfiguriert, Klartext, den die Bibliothek liest. Er läuft vor dem Kernel und
liest deshalb nie eine INI-Datei; `requestshield.ini` enthält nur Einstellungen der Admin-Seiten.

Die Hauptregeldatei ist die erste, die zutrifft:

| Reihenfolge | Datei | Wann |
|---|---|---|
| 1 | die Datei, die die Konstante `EXP_REQUEST_SHIELD_RULES` nennt | in `config.php` vor der Hook-Zeile definiert |
| 2 | `settings/request-shield/request-shield.rules` | die Website hat eine eigene Datei |
| 3 | `extension/exp_request_shield/rules/request-shield.rules` | die Vorgabe der Extension |

Eine echte Website verwendet ihre eigene Datei (2): Aktualisierungen der Extension ersetzen deren Dateien. Die drei
Dateien `exponential*.rules` werden neben die Hauptdatei der Website kopiert, weil ein `include` das Verzeichnis
seiner Datei nicht mit `..` verlassen darf. Der erste Modus-Schalter in der Administration legt diese Kopien selbst
an; von Hand sind es zwei Befehle ([der Leitfaden](../../anleitungen/request-shield.md#2-der-website-eigene-regeln-geben)).

| Mitgelieferte Datei | Was sie ist |
|---|---|
| `rules/request-shield.rules` | die vorgegebene Hauptdatei: bindet die Exponential-Regeln ein, Modus enforce, Speicher und Log unter `var/request-shield/`, die eigenen Seiten der Bibliothek (`/rs`) für alle außer dem Server gesperrt |
| `rules/exponential.rules` | das Frontend: System-URLs, interne Dateien, Ansichtsparameter, Formulare, die Suche, das Tempo. Wird eingebunden, nie allein geladen |
| `rules/exponential-admin-uri.rules` | die Administration als Siteaccess `/admin` (URI-Zuordnung). Bindet `exponential.rules` ein |
| `rules/exponential-admin-host.rules` | die Administration auf eigenem Host (`admin.example.org`). Bindet `exponential.rules` ein |

Behalten Sie `www.example.org` in einer `host`-Zeile der Website-Datei: Die mitgelieferten Beispiele (`expect`-Zeilen)
senden diesen Namen, und `bin/request-shield test` lässt ohne ihn fünf davon scheitern. Die Domain ist für
Dokumentation reserviert; kein echter Besucher sendet sie.

Jede Zeile der Regelsprache ist in der Bibliothek dokumentiert (auf Englisch):
[Regeldateien](https://github.com/cjw-network/request-shield/blob/main/docs/reference/rule-files.md) und
[Einstellungen](https://github.com/cjw-network/request-shield/blob/main/docs/reference/settings.md).

## Modi und Ausschalten

| Modus | Wirkung |
|---|---|
| `enforce` | die Entscheidungen werden ausgeführt; mit `monitor` markierte Regeln werden nur beobachtet. **So installiert.** |
| `monitor` | jede Entscheidung wird so protokolliert, wie sie fiele (`monitor-reject`, `monitor-challenge` ...); niemand wird abgewiesen |
| `strict` | enforce, und dazu die als strict markierten Regeln |
| `off` | nichts wird entschieden |

Um den Shield sofort auf allen Servern auszuschalten, ohne etwas zu bearbeiten, legen Sie
`var/request-shield/PAUSED` an; entfernen Sie die Datei, um ihn wieder einzuschalten. Dauerhaft entfernen Sie die
Hook-Zeilen aus `config.php` (und starten Velocity neu). Auch die Konstante `EXP_REQUEST_SHIELD_DISABLED` (vor der
Hook-Zeile als `true` definiert) lässt den Hook nichts tun.

## Dateien auf der Festplatte

| Pfad | Was | Geschrieben von |
|---|---|---|
| `extension/exp_request_shield/lib/requestshield/` | die Bibliothek (und `php74/`, ihr Build für PHP 7.4) | dem Paket |
| `settings/request-shield/` | die Regeldateien der Website | Ihnen oder dem ersten Modus-Schalter |
| `var/request-shield/cache/` | die übersetzten Einstellungen | dem Shield |
| `var/request-shield/store/` | Zähler, das Geheimnis des Pass-Cookies, Listen, Statistiken | dem Shield |
| `var/request-shield/log/shield.log` | eine Zeile pro Entscheidung | dem Shield |
| `var/request-shield/backup/` | die Regeldatei, wie sie vor jedem Modus-Schalter war | den Schaltern der Administration |
| `var/request-shield/PAUSED` | schaltet den Shield aus, solange sie existiert | Ihnen oder **Pausieren** |
| `var/log/requestshield.log` | wer was geschaltet hat und mit welchem Ergebnis | den Schaltern der Administration |

Das ganze Verzeichnis `var/request-shield/` muss für den Benutzer beschreibbar sein, unter dem PHP für die Website
läuft. Der Webserver darf `settings/` und `var/request-shield/` nie ausliefern; ein standardmäßiger virtueller Host
für Exponential verweigert beide. Das Log hält das Netz eines Clients fest (IPv4 /24, IPv6 /48), nie die vollständige
Adresse einer Person.

## In der Administration

**Setup > Request Shield** (Modul `requestshield`, im Setup-Menü von admin4 in der Gruppe **System**, direkt nach
**Logdateien**) hat drei Seiten und die Schalter:

| Seite | Ansicht | Was sie zeigt |
|---|---|---|
| Übersicht | `requestshield/dashboard` | Hinweise (Regeln nicht lesbar, pausiert, für diese Seite nicht gelaufen, Modus monitor), die Kennzahlen, ein Diagramm der Entscheidungen pro Stunde, die Einrichtung (Version der Bibliothek, Regeldatei, Stufe des Speichers, Log), die Regeln, die am häufigsten entschieden haben, die neuesten Logzeilen |
| Protokoll | `requestshield/log` | die Logzeilen eines Zeitraums, nach Entscheidung gefiltert, seitenweise, mit Ansichtsparametern wie `/requestshield/log/(hours)/72/(group)/refused` |
| Regeln | `requestshield/rules` | ob sich die Regeln übersetzen lassen, die Hauptdatei und die eingebundenen Dateien und die Befehle, die sie prüfen |
| Schalter | `requestshield/switch` | **Pausieren** / **Einschalten**, **Durchsetzen**, **Beobachten**, **Aus** oben auf der Übersicht |

| Richtlinie | Erlaubt |
|---|---|
| `requestshield/read` | alle Seiten |
| `requestshield/manage` | die Schalter |

In einer neuen Installation hat nur die Rolle Administrator diese Richtlinien. Ein Modus-Schalter sichert die
Regeldatei, übersetzt das Ergebnis und stellt die alte Datei wieder her, wenn sich die neue nicht übersetzen lässt.
Die Seiten gibt es in den Designs admin4 und admin4l, im Design adminui von exp_adminui und im klassischen
Admin-Design, jeweils hell und dunkel, ohne Inline-CSS und Inline-Skript. Die Hilfe für Administratoren:
[Request Shield: hier beginnen](../../hilfe/request-shield/README.md).

## Die Kommandozeile

Führen Sie sie als der Benutzer aus, unter dem PHP für die Website läuft, damit die Dateien, die sie schreibt, für
die Website lesbar bleiben:

```bash
php extension/exp_request_shield/bin/request-shield rules                     # welche Hauptdatei die Website verwendet
php extension/exp_request_shield/bin/request-shield check settings/request-shield/request-shield.rules
php extension/exp_request_shield/bin/request-shield test  settings/request-shield/request-shield.rules
php extension/exp_request_shield/bin/request-shield trace settings/request-shield/request-shield.rules "GET https://www.example.org/the/path"
```

`check` benennt jeden Fehler mit Datei und Zeile, `test` entscheidet jedes `expect`-Beispiel neben den Regeln,
`trace` zeigt, wie eine einzelne Anfrage Regel für Regel entschieden wird. Alle Befehle:
[CLI-Referenz der Bibliothek](https://github.com/cjw-network/request-shield/blob/main/docs/reference/cli.md)
(auf Englisch). Verwenden Sie `self-update` nicht für die mitgelieferten Dateien; aktualisieren Sie stattdessen die
Extension.

## Lizenzen

| Teil | Lizenz |
|---|---|
| Die Extension: Bootstrap, Klassen, Admin-Modul, Templates, Stylesheet, Einstellungen, Kommandozeilen-Wrapper, die vorgegebene `rules/request-shield.rules`, Dokumentation, Übersetzungen | GNU General Public License v2.0 oder jede spätere Version (GPL-2.0-or-later); Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved. |
| `lib/requestshield/*.php`, `lib/requestshield/php74/*.php` | MIT-Lizenz, Copyright (c) 2026 JAC Systeme GmbH (CJW Network) |
| `rules/exponential.rules`, `rules/exponential-admin-uri.rules`, `rules/exponential-admin-host.rules` | MIT-Lizenz, unverändert aus `examples/exponential/` von request-shield kopiert |

Die MIT-Lizenz ist mit der GPL v2 vereinbar und erlaubt es, die Dateien mit der Extension zu verbreiten.

## Wer ihn bekommt und wann

| Installation | Was sie bekommt |
|---|---|
| Jede Website mit Exponential 6.0, ab sofort | `composer require se7enxweb/exp_request_shield`, `ActiveExtensions[]=exp_request_shield` und die Hook-Zeile in `config.php` ([der Leitfaden](../../anleitungen/request-shield.md)) |
| Eine neue Installation aus `se7enxweb/exponential` `main` | `composer.json` verlangt das Paket, und `config.php-RECOMMENDED` lädt den Hook; ein daraus erstelltes neues `config.php` ist ab der ersten Anfrage geschützt |
| Eine bestehende Installation, auf die Linie `main` aktualisiert | das Paket kommt mit `composer.json`, aber das `config.php` der Website wird nicht geändert: Fügen Sie die Hook-Zeile selbst hinzu ([Hinweise zur Aktualisierung](../../bc/6.0/request-shield.md)) |
| Die Website-Pakete von 6.0.15 `sevenx_site`, `sevenx_multisite`, `cjw_multisite` und ihre Varianten `_clean` | aktivieren die Extension bei der Installation |
| `composer create-project` der Distributionen | löst auf v6.0.14 auf, bis eine Version 6.0.15 getaggt ist; der Shield ist dann nicht enthalten, fügen Sie ihn mit `composer require` hinzu |

Unter Velocity hat eine Website auf 6.0.14 kein `EXP_REQUEST_PREPEND_FILE` in ihrem `index.php`: Der Shield schützt
ihre Anfragen über PHP-FPM, nicht ihre Anfragen über Velocity, bis die Website die Linie 6.0.15 verwendet.

## Verwandte Seiten

- [Den Request Shield einrichten](../../anleitungen/request-shield.md)
- [Aktualisierung auf 6.0.15: der Request Shield](../../bc/6.0/request-shield.md)
- [Request Shield: hier beginnen](../../hilfe/request-shield/README.md)
- [Der HTTP-Cache mit vorgeschaltetem request-shield](../../bc/6.0/httpcache.md#mit-vorgeschaltetem-request-shield)
- [Sicherheitshärtung](../../hilfe/sicherheitshaertung.md)
- [Content Security Policy](content-security-policy.md)
- [Glossar](../../glossar.md#r)
- [Änderungsprotokoll von 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Die eigene Dokumentation der Extension (auf Englisch): [README](https://github.com/se7enxweb/exp_request_shield/blob/main/README.md),
  [INSTALL](https://github.com/se7enxweb/exp_request_shield/blob/main/INSTALL.md)
- Die Bibliothek: [Dokumentation von request-shield](https://github.com/cjw-network/request-shield/tree/main/docs)
