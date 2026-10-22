---
title: Inhalte aus einem anderen CMS migrieren
audience: integrator, developer, project-manager
category: integration
order: 320
summary: Die Inhalte eines anderen Content-Management-Systems als Integrationsprojekt nach Exponential übertragen - Bestandsaufnahme, Abbildung, der Importansatz, interne Links und Medien, Benutzer, Weiterleitungen für alte Adressen, Proben, das Umstellungsfenster und die Abnahme.
keywords: Migration, Inhaltsmigration, CMS-Migration, WordPress, Drupal, TYPO3, Alt-CMS, Bestandsaufnahme, Abbildung, Import, interne Links, Medien, Benutzer, Passwort zurücksetzen, Weiterleitungen, URL-Aliase, Wildcards, Umstellung, Probe, Wartungsmodus, migration, content migration, CMS migration, legacy CMS, inventory, mapping, internal links, media, users, password reset, redirects, URL aliases, cut-over, rehearsal, maintenance mode
type: help
---
# Inhalte aus einem anderen CMS migrieren

Diese Seite ist für Integratoren, die eine Website aus einem anderen Content-Management-System nach Exponential
übertragen. Eine Migration ist eine Integration, die ein paar Mal statt jede Nacht läuft, aber sie braucht dieselbe
Disziplin: einen Schlüssel pro Datensatz, einen Probelauf, einen wiederholbaren Import und eine Prüfung. Migrationen
von früheren Versionen dieser Codebasis (4.x, 5.x Legacy) sind eine andere Aufgabe, die das Installationsbuch behandelt
([Von 4.x migrieren](../../installation/14-migration-von-4x.md)).

## Inhalt

1. [Die Phasen](#die-phasen)
2. [Bestandsaufnahme und Abbildung](#bestandsaufnahme-und-abbildung)
3. [Der Importansatz](#der-importansatz)
4. [Interne Links, Medien und Benutzer](#interne-links-medien-und-benutzer)
5. [Alte Adressen](#alte-adressen)
6. [Proben und Umstellung](#proben-und-umstellung)
7. [Abnahme](#abnahme)

## Die Phasen

| Phase | Ergebnis |
|---|---|
| 1. Bestandsaufnahme | Anzahl pro Inhaltstyp, Sprachen, Medienvolumen, Benutzer, URL-Muster der alten Website |
| 2. Abbildung | eine Abbildungstabelle pro Klasse ([Vorlage](inhaltsklassen-auf-externe-daten-abbilden.md#eine-abbildungstabelle)) und Entscheidungen darüber, was nicht migriert wird |
| 3. Extraktion | die Inhalte des alten Systems als Dateien (Datenbankexport, ein API-Dump, XML, CSV) |
| 4. Aufbau des Imports | ein wiederholbarer Import mit `remote_id` als Schlüssel (`<altes System>-<Typ>-<alte ID>`) |
| 5. Proben | mindestens zwei vollständige Läufe auf einer Kopie, mit Prüfungen, mit Zeitmessung |
| 6. Inhaltsstopp und Umstellung | der letzte Lauf, Weiterleitungen an, DNS oder Proxy umgestellt |
| 7. Abnahme und Aufräumen | Prüfungen abgezeichnet; altes System archiviert |

## Bestandsaufnahme und Abbildung

Zählen Sie, bevor Sie schätzen: Inhalte pro Typ und Sprache, die Tiefe des Baums, Anzahl und Größe der Mediendateien,
den Anteil an Rich Text mit eingebetteten Elementen (Tabellen, Einbettungen, Shortcodes), Benutzer und ihre Gruppen
sowie die URL-Muster. Jedes Muster eingebetteten Markups, das das alte System erfunden hat (Shortcodes, Makros), ist
eine Umwandlungsregel, die gebaut und getestet werden muss.

Vereinbaren Sie mit dem Kunden, was **nicht** migriert wird: veraltete Neuigkeiten, Entwürfe, Revisionen, Kommentare,
Statistiken. Jedes ausgelassene Element spart Zeit in jeder Probe.

## Der Importansatz

| Menge und Form | Ansatz |
|---|---|
| Ein paar hundert Seiten, einfache Felder | Import mit `xrowextract` aus CSV oder JSON mit Spaltenzuordnung und Vorschau ([Seite](massendatenaustausch-mit-xrowextract.md)) oder `php bin/php/ezcsvimport.php` |
| Tausende Elemente, Rich Text, Beziehungen | ein eigener Importbefehl mit `eZContentFunctions` ([Ein Importskript schreiben](ein-importskript-schreiben.md)) |
| Ein von einem Werkzeug offline vorbereiteter Baum | ein JSON-Paket, installiert mit `ezpm.php` (`install --as-drafts` zur Prüfung) ([Seite](pakete-importieren-und-exportieren.md)) |

Importieren Sie in der Reihenfolge der Abhängigkeiten: Ordner und Kategorien, dann Medien, dann Inhalte, die auf sie
verweisen, dann Beziehungen in einem zweiten Durchlauf. Veröffentlichen Sie ohne Benachrichtigung. Setzen Sie beim
Anlegen `section_id`, wo Sektionen den Zugriff steuern.

## Interne Links, Medien und Benutzer

- **Interne Links** im Rich Text verweisen auf alte Adressen. Ersetzen Sie bei der Umwandlung jeden Link auf ein
  migriertes Element durch einen Link auf das neue Objekt (Rich Text in `ezxmltext` kann auf Objekte und Knoten
  verlinken); sammeln Sie die Links, die sich nicht auflösen lassen, in einem Bericht für Redakteure.
- **Medien**: Importieren Sie Dateien zuerst, mit Remote-ID als Schlüssel, damit Inhalte auf sie verweisen können.
  Behalten Sie den Alternativtext; der Bild-Datentyp nimmt `path|alternative text`.
- **Benutzer**: Importieren Sie Benutzer als Benutzerobjekte mit ihren Gruppen. Man kann nicht davon ausgehen, dass
  Passwort-Hashes eines anderen Systems in Exponential funktionieren; planen Sie für jedes migrierte Konto ein
  Zurücksetzen des Passworts ein (die Funktion für vergessene Passwörter oder `php bin/php/resetuserpassword.php` für
  einzelne Konten), und informieren Sie die Benutzer vor der Umstellung. Migrieren Sie nur die Benutzer, die noch
  gebraucht werden.

## Alte Adressen

Jede alte Adresse, die Suchmaschinen oder andere Websites kennen, sollte mit einer dauerhaften Weiterleitung antworten:

| Muster | Verwendung |
|---|---|
| Ein paar wichtige Seiten | **Globale URL-Aliase**, die weiterleiten, unter Setup > URL-Aliase ([Links, URL-Aliase, Wildcards](../../anleitungen/urls-und-aliase.md#3-globale-url-aliase)) |
| Ein ganzes altes Pfadmuster (`/blog/*`) | **URL-Wildcards** ([derselbe Leitfaden](../../anleitungen/urls-und-aliase.md#4-url-platzhalter)) |
| Tausende einzelne alte IDs | eine Weiterleitungstabelle am Webserver, vom Import aus alter Adresse und neuem URL-Alias erzeugt |

Erzeugen Sie die Weiterleitungsliste aus dem Import selbst: Jeder Datensatz kennt seine alte Adresse und, nach dem
Veröffentlichen, den URL-Alias seines Knotens.

## Proben und Umstellung

- Führen Sie den vollständigen Import mindestens zweimal auf einer Kopie der Produktion aus; messen Sie die Zeit;
  beheben und wiederholen Sie.
- Vor dem letzten Lauf: Frieren Sie die Inhalte im alten System ein, halten Sie die Cronjob-Teile Ihrer anderen
  Integrationen an, und schalten Sie den Wartungsmodus ein, wenn die neue Website schon öffentlich ist
  (`php bin/php/maintenance.php on --message="..." --until=2h`).
- Nach dem Lauf: Bauen Sie den Suchindex neu auf, wenn die Indizierung verzögert war
  (`php bin/php/updatesearchindex.php`), leeren Sie die Caches ([Caching nach Importen](caching-nach-importen.md)),
  schalten Sie die Weiterleitungen ein und den Wartungsmodus aus, setzen Sie die Cronjob-Teile fort.

## Abnahme

- [ ] Die Anzahl pro Klasse stimmt mit der Bestandsaufnahme überein (abzüglich dessen, was vereinbart weggelassen
      wurde).
- [ ] Eine Stichprobe von Seiten pro Klasse wurde nebeneinander mit der alten Website verglichen.
- [ ] Keine unaufgelösten internen Links über dem vereinbarten Schwellenwert; der Bericht ist an die Redakteure
      übergeben.
- [ ] Jedes Bild hat seinen Alternativtext.
- [ ] Eine Stichprobe alter Adressen antwortet mit einer dauerhaften Weiterleitung auf die richtige Seite.
- [ ] Benutzer können ihr Passwort zurücksetzen und sich anmelden.
- [ ] Der Import lässt sich ohne Duplikate erneut ausführen (der zweite Lauf meldet nur Unverändertes).

## Verwandte Seiten

- [Playbook für Implementierungsprojekte](../partner/leitfaden-fuer-implementierungsprojekte.md)
- [Playbook für Integrationsprojekte](playbook-fuer-integrationsprojekte.md)
- [Migrationsreferenz](../../installation/17-migrationsreferenz.md)
- [Wartungsmodus](../../anleitungen/wartungsmodus.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
