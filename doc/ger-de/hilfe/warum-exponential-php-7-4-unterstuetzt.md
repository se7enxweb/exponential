---
title: Warum Exponential PHP 7.4 unterstützt
audience: admin, developer, user, press, business
category: getting-started
order: 3
summary: Unterstützt - PHP 8.0 bis 8.5; ohne Support nutzbar - PHP 7.4 (Exponential 6.0.15 und neuer). Für wen das gedacht ist, was unter PHP 7.4 funktioniert und was PHP 8 braucht (eine Tabelle pro Funktion), wie ein einziges Composer-Metapaket für jede PHP-Version die passenden Pakete wählt, wie die Sicherheit gewahrt bleibt, die ehrlichen Kompromisse und die Haltung zum Lebensende - und warum es zu den Werten des Projekts gehört, Brücken zu älteren PHP-Versionen zu bauen.
keywords: PHP 7.4, PHP 8.0, PHP 8.5, unterstützte PHP-Versionen, altes PHP, altes Hosting, gesperrtes Hosting, se7enxweb/exponential-php-floor, Metapaket, Composer, Polyfills, symfony/polyfill-php80, symfony/polyfill-php81, Lebensende, Sicherheit, Velocity, Layouts, Upgrade-Pfad, 6.0.15, supported PHP versions, legacy PHP, metapackage, end of life, security
type: help
---
# Warum Exponential PHP 7.4 unterstützt

Diese Seite ist für Website-Betreiber, Administratoren, Hosting-Unternehmen und Entwickler, die Exponential unter
PHP 7.4 betreiben oder betreiben müssen, und für alle, die sich fragen, warum ein Projekt, das PHP 8.5 vom ersten Tag
an unterstützt, auch zu einer PHP-Version zurückgeht, die ihre Entwickler 2022 aufgegeben haben.

> **Unterstützt: PHP 8.0 bis 8.5. Ohne Support nutzbar: PHP 7.4 (Exponential 6.0.15 und neuer).**
>
> „Ohne Support“ heißt: Die Software ist so gebaut, dass sie unter PHP 7.4 nutzbar ist, aber das Projekt gibt keine Zusage für Fehler- oder Sicherheitskorrekturen, die nur PHP 7.4 betreffen; Sie nutzen es auf eigenes Risiko, und die Funktionen, die PHP 8 brauchen, gibt es dort nicht.

Die unterstützten Versionen bleiben PHP 8.0 bis 8.5, und das neueste PHP bleibt voll unterstützt; die Nutzbarkeit unter
PHP 7.4 kommt darunter hinzu und ersetzt nichts.

## Inhalt

1. [Kurz gesagt](#kurz-gesagt)
2. [Für wen es gedacht ist](#für-wen-es-gedacht-ist)
3. [Was unter PHP 7.4 funktioniert und was PHP 8 braucht](#was-unter-php-74-funktioniert-und-was-php-8-braucht)
4. [Wie es funktioniert](#wie-es-funktioniert)
5. [Eine kurze Geschichte der PHP-Unterstützung in Exponential](#eine-kurze-geschichte-der-php-unterstützung-in-exponential)
6. [Warum das Projekt das tut](#warum-das-projekt-das-tut)
7. [Wie die Sicherheit gewahrt bleibt](#wie-die-sicherheit-gewahrt-bleibt)
8. [Die ehrlichen Kompromisse](#die-ehrlichen-kompromisse)
9. [Die Haltung zum Lebensende](#die-haltung-zum-lebensende)
10. [Installation unter PHP 7.4](#installation-unter-php-74)
11. [Verwandte Seiten](#verwandte-seiten)

## Kurz gesagt

| Frage | Antwort |
|---|---|
| Welche PHP-Versionen? | Unterstützt: 8.0 bis 8.5. Ohne Support nutzbar: 7.4. `composer.json` deklariert `"php": "^7.4 \|\| ^8.0 \|\| ... \|\| ^8.8"`; Versionen nach 8.5 gibt es noch nicht, sie sind nicht getestet. |
| Was ist unter 7.4 ohne Support nutzbar? | Der ganze Kernel: Inhalte, die Administrations- und Redaktionsoberfläche, Templates, die REST-API, SOAP, WebDAV, Cronjobs, die Suche, der Setup-Assistent und `exp:install` sowie die mit dem Kernel ausgelieferten Erweiterungen. |
| Was braucht PHP 8? | Exponential Velocity (8.0), Netgen Layouts und die Mediensite (8.1) sowie einige Erweiterungen, deren Releases 8.0 brauchen. Die [Tabelle](#was-unter-php-74-funktioniert-und-was-php-8-braucht) nennt jede einzeln. |
| Muss ich Pakete von Hand wählen? | Nein. Der Kernel verlangt das Metapaket `se7enxweb/exponential-php-floor`; Composer installiert die Version, die zu Ihrem PHP passt, und mit ihr die richtigen Pakete. |
| Ist PHP 7.4 sicher? | PHP 7.4 erhält seit dem 28. November 2022 keine Sicherheitskorrekturen des PHP-Projekts mehr. Es ist nur dort sicher, wo Ihr Betriebssystemhersteller oder Hoster es weiter pflegt. Siehe [Wie die Sicherheit gewahrt bleibt](#wie-die-sicherheit-gewahrt-bleibt). |
| Was heißt „ohne Support“? | Keine Zusage für Fehler- oder Sicherheitskorrekturen, die nur PHP 7.4 betreffen; ein Problem, das nur unter 7.4 auftritt, kann unbehoben bleiben. Sie nutzen es auf eigenes Risiko. |
| Wie lange? | Die Nutzbarkeit unter PHP 7.4 ist eine Brücke, kein Ziel. Siehe [Die Haltung zum Lebensende](#die-haltung-zum-lebensende). |

## Für wen es gedacht ist

Exponential unter PHP 7.4 ohne Support zu nutzen ist für Organisationen gedacht, die ein aktuelles Exponential wollen, aber **die PHP-Version des
Rechners, auf dem es läuft, noch nicht ändern können**. Nach unserer Erfahrung sind sie selten nachlässig; meist sind
sie an etwas gebunden, das sie nicht selbst in der Hand haben:

- **Hosting-Verträge und verwaltete Plattformen.** Ein Shared- oder Managed-Hoster bestimmt die angebotenen
  PHP-Versionen, und ein Vertrag kann Jahre laufen.
- **Betriebssystem-Releases mit langer Laufzeit.** Distributionen mit langen Supportzeiträumen haben PHP 7.4 als
  Standard-PHP ausgeliefert: Debian 11, Ubuntu 20.04 LTS und der Stream `php:7.4` von Red Hat Enterprise Linux 8 und
  seinen Nachbauten. Organisationen mit erweiterten Supportverträgen erhalten dafür gepflegte PHP-Pakete von ihrem
  Hersteller.
- **Zertifizierte und geprüfte Umgebungen.** In der öffentlichen Verwaltung, im Gesundheitswesen, im Finanzwesen und
  in der Industrie wird ein Server-Image oft als Ganzes zertifiziert. Ein Wechsel von PHP bedeutet eine neue
  Zertifizierung, die Monate im Voraus geplant wird.
- **Abgelegene, getrennte und schwach angebundene Standorte.** Eine Installation in einer Außenstelle, auf einem
  Schiff, in einer Forschungsstation oder in einem Netz ohne Internetzugang wird aktualisiert, wenn jemand dorthin
  kommt.
- **Andere Anwendungen auf demselben Server.** Ein Server, auf dem auch Software läuft, die nur unter PHP 7.4
  funktioniert, kann erst umziehen, wenn diese Software es kann.

Für sie alle hieß die Wahl bisher: bei einem alten Exponential mit bekannten Fehlern bleiben oder warten. Jetzt können
sie das aktuelle Release mit seinen Korrekturen und Funktionen betreiben und auf PHP 8 wechseln, wenn ihre Umstände es
erlauben, ohne in diesem Moment auch Exponential noch einmal umzustellen.

## Was unter PHP 7.4 funktioniert und was PHP 8 braucht

Der **Kernel** (alles in `kernel/` und `lib/`) und die **mit ihm ausgelieferten Erweiterungen** (`ezjscore`, `ezoe`,
`ezformtoken`, `expservices`, `expimageeditor`, `exphelpsite`) laufen unter PHP 7.4. Für die Erweiterungspakete und
Server zeigt die Tabelle das älteste PHP, das jedes braucht. „7.4 (älteres Release)“ bedeutet: Composer installiert
unter PHP 7.4 das letzte Release derselben Linie, das dort noch läuft; unter PHP 8 erhalten Sie das neueste.

| Funktion oder Paket | PHP 7.4 (ohne Support) | PHP 8.0 | PHP 8.1 bis 8.3 | PHP 8.4 und 8.5 |
|---|---|---|---|---|
| Kernel: Inhalte, Administration, Redaktion, Templates, REST v1 und v2, SOAP, WebDAV, Cronjobs, Suche | ja | ja | ja | ja |
| Setup-Assistent, Kickstarter, `exp:install` | ja | ja | ja | ja |
| Apache oder nginx mit PHP-FPM oder mod_php | ja | ja | ja | ja |
| Der eingebaute Server von PHP (`exp:velocity start --engine=php`) | ja | ja | ja | ja |
| Exponential Velocity, der Anwendungsserver (`--engine=qbix`) | nein: `exp:velocity start` lehnt mit einer Meldung ab | ja | ja | ja |
| FrankenPHP-Engine | bringt ihr eigenes PHP 8 mit | ja | ja | ja |
| Netgen Layouts (`explayouts` und seine Pakete), die expsite-Pakete, das Theme der Mediensite | nein | nein | ja | ja |
| Zwei-Faktor-Authentifizierung (`sevenx_authentication_2fa`) | nein | ja | ja | ja |
| `exp_adminui`, `expai`, `expqueue`, `exp_enhanced_link`, `expchangeclass`, `cjw_themes_jumper`, `cjw_tmv` | nein | ja | ja | ja |
| `sqliimport` 2.x, `ezodf` 7.x, `ezownerchange` 1.4 | nein | ja | ja | ja |
| Tags (`eztags`), CJW Newsletter, Syndication, `bccie`, `git_manager` | 7.4 (älteres Release) | ja | ja | ja |
| `xrowextract`, `sevenx_dse` | nein (ihr Code braucht PHP 8) | ja | ja | ja |
| reCAPTCHA-Bibliothek (`google/recaptcha`) | 1.2.4 | 1.3 | 1.3 | 2.x |
| Alle anderen Erweiterungen, die der Kernel installiert (ezflow, ezwebin, ezdemo, recaptcha, hcaptcha, ezmultiupload, ...) | ja | ja | ja | ja |
| Bildausgabe in AVIF | nein (WebP ist das beste Format) | nein | ja | ja |
| Dateizeiten im ZIP-Download der Protokollansicht | die Zeit des Downloads | die ursprünglichen Zeiten | die ursprünglichen Zeiten | die ursprünglichen Zeiten |

Nichts in dieser Tabelle scheitert still mit einer weißen Seite. Ein Paket, das unter Ihrem PHP nicht laufen kann,
installiert Composer einfach nicht, und der eine Server, der PHP 8 bräuchte, Velocity, sagt es:

```text
$ php bin/php/console exp:velocity start
the Velocity engine (qbix) needs PHP 8.0 or later, this is PHP 7.4. Exponential runs on PHP 7.4 with Apache or
nginx and PHP-FPM, or with the php engine (exp:velocity start --engine=php)
```

## Wie es funktioniert

Drei Teile sorgen dafür, dass eine einzige Codebasis von PHP 7.4 bis PHP 8.5 läuft.

**1. Der Kernel wird für das älteste unterstützte PHP geschrieben.** Jede PHP-Datei muss gültiges PHP 7.4 sein:
keine Union Types, kein Nullsafe-Operator, kein `match`, keine benannten Argumente, keine Constructor Property
Promotion, keine Enums, kein `readonly`. Typisierte Eigenschaften und Arrow Functions, die PHP 7.4 eingeführt hat,
sind erlaubt. Eine neuere Funktion wird nur hinter `function_exists()` oder über ein Polyfill verwendet. Die Regel
prüfen `make lint74-all` und ein Job für PHP 7.4 in der kontinuierlichen Integration, der jede Datei prüft, die
Produktionsabhängigkeiten mit Composer unter PHP 7.4 installiert und jede Klasse des Kernels und der Erweiterungen
lädt.

Für 6.0.15 bedeutete das: Der Namensraum `Exponential\View\Kernel\Class` heißt jetzt
`Exponential\View\Kernel\ContentClass` (`class` ist vor PHP 8.0 ein reserviertes Wort; der alte Name funktioniert
über `class_alias` weiter), einige Parameter `Type|null` wurden zu `?Type`, drei Nullsafe-Operatoren zu einfachen
Prüfungen, `mixed` verschwand aus zwei magischen Methoden, und einige Laufzeitunterschiede erhielten eine Ausweichlösung:
`WeakMap` (8.0) weicht auf `SplObjectStorage` aus, ein GD-Bild ist unter 7.4 eine Ressource und unter 8 ein Objekt,
ein cURL-Handle muss bis zu seiner letzten Verwendung offen bleiben, und `ZipArchive::setMtimeName()` gibt es erst ab
8.0.

**2. Polyfills ergänzen die Funktionen.** `symfony/polyfill-php80` und `symfony/polyfill-php81` definieren
`str_contains()`, `str_starts_with()`, `get_debug_type()`, `array_is_list()` und die anderen kleinen Funktionen aus
PHP 8.0 und 8.1 auf den PHP-Versionen, denen sie fehlen; auf neuerem PHP tun sie nichts. `lib/phpcompat.php` deckt die
wenigen ab, die der Kernel darüber hinaus braucht.

**3. Ein Metapaket pro PHP-Bereich.** Die `composer.json` des Kernels verlangt `se7enxweb/exponential-php-floor` in
`^1.0`. Jede Version dieses Metapakets gehört zu einem PHP-Bereich und verlangt die Pakete, die dort laufen:

| Version des Metapakets | PHP | Was sie hinzufügt |
|---|---|---|
| 1.0.1 | 7.4 (ohne Support nutzbar) | die letzten Releases für PHP 7.4 der Linien, die zu PHP 8 gewechselt sind (eztags ~2.4.23, cjw_newsletter ~4.2.14, syndication ~1.3.10, bccie ~1.1.18, git_manager ~2.0.22) und google/recaptcha ~1.2.4; 1.0.0 verlangte auch xrowextract und sevenx_dse, deren Code unter 7.4 nicht läuft |
| 1.1.0 | 8.0 | die Erweiterungen, die PHP 8.0 brauchen, einschließlich Velocity, und google/recaptcha ~1.3.1 |
| 1.2.0 | 8.1 bis 8.3 | alles aus 1.1.0, dazu Netgen Layouts, die expsite-Pakete und das Theme der Mediensite; google/recaptcha ~1.3.1 |
| 1.3.0 | 8.4 und neuer | dieselbe Auswahl mit dem aktuellen google/recaptcha 2.x |

Composer installiert immer die höchste Version, deren PHP-Anforderung das laufende PHP erfüllt. Das Ergebnis: Dasselbe
`composer require se7enxweb/exponential` (oder `composer create-project` eines Skeletons) ergibt auf jedem
unterstützten PHP eine vollständige, stimmige Installation, und niemand muss wissen, welche Erweiterung welches PHP
braucht. Die Quelle des Metapakets ist
[se7enxweb/exponential-php-floor](https://github.com/se7enxweb/exponential-php-floor); seine README nennt jede Version.

Die Technik ist für das Projekt nicht neu: Die Linie Exponential Platform DXP 4.6 verwendet dieselbe Art Metapaket,
`se7enxweb/exponential-platform-dxp-php-floor`, um auf älteren PHP-Versionen gepflegte Forks ihrer Bibliotheken zu
wählen.

## Eine kurze Geschichte der PHP-Unterstützung in Exponential

| Release | PHP |
|---|---|
| eZ Publish 4.x und 5.x (historisch, eingestellt) | PHP 5, später PHP 7 für den Legacy-Kernel |
| bis 6.0.7 | PHP 7.4 und 8.1 |
| 6.0.8 (April 2025) bis 6.0.14 | PHP 8.1 und neuer; die Unterstützung für PHP 8.4 begann mit 6.0.8, die für PHP 8.5 mit 6.0.11 |
| 6.0.15 | Unterstützt: wieder PHP 8.0 (das Standard-PHP von Red Hat Enterprise Linux 9) bis 8.5; ohne Support nutzbar: PHP 7.4 |

Der Schritt zu PHP 8.1 in 6.0.8 war damals richtig: Er erlaubte dem Projekt, Jahre an PHP-5- und PHP-7-Code schnell
aufzuräumen. Als diese Arbeit getan war, zeigte dieselbe Prüfung, dass sehr wenig im Kernel PHP 8 wirklich brauchte.
Nur eine Handvoll Sprachmittel stand zwischen dem Code und PHP 7.4, und der Weg zurück kostete Tage, nicht Monate.
Deshalb sank die unterstützte Untergrenze in 6.0.15 auf 8.0, und die Software wurde ohne Support unter 7.4 nutzbar gemacht.

## Warum das Projekt das tut

Exponential ist aus einem Content-Management-System hervorgegangen, das Organisationen seit mehr als zwei Jahrzehnten
betreiben. Viele tun es noch heute. Die Ingenieure von 7x, die Exponential pflegen, haben den größten Teil dieser Zeit
damit verbracht, solchen Organisationen zu helfen, und sie haben dasselbe Muster viele Male gesehen: Eine Website wird
nicht von fehlendem Willen aufgehalten, sondern von einem Vertrag, einer Zertifizierung, einer Plattform oder einem
Budgetzyklus, den in diesem Jahr niemand im Projekt ändern kann.

Die Werte des Projekts folgen aus dieser Erfahrung:

- **Menschen dort abholen, wo sie stehen.** Software, die nur auf der neuesten Plattform läuft, hilft den
  Organisationen, die Hilfe am wenigsten brauchen. Die Software auf einer älteren PHP-Version nutzbar zu halten, auch ohne
  Support, lässt die anderen teilhaben, die aktuellen Korrekturen und Funktionen nutzen und ihren eigenen Umzug planen.
- **Das Upgrade der Anwendung vom Upgrade der Plattform trennen.** Exponential und PHP gleichzeitig zu wechseln,
  verdoppelt das Risiko. Mit der Unterstützung für PHP 7.4 zieht eine Website zuerst auf das aktuelle Exponential mit
  dem PHP um, das sie hat, und später auf PHP 8 mit einer Anwendung, die dort bereits läuft. Jeder Schritt lässt sich
  für sich testen und zurücknehmen.
- **Nie die Zukunft gegen die Vergangenheit tauschen.** Die Unterstützung für PHP 7.4 hält PHP 8.5 nicht auf: Das
  neueste PHP wird ab seinem ersten Release unterstützt, und jede Funktion, die PHP 8 braucht, wird weiter gebaut. Die
  alte Version wird von einigen Regeln und Werkzeugen getragen, nicht dadurch, dass etwas aufgegeben wird.
- **Ehrlich über die Kosten sein.** Älteres PHP hat echte Nachteile. Diese Seite nennt sie, statt sie zu verbergen.

## Wie die Sicherheit gewahrt bleibt

Exponential auf einem alten PHP ist nur so sicher wie der ganze Stack, und unter PHP 7.4 ist dieser Stack nicht unterstützt. So wird jede Schicht sicher
gehalten.

**PHP selbst.** Das PHP-Projekt hat die Sicherheitsunterstützung für PHP 7.4 am 28. November 2022 beendet. Betreiben
Sie PHP 7.4 nur dort, wo es noch jemand korrigiert: mit den PHP-Paketen eines Betriebssystems mit langer Laufzeit (zum
Beispiel Debian LTS oder erweiterter Support, Ubuntu Pro oder der PHP-7.4-Stream von Red Hat Enterprise Linux 8 und
seinen Nachbauten innerhalb ihrer Supportzeiträume) oder bei einem Hoster, der Sicherheitskorrekturen zurückportiert.
Ein ungepflegtes, irgendwo heruntergeladenes PHP 7.4 ist ein Risiko, das keine Anwendung beseitigen kann.

**Der eigene Code von Exponential.** Der Kernel ist eine einzige Codebasis für jede PHP-Version. Sicherheitskorrekturen
gehen einmal hinein und erreichen PHP 7.4 und PHP 8.5 gleichermaßen, auch wenn das Projekt für 7.4 nichts zusagt; es gibt keinen getrennten, älteren Zweig, der
zurückfallen könnte.

**Abhängigkeiten.** Die Sicherheitssperre von Composer bleibt eingeschaltet: Composer lehnt bei der Auflösung
Paketversionen mit bekannten Sicherheitswarnungen ab, auf jeder PHP-Version. Das Metapaket wählt nur Versionen, die
Composer akzeptiert. Wo eine Bibliothek eine alte PHP-Version aufgegeben hat, bevor sie eine Schwachstelle behob, pflegt
das Projekt gepatchte Forks der letzten passenden Linie (der Ansatz, den die Linie DXP 4.6 schon verwendet, zum
Beispiel für Twig 3.11 und graphql-php 14.11), statt ein verwundbares Release festzuschreiben.

**Erweiterungen.** Unter PHP 7.4 werden einige Erweiterungen im letzten Release ihrer Linie installiert, das dort noch
läuft. Diese Releases sind Tage oder Wochen älter als die neuesten, nicht Jahre; kommt eine Sicherheitskorrektur in
eine solche Erweiterung, veröffentlicht das Projekt ein Release dieser Linie auch für PHP 7.4 oder eine neue Version
des Metapakets, die es verlangt.

**Polyfills.** Die Polyfills stammen vom Symfony-Projekt, sind weit verbreitet und werden unabhängig von Exponential
gepflegt. Sie fügen nur Funktionen hinzu und ändern nichts auf PHP-Versionen, die diese bereits haben.

## Die ehrlichen Kompromisse

- **Weniger Funktionen.** Velocity, Layouts, die Zwei-Faktor-Authentifizierung und die anderen Pakete der Tabelle sind
  unter PHP 7.4 nicht verfügbar. Für viele kleine und mittlere Websites ist das annehmbar; für andere ist es der Grund,
  zuerst auf PHP 8 umzuziehen.
- **Ältere Releases von Erweiterungen.** Einige Erweiterungen liegen unter PHP 7.4 ein oder wenige Releases zurück.
- **Weniger Tests.** Die vollständige Testsuite läuft unter PHP 8.1 bis 8.5. Unter PHP 7.4 prüft die kontinuierliche
  Integration die Syntax jeder Datei, eine Produktionsinstallation und das Laden jeder Klasse. Die eigenen Testläufe
  des Projekts unter PHP 7.4 verwenden PHPUnit 9.6, das letzte PHPUnit für PHP 7.4, das die PHP-8-Attribute einiger
  Tests nicht liest.
- **Langsameres PHP.** PHP 8 ist schneller als PHP 7.4, mit OPcache und dem JIT oft deutlich. Eine stark besuchte
  Website gewinnt allein durch den Umzug an Geschwindigkeit.
- **Die Sicherheit hängt von Ihrem Hersteller ab.** Siehe oben: Ein ungepflegtes PHP 7.4 ist ein Risiko, das keine
  Anwendung beseitigen kann.

## Die Haltung zum Lebensende

Die Nutzbarkeit unter PHP 7.4 ist eine **Brücke**, ohne Support. Sie besteht, damit Organisationen ein aktuelles Exponential
betreiben können, während sie ihren Umzug auf PHP 8 vorbereiten. Die Zusagen des Projekts:

- Die Nutzbarkeit unter PHP 7.4 wird nicht in einem Patch-Release entfernt. Wenn es so weit ist, wird es mindestens ein
  Release vorher im Changelog und in dieser Dokumentation angekündigt, und das Release, das sie entfernt, sagt es in
  seinen Hinweisen.
- Entscheidend ist die Sicherheit: Solange gepflegte, gepatchte PHP-7.4-Pakete von Betriebssystemherstellern breit
  verfügbar sind und die Abhängigkeiten unter PHP 7.4 frei von bekannten Schwachstellen gehalten werden können, bleibt
  die Brücke.
- Das neueste PHP wird nie aufgegeben oder verzögert, um ein altes zu halten.
- Jede Website unter PHP 7.4 sollte einen Plan für den Umzug haben. Das
  [Runbook für PHP-Upgrades](hosting/runbooks/php-upgrade.md) zeigt die Schritte; mit einem bereits aktuellen
  Exponential ist der Umzug ein Wechsel von PHP und ein `composer update`, nicht mehr.

## Installation unter PHP 7.4

1. Prüfen Sie den Rechner mit [2. Voraussetzungen](../installation/02-voraussetzungen.md): PHP 7.4 mit den
   erforderlichen Erweiterungen für den Webserver **und** die Kommandozeile (dieselbe Version und dieselben
   Erweiterungen in beiden).
2. Installieren Sie die Linie 6.0.15 wie auf jeder PHP-Version ([Kapitel 3](../installation/03-den-code-beschaffen.md)).
   Führen Sie Composer mit dem PHP 7.4 aus, das die Website ausliefern wird, damit es die Pakete für PHP 7.4 wählt.
3. Liefern Sie die Website mit Apache oder nginx und PHP-FPM aus, oder für einen schnellen Test mit
   `exp:velocity start --engine=php`.
4. Wenn der Rechner auf PHP 8 umzieht, führen Sie `composer update` mit dem neuen PHP aus: Composer stellt das
   Metapaket auf die Version für dieses PHP um und installiert die Pakete, die ausgelassen wurden.

## Verwandte Seiten

- [2. Voraussetzungen](../installation/02-voraussetzungen.md): jede PHP-Version, Erweiterung und Einstellung
- [Betriebssystemunterstützung](betriebssystemunterstuetzung.md)
- [Unterstützung für PHP 8.0](../bc/6.0/php-8.0-unterstuetzung.md): die Ausweichlösungen für PHP 8.0, die PHP 7.4 teilt
- [Unterstützte PHP-Versionen](php-entwickler/unterstuetzte-php-versionen.md): die Regeln für Code und Erweiterungen
- [Runbook für PHP-Upgrades](hosting/runbooks/php-upgrade.md)
- [Freie Dokumentation](freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
