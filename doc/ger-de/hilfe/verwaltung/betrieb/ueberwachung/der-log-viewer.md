---
title: Der Log-Viewer
audience: government, admin, developer
category: public-sector
order: 30
summary: Die Logs von Exponential in der Administration (Setup > Logdateien) und auf der Shell (exp:logs) lesen - welche Logs es gibt, Permalinks, eine Datei live verfolgen, suchen, einen Fehler über seine Referenz finden, Maskierung personenbezogener Daten und wer den Log-Viewer verwenden darf.
keywords: Log-Viewer, Setup Logdateien, Logs, error.log, exp:logs, Permalink, verfolgen, Suche, Fehlerreferenz, Maskierung, personenbezogene Daten, setup/logs, setup/unmasklogs, log viewer, Setup Log files, follow, search, error reference, masking, personal data
type: help
---
# Der Log-Viewer

Diese Seite richtet sich an die Administration und die Entwicklung, die die Logs einer öffentlichen Website lesen, und an
Datenschutzzuständige, die wissen möchten, was der Log-Viewer anzeigt. Die vollständige Referenz ist
[Der Log-Viewer](../../../../funktionen/6.0/log-betrachter.md); die Sicht des Hostings ist
[Log-Viewer und Rotation (Hosting)](../../../hosting/betrieb/log-viewer-und-log-rotation.md).

## Inhalt
1. [Was er anzeigt](#was-er-anzeigt)
2. [Lesen und suchen](#lesen-und-suchen)
3. [Eine Fehlerreferenz von einer Bürgerin oder einem Bürger](#eine-fehlerreferenz-von-einer-bürgerin-oder-einem-bürger)
4. [Personenbezogene Daten](#personenbezogene-daten)
5. [Auf der Shell](#auf-der-shell)
6. [Wer ihn verwenden darf](#wer-ihn-verwenden-darf)
7. [Verwandte Seiten](#verwandte-seiten)

## Was er anzeigt

**Setup > Logdateien** (`/setup/logs`) listet `error.log`, `warning.log`, `notice.log`, `debug.log`, `deprecated.log`,
`auth.log`, die Anfrage-Logs `404.log` und `security.log`, `csp.log`, die Cronjob-Logs, die Logs jeder Site
(`var/<site>/log/*.log`), die Logs der Erweiterungen und die rotierten Archive auf. Eine Navigation gruppiert sie als
Kernel, Site, Extensions, Webserver und Rotierte Archive, mit Größe, letztem Schreibzeitpunkt und Zeilen pro Minute.
Webserver-Logs erscheinen nur, wenn `logviewer.ini` sie benennt; sie werden nie erraten.

## Lesen und suchen

- Jede Ansicht hat einen **Permalink** wie `/setup/logs/view/error/(level)/error/(search)/timeout`;
  **Permalink kopieren** legt ihn für ein Ticket in die Zwischenablage.
- **Verfolgen** Sie eine Datei live, mit **Anhalten** und **Fortsetzen**; dabei bleibt kein Serverprozess offen.
- **Suchen** Sie in einer Datei, in allen Kernel-Logs oder in allen Dateien, einschließlich der Rotationen: Text oder
  ein begrenzter regulärer Ausdruck, Stufen, ein Zeitraum, Siteaccess, Anfrage, Client-Adresse, Fehlerreferenz.
- Zeilen werden zerlegt, nach Stufe eingefärbt (nie allein durch Farbe), nach Anfrage gruppiert und mit den
  Audit-Einträgen derselben Minute sowie mit Knoten, Objekten und Benutzern in der Administration verknüpft.
- **Meldungen kopieren** kopiert die angezeigten Zeilen als Text.

## Eine Fehlerreferenz von einer Bürgerin oder einem Bürger

Wenn ein Besucher eine Fehlerseite mit einer Referenz wie `ERR-0123456789` meldet, suchen Sie danach: Der Log-Viewer
findet die Zeile und die anderen Zeilen derselben Anfrage. Leitfaden:
[Einen Fehler über seine Referenz finden](../../../../anleitungen/fehler-anhand-der-referenz-finden.md) und
[Einen Fehler über seine Referenz finden (Aufgabe)](../../aufgaben/betrieb/einen-fehler-anhand-seiner-referenz-finden.md).

## Personenbezogene Daten

Standardmäßig maskiert der Log-Viewer E-Mail-Adressen, IP-Adressen, Passwörter, Tokens, Schlüssel, Sitzungs-IDs und
Cookies; die Werte geheimer Einstellungen werden nie angezeigt. **Unmaskiert zeigen** (nur mit `setup/unmasklogs`)
zeigt sie dieser Person an, solange der Schalter eingeschaltet ist. Jede Ansicht und jede Aktion wird auditiert.
Downloads sind maskiert und komprimiert. Damit eignet sich der Log-Viewer für Beschäftigte, die Fehler untersuchen
müssen, ohne die Daten von Bürgerinnen und Bürgern zu sehen; heben Sie die Maskierung nur für Untersuchungen auf. Die
Aufbewahrung von Logs behandelt [Aufbewahrung und Rotation von Logs](aufbewahrung-und-rotation-von-protokollen.md).

## Auf der Shell

```bash
./console exp:logs --list
./console exp:logs --tail=var/log/error.log --lines=50
./console exp:logs --search=timeout --file=var/log/error.log --rotations --level=error,warning
./console exp:logs --reference=ERR-0123456789
```

Die Kommandozeile wendet dieselbe Maskierung und dieselben Grenzen an, prüft aber keine Richtlinie (der Shell-Zugang
ist die Kontrolle); jeder Lauf, der eine Datei liest, wird als `system.log.read` aufgezeichnet.

## Wer ihn verwenden darf

| Funktion | Erlaubt |
|---|---|
| `setup/logs` | die Seite, Verfolgen, Suche, Download eines Bereichs (wenn Downloads eingeschaltet sind) |
| `setup/managelogs` | Jetzt rotieren, leeren, ein Archiv entfernen, jeweils nur, wenn in `logviewer.ini [ActionSettings]` eingeschaltet |
| `setup/unmasklogs` | den Schalter **Unmaskiert zeigen** |

In einer neuen Installation hat nur Administrator diese Funktionen; siehe
[Zugriff auf Logs ohne Administratorrechte geben](../../aufgaben/personen-und-berechtigungen/zugriff-auf-logs-ohne-administratorrechte-geben.md).

## Verwandte Seiten
- [Fehlerübersicht und Zustand der Logs](fehleruebersicht-und-zustand-der-logs.md)
- [Anfrage-Logs: 404 und Sicherheit](anfrageprotokolle-404-und-sicherheit.md)
- [Aufbewahrung und Rotation von Logs](aufbewahrung-und-rotation-von-protokollen.md)
- [Funktion Log-Viewer](../../../../funktionen/6.0/log-betrachter.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
