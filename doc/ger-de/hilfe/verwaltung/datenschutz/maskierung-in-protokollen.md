---
title: Maskierung in Protokollen und im Protokollbetrachter
audience: government, admin
category: public-sector
order: 70
summary: Für Administratoren öffentlicher Websites - wie Exponential E-Mail-Adressen, IP-Adressen und Geheimnisse in Anfrageprotokollen und im Protokollbetrachter maskiert, wer unmaskierte Werte sehen darf, und wie Sie Screenshots, Downloads und Supportberichte frei von personenbezogenen Daten halten.
keywords: Maskierung in Protokollen, Schwärzung, MaskIP, MaskEmails, MaskIPs, MaskSecrets, RedactionSettings, setup/unmasklogs, Unmaskiert zeigen, Protokollbetrachter, Log-Viewer, Anfrageprotokolle, Supportberichte, personenbezogene Daten, öffentlicher Sektor, log masking, redaction, log viewer, request logs
type: help
---
# Maskierung in Protokollen und im Protokollbetrachter

Diese Seite richtet sich an Administratoren. Protokolle sind voller personenbezogener Daten: Adressen, E-Mail-Adressen
in URLs, User-Agents. Exponential maskiert sie an zwei Stellen, beim Schreiben der Anfrageprotokolle und beim Anzeigen
jedes Protokolls ([Protokollbetrachter: Datenschutz](../../../../eng-us/features/6.0/log-viewer.md#privacy-what-is-masked),
[Anfrageprotokolle](../../../../eng-us/features/6.0/request-logs.md)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Beim Schreiben](#beim-schreiben)
2. [Beim Anzeigen](#beim-anzeigen)
3. [Unmaskierter Zugriff](#unmaskierter-zugriff)
4. [Gute Praxis](#gute-praxis)
5. [Verwandte Seiten](#verwandte-seiten)

## Beim Schreiben

`site.ini [RequestLogSettings]` für `404.log` und `security.log`:

- `MaskIP=enabled` (Voreinstellung): die Client-Adresse mit verborgenem Hostanteil (`203.0.x.x`, `2001:db8:x:x`); die
  unveränderte Adresse wird nie in eine Datei geschrieben. `disabled` schreibt sie vollständig, „nur für eine Website,
  die dafür einen Grund und eine Rechtsgrundlage hat“.
- In `404.log` wird jeder Abfragewert durch `***` ersetzt; in `security.log` bleiben Abfragewerte als Nachweis
  erhalten, wobei Geheimnisse, E-Mail- und IP-Adressen darin maskiert sind.
- User-Agents: ein kurzer Hash und die ersten 60 Zeichen.

Andere Kernel-Protokolle (`error.log`, `debug.log` ...) werden so geschrieben, wie der Code sie schreibt; sie werden
beim Anzeigen maskiert.

## Beim Anzeigen

`settings/logviewer.ini [RedactionSettings]`:

| Einstellung | Maskiert |
|---|---|
| `MaskEmails` | aus `jane.doe@example.com` wird `j***@***.com` |
| `MaskIPs` | aus `203.0.113.7` wird `203.0.x.x`; IPv6 behält seine ersten zwei Gruppen |
| `MaskSecrets` | Passwörter in Adressen, Werte von `password=`, `token=`, `api_key=`, Bearer- und Basic-Zugangsdaten, JSON Web Tokens, Sitzungskennungen und Cookies, lange Schlüssel und Hashes |
| `SecretSettings[]`, `SecretGroups[]` | immer maskiert, unabhängig von den Schaltern |

Downloads aus dem Betrachter werden auf dieselbe Weise maskiert.

## Unmaskierter Zugriff

Ein Benutzer mit der Richtlinie `setup/unmasklogs` kann **Unmaskiert zeigen** für sich selbst einschalten; das gilt
für die Seite, ihre Aktualisierungen und ihre Downloads, und das Audit zeichnet auf, dass die Datei unmaskiert gelesen
wurde. Geben Sie die Richtlinie nur sehr wenigen Personen, für Untersuchungen.

## Gute Praxis

- Erstellen Sie Screenshots und Supportberichte mit eingeschalteter Maskierung, wie es die Dokumentation des
  Protokollbetrachters empfiehlt.
- Wenn Sie einem Lieferanten ein Protokoll senden, senden Sie einen maskierten Download, nicht die Rohdatei.
- Die Zugriffsprotokolle des Webservers liegen außerhalb von Exponential und enthalten in der Regel vollständige
  Adressen: Legen Sie ihre Aufbewahrung und Maskierung im Webserver oder mit Ihrem Hoster fest.

## Verwandte Seiten

- [Der Protokollbetrachter](../../../../eng-us/features/6.0/log-viewer.md)
- [Anfrageprotokolle](../../../../eng-us/features/6.0/request-logs.md)
- [Aufbewahrung von Protokollen](aufbewahrung-von-protokollen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
