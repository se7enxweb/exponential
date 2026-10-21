# Control Panel und Dashboard von Velocity

Diese Seite richtet sich an Administratoren, die einen Velocity-Server betreiben und ihn aus einem Browser beobachten und
verwalten möchten. Jeder Velocity-Server hat zwei eingebaute Webansichten seiner selbst:

- das **Dashboard** unter `/Q/dashboard`: laufende Anfragen, Worker, Speicher, Statuscodes und meistaufgerufene Pfade,
  über einen WebSocket (ohne Neuladen);
- das **Control Panel** unter `/Q/panel`: eine passwortgeschützte Verwaltungsseite, die Apps, Domains, Zertifikate, den
  Antwort-Cache, Protokolle, Worker und Einstellungen verwaltet.

Sie antworten dem Server, nicht Ihrer Anwendung, und funktionieren daher weiter, wenn die Anwendung defekt ist. Beide
tragen dieselbe Werkzeugleiste (Dashboard, Control Panel, Documentation, PHP Info, Health, Metrics, Shell), sodass jede
Ansicht von den anderen nur einen Klick entfernt ist. Gilt für Exponential Velocity 0.0.4.x.

Mit ihnen sehen Sie, was der Server tut, ohne sich am Rechner anzumelden (Trefferquote, beschäftigte und freie Worker,
Swap, langsamste Anfragen); Sie ändern den Status einer Domain, erneuern ein Zertifikat, leeren den Cache oder lesen
gefilterte Protokolle aus einem Browser; und Sie gestalten die Seiten neu, ohne die Engine anzufassen.

## Erste Anmeldung

1. Öffnen Sie `https://your-host/Q/panel`.
2. Auf einer neuen Installation akzeptiert das Panel einmalig einen dokumentierten Standardschlüssel, nur damit Sie ein
   echtes Passwort wählen können (die Panel-Anleitung der Engine nennt ihn). Bis Sie das tun, wird jede andere
   Panel-Aktion abgelehnt.
3. Wählen Sie ein Passwort, das die folgenden Regeln erfüllt. Der Standard funktioniert danach nirgends mehr.

**Sperren Sie auf einem aus dem Internet erreichbaren Server den Standard, bevor Sie live gehen**, oder setzen Sie das
Passwort sofort auf der Kommandozeile:

```bash
qbixctl panel:password --root=/path/to/web              # asks twice, without echo
qbixctl panel:password --root=/path/to/web --generate   # makes a strong one, prints it once
```

Übergeben Sie dasselbe `--root` (oder `--app`, `--conf-dir`, `--config`), mit dem der Server läuft. Ein laufender Server
verwendet das neue Passwort bei seiner nächsten Anfrage; eine Änderung beendet jede Session.

### Passwortregeln

- Mindestens 16 Zeichen (höchstens 72 Bytes: bcrypt liest nicht mehr).
- Ein Groß- und ein Kleinbuchstabe, eine Ziffer und ein Sonderzeichen; mindestens 10 verschiedene Zeichen.
- Kein Buchstabe dreimal hintereinander, keine Folge von vier in Reihe, keiner der Produkt- oder Hostnamen, kein
  gängiges Passwort.
- Mindestens 80 Bit geschätzte Stärke und verschieden vom aktuellen Passwort.

Passwörter werden mit bcrypt gespeichert (standardmäßig Kostenfaktor 12, neu gehasht, wenn sich der Kostenfaktor
ändert). Fünf fehlgeschlagene Anmeldungen von einer Adresse sperren diese für 60 Sekunden, mit jeder weiteren Sperre
verdoppelt bis zu einer Stunde (eine gesperrte Anmeldung erhält `429`).

## Wenn das Panel gesperrt ist: die Vertrauensregel

Bevor irgendetwas gelesen oder geschrieben wird, muss jedes Verzeichnis vom Zugangsdatenspeicher bis hinauf zu `/` root
oder dem Benutzer des Servers gehören und darf nicht für Gruppe oder andere beschreibbar sein. Der Speicher selbst muss
`0700` / `0600` sein, und auf dem Weg darf kein symbolischer Link liegen. Andernfalls ist **das Panel gesperrt**:
Anmeldung, der Standardschlüssel und jede Session werden abgelehnt, und das Startprotokoll nennt den Grund. Prüfen und
beheben:

```bash
qbixctl panel:check --root=/path/to/web          # prints each path, owner, mode, verdict and the fix
chmod g-w,o-w /srv/www/app                       # the usual cause after an upgrade: a 0775 directory
```

Oder legen Sie die Dateien woanders ab, indem Sie `Q.panel.aclDir` und `Q.panel.sessionsDir` setzen.

| Situation | Zugangsdaten | Sessions |
|---|---|---|
| Ausdrücklich gesetzt | `Q.panel.aclDir` | `Q.panel.sessionsDir` |
| Mit einem Konfigurationsbaum | `<tree>/acl` (`/etc/qbix/acl`) | `<state dir>/sessions` (`/var/lib/qbix/sessions`) |
| Ohne einen solchen | `local/` oberhalb des Document Root | `local/sessions/` |

Eine `local/panel.json` aus der Zeit vor 0.0.4.28 wird einmal verschoben (eine Kopie wird als
`panel.json.pre-migration-<time>` aufbewahrt).

## Zwei-Faktor-Authentifizierung (optional)

Standardmäßig aus. Registrieren Sie zuerst ein Gerät und schalten Sie dann die Durchsetzung ein, damit niemand
ausgesperrt wird:

```bash
qbixctl panel:2fa status  --root=/path/to/web
qbixctl panel:2fa enroll  --root=/path/to/web      # prints the secret and an otpauth:// URI
qbixctl panel:2fa confirm --root=/path/to/web --code=123456   # enable; prints ten recovery codes once
```

Setzen Sie dann `{"Q":{"panel":{"twofactor":true}}}` in der Site-Datei und starten Sie neu. Die Anmeldung besteht dann
aus dem Passwort, gefolgt von einem sechsstelligen Code (RFC 6238, 30-Sekunden-Schritt) oder einem
Wiederherstellungscode (jeder einmal verwendbar). Ein falscher oder wiederholt verwendeter Code zählt für dieselbe Sperre
wie ein falsches Passwort. Wenn Sie den Authenticator verlieren, ist `qbixctl panel:2fa disable --root=...` auf dem
Rechner der Weg zur Wiederherstellung. Sie können sich auch im Tab **Security** des Panels registrieren.

## Wer was öffnen darf

Für `/Q/panel` und `/Q/api/...`:

- von diesem Rechner aus immer;
- von anderswo nur, wenn ein Passwort gesetzt ist (oder der Standard noch gilt und die Seite ihr Anmeldeformular
  zeigt), wenn die Anfrage das Dashboard-Token oder eine Panel-Session trägt oder wenn `Q.panel.remote` oder
  `Q.dashboard.remote` den Wert `true` hat.

Andernfalls sagt die eigene 403-Seite des Servers, wie Sie ein Passwort setzen. Das Dashboard, `/Q/stats`, `/Q/metrics`
und `/Q/phpinfo` sind nach derselben Regel einem Administrator vorbehalten. `/Q/health` antwortet jedem mit
`{"status":"ok"}` und liefert Kennzahlen nur einem Administrator.

Dies sind die eigenen Regeln der Engine. Eine Exponential-Installation konfiguriert sie über `settings/velocity.ini` und
kann strenger sein (zum Beispiel das Panel nur von diesem Rechner aus); siehe „Views and who may open them" in
[Velocity-Engines](../../bc/6.0/velocity-engines.md).

Ein nicht angemeldeter Browser wird bei den Verwaltungsansichten zur Panel-Anmeldung umgeleitet und nach der Anmeldung
zu der Seite zurückgebracht, die er angefordert hatte (Release 0.0.4.28).

## Einstellungen

Alle unter `Q.panel`, sofern nicht anders angegeben.

| Konfigurationsschlüssel | Standard | Bedeutung |
|---|---|---|
| `bcryptCost` | `12` | Kostenfaktor von bcrypt, gehalten zwischen 10 und 15 |
| `passwordMinLength` | `16` | Darf erhöht, nie gesenkt werden |
| `passwordMinDistinct` | `10` | Darf erhöht, nie gesenkt werden |
| `passwordMinBits` | `80` | Darf erhöht, nie gesenkt werden |
| `defaultPassword` | der dokumentierte Standard | `null` oder `""` schaltet den Standard ab; das erste Passwort wird dann von diesem Rechner aus, mit dem Dashboard-Token oder auf der Kommandozeile gesetzt |
| `defaultLocalOnly` | `false` | Den Standard nur von diesem Rechner aus oder mit dem Dashboard-Token akzeptieren |
| `remote` (und `Q.dashboard.remote`) | `false` | Jeden, der die anderen Prüfungen besteht, das Panel auch von anderswo erreichen lassen |
| `aclDir`, `sessionsDir` | siehe die Vertrauensregel oben | Wo die Zugangsdaten und Sessions liegen |
| `twofactor` | `false` | Nach dem Passwort einen TOTP-Code verlangen |
| `twofactorWindow` | `1` | Akzeptierte Uhrabweichung in 30-Sekunden-Schritten auf jeder Seite |
| `Q.dashboard` | aktiviert | `false` deaktiviert `/Q/dashboard`, `/Q/health`, `/Q/ws` |
| `Q.dashboard.token` | keins | Verlangt `?token=VALUE` für das Dashboard |
| `Q.dashboard.hidePanelRequests` | `false` | `/Q/`-Anfragen aus den Zählungen und dem Protokoll des Dashboards herauslassen |

## Die Tabs

| Tab | Was Sie dort tun |
|---|---|
| **Apps**, **Frameworks** | Jede PHP-Anwendung sehen, die der Server erkennt (Name, Release, Wurzelverzeichnis), das Document Root umschalten, die eigenen Werkzeuge jedes Frameworks ausführen (Caches leeren, Migrationen, Routenlisten). Mit `...` markierte Befehle ändern die laufende Installation und fragen vorher nach. |
| **Domains** | Die verwendeten Domains sehen (Zertifikatsnamen, Einträge, gesehene Host-Header); einen Status pro Domain setzen (`active`; `suspended` antwortet mit `503` und `Retry-After`; `disabled` antwortet mit `404`); Aliase, Subdomains und ein Document Root pro Domain; Weiterleitungen (HTTP auf HTTPS, bevorzugter Host mit `www` oder ohne, Weiterleitung an andere Adressen); HSTS; eigene Fehlerseiten; das abdeckende Zertifikat mit Ausstellung oder Erneuerung als Hintergrund-Job; Datenverkehr pro Domain |
| **SSL** | Das ausgelieferte Zertifikat, alle Zertifikate nach Ablaufdatum, sichere Einstellungen, Erneuern und Neuladen, eine begrenzte Zertifikatshistorie ([HTTPS und Zertifikate](velocity-https-zertifikate.md)) |
| **Cache** | Den [Antwort-Cache](velocity-antwort-cache.md) aus dem Browser bedienen |
| **Logs** | Das Zugriffsprotokoll nach Text, Methode, Status und Host filtern; Tabs lassen sich als Lesezeichen speichern |
| **Scripts**, **Plugins**, **Playground**, **System** | Skripte ausführen, Plugins sehen, PHP in einer Sandbox ausprobieren (32 MB, 5 s, kein Netzwerk), PHP- und Systeminformationen sehen |

Die eigenen Pfade `/Q/` und `/.well-known/` des Servers werden nie durch einen Domain-Status gesperrt, sodass das Panel
und Zertifikatserneuerungen auf einem gesperrten Host weiter funktionieren.

## Das Dashboard

Karten: Anfragen insgesamt, Anfragen pro Sekunde (5-Sekunden-Fenster), durchschnittliche und langsamste Antwort, Speicher
und Spitzenwert, Worker (Anzahl, frei, beschäftigt, bediente PHP- und statische Anfragen), WebSocket-Verbindungen,
übertragene Daten, Statuscodes, meistaufgerufene Pfade mit Trefferzahl und Durchschnittszeit, eine Sparkline des
Durchsatzes mit 60 Punkten und ein Live-Anfrageprotokoll mit Spaltenüberschriften und Statusfilter. Zwei Karten sollten
Sie kennen:

- **Worker Memory** zeigt die PSS (proportional set size), summiert über den Elternprozess und eine begrenzte Stichprobe
  von Workern, nicht die summierte RSS (die jede gemeinsam genutzte Copy-on-Write-Seite pro Worker einmal zählt und einen
  Pool um ein Vielfaches überschätzt).
- **System RAM** zeigt den belegten Speicher (gesamt minus verfügbar) und, wenn Swap vorhanden ist, belegten / gesamten
  Swap, eingefärbt danach, wie schnell Seiten aus dem Swap zurückkommen: gedämpft im Leerlauf, gelb ab 1 MB/s
  Rücklesen oder 90 Prozent Füllstand, rot ab 10 MB/s.

| Endpunkt | Format |
|---|---|
| `/Q/dashboard` | HTML |
| `/Q/health` | JSON, für Monitoring-Systeme |
| `/Q/stats` | JSON, die vollständigen Daten für Grafana oder Ähnliches |
| `/Q/metrics` | Prometheus-Text für Scraper; ein Browser sieht eine Seite |
| `/Q/phpinfo` | Der eigene Bericht von PHP als HTML-Seite mit Filter |

## Die Seiten des Servers neu gestalten (Designs)

Dashboard, Panel, Dokumentation, Verzeichnisliste und Fehlerseiten lesen ihr Markup aus einem Design auf der Festplatte
(`designs/` im Konfigurationsbaum, `/etc/vc/designs` für Velocity), sodass Sie sie neu gestalten können, ohne die Engine
zu bearbeiten. Fehlerseiten sind gestaltete Seiten in klaren Worten für jeden Fehler, den der Server selbst beantwortet,
über HTTP/1.1 und HTTP/2. Die Meldung einer nicht abgefangenen Exception wird nie an den Client gesendet, es sei denn,
`Q.webserver.debug` ist eingeschaltet.

## Grenzen

- Das Dashboard aktualisiert sich über einen WebSocket. Hinter einem Proxy braucht die Shell ihren öffentlichen Origin
  in `Q.shell.allowedOrigins`.
- Ein Panel-Speicher, der die Vertrauensregel nicht erfüllt, sperrt das Panel absichtlich; es gibt keinen lockereren
  Ausweichweg.
- Im Tab Cache gespeicherte Einstellungen haben Vorrang vor der Site-Datei.

## Verwandte Seiten

- [Q-Shell](velocity-q-shell.md), [HTTPS und Zertifikate](velocity-https-zertifikate.md), [Antwort-Cache](velocity-antwort-cache.md), [Velocity-Webserver](velocity-webserver.md)
- Spezifikationen: [HTTP/2 und Sicherheit](../../spezifikationen/6.0/velocity-http2-und-sicherheit.md) (die Verwaltungsoberfläche), [Engine-Einstellungen](../../spezifikationen/6.0/velocity-engine-einstellungen.md) (`Q.dashboard`, Branding, Protokollierung)
- Aktualisierung: [Hinweise zur Aktualisierung der Velocity-Engine](../../bc/6.0/velocity-engine-upgrade-hinweise.md) (Zugangsdatenspeicher, Vertrauensregel), [Velocity-Engines](../../bc/6.0/velocity-engines.md) (Ansichten und wer sie aus Exponential öffnen darf)
- [Changelog: Engine Exponential Velocity](../../../changelogs/extensions/exponential-velocity.md)
- Geschichte: [Juli 2026](../../geschichte/velocity/2026-07.md) (erstes Panel), [24. September](../../geschichte/velocity/2026-09d.md) (Passwörter, Domains), [25. bis 30. September](../../geschichte/velocity/2026-09e.md) (SSL, Cache, Zwei-Faktor)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
