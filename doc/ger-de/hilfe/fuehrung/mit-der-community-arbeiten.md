---
title: Mit der Community arbeiten und etwas zurückgeben
audience: project-manager, developer
category: leadership
order: 120
summary: Wo sich die Exponential-Community trifft, welcher Tracker welche Meldung annimmt, wie eine Korrektur oder eine Dokumentationsseite zurück in das Projekt gelangt und wie ein Projektteam Beiträge plant und budgetiert.
keywords: Community, beitragen, Beitrag, Pull-Request, Issue, Tracker, Fehlermeldung, Sicherheit, Offenlegung, Forum, Upstream, Fork, GPL, GFDL, Übersetzung, Dokumentation, Sponsor, Foundation, Open-Source-Richtlinie, community, contribute, contribution, pull request, bug report, security, disclosure, forum, translation, documentation, open source policy
type: help
---
# Mit der Community arbeiten und etwas zurückgeben

Diese Seite richtet sich an Projektleiter und Entwickler, deren Projekt Exponential verwendet und die möchten, dass
ihr Projekt **mit** der Community arbeitet statt neben ihr her. Sie beantwortet folgende Fragen: Wo trifft man sich,
wohin gehört eine Fehlermeldung, wie gelangt eine Korrektur in das nächste Release, was darf Ihr Team beitragen, und
wie planen Sie diese Arbeit in ein Projekt ein?

Exponential ist ein Open-Source-Projekt. Es wird von der Exponential Foundation betrieben, einer gemeinnützigen
Organisation, und 7x ist sein wichtigster Mitwirkender ([Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md)).
Exponential führt die Codebasis von eZ Publish fort, einem Produkt, das sein Hersteller nach fast 18 Jahren eingestellt
hat; die Geschichte steht unter [Wer ist 7x?](../wer-ist-7x.md).

## Inhalt

1. [Warum sich Beiträge für ein Projekt auszahlen](#warum-sich-beiträge-für-ein-projekt-auszahlen)
2. [Wo sich die Community trifft](#wo-sich-die-community-trifft)
3. [Welcher Tracker welche Meldung annimmt](#welcher-tracker-welche-meldung-annimmt)
4. [Sicherheitsprobleme: vertraulich, nie öffentlich](#sicherheitsprobleme-vertraulich-nie-öffentlich)
5. [Eine Korrektur beitragen: der Weg über den Pull-Request](#eine-korrektur-beitragen-der-weg-über-den-pull-request)
6. [Wie das Projekt mit Pull-Requests umgeht](#wie-das-projekt-mit-pull-requests-umgeht)
7. [Dokumentation und Übersetzungen beitragen](#dokumentation-und-übersetzungen-beitragen)
8. [Lizenzierung Ihrer Beiträge](#lizenzierung-ihrer-beiträge)
9. [Beiträge in ein Projekt einplanen](#beiträge-in-ein-projekt-einplanen)
10. [Eine Beitragsrichtlinie für Ihr Team zum Anpassen](#eine-beitragsrichtlinie-für-ihr-team-zum-anpassen)
11. [Das Projekt finanziell unterstützen](#das-projekt-finanziell-unterstützen)

## Warum sich Beiträge für ein Projekt auszahlen

Die Hilfeseite [Mit Git entwickeln](../mit-git-entwickeln.md#eine-änderung-per-pull-request-beitragen) sagt es ohne
Umschweife: Schicken Sie die Korrektur zurück, „damit sie im nächsten Release enthalten ist und Sie sie nicht selbst
mitschleppen müssen“. Für einen Projektleiter lassen sich die Gründe in Kosten und Risiken übersetzen:

| Ohne Beiträge | Mit Beiträgen |
|---|---|
| Jeder lokale Patch muss bei jedem Upgrade erneut angewendet und getestet werden | Die Korrektur kommt mit dem nächsten Release; das Upgrade bringt sie mit |
| Das Wissen über die Korrektur bleibt in einem Team | Die Korrektur wird geprüft und in der öffentlichen Historie dokumentiert |
| Ein Fehler, den Ihre Redakteure gefunden haben, kann Sie anderswo erneut treffen | Jede Site, auch Ihr nächstes Projekt, erhält die Korrektur |
| Ihr Team arbeitet um das Produkt herum | Ihr Team gestaltet es mit |

## Wo sich die Community trifft

Aus [Orte der Community](../wer-ist-die-exponential-foundation.md#orte-der-community) und
[Wer ist 7x?, „Die Community“](../wer-ist-7x.md#die-community):

| Ort | Wofür |
|---|---|
| [share.exponential.earth](https://share.exponential.earth/) | Das Community-Portal: Foren, Blogs, Tutorials und Projekte (Erweiterungen) |
| [Die Foren](https://share.exponential.earth/forums) | Fragen und Diskussionen („Wie mache ich ...?“) |
| Telegram | Der Support-Chat der Community; die Adresse steht unter [Orte der Community](../wer-ist-die-exponential-foundation.md#orte-der-community) |
| [eZpedia](https://ezpedia.exponential.earth/) | Die freie Enzyklopädie der früheren Jahre, seit 2006, unter der GNU FDL |
| [Dokumentation](https://doc.exponential.earth/) | „The Official Documentation For Exponential 6 CMS“ |
| [Spiegel der Schulungsbücher](https://github.com/se7enxweb/mirror.exponential.se7enx.com) | Drei ursprüngliche Schulungsbücher für Entwickler als PDF |
| [Issue-Tracker der Community](https://github.com/se7enxweb/exponential-community/issues) | Das Projekt, die Community, das Ökosystem und die Websites |
| Discord und Slack | Unter den Support-Kanälen auf der Website des Projekts genannt |

> **Hinweis**
> Die für diese Hilfe verwendeten Quellen dokumentieren keine Veranstaltungen (Konferenzen, Meetups). Wenn Ihre
> Organisation eine ausrichten möchte, fragen Sie in den Foren oder im Tracker der Community.

Praktische Ratschläge für ein Projektteam:

- **Suchen Sie zuerst.** Die Foren, die Tracker und diese Hilfe kennen die Antwort vielleicht schon.
- **Fragen Sie öffentlich.** Eine Antwort im Forum hilft der nächsten Person; eine private Nachricht hilft einer einzigen.
- **Teilen Sie, was Sie gelernt haben.** Ein Tutorial im Portal oder eine Seite dieser Hilfe (siehe unten) gibt die
  Zeit zurück, die andere Ihnen erspart haben.

## Welcher Tracker welche Meldung annimmt

Aus [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md#einen-fehler-in-exponential-melden):

| Tracker | Für |
|---|---|
| [Exponential (die Software)](https://github.com/se7enxweb/exponential/issues) | Fehler im Kernel, in der Administration, in den mitgelieferten Erweiterungen und in der Dokumentation |
| [Exponential Velocity](https://github.com/se7enxweb/exponential-velocity/issues) | Der Anwendungsserver Velocity: Start, Worker, sein Antwort-Cache, HTTPS |
| Das eigene Repository der Erweiterung | Fehler in einer Erweiterung, die ein eigenes Repository hat |
| [Exponential-Projekt und -Community](https://github.com/se7enxweb/exponential-community/issues) | Das Projekt, die Website, das Ökosystem und alles, was kein Fehler in der Software ist |

Die Seite **Fehler melden** der Hilfe (`/exphelp/report` in der Administration) führt die meldende Person: Sie sagt,
welcher Tracker zu verwenden ist, füllt eine Zusammenfassung der Installation aus und öffnet ein neues Issue mit
vorbereitetem Titel und Text ([Hilfesystem](../../funktionen/6.0/hilfesystem.md)). Die Seite listet auch die eigenen
Tracker der Erweiterungen auf.

### Was eine Meldung nützlich macht

Aus [Eine gute Fehlerbeschreibung](../wenn-etwas-schiefgeht.md#eine-gute-fehlerbeschreibung):

- ein kurzer Titel, der das Problem benennt;
- was Sie getan haben, Schritt für Schritt;
- was Sie erwartet haben und was stattdessen geschah, mit der genauen Meldung;
- die Umgebung: Exponential-Version, PHP-Version, Webserver, Datenbank, Design der Administration
  (**Setup > Systeminformationen > Bericht als Text kopieren** liefert diese Zusammenfassung ohne Passwörter,
  Schlüssel oder Serverpfade);
- Logzeilen aus dem betreffenden Zeitraum, aus denen alles Private entfernt ist.

Schreiben Sie nie Passwörter, Schlüssel, Sitzungs-Cookies, Datenbankadressen oder personenbezogene Daten von Kunden
oder Benutzern in ein öffentliches Issue.

### Eine Routine zur Bewertung für Ihr Team

1. Ein Redakteur stößt auf einen Fehler und gibt die **Fehlerreferenz** (`ERR-...`), die Uhrzeit, die Adresse und die
   Schritte an den Administrator weiter.
2. Der Administrator findet die Anfrage in den Logs
   ([Einen Fehler anhand seiner Referenz finden](../../anleitungen/fehler-anhand-der-referenz-finden.md)) und entscheidet:
   die Einrichtung unserer Site, unser eigener Code oder Exponential selbst.
3. Unsere Einrichtung oder unser Code: im Projekt beheben. Exponential selbst: im Tracker suchen und den Fehler dann
   über die Seite **Fehler melden** melden.
4. Der Projektleiter verknüpft das öffentliche Issue mit dem eigenen Ticket des Projekts, damit das Upgrade, das die
   Korrektur enthält, es schließen kann.

## Sicherheitsprobleme: vertraulich, nie öffentlich

Ein Sicherheitsproblem wird **nie** in einem öffentlichen Tracker gemeldet. Melden Sie es vertraulich, wie
[SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) es beschreibt: an die dort genannte
Sicherheitsadresse oder über die private Meldefunktion für Sicherheitslücken von GitHub im Repository
([Sicherheitshärtung, „Eine Sicherheitslücke melden“](../sicherheitshaertung.md#eine-schwachstelle-melden)).

Wozu sich SECURITY.md verpflichtet und worum es bittet:

- „Eine erste Antwort können Sie innerhalb eines Werktags erwarten.“
- Sie erfahren, ob die Meldung angenommen oder abgelehnt wird, und werden auf dem Laufenden gehalten, während eine
  Korrektur vorbereitet wird.
- Ein angenommenes Problem wird auf `main` behoben und mit der nächsten 6.0.x-Version veröffentlicht.
- Geben Sie die Version, die PHP-Version, die Engine, die die Site ausliefert, und die Datenbank an, was ein Angreifer
  tun kann und die Schritte zum Nachvollziehen. Lassen Sie Passwörter, Geheimnisse und personenbezogene Daten weg.

Nehmen Sie das in das Verfahren Ihres Teams für Sicherheitsvorfälle auf und stellen Sie sicher, dass alle wissen:
„Eine Lücke gefunden“ heißt „vertrauliche Meldung“, nicht „Issue auf GitHub“ oder „Beitrag im Chat“.

## Eine Korrektur beitragen: der Weg über den Pull-Request

Das Verfahren Schritt für Schritt steht in [Mit Git entwickeln](../mit-git-entwickeln.md#eine-änderung-per-pull-request-beitragen)
und in [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md). Kurz gesagt:

1. **Forken** Sie das Repository auf GitHub (den Kernel oder die Erweiterung).
2. **Legen Sie** in Ihrem Klon **einen Branch an**: `git switch -c fix-<what>`.
3. **Ändern und testen** Sie auf Ihrer Site.
4. **Committen** Sie mit einem Betreff, der mit dem beginnt, was die Änderung tut: `Added:`, `Updated:`, `Fixed:`,
   `Removed:` oder `Renamed:`.
5. **Pushen** Sie in Ihren Fork und **öffnen Sie einen Pull-Request**; beschreiben Sie, was Sie korrigiert und wie Sie
   es getestet haben. Erwähnen Sie das Issue (`Fixes #123`), damit beide verknüpft sind.

Für den Kernel ergänzt CONTRIBUTING.md:

- Einmal `make hooks` in Ihrem Klon: Der Commit-Hook prüft Syntax, Dateimanifest und Codestil.
- Jeder Commit, der eine Datei hinzufügt, ändert oder entfernt, aktualisiert ihre Zeile in `share/filelist.md5`
  (`make manifest-fix`).
- Templates und Stylesheets der Administration folgen [Die Administration gestalten](../../bc/6.0/admin4-gestaltung.md):
  kein Inline-CSS, gemeinsame Komponenten und Tokens.
- Halten Sie einen Pull-Request fokussiert; mehrere Beiträge sind mehrere Pull-Requests.

> **Tipp**
> Bitten Sie Ihre Entwickler, die Site von Anfang an als Git-Klone zu installieren ([Mit Git entwickeln](../mit-git-entwickeln.md)).
> Eine Korrektur ist dann nur „einen Branch in Ihrem Fork und einen Pull-Request entfernt“, und der Aufwand für einen
> Beitrag sinkt auf wenige Minuten.

## Wie das Projekt mit Pull-Requests umgeht

Aus [Mitwirkende und Pull-Requests](../wer-ist-die-exponential-foundation.md#mitwirkende-und-pull-requests) und
[Kontinuierliche Integration](../../bc/6.0/ci.md):

- **Jeder Pull-Request wird gemergt.** „Jeder Pull-Request wird gemergt, und seine Commits bleiben so erhalten, wie
  der Mitwirkende sie geschrieben hat, und werden nie umgeschrieben.“ Was korrigiert werden muss, wird in Commits
  darüber korrigiert.
- **Die Commit-Nachricht eines Mitwirkenden blockiert nie.** Ein Betreff außerhalb des Formats ist eine Warnung, kein
  Fehlschlag.
- **Die Prüfungen laufen auf dem Merge mit `main`.** Ein Branch, der grün war, kann rot werden, wenn sich `main`
  weiterbewegt; mergen Sie `main` in den Branch und pushen Sie erneut.
- **Der erste Pull-Request neuer Mitwirkender wartet auf die Genehmigung eines Maintainers**, bevor irgendein
  Workflow läuft.
- **Die Arbeit anderer wird gewürdigt.** Das Projekt hat die LoveStack-Verbesserungen von Mugo Web „with full credit to
  Mugo Web“ (unter voller Nennung von Mugo Web) neu aufgebaut, und Exponential Layouts portiert das Konzept von Netgen
  Layouts (README, zitiert auf der Seite der Foundation).

Für einen Projektplan bedeutet das: Ein Beitrag hängt nicht davon ab, eine Diskussion zu gewinnen. Planen Sie die Zeit
ein, ihn gut zu schreiben und zu testen; der Merge selbst ist nicht der unsichere Teil.

## Dokumentation und Übersetzungen beitragen

**Dokumentation.** Jede Hilfeseite hat einen Link *Auf GitHub bearbeiten* zu ihrer Quelldatei in `doc/`. Korrigieren
Sie sie und öffnen Sie einen Pull-Request, genauso wie bei Code. Die Seiten sind freie Dokumentation unter der GNU
Free Documentation License 1.3 oder einer späteren Version, und Beiträge werden unter derselben Lizenz angenommen
([Freie Dokumentation](../freie-dokumentation.md)).

Gute erste Dokumentationsbeiträge für ein Projektteam:

- die Seite, die Sie sich zu Beginn Ihres Projekts gewünscht hätten;
- eine Korrektur, wo eine Seite nicht mit dem übereinstimmte, was Sie gesehen haben;
- ein durchgespieltes Beispiel aus Ihrem Projekt, ohne die Angaben des Kunden.

**Übersetzungen.** Die Administration hat 37 Übersetzungen, 24 davon vollständig (Stand 25. Oktober 2026), und es gibt 268 Locale-Dateien
([Sprachen](../../sprachen.md)). Wie Sie eine Übersetzung der Administration hinzufügen oder vervollständigen, steht
in [Abschnitt 8 von „Sprachen“](../../sprachen.md#8-eine-übersetzung-der-administrationsoberfläche-hinzufügen-oder-vervollständigen). Ein Team,
das eine Site in einer Sprache baut, deren Übersetzung der Administration unvollständig ist, kann sie für alle
vervollständigen.

## Lizenzierung Ihrer Beiträge

- **Code** steht unter GPL v2 oder später. „Mit Ihrem Beitrag erklären Sie sich damit einverstanden, dass Ihre
  Beiträge unter der GNU General Public License, Version 2.0 oder später, lizenziert werden“ (CONTRIBUTING.md).
- **Dokumentation** steht unter GNU FDL 1.3 oder später ([Freie Dokumentation](../freie-dokumentation.md)).

Prüfen Sie, bevor Ihr Team etwas beiträgt:

- Erlaubt Ihr Arbeits- oder Kundenvertrag den Mitarbeitenden, Code zu veröffentlichen, den sie im Projekt geschrieben
  haben? Fragen Sie Ihre Rechtsabteilung; diese Seite ist keine Rechtsberatung.
- Ist der Code frei von kundenspezifischen Geheimnissen, Namen und Daten?
- Dürfen Sie den Code weitergeben? Code, der von anderswo kopiert wurde, behält seine eigene Lizenz.

Mehr zu Lizenzen für Käufer: [Lizenzierung für Käufer erklärt](../geschaeft/lizenzen-fuer-kaeufer.md).

## Beiträge in ein Projekt einplanen

| Phase | Aufgabe für Beiträge | Verantwortlich |
|---|---|---|
| Planung | Die Beitragsrichtlinie festlegen (siehe unten); „Korrekturen an den Upstream“ in die Schätzung aufnehmen | Projektleiter |
| Umsetzung | In Exponential gefundene Fehler melden; Korrekturen als Pull-Requests schicken statt als lokale Patches | Entwickler |
| Inhaltsmigration | Gefundene Lücken in der Dokumentation melden | Wer auch immer sie gefunden hat |
| Abnahme | Jedes Upstream-Issue mit Ihrer eigenen Mängelliste verknüpfen | Projektleiter |
| Livegang | Festhalten, welche Ihrer Korrekturen im Upstream sind und welche noch lokal | Leitender Entwickler |
| Betrieb | Die lokalen Patches bei jedem Upgrade erneut prüfen; die inzwischen veröffentlichten entfernen | Entwickler |

Eine einfache Kennzahl für das Übergabedokument: **Anzahl der noch mitgeführten lokalen Patches**. Weniger ist
besser; jeder Patch, der in den Upstream gegangen ist, muss beim nächsten Upgrade nicht erneut angewendet werden.

## Eine Beitragsrichtlinie für Ihr Team zum Anpassen

```text
Contribution policy: <organization name>, version <date>

1. Scope. Staff may contribute fixes, documentation and translations to Exponential and its extensions
   when the work was done on <projects / time allowed>.
2. Approval. <role> approves the first contribution of each person; after that, no approval is needed for
   fixes and documentation. New features need <role>'s approval.
3. Content. Never contribute customer names, data, secrets, host names or credentials. Remove them from
   code, logs and screenshots.
4. Licenses. Code goes out under GPL v2 or later, documentation under GNU FDL 1.3 or later, as the project
   requires. Only contribute work the organization owns.
5. Security. Security problems are reported privately as SECURITY.md describes, never in public.
6. Accounts. Contributions are made from personal GitHub accounts linked to <organization> / from the
   organization account <name>.
7. Time. <n> hours per sprint / per month are reserved for upstream work.
8. Record. Every upstream issue and pull request is linked from the internal ticket that caused it.
```

## Das Projekt finanziell unterstützen

Die Foundation und 7x haben jeweils eine Sponsorenseite, und die Foundation nimmt über ihre Website außerdem
Abonnements und einmalige Spenden entgegen. Die Adressen stehen unter [Unterstützen oder mitmachen](../wer-ist-die-exponential-foundation.md#unterstützen-oder-mitmachen).
Die Foundation gibt an, dass Spenden in den Vereinigten Staaten steuerlich absetzbar sind; klären Sie das mit Ihrer
eigenen Steuerberatung.

## Verwandte Seiten

- [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md) und [Wer ist 7x?](../wer-ist-7x.md)
- [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md), [Mit Git entwickeln](../mit-git-entwickeln.md)
- [Ein Team mit Git führen](ein-team-mit-git-fuehren.md), [Eine führende Rolle in der Exponential-Community übernehmen](community-leiter-werden.md)
- [Die Community wachsen lassen](../geschaeft/die-community-wachsen-lassen.md), [Die Community und das Ökosystem als Verkaufsargument](../geschaeft/community-und-oekosystem.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
