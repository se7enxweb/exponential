---
title: request-shield vor einer öffentlichen Website
audience: government, admin, hosting
category: public-sector
order: 140
summary: Für öffentliche Stellen, deren Websites ständig abgetastet werden - was Request Shield ist (eine kleine PHP-Firewall des CJW Network, seit Exponential 6.0.15 über die Erweiterung exp_request_shield Teil der Standardinstallation), wie es die Anfrageklassifizierung des Kernels ergänzt und wie Sie es prüfen, anpassen und bei einer ungewöhnlichen Website in den Beobachtungsmodus schalten.
keywords: request-shield, Request Shield, exp_request_shield, Setup > Request Shield, Web Application Firewall, WAF, Bot-Schutz, Ratenbegrenzung, Browserprüfung, Scanner, Beobachtungsmodus, Durchsetzungsmodus, Anfrageklassifizierung, öffentlicher Sektor, web application firewall, bot protection, rate limiting, browser check, scanners, monitor mode, enforce mode, request classifier, security.log, Velocity, EXP_REQUEST_PREPEND_FILE, public sector
type: help
---
# request-shield vor einer öffentlichen Website

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Hosting-Teams öffentlicher Websites.
Öffentliche Websites erhalten jeden Tag Scanner, Angriffe auf Zugangsdaten und Scraper. Request Shield ist eine kleine
Web Application Firewall für PHP, die über jede Anfrage entscheidet, **bevor der Kernel von Exponential startet**: Sie
weist Scanner und fehlerhafte Anfragen ab, bremst Fluten, verlangt von verdächtigen Clients eine unsichtbare
Browserprüfung und kann die Administration einschränken
([Sicherheitshärtung: Ein vorgeschalteter Schutz](../../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield)).
Seit Exponential 6.0.15 ist es Teil der Standardinstallation und schützt eine neue Website ab der ersten Anfrage
([Request Shield](../../../funktionen/6.0/request-shield.md)).

## Inhalt

1. [Was es ist und was nicht](#was-es-ist-und-was-nicht)
2. [Klassifizierung und Schutz nebeneinander](#klassifizierung-und-schutz-nebeneinander)
3. [Prüfen und anpassen](#prüfen-und-anpassen)
4. [Hinweise zu Beschaffung und Steuerung](#hinweise-zu-beschaffung-und-steuerung)
5. [Verwandte Seiten](#verwandte-seiten)

## Was es ist und was nicht

- Die Bibliothek request-shield 0.4.0 der JAC Systeme GmbH (CJW Network), MIT-Lizenz,
  <https://github.com/cjw-network/request-shield>. Sie bleibt ein Projekt des CJW Network.
- **Seit 6.0.15 Teil der Standardinstallation**: Die `composer.json` im Wurzelverzeichnis verlangt die Erweiterung
  `exp_request_shield` (`se7enxweb/exp_request_shield` 0.0.1.1, GPL-2.0-or-later). Sie enthält die Bibliothek (und
  einen Build für PHP 7.4; PHP 7.4 bis 8.5) und führt sie über eine Hook-Zeile in `config.php` aus. Eine neue
  Installation erhält diese Zeile aus `config.php-RECOMMENDED`; eine aktualisierte Installation ergänzt sie selbst,
  denn `config.php` wird nie überschrieben ([Umstieg auf Request Shield](../../../bc/6.0/request-shield.md)).
- Es **setzt durch, wie installiert**: Scanner, Fluten, verirrte Formularsendungen und System-URLs werden abgewiesen,
  und die Anmeldung und die Formulare der Administration verlangen die Browserprüfung. Jede Administration, die über
  einen Pfad erreicht wird (`/admin`, `/adminui`, `/editor` und die älteren Admin-Designs), behält ihre System-URLs,
  Module und Formulare; eine Administration mit eigenem Host wird in der eigenen Regeldatei der Website eingerichtet
  ([Konfiguration](https://github.com/se7enxweb/exp_request_shield/blob/main/doc/configuration.md)).
- Die eigene JSON-API des Shields bleibt aus, und seine Statistikseiten sind für alle außer dem Server geschlossen,
  solange die Website ihnen keine Anmeldung gibt.
- Es läuft vor dem frühen Ausstieg des HTTP-Caches, sodass auch zwischengespeicherte Seiten geschützt sind, und PHP-FPM
  und Exponential Velocity geben dieselben Antworten.
- Es kann ein richtiges Passwort nicht von einem falschen unterscheiden: Kontosperre und zweistufige Anmeldung bleiben
  Aufgabe von Exponential.
- Es sieht keine statischen Dateien, die der Webserver selbst ausliefert.

## Klassifizierung und Schutz nebeneinander

| | Anfrageklassifizierung des Kernels | Request Shield |
|---|---|---|
| Tut | klassifiziert nur (Sondierung, Traversal, Injection, Scanner, 404-Häufungen) | entscheidet: abweisen, bremsen, prüfen, sperren oder nur protokollieren |
| Läuft | im Kernel | vor dem Kernel |
| Protokoll | `var/log/security.log` | `var/request-shield/log/shield.log`, zu lesen unter **Setup > Request Shield > Log** |
| Einstellungen | `settings/requestclassifier.ini` | seine Regeldateien, die eigenen der Website in `settings/request-shield/` |

Wo sie sich überschneiden, verwenden beide dieselben Regelnamen, sodass sich eine Zeile des einen im anderen finden
lässt.

## Prüfen und anpassen

1. Öffnen Sie **Setup > Request Shield** (Richtlinie `requestshield/read`): Das Dashboard zeigt, ob der Schutz für
   diese Seite lief, in welchem Modus, aus welcher Regeldatei und was er in den letzten 24 Stunden entschieden hat. Die
   Schalter (Richtlinie `requestshield/manage`) pausieren ihn, schalten ihn wieder ein oder setzen den Modus auf
   Durchsetzen, Beobachten oder Aus.
2. Geben Sie der Website eine eigene Regeldatei in `settings/request-shield/` (der erste Modusschalter in der
   Administration legt sie aus der Vorgabe an), und wählen Sie die Regeln der Administration:
   `exponential-admin-uri.rules` (Administration unter `/admin` auf demselben Host) oder
   `exponential-admin-host.rules` (Administration auf eigenem Host).
3. Passen Sie die website-spezifischen Zeilen an (Hostnamen, Load Balancer, Siteaccesses, Module von Erweiterungen,
   die Formulare annehmen).
4. Lesen Sie in den ersten Tagen **Setup > Request Shield > Log**. Eine Website mit vielen ungewöhnlichen Adressen kann
   währenddessen auf **Beobachten** schalten: Jede Entscheidung wird dann so protokolliert, wie sie getroffen würde,
   und niemand wird abgewiesen. Ergänzen Sie eine Ausnahme für das, was eine echte Besucherin oder ein Redakteur getan
   hat, und kehren Sie dann zu **Durchsetzen** zurück.
5. Prüfen und testen Sie nach jeder Änderung, als der Benutzer, unter dem das PHP der Website läuft:
   `php extension/exp_request_shield/bin/request-shield check settings/request-shield/request-shield.rules`, `test`
   mit derselben Datei und `trace <main file> "GET https://www.example.gov/..."` für eine einzelne Anfrage.
6. Zum sofortigen Abschalten auf allen Servern legen Sie `var/request-shield/PAUSED` an (oder verwenden **Pausieren**);
   entfernen Sie die Datei, um ihn wieder einzuschalten.

Die Anleitung Schritt für Schritt: [Request Shield betreiben](../../../anleitungen/request-shield.md).

## Hinweise zu Beschaffung und Steuerung

- Es ist Open-Source-Software eines Dritten, ausgeliefert in einer Erweiterung von Exponential: Führen Sie
  `exp_request_shield` und die enthaltene Version von request-shield mit ihren Lizenzen in Ihrem Softwareverzeichnis und
  verfolgen Sie ihre Releases wie bei jeder Abhängigkeit.
- Sein Protokoll enthält das Netz jedes Clients (standardmäßig die Adresse auf /24 bzw. /48 gekürzt); nehmen Sie es in
  Ihr Verzeichnis von Verarbeitungstätigkeiten und seine Aufbewahrung in Ihren Aufbewahrungsplan auf.
- Die ausgelieferten Regeln beschränken System-URLs und Administrationsmodule auf der öffentlichen Website, was auch die
  Angriffsfläche für Scanner verkleinert.

## Verwandte Seiten

- [Sicherheitshärtung](../../sicherheitshaertung.md)
- [Request Shield (Funktion)](../../../funktionen/6.0/request-shield.md) und [Request Shield betreiben (Anleitung)](../../../anleitungen/request-shield.md)
- [Anfrageprotokolle](../../../funktionen/6.0/anfrageprotokolle.md)
- [request-shield und Anfrageregeln für Hoster](../../hosting/sicherheit/request-shield-und-request-regeln.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
