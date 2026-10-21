---
title: Hosting im eigenen Rechenzentrum
audience: government, hosting, admin
category: public-sector
order: 20
summary: Für interne IT-Teams, die Exponential auf ihren eigenen Servern betreiben. Was eine Website von Ihrem Rechenzentrum braucht, welche Betriebssysteme das Projekt testet, wie Sie die Arbeit zwischen Teams aufteilen und wo die ausführlichen Serverseiten stehen.
keywords: im eigenen Rechenzentrum, eigenes Rechenzentrum, internes Hosting, Selbstbetrieb, Serverraum, IT im öffentlichen Sektor, Linux, AlmaLinux, RHEL, Debian, Ubuntu, Stufen der Betriebssysteme, Anforderungen, on-premise, on premises, own data center, in-house hosting, self-hosting, operating system tiers, requirements
type: help
---
# Hosting im eigenen Rechenzentrum

Diese Seite richtet sich an das interne IT-Team einer öffentlichen Stelle, das Exponential auf eigener Hardware oder in
einem eigenen Virtualisierungscluster betreiben möchte. Sie beantwortet, was die Software von Ihrem Rechenzentrum
braucht, welche Plattformen das Projekt selbst betreibt und testet, wie sich die Arbeit üblicherweise zwischen
Infrastruktur-, Anwendungs- und Webteams aufteilt und wo die Schritt-für-Schritt-Seiten zu den Servern stehen. Die
Fakten stammen aus [Unterstützte Betriebssysteme](../../../betriebssystemunterstuetzung.md),
[Anforderungen auf einen Blick](../../../hosting/grundlagen/anforderungen-auf-einen-blick.md) und dem Installationshandbuch.

Der Bereich Hosting dieser Hilfe wurde für Hoster geschrieben. Das meiste davon gilt auch für ein internes Team;
diese Seite verlinkt darauf, statt es zu wiederholen, und ergänzt, was anders ist, wenn der „Kunde“ Ihre eigene
Organisation ist.

## Inhalt

1. [Was eine Website von Ihrem Rechenzentrum braucht](#was-eine-website-von-ihrem-rechenzentrum-braucht)
2. [Das Betriebssystem wählen](#das-betriebssystem-wählen)
3. [Wer was übernimmt](#wer-was-übernimmt)
4. [Warum das eigene Rechenzentrum zu vielen öffentlichen Stellen passt](#warum-das-eigene-rechenzentrum-zu-vielen-öffentlichen-stellen-passt)
5. [Was Ihnen das eigene Rechenzentrum nicht bietet](#was-ihnen-das-eigene-rechenzentrum-nicht-bietet)
6. [Checkliste](#checkliste)

## Was eine Website von Ihrem Rechenzentrum braucht

Eine Exponential-Website ist eine PHP-Anwendung mit einer Datenbank und einem Verzeichnis mit Dateien, die sie
schreibt. Die vollständige Liste steht unter
[Was das Hosting von Exponential umfasst](../../../hosting/grundlagen/was-das-hosting-von-exponential-umfasst.md); kurz gefasst:

| Bestandteil | Was es ist | Wo Sie nachlesen |
|---|---|---|
| PHP 8 für das Web und für die Kommandozeile | dieselbe Version für beide; 8.0 oder neuer, Velocity eingeschlossen | [PHP-Versionen und -Erweiterungen](../../../hosting/grundlagen/php-versionen-und-erweiterungen.md) |
| Eine Datenbank | eine von sechs, in der offiziellen Reihenfolge des Projekts | [Datenbankwahl in der offiziellen Reihenfolge](datenbankwahl-in-der-offiziellen-reihenfolge.md) |
| Ein Webserver oder die Velocity-Engine | liefert Seiten und HTTPS aus | [Wahl des Webservers](wahl-des-webservers.md) |
| Beschreibbare Verzeichnisse | `var/` und die überschreibenden Einstellungen | [Aufbau des Speichers](../../../hosting/betrieb/aufbau-des-speichers.md) |
| Cron | führt die Cronjob-Teile aus (Veröffentlichung, Workflows, das Audit) | [Cronjobs](../../../hosting/betrieb/cronjobs.md) |
| Ausgehende E-Mail | Benachrichtigungen, Passwort-E-Mails, Newsletter | [E-Mail-Zustellung](../../../hosting/betrieb/mailzustellung.md) |
| Datensicherungen und Überwachung | Ihre eigenen Systeme | [Datensicherung und Wiederherstellung](../../../../../eng-us/help/government/operations/backups-and-recovery/README.md), Abschnitt Überwachung dieser Betriebsseiten |

Bevor Sie etwas installieren, prüfen Sie eine Maschine mit dem Befehl, den das Projekt bereitstellt; er listet die
PHP-Erweiterungen auf, die die Installation verlangt, und gibt den Installationsbefehl für Ihr System aus:

```bash
php -v | head -1
./console exp:velocity ext check --allow-root-user
```

## Das Betriebssystem wählen

Das Projekt ordnet Betriebssysteme in vier Stufen ein ([Unterstützte Betriebssysteme](../../../betriebssystemunterstuetzung.md)):

| Stufe | Bedeutung | Beispiele |
|---|---|---|
| 1. Hier getestet | täglich vom Projekt oder seiner CI betrieben | AlmaLinux 9 (der Entwicklungsserver); Ubuntu in GitHub Actions |
| 2. Bekanntermaßen funktionsfähig | durch Dokumentation, Pakete oder die Velocity-CI abgedeckt | RHEL 9 und 10, Rocky Linux, Oracle Linux, CentOS Stream; Debian 12 und 13; Ubuntu 22.04 und 24.04; Alpine; FreeBSD 14; Windows x64; Docker und Podman |
| 3. Voraussichtlich funktionsfähig | nichts Bekanntes steht im Weg, niemand hat es laufen sehen | Fedora, SUSE und openSUSE, Amazon Linux 2023, Kubernetes |

Für eine öffentliche Stelle bedeutet das meist: Verwenden Sie die Linux-Distribution, die Ihre Organisation bereits
unterstützt, solange sie in Stufe 1 oder 2 liegt. Ein System der Stufe 3 ist „eine begründete Erwartung, kein
Testergebnis“ ([Betriebssysteme für Hosts](../../../hosting/grundlagen/betriebssysteme-fuer-hoster.md)). Liegt Ihre
Standardplattform in Stufe 3, halten Sie das als Risiko in Ihrer Hosting-Entscheidung fest (siehe
[Entscheidungsdokumentation zum Hosting](hosting-entscheidungsprotokoll.md)).

## Wer was übernimmt

In einer typischen IT-Organisation des öffentlichen Sektors teilt sich die Arbeit so auf. Passen Sie die Namen an
Ihre eigenen Teams an.

| Aufgabe | Meist erledigt von | Werkzeuge von Exponential |
|---|---|---|
| Maschinen, Betriebssystem, Patches | Infrastrukturteam | keine; Ihre Standardwerkzeuge |
| PHP, der Datenbankserver, der Webserver oder Velocity | Infrastruktur- oder Plattformteam | `exp:velocity ext check`, `exp:velocity status` |
| Exponential installieren und aktualisieren | Anwendungsteam | `exp:install`, die Anleitung zur Aktualisierung, Setup > Upgrade-Prüfung |
| Einstellungen, Siteaccesses, Rollen | Anwendungsteam, Webteam | Setup > INI-Einstellungen, Benutzer > Rollen und Richtlinien |
| Inhalte | Redaktionen der Fachbereiche | die Administration |
| Datensicherungen, Überwachung, Vorfälle | Betrieb | siehe die Seiten zu Datensicherung, Überwachung und Vorfällen in diesem Bereich |

Schreiben Sie die Aufteilung einmal auf, mit den Namen von Teams statt von Personen, und bewahren Sie sie neben Ihren
Runbooks auf. Die Vorfallseiten dieses Bereichs setzen voraus, dass die Person in Rufbereitschaft weiß, wem die
Datenbank und wem die Anwendung gehört.

## Warum das eigene Rechenzentrum zu vielen öffentlichen Stellen passt

- **Die Daten bleiben, wo Sie sie ablegen.** Alle Inhalte, hochgeladenen Dateien, Konten und Logs liegen auf Ihren
  eigenen Maschinen; siehe [Speicherort der Daten und was wo gespeichert wird](datenstandort-und-was-wo-gespeichert-wird.md).
- **Keine Lizenzgebühren pro Server.** Exponential ist freie Software unter der GNU GPL v2 oder neuer
  ([Lizenz](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)), daher ist ein weiterer Test- oder
  Bereitschaftsserver eine Hardware-Entscheidung und keine Beschaffung weiterer Lizenzen.
- **Ihr eigener Änderungsprozess gilt.** Code und Einstellungen können aus der Versionsverwaltung bereitgestellt
  werden, und die Administration lässt sich gegen Änderungen an Einstellungen sperren
  ([Konfiguration unter Versionsverwaltung](konfiguration-unter-versionskontrolle.md)).
- **Eine Maschine reicht für viele Websites.** Die Standarddatenbank SQLite 3 braucht keinen Datenbankserver und
  eignet sich für eine Website, die von einer Maschine ausgeliefert wird ([Datenbankunterstützung](../../../datenbankunterstuetzung.md)).

## Was Ihnen das eigene Rechenzentrum nicht bietet

- **Redundanz ist Ihre Aufgabe.** Ein einzelner Server ist ein Single Point of Failure. Lesen Sie die
  [Übersicht zur Hochverfügbarkeit](../verfuegbarkeit/hochverfuegbarkeit-im-ueberblick.md), bevor Sie mehr versprechen, als eine
  Maschine leisten kann.
- **Kopien außer Haus sind Ihre Aufgabe.** Eine Datensicherung im selben Raum wie der Server übersteht keinen Brand
  und kein Hochwasser; siehe [Externe und verschlüsselte Kopien](../../../../../eng-us/help/government/operations/backups-and-recovery/offsite-and-encrypted-copies.md).
- **Standardmäßig gibt es keinen Supportvertrag eines Herstellers.** Das Projekt veröffentlicht keine
  Service-Level-Vereinbarung; Supportmöglichkeiten sind unter [Wer ist 7x?](../../../wer-ist-7x.md) beschrieben.

## Checkliste

- [ ] Das Betriebssystem liegt in Stufe 1 oder Stufe 2, oder das Risiko der Stufe 3 ist dokumentiert.
- [ ] `exp:velocity ext check` meldet für die genutzten Funktionen nichts Fehlendes.
- [ ] Datenbank, Webserver oder Velocity, Cron und E-Mail sind gewählt und dokumentiert.
- [ ] Die Aufteilung der Aufgaben zwischen den Teams ist schriftlich festgehalten.
- [ ] Datensicherungen verlassen das Gebäude, und eine Wiederherstellung wurde getestet.

## Verwandte Seiten

- [Hosting](README.md): Übersicht dieses Ordners
- [Ein Hosting-Modell wählen](../../../hosting/modelle/ein-hosting-modell-waehlen.md) und [Dedizierte Server](../../../hosting/modelle/dedizierte-server.md)
- [Checkliste für neue Server](../../../hosting/checklisten/checkliste-fuer-einen-neuen-server.md)
- [Den Host härten](den-host-haerten.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
