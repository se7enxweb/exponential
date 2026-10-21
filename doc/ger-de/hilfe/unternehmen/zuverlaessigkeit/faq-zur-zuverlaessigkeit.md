---
title: FAQ zur Zuverlässigkeit
audience: businesses, project-manager, sales-marketing, admin
category: business-solutions
order: 230
summary: Kurze, belegte Antworten auf die Fragen, die Inhaber und Führungskräfte am häufigsten zu Stabilität, Support, Verfügbarkeit, Backups, Aktualisierungen, Velocity und Datenbanken von Exponential stellen, und dazu, was zu tun ist, wenn etwas schiefgeht.
keywords: FAQ, Fragen, Zuverlässigkeit, Stabilität, Verfügbarkeit, Support, Sicherheitskorrekturen, Backups, Aktualisierungen, Datenbank, Ausfallzeit, questions, reliability, stability, uptime, support, security fixes, PHP, backups, upgrades, Velocity, database, downtime, SLA
type: help
---
# FAQ zur Zuverlässigkeit

Diese Seite richtet sich an Inhaber, Führungskräfte und Entscheider, die schnelle Antworten wollen. Jede Antwort ist
kurz, sagt, woher die Tatsache stammt, und verweist auf die Seite mit den Einzelheiten. Steht Ihre Frage nicht hier,
beginnen Sie bei [Zuverlässigkeit für Unternehmen](README.md) oder bei der [FAQ für Unternehmen](../faq-fuer-unternehmen.md)
für den ganzen Bereich.

## Inhalt

1. [Stabilität und Support](#stabilität-und-support)
2. [Verfügbarkeit und Monitoring](#verfügbarkeit-und-monitoring)
3. [Backups und Wiederherstellung](#backups-und-wiederherstellung)
4. [Aktualisierungen und Änderungen](#aktualisierungen-und-änderungen)
5. [Leistung und Wachstum](#leistung-und-wachstum)
6. [Wenn etwas schiefgeht](#wenn-etwas-schiefgeht)

## Stabilität und Support

**Wird Exponential noch gepflegt?**
Ja. „Exponential 6.0 ist die gepflegte Linie“; Sicherheitskorrekturen werden auf `main` vorgenommen und mit dem nächsten
6.0.x-Release veröffentlicht ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)).
Exponential 6.0.0 erschien am 1. Januar 2024 und 6.0.14 am 7. Juni 2026
([Wer ist 7x?](../../wer-ist-7x.md#die-releases-60x)). Siehe
[Release-Linie und Support-Richtlinie](release-linie-und-support-richtlinie.md).

**Wie alt ist die Codebasis?**
Die Historie des Repositorys reicht bis zum 2. September 2002 zurück; der Code begann als eZ Publish, entwickelt ab 1999
([Wer ist 7x?](../../wer-ist-7x.md#die-jahre-von-ez-publish-1999-bis-2021)). Siehe
[Warum Stabilität wichtig ist](warum-stabilitaet-wichtig-ist.md).

**Kann sich eine veröffentlichte Version unter uns ändern?**
Nein. „Ein veröffentlichter Tag wird nie geändert“
([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)). Eine Korrektur kommt als nächste
Version.

**Welche PHP-Versionen werden unterstützt?**
PHP 8.0 ist die älteste unterstützte Version, 8.1 bis 8.5 werden unterstützt, und Velocity läuft unter 8.0 oder neuer
(ab seinem Release 0.0.4.54)
([README, „What version of PHP is required“](https://github.com/se7enxweb/exponential/blob/main/README.md#what-version-of-php-is-required)).

**Gibt es ein offizielles Datum für das Ende der Unterstützung oder einen Supportvertrag?**
Das Projekt veröffentlicht weder einen Release-Kalender noch ein Service Level Agreement. Kommerzieller Support und
Hosting kommen von 7x und von Hosting-Partnern
([SLA-Optionen für Exponential-Hosting](../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md),
[Zusammenarbeit mit 7x und dem Exponential-Projekt](../auswahl/zusammenarbeit-mit-7x-und-dem-projekt.md)).

**Wie schnell werden Sicherheitsmeldungen beantwortet?**
SECURITY.md sagt: „Sie können innerhalb eines Werktags mit einer ersten Antwort rechnen“, für eine Meldung, die
vertraulich so geschickt wird, wie dort beschrieben.

## Verfügbarkeit und Monitoring

**Welche Verfügbarkeit garantiert Exponential?**
Keine, und keine Seite dieser Hilfe nennt einen Prozentwert. Die Verfügbarkeit hängt ebenso von Ihrem Hosting, Ihrem
Netzwerk und Ihrem Betrieb ab wie von der Software. Die Zahlen eines Verfügbarkeitsversprechens sind „eine geschäftliche
Entscheidung; die Quellen nennen keine“
([Zustandsprüfungen und Verfügbarkeit](../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md#verfügbarkeit-in-einem-hosting-tarif)).
Siehe [Verfügbarkeit in der Praxis](verfuegbarkeit-in-der-praxis.md).

**Gibt es eine Health-URL, die wir überwachen können?**
„Es gibt keine einzelne Health-URL; kombinieren Sie eine Prüfung von außen mit den Statusbefehlen im Inneren“
([10.11](../../../installation/10-nach-der-installation.md#1011-überwachung-und-zustandsprüfungen)). Prüfen Sie von außen die
Startseite und die Anmeldeseite der Administration, und führen Sie auf dem Server `exp:system:info`, den Cache-Status
und die Audit-Prüfung aus. Unter Velocity helfen außerdem `/Q/health` und `exp:velocity status --json`. Siehe
[Health-Checks und Monitoring](health-checks-und-monitoring.md).

**Schadet geplante Wartung unserem Suchmaschinen-Ranking?**
Der Wartungsmodus antwortet mit `503` und `Retry-After`, was Suchmaschinen als „komm später wieder“ verstehen: „Die
Seiten behalten ihren Platz im Index“
([Anleitung zum Wartungsmodus](../../../anleitungen/wartungsmodus.md#1-was-der-wartungsmodus-tut)). Siehe
[Wartungsmodus und geplante Arbeiten](wartungsmodus-und-geplante-arbeiten.md).

## Backups und Wiederherstellung

**Was muss gesichert werden?**
Die Datenbank und die Dateien (`var/storage`, `settings/override`, `settings/siteaccess`, Ihre eigenen Erweiterungen und
Designs), zur selben Zeit gesichert
([Backups und Wiederherstellungen](../../../../eng-us/help/creators/website-creators/backups-and-restores.md#what-to-back-up)). Siehe
[Backups, die sich wiederherstellen lassen](backups-die-sich-wiederherstellen-lassen.md).

**Woher wissen wir, dass unsere Backups funktionieren?**
Stellen Sie regelmäßig eines auf einem anderen Rechner wieder her und messen Sie die Zeit. „Ein Backup, das Sie nie
wiederhergestellt haben, ist eine Hoffnung“
([Betrieb einer Site](../../../anleitungen/betrieb-einer-website.md#6-sichern-und-wiederherstellen)). Siehe
[Wiederherstellungsübungen](wiederherstellungsuebungen.md).

**Ein Redakteur hat eine Seite gelöscht. Brauchen wir eine Wiederherstellung?**
Meist nicht. Entfernte Elemente landen im Papierkorb, und aus früheren Versionen lässt sich ein neuer Entwurf machen
([Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md)). Siehe
[Vorschau vor dem Veröffentlichen](vorschau-vor-dem-veroeffentlichen.md#zurückgehen).

## Aktualisierungen und Änderungen

**Wie oft sollten wir aktualisieren?**
Behandeln Sie den Wechsel auf die neueste 6.0.x als wiederkehrende Aufgabe; aktualisieren Sie im nächsten Fenster, wenn
es ein Sicherheits-Release gibt
([Release- und Upgrade-Planung](../../fuehrung/release-und-upgrade-planung.md#entscheiden-wann-ein-upgrade-erfolgt)). Siehe
[Aktualisierungen ohne Überraschungen](aktualisierungen-ohne-ueberraschungen.md).

**Woher wissen wir, dass eine Aktualisierung unsere Änderungen nicht überschreibt?**
Führen Sie vor dem Start **Setup > Upgrade-Prüfung** aus: Sie listet Release-Dateien auf, die von Hand verändert wurden,
und liest nur ([Die Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)). Siehe
[Upgrade-Prüfung und Dateikonsistenz](upgrade-pruefung-und-dateikonsistenz.md).

**Ist ein Deployment möglich, ohne Besucher zu verlieren?**
Unter Velocity „führt `graceful` den Server neu aus, ohne den lauschenden Socket freizugeben, sodass ein Deployment
keine Anfragen verliert“, und `exp:velocity deploy` führt jeden Schritt in der richtigen Reihenfolge aus und stoppt beim
ersten Fehler ([README](https://github.com/se7enxweb/exponential/blob/main/README.md#using-it),
[Bereitstellung](../../../anleitungen/bereitstellung.md#7-eine-php-änderung-mit-einem-befehl-ausliefern)). Siehe
[Unterbrechungsfreie Deployments](unterbrechungsfreie-deployments.md).

**Können sich Updates selbst installieren?**
`ezupdate` sucht nach Updates und führt Composer aus der Administration aus, hat aber „keinen eigenen Zeitplan, der eine
Website unbeaufsichtigt aktualisiert“, und das Aktualisieren ist ausgeschaltet, bis ein Administrator es einschaltet
([Automatische Updates mit ezupdate](../../hosting/betrieb/automatische-updates-mit-ezupdate.md#was-es-tut)).

## Leistung und Wachstum

**Brauchen wir Velocity?**
Nein. Exponential läuft unter Apache, Nginx und anderen Webservern, und die README merkt an, dass „Exponential 6.0.15+
auch unter Apache oder Nginx unter Last schnell ist“. Velocity hebt die Obergrenze dort an, wo Ihr Hosting einen
langlebigen Prozess erlaubt. Siehe [Velocity für Unternehmen](velocity-fuer-unternehmen.md).

**Was bedeuten die Zahlen zu Velocity für uns?**
Die README dokumentiert 0,36 ms pro gecachter Seite und 8.941 Anfragen pro Sekunde im Dauerbetrieb auf einem Rechner
mit 12 Kernen und erklärt, wie sie gemessen wurden. Sie sind kein Versprechen für Ihre Website; messen Sie Ihre eigenen
Seiten mit `exp:benchmark`. Siehe
[Velocity für Unternehmen](velocity-fuer-unternehmen.md#die-dokumentierten-zahlen-ehrlich-gelesen).

**Welche Datenbank ist die zuverlässigste Wahl?**
Die offizielle Reihenfolge des Projekts ist 1. SQLite 3 (Standard) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB ·
5. Oracle · 6. MySQL ([Datenbankunterstützung](../../datenbankunterstuetzung.md#die-unterstützten-datenbanken-in-ihrer-reihenfolge)). SQLite
passt zu einem Rechner; für viele gleichzeitig Schreibende oder mehrere Webserver verwenden Sie einen Datenbankserver.
Siehe [Datenbankwahl für Zuverlässigkeit](datenbankwahl-fuer-zuverlaessigkeit.md).

**Wann brauchen wir mehr als einen Server?**
Wenn ein Rechner mit eingeschalteten Caches Ihre gemessene Spitzenlast nicht tragen kann oder wenn die Kosten eines
Ausfalls Redundanz rechtfertigen. „Ein einzelner Rechner mit genügend CPU und Arbeitsspeicher ist für die meisten
Websites die einfachere Wahl“ ([Mehrere Web-Knoten](../../hosting/betrieb/mehrere-web-knoten.md)). Siehe
[Clustering und Hochverfügbarkeit](clustering-und-hochverfuegbarkeit.md).

**Wie bereiten wir uns auf einen Sale oder eine Kampagne vor?**
Planen Sie Wochen vorher, messen Sie, wärmen Sie die Caches vor, stoppen Sie Änderungen und beobachten Sie am Tag selbst.
Siehe [Lastspitzen planen](lastspitzen-planen.md).

## Wenn etwas schiefgeht

**Ein Kunde hat „Something went wrong“ gesehen. Was jetzt?**
Fragen Sie nach der Referenz `ERR-` auf der Seite; ein Administrator findet damit unter **Setup > Logdateien** die genaue
Anfrage ([Einen Fehler anhand seiner Referenz finden](../../../anleitungen/fehler-anhand-der-referenz-finden.md)). Siehe
[Logs und Fehlerreferenzen](logs-und-fehlerreferenzen.md).

**Wer hat diese Einstellung geändert, und wann?**
Das Prüfprotokoll zeichnet geschriebene Einstellungen, Deployments, geleerte Caches, Wartung und Änderungen an Inhalten auf
und kann nachweisen, dass die Aufzeichnung nicht verändert wurde ([Prüfprotokoll](../../../../eng-us/features/6.0/audit-trail.md)).
Siehe [Prüfprotokoll für den Betrieb](audit-trail-fuer-den-betrieb.md).

**Die Website ist nicht erreichbar. Wo fangen wir an?**
Mit Ihrem Störungsplan und dem Runbook für eine nicht erreichbare Website. Siehe
[Störungsbehandlung für kleine Teams](stoerungsbehandlung-fuer-kleine-teams.md) und
[Runbook: Website nicht erreichbar](../../hosting/runbooks/website-nicht-erreichbar.md).

**Wir haben einen Fehler in Exponential selbst gefunden.**
Melden Sie ihn im öffentlichen Tracker; Sicherheitsprobleme gehen vertraulich an die Adresse in SECURITY.md
([Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)).

## Verwandte Seiten

- [Zuverlässigkeit für Unternehmen](README.md)
- [Bewertungsbogen Zuverlässigkeit](bewertungsbogen-zuverlaessigkeit.md)
- [FAQ für Unternehmen](../faq-fuer-unternehmen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
