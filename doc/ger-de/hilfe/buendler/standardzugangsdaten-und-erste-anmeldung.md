---
title: Standardzugangsdaten und erste Anmeldung
audience: bundler, hosting, admin
category: bundling
order: 370
summary: Welches Administratorpasswort eine gebündelte Installation je nach Installer erhält, warum kein Bundle ein festes oder gemeinsames Passwort ausliefern darf, wie Sie ein erzeugtes Passwort einmal an den Empfänger übergeben, wie die erste Anmeldung zu einer Passwortänderung führt und das Passwort der Datenbank-Grunddaten, das jeder Bündler kennen muss.
keywords: Standardpasswort, Administrator, erzeugtes Passwort, bekannte Passwörter, erste Anmeldung, Übergabe, default password, administrator, admin, initial-admin-password, publish, generated password, MinPasswordLength, well-known passwords, WeakPasswordCheck, first sign-in, hand-over, exp:resetuserpassword, bundle
type: help
---
# Standardzugangsdaten und erste Anmeldung

Diese Seite richtet sich an Bündler, die dafür verantwortlich sind, wie sich der Empfänger eines Bundles zum ersten
Mal anmeldet. Jede Installation beginnt mit einem Administrator, Login **`admin`**. Wie sein Passwort lautet, hängt
vom Installer ab; die vollständige Referenz ist
[Das Standard-Administratorpasswort](../standard-administratorpasswort.md).

## Inhalt

1. [Die Regel](#die-regel)
2. [Was jeder Installer tut](#was-jeder-installer-tut)
3. [Das Passwort der Grunddaten](#das-passwort-der-grunddaten)
4. [Das Passwort übergeben](#das-passwort-übergeben)
5. [Die erste Anmeldung zur Änderung nutzen](#die-erste-anmeldung-zur-änderung-nutzen)
6. [Checkliste](#checkliste)

## Die Regel

**Kein Bundle liefert ein festes, gemeinsames oder erratbares Administratorpasswort aus**, außer einer öffentlichen
Demo, deren Passwort bewusst veröffentlicht ist und die im Demo-Modus läuft
([Demo-Modus in Bundles](demomodus-in-bundles.md)).

## Was jeder Installer tut

| Installer | Passwort nach der Installation |
|---|---|
| Einrichtungsassistent | das im Assistenten eingegebene; "es gibt keinen Standard". Er lehnt Passwörter ab, die kürzer als `MinPasswordLength` (10) oder allgemein bekannt sind |
| Kickstarter | `[site_admin] Password` aus `kickstart.ini`; "ist es leer, kürzer als `MinPasswordLength` oder ein allgemein bekanntes Wort, ein zufälliges Passwort mit 20 Zeichen, einmal ausgegeben und nach `var/log/initial-admin-password` geschrieben" |
| `exp:install` | `--password`, wenn lang genug und nicht allgemein bekannt; "sonst ein zufälliges Passwort mit 24 Zeichen, am Ende ausgegeben und nach `var/log/initial-admin-password` geschrieben" |
| Seitenpakete | "keine Änderung: Pakete bringen keine Konten mit" |

Quelle: [In Kürze](../standard-administratorpasswort.md#kurz-gesagt). `var/log/initial-admin-password` hat den Modus
`0600`.

Die Liste der allgemein bekannten Passwörter steuert `site.ini [UserSettings] WeakPasswordCheck=enabled`
(ausgeliefert), mit `WeakPasswordList[]` und `WeakPasswordFile` für Ergänzungen wie Ihren Firmen- oder Produktnamen.
Fügen Sie den Namen Ihres Bundles in den Einstellungen, die Ihr Bundle ausliefert, zu dieser Liste hinzu, damit
niemand ihn als Passwort verwendet.

## Das Passwort der Grunddaten

"Die saubere Datenbank des Kernels ... hat das Administratorkonto mit dem Passwort **`publish`**"
(`share/db_data.dba`, `kernel/sql/common/cleandata.sql`, `kernel/sql/sqlite/cleandata.sql`). Die Installer
überschreiben es immer. Es bleibt aber das Passwort, wenn eine Datenbank von Hand ohne Installer aus diesen Dateien
geladen wurde oder wenn der Einrichtungsassistent angewiesen wurde, eine solche Datenbank zu behalten. Ein Bundle, das
seine Datenbank auf anderem Weg als über einen Installer aufbaut, muss es zurücksetzen:

```bash
php bin/php/console exp:resetuserpassword --allow-root-user -u admin -g -l 24
```

(der Befehl, der unter
[Die ausgelieferten Datenbank-Grunddaten](../standard-administratorpasswort.md#die-mitgelieferte-datenbank-grundausstattung-publish)
gezeigt wird).

## Das Passwort übergeben

| Bundle | Übergabe |
|---|---|
| Panel-Installer | einmal auf der Ergebnisseite des Panels zeigen oder über den sicheren Kanal des Panels senden; die Datei löschen |
| Server-Image | einmal auf der Konsole oder im Instanzprotokoll der Plattform ausgeben, oder den Besitzer beim Start nach einem Passwort fragen |
| Container | der Besitzer liest `var/log/initial-admin-password` einmal aus dem Volume, oder übergibt `--password` aus einem Geheimnisspeicher |
| Appliance | der Bildschirm beim ersten Start zeigt es an oder fragt danach |

Löschen Sie danach `var/log/initial-admin-password`, wie es die Zusammenfassung des Installers sagt.

## Die erste Anmeldung zur Änderung nutzen

- Sagen Sie dem Empfänger in Ihrem Text für die ersten Schritte, dass er das Passwort sofort ändern soll:
  [Das Administratorpasswort ändern](../administratorpasswort-aendern.md).
- Geben Sie dem Konto eine echte E-Mail-Adresse (`--email`, `[site_admin] Email`), damit **Passwort vergessen?**
  jemanden erreicht.
- Empfehlen Sie ein persönliches Administratorkonto je Person, die Anmeldung mit `admin` stillzulegen
  ([Kontenhygiene](../standard-administratorpasswort.md#kontenhygiene)) und die zweistufige Anmeldung für
  Administratoren.
- Eine Einstellung, die bei der ersten Anmeldung eine Passwortänderung erzwingt, ist für Exponential 6.0.15 nicht
  dokumentiert; ein Bundle, das dieses Verhalten möchte, fragt das Passwort beim ersten Start ab, statt eines zu
  erzeugen.

## Checkliste

- [ ] Kein festes Passwort in Vorlagen, Images, Skripten oder der Dokumentation eines Bundles für echte Websites.
- [ ] Das erzeugte Passwort erreicht den Empfänger einmal, und die Datei wird gelöscht.
- [ ] Die E-Mail-Adresse des Administrators ist die des Empfängers.
- [ ] Keine Datenbank im Bundle hat noch `publish`.
- [ ] Ihr Text für die ersten Schritte verlinkt die Passwortänderung und die Kontenhygiene.

## Verwandte Seiten

- [Erste Anmeldung und Standardpasswörter (Hosting)](../hosting/sicherheit/erste-anmeldung-und-standardpasswoerter.md)
- [Kapitel 10.1 Erste Anmeldung](../../installation/10-nach-der-installation.md#101-erste-anmeldung-und-das-administratorpasswort)
- [Sicherheitsvorgaben eines Bundles](sicherheitsvorgaben-eines-bundles.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
