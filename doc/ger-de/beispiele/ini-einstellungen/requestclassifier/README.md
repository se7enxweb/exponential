---
title: requestclassifier.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von requestclassifier.ini.
keywords: Beispiele, Referenz, requestclassifier.ini, examples, reference
type: generated
generated: exp:help:examples
---
# requestclassifier.ini

Die ausgelieferten Einstellungen von `settings/requestclassifier.ini`: 7 Blöcke, 22 Einstellungen.

> Der Anfrage-Klassifizierer: unterscheidet eine Sondierung (/wp-login.php, /.env, /backup.zip), eine Pfadüberschreitung (../), eine Injektion  
> (union select, {{7\*7}}, <script>), ein bekanntes Angriffswerkzeug und eine Häufung von Nicht-gefunden-Antworten von einer gewöhnlichen Anfrage  
> und schreibt für jede solche Anfrage eine Zeile in var/log/security.log statt in 404.log, notice.log oder error.log.  
> Anleitung: doc/eng-us/features/6.0/request-logs.md  
>
> Bearbeiten Sie diese Datei nicht: Überschreiben Sie sie in settings/override/requestclassifier.ini.append.php, in der  
> requestclassifier.ini.append.php eines Siteaccess oder in den Einstellungen einer Erweiterung.  
>
> Nur Einordnung. Wegen eines Urteils wird nichts blockiert, verlangsamt oder anders beantwortet: Die Anfrage  
> erhält die Antwort, die sie ohnehin erhielte, und ihre Logzeile geht nach security.log. Blockieren und Ratenbegrenzung bleiben  
> den Listenern (Listeners\[\] unten) und dem vorgelagerten Webserver überlassen (request-shield unter Velocity lehnt manche  
> Sondierungen ab, bevor PHP läuft, diese erreichen den Klassifizierer also nie).  
>
> Kosten: keine Datenbank. Die Muster eines Regelsatzes werden zu einem regulären Ausdruck kompiliert, pro Version  
> dieser Einstellungen in var/<site>/cache/requestclassifier/ gecacht (darf gelöscht werden; bei der nächsten Anfrage neu erzeugt). Nur eine  
> Antwort 404 oder 410 berührt den Zähler der Nicht-gefunden-Antworten.  
>
> Eine Zeile in security.log (die Adresse maskiert, wie site.ini \[RequestLogSettings\] MaskIP sagt, die Query-Werte mit  
> Geheimnissen und E-Mail-Adressen maskiert, der User-Agent als kurzer Hash und seine ersten 60 Zeichen):  
> \[ Oct 17 2026 09:00:00 \] \[203.0.x.x\] probe/wp-login confidence=high 404 GET /wp-login.php siteaccess=site namespace=/wp-login.php ua=3f2a9c1b "curl/8.5.0"  
>   probe/wp-login      <category>/<rule>: die Kategorie des Regelsatzes und die Regel, die zutraf  
>   confidence=high     low | medium | high, wie sicher die Regel ist  
>   404 GET /wp-login.php   der endgültige HTTP-Status (- wenn unbekannt), die Methode und die URI  
>   namespace=          der Pfad unter dem Siteaccess: /admin/wp-login.php und /site/wp-login.php sind beide  
>                       /wp-login.php, eine Regel sieht also unter jedem Siteaccess denselben Pfad  

| Block | Einstellungen |
|---|---|
| [ClassifierSettings](ClassifierSettings.md) | 5 |
| [ThresholdSettings](ThresholdSettings.md) | 3 |
| [RuleSet-probes](RuleSet-probes.md) | 4 |
| [RuleSet-traversal](RuleSet-traversal.md) | 3 |
| [RuleSet-injection](RuleSet-injection.md) | 3 |
| [RuleSet-scanner](RuleSet-scanner.md) | 3 |
| [RuleSet-threshold](RuleSet-threshold.md) | 1 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/requestclassifier.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
