---
title: Geheimnisse vom Bildschirm fernhalten
audience: press
category: press-media
order: 163
summary: Die vollständige Liste dessen, was nie in einem Video, Stream, Bildschirmfoto oder Podcast über Exponential erscheinen darf - Passwörter, das erzeugte Administratorpasswort, Anbieterschlüssel, Einstellungsdateien, Tokens, Serveradressen - wo jedes davon im Produkt auftaucht, wie Sie den Rechner vorbereiten und was Sie tun, wenn etwas durchsickert.
keywords: Geheimnisse, Passwörter, API-Schlüssel, Anbieterschlüssel, Tokens, Einstellungs-Override, erstes Administratorpasswort, Bildschirmaufnahme, Leck, Austausch, widerrufen, unkenntlich machen, Creator, secrets, passwords, API keys, provider keys, tokens, settings override, initial admin password, screen recording, leak, rotation, revoke, blur
type: help
---
# Geheimnisse vom Bildschirm fernhalten

Diese Seite ist für jeden Creator, der einen Bildschirm aufnimmt oder teilt. Ein einziges Bild mit einem Passwort oder
einem Schlüssel genügt, damit jemand es benutzt, und Aufnahmen, Clips und Bildschirmfotos bleiben jahrelang erhalten.
Das sicherste Geheimnis ist eines, das gar nicht auf dem Rechner liegt, den Sie zeigen. Diese Seite listet auf, wo die
Geheimnisse von Exponential auftauchen, damit Sie sie aus dem Bild heraushalten können.

## Inhalt

1. [Nie auf dem Bildschirm](#nie-auf-dem-bildschirm)
2. [Wo Geheimnisse in Exponential auftauchen](#wo-geheimnisse-in-exponential-auftauchen)
3. [Den Rechner vorbereiten](#den-rechner-vorbereiten)
4. [Während der Aufnahme](#während-der-aufnahme)
5. [Wenn etwas durchsickert](#wenn-etwas-durchsickert)

## Nie auf dem Bildschirm

- Passwörter jedes Kontos, auch das, das Sie in das Anmeldeformular tippen.
- Das erzeugte Administratorpasswort und die Datei `var/log/initial-admin-password`.
- Anbieterschlüssel (zum Beispiel für Exponential Intelligence), API-Schlüssel, OAuth-Client-Secrets, Tokens,
  Sitzungs-Cookies.
- Der Inhalt von `settings/override/` und jede Datei mit Zugangsdaten zur Datenbank.
- Passwörter des Control Panels und Dashboard-Tokens von Velocity.
- Private Schlüssel von Zertifikaten.
- Serveradressen, Hostnamen von Datenbanken und interne Pfade eines echten Servers.
- QR-Codes, Geheimnisse und Wiederherstellungscodes der zweistufigen Anmeldung.
- Daten anderer Menschen: siehe [Privatsphäre vor der Kamera](../../../eng-us/help/press/privacy-on-camera.md).

## Wo Geheimnisse in Exponential auftauchen

| Ort | Was er zeigt | Was Sie tun | Quelle |
|---|---|---|---|
| Die Zusammenfassung von `exp:install` | Das Administratorpasswort, zweimal | Herausschneiden oder unkenntlich machen; das Passwort nach der Aufnahme ändern | [Die Installation auf der Konsole](../../installation/07-installation-ueber-die-konsole.md#710-was-sie-am-ende-sehen) |
| `var/log/initial-admin-password` | Das erzeugte Passwort | Nie vor der Kamera öffnen; nach der ersten Anmeldung löschen | [Das voreingestellte Administratorpasswort](../standard-administratorpasswort.md) |
| `--password=`, `--db-password=` auf der Kommandozeile | Passwörter im Terminal und in seinem Verlauf | Für die Datenbank `EXP_INSTALL_DB_PASSWORD` verwenden; den Verlauf löschen | [Mit einem Befehl installieren](../../../eng-us/features/6.0/install-in-one-command.md#choose-a-database) |
| `settings/override/site.ini.append.php` | Datenbankeinstellungen | Nie vor der Kamera öffnen | [Sicherheit bei Live-Demos](../fuersprecher/sicherheit-bei-live-demos.md#was-nie-auf-dem-bildschirm-sein-darf) |
| `settings/override/expai.ini.append.php` | Der Anbieterschlüssel von Exponential Intelligence | Nie öffnen; den Schlüssel nach der Aufnahme widerrufen | [Exponential Intelligence](../../../eng-us/features/6.0/extensions/expai.md#switch-it-on) |
| Anmeldung am Control Panel von Velocity, `qbixctl panel:password --generate` | Das Passwort des Panels | Außerhalb der Kamera setzen | [Control Panel von Velocity](../../../eng-us/features/6.0/velocity-control-panel.md#first-sign-in) |
| Eine Dashboard-Adresse mit `?token=` | Das Dashboard-Token | Die Adressleiste nicht zeigen | [Control Panel von Velocity](../../../eng-us/features/6.0/velocity-control-panel.md) |
| Einrichtung der zweistufigen Anmeldung (`/user2fa/setup`) | Der QR-Code und das Geheimnis; Wiederherstellungscodes | Die Einrichtung nie vor der Kamera abschließen | [Zweistufige Anmeldung](../../anleitungen/zwei-faktor-anmeldung.md) |
| **Setup > Systeminformationen** | Pfade, Hostnamen, Angaben zur Datenbank | Nur den oberen Teil zeigen oder unkenntlich machen | [Systeminformationen](../../anleitungen/systeminformationen.md) |
| **Setup > Logdateien** mit **Unmaskiert zeigen** | E-Mail- und IP-Adressen, Geheimnisse so, wie sie geschrieben wurden | Während der Aufnahme nie einschalten | [Log-Viewer](../../../eng-us/features/6.0/log-viewer.md) |
| Fehlerseiten und Fehler im Terminal | Pfade, manchmal Einstellungen | Erst lesen, dann zeigen | [Sicherheit bei Live-Demos](../fuersprecher/sicherheit-bei-live-demos.md#während-der-demo) |

## Den Rechner vorbereiten

```text
[ ] A demo installation that holds no real secret: its own passwords, a key made only for the recording
[ ] A separate browser profile: no saved passwords, no autocomplete, bookmarks bar hidden
[ ] One window captured, never the whole screen
[ ] Notifications off: system, chat, e-mail, phone
[ ] Terminal: large font, a prompt without real host or user names, history cleared or off for the session
[ ] Password manager locked; sign in before recording, not during
[ ] Desktop and file manager: no file names with customers or projects
```

## Während der Aufnahme

- Wenn ein Anmeldeformular erscheint, pausieren Sie die Aufnahme oder die Bildschirmfreigabe, tippen Sie und machen Sie
  dann weiter.
- Zeigen Sie Seiten der Dokumentation statt Einstellungsdateien: Sie erklären dasselbe und enthalten keine Geheimnisse.
- Bevor Sie eine Fehlerseite oder eine lange Ausgabe im Terminal zeigen, lesen Sie sie.
- Halten Sie die Adressleiste aus dem Bild, wenn eine Adresse ein Token enthält.

## Wenn etwas durchsickert

| Was durchgesickert ist | Sofort tun | Danach |
|---|---|---|
| Ein Passwort | Ändern | Andere Sitzungen abmelden |
| Ein Anbieter- oder API-Schlüssel | Beim Anbieter widerrufen | Einen neuen Schlüssel erstellen; die Nutzung beim Anbieter auf Missbrauch prüfen |
| Das Passwort des Control Panels oder das Dashboard-Token | Ein neues Passwort setzen; die Änderung beendet jede Sitzung | Die Anmeldungen am Panel prüfen |
| Ein Geheimnis oder Wiederherstellungscode der zweistufigen Anmeldung | Die zweistufige Anmeldung für dieses Konto neu einrichten | |
| Die Adresse oder ein Pfad eines echten Servers | Den Eigentümer des Servers informieren | Prüfen, was offenliegt |
| Eine Aufnahme mit einem der oben genannten Punkte | Nicht veröffentlichen oder herunternehmen | Die Stelle herausschneiden oder neu aufnehmen |

Nachträgliches Unkenntlichmachen hilft den Zuschauern, die später kommen; es hilft nicht gegen Menschen, die das
Original schon gesehen oder heruntergeladen haben. Betrachten Sie ein Geheimnis, das auf dem Bildschirm war, als
verbrannt. Wenn Sie feststellen, dass ein Geheimnis durch einen Fehler in der Software durchsickert, melden Sie es
vertraulich an security@se7enx.com, wie `SECURITY.md` es verlangt, und veröffentlichen Sie keine Einzelheiten, bevor
eine Korrektur erschienen ist.

## Verwandte Seiten

- [Sicherheit bei Live-Demos](../fuersprecher/sicherheit-bei-live-demos.md)
- [Privatsphäre vor der Kamera](../../../eng-us/help/press/privacy-on-camera.md)
- [Leitfaden für Livestreams](leitfaden-fuer-livestreams.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
