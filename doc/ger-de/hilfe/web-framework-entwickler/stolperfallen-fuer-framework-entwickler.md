---
title: Zehn Stolperfallen für Framework-Entwickler
audience: web-framework-developer, developer
category: web-frameworks
order: 500
summary: Die zehn Fehler, die Entwickler aus Symfony, Laravel, Django, Rails und ähnlichen Frameworks in ihrem ersten Exponential-Projekt am häufigsten machen, jeweils mit Symptom, Ursache und Lösung - von nicht maskierter Template-Ausgabe und veralteten Caches bis zur Reihenfolge der INI-Ebenen, persistenten Workern und SQL auf Inhaltstabellen.
keywords: Stolperfallen, Fehler, Fallstricke, Fehlersuche, wash, Maskieren, Caches, INI-Reihenfolge, persistente Worker, statische Eigenschaften, Autoloads, Inhaltstabellen, Berechtigungen, Siteaccess, Web-Framework-Entwickler, pitfalls, gotchas, troubleshooting, escaping, web framework developer
type: help
---
# Zehn Stolperfallen für Framework-Entwickler

Diese Seite richtet sich an Entwickler in ihren ersten Wochen mit Exponential. Jede Stolperfalle stammt aus einer
Gewohnheit, die in einem anderen Framework richtig und hier falsch ist.

## Inhalt

1. [Ausgabe ohne wash](#1-ausgabe-ohne-wash)
2. [Eine neue Klasse wird „nicht gefunden“](#2-eine-neue-klasse-wird-nicht-gefunden)
3. [Eine Einstellung wirkt nicht](#3-eine-einstellung-wirkt-nicht)
4. [Eine Template-Änderung wird nicht angezeigt](#4-eine-template-änderung-wird-nicht-angezeigt)
5. [Zustand sickert zwischen Requests durch](#5-zustand-sickert-zwischen-requests-durch)
6. [SQL in Inhaltstabellen schreiben](#6-sql-in-inhaltstabellen-schreiben)
7. [Eine Tabelle für etwas, das Inhalt sein sollte](#7-eine-tabelle-für-etwas-das-inhalt-sein-sollte)
8. [Berechtigungen nur in Templates prüfen](#8-berechtigungen-nur-in-templates-prüfen)
9. [Den Siteaccess auf der Kommandozeile vergessen](#9-den-siteaccess-auf-der-kommandozeile-vergessen)
10. [Vergleiche mit Symbolen](#10-vergleiche-mit-symbolen)

## 1. Ausgabe ohne wash

**Symptom:** Text mit `<` oder `&` zerstört die Seite, oder ein Skript wird ausgeführt. **Ursache:** Templates
maskieren nicht automatisch. **Lösung:** `{$value|wash}` auf alles, was von Benutzern oder aus der Datenbank kommt
([Die View-Schicht](templates-im-vergleich-zu-twig-und-blade.md#fünf-unterschiede-die-beißen)).

## 2. Eine neue Klasse wird „nicht gefunden“

**Ursache:** Klassen werden über erzeugte Class Maps gefunden, für Erweiterungsklassen nicht über PSR-4.
**Lösung:** `php bin/php/ezpgenerateautoloads.php -e`, dann deployen; `--kernel` für Kernel-Klassen
([Autoloads und Klassenbenennung](../ersteller/anwendungsentwickler/autoloads-und-klassenbenennung.md)).

## 3. Eine Einstellung wirkt nicht

**Ursachen:** der INI-Cache; oder der Wert wird von einer späteren Ebene überschrieben, zum Beispiel gewinnen die
globalen Einstellungen einer Erweiterung gegenüber `settings/siteaccess/`. **Lösung:**
`./console exp:ini where <file>/<block>/<key> <siteaccess>`, dann `php bin/php/ezcache.php --clear-tag=ini`
([Konfiguration](konfiguration-ini-dateien.md#die-ebenen-und-ihre-reihenfolge)).

## 4. Eine Template-Änderung wird nicht angezeigt

**Ursachen:** der View-Cache, ein Cache-Block oder der Override-Cache bei einer neuen Template-Datei.
**Lösung:** Leeren Sie `template-override` nach dem Hinzufügen einer Template-Datei, leeren Sie `content` und
`template-block`, wenn zwischengespeicherte Ausgaben veraltet sind; `exp:velocity deploy` erledigt all das in der
richtigen Reihenfolge ([Caching](cache-ebenen.md)).

## 5. Zustand sickert zwischen Requests durch

**Symptom:** Ein Benutzer sieht die Daten eines anderen Benutzers, oder ein Listener läuft zweimal, nur unter
Velocity. **Ursache:** Persistente Worker behalten statische Eigenschaften und angehängte Listener. **Lösung:** kein
Request-bezogener Zustand in statischen Eigenschaften; registrieren Sie Listener in den Einstellungen
([Server und Deployment](server-und-deployment.md#regeln-für-persistente-worker)).

## 6. SQL in Inhaltstabellen schreiben

**Symptom:** Eine Änderung erscheint nicht, oder sie erscheint ohne Versionsgeschichte, Suche oder Cache-Aktualisierung.
**Ursache:** Die Inhaltstabellen sind nicht Ihre Tabellen. **Lösung:** `eZContentFunctions` oder die Operationen, die
Workflows ausführen und Caches leeren
([Das Inhaltsmodell](das-inhaltsmodell-fuer-orm-nutzer.md#lesen-und-schreiben-aus-code)).

## 7. Eine Tabelle für etwas, das Inhalt sein sollte

**Symptom:** Sie bauen eine Administrationsseite, eine Revisionstabelle und eine Übersetzungstabelle. **Lösung:**
Machen Sie daraus eine Inhaltsklasse und erhalten Sie alle drei geschenkt
([ORM](orm-und-ezpersistentobject.md#inhaltsklasse-oder-eigene-tabelle)).

## 8. Berechtigungen nur in Templates prüfen

**Ursache:** Einen Link auszublenden ist keine Zugriffskontrolle. **Lösung:** Nennen Sie die Funktion in `functions`
der View, prüfen Sie den Objektzugriff in der View, filtern Sie Listen in der Abfrage
([Autorisierung](autorisierung-rollen-und-richtlinien.md)).

## 9. Den Siteaccess auf der Kommandozeile vergessen

**Symptom:** Ein Befehl oder Cronjob-Teil findet Ihr Modul, Ihre Einstellung oder Ihren Jobtyp nicht. **Ursache:** Ihre
Erweiterung ist nur in einem Siteaccess aktiv. **Lösung:** `-s <siteaccess>`
([Kommandozeile](cli-und-konsolenbefehle.md#siteaccess-benutzer-und-root)).

## 10. Vergleiche mit Symbolen

**Symptom:** Ein `{if}` verhält sich willkürlich, und das Log zeigt einen Parser-Fehler. **Ursache:** Die
Template-Sprache hat keine Infix-Operatoren. **Lösung:** `gt`, `lt`, `eq`, `ne`, `and`, `or`
([Kein Infix-Vergleich](../template-sprache/smarty-unterschiede-im-detail.md#keine-vergleiche-in-infixschreibweise)).

## Verwandte Seiten

- [Checklisten für Framework-Entwickler](checklisten-fuer-framework-entwickler.md)
- [Häufige Fehler in Templates](../designer/vorlagen/haeufige-template-fehler.md)
- [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
