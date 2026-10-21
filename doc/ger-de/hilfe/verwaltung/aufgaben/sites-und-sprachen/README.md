---
title: Aufgaben zu Sites und Sprachen
audience: government, admin, editor, website-creator
category: public-sector
order: 10
summary: Schritt-für-Schritt-Aufgaben für Behörden zu mehrsprachigen Sites, Abteilungs-Sites, Intranets und Extranets, offenen Daten, Transparenz, Suche, Newslettern und Karten in Exponential, jede mit Voraussetzungen, Schritten, Prüfungen und häufigen Problemen.
keywords: Aufgaben, Anleitung, Schritt für Schritt, Sprachen, Übersetzung, Multisite, Intranet, Extranet, offene Daten, Informationsfreiheit, Suche, Newsletter, Karten, Index, tasks, how to, step by step, languages, translation, open data, FOI, search, maps
type: help
---
# Aufgaben zu Sites und Sprachen

Dieser Bereich enthält kurze Aufgabenseiten: je eine Aufgabe, mit dem, was Sie zuerst brauchen, den Schritten, der
Prüfung des Ergebnisses und den üblichen Problemen. Den Hintergrund jeder Aufgabe finden Sie in den verlinkten
Bereichen der Anleitung: Sprachen, offene Daten, Transparenz und Informationsfreiheit, Souveränität sowie die Bereiche
zum Aufbauen von Suche, Karten, Newslettern, Multisite, Intranets und Extranets.

Nach oben: [Aufgaben für Behörden](../README.md), [Behörden: hier beginnen](../../verwaltungen-hier-beginnen.md).

## Inhalt

1. [Sprachen](#sprachen)
2. [Sites, Intranets und Extranets](#sites-intranets-und-extranets)
3. [Offene Daten und Transparenz](#offene-daten-und-transparenz)
4. [Suche, Newsletter und Karten](#suche-newsletter-und-karten)

## Sprachen

| Aufgabe | Was sie bewirkt |
|---|---|
| [Eine Seite in die zweite Amtssprache übersetzen](eine-seite-in-die-zweite-amtssprache-uebersetzen.md) | Die Übersetzung einer bestehenden Seite in der zweiten Amtssprache hinzufügen, ausgehend von der ersten Sprache, sie veröffentlichen und auf der Site dieser Sprache prüfen. |
| [Eine neue Inhaltssprache hinzufügen](eine-neue-inhaltssprache-hinzufuegen.md) | Eine Inhaltssprache unter **Setup > Sprachen** hinzufügen, sie über einen Siteaccess sichtbar machen und Übersetzern Zugang geben. |
| [Einen Sprachumschalter hinzufügen](einen-sprachumschalter-hinzufuegen.md) | Einen barrierefreien Sprachumschalter auf jede Seite einer mehrsprachigen öffentlichen Site setzen, mit TranslationSA und dem Operator language_switcher. |
| [Einer Sprache eine eigene Adresse geben](einer-sprache-eine-eigene-adresse-geben.md) | Einen Siteaccess für eine Sprache anlegen und ihn über einen Pfad (/fr/) oder einen Host (fr.example.gov) erreichen, mit den RegionalSettings, die diese Sprache zuerst zeigen. |
| [Die Startseite auf die Sprache des Besuchers umleiten](die-startseite-auf-die-sprache-des-besuchers-umleiten.md) | www.example.gov/ jeden Besucher einmal in die Sprache schicken lassen, die sein Browser verlangt, mit DefaultHostUriMatchMapItems, und das mit curl testen. |
| [Gemeinsam genutzte Elemente immer verfügbar machen](gemeinsam-genutzte-elemente-immer-verfuegbar-machen.md) | Bilder, Medienordner, Benutzerkonten und einsprachige Dokumente als immer verfügbar markieren, damit jede Sprach-Site sie zeigt. |
| [Einen Übersetzer auf eine Sprache beschränken](einen-uebersetzer-auf-eine-sprache-beschraenken.md) | Eine Rolle anlegen, deren Inhaltsrichtlinien durch Sprache eingeschränkt sind, sie einer Übersetzergruppe zuweisen und mit einem Testkonto prüfen. |
| [Die Hauptsprache eines Elements ändern](die-hauptsprache-eines-elements-aendern.md) | Eine andere vorhandene Übersetzung im Reiter **Übersetzungen** zur Hauptsprache eines Elements machen, zum Beispiel bevor eine in der falschen Sprache angelegte Übersetzung entfernt wird. |
| [Eine falsche Übersetzung sicher entfernen](eine-falsche-uebersetzung-sicher-entfernen.md) | Ein in der falschen Sprache angelegtes Element in die richtige verschieben (übersetzen, als Haupt setzen, entfernen), im Wissen, dass das Entfernen einer Übersetzung sie in jeder Version löscht. |
| [Eine Sprach-Site mit Schreibrichtung von rechts nach links hinzufügen](eine-sprach-site-mit-schreibrichtung-von-rechts-nach-links-hinzufuegen.md) | Eine Sprach-Site für Arabisch, Hebräisch, Persisch oder Urdu hinzufügen, mit der im Pagelayout ausgegebenen Schreibrichtung und logischem CSS, und sie testen. |
| [Die Administration auf eine andere Sprache umschalten](die-administration-auf-eine-andere-sprache-umschalten.md) | Die Sprache der Menüs und Schaltflächen der Administration für sich selbst über die Liste **Sprache der Oberfläche** wählen und wissen, was sich ändert und was nicht. |
| [Die Abdeckung der Oberflächenübersetzung prüfen](die-abdeckung-der-oberflaechenuebersetzung-pruefen.md) | Herausfinden, wie vollständig die Übersetzung der Administration in eine Sprache ist, welche Übersetzung eine regionale Locale wirklich zeigt und was unübersetzt ist, mit ezchecktranslation, exp:i18n und **Setup > Oberflächensprachen**. |
| [Einen Freigabe-Workflow für Übersetzungen einrichten](einen-freigabe-workflow-fuer-uebersetzungen-einrichten.md) | Übersetzungen in eine Sprache vor dem Veröffentlichen von den Prüfern dieser Sprache freigeben lassen, mit einem Approve-Ereignis, das auf die betroffenen Sprachen beschränkt und mit **Vor dem Veröffentlichen von Inhalten** verbunden ist. |
| [Übersetzungen mit Lorem Ipsum testen](uebersetzungen-mit-lorem-ipsum-testen.md) | Die Testsprache Lorem Ipsum (qlo-ZZ) hinzufügen, Übersetzungen mit exp:loremipsum füllen, Ersatzsprachen und unübersetzte Texte auf einem Test-Siteaccess prüfen und sie wieder entfernen. |
| [Eine eigene Locale für eine Minderheitensprache hinzufügen](eine-eigene-locale-fuer-eine-minderheitensprache-hinzufuegen.md) | Eine Sprache ohne Locale-Datei hinzufügen, indem Sie share/locale/<code>.ini aus einer verwandten Locale schreiben, sie als individuelle Inhaltssprache hinzufügen und von einem Sprecher prüfen lassen. |
| [Tagesordnung und Protokoll einer Sitzung in zwei Sprachen veröffentlichen](tagesordnung-und-protokoll-einer-sitzung-in-zwei-sprachen-veroeffentlichen.md) | Eine Rats- oder Ausschusssitzung mit ihrer Tagesordnung und später ihrem Protokoll in beiden Amtssprachen veröffentlichen, mit übersetzten Sitzungsobjekten und einem Dokument je Sprache. |

## Sites, Intranets und Extranets

| Aufgabe | Was sie bewirkt |
|---|---|
| [Eine Unter-Site für eine Abteilung einrichten](eine-unter-site-fuer-eine-abteilung-einrichten.md) | Einer bestehenden Installation eine Site für eine Abteilung hinzufügen, mit eigenem Ordner, Siteaccess, Host und Link zur Administration. |
| [Einer Abteilung ein eigenes Design geben](einer-abteilung-ein-eigenes-design-geben.md) | Eine dünne Designschicht für eine Abteilungs-Site anlegen, die das gemeinsame Basisdesign nur wo nötig überschreibt, sie im Siteaccess setzen und die Barrierefreiheit testen. |
| [Eine Seite zwischen zwei Sites teilen](eine-seite-zwischen-zwei-sites-teilen.md) | Eine Seite auf zwei Abteilungs-Sites erscheinen lassen, ohne sie zu kopieren, indem Sie ihr einen zweiten Ort geben, sodass eine Korrektur beide Sites zugleich erreicht. |
| [Von einer Abteilungs-Site auf eine andere verlinken](von-einer-abteilungs-site-auf-eine-andere-verlinken.md) | Links von einer Site auf Inhalte einer anderen Site derselben Installation auf den richtigen Host zeigen lassen, mit den Einstellungen von ezurlmultisite, und sie prüfen. |
| [Einen Intranet-Siteaccess für Mitarbeiter einrichten](einen-intranet-siteaccess-fuer-mitarbeiter-einrichten.md) | Einen Intranet-Siteaccess anlegen, der für jede Seite eine Anmeldung verlangt, mit eigenem Ordner in einer Sektion, die Besucher nicht lesen können, und einer Rolle für Mitarbeiter, und ihn von außen und innen testen. |
| [Die Anmeldung der Mitarbeiter an LDAP anbinden](die-anmeldung-der-mitarbeiter-an-ldap-anbinden.md) | Mitarbeiter sich im Intranet mit ihrem Verzeichniskonto über den LDAP-Login-Handler des Kernels anmelden lassen, konfiguriert in ldap.ini und zuerst auf einer Testinstallation getestet, weil es keine Anleitung für 6.x gibt. |
| [Die zweistufige Anmeldung für Mitarbeiter einrichten](die-zweistufige-anmeldung-fuer-mitarbeiter-einrichten.md) | Die zweistufige Anmeldung (Authenticator-App oder E-Mail-Code) mit sevenx_authentication_2fa einschalten, sie für Mitarbeiter verlangen, die Geheimnisse schützen und sie testen. |
| [Auftragnehmern Zugang zum Extranet geben](auftragnehmern-zugang-zum-extranet-geben.md) | Den Mitarbeitern eines Auftragnehmers nur Zugang zu ihrem eigenen Projektbereich geben, mit einer Benutzergruppe, einer Sektion, einer eingeschränkten Rollenzuweisung und persönlichen Konten, und das Enddatum festhalten. |
| [Verwaiste Konten von Auftragnehmern prüfen und deaktivieren](verwaiste-konten-von-auftragnehmern-pruefen-und-deaktivieren.md) | Einmal im Monat externe Konten, die nie oder lange nicht genutzt wurden, unter **Benutzer > Benutzerkonten prüfen** finden, die Liste für die Projektleiter exportieren und deaktivieren, was nicht mehr gebraucht wird. |

## Offene Daten und Transparenz

| Aufgabe | Was sie bewirkt |
|---|---|
| [Einen Datensatz mit einer stabilen URL veröffentlichen](einen-datensatz-mit-einer-stabilen-url-veroeffentlichen.md) | Eine Datensatzseite mit ihren Dateien im Katalog anlegen, ihr einen kurzen dauerhaften Alias zum Zitieren geben und die Adressen als Besucher prüfen. |
| [Einer Datensatzklasse ein Lizenzfeld hinzufügen](einer-datensatzklasse-ein-lizenzfeld-hinzufuegen.md) | Der Datensatzklasse eine verpflichtende Auswahl License mit den Lizenzen hinzufügen, die Ihre Behörde genehmigt hat, und sie für bestehende Datensätze ausfüllen. |
| [Einen Feed neuer Datensätze veröffentlichen](einen-feed-neuer-datensaetze-veroeffentlichen.md) | Einen RSS-2.0- oder Atom-Export des Datenkatalogs unter **Setup > RSS** anlegen, die Felder der Datensätze zuordnen und den Feed von der Katalogseite aus verlinken. |
| [Eine Liste von Inhalten als CSV exportieren](eine-liste-von-inhalten-als-csv-exportieren.md) | Die Objekte einer Klasse unterhalb eines Ordners (ein Register, eine Liste von Servicestellen) als CSV exportieren, mit xrowextract in der Administration oder exp:ezcsvexport des Kernels, und vor dem Veröffentlichen prüfen. |
| [Das Offenlegungsprotokoll veröffentlichen](das-offenlegungsprotokoll-veroeffentlichen.md) | Das öffentliche Offenlegungsprotokoll einrichten (eine Klasse für Einträge ohne Daten der Antragsteller, ein Ordner je Jahr, eine Listenansicht, ein Feed) und den ersten Eintrag veröffentlichen. |
| [Eine Auskunft mit geprüften Schwärzungen veröffentlichen](eine-auskunft-mit-geprueften-schwaerzungen-veroeffentlichen.md) | Die Schwärzungen freigegebener Dokumente außerhalb von Exponential prüfen, dann die Auskunft als Eintrag im Offenlegungsprotokoll mit ihren Dateien veröffentlichen, freigeben lassen und abgemeldet prüfen. |
| [Ein Antragsformular für Informationsfreiheit einrichten](ein-antragsformular-fuer-informationsfreiheit-einrichten.md) | Ein Online-Antragsformular mit dem Information Collector bauen, jeden Antrag an das Postfach des Teams für Informationsfreiheit senden, auf eine Dankesseite weiterleiten und es testen. |
| [Gesammelte Anträge exportieren und löschen](gesammelte-antraege-exportieren-und-loeschen.md) | Die Einsendungen eines Antragsformulars unter **Setup > Gesammelte Informationen** als CSV für die Fallakte exportieren und sie dann gemäß der Aufbewahrungsregel von der Website entfernen. |
| [Die Grenze der Versionshistorie für Schriftgut anheben](die-grenze-der-versionshistorie-fuer-schriftgut-anheben.md) | Für schriftgutrelevante Klassen (Beschlüsse, Offenlegungseinträge, Sitzungen) mehr Versionen als die voreingestellten 45 aufbewahren, mit VersionHistoryClass in content.ini, und die Wirkung prüfen. |
| [Herausfinden, wer eine Seite geändert hat](herausfinden-wer-eine-seite-geaendert-hat.md) | Herausfinden, wer eine Seite geändert, verschoben oder entfernt hat und was sich geändert hat, mit der Seite der Versionen (zwei Versionen vergleichen) und dem Audit-Protokoll (Konsole oder exp:audit). |
| [Eine Veröffentlichung für einen festen Zeitpunkt planen](eine-veroeffentlichung-fuer-einen-festen-zeitpunkt-planen.md) | Eine Seite (eine Statistikveröffentlichung, eine Tagesordnung) zu einem festen Datum und einer festen Uhrzeit mit dem Workflow-Ereignis **Bis Datum warten** veröffentlichen und wissen, wie genau der Zeitpunkt ist. |
| [Inhalte nach einem Schlussdatum verstecken](inhalte-nach-einem-schlussdatum-verstecken.md) | Beteiligungsverfahren, Stellenangebote oder Aufrufe automatisch verstecken, wenn ihr Schlussdatum vorbei ist, mit dem Cronjob hide.php und den HideSettings in content.ini, wobei der Inhalt als Schriftgut erhalten bleibt (anders als bei unpublish.php, das ihn entfernt). |

## Suche, Newsletter und Karten

| Aufgabe | Was sie bewirkt |
|---|---|
| [Die Suche in Dokumenten einrichten](die-suche-in-dokumenten-einrichten.md) | Den Text in PDF- (und Word-)Dokumenten durchsuchbar machen, mit den Handlern zur Textextraktion in binaryfile.ini und einem installierten Extraktionswerkzeug, dann den Index neu aufbauen und testen. |
| [Die Suchstatistik einschalten](die-suchstatistik-einschalten.md) | Festhalten, wonach Besucher auf einem öffentlichen Siteaccess suchen (LogSearchStats), **Setup > Suchstatistiken** lesen und die Suchbegriffe regelmäßig zurücksetzen, weil sie personenbezogene Daten enthalten können. |
| [Eine Newsletter-Liste für Warnmeldungen hinzufügen](eine-newsletter-liste-fuer-warnmeldungen-hinzufuegen.md) | cjw_newsletter eine Liste für Warnmeldungen (zum Beispiel zu Beteiligungsverfahren) hinzufügen, ihr Anmeldeformular von den Seiten des Themas aus verlinken und nach einer Probe eine erste Warnmeldung senden. |
| [Einen Newsletter mit dem Datei-Transport proben](einen-newsletter-mit-dem-datei-transport-proben.md) | Einen ganzen Newsletter-Zyklus (anmelden, bestätigen, senden, abmelden) durchspielen, während cjw_newsletter die Mails in .eml-Dateien schreibt, sodass keine echte Person eine Mail erhält. |
| [Einer Servicestelle einen Kartenstandort hinzufügen](einer-servicestelle-einen-kartenstandort-hinzufuegen.md) | Der Klasse der Servicestelle ein Feld GMap Location (ezgmaplocation) geben und den Standort eines Büros setzen; Google Maps ist ein Drittanbieter und braucht einen API-Schlüssel, ohne den Koordinaten ohne Karte erscheinen. |

## Verwandte Seiten

- [Sprachen](../../sprachen/README.md)
- [Offene Daten](../../offene-daten/README.md)
- [Transparenz und Informationsfreiheit](../../transparenz-und-informationsfreiheit/README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
