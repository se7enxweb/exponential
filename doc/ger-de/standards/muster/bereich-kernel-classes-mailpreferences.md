---
title: Muster in kernel/classes/mailpreferences
audience: developer
category: standards
order: 504
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/mailpreferences mit den Entwurfsmustern.
keywords: kernel/classes/mailpreferences, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/mailpreferences

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/mailpreferences` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (18)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expConsentContext` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/mailpreferences/expconsentcontext.php:19` | Woher eine Änderung einer Präferenz stammt, wie das Einwilligungsprotokoll sie aufzeichnet: die Quelle (page, link, admin, import, signup, bridge, confirm, system), di… |
| `expConsentLog` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/mailpreferences/expconsentlog.php:22` | Das Einwilligungsprotokoll (Tabelle expmail_consent_log): eine Zeile pro Änderung der E-Mail-Präferenzen einer Person, mit Zeit, Kategorie, Aktion (on, off, p… |
| `expMailAddressChange` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/mailpreferences/expmailaddresschange.php:20` | Eine neue E-Mail-Adresse für ein Konto wird erst wirksam, wenn sie bestätigt ist (Double-Opt-in, mailpreferences.ini [EmailChangeSettings] Confirm=enabled,… |
| `expMailBounceReader` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/mailpreferences/expmailbouncereader.php:36` | Liest das Postfach, in dem Bounces und Beschwerden eingehen (mailpreferences.ini [BounceSettings], gesetzt in settings/override/mailpreferences.ini.append.ph… |
| `expMailCategory` | class | – | `kernel/classes/mailpreferences/expmailcategory.php:18` | Eine Kategorie von E-Mails (Inhaltsbenachrichtigungen, Newsletter, Kontosicherheit, ...): ein Wertobjekt. |
| `expMailCategoryHandler` | interface | [Schnittstelle (Vertrag)](schnittstelle.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/mailpreferences/expmailcategoryhandler.php:26` | Die optionale PHP-Klasse einer Kategorie ([Category_] HandlerClass): sie liest Daten, die vor dem Präferenzsystem existierten (Teilbaum-Benachrichtigungen, Dige… |
| `expMailCategoryRegistry` | class | [Registry](registry.md), [Singleton / instance()](singleton.md) | `kernel/classes/mailpreferences/expmailcategoryregistry.php:26` | Alle E-Mail-Kategorien, in der Reihenfolge, in der die Präferenzseite sie zeigt: |
| `expMailCategoryRow` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/mailpreferences/expmailcategoryrow.php:15` | Eine Kategorie, die im Kategorienmanager der Administrationsoberfläche angelegt oder geändert wurde (Tabelle expmail_category). |
| `expMailGate` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/mailpreferences/expmailgate.php:34` | Das zentrale Mail-Gate. |
| `expMailPendingRow` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/mailpreferences/expmailpendingrow.php:14` | Ein Double-Opt-in, das auf seine Bestätigung wartet (Tabelle expmail_pending): Art 'category' (eine eingeschaltete Newsletter- oder Marketingkategorie), 'email_change… |
| `expMailPreferenceRow` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/mailpreferences/expmailpreferencerow.php:14` | Eine gespeicherte Präferenz (Tabelle expmail_preference): ein Empfänger (recipient_key 'u:' oder 'a:'), eine Kategorie, ihr Zustand (on, off, pending) und die Häufigkeit. |
| `expMailPreferences` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/mailpreferences/expmailpreferences.php:28` | Die E-Mail-Einstellungen einer Person (expMailRecipient): der Hauptschalter "alle optionalen Mails", der Zustand jeder Kategorie (an, aus oder ausstehend, solange… |
| `expMailPreferencesService` | class | [Fassade / Dienst](fassade.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/mailpreferences/expmailpreferencesservice.php:18` | Die Abläufe rund um die Präferenzen: die Double-Opt-in-Mail und ihre Bestätigung, die Abmeldung mit einem Klick, „Link zusenden“ für Personen ohne Login,… |
| `expMailRecipient` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/mailpreferences/expmailrecipient.php:23` | Bestimmt die Person, deren Präferenzen gemeint sind: ein Benutzerkonto oder eine E-Mail-Adresse ohne Konto. |
| `expMailSecret` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/mailpreferences/expmailsecret.php:20` | Das Site-Geheimnis des E-Mail-Präferenzsystems: der Schlüssel der Links in Mails (expMailToken) und das Salt der Adress-Hashes (expMailSuppression,… |
| `expMailSenderDetails` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/mailpreferences/expmailsenderdetails.php:27` | Wer die optionale Mail sendet: der Name der Organisation und die Postanschrift, die das Mail-Gate in die Fußzeile jeder optionalen Mail setzt (CAN-SPAM, CASL und… |
| `expMailSuppression` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/mailpreferences/expmailsuppression.php:22` | Die Sperrliste (Tabelle expmail_suppression): Adressen, an die keine optionale Mail geht (Hard Bounces, Beschwerden, „von allem abmelden“, rechtliche Aufford… |
| `expMailToken` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/mailpreferences/expmailtoken.php:24` | Die Links in Mails, die ohne Login funktionieren: Abmelden (ein Klick, RFC 8058), Verwalten (die Präferenzseite) und Bestätigen (ein Double-Opt-in). |
