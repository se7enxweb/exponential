---
title: Demo-Websites für Wiederverkäufer
audience: reseller, partner, hosting, sales-marketing
category: reselling
order: 250
summary: Wie ein Wiederverkäufer Demo-Websites für Interessenten betreibt - eine private Demo, die Sie kontrollieren, oder eine öffentliche Demo mit veröffentlichten Anmeldedaten im Demomodus (die Umgebungsvariable EXP_DEMO_MODE=1), die Wahl des Seitenpakets, der planmäßige Neuaufbau, was der Demomodus schützt und was nicht, und eine Checkliste für Demo-Websites.
keywords: Demo, Demo-Website, öffentliche Demo, Demomodus, EXP_DEMO_MODE, veröffentlichtes Passwort, sevenx_multisite, Seitenpaket, Neuaufbau, Interessenten, Vertriebsdemo, Testversion, demo, demo site, public demo, demo mode, published password, site package, rebuild, prospects, sales demo, trial
type: help
---
# Demo-Websites für Wiederverkäufer

Diese Seite ist für Vertriebsmitarbeiter und Systemadministratoren bei Wiederverkäufern, die eine Website brauchen,
um sie Interessenten zu zeigen. Eine Demo, die Sie kontrollieren, gewinnt mehr Aufträge als Screenshots. Es gibt zwei
Arten, und sie brauchen unterschiedliche Pflege.

## Inhalt

1. [Private oder öffentliche Demo](#private-oder-öffentliche-demo)
2. [Eine Demo aufbauen](#eine-demo-aufbauen)
3. [Demomodus für eine öffentliche Demo](#demomodus-für-eine-öffentliche-demo)
4. [Was der Demomodus nicht schützt](#was-der-demomodus-nicht-schützt)
5. [Checkliste für Demo-Websites](#checkliste-für-demo-websites)

## Private oder öffentliche Demo

| | Private Demo | Öffentliche Demo |
|---|---|---|
| Wer sich anmeldet | Ihr Vertriebspersonal oder ein Interessent mit einem persönlichen Konto | Jeder, mit veröffentlichten Anmeldedaten eines Administrators |
| Passwort | Geheim, stark | Veröffentlicht |
| Demomodus | Aus | An: `EXP_DEMO_MODE=1` |
| Neuaufbau | Vor wichtigen Demos | Nach Plan, alle paar Stunden |
| Verwendung für | Geführte Demos, zugeschnitten auf einen Interessenten | „Probieren Sie es selbst“ auf Ihrer Website |

## Eine Demo aufbauen

1. Wählen Sie ein Seitenpaket mit Demo-Inhalten: `sevenx_multisite` („7x MultiSite Default Installation“) oder
   `sevenx_site`; die Varianten `_clean` haben keine Demo-Artikel und -Bilder
   ([Seitenpakete für Hoster](../../hosting/bereitstellung/seitenpakete-fuer-hoster.md)).
2. Installieren Sie es unter einem eigenen Systembenutzer, Pool und einer eigenen Datenbank, nie neben
   Kunden-Websites ([Installation mit einem Befehl](../../../funktionen/6.0/installation-mit-einem-befehl.md)).
3. Fügen Sie eigene Akzente hinzu: Ihr Logo im Design, eine Seite über Ihre Dienstleistungen, ein Beispiel aus der
   Branche des Interessenten.
4. Bereiten Sie den Gesprächsleitfaden vor: [Demo-Drehbücher](../../geschaeft/demo-drehbuecher.md) enthält
   Vorführungen von 5, 15 und 45 Minuten mit einer Checkliste für die Vorbereitung und dem, was schiefgehen kann.
5. Für eine Presse- oder öffentliche Demo siehe [Eine Demo-Installation einrichten](../../presse/eine-demo-installation-einrichten.md).

## Demomodus für eine öffentliche Demo

Eine öffentliche Demo veröffentlicht die Anmeldedaten ihres Administrators, damit jeder die Administration
ausprobieren kann. Der **Demomodus** hält sie für den nächsten Besucher nutzbar: Niemand kann das Passwort eines
Administrators ändern oder zurücksetzen ([Demomodus](../../demomodus.md)).

| Regel | Detail |
|---|---|
| Wie er eingeschaltet wird | Nur durch die Umgebungsvariable `EXP_DEMO_MODE=1` für die PHP-Prozesse der Demo; keine INI-Einstellung, kein Anfrageparameter, keine Seite in der Administration |
| Nur der exakte Wert `1` | `0`, `true`, `yes`, `on` und ein leerer Wert lassen ihn aus |
| Jeder Prozess | Web-PHP, Exponential Velocity, Cron und Konsolenbefehle brauchen ihn jeweils |
| Was Besucher sehen | Ein gelbes Banner **Demo-Modus** auf jeder Seite der Administration |
| Installation | Bei gesetzter Variable akzeptieren die Installer das veröffentlichte Demo-Passwort (`publish` oder `publishing`) für den neuen Administrator |
| Prüfung | Zeilen `demo/mode` in `var/log/security.log`, Audit-Ereignisse `system.demo.mode` |

Wie Sie ihn für PHP-FPM, Apache, LiteSpeed, Velocity, Cron und Container setzen:
[Demomodus für Hoster](../../hosting/sicherheit/demomodus-fuer-hoster.md). Setzen Sie ihn nie serverweit, in einer
Provisionierungsvorlage oder an einer Stelle, von der eine Kunden-Website ihn erben könnte.

## Was der Demomodus nicht schützt

- Der Demomodus schützt nur Passwörter von Administratoren. Ein als Administrator angemeldeter Besucher kann weiterhin
  Konten anlegen, Rollen und Einstellungen ändern, Pakete installieren und Dateien hochladen. Bauen Sie die Demo nach
  Plan neu auf.
- Andere veröffentlichte Anmeldedaten (ein Redakteur, ein Mitglied) sind nicht geschützt.
- Halten Sie echte Daten, echte Mailadressen und Geheimnisse aus der Demo heraus und leiten Sie ihre Mails in eine
  Datei oder eine Senke.
- Geht die Variable nach einem Neustart verloren, ist der Demomodus aus, und das einzige Anzeichen ist das fehlende
  Banner.

Quelle: [Demomodus für Hoster](../../hosting/sicherheit/demomodus-fuer-hoster.md#risiken-die-sie-dem-eigentümer-der-demo-nennen). Antworten
auf häufige Fragen: [FAQ zum Demomodus](../../demomodus-faq.md).

## Checkliste für Demo-Websites

- [ ] Eigener Systembenutzer, Pool und eigene Datenbank; keine Kundendaten.
- [ ] Private Demo: starkes Passwort, Demomodus aus. Öffentliche Demo: `EXP_DEMO_MODE=1` für jeden Prozess.
- [ ] Banner in der Administration einer öffentlichen Demo sichtbar.
- [ ] Neuaufbau geplant; das Build-Skript schreibt die Variable nicht in `config.php` oder `settings/`.
- [ ] Mails gehen in eine Datei oder eine Senke.
- [ ] Produktivmaschinen geprüft: `grep -r EXP_DEMO_MODE` über Pools, virtuelle Hosts, Service-Units und Crontabs
      findet nichts.
- [ ] Gesprächsleitfaden und Schritte zur Wiederherstellung bereit ([Demo-Drehbücher](../../geschaeft/demo-drehbuecher.md)).

## Verwandte Seiten

- [Demomodus](../../demomodus.md), [FAQ zum Demomodus](../../demomodus-faq.md)
- [Probleme mit dem Demomodus](../../hosting/runbooks/probleme-mit-dem-demomodus.md)
- [Vertriebsprozess für Wiederverkäufer](../verkauf/vertriebsprozess.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
