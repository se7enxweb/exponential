---
title: "Lektion 2.4: Der Antwort-Cache und HTTPS-Zertifikate"
audience: trainer, hosting, admin
category: training
order: 24
summary: Die Teilnehmenden weisen einen Treffer im Antwort-Cache nach, erklären, was der Cache nie speichert und wie man ihn leert, schalten dann HTTPS mit einem Zertifikatspaar ein und lesen es mit exp:velocity ssl show. 45 Minuten.
keywords: hosting und betrieb, Velocity, antwort-cache, X-Cache, DefaultTtl, NotFoundSeconds, cache clear, cache stats, HTTPS, zertifikat, ssl show, Let's Encrypt, ACME, HSTS, selbstsigniert, hosting and operations, response cache, certificate, self-signed
type: help
---
# Lektion 2.4: Der Antwort-Cache und HTTPS-Zertifikate

Zwei Dinge, die Velocity selbst erledigt und die eine traditionelle PHP-FPM-Einrichtung anderen Werkzeugen überlässt:
wiederholte anonyme Seiten aus dem eigenen Antwort-Cache beantworten und HTTPS mit eigenen Zertifikaten ausliefern.
Support-Mitarbeitende begegnen beidem jede Woche („meine Änderung erscheint nicht“, „die Zertifikatswarnung“), deshalb
gibt diese Lektion den Teilnehmenden die Prüfungen und die Antworten.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Modul | [Modul 2: Velocity](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Systemadministratoren und Support-Mitarbeitende im Hosting |
| Voraussetzungen | [Lektion 2.3](worker-und-pools.md); `openssl` auf dem Rechner der Person |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- mit zwei Anfragen einen Treffer im Antwort-Cache zeigen und `X-Cache` und `Age` lesen;
- aufzählen, was der Antwort-Cache nie speichert, einschließlich jeder Seite für einen angemeldeten Besucher;
- den Antwort-Cache leeren und sagen, wann kein Neustart nötig ist;
- HTTPS mit einem Zertifikatspaar einschalten und das ausgelieferte Zertifikat mit `exp:velocity ssl show` lesen;
- den selbstsignierten Ersatz, den Austausch von Zertifikaten im laufenden Betrieb und das Risiko einer langen
  HSTS-Dauer erklären.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Velocity-Antwort-Cache](../../../hosting/leistung/antwort-cache-von-velocity.md),
[Velocity-HTTPS-Zertifikate](../../../hosting/server/https-zertifikate-in-velocity.md) und
[Kapitel 8, Abschnitt 8.3.9](../../../../installation/08-die-website-ausliefern.md#839-https-direkt-von-velocity).

1. **Was der Cache tut** (4 Minuten). Dargestellte anonyme Seiten werden im Serverprozess beantwortet, bevor ein Worker
   gewählt wird: kein Fork, kein PHP, keine Datenbank. Bedingte Anfragen bekommen `304`; eine abgelaufene Seite wird
   weiter ausgeliefert, während eine Anfrage den Ersatz darstellt (`StaleWhileRevalidate`, 60 s).
2. **Was er nie speichert** (3 Minuten). Angemeldete Besucher werden nie daraus bedient; Anfragen mit dem
   Sitzungs-Cookie oder einem `Authorization`-Header; Antworten, die ein Cookie setzen oder `no-store` oder `private`
   sagen; alles außer `GET`; Antworten außer 200 (ein 404 nur, wenn `NotFoundSeconds` über 0 liegt).
3. **Aktualität** (3 Minuten). `DefaultTtl` (30 s) ist das Alter, das eine Seite haben darf, die ein Besucher nach einer
   Änderung sieht. Leeren mit `exp:velocity cache clear`; nach einer Template-Änderung ist kein Neustart nötig. Der
   Navigations-Cache des Browsers bewirkt, dass eine veröffentlichte Änderung erst beim zweiten Aufruf erscheinen kann.
4. **HTTPS** (6 Minuten). Mit einem Paar von PEM-Dateien: `[HTTPSSettings] Certificate`, `Key` und `Enabled true`
   setzen, neu starten. Das Paar wird vor der Verwendung geprüft; ein Paar, das die Prüfung nicht besteht, wird nie
   gezeigt, ein selbstsigniertes Zertifikat springt ein. Eine erneuerte Datei wird innerhalb einer Minute im laufenden
   Betrieb ausgetauscht. Let's Encrypt und andere Quellen werden in einem Snippet in `conf-available/` eingerichtet;
   Port 80 muss den Server für die HTTP-01-Challenge erreichen.
5. **HSTS und Sicherungen** (4 Minuten). Ein Browser, der `Strict-Transport-Security` gesehen hat, verweigert einfaches
   HTTP für die ganze Dauer; erhöhen Sie `HSTSMaxAge` erst dann auf ein Jahr, wenn jeder Name dauerhaft auf HTTPS ist.
   Sichern Sie das Verzeichnis `ssl/`; nur die ACME-Kontoschlüssel lassen sich nicht neu erzeugen.

### Häufige Fragen

**Ein Kunde sagt, angemeldete Benutzer sehen eine alte Seite.** Nicht der Antwort-Cache: Angemeldete Besucher bekommen
ihn nie. Sehen Sie sich den View-Cache oder den HTTP-Cache an.

**Warum werden 404 standardmäßig nicht zwischengespeichert?** `NotFoundSeconds=0` schickt jedes 404 an PHP, sodass
`404.log` und das Sicherheitslog jedes sehen. Lassen Sie den Wert auf 0, wo das Sicherheitsmonitoring diese Logs liest.

**Wie früh sollte ich mir wegen des Ablaufs Sorgen machen?** Ein 90-Tage-Zertifikat wird erneuert, wenn noch etwa
30 Tage übrig sind; weniger als 14 verbleibende Tage bedeuten, dass die Erneuerung seit zwei Wochen fehlschlägt.
Alarmieren Sie bei `daysLeft` unter 14.

**Leitet die mitgelieferte Konfiguration HTTP auf HTTPS weiter?** `velocity.ini` hat keine Einstellung, die das eine auf
das andere weiterleitet; beide lauschen gleichzeitig. Machen Sie die eigenen Adressen der Website zu HTTPS-Adressen,
bevor Sie HSTS erhöhen.

### Stolperfallen

- **Angemeldet testen.** Ein Browser mit einer Sitzung der Administration sieht nie einen Cache-Treffer. Verwenden Sie
  `curl`.
- **ACME im Kurs.** Let's Encrypt braucht öffentliches DNS und Port 80 aus dem Internet. Verwenden Sie im Kurs wie in der
  Übung ein selbst erzeugtes Paar und zeigen Sie ACME nur am Beamer.
- **HSTS auf einem Schulungs-Hostnamen.** Setzen Sie `HSTSMaxAge` nicht über den ausgelieferten Wert bei einem Namen,
  den die Teilnehmenden wiederverwenden.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, ausgeliefert von Velocity auf den mitgelieferten Ports (8088 und 8080).

1. Fragen Sie die Startseite zweimal an, ohne sich anzumelden:

   ```bash
   curl -sI http://127.0.0.1:8088/ | grep -iE 'x-cache|age'
   curl -sI http://127.0.0.1:8088/ | grep -iE 'x-cache|age'
   ```

2. Zeigen Sie den Zustand des Caches: `php bin/php/console exp:velocity cache stats`.
3. Leeren Sie ihn: `php bin/php/console exp:velocity cache clear`, und wiederholen Sie dann Schritt 1 einmal.
4. Erzeugen Sie für die Übung ein selbstsigniertes Paar in einem Verzeichnis Ihrer Installation:

   ```bash
   mkdir -p var/training-tls
   openssl req -x509 -newkey rsa:2048 -nodes -days 30 -subj "/CN=training.example.invalid" \
     -keyout var/training-tls/key.pem -out var/training-tls/cert.pem
   ```

5. Verweisen Sie Velocity auf das Paar und schalten Sie HTTPS ein:

   ```bash
   php bin/php/console exp:velocity config set HTTPSSettings Certificate var/training-tls/cert.pem
   php bin/php/console exp:velocity config set HTTPSSettings Key var/training-tls/key.pem
   php bin/php/console exp:velocity config set HTTPSSettings Enabled true
   php bin/php/console exp:velocity restart
   ```

6. Lesen Sie `php bin/php/console exp:velocity status` und suchen Sie die HTTPS-Adresse.
7. Zeigen Sie das ausgelieferte Zertifikat: `php bin/php/console exp:velocity ssl show`.
8. Fragen Sie die Website über HTTPS an und akzeptieren Sie dabei das selbst erzeugte Zertifikat:
   `curl -skI https://127.0.0.1:8080/ | head -1`.
9. Schalten Sie HTTPS mit `config set HTTPSSettings Enabled false` wieder aus und starten Sie neu.

## Erwartetes Ergebnis

- Schritt 1: Die zweite Antwort trägt `X-Cache: HIT` und einen `Age`-Header.
- Schritt 3: Die erste Anfrage nach dem Leeren ist kein Treffer; die nächste wieder.
- Schritt 7 listet das Zertifikat für `training.example.invalid` mit Ablaufdatum und verbleibenden Tagen auf.
- Schritt 8 gibt `HTTP/2 200` oder eine Weiterleitung aus.
- Der Trainer prüft das, indem er nach den verbleibenden Tagen fragt, die `ssl show` ausgegeben hat.

## Quiz

1. Welcher Header zeigt, dass eine Seite aus dem Antwort-Cache kam?
2. Ein angemeldeter Redakteur sieht eine alte Seite. Ist der Antwort-Cache die Ursache?
3. Was liefert Velocity aus, wenn das konfigurierte Zertifikat abgelaufen ist?
   A. nichts, HTTPS stoppt B. das abgelaufene Zertifikat C. einen selbstsignierten Ersatz D. einfaches HTTP auf
   Port 443
4. Ein Erneuerungswerkzeug ersetzt die Zertifikatsdatei. Was muss der Betreiber tun?
5. Welche Datei im Verzeichnis `ssl/` lässt sich nicht neu erzeugen und muss gesichert werden?

<details>
<summary>Antworten</summary>

1. `X-Cache: HIT`, zusammen mit einem `Age`-Header.
2. Nein. Angemeldete Besucher werden nie daraus bedient; sehen Sie sich den View-Cache oder den HTTP-Cache an.
3. **C.** Ein Paar, das die Prüfung nicht besteht, wird nie gezeigt; das selbstsignierte Zertifikat springt ein, bis das
   echte verwendbar ist ([Velocity-HTTPS-Zertifikate](../../../hosting/server/https-zertifikate-in-velocity.md#worauf-sie-sich-verlassen-können)).
4. Nichts: Ein geändertes, gültiges Paar wird innerhalb von `watchInterval` (60 Sekunden) ohne Neustart im laufenden
   Betrieb ausgetauscht.
5. Die ACME-Kontoschlüssel (`acme/account-<ca>.pem`).

</details>

## Weiterführende Informationen

- [Velocity-Antwort-Cache](../../../hosting/leistung/antwort-cache-von-velocity.md): Einstellungen und Antworten für den
  Support.
- [Velocity-HTTPS-Zertifikate](../../../hosting/server/https-zertifikate-in-velocity.md): jede Zertifikatsquelle.
- [Velocity, HTTPS und Zertifikate](../../../../funktionen/6.0/velocity-https-zertifikate.md): die Funktionsseite.
- [Ablauf von Zertifikaten](../../../hosting/runbooks/ablauf-von-zertifikaten.md): das Runbook.
- [TLS und HTTPS](../../../hosting/sicherheit/tls-und-https.md): HTTPS für Hoster allgemein.

## Verwandte Seiten

- [Modul 2: Velocity](README.md)
- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
