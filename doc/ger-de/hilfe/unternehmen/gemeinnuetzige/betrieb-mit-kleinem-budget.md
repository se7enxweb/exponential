---
title: Betrieb mit kleinem Budget
audience: businesses, project-manager, admin, hosting
category: business-solutions
order: 60
summary: Wie eine gemeinnützige Organisation eine Exponential-Website günstig betreibt, ohne sie anfällig zu machen - keine Lizenzgebühr, SQLite als Standarddatenbank, Hosting-Möglichkeiten, Installation mit einem Befehl, die Routine, die teure Ausfälle verhindert, was Ehrenamtliche tun können und wo Sparen eine falsche Sparsamkeit ist.
keywords: kleines Budget, Hosting für gemeinnützige Organisationen, keine Lizenzgebühr, Shared Hosting, Wartung, Backups, Updates, ehrenamtlicher Administrator, Gesamtkosten, günstige Website, low budget, nonprofit hosting, no license fee, SQLite, shared hosting, VPS, maintenance, backups, updates, volunteer administrator, total cost, cheap website
type: help
---
# Betrieb mit kleinem Budget

Diese Seite richtet sich an Schatzmeister, Vorstände und die Ehrenamtlichen oder Partner, die sich um die Website einer
gemeinnützigen Organisation kümmern. Sie erklärt, wo eine Exponential-Website Geld kostet, welche Entscheidungen das
niedrig halten und welche kleine Routine die teure Art von Problemen verhindert. Sie nennt keine Preise: Verwenden Sie
Ihre eigenen Angebote und das Kostenmodell unter [Kosten und Rendite](../kosten/README.md).

## Inhalt

1. [Wohin das Geld geht](#wohin-das-geld-geht)
2. [Keine Lizenzgebühr](#keine-lizenzgebühr)
3. [SQLite: eine Datei statt eines Datenbankservers](#sqlite-eine-datei-statt-eines-datenbankservers)
4. [Hosting-Möglichkeiten](#hosting-möglichkeiten)
5. [Günstig anfangen](#günstig-anfangen)
6. [Die Routine, die teure Probleme verhindert](#die-routine-die-teure-probleme-verhindert)
7. [Was Ehrenamtliche tun können und was einen Profi braucht](#was-ehrenamtliche-tun-können-und-was-einen-profi-braucht)
8. [Falsche Sparsamkeit](#falsche-sparsamkeit)
9. [Ein durchgerechnetes Beispiel](#ein-durchgerechnetes-beispiel)

## Wohin das Geld geht

| Kosten | Können sie null sein? |
|---|---|
| Softwarelizenz | Ja, es gibt keine |
| Hosting | Nein, aber sie können gering sein |
| Domainname und Zertifikat | Domain nein; Zertifikate gibt es beim Hoster oft kostenlos |
| Aufbau der Website (Design, Einrichtung) | Nur wenn ein fähiger Ehrenamtlicher es übernimmt |
| Betrieb (Updates, Backups, Prüfungen) | Nur wenn jemand verlässlich verantwortlich ist |
| Mail-Zustellung für Newsletter | Hängt von Ihrem Anbieter ab |

## Keine Lizenzgebühr

„Die Nutzung von Exponential unter den Bedingungen der GNU GPL ist kostenlos“ (README, „License“). Die Erweiterungen, die
dieser Bereich nennt (`cjw_newsletter`, `eztags`, Exponential Layouts, `ezpaypal`), werden über `composer.json` mit dem
Produkt installiert, ohne separaten Kauf. Käufer bezahlen für „Arbeit und Dienstleistungen, wenn sie sie wollen: Design,
Entwicklung, Migration, Support, Hosting, Schulung“
([Lizenzierung für Käufer](../../geschaeft/lizenzen-fuer-kaeufer.md#keine-lizenzgebühren-und-wofür-käufer-tatsächlich-bezahlen)).
Es gibt keine Gebühr pro Redakteur, pro Website oder pro Sprache, die Sie budgetieren müssten.

## SQLite: eine Datei statt eines Datenbankservers

SQLite 3 ist die erste Datenbank in der offiziellen Reihenfolge und der Standard. „Mit SQLite läuft eine vollständige
Exponential-Website aus einer Datei auf der Platte, ohne Datenbankserver, der installiert, konfiguriert oder abgesichert
werden müsste“ ([Unterstützung für SQLite-Datenbanken](../../../funktionen/6.0/sqlite-datenbank.md)). Für eine kleine
gemeinnützige Organisation bedeutet das einen Dienst weniger, den man bezahlen, patchen und sichern muss.

Kennen Sie ihre Grenze: „SQLite erlaubt jeweils nur einen Schreibenden. Viele Redakteure, die im selben Moment
veröffentlichen, reihen sich hintereinander ein ... für ein geschäftiges Redaktionsteam wählen Sie MySQL, PostgreSQL oder
MongoDB“ (dieselbe Seite, Grenzen und gute Praxis). Eine gemeinnützige Organisation mit einer Handvoll Redakteuren
liegt meist gut darunter. Ein späterer Wechsel ist möglich; siehe
[Datenbankunterstützung](../../datenbankunterstuetzung.md).

## Hosting-Möglichkeiten

| Modell | Passt zu | Hinweis |
|---|---|---|
| Shared Hosting | Sehr kleinen Websites | Apache oder LiteSpeed mit `.htaccess`, der Einrichtungsassistent, SQLite oder die Datenbank des Panels funktionieren; Velocity meist nicht ([Shared Hosting](../../hosting/modelle/shared-hosting.md)) |
| Ein kleiner virtueller Server | Den meisten kleinen gemeinnützigen Organisationen | Mehr Kontrolle; jemand muss ihn patchen ([VPS-Hosting](../../hosting/modelle/vps-hosting.md)) |
| Managed Hosting | Organisationen ohne technische Ehrenamtliche | Der Hoster patcht und sichert ([Managed Hosting](../../hosting/modelle/managed-hosting.md)) |

Exponential Velocity, der mitgelieferte Server, „braucht außer PHP nichts Installiertes“ (README, Requirements), was zu
einem kleinen virtuellen Server passt. Was eine Website von jedem Hoster braucht, steht in
[Hosting für eine Website](../../ersteller/website-ersteller/hosting-fuer-eine-website.md); siehe auch
[Hosting auswählen](../auswahl/hosting-auswaehlen.md).

## Günstig anfangen

`./console exp:install` „installiert das Multisite-Paket auf **SQLite**, in `eng-US`“, mit einer Website und einer
Administration, und gibt die Anmeldeadressen aus
([Exponential mit einem Befehl installieren](../../../funktionen/6.0/installation-mit-einem-befehl.md)). Ein Ehrenamtlicher kann
so das ganze Produkt auf einem Laptop ausprobieren, bevor irgendetwas gekauft wird.

Die mitgelieferten `ezwebin`-Klassen (Artikel, Veranstaltung, Veranstaltungskalender, Feedback-Formular, Datei, Ordner,
Frontpage, Blog) decken die meisten Seiten einer gemeinnützigen Organisation ohne eigene Entwicklung ab
(`extension/ezwebin/packages/`).

## Die Routine, die teure Probleme verhindert

Die meisten teuren Vorfälle auf kleinen Websites kommen von fehlenden Backups, nicht eingespielten Updates und davon,
dass niemand hinschaut. Die Routine für Website-Ersteller ist kurz
([Ihre Website pflegen](../../ersteller/website-ersteller/ihre-website-pflegen.md)):

- [ ] **Backups** von Datenbank und Speicher, mit einer tatsächlich getesteten Wiederherstellung. Bei SQLite sichern Sie
      mit `sqlite3 <file> ".backup <target>"`, „nicht indem Sie die Datei kopieren, während die Website beschäftigt ist“
      ([Unterstützung für SQLite-Datenbanken](../../../funktionen/6.0/sqlite-datenbank.md#grenzen-und-gute-praxis),
      [Backups und Wiederherstellungen](../../ersteller/website-ersteller/backups-und-wiederherstellungen.md)).
- [ ] **Updates** von Exponential, Erweiterungen und PHP, zuerst auf einer Kopie getestet
      ([Updates und Upgrades](../../ersteller/website-ersteller/updates-und-aktualisierungen.md)).
- [ ] **Cron** läuft, damit Newsletter, Indexierung und Aufräumarbeiten stattfinden ([Cronjobs](../../../anleitungen/cronjobs.md)).
- [ ] **Logs** werden monatlich angesehen ([Log-Viewer](../../../funktionen/6.0/log-betrachter.md)).
- [ ] **Konten** ausgeschiedener Ehrenamtlicher werden deaktiviert ([Benutzerkonten prüfen](../../../funktionen/6.0/benutzerpflege.md)).

Setzen Sie einen Namen und ein Datum neben jede Zeile; siehe die [Monatliche Betriebscheckliste](../vorlagen/monatliche-betriebscheckliste.md).

## Was Ehrenamtliche tun können und was einen Profi braucht

| Aufgabe | Ein geschulter Ehrenamtlicher | Ein Profi oder Partner |
|---|---|---|
| Inhalte schreiben und veröffentlichen | Ja | |
| Kampagnenseiten in Layouts | Ja | |
| Formulare und Newsletter lesen | Ja | |
| Monatliche Prüfungen nach einer Checkliste | Ja | |
| Updates, PHP-Versionen, Serversicherheit | Mit Erfahrung | Empfohlen |
| Wiederherstellung aus dem Backup unter Druck | Nur wenn geprobt | Empfohlen |
| Eigene Templates und Klassen | Mit Erfahrung | Empfohlen |

Wer auch immer die Administratorrolle innehat: Sorgen Sie dafür, dass eine zweite Person übernehmen kann
([Übergabe bei Wechsel der Ehrenamtlichen](../vereine/uebergabe-bei-wechsel-der-ehrenamtlichen.md)).

## Falsche Sparsamkeit

- **Keine getestete Wiederherstellung.** Ein Backup, das niemand wiederhergestellt hat, ist eine Hoffnung, kein Backup.
- **Updates auslassen.** Sicherheitskorrekturen werden als Updates veröffentlicht; siehe
  [Sicherheitsupdates und Offenlegung](../sicherheit/sicherheitsupdates-und-offenlegung.md).
- **Eine Person weiß alles.** Wenn sie geht, ist die Website verwaist.
- **Newsletter von einer nicht authentifizierten Domain versenden.** Die Mails landen im Spam, und die Unterstützer
  sehen sie nie ([Zustellung der Newsletter-Mails](../newsletter/zustellung-der-newsletter-mails.md#absenderdomain-und-ruf)).
- **Unbegrenzte anonyme Uploads.** Die Platte läuft voll, und die Backups wachsen
  ([Ehrenamtliche und Anmeldeformulare](ehrenamtliche-und-anmeldeformulare.md#das-anhängen-einer-datei-erlauben)).

## Ein durchgerechnetes Beispiel

Der Bereich Kosten hat ein durchgerechnetes Beispiel für eine gemeinnützige Organisation, in dem jede Eingabe als
Beispielannahme gekennzeichnet ist, die Sie durch Ihre eigenen Zahlen ersetzen:
[Durchgerechnetes Beispiel: eine gemeinnützige Organisation](../kosten/durchgerechnetes-beispiel-gemeinnuetzige-organisation.md).

## Verwandte Seiten

- [Gemeinnützige Organisationen](README.md)
- [Gesamtbetriebskosten](../kosten/gesamtbetriebskosten.md)
- [Wofür Sie nicht bezahlen](../kosten/wofuer-sie-nicht-bezahlen.md)
- [SQLite für Hoster](../../hosting/grundlagen/sqlite-fuer-hoster.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
