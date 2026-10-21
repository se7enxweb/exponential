---
title: request-shield vor einer öffentlichen Website
audience: government, admin, hosting
category: public-sector
order: 140
summary: Für öffentliche Stellen, deren Websites ständig abgetastet werden - was request-shield ist (eine eigenständige, MIT-lizenzierte PHP-Firewall des CJW Network, nicht Teil der composer.json von Exponential), wie es die Anfrageklassifizierung des Kernels ergänzt und wie Sie es sicher einführen, zuerst im Beobachtungsmodus.
keywords: request-shield, Web Application Firewall, WAF, Bot-Schutz, Ratenbegrenzung, Browserprüfung, Scanner, Beobachtungsmodus, Durchsetzungsmodus, Anfrageklassifizierung, öffentlicher Sektor, web application firewall, bot protection, rate limiting, browser check, scanners, monitor mode, enforce mode, request classifier, security.log, Velocity, EXP_REQUEST_PREPEND_FILE, public sector
type: help
---
# request-shield vor einer öffentlichen Website

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Hosting-Teams öffentlicher Websites.
Öffentliche Websites erhalten jeden Tag Scanner, Angriffe auf Zugangsdaten und Scraper. request-shield ist eine kleine
Web Application Firewall für PHP, die über jede Anfrage entscheidet, **bevor der Kernel von Exponential startet**: Sie
weist Scanner und fehlerhafte Anfragen ab, bremst Fluten, verlangt von verdächtigen Clients eine unsichtbare
Browserprüfung und kann die Administration einschränken
([Sicherheitshärtung: Ein vorgeschalteter Schutz](../../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield)).

## Inhalt

1. [Was es ist und was nicht](#was-es-ist-und-was-nicht)
2. [Klassifizierung und Schutz nebeneinander](#klassifizierung-und-schutz-nebeneinander)
3. [Einführen](#einführen)
4. [Hinweise zu Beschaffung und Steuerung](#hinweise-zu-beschaffung-und-steuerung)
5. [Verwandte Seiten](#verwandte-seiten)

## Was es ist und was nicht

- Eine eigenständige Bibliothek: <https://github.com/cjw-network/request-shield>, MIT-Lizenz, vom CJW Network.
- **Nicht Teil der `composer.json` von Exponential**; Sie fügen es mit `composer require cjw-network/request-shield`
  hinzu oder als Checkout außerhalb des Document-Root.
- Es kann ein richtiges Passwort nicht von einem falschen unterscheiden: Kontosperre und zweistufige Anmeldung bleiben
  Aufgabe von Exponential.
- Es sieht keine statischen Dateien, die der Webserver selbst ausliefert.

## Klassifizierung und Schutz nebeneinander

| | Anfrageklassifizierung des Kernels | request-shield |
|---|---|---|
| Tut | klassifiziert nur (Sondierung, Traversal, Injection, Scanner, 404-Häufungen) | entscheidet: abweisen, bremsen, prüfen, sperren oder nur protokollieren |
| Läuft | im Kernel | vor dem Kernel |
| Protokoll | `var/log/security.log` | ein eigenes Protokoll und eine Live-Ansicht unter `/rs/` |
| Einstellungen | `settings/requestclassifier.ini` | seine Regeldateien |

Wo sie sich überschneiden, verwenden beide dieselben Regelnamen, sodass sich eine Zeile des einen im anderen finden
lässt.

## Einführen

1. Installieren Sie die Bibliothek; kopieren Sie die Beispielregeln für Exponential aus ihrem Verzeichnis
   `examples/exponential/` in ein Verzeichnis, das der Webserver nie ausliefert (die Bibliothek schlägt
   `settings/request-shield/` vor).
2. Wählen Sie die Hauptdatei: `exponential-admin-uri.rules` (Administration unter `/admin` auf demselben Host) oder
   `exponential-admin-host.rules` (Administration auf eigenem Host).
3. Schalten Sie es für jede Anfrage ein: mit PHP-FPM ein Aufruf von `Shield::protectFile()` am Anfang von `config.php`
   oder `auto_prepend_file`; unter Velocity die Datei, die in der Konstante `EXP_REQUEST_PREPEND_FILE` genannt ist (seit
   6.0.15).
4. **Zuerst der Beobachtungsmodus** (`set mode monitor`, wie die Beispiele ausgeliefert werden): Lesen Sie das
   Protokoll einige Tage lang, während die Redaktion arbeitet.
5. Passen Sie die website-spezifischen Zeilen an (Siteaccesses, Module von Erweiterungen, die Formulare annehmen,
   Hostnamen, Load Balancer).
6. Prüfen und testen Sie nach jeder Änderung: `bin/request-shield check <main file>`,
   `bin/request-shield test <main file>`, `bin/request-shield trace <main file> "GET https://www.example.gov/..."`.
7. Wechseln Sie zu `set mode enforce`; beobachten Sie die Sperr- und strengen Regeln, bis ihr Protokoll sauber ist.
8. Zum Abschalten verwenden Sie `set mode off`, statt das Include zu entfernen.

## Hinweise zu Beschaffung und Steuerung

- Es ist Open-Source-Software eines Dritten: Führen Sie es mit Lizenz und Version in Ihrem Softwareverzeichnis und
  verfolgen Sie seine Releases wie bei jeder Abhängigkeit.
- Sein Protokoll enthält Adressen von Besuchern; nehmen Sie es in Ihr Verzeichnis von Verarbeitungstätigkeiten und
  seine Aufbewahrung in Ihren Aufbewahrungsplan auf.
- Die Beispielregeln beschränken System-URLs und Administrationsmodule auf der öffentlichen Website, was auch die
  Angriffsfläche für Scanner verkleinert.

## Verwandte Seiten

- [Sicherheitshärtung](../../sicherheitshaertung.md)
- [Anfrageprotokolle](../../../../eng-us/features/6.0/request-logs.md)
- [request-shield und Anfrageregeln für Hoster](../../hosting/sicherheit/request-shield-und-request-regeln.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
