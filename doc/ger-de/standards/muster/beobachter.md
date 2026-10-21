---
title: Beobachter / Listener / Ereignis
audience: developer
category: standards
order: 327
summary: "Code, der auf etwas reagiert, das anderswo geschehen ist, ohne dass die Quelle davon weiß: ezpEvent-Listener, Benachrichtigungs-Handler, Cache-Listener. 8 Instanzen in Exponential."
keywords: Entwurfsmuster, Beobachter, Verhalten, Musterkatalog, design pattern, observer, pattern catalogue
type: catalogue
---
# Beobachter / Listener / Ereignis

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Code, der auf etwas reagiert, das anderswo geschehen ist, ohne dass die Quelle davon weiß: ezpEvent-Listener, Benachrichtigungs-Handler, Cache-Listener.

|  |  |
| --- | --- |
| Gruppe | Verhalten |
| Instanzen | 8 |
| Erkannt an | Der Klassenname enthält Listener, Observer oder Subscriber, oder die Klasse hat Methoden attach()/notify()/addListener(); jeder Aufruf von ezpEvent notify()/filter() (siehe die Seite der Hooks). |
| Erweiterungspunkt | event.ini [Event] Listeners[]=EVENT@CALLBACK; notification.ini [NotificationEventHandlerSettings] AvailableNotificationEventTypes[]. |

## Klassen nach Bereich

### extension/cjw_newsletter (2)

[Alle Muster von extension/cjw_newsletter](bereich-extension-cjw-newsletter.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `CjwNewsletterSubscriberExport` | class | `extension/cjw_newsletter/classes/importexport/cjwnewslettersubscriberexport.php:22` | Die Abonnenten einer Liste als CSV, mit Filtern (Abonnementstatus, ein Datumsbereich auf einem gewählten Datum) und einer Auswahl von Spalten. | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SubscriberExport` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/subscriber_export.php:15` | newsletter/subscriber_export/: die Abonnenten einer Liste als CSV-Datei, mit Filtern (Status, ein Datumsbereich) und einer Auswahl von Spalten. | – |

### extension/explayouts (2)

[Alle Muster von extension/explayouts](bereich-extension-explayouts.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsSiteBundleEvent` | class | `extension/explayouts/classes/explayoutssitebundleevent.php:4` | – | – |
| `expLayoutsSiteBundleEventListener` | class | `extension/explayouts/classes/explayoutssitebundleeventlistener.php:4` | – | – |

### kernel/classes/requestclassifier (1)

[Alle Muster von kernel/classes/requestclassifier](bereich-kernel-classes-requestclassifier.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expRequestVerdictListenerInterface` | interface | `kernel/classes/requestclassifier/exprequestverdictlistenerinterface.php:18` | Wird über jedes Urteil des Anfrageklassifizierers informiert, nachdem dessen security.log-Zeile geschrieben wurde (settings/requestclassifier.ini [ClassifierSettings] Listen… | – |

### kernel/private (1)

[Alle Muster von kernel/private](bereich-kernel-private.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpEvent` | class | `kernel/private/classes/ezpevent.php:20` | Diese Klasse verarbeitet interne Kernel-Ereignisse in Exponential, auch Hooks genannt. | – |

### kernel/private/classes/clusterfilehandlers (1)

[Alle Muster von kernel/private/classes/clusterfilehandlers](bereich-kernel-private-classes-clusterfilehandlers.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZClusterEventListener` | interface | `kernel/private/classes/clusterfilehandlers/interfaces/ezclustereventlistener.php:14` | Schnittstelle, die jeder Cluster-Ereignis-Listener implementieren muss | – |

### kernel/private/classes/httpcache (1)

[Alle Muster von kernel/private/classes/httpcache](bereich-kernel-private-classes-httpcache.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpHttpCacheListener` | class | `kernel/private/classes/httpcache/ezphttpcachelistener.php:27` | Die Kernel-Seite des rollenbewussten HTTP-Caches: speichert gerenderte Inhaltsansichten, damit der frühe Ausstieg und der Elternprozess von Velocity sie ausliefern, und macht aus den… | – |
