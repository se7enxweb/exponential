---
title: Muster in kernel/private/classes/services
audience: developer
category: standards
order: 523
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/private/classes/services mit den Entwurfsmustern.
keywords: kernel/private/classes/services, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/private/classes/services

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/private/classes/services` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (8)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `Exponential\Service\ContentJobDetails` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/classes/services/contentjobdetails.php:17` | Was die Seiten der Inhaltsjobs über einen Job über seinen Fortschritt hinaus zeigen: wer ihn gestartet hat (und in wessen Auftrag), die Zeiten, die Dauer und eine Schätzung der… |
| `Exponential\Service\DraftsCleanup` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/classes/services/draftscleanup.php:22` | Entfernen von Entwürfen, die älter als ihre konfigurierte Lebensdauer sind: ein Dienst für die beiden Cronjob-Teile, die das mit demselben Code und unterschiedlichen Einstellungen taten, cr… |
| `Exponential\Service\MailPreferencesPage` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/classes/services/mailpreferencespage.php:27` | Was die Ansichten des Moduls mailpreferences gemeinsam haben: die Einstellungsseite (angemeldet, über einen persönlichen Link oder durch einen Administrator für einen Benutzer), die Boxen auf… |
| `Exponential\Service\ReverseRelationCheck` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/classes/services/reverserelationcheck.php:22` | Welche anderen Objekte auf den Inhalt verweisen, den ein Entfernen oder Verbergen gleich wegnimmt: die umgekehrten Relationen (ezcontentobject_link) der Objekte, die… |
| `Exponential\Service\SessionGarbageCollector` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/classes/services/sessiongarbagecollector.php:20` | Entfernen abgelaufener Sitzungen: ein Dienst für die Sitzungsansicht (setup/session, "Remove timed out sessions"), den Befehl (bin/php/ezsessiongc.php) und… |
| `Exponential\Service\Trash` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/classes/services/trash.php:23` | Leeren des Papierkorbs: ein Dienst für die Papierkorb-Ansicht (content/trash), den Befehl (bin/php/trashpurge.php) und den Cronjob-Teil (cronjobs/trashpurge.ph… |
| `Exponential\Service\TrashList` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/classes/services/trashlist.php:27` | Was die Papierkorbansicht (content/trash) über jedes Element, ihre Filter und ihre Zusammenfassungszeile zeigt. |
| `Exponential\Service\TrashRecord` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/classes/services/trashrecord.php:30` | Die Datei, in der festgehalten wurde, wer ein Objekt in den Papierkorb verschoben hat und von wo, bevor die Papierkorbzeilen die Spalten trashed_by und trashed_via hatten. |
