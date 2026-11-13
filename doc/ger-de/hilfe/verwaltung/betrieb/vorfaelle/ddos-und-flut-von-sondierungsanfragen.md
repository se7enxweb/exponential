---
title: DDoS und Fluten von Sondierungsanfragen
audience: government, admin, hosting
category: public-sector
order: 80
summary: Runbook für eine öffentliche Website unter einer Flut von Anfragen - Sondierungen mit security.log und der Fehlerübersicht von echter Last unterscheiden, was Exponential blockiert und was nicht, die Website mit Caches, Anfrageregeln und Request Shield vor dem Kernel (seit 6.0.15 Teil der Standardinstallation) am Laufen halten und mit Ihrem Netzanbieter zusammenarbeiten.
keywords: DDoS, Denial of Service, Flut von Sondierungsanfragen, Bot-Verkehr, Scanner, security.log, Schwellenwert, 404-Rate, request-shield, Request Shield, exp_request_shield, Setup > Request Shield, WAF, Anfrageregeln, statischer Cache, Velocity-Antwortcache, Ratenbegrenzung, Vorfall, denial of service, probe flood, bot traffic, threshold, 404 rate, request rules, static cache, Velocity response cache, rate limiting, incident
type: help
---
# DDoS und Fluten von Sondierungsanfragen

Dieses Runbook richtet sich an die Administration, wenn eine öffentliche Website durch Anfragen verlangsamt oder
überlastet wird: ein automatischer Scanner, ein Botnetz oder ein Ansturm von Interesse nach einer öffentlichen
Ankündigung (das ist kein Angriff und darf nicht blockiert werden).

## Inhalt
1. [Was Exponential tut und was nicht](#was-exponential-tut-und-was-nicht)
2. [Den Verkehr ansehen](#den-verkehr-ansehen)
3. [Die Website am Laufen halten](#die-website-am-laufen-halten)
4. [Request Shield](#request-shield)
5. [Davor blockieren](#davor-blockieren)
6. [Große Angriffe](#große-angriffe)
7. [Verwandte Seiten](#verwandte-seiten)

## Was Exponential tut und was nicht

- Der Anfrageklassifizierer **klassifiziert** Sondierungen, Injektionen, Pfadmanipulationen, Scanner und 404-Häufungen
  in `security.log`; er **blockiert nichts** ([Anfrage-Logs](../../../../funktionen/6.0/anfrageprotokolle.md)).
- Anfrageregeln (`requestrules.ini`) können ausgewählte Anfragen frühzeitig beantworten, zum Beispiel mit „not found“
  ([Anfrageregeln](../../../../funktionen/6.0/anfrageregeln.md)).
- Request Shield (`exp_request_shield`, seit 6.0.15 Teil der Standardinstallation) **blockiert**, bevor der Kernel
  startet: Es weist Scanner und System-URLs ab, bremst Fluten mit einer Browserprüfung und sperrt Wiederholungstäter
  für eine Weile ([Request Shield](#request-shield)).
- Bandbreite und volumetrische Angriffe sind Aufgabe dessen, was vor dem Server steht: der Webserver, ein Reverse Proxy,
  ein CDN oder Ihr Netzanbieter
  ([Sicherheitshärtung, request-shield](../../../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield)).

## Den Verkehr ansehen

```bash
./console exp:logs --tail=var/log/security.log --lines=100
./console exp:logs --search='threshold/404-rate' --file=var/log/security.log
```

**Setup > Logdateien**, **Fehlerübersicht**, Log „Security (request classifier)“ gruppiert die Zeilen nach Kategorie und
Regel; `404.log` nach Grund gruppiert zeigt, was angefragt wird. Das Zugriffslog des Webservers (oder das von Velocity,
`var/vc/qbix/log/site-access.log`) zeigt die Anfrageraten je Client. Die maskierten Adressen in den Logs von
Exponential reichen aus, um Muster zu erkennen; Ihr Netzwerkteam hat die vollständigen Adressen in seinen eigenen Logs.

| Muster | Wahrscheinlich |
|---|---|
| Viele `probe/*` und 404 aus wenigen Netzen | ein Scanner; harmlos, wenn nichts mit 200 antwortet |
| `injection/*` mit Status 200 | genauer ansehen, gemeinsam mit einer Entwicklerin oder einem Entwickler |
| Echte Seiten, viele Clients, nach einer Ankündigung | echte Last: skalieren und cachen, nicht blockieren |
| Riesige Mengen von allem | ein Denial-of-Service-Angriff: Ihr Anbieter |

## Die Website am Laufen halten

- **Caches vor PHP**: Unter Velocity liefert der Antwortcache Seiten ohne PHP aus
  ([Velocity-Antwortcache](../../../../funktionen/6.0/velocity-antwort-cache.md)); mit dem statischen Cache beantwortet
  der Webserver öffentliche Seiten als Dateien ([Statischer Cache](../verfuegbarkeit/statischer-cache.md)).
- **Wärmen Sie** die meistbesuchten Seiten nach dem Leeren eines Caches vor:
  `php bin/php/preload.php --apply --siteaccess=site --allow-root-user`.
- Der **Wartungsmodus** mit einer kurzen Meldung ist das letzte Mittel für eine Website, die die Last nicht bewältigt;
  er antwortet, bevor Einstellungen oder die Datenbank berührt werden.

## Request Shield

Seit Exponential 6.0.15 hat jede neue Installation request-shield (vom CJW Network) vor dem Kernel, über die
Erweiterung `exp_request_shield` und eine Hook-Zeile in `config.php`; eine aktualisierte Installation hat es, sobald
diese Zeile ergänzt ist ([Umstieg auf Request Shield](../../../../bc/6.0/request-shield.md)). Es läuft vor dem frühen
Ausstieg des HTTP-Caches, antwortet unter PHP-FPM und Velocity gleich und **setzt durch, wie installiert**:

- Scanner, interne Dateien, System-URLs und verirrte Formularsendungen werden abgewiesen, in wenigen Mikrosekunden je
  Anfrage;
- ab 120 Seiten pro Minute erhält ein Client die unsichtbare Browserprüfung, und zehn Abweisungen in zehn Minuten
  sperren seine Adresse für eine Stunde (verifizierte Suchmaschinen werden nie gesperrt);
- die Anmeldung und die Formulare der Administration verlangen die Browserprüfung.

Während einer Flut:

1. Öffnen Sie **Setup > Request Shield**: Das Dashboard zeigt die Entscheidungen pro Stunde und die Regeln, die am
   häufigsten entschieden haben; die Seite **Log** filtert nach Zeitraum und Entscheidung.
   `var/request-shield/log/shield.log` ist dasselbe Log auf der Festplatte.
2. Werden echte Besucher abgewiesen (eine Ankündigung, ein Seitenalias, der wie eine System-URL aussieht), schalten Sie
   währenddessen auf **Beobachten**, ergänzen eine Ausnahme in der Regeldatei der Website in `settings/request-shield/`,
   prüfen sie mit
   `php extension/exp_request_shield/bin/request-shield check settings/request-shield/request-shield.rules` und
   schalten zurück auf **Durchsetzen**.
3. Ein Netz, das immer wiederkommt, lässt sich mit einer weiteren Regel in derselben Datei abweisen; die Server lesen
   eine geänderte Regeldatei innerhalb von Sekunden, ohne Neustart.
4. Nehmen Sie den Schutz während eines Angriffs nie heraus. Muss er sofort anhalten, legen Sie
   `var/request-shield/PAUSED` an (oder drücken **Pausieren**) und entfernen die Datei danach.

Einzelheiten: [Request Shield](../../../../funktionen/6.0/request-shield.md),
[Request Shield betreiben](../../../../anleitungen/request-shield.md).

## Davor blockieren

Was der Schutz nicht halten kann, geben Sie an diejenigen weiter, die die vorgeschaltete Ebene betreiben: Pfade, die
nur Scanner anfragen, User-Agents von Angriffswerkzeugen, Netze mit Fluten. Eine Flut, die die Leitung füllt, erreicht
PHP nie; blockieren Sie sie deshalb am Rand.

## Große Angriffe

Volumetrische Angriffe werden von Netzanbietern und ihren Schutzdiensten abgewehrt. Klären Sie vor einem Vorfall, wer
die Ansprechperson bei Ihrem Anbieter ist und welchen Schutz Ihr Vertrag umfasst; Verwaltungsnetze haben dafür oft einen
zentralen Dienst. Melden Sie den Angriff Ihrem Sicherheitsteam oder der nationalen Behörde, wie es Ihre Regeln
verlangen.

## Verwandte Seiten
- [Anfrage-Logs: 404 und Sicherheit](../ueberwachung/anfrageprotokolle-404-und-sicherheit.md)
- [Kapazitätsplanung](../verfuegbarkeit/kapazitaetsplanung.md)
- [Langsame Website (Hosting-Runbook)](../../../hosting/runbooks/langsame-website.md)
- [Request-shield und Anfrageregeln (Hosting)](../../../hosting/sicherheit/request-shield-und-request-regeln.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
