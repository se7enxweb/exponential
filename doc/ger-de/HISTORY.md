# Geschichte von Exponential (1999 - 2026)

Dieses Buch erzählt die ganze Geschichte des Codes, mit dem Sie arbeiten. Der erste Teil ist die Geschichte von
Exponential selbst: wie der Legacy-Kernel 2023 übernommen wurde, jede Version der Reihe 6.0, die Umbenennung, die
Funktionen, die verändert haben, was die Software kann, die Exponential Foundation, die Distributionen und die
Community. Der zweite Teil geht weiter zurück, Version für Version, bis zur Herkunft des Quellcodes: eZ publish, ab
1999 von der eZ Systems AS in Norwegen geschrieben, über die Reihen 3.x und 4.x, das auf Symfony gestützte eZ Publish
5, eZ Platform und das Unternehmen, das heute Ibexa heißt, und wie der Legacy-Kernel weiterlebte, bis er zu
Exponential wurde.

Lesen Sie es, um zu verstehen, warum der Code so aussieht, wie er aussieht, warum ein Bezeichner noch mit `ez`
beginnt, welche alte Dokumentation noch gilt und wo eine Funktion zuerst erschien. Jedes Datum im Teil über
Exponential stammt aus dem Repository selbst (seinen Tags, Commits und Änderungsprotokollen); jedes Datum zu
eZ Systems und Ibexa stammt entweder aus den historischen Dateien des Repositorys oder aus einer öffentlichen Quelle,
die unter [Quellen](#16-quellen) steht. Wo sich ein Datum nicht bestätigen ließ, steht hier „etwa“, oder es fehlt.

## Inhalt

1. [Kurz gefasst](#1-kurz-gefasst)
2. [Der Stammbaum des Codes](#2-der-stammbaum-des-codes)
3. [Exponential: die Übernahme des Legacy-Kernels (2023)](#3-exponential-die-übernahme-des-legacy-kernels-2023)
4. [Exponential 6.0.0 bis 6.0.9 (2024 - 2025)](#4-exponential-600-bis-609-2024---2025)
5. [Der Name Exponential (August 2025)](#5-der-name-exponential-august-2025)
6. [Exponential 6.0.10 bis 6.0.14 (2025 - 2026)](#6-exponential-6010-bis-6014-2025---2026)
7. [Die Reihe 6.0.15 (2026)](#7-die-reihe-6015-2026)
8. [Die Exponential Foundation und 7x](#8-die-exponential-foundation-und-7x)
9. [Distributionen, Pakete und die Community](#9-distributionen-pakete-und-die-community)
10. [eZ Systems und eZ publish 1.x und 2.x (1999 - 2002)](#10-ez-systems-und-ez-publish-1x-und-2x-1999---2002)
11. [eZ publish 3 (2002 - 2007)](#11-ez-publish-3-2002---2007)
12. [eZ Publish 4 (2007 - 2012)](#12-ez-publish-4-2007---2012)
13. [eZ Publish 5, eZ Platform und Ibexa (2012 - heute)](#13-ez-publish-5-ez-platform-und-ibexa-2012---heute)
14. [Wie der Legacy-Kernel weiterlebte (2014 - 2023)](#14-wie-der-legacy-kernel-weiterlebte-2014---2023)
15. [Lizenzen, Versionsnummern und Namen im Lauf der Zeit](#15-lizenzen-versionsnummern-und-namen-im-lauf-der-zeit)
16. [Quellen](#16-quellen)

## 1. Kurz gefasst

| Jahr | Was geschah |
|---|---|
| 1999 | Die eZ Systems AS wurde in Norwegen gegründet; im selben Jahr begann das Projekt eZ publish. |
| 2000 | Im November erschien eZ publish 1.0, ein unter der GPL lizenziertes Content-Management-System in PHP. |
| 2001 | Die Reihe 2.x (2.0 bis 2.2) machte eZ publish zu einem weit verbreiteten freien CMS; Debian paketierte 2.2. |
| 2002 | Die Neuentwicklung, aus der eZ publish 3 wurde, begann (erster Commit dieses Repositorys: 2. September 2002). |
| 2003 | eZ publish 3.0 erschien (Tag `3.0-1`, 24. März 2003): die Architektur aus Kernel, Modulen und Templates, die dieser Code noch hat. |
| 2003 - 2007 | Zehn Unterreihen von 3.x, von 3.1 bis 3.10. |
| 2007 | eZ Publish 4.0 brachte PHP 5 (Tag `4.0.0`, 4. Dezember 2007). |
| 2009 - 2012 | 4.1 bis 4.7; ab 2011 erschien die freie Ausgabe als eZ Publish Community Project (4.2011, dann 2011.5 und spätere Builds). |
| 2012 | eZ Publish 5 stellte einen auf Symfony gestützten Stack neben den alten Kernel, der von da an Legacy-Kernel hieß. |
| 2014 | Die letzten Versionen von eZ Publish durch eZ Systems: 5.4 und der Community-Build 2014.11 (17. November 2014). |
| 2015 | eZ Platform, ohne den Legacy-Kernel, erschien am 15. Dezember 2015. |
| 2016 - 2021 | Der Legacy-Kernel lebte als `ezsystems/ezpublish-legacy` weiter (Versionen 2017.10 bis 2019.03.x) und in einem Fork von Netgen. |
| 2020 | eZ Systems gab am 27. April 2020 seinen neuen Namen bekannt: Ibexa. |
| 2023 | se7enxweb forkte den Legacy-Kernel auf GitHub (5. Oktober 2023) und übernahm am 11. Dezember 2023 die erste Änderung von außen. |
| 2024 | Die Vorgängerversion 6.0.0 wurde am 1. Januar 2024 stabil, mit einem SQLite-Treiber; im August folgte das Design admin3. |
| 2025 | Unterstützung für PHP 8.4 (April) und PHP 8.5 (Dezember); im August 2025 wurde das Produkt in Exponential umbenannt (6.0.10). |
| 2026 | MongoDB (6.0.14, Juni), Velocity, das Design admin4, das Hilfecenter, die Exponential Foundation als Rechteinhaberin und die Reihe 6.0.15. |

## 2. Der Stammbaum des Codes

Der Code dieser Installation hat eine durchgehende Geschichte. Jeder Schritt unten übernahm den Quellcode des
vorigen samt seiner Versionsgeschichte; nach 2002 wurde nichts mehr von Grund auf neu geschrieben.

1. **eZ publish 1.x und 2.x** (1999 - 2002), von eZ Systems. Eine andere Code-Basis; ihre Ideen, nicht ihre Dateien, gingen in 3.0 ein.
2. **eZ publish 3.x** (2002 - 2007). Der erste Commit dieses Repositorys stammt vom 2. September 2002, im
   Subversion-Repository „nextgen“ bei eZ Systems. Die frühen Versionsnummern 2.9-1 bis 2.9-8 waren die Betas von 3.0.
3. **eZ Publish 4.x** (2007 - 2012). Derselbe Kernel, umgestellt auf PHP 5.
4. **eZ Publish 5.x „legacy“** (2012 - 2014). Derselbe Kernel, im oder neben dem neuen Symfony-Stack betrieben.
5. **`ezsystems/ezpublish-legacy`** auf GitHub (Repository angelegt im Juni 2010; Community-Versionen bis 2019.03.x;
   seitdem archiviert, mit dem Hinweis „Succeeded by eZ Platform, no further feature releases planned“).
6. **`netgen/ezpublish-legacy`** (ab Februar 2016), „maintained by Netgen for compatibility with eZ Platform“.
7. **`se7enxweb/exponential`** (ab 5. Oktober 2023), geforkt von Netgens Repository, veröffentlicht als 6.0.x und im
   August 2025 in Exponential umbenannt.

Sie können diese Geschichte selbst durchgehen:

```bash
git log --reverse --format='%ad %h %s' --date=short | head -3     # 2002-09-02 008ec26cca Added trunk dir.
git for-each-ref --sort=creatordate --format='%(creatordate:short) %(refname:short)' refs/tags | head
git tag -l 'v6.0.*' --sort=version:refname
```

Das Repository enthält fast 30.000 Commits. Nach Jahren erzählt schon die Aktivität die Geschichte: Tausende Commits
im Jahr von 2002 bis 2011 bei eZ Systems, einige Hundert im Jahr, solange die Reihe 5.x gepflegt wurde, ein Rinnsal
von 2015 bis 2022 und dann die Zeit von se7enxweb ab 2023, mit mehr als 8.000 Commits allein 2026.

## 3. Exponential: die Übernahme des Legacy-Kernels (2023)

2023 hatte der Legacy-Kernel seit Jahren keine Version eines Herstellers mehr bekommen (das letzte Tag von
`ezsystems/ezpublish-legacy`, 2019.03.6.1, entstand im September 2021), doch viele Websites liefen noch darauf.
7x (vor 2022 als Brookins Consulting bekannt), ein Unternehmen, das lange mit eZ Publish gearbeitet hatte, forkte am
5. Oktober 2023 die von Netgen gepflegte Kopie auf GitHub als `se7enxweb/exponential`.

- **11. Dezember 2023:** Die erste Änderung von außen wurde in den Fork übernommen (Upstream-Pull-Request #40).
- **Dezember 2023:** Der Setup-Assistent lief nun unter PHP 8, und Update-Skripte ab 5.4 wurden veröffentlicht, damit
  eine Website auf der letzten Version von eZ Systems auf die neue Reihe wechseln konnte.

Die Geschichte dieser Zeit und jedes folgenden Monats steht in der [Chronik](geschichte/README.md).

## 4. Exponential 6.0.0 bis 6.0.9 (2024 - 2025)

Die neue Reihe erhielt die Nummer 6.0, die nächste Hauptversion nach den Versionen 5.x von eZ Systems. Die Daten
sind die Daten der Versions-Tags in diesem Repository.

| Version | Datum | Wichtigstes |
|---|---|---|
| 6.0.0 | 1. Januar 2024 | Stabil unter PHP 8; SQLite kam als Datenbank ohne Server hinzu (Treiber, Schema-Handler, Demo-Datenbank). |
| 6.0.1 | 29. Januar 2024 | Korrekturen nach der ersten stabilen Version. |
| 6.0.2 | 29. Februar 2024 | Korrekturen; die Wurzel des Inhaltsbaums lässt sich anzeigen (März). |
| 6.0.3 | 2. April 2024 | Wartungsversion. |
| 6.0.4 | 5. September 2024 | admin3, ein vollständiges, responsives Design der Administration (eingeführt am 22. August 2024). |
| 6.0.5 | 1. Oktober 2024 | Wartungsversion. |
| 6.0.6 | 17. Dezember 2024 | admin3 nach Tests auf echten Geräten korrigiert. |
| 6.0.7 | 1. Februar 2025 | Kopieren eines Teilbaums; die letzte Version für Websites unter PHP 7.4. |
| 6.0.8 | 2. Mai 2025 | Unterstützung für PHP 8.4; die Unterstützung für PHP 7 endete. |
| 6.0.9 | 10. Juni 2025 | Weitere Arbeit an PHP 8.4 und am Headless-Einsatz. |

Im Juli 2025 wurden Lizenz und Urheberschaft mit `COPYRIGHT.md` und `LICENSE.md` ausdrücklich festgehalten (GNU GPL
Version 2 oder später).

## 5. Der Name Exponential (August 2025)

Im August 2025 bat Ibexa 7x, den geschützten Namen „eZ Publish“ für die neuen Versionen nicht mehr zu verwenden. 7x
benannte das Produkt sofort in **Exponential** um: Die Umbenennung erfolgte im Repository am 12. August 2025, und
6.0.10, die erste Version unter dem neuen Namen, folgte am 28. August 2025.

Was sich änderte und was nicht:

- **Geändert:** der Produktname in der Oberfläche, der Dokumentation, den Paketen und den Meldungen.
- **Nicht geändert:** Bezeichner, Klassennamen (`eZContentObject`, `eZINI`), Dateipfade (`bin/php/ezcache.php`),
  Datenbanktabellen (`ezcontentobject`) und Einstellungen. Jede bestehende Website, Erweiterung und jedes Template lief
  weiter.
- **Neuer Code** verwendet das Präfix `exp` (zum Beispiel `expHelp`, `exp:velocity`), damit sich die Epochen
  unterscheiden lassen.

Seitdem wird der alte Name nur noch für die Geschichte verwendet, wie auf dieser Seite.

## 6. Exponential 6.0.10 bis 6.0.14 (2025 - 2026)

| Version | Datum | Wichtigstes |
|---|---|---|
| 6.0.10 | 28. August 2025 | Die erste Version mit dem Namen Exponential. |
| 6.0.11 | 30. Dezember 2025 | Unterstützung für PHP 8.5 (nullbare Typen, aktualisierte Zeta Components). |
| 6.0.12 | 10. Februar 2026 | Wartung; im März wechselte die Testumgebung zu PHPUnit 13. |
| 6.0.13 | 20. April 2026 | Wartung und aktualisierte Erweiterungen. |
| 6.0.14 | 7. Juni 2026 | MongoDB 8 als Datenbank (eingetroffen am 1. Juni 2026). |

## 7. Die Reihe 6.0.15 (2026)

6.0.15 ist die umfangreichste Reihe in der Geschichte des Codes. Sie ist beim Schreiben dieser Seite in Entwicklung
und hat noch kein Versions-Tag; die vollständigen Hinweise stehen im
[Änderungsprotokoll von 6.0.15](../changelogs/6.0/6.0.15.md). Die wichtigsten Schritte in der Reihenfolge ihres
Eintreffens:

- **Juli 2026, die Kommandozeile wird erwachsen:** Die Paketverwaltung kam in die Konsole, ein Installer mit einem
  Befehl und der Kickstarter kamen hinzu, dazu ein Werkzeug zum Zurücksetzen von Passwörtern.
- **August 2026:** Template-Debugging, das das wirklich ausgeführte Template nennt, sechs Sicherheitspatches und Tests
  bei jeder Änderung.
- **September 2026:** lange Listen in der Administration, geblättert und sortierbar, eine Installation mit einem
  Befehl, eine Härtungsrunde und **Velocity**, ein Webserver mit dauerhaften Workern für Exponential, der am
  22. September kam und innerhalb von zwei Tagen zur empfohlenen Engine wurde.
- **Oktober 2026:** jQuery 4, das Design **admin4**, das Audit-Protokoll, `exp:ini`, eine Debug-Leiste, Inhaltsjobs und
  TinyMCE 8; PHP 8.0 wurde die älteste unterstützte Version; das **Hilfecenter** mit seinen Büchern auf Englisch und
  Deutsch; Exponential Intelligence (`expai`); die Werkzeugleiste für die Bearbeitung im Frontend `expwt`;
  Übersetzungen der Administration in 156 Sprachen, darunter Quechua, Grönländisch, Hawaiisch und Guarani.
- **November 2026:** Oracle und MongoDB neben MySQL, PostgreSQL und SQLite für jedes Website-Paket getestet, und die
  Website-Pakete mit neuen Demo-Inhalten neu gebaut.

## 8. Die Exponential Foundation und 7x

Das Urheberrecht an Exponential halten die **Exponential Foundation** (<https://foundation.exponential.earth>), die
gemeinnützige Organisation, die das Projekt für seine Nutzer und die Community betreibt, und **7x**
(<https://se7enx.com>), das Unternehmen, das die Entwicklung leitet und die Kunden betreut. Am 29. Oktober 2026 ging
das Urheberrecht von 7x allein auf die Exponential Foundation und 7x gemeinsam über, und jeder Hinweis des Projekts
wurde zu einem Hinweis: `Exponential Copyright © 1998-2026 Exponential Foundation and 7x`.

Der Hinweis des ursprünglichen Entwicklers bleibt stehen, wie es die Lizenz verlangt: Exponential wurde ursprünglich
von der eZ Systems AS als eZ Publish entwickelt, `Copyright (C) 1999 - 2014 eZ Systems AS`. Die Einzelheiten stehen in
`COPYRIGHT.md` im Wurzelverzeichnis der Installation.

## 9. Distributionen, Pakete und die Community

Rund um den Kernel pflegt das Team von se7enxweb eine Familie von Repositorys (53 davon erfasst das Verzeichnis der
Geschichte):

- **Distributionen:** Exponential 6 (dieser Kernel) und die auf Symfony gestützten Reihen der Exponential Platform:
  3.x, Platform Legacy 2.5, das Skelett der DXP 4.6, Platform v5 mit dem Nexus-Starter und die CJW-Variante von
  Nexus. Siehe die [Übersicht des Ökosystems](geschichte/oekosystem.md).
- **Die Floor-Pakete** `se7enxweb/exponential-php-floor` (Reihen 1.0 bis 1.3) bringen die gebündelten Erweiterungen,
  die zu jedem PHP-Bereich passen.
- **Website-Pakete** (ezwebin_site, ezflow_site, ezdemo_site, sevenx_site, sevenx_multisite, cjw_multisite)
  installieren vollständige Demo-Websites; im November 2026 wurden sie mit Release 15-stable und neuen Inhalten über
  Exponential neu gebaut.
- **Demos** laufen auf demo.exponential.earth und verwandten Hosts und werden regelmäßig aus dem aktuellen Code neu
  gebaut.
- **Dokumentation:** das Hilfecenter dieser Installation, doc.exponential.earth und die Archive des alten
  Community-Materials, die 7x online hält.

## 10. eZ Systems und eZ publish 1.x und 2.x (1999 - 2002)

Die eZ Systems AS wurde 1999 in Norwegen gegründet. Ihr Mitgründer Aleksander Farstad beschrieb sie als ein
Unternehmen, das sein Bruder, zwei Freunde und er selbst gründeten, zunächst als Beratung für Linux- und
Open-Source-Lösungen; er war von 1999 bis 2009 und später erneut ihr Geschäftsführer. Zeitgenössische Quellen nennen
unterschiedliche Gründungsorte: Eine Firmengeschichte nennt Kongsberg (Mai 1999), während der langjährige Hauptsitz
des Unternehmens Skien war, von wo seine Pressemitteilungen datiert waren. Später hatte das Unternehmen seinen Sitz in
Oslo.

- **Das Projekt eZ publish begann 1999.** Das Produkt war in PHP geschrieben und wurde als freie Software unter der
  GNU General Public License veröffentlicht.
- **eZ publish 1.0** erschien im November 2000.
- **eZ publish 2.x** (2.0 bis 2.2, etwa 2001 bis 2002) machte es zu einem weit verbreiteten freien CMS. Debian
  paketierte eZ publish 2.2 im Oktober 2001 und vermerkte als Lizenz die GPL Version 2. eZ Systems bot die Reihe 2.x
  später unter zwei Lizenzen an: der GPL und einer kostenpflichtigen Lizenz „eZ publish Professional“ für alle, die die
  GPL nicht annehmen konnten.
- Die Code-Basis von 2.x war nicht der Vorfahr der Dateien dieses Repositorys. Das Team begann einen neuen Entwurf,
  intern „nextgen“ genannt, aus dem 3.0 wurde.

## 11. eZ publish 3 (2002 - 2007)

eZ publish 3 ist die Architektur, die Exponential noch heute hat: der Kernel mit seinen Modulen und Ansichten
(`kernel/content`, `kernel/user` und so weiter), das Inhaltsmodell aus Klassen, Objekten, Versionen und Knoten, die
Datentypen, die INI-Einstellungen mit ihren Override-Ebenen, die Siteaccesses, die Template-Sprache mit ihren
Operatoren und Fetch-Funktionen, das Design- und Override-System, Erweiterungen, Workflows und Rollen mit Richtlinien.

Die Entwicklung begann im Subversion-Repository „nextgen“ (erster Commit am 2. September 2002). Die Betas trugen die
Nummern 2.9-1 bis 2.9-8 ([Release-Übersicht](release-uebersicht)). Die Daten unten sind die Daten der Tags in diesem
Repository; die wichtigsten Funktionen sind den Funktionshinweisen in `doc/eng-us/features/` und den
[Änderungsprotokollen](../changelogs/README.md) entnommen.

| Reihe | Erste Version | Wichtigstes |
|---|---|---|
| 3.0 | 24. März 2003 (3.0-1) | Der neue Kernel, Inhaltsklassen und -objekte, Versionen, die Template-Sprache, mehrere Websites. Betas ab 1. November 2002 (2.9-3). |
| 3.1 | 26. August 2003 | Wartung und die ersten Verbesserungen des neuen Kernels. |
| 3.2 | 16. September 2003 | Weiterentwicklung; 3.2 erhielt Korrekturen bis Mai 2004 (3.2-6). |
| 3.3 | 7. Januar 2004 | Wartungsversionen bis 2005 (3.3-7). |
| 3.4 | 2. Juni 2004 (3.4.0) | Ein neues Versionsschema mit drei Zahlen; 3.4.8 im Juli 2005. |
| 3.5 | 22. Dezember 2004 | Prüfungen beim Bearbeiten und Entfernen von Klassen, Leeren des Inhalts-Caches, eigene Navigationsteile, mehr Informationen zu Datentypen, Verbergen von Inhalten. |
| 3.6 | 30. Mai 2005 | Die Werkzeugleiste der Administration, Kopieren eines Teilbaums, erweiterte verwandte Objekte, Datenbanktransaktionen, Upload-Handler. |
| 3.7 | 12. Oktober 2005 | Siteaccess-Einstellungen in Erweiterungen. |
| 3.8 | 2. Mai 2006 | Der Freigabe-Workflow, Clustering, der Inhaltsvergleich, der Operator für Inhaltsverzeichnisse, das linke Menü der Administration, Bearbeitungs-Handler. |
| 3.9 | 18. Dezember 2006 | Klassennamen in mehreren Sprachen, gesperrte Konten nach fehlgeschlagenen Anmeldungen, alphabetische Navigation, Debugging pro Benutzer, Sortiervorgaben pro Klasse. |
| 3.10 | 9. Oktober 2007 | Verbesserungen am Cluster, mehrsprachige URL-Aliase, Unterstützung für SSL-Proxys, der Operator `ezmodule`. |

Die Reihe 3.x endete mit 3.10.1 und der letzten Version von 3.9 im August 2008.

## 12. eZ Publish 4 (2007 - 2012)

eZ Publish 4.0 war der Kernel von 3.10, umgestellt auf PHP 5. eZ Systems kündigte es am 6. Dezember 2007 aus Skien an;
das Tag in diesem Repository trägt das Datum 4. Dezember 2007. Etwa zu dieser Zeit wurde der Produktname mit großem P
geschrieben: „eZ Publish“.

| Reihe | Erste Version | Wichtigstes |
|---|---|---|
| 4.0 | 4. Dezember 2007 | PHP 5, automatisches Laden der Klassen, statischer Cache nach User-Agent; 4.0.20 im Februar 2009. |
| 4.1 | 19. März 2009 | Verbesserungen am Cache, Clustering, ein Installer für Windows, der Sprachumschalter, Ausgabefilter, Systeminformationen. |
| 4.2 | 13. November 2009 | Der Cluster-Datei-Handler eZDFS für mehrere Webserver. |
| 4.3 | 14. Juli 2010 | Mailversand, der LDAP-Login-Handler, Verbesserungen am Shop. |
| 4.4 | 29. September 2010 | `extension.xml` und die Reihenfolge der Erweiterungen, Siteaccess-Zuordnung nach Host und URI, Verbesserungen an INI und Sitzungen. |
| 4.5 | 2011 | Asynchrones Veröffentlichen, Ausschlusslisten für `autoload`, Datenbankfehler als Ausnahmen, Registrierung über einen Workflow. |
| 4.6 | etwa Ende 2011 | Unterstützung für PHP-Namensräume im Autoloader. |
| 4.7 | 2012 | Cluster-Ereignisse und der Cluster-Index. |

**Das Community Project.** Ab 2011 teilte eZ Systems das Produkt: die kostenpflichtige Ausgabe **eZ Publish
Enterprise** mit Abonnement und eine freie Ausgabe, gebaut vom **eZ Publish Community Project**. Der erste Build hieß
4.2011; ab dem zweiten Build (2011.5, erschienen im Mai 2011) wurden die Builds nach Jahr und Monat benannt
(`yyyy.mm`), zum Beispiel 2011.9, 2012.9 oder 2013.1. Abonnenten der Enterprise-Ausgabe konnten statt der GPL eine
proprietäre Lizenz wählen (siehe [Abschnitt 15](#15-lizenzen-versionsnummern-und-namen-im-lauf-der-zeit)).

## 13. eZ Publish 5, eZ Platform und Ibexa (2012 - heute)

**eZ Publish 5 (2012 - 2014).** 2012 kündigte eZ Systems an, dass die nächste Version das Full-Stack-Framework Symfony
mit Twig-Templates verwenden würde. eZ Publish 5.0 (Codename „Kilimanjaro“, Community-Build 2012.10) stellte eine neue
API für das Content-Repository und den Symfony-Stack neben den bestehenden Kernel, der von da an **Legacy-Kernel**
hieß. Beide arbeiteten mit derselben Datenbank; alte Module und Templates liefen über einen Rückfallmechanismus. 5.1
brachte die öffentlichen APIs, 5.2, 5.3 und 5.4 folgten bis 2014. Der letzte Community-Build, 2014.11, erschien am
17. November 2014, und die letzte Version von eZ Publish durch eZ Systems kam im Dezember 2014.

**eZ Platform (2015 - 2020).** eZ Systems veröffentlichte am 15. Dezember 2015 eZ Platform, den Nachfolger ohne
Legacy-Code, daneben eine Enterprise-Ausgabe. eZ Platform 2.5, eine Version mit Langzeitunterstützung, erschien im März
2019, eZ Platform 3.0 am 2. April 2020.

**Ibexa (2020 - heute).** Am 27. April 2020 gab eZ Systems seinen neuen Namen Ibexa bekannt und seine Neuausrichtung
als Anbieter einer Digital Experience Platform (DXP). Das Produkt ging als **Ibexa DXP** weiter: 3.2 am 26. Oktober
2020, 4.0 im Februar 2022, 4.6, eine Version mit Langzeitunterstützung, am 13. Februar 2024, und 5.0 im Jahr 2025.
Ibexa DXP wird in mehreren Ausgaben angeboten (Content, in 4.6 in Headless umbenannt; Experience; Commerce). Die QNTM
Group übernahm Ibexa am 19. November 2021; Ibexa fusionierte im Dezember 2024 mit Quable und kündigte im Januar 2025
eine Fusion mit Raptor an. Ibexa hat seinen Sitz in Oslo, Norwegen; seine Website ist <https://www.ibexa.co>.

Exponential ist nicht mit Ibexa verbunden. Es führt den Legacy-Kernel weiter, den eZ Systems unter der GPL
veröffentlicht hat, und nennt eZ Systems als ursprünglichen Entwickler, wie es die Lizenz verlangt.

## 14. Wie der Legacy-Kernel weiterlebte (2014 - 2023)

Nach 2014 war der Legacy-Kernel nicht mehr das Produkt von eZ Systems, aber er wurde nicht aufgegeben:

- **eZ Systems** veröffentlichte weiter Community-Versionen von `ezsystems/ezpublish-legacy` für Websites, die
  eZ Platform zusammen mit Legacy-Code über die Legacy Bridge betrieben: 2017.10 (30. Oktober 2017) bis zur Serie
  2019.03 (2019.03.0 im Mai 2019; das letzte Tag, 2019.03.6.1, im September 2021). Die interne Version „5.90“ in
  diesem Repository ist ein Alias für diese Versionen ab 2017.08. Das Repository ist heute archiviert.
- **Netgen**, eine kroatische Agentur, die viele eZ-Websites baute und später Netgen Layouts entwickelte, pflegte
  einen eigenen Fork, `netgen/ezpublish-legacy` (ab Februar 2016), „für die Kompatibilität mit eZ Platform“, mit
  Korrekturen für PHP 7.
- Commits von 2019 bis 2023 in diesem Repository zeigen diese Arbeit: Korrekturen für PHP 7.2 bis 7.4 und 2022 die
  Anhebung der erlaubten PHP-Versionen auf 7.4 und 8.1.
- **se7enxweb** forkte im Oktober 2023 Netgens Repository, und dort beginnt
  [Abschnitt 3](#3-exponential-die-übernahme-des-legacy-kernels-2023). Netgen Layouts, auf diesen Kernel portiert als
  Exponential Layouts, kam 2026 in die Code-Basis zurück.

## 15. Lizenzen, Versionsnummern und Namen im Lauf der Zeit

**Lizenzen.**

| Zeitraum | Lizenz |
|---|---|
| 1999 - 2002 (1.x, 2.x) | GNU GPL; ab 2.x zusätzlich eine kostenpflichtige Lizenz „eZ publish Professional“ |
| 2003 - 2014 (3.x bis 5.x) | GNU GPL Version 2; für Enterprise-Abonnenten zusätzlich die eZ Proprietary Use License (eZPUL) und ab Enterprise 4.7 die eZ Business Use License (eZ BUL) |
| 2015 - heute (Ibexa) | Freie und kommerzielle Ausgaben von eZ Platform und Ibexa DXP, zu ihren eigenen Bedingungen |
| 2023 - heute (Exponential) | GNU GPL Version 2 oder jede spätere Version; Dokumentation unter der GFDL 1.3 ([README-LICENSE](README-LIZENZ.md)) |

**Versionsnummern.**

- 2.9-x: die Betas von 3.0 (2002 - 2003).
- 3.0-1, 3.1-1 ... 3.3-7: die frühen Versionen von 3.x, nummeriert nach Reihe und Build.
- 3.4.0 bis 4.7.x: dreiteilige Versionen.
- 4.2011, dann `yyyy.mm` (2011.5 bis 2014.11): die Builds des Community Project.
- 5.0 bis 5.4: eZ Publish 5; 5.90: der Alias der Legacy-Versionen ab 2017.08.
- 2017.10 bis 2019.03.x: die Community-Versionen von `ezsystems/ezpublish-legacy`.
- 6.0.0 bis 6.0.15: die Reihe von se7enxweb, bis August 2025 eZ Publish 6 und seitdem Exponential.

**Namen.** „eZ publish“ (mit kleinem p) in den frühen Jahren, „eZ Publish“ ab etwa 2007, „eZ Platform“ für den
Nachfolger ab 2015, „Ibexa DXP“ ab 2020 und „Exponential“ für diesen Code seit August 2025. Das Präfix `ez` in Klassen-,
Datei- und Tabellennamen stammt aus dieser Geschichte und bleibt, weil eine Änderung jede Website und jede Erweiterung
brechen würde.

## 16. Quellen

Der Teil über Exponential und die Daten der Versionen 2.9 bis 4.4 stammen aus diesem Repository: seinen Tags
(`git for-each-ref refs/tags`), seiner Commit-Geschichte, der [Release-Übersicht](release-uebersicht), den
[Änderungsprotokollen](../changelogs/README.md), den Funktionshinweisen (`doc/eng-us/features/`), der
[Chronik](geschichte/README.md), `COPYRIGHT.md` und den GitHub-Daten der Repositorys `ezsystems/ezpublish-legacy`,
`netgen/ezpublish-legacy` und `se7enxweb/exponential`. Die übrigen Angaben stammen aus diesen öffentlichen Quellen
(geprüft am 4. November 2026):

- Wikipedia, „eZ Publish“: <https://en.wikipedia.org/wiki/EZ_Publish>
- Wikipedia, „Ibexa DXP“: <https://en.wikipedia.org/wiki/Ibexa_DXP>
- Ibexa, „eZ Systems Announces Company Name Change to Ibexa“ (April 2020): <https://www.ibexa.co/blog/ez-systems-announces-company-name-change-to-ibexa>
- Netgen, „Interview with Aleksander Farstad, founder of eZ Systems and eZ Publish open source CMS“ (5. Oktober 2011): <https://netgen.io/blog/interview-with-aleksander-farstad-founder-of-ez-systems-and-ez-publish-open-source-cms>
- SitePoint, „Interview – Bard Farstad of eZ systems“: <https://www.sitepoint.com/bard-farstad-ez-systems/>
- tek.no, Firmenprofil von eZ Systems: <https://www.tek.no/artikkel/i/opl94R/bedriftsprofil-ez-systems-norden>
- PR.com, „eZ Systems Introduces eZ Publish 4.0“ (6. Dezember 2007): <https://www.pr.com/press-release/63111>
- LinuxFr.org, „eZ Publish 4.0, première version d'eZ Publish supportant PHP5“: <https://linuxfr.org/news/ez-publish-40-premiere-version-dez-publish-supportant-php5>
- Debian, Copyright-Datei des Pakets ezpublish 2.2.8: <https://sources.debian.org/src/ezpublish/2.2.8-2/debian/copyright>
- Symfony-Blog, „Symfony2 meets eZ Publish 5“: <https://symfony.com/blog/symfony2-meets-ez-publish-5>
- CMSWire, „eZ Publish Releases Version 5.1“: <https://www.cmswire.com/cms/customer-experience/ez-publish-releases-version-51-021277.php>
- Ibexa, „eZ Business Use License Agreement (eZ BUL) version 2.1“: <https://www.ibexa.co/software-information/licenses-and-agreements/ez-business-use-license-agreement-ez-bul-version-2.1>
- Ibexa-Entwicklerportal, Produktstart von Ibexa DXP v4.0 (8. Februar 2022): <https://developers.ibexa.co/topics/uuid/39?page=3>
- Ibexa-Dokumentation, 4.6: <https://doc.ibexa.co/en/4.6>
- GitHub: <https://github.com/ezsystems/ezpublish-legacy>, <https://github.com/netgen/ezpublish-legacy>, <https://github.com/se7enxweb/exponential>

## Verwandte Seiten

- [Die Chronik, Monat für Monat](geschichte/README.md)
- [Das Ökosystem der Plattform](geschichte/oekosystem.md)
- [Branch-Geschichte](branch-uebersicht.md)
- [Release-Übersicht](release-uebersicht)
- [Distributionen von Exponential](DISTRIBUTIONS.md)
- [Exponential Community](COMMUNITY.md): wo sich die Community heute trifft, und jede öffentliche Website des Projekts
- [Exponential 6.0 installieren](INSTALL.md)
- [Glossar](glossar.md)
