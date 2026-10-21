---
title: "Zusammenarbeiten: Projektleiter, Vertrieb, Redakteure, Administratoren und Entwickler"
audience: project-manager, sales-marketing, editor, admin, developer
category: leadership
order: 200
summary: Wie die fünf Rollen rund um eine Exponential-Site einander Arbeit übergeben – eine Übergabetabelle, Besprechungsrhythmen, die gemeinsamen Arbeitsunterlagen und welcher Teil der Administration welcher Rolle gehört, mit den Richtlinien dahinter.
keywords: Zusammenarbeit, Übergabe, Rollen, RACI, Besprechungen, Rhythmus, Stand-up, Review, Arbeitsunterlagen, Zuständigkeit, Richtlinien, Projektleiter, Vertrieb, Marketing, Redakteur, Administrator, Entwickler, Team, collaboration, handoff, roles, meetings, cadence, stand-up, review, artefacts, ownership, policies, project manager, sales, marketing, editor, administrator, developer, team
type: help
---
# Zusammenarbeiten: Projektleiter, Vertrieb, Redakteure, Administratoren und Entwickler

Diese Seite ist für alle, die mit anderen zusammen an einer Exponential-Site arbeiten: Projektleiter, Vertrieb und
Marketing, Redakteure, Administratoren und Entwickler. Sie beantwortet vier Fragen. Wer übergibt wem was? Wann treffen
wir uns, und worüber sprechen wir? Welche Dokumente und Aufzeichnungen teilen wir? Und wem gehört welcher Teil der
Administration?

Exponential selbst zieht bereits viele der Grenzen zwischen den Rollen: Was eine Person in der Administration sieht und
tun darf, wird durch **Rollen** entschieden, die aus **Richtlinien** bestehen
([Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md)). Diese Seite baut die
Arbeitsweise des Teams auf diesen Grenzen auf, sodass Prozess und Berechtigungen dasselbe aussagen.

## Inhalt

1. [Die fünf Rollen im Überblick](#die-fünf-rollen-im-überblick)
2. [Die Übergaben](#die-übergaben)
3. [Wem was in der Administration gehört](#wem-was-in-der-administration-gehört)
4. [Gemeinsame Arbeitsunterlagen](#gemeinsame-arbeitsunterlagen)
5. [Besprechungsrhythmen](#besprechungsrhythmen)
6. [Durchgespieltes Beispiel: von einem Vertriebsversprechen zur veröffentlichten Seite](#durchgespieltes-beispiel-von-einem-vertriebsversprechen-zur-veröffentlichten-seite)
7. [Durchgespieltes Beispiel: ein Fehler auf der Live-Site](#durchgespieltes-beispiel-ein-fehler-auf-der-live-site)
8. [Wenn Rollen uneins sind](#wenn-rollen-uneins-sind)
9. [Checklisten je Rolle](#checklisten-je-rolle)

## Die fünf Rollen im Überblick

| Rolle | Arbeitet überwiegend in | Verantwortet | Beginnt mit dem Lesen bei |
|---|---|---|---|
| **Projektleiter** | Dem Plan, dem Issue-Tracker, der Hilfe | Umfang, Zeitplan, Abnahme, Risiken | [Projektleiter: Hier beginnen](projektleiter-hier-beginnen.md) |
| **Vertrieb und Marketing** | Kundengesprächen, Demos, der öffentlichen Darstellung | Was versprochen wird, und dass es stimmt | [Vertrieb und Marketing: Hier beginnen](../geschaeft/vertrieb-und-marketing-hier-beginnen.md) |
| **Redakteur** | **Dashboard**, **Inhaltsstruktur**, **Medien**, **Layouts** | Die Inhalte und ihre Qualität | [Erste Schritte](../erste-schritte.md), [Ein Rundgang durch die Administration](../rundgang-durch-die-administration.md) |
| **Administrator** | **Benutzer**, **Setup**, dem Server | Konten, Zugriff, Betrieb, Sicherheit | [Eine Site betreiben](../../anleitungen/betrieb-einer-website.md), [Sicherheit und Prüfung](../../anleitungen/sicherheit-und-audit.md) |
| **Entwickler** | Git-Klonen, Erweiterungen, Templates, Einstellungen | Den Code, seine Tests und seine Releases | [Mit Git entwickeln](../mit-git-entwickeln.md) |

In einem kleinen Projekt kann eine Person mehrere Rollen innehaben. Das ist in Ordnung; die unten beschriebenen
Übergaben finden trotzdem statt, nur eben in einem einzigen Kopf. Schreiben Sie sie trotzdem auf: Die nächste Person
wird sie brauchen.

> **Hinweis**
> „Rolle“ bedeutet auf dieser Seite eine Aufgabe im Team. In der Administration ist eine **Rolle** außerdem ein
> technisches Objekt, eine Menge von Richtlinien, die Benutzern oder Gruppen zugewiesen wird. Eine neue Installation
> hat unter anderem die Rollen **Administrator** (die jede Richtlinie enthält), **Editor** und **Anonymous**
> ([Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md)). Ordnen Sie Ihre
> Teamrollen früh solchen Rollen zu; [Ein Exponential-Projekt planen](ein-exponential-projekt-planen.md) zeigt, wie.

## Die Übergaben

Eine Übergabe ist der Moment, in dem Arbeit von einer Rolle zu einer anderen wechselt. Die meisten Probleme in
Projekten beginnen bei einer Übergabe, bei der etwas angenommen statt aufgeschrieben wurde. Die Tabelle führt die
Übergaben auf, die in fast jedem Exponential-Projekt vorkommen, mit dem, was mitgegeben werden muss.

| Von | An | Was übergeben wird | Was mitgegeben werden muss | Wo es festgehalten wird |
|---|---|---|---|---|
| Vertrieb | Projektleiter | Ein Kundenbedarf oder ein Versprechen aus einer Demo | Der genaue Wortlaut des Versprechens, die Funktion, auf der es beruht, und die Hilfeseite, die sie beschreibt | Der Issue-Tracker oder der Projektauftrag ([Projektvorlagen](projektvorlagen.md)) |
| Projektleiter | Entwickler | Ein abgegrenztes Arbeitspaket | Der Bedarf, die Abnahmekriterien, die beteiligten Inhaltsklassen und Rollen | Ein Issue; später ein Branch und ein Pull-Request |
| Projektleiter | Redakteur | Eine Inhaltsaufgabe (neuer Bereich, Migration, Übersetzung) | Das Inhaltsmodell, die Frist, wer freigibt | Das Issue und, in der Administration, der Ordner oder die Sektion, zu der sie gehört |
| Redakteur | Projektleiter | „Die Inhalte sind fertig“ | Welche Objekte, in welchen Sprachen, ob sie veröffentlicht sind oder auf Freigabe warten | **Meine Entwürfe** und **Meine Warteliste** ([Meine Entwürfe und meine Warteliste](../../anleitungen/entwuerfe-und-ausstehende-objekte.md)) |
| Entwickler | Administrator | Eine auszurollende Änderung | Der Branch oder Tag, zu ändernde Einstellungen, ein Datenbank-Update, welche Caches zu leeren sind | Die Beschreibung des Pull-Requests und das Changelog ([Ausrollen](../../anleitungen/bereitstellung.md)) |
| Administrator | Redakteur | Ein neues Konto oder ein neuer Zugriff | Der Benutzer, die Gruppe, die Rolle und ihre Einschränkung (Sektion oder Teilbaum) | **Benutzer > Rollen und Richtlinien**; die Änderung wird im Audit-Protokoll festgehalten ([Das Audit-Protokoll](../../../eng-us/features/6.0/audit-trail.md)) |
| Redakteur oder Besucher | Administrator | „Etwas ist schiefgegangen“ | Die Fehlerreferenz (`ERR-...`), die Uhrzeit, die Adresse, die Schritte | [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md) |
| Administrator | Entwickler | Ein Fehler, der im Code liegt, nicht in der Einrichtung | Die Referenz, die Logzeilen, der Bericht mit den Systeminformationen | [Einen Fehler über seine Referenz finden](../../anleitungen/fehler-anhand-der-referenz-finden.md), [Systeminformationen](../../anleitungen/systeminformationen.md) |
| Entwickler | Die Community | Eine Korrektur oder Verbesserung für alle | Ein Pull-Request an das Repository des Projekts | [Mit Git entwickeln](../mit-git-entwickeln.md#eine-änderung-per-pull-request-beitragen) |
| Projektleiter | Vertrieb und Marketing | Ein abgeschlossener Meilenstein, ein Launch, eine Geschichte zum Erzählen | Was geliefert wurde, welche Funktionen genutzt wurden, was der Kunde zur Veröffentlichung freigegeben hat | [Fallstudien und Content-Marketing schreiben](../geschaeft/fallstudien-und-content-marketing.md) |

### Regeln, mit denen Übergaben gelingen

1. **Verlinken statt umschreiben.** Wenn ein Versprechen auf einer Funktion beruht, verlinken Sie die Hilfeseite dieser
   Funktion. Die Hilfe ist die gemeinsame verbindliche Quelle dafür, was das Produkt leistet; jede Seite liegt im
   eigenen Verzeichnis `doc/` der Installation und wird unter `/exphelp/dashboard` angezeigt
   ([Das Hilfesystem](../../../eng-us/features/6.0/help-system.md)).
2. **Rolle und Richtlinie benennen.** „Der Redakteur kann das“ reicht nicht. Sagen Sie, welche Rolle, mit welcher
   Richtlinie und welcher Einschränkung. Wenn niemand das benennen kann, entscheidet der Administrator, bevor die
   Arbeit beginnt.
3. **Ein Verantwortlicher pro Übergabe.** Der Absender ist für die Übergabe verantwortlich, bis der Empfänger sie
   schriftlich bestätigt hat.
4. **Niemals Geheimnisse mitschicken.** Passwörter, Schlüssel und personenbezogene Daten von Kunden oder Benutzern
   gehören nicht in Issues, Chats oder E-Mails. Die Seite **Fehler melden** der Hilfe und **Setup > Systeminformationen**
   erzeugen Zusammenfassungen, die sie auslassen ([Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md)).

## Wem was in der Administration gehört

Die Administration zeigt jeder Person nur das, was ihre Rollen erlauben: „Links in der Administration folgen bereits den
Berechtigungen, sodass ein Benutzer nie einen Link zu etwas sieht, das verweigert würde“
([Sicherheit und Prüfung](../../anleitungen/sicherheit-und-audit.md)). Die folgende Tabelle führt die Stellen auf, die ein
Team üblicherweise zwischen seinen Rollen aufteilt, die Richtlinie, die jede davon öffnet, soweit die Dokumentation sie
nennt, und wer sie in einer **neuen** Installation innehat. Die letzte Spalte ist ein Vorschlag für Ihr Team.

| Stelle in der Administration | Richtlinie | Inhaber in einer neuen Installation | Vorgeschlagener Verantwortlicher im Team | Mehr lesen |
|---|---|---|---|---|
| **Dashboard**, **Inhaltsstruktur**, **Medien** | Richtlinien `content/*` der Rolle | Hängt von der Rolle ab | Redakteure | [Ein Rundgang durch die Administration](../rundgang-durch-die-administration.md) |
| Reiter **Layouts** und der Layout-Editor | Layouts-Richtlinien der Rolle | Hängt von der Rolle ab | Redakteure, zusammen mit einem Designer | [Der Layout-Editor](../layout-editor.md) |
| **Benutzer > Rollen und Richtlinien** | das Modul `role` | Administrator | Administrator | [Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md) |
| **Benutzer > Nicht aktivierte Benutzer** | `user/activation` | Administrator | Administrator | ebenda |
| **Benutzer > Benutzerkonten prüfen** | `user/hygiene` | Administrator | Administrator | [Benutzerkonten prüfen](../../../eng-us/features/6.0/user-hygiene.md) |
| Reiter **Audit** | `audit/read`, `audit/manage` | „In einer neuen Installation hat sie nur Administrator“ | Administrator; `audit/read` für einen Prüfer | [Das Audit-Protokoll](../../../eng-us/features/6.0/audit-trail.md) |
| **Setup > Logdateien** und der Fehlerüberblick | `setup/logs`, `setup/managelogs`, `setup/unmasklogs` | „In einer neuen Installation nur Administrator“ | Administrator | [Der Log-Viewer](../../../eng-us/features/6.0/log-viewer.md) |
| **Setup > Cronjobs** | `setup/managecronjobs` | Administrator | Administrator | [Cronjob-Konsole](../../../eng-us/features/6.0/cronjobs-console.md) |
| **Setup > Systeminformationen** | `setup/system_info` | Administrator | Administrator; mit Entwicklern als Bericht geteilt | [Systeminformationen](../../anleitungen/systeminformationen.md) |
| **Setup > Upgrade-Prüfung** | (Seite der Administration `/setup/systemupgrade`) | Administrator | Administrator, vor einem Upgrade zusammen mit dem Entwickler | [Die Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md) |
| **Setup > Inhaltsfinder** | `contentfinder/search` und weitere | „Keine Rolle außer Administrator“ | Administrator; kann erfahrenen Redakteuren gegeben werden | [Inhaltsfinder](../../../eng-us/features/6.0/content-finder.md) |
| Wartungsmodus | (Seite der Administration und `bin/php/maintenance.php`) | Administrator | Administrator, auf das Signal des Projektleiters | [Wartungsmodus](../../anleitungen/wartungsmodus.md) |
| Der Reiter **Intelligence** (Exponential Intelligence) | `expai/write`, `expai/vector` und weitere | „in einer neuen Installation nur von der Rolle Administrator gehalten“ | Redakteure, sobald das Projekt entscheidet, es zu nutzen | [Exponential Intelligence](../../../eng-us/features/6.0/extensions/expai.md) |
| Die Hilfe (`/exphelp/dashboard`) | `exphelp/read` | Administrator und Editor | Alle im Team | [Das Hilfesystem](../../../eng-us/features/6.0/help-system.md) |
| Code, Templates, Einstellungsdateien | (außerhalb der Administration: Git) | entfällt | Entwickler | [Mit Git entwickeln](../mit-git-entwickeln.md) |

Quellen für die zitierten Inhaber: die in der letzten Spalte verlinkten Seiten und der Abschnitt „Exponential
Intelligence“ des README (`README.md`).

> **Tipp**
> Geben Sie Projektleitern und dem Vertrieb ein Konto mit einer **Lese**-Rolle, die `exphelp/read` enthält, und dazu
> `audit/read`, wenn sie über Aktivitäten berichten ([Ein Projekt messen](kennzahlen-aus-logs-und-audit.md)). Sie können
> die Arbeit dann im echten System verfolgen statt auf Bildschirmfotos. Legen Sie dafür eine neue Rolle an; „bearbeiten
> Sie niemals die Rolle Administrator, um einem Kollegen etwas mehr zu geben; legen Sie eine neue Rolle an“
> ([Sicherheit und Prüfung](../../anleitungen/sicherheit-und-audit.md)).

### Eine Stelle zu verantworten bedeutet

- Sie entscheiden, **wer sonst noch** die Richtlinie dafür bekommt, und bitten den Administrator, sie zuzuweisen.
- Sie **beobachten** sie: Der Redakteur beobachtet **Meine Warteliste**, der Administrator den Fehlerüberblick und die
  Warnungen des Audit-Protokolls, der Entwickler die CI-Prüfungen der Pull-Requests.
- Sie sind die Person, die andere zuerst fragen. Schreiben Sie Ihren Namen neben jede Zeile in der RACI-Matrix Ihres
  Projekts ([Projektvorlagen](projektvorlagen.md)).

## Gemeinsame Arbeitsunterlagen

Dies sind die Dokumente und Aufzeichnungen, die das ganze Team teilt. Die meisten davon gibt es bereits im Produkt
oder im Repository; das Team muss sich nur darauf einigen, sie zu nutzen.

| Arbeitsunterlage | Was sie ist | Wo sie liegt | Verantwortlich | Gelesen von |
|---|---|---|---|---|
| Das Inhaltsmodell | Die Inhaltsklassen, ihre Attribute, Sektionen und Objektzustände | In der Administration (**Setup > Klassen**, Sektionen); beschrieben in einem Workshop-Dokument | Projektleiter mit einem Entwickler | Alle |
| Die Rollenmatrix | Welche Teamrolle welche Rolle der Administration, Richtlinie und Einschränkung erhält | Eine Tabelle im Projektauftrag; die echten Rollen unter **Benutzer > Rollen und Richtlinien** | Administrator | Projektleiter, Redakteure |
| Die Hilfe | Die eigene Dokumentation des Produkts, frei zu kopieren und anzupassen | `doc/` der Installation, unter `/exphelp/dashboard` | Das Projekt (alle dürfen sie verbessern) | Alle |
| Der Issue-Tracker | Fehler, Aufgaben und Stories | Der Tracker Ihres Projekts; Fehler in der Software in den öffentlichen Trackern ([Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md)) | Projektleiter | Alle |
| Branches und Pull-Requests | Jede Codeänderung, begutachtet | Git; das Repository des Projekts auf GitHub | Entwickler | Projektleiter, Administrator |
| Das Changelog | Was sich in jedem Release geändert hat | `doc/changelogs/` ([Changelogs](../../../changelogs/README.md)); ein eigenes für Ihre Site | Entwickler | Alle |
| Workflows | Freigabe vor dem Veröffentlichen und andere Prozesse | **Setup > Workflows** ([Workflows](../../anleitungen/workflows.md)) | Administrator, nach Entscheidung des Projektleiters | Redakteure |
| Layouts und Regeln | Welche Seite welche Anordnung von Blöcken erhält | Reiter **Layouts** ([Der Layout-Editor](../layout-editor.md)) | Redakteure | Projektleiter, Vertrieb (für Demos) |
| Das Audit-Protokoll | Wer was getan hat, manipulationssicher | Reiter **Audit** ([Das Audit-Protokoll](../../../eng-us/features/6.0/audit-trail.md)) | Administrator | Prüfer, Projektleiter |
| Bericht mit den Systeminformationen | Worauf die Site läuft, ohne Geheimnisse | **Setup > Systeminformationen**, *Bericht als Text kopieren* | Administrator | Entwickler, Support |
| Bericht der Upgrade-Prüfung | Konsistenz von Dateien und Datenbank vor einem Upgrade | **Setup > Upgrade-Prüfung**, herunterladbar | Administrator | Entwickler, Projektleiter |

> **Warnung**
> Einstellungen mit Geheimnissen (Datenbankpasswörter, Schlüssel von Anbietern) liegen in `settings/override/`. Sie
> werden nie in ein Issue, einen Chat oder ein gemeinsames Dokument eingefügt. Das README von Exponential Intelligence
> sagt dasselbe über seinen Anbieterschlüssel: „niemals in eine committete Datei“.

## Besprechungsrhythmen

Das Produkt schreibt keine Besprechungen vor; dies sind Vorschläge, die zu den oben genannten Übergaben passen. Halten
Sie jede Besprechung kurz und beenden Sie sie mit schriftlichen Entscheidungen im Tracker.

| Besprechung | Wann | Wer | Tagesordnung | Input aus dem Produkt |
|---|---|---|---|---|
| **Tägliche Abstimmung** (10 Minuten) | Jeden Arbeitstag während Aufbau und Launch | Projektleiter, Entwickler, leitender Redakteur | Blockaden, heute fällige Übergaben | Offene Pull-Requests und ihr CI-Status ([Continuous Integration](../../../eng-us/bc/6.0/ci.md)) |
| **Inhalts-Stand-up** (15 Minuten) | Zweimal pro Woche während der Inhaltserfassung | Projektleiter, Redakteure | Was als Entwurf vorliegt, was wartet, was veröffentlicht ist | **Meine Entwürfe**, **Meine Warteliste**, die Zahlen im Dashboard ([Ein Rundgang durch die Administration](../rundgang-durch-die-administration.md#das-dashboard)) |
| **Wöchentliche Betriebsbesprechung** (30 Minuten) | Wöchentlich, ab dem Launch | Administrator, Projektleiter, ein Entwickler | Fehler, Sicherheitsereignisse, Cronjobs, Backups | Der Fehlerüberblick ([Der Log-Viewer](../../../eng-us/features/6.0/log-viewer.md)), Warnungen des Audit-Protokolls, die wöchentliche Checkliste aus [Eine Site betreiben](../../anleitungen/betrieb-einer-website.md) |
| **Sprint- oder Meilenstein-Review** (60 Minuten) | Alle zwei bis vier Wochen | Alle, einschließlich Vertrieb | Was geliefert wurde, gezeigt in der echten Administration | Eine Demo auf Ihrer eigenen Installation ([Demo-Drehbücher](../geschaeft/demo-drehbuecher.md)) |
| **Release-Planung** (45 Minuten) | Wenn ein neues Release Exponential 6.0.x oder ein Release einer Erweiterung erscheint | Projektleiter, Administrator, Entwickler | Upgrade ja oder nein, wann, mit welchen Prüfungen | Das Changelog, die BC-Hinweise, die Upgrade-Prüfung ([Release- und Upgrade-Planung](release-und-upgrade-planung.md)) |
| **Pipeline-Abgleich** (30 Minuten) | Alle zwei Wochen | Vertrieb und Marketing, Projektleiter | Laufende Versprechen, Demo-Anfragen, Geschichten zum Erzählen | [Positionierung und Nutzenversprechen](../geschaeft/positionierung-und-nutzenversprechen.md) |
| **Community-Stunde** (60 Minuten) | Monatlich | Alle, die möchten | Zu meldende Fehler, zurückzugebende Korrekturen, zu verbessernde Hilfeseiten | [Eine führende Rolle in der Exponential-Community übernehmen](community-leiter-werden.md) |

### Eine Vorlage für Besprechungsnotizen

```text
Meeting:      <name>            Date: <yyyy-mm-dd>
Present:      <roles, not personal data you do not need>
Decisions:
  1. <decision>  - owner: <role>  - due: <date>  - recorded in: <issue link>
Handoffs agreed:
  - <from role> -> <to role>: <what>, with <what must travel along>
Open questions:
  - <question>  - who answers: <role>
Product pages referred to:
  - <help page or administration page>
```

## Durchgespieltes Beispiel: von einem Vertriebsversprechen zur veröffentlichten Seite

Ein Interessent fragt während einer Demo: „Können unsere Redakteure Kampagnenseiten selbst bauen, ohne Entwickler?“

1. **Der Vertrieb** antwortet nur, was die Hilfe sagt: Mit Exponential Layouts ordnen Redakteure Blöcke in Zonen an,
   und Regeln entscheiden, welche Seiten ein Layout erhalten, „sodass Redakteure Seiten umgestalten, ohne Templates
   anzufassen“ (`README.md`, „Exponential Layouts“). Der Vertrieb notiert das Versprechen und verlinkt
   [Der Layout-Editor](../layout-editor.md).
2. **Der Vertrieb übergibt** an den Projektleiter: das Versprechen in den Worten des Kunden, den Link und die Frage,
   die der Kunde eigentlich hat (Kampagnenseiten je Zeitraum).
3. **Der Projektleiter** prüft die Regelbedingungen im README („Siteaccess, Inhaltsklasse, Query-Parameter, Zeit“) und
   schreibt ein Issue: „Kampagnenlayout über eine Zeitregel“, mit Abnahmekriterien.
4. **Der Entwickler** prüft, dass die Layouts-Erweiterungen in der Installation des Projekts aktiv sind (sie werden in
   `composer.json` vorausgesetzt, aber jede Site schaltet sie selbst ein, siehe `README.md`, „How to enable it“), fügt
   jeden Blocktyp hinzu, den das Design braucht, und eröffnet einen Pull-Request.
5. **Der Administrator** rollt aus und gibt den Kampagnenredakteuren eine Rolle mit den Layouts-Richtlinien, die sie
   brauchen.
6. **Der Redakteur** baut das Layout als Entwurf, sieht es sich in der Vorschau an und veröffentlicht es
   ([Ein Rundgang durch die Administration](../rundgang-durch-die-administration.md#in-der-praxis-der-layout-editor)).
7. **Der Projektleiter** nimmt es anhand der Kriterien ab und teilt dem Vertrieb mit, dass es geliefert ist; das
   **Marketing** fragt den Kunden, ob es als Geschichte erzählt werden darf.

## Durchgespieltes Beispiel: ein Fehler auf der Live-Site

1. Ein **Redakteur** erhält *Something went wrong on our side* mit einer Referenz `ERR-...`. Er notiert die Referenz,
   die Uhrzeit, die Adresse und die Schritte und informiert den Administrator
   ([Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md)).
2. Der **Administrator** findet die Anfrage über ihre Referenz
   ([Einen Fehler über seine Referenz finden](../../anleitungen/fehler-anhand-der-referenz-finden.md)) und liest das Log
   rund um sie unter **Setup > Logdateien**.
3. Liegt es an der Einrichtung der Site selbst, behebt der Administrator es und informiert den Redakteur. Liegt es an
   der Software, übergibt der Administrator es mit den Logzeilen und dem Bericht mit den Systeminformationen an einen
   **Entwickler**.
4. Der **Entwickler** meldet es im richtigen öffentlichen Tracker oder schickt die Korrektur als Pull-Request; die
   Seite **Fehler melden** der Hilfe (`/exphelp/report`) bereitet das Issue vor.
5. Der **Projektleiter** hält den Vorfall, die Zeit bis zur Lösung und die Nacharbeit in der Betriebsbesprechung fest.
6. Handelt es sich um ein **Sicherheitsproblem**, meldet es niemand öffentlich: Es geht vertraulich an die Adresse in
   `SECURITY.md`.

## Wenn Rollen uneins sind

| Meinungsverschiedenheit | Wer entscheidet | Auf welcher Grundlage |
|---|---|---|
| Der Vertrieb hat etwas versprochen, das das Produkt nicht dokumentiert | Projektleiter, zusammen mit dem Vertrieb | Die Hilfeseite. Wenn keine Seite es sagt, ist es nicht versprochen; es wird zu einer abgegrenzten Änderung oder zurückgezogen. |
| Ein Redakteur braucht einen Zugriff, den der Administrator nicht geben will | Administrator, zusammen mit dem Projektleiter | Minimale Rechte: eine neue Rolle mit einer Einschränkung, niemals eine Änderung an der Rolle Administrator ([Sicherheit und Prüfung](../../anleitungen/sicherheit-und-audit.md)) |
| Ein Entwickler möchte den Kernel ändern statt einer Erweiterung | Leitender Entwickler, zusammen mit dem Projektleiter | Änderungen am Kernel gehen als Pull-Requests an das Projekt zurück; sitespezifische Arbeit kommt in die eigene Erweiterung der Site ([Erweiterungen](../../anleitungen/extensions.md)) |
| Launch-Termin gegen offene Mängel | Projektleiter | Die Abnahme-Checkliste ([Qualitätsschranken und Abnahme](qualitaetsschranken-und-abnahme.md)) |
| Ob jetzt ein Upgrade erfolgen soll | Administrator und Projektleiter | Das Changelog, die BC-Hinweise und die Upgrade-Prüfung ([Release- und Upgrade-Planung](release-und-upgrade-planung.md)) |

## Checklisten je Rolle

### Projektleiter

- [ ] Jede Teamrolle ist im Projektauftrag einer Rolle der Administration zugeordnet, mit ihren Einschränkungen.
- [ ] Jede offene Übergabe hat im Tracker einen Verantwortlichen und ein Datum.
- [ ] Jedes Versprechen an den Kunden verlinkt eine Hilfeseite.
- [ ] Der Besprechungsrhythmus ist vereinbart und steht in allen Kalendern.

### Vertrieb und Marketing

- [ ] Ich kann die Hilfeseite hinter jeder meiner Aussagen benennen.
- [ ] Ich übergebe jedes Versprechen noch am selben Tag schriftlich an den Projektleiter.
- [ ] Ich führe Demos nur auf einer Installation vor, die mein Team kontrolliert ([Demo-Drehbücher](../geschaeft/demo-drehbuecher.md)).
- [ ] Ich schreibe die Produktnamen so, wie die Markenregeln es vorgeben ([Regeln für Marke und Benennung](../geschaeft/marken-und-namensregeln.md)).

### Redakteur

- [ ] Ich weiß, welche Sektionen und Teilbäume meine Rolle abdeckt.
- [ ] Ich halte unfertige Arbeit als Entwürfe und prüfe **Meine Warteliste** vor jedem Inhalts-Stand-up.
- [ ] Wenn ein Fehler auftritt, gebe ich die Referenz, die Uhrzeit, die Adresse und die Schritte weiter.

### Administrator

- [ ] Die Rollen folgen der Rollenmatrix; niemand hat zusätzlichen Zugriff dadurch bekommen, dass die Rolle
      Administrator bearbeitet wurde.
- [ ] Die Cronjob-Gruppe `frequent` läuft, sodass das Audit-Protokoll indexiert und warnt ([Das Audit-Protokoll](../../../eng-us/features/6.0/audit-trail.md)).
- [ ] Die wöchentliche Checkliste aus [Eine Site betreiben](../../anleitungen/betrieb-einer-website.md) ist erledigt und berichtet.

### Entwickler

- [ ] Jede Änderung ist ein Branch und ein Pull-Request; Betreffzeilen von Commits beginnen mit `Added:`, `Updated:`,
      `Fixed:`, `Removed:` oder `Renamed:` (`CONTRIBUTING.md`).
- [ ] Jede Notiz zum Ausrollen nennt Einstellungen, Datenbank-Updates und Caches.
- [ ] Korrekturen, die allen helfen, gehen an das Projekt zurück ([Mit der Community arbeiten](mit-der-community-arbeiten.md)).

## Verwandte Seiten

- [Projektleiter: Hier beginnen](projektleiter-hier-beginnen.md)
- [Vertrieb und Marketing: Hier beginnen](../geschaeft/vertrieb-und-marketing-hier-beginnen.md)
- [Projektvorlagen: Projektauftrag, RACI, Checklisten, Launch und Übergabe](projektvorlagen.md)
- [Ein Team mit Git führen](ein-team-mit-git-fuehren.md)
- [Der Lesepfad für die Führung](lesepfad-fuer-fuehrungskraefte.md)
- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md)
- [Ein Rundgang durch die Administration](../rundgang-durch-die-administration.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
