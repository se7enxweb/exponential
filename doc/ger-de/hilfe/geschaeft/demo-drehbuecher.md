---
title: Demo-Drehbücher
audience: sales-marketing, editor
category: business
order: 60
summary: Sofort einsetzbare Vorführungen von Exponential in 5, 15 und 45 Minuten, auf einer Demo-Installation, über die Sie selbst verfügen: die Checkliste zur Vorbereitung, der Gesprächsleitfaden, was Sie anklicken und was schiefgehen kann, für die Administration, den Layout-Editor, die Hilfe und die Sprachen einschließlich der Schreibrichtung von rechts nach links.
keywords: Demo, Vorführung, Presales, Drehbuch, Gesprächsleitfaden, Rundgang, Präsentation, Tour, Layout-Editor, Hilfesystem, Sprachen, von rechts nach links, RTL, Audit-Protokoll, Exponential Intelligence, Checkliste, Nachbereitung, demo, demonstration, presales, script, talk track, walk-through, presentation, tour, layouts editor, help system, languages, right to left, audit trail, checklist, follow-up
type: help
---
# Demo-Drehbücher

Diese Seite ist für Mitarbeiter aus Vertrieb und Marketing, die Interessenten Exponential vorführen, und für
Redakteure, die bei einer Vorführung helfen sollen. Sie enthält drei Drehbücher von 5, 15 und 45 Minuten, die durch die
Administration, den Layout-Editor, die Hilfe und die Sprachen einschließlich der Schreibrichtung von rechts nach links
führen. Jedes Drehbuch hat eine Checkliste zur Vorbereitung, einen Gesprächsleitfaden, der nur auf dokumentierten
Fakten beruht, die genauen Klicks und eine Tabelle dazu, was schiefgehen kann und wie Sie die Lage retten. Jedes
Drehbuch läuft auf einer **Demo-Installation, über die Sie selbst verfügen**: Ihrem eigenen Laptop, einem Testserver
Ihres Teams oder einer Kopie, die für den Kunden angelegt wurde. Führen Sie nie auf der Live-Site eines Kunden vor oder
auf einer gemeinsam genutzten Installation, deren Einstellungen jemand anderem gehören.

## Inhalt

1. [Vor jeder Demo: die Checkliste zur Vorbereitung](#vor-jeder-demo-die-checkliste-zur-vorbereitung)
2. [Die 5-Minuten-Demo](#die-5-minuten-demo)
3. [Die 15-Minuten-Demo](#die-15-minuten-demo)
4. [Die 45-Minuten-Demo](#die-45-minuten-demo)
5. [Was schiefgehen kann und wie Sie die Lage retten](#was-schiefgehen-kann-und-wie-sie-die-lage-retten)
6. [Nach der Demo](#nach-der-demo)
7. [Eine Demo auf den Kunden zuschneiden](#eine-demo-auf-den-kunden-zuschneiden)

## Vor jeder Demo: die Checkliste zur Vorbereitung

### Die Demo-Installation aufbauen

Ein einziger Befehl installiert eine vollständige Site auf SQLite, ohne Datenbankserver, in `eng-US`, mit den
Siteaccesses `site` und `admin` ([Exponential mit einem Befehl installieren](../../funktionen/6.0/installation-mit-einem-befehl.md)):

```bash
./console exp:install --dry-run    # checks the configuration and installs nothing
./console exp:install              # installs the multisite package on SQLite
```

- Der Administrator ist `admin` mit einem erzeugten Passwort, das in der Zusammenfassung ausgegeben und in
  `var/log/initial-admin-password` geschrieben wird. Ändern Sie es nach der ersten Anmeldung und löschen Sie diese Datei
  ([Das Standardpasswort des Administrators](../standard-administratorpasswort.md)).
- `exp:install` verweigert die Ausführung über einer bestehenden Installation, es sei denn, Sie geben `--force` an.
  Richten Sie es nie auf eine Datenbank, die Sie behalten wollen: Ein Installer legt eine neue Datenbank an
  ([Upgrade, 11.1](../../installation/11-aktualisieren.md)).
- Andere Installationswege und was jeder davon schreibt, stehen in
  [Kapitel 4, Eine Installationsmethode wählen](../../installation/04-installationsweg-waehlen.md).

### Die Checkliste, einen Tag vorher

```text
[ ] The demo installation opens in the browser: site and administration
[ ] Administrator password changed; var/log/initial-admin-password deleted
[ ] A demo editor account exists, with the Editor role (the help is readable to Editor on a new installation)
[ ] The Layouts tab shows at least one layout (if not: Layouts > New layout, any type, give it a name)
[ ] A second content language is added (Setup > Languages), so items show a dashed badge to translate
[ ] One folder with a few items exists to edit, and nothing on screen contains real personal data
[ ] The Interface box in the right column lists German, Hebrew and Arabic
[ ] Optional, only if you will show it: Exponential Intelligence has a provider key on this installation
[ ] Optional, 45-minute demo: a terminal on the demo machine, for exp:velocity and exp:audit
[ ] The browser: zoom readable for the room, other tabs closed, notifications off, a private window
[ ] This page and the pages you will send afterwards are open in a second window
```

### Die Checkliste, zehn Minuten vorher

```text
[ ] Signed in as the demo account you will use (the editor for the 5- and 15-minute demos)
[ ] Interface language set back to English; color mode set to light (or to what the room's projector shows best)
[ ] No drafts left from the last demo (Dashboard > My drafts)
[ ] The first screen of the script is open
```

> **Warnung**
> Zeigen Sie auf einem geteilten Bildschirm nie einen Anbieterschlüssel, ein Passwort, eine Serveradresse oder den
> Inhalt von `settings/override`. Wenn eine Seite während der Demo nach einem Passwort fragt, tippen Sie es bei
> pausierter Bildschirmfreigabe ein.

## Die 5-Minuten-Demo

**Ziel:** Der Interessent sieht, dass Redakteure selbstständig und sicher in einer übersichtlichen Oberfläche arbeiten
und dass die Hilfe eingebaut ist.
**Publikum:** ein Entscheider, eine Redaktionsleitung, ein erster Termin.

| Zeit | Klick | Sagen |
|---|---|---|
| 0:00 | Das Dashboard | „Das ist die Administration nach der Anmeldung: Schnellaktionen, die Zahlen Ihrer Inhalte, die Veröffentlichungen der letzten 14 Tage und Ihre eigenen Entwürfe und offenen Vorgänge.“ ([Ein Rundgang durch die Administration](../rundgang-durch-die-administration.md#das-dashboard)) |
| 0:40 | Einen Ordner im Baum öffnen, **Bearbeiten** drücken | „Jede Bearbeitung beginnt mit einem Entwurf. Die Site zeigt weiter die veröffentlichte Version, bis Sie veröffentlichen.“ |
| 1:20 | Den **Short name** ändern, **Vorschau** drücken, die Schaltfläche für die Telefonbreite drücken | „Vor dem Veröffentlichen sehen Sie die Seite in der Breite eines Telefons, eines Tablets und eines Desktops.“ |
| 2:00 | **Zurück zur Bearbeitung**, **Zur Veröffentlichung schicken** | „Beim Veröffentlichen bleibt die frühere Version erhalten. Sie können jederzeit zurück.“ |
| 2:30 | Tab **Layouts**, **Layouts**, **Öffnen** bei einem Layout | „Hier ordnen Redakteure Seiten an: Zonen und darin Blöcke. Exponential Layouts bringt das Konzept von Netgen Layouts zu Exponential.“ (`README.md`) |
| 3:10 | Einen Block anklicken | „Seine Einstellungen stehen rechts: was er zeigt und wie er aussieht.“ |
| 3:40 | **Discard**, bestätigen | „Das war ein Entwurf. Wenn Sie ihn verwerfen, bleibt die veröffentlichte Seite genau so, wie sie war.“ |
| 4:10 | Die Schaltfläche **Hilfe** in der Kopfzeile drücken | „Die Hilfe ist eingebaut. Die Schaltfläche Hilfe öffnet die Seite, die zu dem Bildschirm gehört, auf dem Sie gerade sind.“ ([Hilfesystem](../../funktionen/6.0/hilfesystem.md)) |
| 4:40 | Schließen | „Es ist Open Source unter der GPL, und Sie können es selbst hosten.“ ([Wer ist 7x?, Open-Source-Lizenzen](../wer-ist-7x.md#open-source-lizenzen)) |

**Verzichten Sie** in fünf Minuten auf Installieren, Konfigurieren, das Öffnen von Einstellungen und das Zeigen von mehr als
einem Layout.

## Die 15-Minuten-Demo

**Ziel:** Bearbeiten, Versionen, Layouts, Sprachen und Schreibrichtung von rechts nach links, die Hilfe und ein
Nachweis zur Sicherheit.
**Publikum:** ein Redaktionsteam, ein Projektleiter, ein zweiter Termin.

### Teil 1: Bearbeiten und Versionen (4 Minuten)

Folgen Sie dem [Ordner-Beispiel des Rundgangs](../rundgang-durch-die-administration.md#in-der-praxis-einen-ordner-bearbeiten):

1. Öffnen Sie einen Ordner, wählen Sie die Sprache in der Liste, drücken Sie **Bearbeiten**.
2. Zeigen Sie die **Feldleiste**: Je Feld springt eine Schaltfläche zu seiner Karte; ein rotes `*` kennzeichnet ein
   Pflichtfeld.
3. Tippen Sie etwas in **Description**; zeigen Sie auf die Werkzeugleiste für formatierten Text und den Umschalter
   **Editor** unter dem Feld.
4. Drücken Sie **Entwurf speichern und beenden**. Sagen Sie: „Der Entwurf wartet unter Meine Entwürfe; nur Sie sehen
   ihn. Das Formular speichert außerdem alle paar Minuten von selbst.“
5. Öffnen Sie **Versionen verwalten** für das Objekt, drücken Sie **Bearbeiten** beim Entwurf und dann **Zur
   Veröffentlichung schicken**.
6. Drücken Sie in **Versionen verwalten** auf **Vergleichen** zwischen den beiden Versionen.

Gesprächsleitfaden: „Wenn Ihre Site eine Freigabe verwendet, wartet die Version unter Meine Warteliste, bis jemand sie
freigibt.“ ([Workflows](../../anleitungen/workflows.md))

### Teil 2: der Layout-Editor (4 Minuten)

Folgen Sie dem [Layout-Beispiel des Rundgangs](../rundgang-durch-die-administration.md#in-der-praxis-der-layout-editor):

1. Tab **Layouts**, **Layouts**, ein Layout **öffnen**. Zeigen Sie auf die Zonen und ihre Namen.
2. Drücken Sie **Add a block**, ziehen Sie einen Block **Title** in eine Zone, tippen Sie in seinem Tab **Content**
   einen Titel ein.
3. Ziehen Sie den Block an seiner Kopfleiste in eine andere Zone.
4. Zeigen Sie auf eine verknüpfte Zone (rot gestrichelter Rahmen, schraffierte Blöcke): „Kopf- und Fußzeile liegen in
   einem gemeinsam genutzten Layout; andere Layouts verknüpfen sich damit, statt sie zu kopieren.“
5. Öffnen Sie das Menü mit den drei Punkten und zeigen Sie **Save draft and close**; drücken Sie dann **Discard** und
   bestätigen Sie.

Gesprächsleitfaden: „Welche Seiten ein Layout verwenden, legen seine Zuordnungen fest: ein Ort, ein Teilbaum, ein
Inhaltstyp oder ein Siteaccess.“ ([Welche Seiten ein Layout verwenden](../layout-editor.md#welche-seiten-ein-layout-verwenden))

### Teil 3: Sprachen und Schreibrichtung von rechts nach links (3 Minuten)

1. Öffnen Sie ein Objekt. Zeigen Sie auf die Sprachabzeichen: Ein ausgefülltes steht für eine vorhandene Übersetzung,
   ein gestricheltes für eine noch fehlende.
2. Klicken Sie auf das gestrichelte Abzeichen, wählen Sie unter **Übersetzung basiert auf** die vorhandene Übersetzung,
   übersetzen Sie den Namen und veröffentlichen Sie. Sagen Sie: „Jede Übersetzung wird für sich veröffentlicht.“
   ([Sprachen](../sprachen.md#ein-objekt-übersetzen))
3. Wählen Sie in der rechten Spalte unter **Oberfläche** Deutsch und drücken Sie **Anwenden**. Sagen Sie: „Das ist die
   eigene Sprache der Administration dieses Redakteurs; die Inhalte ändern sich nicht.“
4. Wählen Sie Hebräisch oder Arabisch und drücken Sie **Anwenden**. Sagen Sie: „In einer Sprache mit Schreibrichtung von
   rechts nach links dreht sich die Administration um: Die Seitenspalten tauschen die Plätze, und der Text beginnt
   rechts.“ ([Von rechts nach links](../../funktionen/6.0/rechts-nach-links.md))
5. Wechseln Sie zurück zu Englisch.

Gesprächsleitfaden: „268 Locale-Dateien lassen sich als Inhaltssprachen verwenden; es gibt 37 Übersetzungen der
Administration, 24 davon vollständig (Stand 25. Oktober 2026).“ ([Sprachen in Exponential](../../sprachen.md))

### Teil 4: Hilfe und Nachweis (4 Minuten)

1. Drücken Sie **Hilfe**. Drücken Sie `/`, tippen Sie `publish` ein und öffnen Sie ein Ergebnis.
2. Drücken Sie **Als PDF herunterladen**. Sagen Sie: „Jede Hilfeseite lässt sich sauber drucken oder als PDF
   herunterladen, erzeugt auf dem Server, ohne externen Dienst.“ ([Hilfesystem, Drucken und PDF](../../funktionen/6.0/hilfesystem.md#drucken-und-pdf))
3. Wenn Sie als Administrator angemeldet sind: Öffnen Sie den Tab **Audit** (`/audit/dashboard`) und dann **Konsole**.
   Sagen Sie: „Ihre Anmeldung ist bereits festgehalten. Jeder Eintrag trägt den Hash des vorherigen, sodass eine
   Änderung am Protokoll auffällt.“ ([Das Audit-Protokoll](../../funktionen/6.0/pruefprotokoll.md))

## Die 45-Minuten-Demo

**Ziel:** ein vollständiges Bild für ein Team, das entscheiden wird: Redakteure, ein Administrator, ein Entwickler und
ein Projektleiter im Raum. Planen Sie 35 Minuten Vorführung und 10 Minuten für Fragen ein. Verwenden Sie für die Teile 5
bis 8 das Administratorkonto.

| Teil | Minuten | Inhalt | Quelle zum Zeigen oder Schicken |
|---|---|---|---|
| 1 | 3 | Wer dahintersteht, die Lizenz, die Herkunft | [Wer ist 7x?](../wer-ist-7x.md), [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md) |
| 2 | 6 | Die Administration und das Bearbeitungsbeispiel, alle acht Schritte | [Ein Rundgang durch die Administration](../rundgang-durch-die-administration.md) |
| 3 | 4 | Das Inhaltsmodell: eine Inhaltsklasse und ihre Attribute | [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md) |
| 4 | 6 | Der Layout-Editor, einschließlich der Zuordnungen | [Der Layout-Editor](../layout-editor.md) |
| 5 | 4 | Sprachen, Schreibrichtung von rechts nach links, Barrierefreiheit per Tastatur | [Sprachen](../sprachen.md), [Tastenkürzel](../tastenkuerzel.md) |
| 6 | 4 | Exponential Intelligence (nur mit einem Anbieterschlüssel auf der Demo) | [Exponential Intelligence](../../funktionen/6.0/erweiterungen/expai.md) |
| 7 | 4 | Sicherheit: Audit-Protokoll, Anmeldung in zwei Schritten, CSP, Sicherheitshärtung | [Sicherheitshärtung](../sicherheitshaertung.md) |
| 8 | 4 | Betrieb: Logs, Upgrade-Prüfung, Geschwindigkeit | [Log-Viewer](../../funktionen/6.0/log-betrachter.md), [Velocity](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md) |
| 9 | 10 | Fragen | [Einwandbehandlung](einwandbehandlung.md) |

### Teil 1: wer und was (3 Minuten)

Sagen Sie Folgendes und zeigen Sie die Quellen auf dem Bildschirm:

- „Exponential ist ein professionelles PHP-Anwendungsframework mit fortgeschrittenen CMS-Funktionen; sein
  bemerkenswertestes Merkmal ist das vollständig anpassbare und erweiterbare Inhaltsmodell.“ (`README.md`)
- „Es ist freie Software unter der GNU GPL Version 2 oder später, und seine Dokumentation ist frei unter der GNU FDL.“
- „Exponential führt die Codebasis von eZ Publish fort, einem Produkt, das sein Hersteller nach fast 18 Jahren
  eingestellt hat. 7x führt es seit 2023, und die Exponential Foundation, eine gemeinnützige Organisation, betreibt das
  Projekt.“ ([Wer ist 7x?](../wer-ist-7x.md#kurz-gesagt))

### Teil 2: die Administration (6 Minuten)

Gehen Sie die [Kopfzeile](../rundgang-durch-die-administration.md#die-kopfzeile) von links nach rechts durch (Logo, Tabs, Suche mit `/`, die
Globus-Schaltfläche zum Öffnen der Site, die Sonnen-Schaltfläche für den Farbmodus, das Benutzermenü), dann alle acht
Schritte des Beispiels [Einen Ordner bearbeiten](../rundgang-durch-die-administration.md#in-der-praxis-einen-ordner-bearbeiten), zum Schluss mit **Entwurf
verwerfen** bei einem zweiten Entwurf.

### Teil 3: das Inhaltsmodell (4 Minuten)

Öffnen Sie **Setup > Klassen**, öffnen Sie die Klasse *Folder* und zeigen Sie auf ihre Attribute; kehren Sie zum
Bearbeitungsformular des Ordners zurück und zeigen Sie, dass jedes Attribut eine Karte ist. Sagen Sie: „Ihre
Inhaltstypen werden hier definiert, nicht im Code. Ein Artikel, ein Produkt, eine Veranstaltung: Jeder Typ erhält genau
die Felder, die er braucht.“ Ändern Sie während einer Demo keine Klasse.

### Teil 4: Layouts von Anfang bis Ende (6 Minuten)

1. Das **Layouts Dashboard**: die Zahlen der Layouts, Zonen, Blöcke und Zuordnungen.
2. Ein Layout **öffnen**; einen Block hinzufügen, konfigurieren, verschieben und duplizieren; die Blockgruppen zeigen
   (**Basic**, **Components**, **Listing**, **Gallery**, **Containers**, **Placeholders**).
3. Der Modus zum Verknüpfen von Zonen: **Link zone with shared layout zone**, dann **Back**.
4. **Discard**.
5. **Rules**: Zeigen Sie bei einer Zuordnung auf **Targets** und **Conditions**. Sagen Sie: „Es gewinnt die erste
   aktivierte Zuordnung nach Priorität, deren Ziele und Bedingungen auf eine Seite zutreffen.“

### Teil 5: Sprachen, Schreibrichtung von rechts nach links und die Tastatur (4 Minuten)

1. Übersetzen Sie ein Objekt (15-Minuten-Drehbuch, Teil 3), stellen Sie dann die Oberfläche auf Arabisch um und wieder
   zurück.
2. Laden Sie eine Seite neu und drücken Sie einmal `Tab`: **Zum Hauptinhalt springen** erscheint.
3. Drücken Sie `/`: Der Fokus springt in die Suche. Drücken Sie `Esc`.
4. Setzen Sie den Fokus auf den Inhaltsbaum; öffnen und schließen Sie mit den Pfeiltasten einen Ordner; drücken Sie
   `Enter`, um ein Objekt zu öffnen.

Sagen Sie: „Das Design admin4 folgt WCAG-Regeln, die Tests prüfen: Farbkontrast im hellen und im dunklen Modus und
Zielflächen von mindestens 24 mal 24 Pixeln.“ ([Wer ist die Exponential Foundation?, Sprachen und Barrierefreiheit](../wer-ist-die-exponential-foundation.md#sprachen-und-barrierefreiheit))

### Teil 6: Exponential Intelligence (4 Minuten, optional)

Nur, wenn die Demo-Installation einen Anbieterschlüssel hat und Sie den Prompt vorher ausprobiert haben.

1. Öffnen Sie einen Ordner, wählen Sie den Tab **Intelligence** und lassen Sie einen kurzen Artikel darunter anlegen.
2. Zeigen Sie die Liste der Schritte und den Entwurf, den es geschrieben hat; öffnen Sie den Entwurf; verwerfen Sie ihn
   mit **Verwerfen**.
3. Sagen Sie: „Es schreibt nur Entwürfe, als der angemeldete Redakteur und mit dessen Berechtigungen. Es veröffentlicht
   nie von sich aus. Jeder Anbieter hat eine Region, USA, EU oder lokal, und jede Anfrage hält fest, wohin sie ging.“

Ohne Schlüssel: Zeigen Sie stattdessen die Seite [Exponential Intelligence](../../funktionen/6.0/erweiterungen/expai.md) und
sagen Sie, dass es nichts irgendwohin sendet, solange kein Anbieterschlüssel hinterlegt ist (`README.md`).

### Teil 7: Sicherheit (4 Minuten)

1. Tab **Audit**, **Konsole**: Zeigen Sie das neueste `access.session.login`.
2. `/user2fa/setup` mit dem Demo-Konto: Zeigen Sie die Wahl zwischen Authenticator-App und Code per E-Mail; schließen
   Sie die Einrichtung auf einem geteilten Bildschirm nicht ab ([Anmeldung in zwei Schritten](../../anleitungen/zwei-faktor-anmeldung.md)).
3. Öffnen Sie [Content Security Policy](../../funktionen/6.0/content-security-policy.md) in der Hilfe: „Standardmäßig
   läuft sie in der Administration nur im Berichtsmodus und ist auf den Sites ausgeschaltet; die Seite erklärt, wie Sie
   sie durchsetzen.“
4. Öffnen Sie die [Checkliste der Sicherheitshärtung](../sicherheitshaertung.md#checkliste).

### Teil 8: Betrieb und Geschwindigkeit (4 Minuten)

1. **Setup > Logdateien**: der Navigator, eine Suche, die Maskierung personenbezogener Daten („Privacy first“).
2. **Setup > Upgrade-Prüfung**: „Vor einem Upgrade vergleicht diese Seite jede Datei mit dem Release.“
3. Im Terminal auf dem Demo-Rechner:

   ```bash
   ./console exp:velocity status
   ./console exp:audit verify --allow-root-user
   ```

4. Geschwindigkeit: Sagen Sie nur, was dokumentiert ist. „Die README dokumentiert 0,36 ms je gecachter Seite und
   8.941 Anfragen pro Sekunde auf einer Maschine mit 12 Kernen, reproduzierbar mit mitgelieferten Skripten. Wir können
   `exp:benchmark` auf Ihrer eigenen Hardware ausführen.“
   ([Benchmarks](../../anleitungen/benchmarking.md))

> **Hinweis**
> Ein Benchmark auf einem Laptop während eines Termins beweist wenig: „Ein einzelner Lauf auf einer ausgelasteten
> Maschine beweist wenig“ ([Benchmarks](../../anleitungen/benchmarking.md)). Bieten Sie als Nachbereitung eine Messung auf
> der Hardware des Kunden an.

## Was schiefgehen kann und wie Sie die Lage retten

| Was passiert | Warum | Was Sie sofort tun |
|---|---|---|
| Kein Tab **Layouts** | Die Rolle darf möglicherweise nicht mit Layouts arbeiten | Wechseln Sie zum Administratorkonto oder springen Sie zum nächsten Teil |
| Der Layout-Editor fragt nach einer Veröffentlichung, ob Caches geleert werden sollen | Die Site leert ihre Caches nicht von selbst | Wählen Sie **Clear caches** ([Der Layout-Editor](../layout-editor.md)) |
| *Möglicher Bearbeitungs-Konflikt* | Es gibt einen Entwurf aus einer früheren Demo | Öffnen Sie **Versionen verwalten** und verwerfen Sie den alten Entwurf; räumen Sie die Entwürfe vor jeder Demo auf |
| Kein Kasten **Oberfläche** in der rechten Spalte | Sie sind im Bearbeitungsformular, oder der Rolle fehlt die Berechtigung | Verlassen Sie das Bearbeitungsformular; verwenden Sie das Administratorkonto |
| Eine Sprache zeigt englische Texte | Sie hat keine vollständige Übersetzung der Administration | Sagen Sie es offen: Zehn Übersetzungen sind vollständig ([Unterstützungsstufen](../../sprachen.md#2-unterstützungsstufen)) |
| Kein Tab **Intelligence** | Für diesen Siteaccess nicht aktiviert, keine Richtlinie `expai/write` oder kein Schlüssel | Zeigen Sie stattdessen die Dokumentationsseite |
| Keine Schaltfläche **Hilfe** | Der Rolle fehlt `exphelp/read` | Erteilen Sie die Berechtigung ([Hilfesystem, Wer sie lesen darf](../../funktionen/6.0/hilfesystem.md#wer-sie-lesen-darf)) oder verwenden Sie den Administrator |
| Eine Seite meldet *Something went wrong on our side* mit einer Referenz | Ein echter Fehler | Notieren Sie die Referenz; sagen Sie, dass er protokolliert und auffindbar ist ([Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md)); machen Sie weiter |
| Eine Änderung erscheint auf einem Velocity-Server nicht | Die Worker halten die Anwendung im Speicher | `./console exp:velocity restart` nach der Demo, nicht währenddessen (`README.md`) |
| Der Projektor lässt den dunklen Modus verblassen | Das liegt am Kontrast des Raums, nicht an der Seite | Drücken Sie die Sonnen-Schaltfläche, um auf hell umzuschalten |
| Eine Frage, die Sie nicht beantworten können | Sie steht nicht in der Dokumentation | „Die Dokumentation macht dazu keine Angabe; ich prüfe das und schicke Ihnen die Quelle.“ Schreiben Sie sie auf. |

## Nach der Demo

1. Verwerfen Sie jeden Entwurf, den Sie angelegt haben (**Dashboard > Meine Entwürfe**), und entfernen Sie
   Testübersetzungen, damit die nächste Demo sauber beginnt.
2. Stellen Sie die Sprache der Oberfläche wieder auf Englisch.
3. Schicken Sie innerhalb eines Tages eine Nachbereitung, die auf die Seiten hinter dem Gezeigten verlinkt:

```text
Subject: Exponential: the pages behind today's demonstration

Thank you for your time today. Every point we showed is documented here:

- Editing, drafts and versions: <link to "A tour of the administration">
- The layouts editor: <link to "The layouts editor">
- Languages and right to left: <link to "Languages in Exponential">
- Security and audit: <link to "Security hardening"> and <link to "The audit trail">
- Licensing: <link to "Licensing explained for buyers">

Open questions we will answer with a source: <list>
Proposed next step: <a workshop / a test installation for your team / a measurement on your hardware>
```

## Eine Demo auf den Kunden zuschneiden

| Dem Kunden ist am wichtigsten | Zuerst zeigen | Weniger Zeit verwenden auf |
|---|---|---|
| Redaktionelle Unabhängigkeit | Layouts, Bearbeiten, Versionen | Betrieb |
| Viele Länder und Sprachen | Multisite (die Globus-Schaltfläche), Übersetzungen, Schreibrichtung von rechts nach links | Einzelheiten der Layouts |
| Compliance und Sicherheit | Audit-Protokoll, Anmeldung in zwei Schritten, CSP, Checkliste der Sicherheitshärtung | Einzelheiten der Layouts |
| Barrierefreiheit | Tastatur, Landmarks, die Aussagen zu Kontrast und Zielgröße | Geschwindigkeit |
| IT und Hosting | Datenbanken, Betriebssysteme, Velocity, Upgrade-Prüfung | Einzelheiten des Bearbeitens |
| Inhaltsproduktion in großem Umfang | Exponential Intelligence, Inhaltsaufträge, Pakete | Schreibrichtung von rechts nach links |

Über die [Funktionsübersicht](funktionshighlights-fuer-kunden.md#die-übersicht-bedürfnis-funktion-wo-zeigen-nachweis) finden Sie
die Stelle, an der Sie jedes andere Bedürfnis zeigen können.

**Bei einer öffentlichen Demo.** Der Hinweisbalken **Demo-Modus** oben in der Administration ist zu erwarten: Er teilt den Betrachtern mit, dass das Administratorpasswort veröffentlicht ist und nicht geändert werden kann. Erwähnen Sie ihn, wenn danach gefragt wird; lassen Sie **Passwort ändern** im Drehbuch aus, da es dort abgelehnt wird. [FAQ zum Demo-Modus](../demomodus-faq.md).

## Verwandte Seiten

- [Funktionshighlights, den Bedürfnissen der Kunden zugeordnet](funktionshighlights-fuer-kunden.md)
- [Einwandbehandlung: Sicherheit, Langlebigkeit und Support](einwandbehandlung.md)
- [Vertrieb und Marketing: hier beginnen](vertrieb-und-marketing-hier-beginnen.md)
- [Ein Rundgang durch die Administration](../rundgang-durch-die-administration.md)
- [Der Layout-Editor](../layout-editor.md)
- [Zusammenarbeiten](../fuehrung/zusammenarbeiten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
