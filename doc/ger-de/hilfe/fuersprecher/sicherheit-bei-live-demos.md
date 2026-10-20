---
title: Sicherheit bei Live-Demos
audience: advocate
category: advocacy
order: 45
summary: Wie Sie Exponential live zeigen, ohne Geheimnisse, personenbezogene Daten oder die Website eines anderen preiszugeben - was nie auf dem Bildschirm erscheint, wie Sie eine saubere Demo-Installation vorbereiten, was zu tun ist, wenn etwas nach außen gelangt, und eine Tabelle für Demos, die schiefgehen.
keywords: Live-Demo, Sicherheit, Geheimnisse, Passwörter, Schlüssel, personenbezogene Daten, Bildschirmfreigabe, Aufzeichnung, Streaming, Demo-Installation, Wiederherstellung, Leck, Austausch, live demo, safety, secrets, passwords, keys, personal data, screen sharing, recording, streaming, demo installation, recovery, leak, rotation
type: help
---
# Sicherheit bei Live-Demos

Diese Seite ist für jeden Fürsprecher, der einen Bildschirm teilt: auf der Bühne, in einer Videokonferenz, in einer
Aufzeichnung oder einem Stream. Eine Demo kann in einer Sekunde ein Passwort, einen API-Schlüssel, eine Serveradresse
oder personenbezogene Daten preisgeben, und Aufzeichnungen bewahren das für immer. Bereiten Sie sich so vor, dass auf
dem Rechner, den Sie zeigen, nichts Geheimes liegt.

## Inhalt

1. [Was nie auf dem Bildschirm sein darf](#was-nie-auf-dem-bildschirm-sein-darf)
2. [Eine saubere Demo-Installation vorbereiten](#eine-saubere-demo-installation-vorbereiten)
3. [Den Rechner vorbereiten](#den-rechner-vorbereiten)
4. [Während der Demo](#während-der-demo)
5. [Wenn etwas nach außen gelangt](#wenn-etwas-nach-außen-gelangt)
6. [Wenn die Demo schiefgeht](#wenn-die-demo-schiefgeht)

## Was nie auf dem Bildschirm sein darf

- Passwörter, einschließlich des erzeugten Administratorpassworts und der Datei `var/log/initial-admin-password`.
- Schlüssel von Anbietern (zum Beispiel für Exponential Intelligence), API-Schlüssel, Tokens, Session-Cookies.
- Der Inhalt von `settings/override/` und jede Datei mit Zugangsdaten zur Datenbank.
- Serveradressen, Hostnamen von Datenbanken, interne Pfade außerhalb der Installation.
- Namen, E-Mail-Adressen, Bestellungen oder Nachrichten echter Menschen.
- Die Website eines Kunden oder Arbeitgebers, es sei denn, er hat schriftlich zugestimmt.
- Andere Browser-Tabs, Chat-Benachrichtigungen, E-Mail-Vorschauen, die Lesezeichenleiste.

Die Demo-Skripte enthalten dieselbe Warnung ([Demo-Skripte](../geschaeft/demo-drehbuecher.md#die-checkliste-einen-tag-vorher)).

## Eine saubere Demo-Installation vorbereiten

1. Installieren Sie eine frische Kopie mit erfundenen Inhalten ([Installation mit einem Befehl](../../../eng-us/features/6.0/install-in-one-command.md)).
2. Ändern Sie das erzeugte Passwort; löschen Sie `var/log/initial-admin-password`
   ([Das Standardpasswort des Administrators](../standard-administratorpasswort.md)).
3. Legen Sie Demo-Konten mit erfundenen Namen an (zum Beispiel „Demo Redakteur“), nie für echte Menschen.
4. Verwenden Sie erfundene Beispielinhalte. Kopieren Sie keine echten Artikel, Kundendaten oder privaten Texte.
5. Wenn Sie Exponential Intelligence zeigen, verwenden Sie einen Schlüssel mit niedrigem Ausgabenlimit, den Sie danach
   widerrufen können, und öffnen Sie nie den Bildschirm, der ihn zeigt.
6. Betreiben Sie sie auf Ihrem eigenen Rechner oder einem Testserver, den Sie kontrollieren.
7. Wenn sich das Publikum selbst mit einer Anmeldung, die Sie auf dem Bildschirm zeigen, an der Demo anmeldet, betreiben
   Sie sie als öffentliche Demo mit der Umgebungsvariablen `EXP_DEMO_MODE=1`: Dann kann während der Sitzung niemand das
   Administratorpasswort ändern oder zurücksetzen, und ein Banner **Demo-Modus** sagt das auf jeder Seite. Er schützt nur
   das Administratorpasswort; installieren Sie die Demo danach also neu ([Demomodus](../demomodus.md)).

## Den Rechner vorbereiten

```text
[ ] A separate browser profile or private window for the demo
[ ] Bookmarks bar hidden; other tabs closed; autocomplete cleared or off
[ ] Notifications off (system, chat, e-mail)
[ ] Desktop clean; no files with customer names visible
[ ] Terminal: history does not show secrets; prompt shows no host or user names you mind
[ ] Password manager locked; do not type passwords while sharing
[ ] Screen sharing: share one window, not the whole screen
```

## Während der Demo

- Wenn ein Anmeldebildschirm erscheint, **pausieren Sie die Bildschirmfreigabe**, tippen Sie das Passwort, fahren Sie fort.
- Öffnen Sie keine Einstellungsdateien auf dem Bildschirm. Zeigen Sie stattdessen die Dokumentationsseite.
- Wenn eine Fehlerseite erscheint, lesen Sie sie, bevor Sie sie zeigen: Fehlerdetails können Pfade enthalten.
- Wenn jemand bittet, „nur kurz auf meine Website zu schauen“, lehnen Sie während der Sitzung höflich ab; bieten Sie an,
  danach vertraulich zu helfen, ohne Aufzeichnung.

## Wenn etwas nach außen gelangt

Handeln Sie sofort, auch während des Vortrags:

| Was nach außen gelangt ist | Tun Sie jetzt |
|---|---|
| Ein Passwort | Ändern Sie es sofort danach (oder in einer Pause); melden Sie andere Sitzungen ab |
| Ein Anbieter- oder API-Schlüssel | Widerrufen Sie ihn beim Anbieter und erstellen Sie einen neuen |
| Eine Serveradresse oder ein Pfad | Informieren Sie den Eigentümer des Servers; prüfen Sie, was offenliegt |
| Personenbezogene Daten | Stoppen Sie wenn möglich die Aufzeichnung; informieren Sie die Veranstalter; folgen Sie dem Ablauf Ihrer Organisation für Datenvorfälle |
| Eine Aufzeichnung mit einem der obigen Punkte | Veröffentlichen Sie sie nicht; schneiden Sie die Stelle heraus oder nehmen Sie neu auf |

Nehmen Sie nie an, „das hat niemand bemerkt“. Aufzeichnungen und Screenshots verbreiten sich.

## Wenn die Demo schiefgeht

| Problem | Wiederherstellung |
|---|---|
| Das Netzwerk fällt aus | Ihre Demo läuft lokal auf SQLite; wenn nicht, wechseln Sie zu den Screenshots als Rückfallebene |
| Der Laptop fällt aus | Folien als PDF auf einem USB-Stick oder in Ihrer E-Mail |
| Eine Seite zeigt einen Fehler | „Live-Demos - ich zeige Ihnen denselben Schritt aus meinen Notizen.“ Machen Sie weiter; untersuchen Sie es später und melden Sie es, wenn es ein Fehler ist ([Fehler gut melden](fehler-gut-melden.md)) |
| Ein Entwurf aus einem früheren Durchlauf ist im Weg | **Dashboard > Meine Entwürfe**, verwerfen Sie ihn |
| Sie haben vergessen, was als Nächstes kommt | Halten Sie das Skript auf einer gedruckten Karte bereit |

Weitere Wege der Wiederherstellung: [Demo-Skripte, Was schiefgehen kann](../geschaeft/demo-drehbuecher.md#was-schiefgehen-kann-und-wie-sie-die-lage-retten).

## Verwandte Seiten

- [Demos als Fürsprecher zeigen](demos-zeigen.md)
- [Einen Screencast aufnehmen](einen-screencast-aufnehmen.md)
- [Die Privatsphäre von Menschen schützen](privatsphaere-schuetzen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
