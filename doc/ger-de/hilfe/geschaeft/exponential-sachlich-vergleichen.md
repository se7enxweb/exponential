---
title: Exponential sachlich vergleichen
audience: sales-marketing
category: business
order: 50
summary: Wie Sie Exponential mit anderen Optionen vergleichen, und zwar nur anhand von Eigenschaften, die ein Käufer selbst prüfen kann: die GPL-Lizenz, das Selbst-Hosting, die vollständige Git-Historie, die Wahl von Datenbank, Betriebssystem und Webserver, veröffentlichte Versionen, die sich nie ändern, und das Fehlen jeder Herstellerbindung.
keywords: Vergleich, vergleichen, Wettbewerber, Bewertung, Ausschreibung, Angebotsanfrage, Herstellerbindung, Open Source, GPL, Selbst-Hosting, Git-Historie, Datenbanken, Betriebssysteme, Webserver, PHP-Versionen, CI, Sicherheitsrichtlinie, Migration, fairer Vergleich, comparison, compare, competitors, evaluation, RFP, tender, vendor lock-in, self-hosting, Git history, databases, operating systems, web servers, PHP versions, security policy, fair comparison
type: help
---
# Exponential sachlich vergleichen

Diese Seite ist für Mitarbeiter aus Vertrieb und Marketing, denen in einem Termin, einer Ausschreibung oder einer
Angebotsanfrage die Frage gestellt wird: „Wie schneidet Exponential im Vergleich ab?“ Sie beantwortet diese Frage auf
die einzige Weise, die der eigenen Prüfung des Käufers standhält: mit Eigenschaften von Exponential, die dokumentiert
sind und die der Käufer nachprüfen kann, jeweils mit dem Befehl oder der Seite, die sie belegt. Sie bewertet, nennt und
kritisiert keine anderen Produkte. Ein Käufer, der fair vergleicht, füllt die andere Spalte selbst aus; Ihre Aufgabe ist
es, die Spalte für Exponential vollständig, belegt und ehrlich zu machen, auch dort, wo Exponential nicht die richtige
Wahl ist.

## Inhalt

1. [Die Regeln eines fairen Vergleichs](#die-regeln-eines-fairen-vergleichs)
2. [Die nachprüfbaren Eigenschaften](#die-nachprüfbaren-eigenschaften)
3. [Wie ein Käufer jede Eigenschaft prüft](#wie-ein-käufer-jede-eigenschaft-prüft)
4. [Keine Herstellerbindung, Punkt für Punkt](#keine-herstellerbindung-punkt-für-punkt)
5. [Fragen an jede Option, mit den Antworten für Exponential](#fragen-an-jede-option-mit-den-antworten-für-exponential)
6. [Ein Vergleichsraster zum Ausfüllen](#ein-vergleichsraster-zum-ausfüllen)
7. [Wo Exponential nicht die richtige Wahl ist](#wo-exponential-nicht-die-richtige-wahl-ist)
8. [Über verwandte Projekte sprechen](#über-verwandte-projekte-sprechen)
9. [Formulierungen: so und nicht so](#formulierungen-so-und-nicht-so)
10. [Ein ausgearbeitetes Beispiel: eine Ausschreibungsfrage beantworten](#ein-ausgearbeitetes-beispiel-eine-ausschreibungsfrage-beantworten)

## Die Regeln eines fairen Vergleichs

1. **Vergleichen Sie Eigenschaften, nicht den Ruf.** Eine Eigenschaft ist etwas, das der Käufer prüfen kann: eine
   Lizenzdatei, eine Commit-Historie, eine Liste unterstützter Datenbanken. Ein Ruf ist eine Meinung.
2. **Jede Aussage hat eine Quelle.** Verwenden Sie die Links auf dieser Seite. Wenn Sie keine Quelle finden, lassen Sie
   die Aussage weg oder sagen Sie „Die Dokumentation macht dazu keine Angabe“.
3. **Beschreiben Sie nie ein anderes Produkt.** Sie können sagen, was Exponential tut. Was ein anderes Produkt tut, stellt
   der Käufer anhand der Dokumentation dieses Produkts selbst fest.
4. **Datieren Sie Ihre Zahlen.** Zahlen wie die der Commits oder Pakete ändern sich. Nennen Sie das Datum und den Befehl,
   aus dem die Zahl stammt, so wie es die Seiten zur Geschichte tun ([Wer ist 7x?, 7x in Zahlen](../wer-ist-7x.md#7x-in-zahlen)).
5. **Nennen Sie die Grenzen von sich aus.** Der Abschnitt [Wo Exponential nicht die richtige Wahl ist](#wo-exponential-nicht-die-richtige-wahl-ist)
   ist Teil des Vergleichs und kein Anhang, den man versteckt.

## Die nachprüfbaren Eigenschaften

| Eigenschaft | Was für Exponential zutrifft | Quelle |
|---|---|---|
| Lizenz | GNU General Public License, Version 2 oder jede spätere Version. „Die Nutzung von Exponential unter den Bedingungen der GNU GPL ist kostenlos. Die Professional License ist nicht mehr erhältlich.“ | `README.md`, „License“; `LICENSE`; [Wer ist 7x?, Open-Source-Lizenzen](../wer-ist-7x.md#open-source-lizenzen) |
| Beiträge bleiben frei | „Alle Änderungen am Projekt werden ebenfalls unter derselben Lizenz GPLv2 oder später veröffentlicht“ | `CONTRIBUTING.md` |
| Lizenz der Dokumentation | GNU Free Documentation License 1.3 oder später | [Die Lizenz dieser Dokumentation](../../README-LIZENZ.md), [Freie Dokumentation](../freie-dokumentation.md) |
| Quellcode und Historie | Die vollständige Historie ist erhalten, zurück bis zum ältesten Commit vom 2. September 2002; 27.242 Commits auf `main` am 18. Oktober 2026 | [Wer ist 7x?, Die Jahre von eZ Publish](../wer-ist-7x.md#die-jahre-von-ez-publish-1999-bis-2021) |
| Herkunft | Urheberrechtsvermerk „1998 - 2026 7x & Exponential Foundation“ neben „1999 - 2014 eZ Systems AS“ | `COPYRIGHT.md` |
| Selbst-Hosting | Läuft auf „fast jedem [Betriebssystem], auf dem PHP 8.0 oder später läuft“, in vier dokumentierten Stufen | [Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md) |
| Datenbanken | „1. SQLite 3 (Standard) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL“ | [Unterstützte Datenbanken](../datenbankunterstuetzung.md) |
| Webserver | Exponential Velocity (empfohlen), Apache, nginx, FrankenPHP, LiteSpeed, Caddy, IIS, lighttpd und weitere | [Unterstützte Betriebssysteme, Webserver](../betriebssystemunterstuetzung.md#webserver), `README.md` |
| PHP-Versionen | PHP 8.0 bis 8.5, einschließlich Velocity (ab seinem Release 0.0.4.54); PHP 7.4 nur bis 6.0.7 | [Wer ist die Exponential Foundation?, PHP-Versionen](../wer-ist-die-exponential-foundation.md#php-versionen) |
| Unveränderliche Releases | „Ein veröffentlichter Tag wird nie geändert“: Eine Korrektur kommt mit dem nächsten Release | `SECURITY.md` |
| Sicherheitsprozess | Vertrauliche Meldung, eine erste Antwort „innerhalb eines Werktags“, Korrekturen auf `main` und im nächsten 6.0.x-Release | `SECURITY.md` |
| Continuous Integration | Qualitätsprüfungen und PHPUnit bei jedem Push und jedem Pull-Request; PHPUnit unter PHP 8.1 bis 8.5; Datenbanktests mit MySQL 8.0 und PostgreSQL 15; Startprüfung unter PHP 8.0 | [Continuous Integration](../../bc/6.0/ci.md) |
| Vertrieb | Composer-Pakete auf Packagist und Git-Repositories auf GitHub unter `se7enxweb` | [Den Code beziehen](../../installation/03-den-code-beschaffen.md), [Mit Git entwickeln](../mit-git-entwickeln.md) |
| Verantwortung | Ein Unternehmen (7x) und eine gemeinnützige Organisation (die Exponential Foundation) stehen hinter dem Projekt | [Wer ist 7x?](../wer-ist-7x.md), [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md) |

## Wie ein Käufer jede Eigenschaft prüft

Geben Sie dem Käufer diese Schritte; er braucht dafür nichts von Ihnen.

```bash
# License and copyright
git clone https://github.com/se7enxweb/exponential.git
cd exponential
cat LICENSE COPYRIGHT.md

# The full history
git log --reverse --format='%ad %s' | head -1     # the oldest commit
git rev-list --count HEAD                          # the number of commits

# Every release, sorted correctly
git tag -l 'v6.0.*' --sort=version:refname

# Supported PHP versions
grep '"php"' composer.json
```

In einer laufenden Installation kann ein Administrator die Datenbank-Engine unter **Setup > Systeminformationen** zeigen
(die Karte **Datenbank**), und unter **Setup > Upgrade-Prüfung**, ob die Dateien der Installation mit dem Release
übereinstimmen ([Die Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md)).

> **Tipp**
> Bieten Sie dem Käufer eine Testinstallation an, über die er selbst verfügt. Ein einziger Befehl installiert eine
> funktionierende Site auf SQLite, ohne Datenbankserver: `./console exp:install` ([Exponential mit einem Befehl installieren](../../funktionen/6.0/installation-mit-einem-befehl.md)).

## Keine Herstellerbindung, Punkt für Punkt

„Keine Herstellerbindung“ ist eine Aussage, die Käufer schon oft gehört haben. Zerlegen Sie sie in Teile, die sich
jeweils prüfen lassen:

| Risiko der Bindung | Warum es für Exponential nicht gilt | Quelle |
|---|---|---|
| Sie dürfen die Software nicht ohne Vertrag betreiben | Die GPL „gibt Ihnen das Recht, Exponential zu nutzen, zu ändern und weiterzugeben“, kostenlos | `LICENSE`, `README.md` |
| Nur der Hersteller darf sie hosten | Sie läuft auf den eigenen Servern, Containern oder Cloud-Maschinen des Kunden | [Unterstützte Betriebssysteme, Container, Cloud und Hosting-Panels](../betriebssystemunterstuetzung.md#container-cloud-und-hosting-panels) |
| Ihre Inhalte liegen in einem proprietären Speicher | Die Inhalte liegen in einer Standarddatenbank nach Wahl des Kunden und werden mit den eigenen Werkzeugen dieser Datenbank gesichert | [Unterstützte Datenbanken, Backup und Wiederherstellung](../datenbankunterstuetzung.md#backup-und-wiederherstellung) |
| Sie können die Datenbank später nicht wechseln | Dokumentierte Werkzeuge verschieben eine Site zwischen SQL-Engines, nach Oracle und nach MongoDB | [Eine Site in eine andere Datenbank verschieben](../datenbankunterstuetzung.md#eine-site-auf-eine-andere-datenbank-umziehen) |
| Nur der Hersteller kann den Code ändern | Jedes Paket ist ein öffentliches Git-Repository; Änderungen entstehen auf einem Branch und werden als Pull-Requests eingereicht | [Mit Git entwickeln](../mit-git-entwickeln.md) |
| Nur der Hersteller kann Dienstleistungen erbringen | Unter der GPL darf jeder Dienstleistungen anbieten; 7x ist ein Anbieter, der Code bleibt frei | [Wer ist 7x?, Dienstleistungen und Hosting](../wer-ist-7x.md#dienstleistungen-und-hosting) |
| Updates können eine Version, auf die Sie sich verlassen, unbemerkt ändern | Veröffentlichte Tags ändern sich nie | `SECURITY.md` |
| Die Dokumentation liegt hinter einer Anmeldung | Die Dokumentation steht im Repository, frei unter der GNU FDL | [Freie Dokumentation](../freie-dokumentation.md) |

## Fragen an jede Option, mit den Antworten für Exponential

Stellen Sie jeder Option, die der Käufer in Betracht zieht, dieselben neutralen Fragen. Die Antworten für Exponential
stehen unten; die übrigen sammelt der Käufer.

| Frage | Antwort für Exponential | Quelle |
|---|---|---|
| Unter welcher Lizenz steht der Code, und kostet die Nutzung etwas? | GPL v2 oder später, kostenlos | `README.md`, „License“ |
| Können wir den vollständigen Quellcode und seine Historie lesen? | Ja, auf GitHub, Historie seit 2002 | [Wer ist 7x?](../wer-ist-7x.md#fast-dreißig-jahre-die-zeitleiste) |
| Können wir es selbst hosten, auf unserem Betriebssystem? | Ja; siehe die Stufe Ihres Systems | [Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md#die-vier-stufen) |
| Welche Datenbanken werden unterstützt, und welche werden in der CI getestet? | Sechs werden unterstützt; für PostgreSQL und MySQL laufen Datenbanktests in der CI, für SQLite und MongoDB Unit-Tests des Treibers bzw. Adapters | [Unterstützte Datenbanken, Testabdeckung](../datenbankunterstuetzung.md#testabdeckung-ci) |
| Wie werden Sicherheitslücken gemeldet und behoben? | Vertraulich per E-Mail, erste Antwort innerhalb eines Werktags, Korrektur im nächsten 6.0.x | `SECURITY.md` |
| Kann sich ein veröffentlichtes Release ändern, nachdem wir es installiert haben? | Nein | `SECURITY.md` |
| Wie führen wir ein Upgrade durch, und wie prüfen wir es? | Dokumentiertes Kapitel, Update-Dateien je Engine, die Upgrade-Prüfung | [Upgrade](../../installation/11-aktualisieren.md), [Die Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md) |
| Wie steigen wir aus, falls wir müssen? | Datenbank-Dumps mit Standardwerkzeugen, GPL-Code, den wir behalten | [Backup und Wiederherstellung](../datenbankunterstuetzung.md#backup-und-wiederherstellung) |
| Wer steht dahinter? | 7x und die Exponential Foundation | [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md) |
| Was veröffentlicht das Projekt nicht? | Kein einzelnes Dokument mit einer „Support-Richtlinie“; keine Angaben zu Vorstand, Führungsstruktur oder Budget der Foundation | [Die Support-Zusage](../wer-ist-die-exponential-foundation.md#die-zusage-zur-unterstützung), [Was die Quellen nicht sagen](../wer-ist-die-exponential-foundation.md#was-die-quellen-nicht-sagen) |

## Ein Vergleichsraster zum Ausfüllen

Kopieren Sie dieses Raster in das Bewertungsdokument des Käufers. Die Spalte für Exponential ist aus den obigen Quellen
ausgefüllt; die anderen Spalten überlassen Sie dem Käufer.

```text
| Criterion                         | Exponential 6.0.x                                   | Option B | Option C | How verified |
|-----------------------------------|-----------------------------------------------------|----------|----------|--------------|
| License of the code               | GPL v2 or later, free of charge                     |          |          | LICENSE      |
| License of the documentation      | GNU FDL 1.3 or later                                |          |          | doc/eng-us/README-LICENSE.md |
| Source and full history public    | Yes, since 2002                                     |          |          | git log      |
| Self-hosting                      | Yes, any OS with PHP 8.0+ (tiers)                   |          |          | os-support   |
| Databases                         | SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL |          |          | database-support |
| Web servers                       | Velocity, Apache, nginx, FrankenPHP and others      |          |          | os-support   |
| PHP versions                      | 8.0 to 8.5                                          |          |          | composer.json |
| Published releases immutable      | Yes                                                 |          |          | SECURITY.md  |
| Security reporting                | Private, first answer within one business day       |          |          | SECURITY.md  |
| Multisite from one installation   | Yes (siteaccesses)                                  |          |          | README.md    |
| Content languages                 | 368 locale files; at most 62 per installation       |          |          | doc/eng-us/languages.md |
| Administration translations       | all complete (totals: languages page)               |          |          | doc/eng-us/languages.md |
| Visual page builder               | Exponential Layouts                                 |          |          | layouts-editor |
| Audit trail                       | Tamper-evident, on by default                       |          |          | audit-trail  |
| Writing assistant                 | Drafts only, region per provider                    |          |          | expai        |
| Migration paths documented        | From 3.10/4.x, 5.x, eZ Platform and Ibexa           |          |          | install/14-16 |
```

## Wo Exponential nicht die richtige Wahl ist

Sprechen Sie diese Punkte selbst an. Jeder ist dokumentiert.

| Situation | Die ehrliche Antwort | Quelle |
|---|---|---|
| Der Kunde muss Microsoft SQL Server einsetzen | In 6.0.x gibt es keinen Treiber; es „ist bekanntermaßen mit zusätzlichem Aufwand möglich“ | [Unterstützte Datenbanken](../datenbankunterstuetzung.md#microsoft-sql-server-möglich-mit-zusätzlichem-aufwand) |
| Ein reines Symfony-Projekt ohne den Legacy-Kernel | Dafür ist Exponential Platform DXP das passende Mitglied der Produktfamilie, nicht Exponential 6 | [Welches Produkt zu Ihnen passt](../wer-ist-die-exponential-foundation.md#welches-produkt-zu-ihnen-passt) |
| Mehrere Replikate, die sich eine SQLite-Datei teilen | Nicht möglich; mehrere Replikate brauchen einen Datenbankserver, vorrangig PostgreSQL | [Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md#container-cloud-und-hosting-panels) |
| Shared Hosting, das lang laufende Prozesse verbietet | Velocity kann dort meist nicht laufen; Apache oder LiteSpeed mit `.htaccess` liefern die Site trotzdem aus | `README.md`, „When a traditional server is still the right answer“ |
| Strikte Trennung mehrerer Kunden auf einem Server | Betreiben Sie je Site eine eigene Velocity-Instanz unter einem eigenen Systembenutzer | ebenda |
| Mehr als 62 Inhaltssprachen in einer Installation | Die Grenze liegt bei 62 (30 unter 32-Bit-PHP) | [Sprachen in Exponential](../../sprachen.md#11-die-grenze-von-62-inhaltssprachen) |
| Eine vollständig übersetzte Administration in einer Sprache, die heute nicht vollständig ist | Jede mitgelieferte Übersetzung der Administration ist vollständig; eine Sprache ohne eigene zeigt Englisch (die aktuellen Gesamtzahlen stehen am Anfang der Seite Sprachen) | [Unterstützungsstufen](../../sprachen.md#2-unterstützungsstufen) |
| Die Anforderung, MariaDB oder Oracle in der CI zu testen | Beide werden unterstützt, keine von beiden läuft in der CI des Projekts | [Testabdeckung](../datenbankunterstuetzung.md#testabdeckung-ci) |
| Die Forderung nach einer veröffentlichten Support-Richtlinie oder einem SLA-Dokument | Das Projekt veröffentlicht keine einzelne Support-Richtlinie; die Zusagen stehen an den Stellen, die auf der Seite der Foundation aufgeführt sind; Vertragsbedingungen kommen von einem Dienstleister | [Die Support-Zusage](../wer-ist-die-exponential-foundation.md#die-zusage-zur-unterstützung) |

## Über verwandte Projekte sprechen

Manche Käufer kennen die Geschichte. Wählen Sie den Ton der Seiten zur Geschichte: sachlich, belegt, nie abwertend.

- **Die Codebasis.** Exponential führt die Codebasis von eZ Publish fort, einem Produkt, das sein Hersteller nach fast
  18 Jahren eingestellt hat ([Wer ist 7x?, Die Jahre von eZ Publish](../wer-ist-7x.md#die-jahre-von-ez-publish-1999-bis-2021)).
- **Keine Verbindung.** „Dieses Projekt steht in keiner Verbindung zur ursprünglichen Software eZ Publish oder zu ihrem
  ursprünglichen Entwickler, eZ Systems oder Ibexa“ (`README.md`, „Exponential Project Notice : 2025.08.12“).
- **Anerkennung, wo sie hingehört.** Exponential Layouts bringt das Konzept von Netgen Layouts in den Kernel; die
  LoveStack-Verbesserungen von Mugo Web wurden „mit voller Anerkennung für Mugo Web“ neu umgesetzt; `cjw_newsletter`
  stammt vom CJW Network ([Wer ist 7x?, Mitwirkende und Freunde des Projekts](../wer-ist-7x.md#mitwirkende-und-freunde-des-projekts)).
- **Kunden auf eZ Platform oder Ibexa.** Streiten Sie nicht über ihre aktuelle Plattform. Verweisen Sie auf die
  dokumentierten Migrationswege: [Kapitel 16, Migration von eZ Platform und Ibexa](../../installation/16-migration-von-ez-platform-und-ibexa.md),
  und für ältere Sites [Kapitel 14](../../installation/14-migration-von-4x.md) und
  [Kapitel 15](../../installation/15-migration-von-5x-legacy.md).

## Formulierungen: so und nicht so

| Sagen Sie nicht | Sagen Sie stattdessen | Warum |
|---|---|---|
| „Wir sind besser als X.“ | „Das leistet Exponential, und so können Sie es prüfen.“ | Keine Quelle kann eine Rangfolge belegen. |
| „X bindet Sie an sich.“ | „Mit Exponential behalten Sie den Code unter der GPL und Ihre Inhalte in einer Standarddatenbank.“ | Beschreiben Sie Exponential, nicht andere. |
| „Es ist das schnellste CMS.“ | „Die README dokumentiert 0,36 ms je gecachter Seite auf einer Maschine mit 12 Kernen, reproduzierbar mit mitgelieferten Skripten; messen wir das auf Ihrer Hardware.“ | Zahlen brauchen ihren Kontext ([Benchmarks](../../anleitungen/benchmarking.md)). |
| „Es unterstützt jede Sprache.“ | „368 Locale-Dateien für Inhalte; Übersetzungen der Administration (die aktuellen Gesamtzahlen stehen am Anfang der Seite [Sprachen in Exponential](../../sprachen.md)).“ | Genau und belegt. |
| „Es ist das neue eZ Publish.“ | „Exponential führt die Codebasis von eZ Publish fort, einem Produkt, das sein Hersteller nach fast 18 Jahren eingestellt hat.“ | Das Produkt heißt Exponential. |
| „Wir garantieren Support.“ | „Sicherheitsmeldungen erhalten innerhalb eines Werktags eine erste Antwort (SECURITY.md); die Bedingungen für Dienstleistungen legt Ihr Anbieter fest.“ | Nur was schriftlich festgehalten ist. |
| „Hunderte Unternehmen setzen es ein.“ | „Die Quellen zählen keine Anwender. Die Historie zeigt über die Jahre Hunderte von Mitwirkenden.“ | [Mitwirkende](../wer-ist-7x.md#mitwirkende-und-freunde-des-projekts) |

## Ein ausgearbeitetes Beispiel: eine Ausschreibungsfrage beantworten

**Die Frage in der Ausschreibung:** „Beschreiben Sie, wie das angebotene CMS eine Herstellerbindung vermeidet und wie
der Auftraggeber den Lieferanten wechseln könnte.“

**Eine belegte Antwort:**

```text
Exponential is licensed under the GNU General Public License, version 2 or any later version, and is free of
charge (LICENSE, README.md). The complete source code and its history since 2002 are public on GitHub
(github.com/se7enxweb/exponential). The authority can therefore use, modify and redistribute the software
without a license contract, and any supplier may provide services for it.

Content is stored in a standard database chosen by the authority: SQLite 3, PostgreSQL, MongoDB, MariaDB,
Oracle or MySQL (doc/eng-us/help/database-support.md), and is backed up with that database's own tools. Documented
tools move a site from one database engine to another.

Releases are published as Composer packages and Git tags; a published version is never changed afterwards
(SECURITY.md). The documentation is part of the repository and is free documentation under the GNU Free
Documentation License 1.3 (doc/eng-us/README-LICENSE.md), so it can be handed to a new supplier unchanged.
```

Jeder Satz dieser Antwort verweist auf eine Datei, die der Prüfer öffnen kann. Das ist der Maßstab für jeden Vergleich.

## Verwandte Seiten

- [Positionierung und Nutzenversprechen](positionierung-und-nutzenversprechen.md)
- [Funktionshighlights, den Bedürfnissen der Kunden zugeordnet](funktionshighlights-fuer-kunden.md)
- [Einwandbehandlung: Sicherheit, Langlebigkeit und Support](einwandbehandlung.md)
- [Lizenzen für Käufer erklärt: GPL-Code, GFDL-Dokumentation](lizenzen-fuer-kaeufer.md)
- [Regeln für Marke und Namen](marken-und-namensregeln.md)
- [Hosting, Datenbank und Betriebssystem auswählen](../fuehrung/hosting-datenbank-und-betriebssystem-waehlen.md)
- [Wer ist 7x?](../wer-ist-7x.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
