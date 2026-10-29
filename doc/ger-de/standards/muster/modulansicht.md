---
title: Modulansicht (Page Controller)
audience: developer
category: standards
order: 316
summary: "Jede Ansicht ist ein Page Controller: Der Front Controller leitet /MODULE/VIEW an das Skript der Ansicht weiter, das die Anfrage liest und ein Template ausgibt. 993 Instanzen in Exponential."
keywords: Entwurfsmuster, Modulansicht, Anfrageverarbeitung, Musterkatalog, design pattern, module-view, pattern catalogue
type: catalogue
---
# Modulansicht (Page Controller)

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Ansicht ist ein Page Controller: Der Front Controller leitet /MODULE/VIEW an das Skript der Ansicht weiter, das die Anfrage liest und ein Template ausgibt.

| | |
| --- | --- |
| Gruppe | Verarbeitung von Anfragen |
| Instanzen | 993 |
| Erkannt an | Ein Eintrag von $ViewList in einer module.php oder eine Klasse, die ModuleView oder Page erweitert (die ausführbare Form einer Ansicht). |
| Erweiterungspunkt | module.ini [ModuleSettings] ExtensionRepositories[] und ModuleList[]; Ansichten deklarieren die Richtlinienfunktionen, die sie brauchen. |

## Klassen nach Bereich

### extension/bccie (3)

[Alle Muster von extension/bccie](bereich-extension-bccie.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Bccie\Bccie\Doexport` | class extends ModuleView | `extension/bccie/classes/runnable/views/bccie/doexport.php:22` | Der Code von extension/bccie/modules/bccie/doexport.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Bccie\Bccie\Export` | class extends ModuleView | `extension/bccie/classes/runnable/views/bccie/export.php:22` | Der Code von extension/bccie/modules/bccie/export.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Bccie\Bccie\Overview` | class extends ModuleView | `extension/bccie/classes/runnable/views/bccie/overview.php:22` | Der Code von extension/bccie/modules/bccie/overview.php, in eine Klasse verschoben (#207 Stufe 1), in 1.1.12 als Dashboard mit einer Liste neu aufgebaut. | – |

### extension/bcgooglesitemaps (1)

[Alle Muster von extension/bcgooglesitemaps](bereich-extension-bcgooglesitemaps.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Bcgooglesitemaps\Googlesitemapdynamic\Sitemap` | class extends ModuleView | `extension/bcgooglesitemaps/classes/runnable/views/googlesitemapdynamic/sitemap.php:23` | Der Code von extension/bcgooglesitemaps/modules/googlesitemapdynamic/sitemap.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/cjw_newsletter (57)

[Alle Muster von extension/cjw_newsletter](bereich-extension-cjw-newsletter.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\AbTest` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/ab_test.php:16` | Der A/B-Betrefftest eines Versands (cjw_newsletter 4.2.0, Bereich N4 Statistik): newsletter/ab_test/. | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Approval` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/approval.php:14` | Die Freigabe einer Ausgabeversion: ihr Zustand und Verlauf, "Ask for approval" für die Redakteure, Approve und Reject (mit einem Kommentar) für die Benutzer mit… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Archive` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/archive.php:28` | Der Code von extension/cjw_newsletter/modules/newsletter/archive.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\ArticlePool` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/article_pool.php:18` | Die Artikelauswahl einer Ausgabe: die Artikel aus dem Pool der Liste der Ausgabe, gefiltert nach Klasse, Datum, Sektion, Tag und Zustand; die gewählten wer… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\ArticlePoolEdit` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/article_pool_edit.php:14` | Einen Artikelpool anlegen oder bearbeiten: die durchsuchten Knoten, die Klassen, Sektionen, Tags und Zustände, das Alter, wie viele Artikel das automatische Befüllen übernimmt und ihre… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\ArticlePoolList` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/article_pool_list.php:13` | Die Artikelpools: der globale Standard und die Pools der Listen, mit Entfernen (nach einer Bestätigung). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\BlacklistItemAdd` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/blacklist_item_add.php:26` | Der Code von extension/cjw_newsletter/modules/newsletter/blacklist_item_add.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\BlacklistItemList` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/blacklist_item_list.php:24` | Der Code von extension/cjw_newsletter/modules/newsletter/blacklist_item_list.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\BlacklistItemRemove` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/blacklist_item_remove.php:26` | Der Code von extension/cjw_newsletter/modules/newsletter/blacklist_item_remove.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Configure` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/configure.php:26` | Der Code von extension/cjw_newsletter/modules/newsletter/configure.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\ImportList` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/import_list.php:24` | Der Code von extension/cjw_newsletter/modules/newsletter/import_list.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\ImportMapping` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/import_mapping.php:16` | newsletter/import_mapping: ein CSV-Import mit einer Spaltenzuordnung. | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\ImportView` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/import_view.php:24` | Der Code von extension/cjw_newsletter/modules/newsletter/import_view.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Index` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/index.php:24` | Der Code von extension/cjw_newsletter/modules/newsletter/index.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\InterestEdit` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/interest_edit.php:13` | Legt ein Interesse an (newsletter/interest_edit/0) oder bearbeitet es. | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\InterestList` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/interest_list.php:14` | Die Interessen, die Abonnenten wählen können (cjwnl_interest): die Liste mit der Newsletter-Liste, der Quelle, dem Tag, wie viele Abonnenten sie gewählt haben, und Entfer… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Job` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/job.php:14` | Der Zustand eines Hintergrundlaufs als JSON: Status (starting, running, done, failed), das Ergebnis und die letzten Log-Zeilen. | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\MailboxEdit` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/mailbox_edit.php:26` | Der Code von extension/cjw_newsletter/modules/newsletter/mailbox_edit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\MailboxItemList` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/mailbox_item_list.php:24` | Der Code von extension/cjw_newsletter/modules/newsletter/mailbox_item_list.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\MailboxItemView` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/mailbox_item_view.php:64` | Datei durchreichen und sauber beenden | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\MailboxList` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/mailbox_list.php:26` | Der Code von extension/cjw_newsletter/modules/newsletter/mailbox_list.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\MailinAddressEdit` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/mailin_address_edit.php:14` | newsletter/mailin_address_edit/: eine Mail-in-Adresse anlegen, bearbeiten oder entfernen (cjw_newsletter 4.2.0, Bereich deliverability). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\MailinAddressList` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/mailin_address_list.php:14` | newsletter/mailin_address_list: die Mail-in-Adressen und die letzten Nachrichten, die sie erhalten haben (cjw_newsletter 4.2.0, Bereich deliverability). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\MigrationLog` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/migration_log.php:14` | newsletter/migration_log[/]: die Läufe von ext:cjw_newsletter:import-eznewsletter und die Zeilen eines Laufs (gefiltert nach (action)/ und (table)/, seitenweise m… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\O` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/o.php:16` | Das Öffnungspixel eines getrackten Newsletters (cjw_newsletter 4.2.0, Bereich N4 Statistics): newsletter/o//. | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Preview` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/preview.php:27` | Der Code von extension/cjw_newsletter/modules/newsletter/preview.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\PreviewArchive` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/preview_archive.php:28` | Der Code von extension/cjw_newsletter/modules/newsletter/preview_archive.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\PreviewAs` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/preview_as.php:17` | Die Vorschau einer Ausgabe, wie ein Abonnent sie erhält: seine Sprache, die bedingten Teile, der Interessenblock und die Platzhalter, in einem Skin, den die Lis… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\R` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/r.php:19` | Die Klick-Weiterleitung eines getrackten Newsletters (cjw_newsletter 4.2.0, Bereich N4 Statistik): newsletter/r///. | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Report` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/report.php:16` | Die Statistik der Newsletter-Versände (cjw_newsletter 4.2.0, Bereich N4 Statistics): newsletter/report listet die Versände mit ihren Zahlen, newsletter/r… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\ScheduleEdit` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/schedule_edit.php:15` | Einen wiederkehrenden Versand anlegen oder bearbeiten: die Liste, der Modus (eine Vorlagenausgabe kopieren oder die neueste ungesendete Ausgabe senden), wann (gewählte Wochentage, wöchentlich, monat… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\ScheduleList` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/schedule_list.php:14` | Die wiederkehrenden Versände: die Liste mit dem nächsten und dem letzten Lauf, Pausieren, Fortsetzen, Jetzt ausführen (auch als Probelauf), Entfernen und die letzten Läufe aller Zeitpläne. | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Send` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/send.php:24` | Der Code von extension/cjw_newsletter/modules/newsletter/send.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SendAbort` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/send_abort.php:24` | Der Code von extension/cjw_newsletter/modules/newsletter/send_abort.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Settings` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/settings.php:24` | Der Code von extension/cjw_newsletter/modules/newsletter/settings.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SkinPreview` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/skin_preview.php:14` | Die Skins: ihre Namen, Beschreibungen und Vorschaubilder sowie ein Skin, mit einer Ausgabe gerendert (der neuesten oder /(edition)/), im HTML- oder im Te… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SmsConfirm` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/sms_confirm.php:17` | Die öffentliche Seite, auf der ein Abonnent den Bestätigungscode seiner Mobilnummer eingibt: newsletter/sms_confirm/ | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SmsInbound` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/sms_inbound.php:18` | Der Endpunkt, den ein SMS-Anbieter mit einer eingehenden SMS aufruft: newsletter/sms_inbound/. | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SmsSend` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/sms_send.php:17` | Versendet eine Ausgabe per SMS: newsletter/sms_send/. | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\StatisticsExport` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/statistics_export.php:18` | Die Statistik als CSV (cjw_newsletter 4.2.0, Bereich N4 Statistics): newsletter/statistics_export eine Zeile je Versand newsletter/statistics_export/ die Zeile… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Subscribe` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/subscribe.php:24` | Der Code von extension/cjw_newsletter/modules/newsletter/subscribe.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SubscribeInfomail` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/subscribe_infomail.php:24` | Der Code von extension/cjw_newsletter/modules/newsletter/subscribe_infomail.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SubscriberExport` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/subscriber_export.php:15` | newsletter/subscriber_export/: die Abonnenten einer Liste als CSV-Datei, mit Filtern (Status, ein Datumsbereich) und einer Auswahl von Spalten. | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SubscriptionList` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/subscription_list.php:24` | Der Code von extension/cjw_newsletter/modules/newsletter/subscription_list.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SubscriptionListCsvexport` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/subscription_list_csvexport.php:102` | Daten für den CSV-Export abrufen | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SubscriptionListCsvimport` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/subscription_list_csvimport.php:27` | Der Code von extension/cjw_newsletter/modules/newsletter/subscription_list_csvimport.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SubscriptionView` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/subscription_view.php:24` | Der Code von extension/cjw_newsletter/modules/newsletter/subscription_view.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SuppressionImport` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/suppression_import.php:14` | newsletter/suppression_import: eine CSV-Datei mit Adressen in die Sperrliste des Kernels, zuerst geprüft (Probelauf) (cjw_newsletter 4.2.0, Bereich delivera… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\TestGroupEdit` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/test_group_edit.php:13` | newsletter/test_group_edit/: eine Testgruppe anlegen, bearbeiten oder entfernen (cjw_newsletter 4.2.0, Bereich deliverability). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\TestGroupList` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/test_group_list.php:13` | newsletter/test_group_list: die Testgruppen der Listen (cjw_newsletter 4.2.0, Bereich deliverability). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Throttle` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/throttle.php:14` | newsletter/throttle: die Ratenlimits, Pausen und Stapel der Transporte sowie die Einträge, die auf einen erneuten Versuch nach einem Soft Bounce warten (cjw_newsletter 4.2.0, Be… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Unsubscribe` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/unsubscribe.php:24` | Der Code von extension/cjw_newsletter/modules/newsletter/unsubscribe.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\UserCreate` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/user_create.php:26` | Der Code von extension/cjw_newsletter/modules/newsletter/user_create.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\UserEdit` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/user_edit.php:26` | Der Code von extension/cjw_newsletter/modules/newsletter/user_edit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\UserList` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/user_list.php:24` | Der Code von extension/cjw_newsletter/modules/newsletter/user_list.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\UserRemove` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/user_remove.php:24` | Der Code von extension/cjw_newsletter/modules/newsletter/user_remove.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\UserView` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/user_view.php:24` | Der Code von extension/cjw_newsletter/modules/newsletter/user_view.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/expchangeclass (3)

[Alle Muster von extension/expchangeclass](bereich-extension-expchangeclass.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Expchangeclass\Changeclass\Action` | class extends ModuleView | `extension/expchangeclass/classes/runnable/views/changeclass/action.php:43` | Der Code von extension/expchangeclass/modules/changeclass/action.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Expchangeclass\Changeclass\MapAttributes` | class extends ModuleView | `extension/expchangeclass/classes/runnable/views/changeclass/map_attributes.php:10` | Der Code von extension/expchangeclass/modules/changeclass/map_attributes.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Expchangeclass\Changeclass\SelectClass` | class extends ModuleView | `extension/expchangeclass/classes/runnable/views/changeclass/select_class.php:10` | Der Code von extension/expchangeclass/modules/changeclass/select_class.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/expimageeditor (1)

[Alle Muster von extension/expimageeditor](area-extension-expimageeditor.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Expimageeditor\Expimageeditor\Save` | class extends ModuleView | `extension/expimageeditor/classes/runnable/views/expimageeditor/save.php:20` | expimageeditor/save//: nimmt das bearbeitete Bild entgegen (POST, Feld „image“, mit dem Formular-Token im Header X-CSRF-Token oder im Feld ezxform_token), speichert… | – |

### extension/explayouts (9)

[Alle Muster von extension/explayouts](bereich-extension-explayouts.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Explayouts\Explayouts\BlockEdit` | class extends ModuleView | `extension/explayouts/classes/runnable/views/explayouts/block_edit.php:10` | Der Code von extension/explayouts/modules/explayouts/block_edit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Explayouts\Explayouts\Dashboard` | class extends ModuleView | `extension/explayouts/classes/runnable/views/explayouts/dashboard.php:22` | Der Code von extension/explayouts/modules/explayouts/dashboard.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Explayouts\Explayouts\LayoutEdit` | class extends ModuleView | `extension/explayouts/classes/runnable/views/explayouts/layout_edit.php:77` | Der Code von extension/explayouts/modules/explayouts/layout_edit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Explayouts\Explayouts\LayoutList` | class extends ModuleView | `extension/explayouts/classes/runnable/views/explayouts/layout_list.php:10` | Der Code von extension/explayouts/modules/explayouts/layout_list.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Explayouts\Explayouts\Preview` | class extends ModuleView | `extension/explayouts/classes/runnable/views/explayouts/preview.php:10` | Der Code von extension/explayouts/modules/explayouts/preview.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Explayouts\Explayouts\RuleEdit` | class extends ModuleView | `extension/explayouts/classes/runnable/views/explayouts/rule_edit.php:10` | Der Code von extension/explayouts/modules/explayouts/rule_edit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Explayouts\Explayouts\RuleList` | class extends ModuleView | `extension/explayouts/classes/runnable/views/explayouts/rule_list.php:10` | Der Code von extension/explayouts/modules/explayouts/rule_list.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Explayouts\Explayouts\Setup` | class extends ModuleView | `extension/explayouts/classes/runnable/views/explayouts/setup.php:10` | Der Code von extension/explayouts/modules/explayouts/setup.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Explayouts\Explayouts\TemplateEditor` | class extends ModuleView | `extension/explayouts/classes/runnable/views/explayouts/template_editor.php:27` | Der Code von extension/explayouts/modules/explayouts/template_editor.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/explayouts_content_browser_ui (1)

[Alle Muster von extension/explayouts_content_browser_ui](bereich-extension-explayouts-content-browser-ui.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\ExplayoutsContentBrowserUi\ExplayoutsContentBrowserUi\Browser` | class extends ModuleView | `extension/explayouts_content_browser_ui/classes/runnable/views/explayouts_content_browser_ui/browser.php:10` | Der Code von extension/explayouts_content_browser_ui/modules/explayouts_content_browser_ui/browser.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/explayouts_ui (14)

[Alle Muster von extension/explayouts_ui](bereich-extension-explayouts-ui.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\BlockEdit` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/block_edit.php:10` | Der Code von extension/explayouts_ui/modules/explayouts_ui/block_edit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\Components` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/components.php:42` | Wandelt einen view_type-Bezeichner wie „features_style_2“ in eine lesbare Stilbezeichnung um. | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\Dashboard` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/dashboard.php:22` | Der Code von extension/explayouts_ui/modules/explayouts_ui/dashboard.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\LayoutCreate` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/layout_create.php:10` | Der Code von extension/explayouts_ui/modules/explayouts_ui/layout_create.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\LayoutEdit` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/layout_edit.php:77` | Der Code von extension/explayouts_ui/modules/explayouts_ui/layout_edit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\LayoutList` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/layout_list.php:91` | Der Code von extension/explayouts_ui/modules/explayouts_ui/layout_list.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\Preview` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/preview.php:105` | Wählen Sie einen Knoten, gegen den das Layout in der Vorschau angezeigt wird. | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\RuleEdit` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/rule_edit.php:10` | Der Code von extension/explayouts_ui/modules/explayouts_ui/rule_edit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\RuleList` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/rule_list.php:10` | Der Code von extension/explayouts_ui/modules/explayouts_ui/rule_list.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\Setup` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/setup.php:10` | Der Code von extension/explayouts_ui/modules/explayouts_ui/setup.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\SharedLayoutsList` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/shared_layouts_list.php:91` | Der Code von extension/explayouts_ui/modules/explayouts_ui/shared_layouts_list.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\TemplateEditor` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/template_editor.php:40` | explayouts_ui/template_editor: der Template-Editor der Layout-Templates innerhalb der Seite der Administrationsoberfläche (oberes Menü, Breadcrumb, die linke Layouts-… | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\TransferExport` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/transfer_export.php:16` | explayouts_ui/transfer_export: exportiert Layouts, geteilte Layouts und Regeln in eine Layout-Transferdatei (expLayoutsCoreTransferService) und sendet sie als… | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\TransferImport` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/transfer_import.php:18` | explayouts_ui/transfer_import: importiert eine Layout-Transferdatei (expLayoutsCoreTransferService). | – |

### extension/explayouts_ui_api (4)

[Alle Muster von extension/explayouts_ui_api](bereich-extension-explayouts-ui-api.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\ExplayoutsUiApi\ExplayoutsUiApi\App` | class extends ModuleView | `extension/explayouts_ui_api/classes/runnable/views/explayouts_ui_api/app.php:10` | Der Code von extension/explayouts_ui_api/modules/explayouts_ui_api/app.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\ExplayoutsUiApi\ExplayoutsUiApi\Blocks` | class extends ModuleView | `extension/explayouts_ui_api/classes/runnable/views/explayouts_ui_api/blocks.php:38` | Der Code von extension/explayouts_ui_api/modules/explayouts_ui_api/blocks.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\ExplayoutsUiApi\ExplayoutsUiApi\Layouts` | class extends ModuleView | `extension/explayouts_ui_api/classes/runnable/views/explayouts_ui_api/layouts.php:31` | Der Code von extension/explayouts_ui_api/modules/explayouts_ui_api/layouts.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\ExplayoutsUiApi\ExplayoutsUiApi\Rules` | class extends ModuleView | `extension/explayouts_ui_api/classes/runnable/views/explayouts_ui_api/rules.php:50` | Der Code von extension/explayouts_ui_api/modules/explayouts_ui_api/rules.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/expui (1)

[Alle Muster von extension/expui](bereich-extension-expui.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Expui\Expui\Test` | class extends ModuleView | `extension/expui/classes/runnable/views/expui/test.php:23` | Der Code von extension/expui/modules/expui/test.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/ezie (13)

[Alle Muster von extension/ezie](bereich-extension-ezie.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Ezie\Ezie\FilterBrightness` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/filter_brightness.php:23` | Der Code von extension/ezie/modules/ezie/filter_brightness.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\FilterBw` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/filter_bw.php:23` | Der Code von extension/ezie/modules/ezie/filter_bw.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\FilterContrast` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/filter_contrast.php:23` | Der Code von extension/ezie/modules/ezie/filter_contrast.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\FilterSepia` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/filter_sepia.php:23` | Der Code von extension/ezie/modules/ezie/filter_sepia.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\NoSaveAndQuit` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/no_save_and_quit.php:26` | Der Code von extension/ezie/modules/ezie/no_save_and_quit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\Prepare` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/prepare.php:24` | Der Code von extension/ezie/modules/ezie/prepare.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\SaveAndQuit` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/save_and_quit.php:27` | Der Code von extension/ezie/modules/ezie/save_and_quit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\ToolCrop` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/tool_crop.php:23` | Der Code von extension/ezie/modules/ezie/tool_crop.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\ToolFlipHor` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/tool_flip_hor.php:23` | Der Code von extension/ezie/modules/ezie/tool_flip_hor.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\ToolFlipVer` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/tool_flip_ver.php:23` | Der Code von extension/ezie/modules/ezie/tool_flip_ver.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\ToolPixelate` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/tool_pixelate.php:23` | Der Code von extension/ezie/modules/ezie/tool_pixelate.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\ToolRotation` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/tool_rotation.php:23` | Der Code von extension/ezie/modules/ezie/tool_rotation.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\ToolWatermark` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/tool_watermark.php:23` | Der Code von extension/ezie/modules/ezie/tool_watermark.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/ezjscore (3)

[Alle Muster von extension/ezjscore](bereich-extension-ezjscore.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Ezjscore\Ezjscore\Call` | class extends ModuleView | `extension/ezjscore/classes/runnable/views/ezjscore/call.php:89` | Der Code von extension/ezjscore/modules/ezjscore/call.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezjscore\Ezjscore\Hello` | class extends ModuleView | `extension/ezjscore/classes/runnable/views/ezjscore/hello.php:43` | Der Code von extension/ezjscore/modules/ezjscore/hello.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezjscore\Ezjscore\Run` | class extends ModuleView | `extension/ezjscore/classes/runnable/views/ezjscore/run.php:79` | Der Code von extension/ezjscore/modules/ezjscore/run.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/ezmultiupload (2)

[Alle Muster von extension/ezmultiupload](bereich-extension-ezmultiupload.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Ezmultiupload\Ezmultiupload\Service` | class extends ModuleView | `extension/ezmultiupload/classes/runnable/views/ezmultiupload/service.php:23` | ezmultiupload/service: die Prüfungen und Aktionen der Upload-Seite, als JSON beantwortet. | – |
| `Exponential\View\Extension\Ezmultiupload\Ezmultiupload\Upload` | class extends ModuleView | `extension/ezmultiupload/classes/runnable/views/ezmultiupload/upload.php:23` | Der Code von extension/ezmultiupload/modules/ezmultiupload/upload.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/ezodf (2)

[Alle Muster von extension/ezodf](bereich-extension-ezodf.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Ezodf\Ezodf\Export` | class extends ModuleView | `extension/ezodf/classes/runnable/views/ezodf/export.php:71` | Der Code von extension/ezodf/modules/ezodf/export.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezodf\Ezodf\Import` | class extends ModuleView | `extension/ezodf/classes/runnable/views/ezodf/import.php:44` | Der Code von extension/ezodf/modules/ezodf/import.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/ezoe (7)

[Alle Muster von extension/ezoe](bereich-extension-ezoe.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Ezoe\Ezoe\Dialog` | class extends ModuleView | `extension/ezoe/classes/runnable/views/ezoe/dialog.php:10` | Der Code von extension/ezoe/modules/ezoe/dialog.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezoe\Ezoe\EmbedView` | class extends ModuleView | `extension/ezoe/classes/runnable/views/ezoe/embed_view.php:10` | Der Code von extension/ezoe/modules/ezoe/embed_view.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezoe\Ezoe\Load` | class extends ModuleView | `extension/ezoe/classes/runnable/views/ezoe/load.php:10` | Der Code von extension/ezoe/modules/ezoe/load.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezoe\Ezoe\Relations` | class extends ModuleView | `extension/ezoe/classes/runnable/views/ezoe/relations.php:10` | Der Code von extension/ezoe/modules/ezoe/relations.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezoe\Ezoe\SpellcheckRpc` | class extends ModuleView | `extension/ezoe/classes/runnable/views/ezoe/spellcheck_rpc.php:58` | Gibt einen Anfragewert anhand des Namens ohne Magic Quoting zurück. | – |
| `Exponential\View\Extension\Ezoe\Ezoe\Tags` | class extends ModuleView | `extension/ezoe/classes/runnable/views/ezoe/tags.php:11` | Der Code von extension/ezoe/modules/ezoe/tags.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezoe\Ezoe\Upload` | class extends ModuleView | `extension/ezoe/classes/runnable/views/ezoe/upload.php:10` | Der Code von extension/ezoe/modules/ezoe/upload.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/ezpaypal (1)

[Alle Muster von extension/ezpaypal](bereich-extension-ezpaypal.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Ezpaypal\Paypal\NotifyUrl` | class extends ModuleView | `extension/ezpaypal/classes/runnable/views/paypal/notify_url.php:38` | Der Code von extension/ezpaypal/modules/paypal/notify_url.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/eztags (17)

[Alle Muster von extension/eztags](bereich-extension-eztags.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Eztags\Tags\Add` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/add.php:15` | Der Code von extension/eztags/modules/tags/add.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Addsynonym` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/addsynonym.php:15` | Der Code von extension/eztags/modules/tags/addsynonym.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Dashboard` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/dashboard.php:10` | Der Code von extension/eztags/modules/tags/dashboard.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Delete` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/delete.php:15` | Der Code von extension/eztags/modules/tags/delete.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Deletesynonym` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/deletesynonym.php:15` | Der Code von extension/eztags/modules/tags/deletesynonym.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Deletetags` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/deletetags.php:15` | Der Code von extension/eztags/modules/tags/deletetags.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Edit` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/edit.php:15` | Der Code von extension/eztags/modules/tags/edit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Editsynonym` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/editsynonym.php:15` | Der Code von extension/eztags/modules/tags/editsynonym.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Id` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/id.php:15` | Der Code von extension/eztags/modules/tags/id.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Eztags\Tags\ListObjects` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/list_objects.php:15` | Der Code von extension/eztags/modules/tags/list_objects.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Makesynonym` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/makesynonym.php:15` | Der Code von extension/eztags/modules/tags/makesynonym.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Merge` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/merge.php:15` | Der Code von extension/eztags/modules/tags/merge.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Movetags` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/movetags.php:15` | Der Code von extension/eztags/modules/tags/movetags.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Search` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/search.php:10` | Der Code von extension/eztags/modules/tags/search.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Translation` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/translation.php:15` | Der Code von extension/eztags/modules/tags/translation.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Treemenu` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/treemenu.php:15` | Der Code von extension/eztags/modules/tags/treemenu.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Eztags\Tags\View` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/view.php:15` | Der Code von extension/eztags/modules/tags/view.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/ezupdate (8)

[Alle Muster von extension/ezupdate](bereich-extension-ezupdate.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Ezupdate\Update\Browse` | class extends ModuleView | `extension/ezupdate/classes/runnable/views/update/browse.php:22` | Der Code von extension/ezupdate/modules/update/browse.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezupdate\Update\Dashboard` | class extends ModuleView | `extension/ezupdate/classes/runnable/views/update/dashboard.php:23` | Der Code von extension/ezupdate/modules/update/dashboard.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezupdate\Update\Fund` | class extends ModuleView | `extension/ezupdate/classes/runnable/views/update/fund.php:27` | Der Code von extension/ezupdate/modules/update/fund.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezupdate\Update\Installed` | class extends ModuleView | `extension/ezupdate/classes/runnable/views/update/installed.php:24` | Der Code von extension/ezupdate/modules/update/installed.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezupdate\Update\Job` | class extends ModuleView | `extension/ezupdate/classes/runnable/views/update/job.php:23` | Der Code von extension/ezupdate/modules/update/job.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezupdate\Update\Package` | class extends ModuleView | `extension/ezupdate/classes/runnable/views/update/package.php:22` | Der Code von extension/ezupdate/modules/update/package.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezupdate\Update\Packages` | class extends ModuleView | `extension/ezupdate/classes/runnable/views/update/packages.php:22` | Der Code von extension/ezupdate/modules/update/packages.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezupdate\Update\Servers` | class extends ModuleView | `extension/ezupdate/classes/runnable/views/update/servers.php:22` | Der Code von extension/ezupdate/modules/update/servers.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/ezwt (2)

[Alle Muster von extension/ezwt](bereich-extension-ezwt.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Ezwt\Websitetoolbar\Sort` | class extends ModuleView | `extension/ezwt/classes/runnable/views/websitetoolbar/sort.php:34` | Der Code von extension/ezwt/modules/websitetoolbar/sort.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezwt\Websitetoolbar\Websitetoolbar` | class extends ModuleView | `extension/ezwt/classes/runnable/views/websitetoolbar/websitetoolbar.php:33` | Der Code von extension/ezwt/modules/websitetoolbar/websitetoolbar.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/git_manager (5)

[Alle Muster von extension/git_manager](bereich-extension-git-manager.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\GitManager\GitManager\Backup` | class extends ModuleView | `extension/git_manager/classes/runnable/views/git_manager/backup.php:34` | Die Ansicht git_manager/backup: die Backups, wie aktuell sie sind, und die Formulare, die sie anlegen und entfernen. | – |
| `Exponential\View\Extension\GitManager\GitManager\CommitDetails` | class extends ModuleView | `extension/git_manager/classes/runnable/views/git_manager/commit_details.php:84` | Ausgabe von „git log -1 -p“, wie die Seite sie zeigt: die Kopffelder, die Nachricht und jede geänderte Datei mit typisierten Zeilen (add, del, hunk, meta, contex… | – |
| `Exponential\View\Extension\GitManager\GitManager\Dashboard` | class extends ModuleView | `extension/git_manager/classes/runnable/views/git_manager/dashboard.php:19` | Der Code von extension/git_manager/modules/git_manager/dashboard.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\GitManager\GitManager\Download` | class extends ModuleView | `extension/git_manager/classes/runnable/views/git_manager/download.php:30` | Die Ansicht git_manager/download//: ein Archiv eines Backups. | – |
| `Exponential\View\Extension\GitManager\GitManager\Dump` | class extends ModuleView | `extension/git_manager/classes/runnable/views/git_manager/dump.php:23` | Der Code von extension/git_manager/modules/git_manager/dump.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/powercontent (1)

[Alle Muster von extension/powercontent](bereich-extension-powercontent.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Powercontent\Powercontent\Action` | class extends ModuleView | `extension/powercontent/classes/runnable/views/powercontent/action.php:10` | Der Code von extension/powercontent/modules/powercontent/action.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/sevenx_dse (15)

[Alle Muster von extension/sevenx_dse](bereich-extension-sevenx-dse.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `AdminNeo\ClickHouseConnection` | class extends Connection | `extension/sevenx_dse/adminneo/drivers/clickhouse.inc.php:14` | – | – |
| `AdminNeo\DummyConnection` | class extends Connection | `extension/sevenx_dse/adminneo/core/DummyConnection.php:5` | – | – |
| `AdminNeo\ElasticConnection` | class extends Connection | `extension/sevenx_dse/adminneo/drivers/elastic.inc.php:15` | – | – |
| `AdminNeo\MongoConnection` | class extends Connection | `extension/sevenx_dse/adminneo/drivers/mongo.inc.php:23` | – | – |
| `AdminNeo\MsSqlConnection` | class extends Connection | `extension/sevenx_dse/adminneo/drivers/mssql.inc.php:20` | – | – |
| `AdminNeo\MsSqlPdoConnection` | abstract class extends PdoConnection | `extension/sevenx_dse/adminneo/drivers/mssql.inc.php:236` | – | – |
| `AdminNeo\MySqlConnection` | class extends Connection | `extension/sevenx_dse/adminneo/drivers/mysql.inc.php:19` | – | – |
| `AdminNeo\OracleConnection` | class extends Connection | `extension/sevenx_dse/adminneo/drivers/oracle.inc.php:14` | – | – |
| `AdminNeo\PdoConnection` | abstract class extends Connection | `extension/sevenx_dse/adminneo/include/pdo.inc.php:11` | – | – |
| `AdminNeo\PgSqlConnection` | class extends Connection | `extension/sevenx_dse/adminneo/drivers/pgsql.inc.php:14` | – | – |
| `AdminNeo\SimpleDbConnection` | class extends Connection | `extension/sevenx_dse/adminneo/drivers/simpledb.inc.php:14` | – | – |
| `AdminNeo\SqLiteConnection` | class extends SqLiteConnectionBase | `extension/sevenx_dse/adminneo/drivers/sqlite.inc.php:129` | – | – |
| `AdminNeo\SqLiteConnectionBase` | abstract class extends Connection | `extension/sevenx_dse/adminneo/drivers/sqlite.inc.php:18` | – | – |
| `Exponential\View\Extension\SevenxDse\Dse\Adminneo` | class extends ModuleView | `extension/sevenx_dse/classes/runnable/views/dse/adminneo.php:28` | Der Code von extension/sevenx_dse/modules/dse/adminneo.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\SevenxDse\Dse\Dashboard` | class extends ModuleView | `extension/sevenx_dse/classes/runnable/views/dse/dashboard.php:50` | Der Code von extension/sevenx_dse/modules/dse/dashboard.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/sevenx_themes_media (2)

[Alle Muster von extension/sevenx_themes_media](bereich-extension-sevenx-themes-media.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\SevenxThemesMedia\InfoCollection\Submit` | class extends ModuleView | `extension/sevenx_themes_media/classes/runnable/views/info-collection/submit.php:10` | Der Code von extension/sevenx_themes_media/modules/info-collection/submit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\SevenxThemesMedia\InfoCollection\Viewmodal` | class extends ModuleView | `extension/sevenx_themes_media/classes/runnable/views/info-collection/viewmodal.php:10` | Der Code von extension/sevenx_themes_media/modules/info-collection/viewmodal.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/xrowextract (15)

[Alle Muster von extension/xrowextract](bereich-extension-xrowextract.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Archive` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/archive.php:19` | Der Code von extension/xrowextract/modules/xrowextract/archive.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Browse` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/browse.php:42` | Der Code von extension/xrowextract/modules/xrowextract/browse.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\BrowseFile` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/browse_file.php:30` | Der Code von extension/xrowextract/modules/xrowextract/browse_file.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Compare` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/compare.php:25` | Der Code von extension/xrowextract/modules/xrowextract/compare.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Csv` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/csv.php:95` | Einen Export so zurücklesen, wie es eine Tabellenkalkulation tut (gleiches Trennzeichen und gleiche Anführungszeichen), für die Vorschautabelle: Kopfzeile, Zeilen mit einer Markierung pro Zelle und was die Vor… | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Destinations` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/destinations.php:19` | Der Code von extension/xrowextract/modules/xrowextract/destinations.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\History` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/history.php:20` | Der Code von extension/xrowextract/modules/xrowextract/history.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Import` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/import.php:21` | Der Code von extension/xrowextract/modules/xrowextract/import.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\JobDownload` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/job_download.php:22` | Der Code von extension/xrowextract/modules/xrowextract/job_download.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Jobs` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/jobs.php:19` | Der Code von extension/xrowextract/modules/xrowextract/jobs.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\JobStatus` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/job_status.php:19` | Der Code von extension/xrowextract/modules/xrowextract/job_status.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Package` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/package.php:24` | Der Code von extension/xrowextract/modules/xrowextract/package.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Schedules` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/schedules.php:21` | Der Code von extension/xrowextract/modules/xrowextract/schedules.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\SubtreeClasses` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/subtree_classes.php:13` | Der Code von extension/xrowextract/modules/xrowextract/subtree_classes.php: die Klassen unterhalb eines Knotens für Advanced › Export › "Subtr… | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\UploadChunk` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/upload_chunk.php:23` | Der Code von extension/xrowextract/modules/xrowextract/upload_chunk.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/xrowmetadata (2)

[Alle Muster von extension/xrowmetadata](bereich-extension-xrowmetadata.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Xrowmetadata\Sitemaps\Index` | class extends ModuleView | `extension/xrowmetadata/classes/runnable/views/sitemaps/index.php:65` | Der Code von extension/xrowmetadata/modules/sitemaps/index.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Xrowmetadata\Sitemaps\Robots` | class extends ModuleView | `extension/xrowmetadata/classes/runnable/views/sitemaps/robots.php:10` | Der Code von extension/xrowmetadata/modules/sitemaps/robots.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### kernel/private/classes/views (272)

[Alle Muster von kernel/private/classes/views](bereich-kernel-private-classes-views.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Kernel\Audit\Alerts` | class extends ModuleView | `kernel/private/classes/views/audit/alerts.php:17` | Die Ansicht audit/alerts (doc/eng-us/bc/6.0/audit.md, „Views“, „Alerts (F5)“): die Alarme, die ausgelöst wurden (system.audit.alert-Datensätze: Regel, Gruppe, Anzahl, Fen… | – |
| `Exponential\View\Kernel\Audit\Archives` | class extends ModuleView | `kernel/private/classes/views/audit/archives.php:17` | Die Ansicht audit/archives (doc/eng-us/bc/6.0/audit.md, „Views“, „Rotation, archives and retention“): je Kanal seine Live-Dateien (Anzahl, Größe, älteste),… | – |
| `Exponential\View\Kernel\Audit\Charts` | class extends ModuleView | `kernel/private/classes/views/audit/charts.php:16` | Die Ansicht audit/charts (doc/eng-us/bc/6.0/audit.md, „Views“): Ereignisse pro Tag und Kanal, Ablehnungen und Fehler pro Tag, Anmeldungen gegenüber fehlgeschlagenen Anmeldungen… | – |
| `Exponential\View\Kernel\Audit\Console` | class extends ModuleView | `kernel/private/classes/views/audit/console.php:17` | Die Ansicht audit/console (doc/eng-us/bc/6.0/audit.md, „Views“): die Zeitleiste der Audit-Datensätze, neueste zuerst, mit Filtern, Suche und Blättern, und d… | – |
| `Exponential\View\Kernel\Audit\Dashboard` | class extends ModuleView | `kernel/private/classes/views/audit/dashboard.php:22` | Die Ansicht audit/dashboard (doc/eng-us/bc/6.0/audit.md, „The console“, „Views“): die Zusammenfassung, die die Ansichten des Audits verbindet, in Karten, die jeweils… | – |
| `Exponential\View\Kernel\Audit\Event` | class extends ModuleView | `kernel/private/classes/views/audit/event.php:17` | Die Ansicht audit/event (doc/eng-us/bc/6.0/audit.md, „Views“): ein Datensatz vollständig: wann, wer, die Anfrage, Objekt und Ziel mit Links in die Administration,… | – |
| `Exponential\View\Kernel\Audit\Export` | class extends ModuleView | `kernel/private/classes/views/audit/export.php:28` | Die Ansicht audit/export (doc/eng-us/bc/6.0/audit.md, „Views“): die Datensätze des Konsolenfilters als Download, neueste zuerst: /(format)/csv (eine Zeile… | – |
| `Exponential\View\Kernel\Audit\Recent` | class extends ModuleView | `kernel/private/classes/views/audit/recent.php:17` | Die Ansicht audit/recent: die letzten 100 Audit-Ereignisse jedes Kanals, den der Benutzer lesen darf (oder eines, /(channel)/access), mit Name, Zeit, Akteur, Objekt,… | – |
| `Exponential\View\Kernel\Audit\Settings` | class extends ModuleView | `kernel/private/classes/views/audit/settings.php:18` | Die Ansicht audit/settings (doc/eng-us/bc/6.0/audit.md, „Views“, „The settings reference“): die wirksame audit.ini dieses Siteaccess, jeder Wert mit… | – |
| `Exponential\View\Kernel\ContentClass\Classlist` | class extends ModuleView | `kernel/private/classes/views/class/classlist.php:21` | Der Code von kernel/class/classlist.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\ContentClass\Copy` | class extends ModuleView | `kernel/private/classes/views/class/copy.php:21` | Der Code von kernel/class/copy.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\ContentClass\Edit` | class extends ModuleView | `kernel/private/classes/views/class/edit.php:21` | Der Code von kernel/class/edit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\ContentClass\Groupedit` | class extends ModuleView | `kernel/private/classes/views/class/groupedit.php:21` | Der Code von kernel/class/groupedit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\ContentClass\Grouplist` | class extends ModuleView | `kernel/private/classes/views/class/grouplist.php:21` | Der Code von kernel/class/grouplist.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\ContentClass\Removeclass` | class extends ModuleView | `kernel/private/classes/views/class/removeclass.php:21` | Der Code von kernel/class/removeclass.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\ContentClass\Removegroup` | class extends ModuleView | `kernel/private/classes/views/class/removegroup.php:21` | Der Code von kernel/class/removegroup.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\ContentClass\Translation` | class extends ModuleView | `kernel/private/classes/views/class/translation.php:21` | Der Code von kernel/class/translation.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\ContentClass\View` | class extends ModuleView | `kernel/private/classes/views/class/view.php:21` | Der Code von kernel/class/view.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Collaboration\Action` | class extends ModuleView | `kernel/private/classes/views/collaboration/action.php:21` | Der Code von kernel/collaboration/action.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Collaboration\Group` | class extends ModuleView | `kernel/private/classes/views/collaboration/group.php:21` | Der Code von kernel/collaboration/group.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Collaboration\Item` | class extends ModuleView | `kernel/private/classes/views/collaboration/item.php:21` | Der Code von kernel/collaboration/item.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Collaboration\View` | class extends ModuleView | `kernel/private/classes/views/collaboration/view.php:21` | Der Code von kernel/collaboration/view.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Contentcheck\Brokenrelations` | class extends ModuleView | `kernel/private/classes/views/contentcheck/brokenrelations.php:21` | Die Ansicht contentcheck/brokenrelations (doc/eng-us/features/6.0/content-checks.md, „Broken relations“): sichtbarer, veröffentlichter Inhalt unterhalb eines Knotens (conte… | – |
| `Exponential\View\Kernel\Contentcheck\Fixfiles` | class extends ModuleView | `kernel/private/classes/views/contentcheck/fixfiles.php:23` | Die Ansicht contentcheck/fixfiles (doc/eng-us/features/6.0/content-checks.md, "Resolving"): nur POST. | – |
| `Exponential\View\Kernel\Contentcheck\Scope` | class extends ModuleView | `kernel/private/classes/views/contentcheck/scope.php:23` | Die Ansicht contentcheck/scope (doc/eng-us/features/6.0/content-checks.md, „Where a check starts“): wählt den Startort einer Prüfung mit dem gemeinsamen… | – |
| `Exponential\View\Kernel\Contentcheck\Storage` | class extends ModuleView | `kernel/private/classes/views/contentcheck/storage.php:24` | Die Ansicht contentcheck/storage (doc/eng-us/features/6.0/content-checks.md, „Storage files“): das Ergebnis des letzten Scans von var//storage, in drei Listen… | – |
| `Exponential\View\Kernel\Contentcheck\Storageaction` | class extends ModuleView | `kernel/private/classes/views/contentcheck/storageaction.php:26` | Die Ansicht contentcheck/storageaction (doc/eng-us/features/6.0/content-checks.md, "Storage files"): nur POST, aus den Formularen von contentcheck/storage. | – |
| `Exponential\View\Kernel\Contentcheck\Trash` | class extends ModuleView | `kernel/private/classes/views/contentcheck/trash.php:22` | Die Ansicht contentcheck/trash (doc/eng-us/features/6.0/content-checks.md, "Moving to the trash"): nur POST. | – |
| `Exponential\View\Kernel\Contentcheck\Unreferenced` | class extends ModuleView | `kernel/private/classes/views/contentcheck/unreferenced.php:18` | Die Ansicht contentcheck/unreferenced (doc/eng-us/features/6.0/content-checks.md, „Unreferenced objects“): die veröffentlichten Objekte unterhalb eines Knotens (contentch… | – |
| `Exponential\View\Kernel\Contentfinder\Action` | class extends ModuleView | `kernel/private/classes/views/contentfinder/action.php:21` | Die Ansicht contentfinder/action (doc/eng-us/features/6.0/content-finder.md, "Actions"): nur POST. | – |
| `Exponential\View\Kernel\Contentfinder\Search` | class extends ModuleView | `kernel/private/classes/views/contentfinder/search.php:20` | Die Ansicht contentfinder/search (doc/eng-us/features/6.0/content-finder.md): ein Suchfeld und eine Typauswahl, gelesen aus den View-Parametern (text)… | – |
| `Exponential\View\Kernel\Content\Action` | class extends ModuleView | `kernel/private/classes/views/content/action.php:21` | Der Code von kernel/content/action.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Advancedsearch` | class extends ModuleView | `kernel/private/classes/views/content/advancedsearch.php:51` | Der Code von kernel/content/advancedsearch.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Bookmark` | class extends ModuleView | `kernel/private/classes/views/content/bookmark.php:30` | Meine Lesezeichen: die Lesezeichen des aktuellen Benutzers, nach Ordnern gruppiert, mit Ordner (folder), Reihenfolge (sort), Suche (/(q)/) und Seitenumbruch (offset), die erhalten bleiben… | – |
| `Exponential\View\Kernel\Content\Browse` | class extends ModuleView | `kernel/private/classes/views/content/browse.php:21` | Der Code von kernel/content/browse.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Collectedinfo` | class extends ModuleView | `kernel/private/classes/views/content/collectedinfo.php:21` | Der Code von kernel/content/collectedinfo.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Collectinformation` | class extends ModuleView | `kernel/private/classes/views/content/collectinformation.php:21` | Der Code von kernel/content/collectinformation.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Copy` | class extends ModuleView | `kernel/private/classes/views/content/copy.php:171` | Der Code von kernel/content/copy.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Copysubtree` | class extends ModuleView | `kernel/private/classes/views/content/copysubtree.php:859` | Der Code von kernel/content/copysubtree.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Dashboard` | class extends ModuleView | `kernel/private/classes/views/content/dashboard.php:21` | Der Code von kernel/content/dashboard.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Download` | class extends ModuleView | `kernel/private/classes/views/content/download.php:21` | Der Code von kernel/content/download.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Draft` | class extends ModuleView | `kernel/private/classes/views/content/draft.php:27` | Der Code von kernel/content/draft.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Edit` | class extends ModuleView | `kernel/private/classes/views/content/edit.php:386` | Der Code von kernel/content/edit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Hide` | class extends ModuleView | `kernel/private/classes/views/content/hide.php:21` | Der Code von kernel/content/hide.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\History` | class extends ModuleView | `kernel/private/classes/views/content/history.php:27` | Der Code von kernel/content/history.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Job` | class extends ModuleView | `kernel/private/classes/views/content/job.php:19` | Die Ansicht content/job/: die Fortschrittsseite eines Inhaltsjobs (ein großes Entfernen oder Kopieren eines Teilbaums, das im Hintergrund läuft, siehe kernel/classes/contentjob/… | – |
| `Exponential\View\Kernel\Content\Jobs` | class extends ModuleView | `kernel/private/classes/views/content/jobs.php:20` | Die Ansicht content/jobs: die Liste der Inhaltsjobs (große Teilbaum-Entfernungen und -Kopien, die im Hintergrund laufen) mit dem, der sie gestartet hat, der Operation, so… | – |
| `Exponential\View\Kernel\Content\Keyword` | class extends ModuleView | `kernel/private/classes/views/content/keyword.php:21` | Der Code von kernel/content/keyword.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Move` | class extends ModuleView | `kernel/private/classes/views/content/move.php:21` | Der Code von kernel/content/move.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Multiedit` | class extends ModuleView | `kernel/private/classes/views/content/multiedit.php:10` | Der Code von kernel/content/multiedit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Newcontent` | class extends ModuleView | `kernel/private/classes/views/content/newcontent.php:21` | Der Code von kernel/content/newcontent.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Pdf` | class extends ModuleView | `kernel/private/classes/views/content/pdf.php:147` | Der Code von kernel/content/pdf.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Pendinglist` | class extends ModuleView | `kernel/private/classes/views/content/pendinglist.php:28` | Der Code von kernel/content/pendinglist.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Queued` | class extends ModuleView | `kernel/private/classes/views/content/queued.php:24` | Der Code von kernel/content/queued.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Removeassignment` | class extends ModuleView | `kernel/private/classes/views/content/removeassignment.php:21` | Der Code von kernel/content/removeassignment.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Removeeditversion` | class extends ModuleView | `kernel/private/classes/views/content/removeeditversion.php:21` | Der Code von kernel/content/removeeditversion.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Removenode` | class extends ModuleView | `kernel/private/classes/views/content/removenode.php:21` | Der Code von kernel/content/removenode.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Removeobject` | class extends ModuleView | `kernel/private/classes/views/content/removeobject.php:21` | Der Code von kernel/content/removeobject.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Restore` | class extends ModuleView | `kernel/private/classes/views/content/restore.php:21` | Der Code von kernel/content/restore.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Reverserelatedlist` | class extends ModuleView | `kernel/private/classes/views/content/reverserelatedlist.php:21` | Der Code von kernel/content/reverserelatedlist.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Search` | class extends ModuleView | `kernel/private/classes/views/content/search.php:51` | Der Code von kernel/content/search.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Subitemsexport` | class extends ModuleView | `kernel/private/classes/views/content/subitemsexport.php:18` | Die Ansicht content/subitemsexport/: die Unterelementliste eines Knotens in der Admin als CSV-Download. | – |
| `Exponential\View\Kernel\Content\Tipafriend` | class extends ModuleView | `kernel/private/classes/views/content/tipafriend.php:21` | Der Code von kernel/content/tipafriend.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Translation` | class extends ModuleView | `kernel/private/classes/views/content/translation.php:21` | Der Code von kernel/content/translation.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Translations` | class extends ModuleView | `kernel/private/classes/views/content/translations.php:27` | Der Code von kernel/content/translations.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Trash` | class extends ModuleView | `kernel/private/classes/views/content/trash.php:21` | Der Code von kernel/content/trash.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Treemenu` | class extends ModuleView | `kernel/private/classes/views/content/treemenu.php:21` | Der Code von kernel/content/treemenu.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Upload` | class extends ModuleView | `kernel/private/classes/views/content/upload.php:21` | Der Code von kernel/content/upload.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Urlalias` | class extends ModuleView | `kernel/private/classes/views/content/urlalias.php:22` | Der Code von kernel/content/urlalias.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\UrlaliasGlobal` | class extends ModuleView | `kernel/private/classes/views/content/urlalias_global.php:21` | Der Code von kernel/content/urlalias_global.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\UrlaliasWildcard` | class extends ModuleView | `kernel/private/classes/views/content/urlalias_wildcard.php:21` | Der Code von kernel/content/urlalias_wildcard.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\Versionview` | class extends ModuleView | `kernel/private/classes/views/content/versionview.php:21` | Der Code von kernel/content/versionview.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Content\View` | class extends ModuleView | `kernel/private/classes/views/content/view.php:21` | Der Code von kernel/content/view.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Csp\Report` | class extends ModuleView | `kernel/private/classes/views/csp/report.php:29` | Nimmt einen Bericht der Content Security Policy von einem Browser entgegen und schreibt dessen Verstöße nach var/log/csp.log (expContentSecurityPolicyReport). | – |
| `Exponential\View\Kernel\Datatable\Conflicts` | class extends ModuleView | `kernel/private/classes/views/datatable/conflicts.php:15` | Die Ansicht datatable/conflicts (doc/eng-us/features/6.0/data-tables.md, „Conflicts“): die offenen Konflikte der Zwei-Wege-Synchronisationen mit der Regel… | – |
| `Exponential\View\Kernel\Datatable\Connection` | class extends ModuleView | `kernel/private/classes/views/datatable/connection.php:17` | Die Ansicht datatable/connection (doc/eng-us/features/6.0/data-tables.md, „Connections“): legt eine neue Verbindung an (/new) oder zeigt, ändert, testet und ent… | – |
| `Exponential\View\Kernel\Datatable\Connections` | class extends ModuleView | `kernel/private/classes/views/datatable/connections.php:16` | Die Ansicht datatable/connections (doc/eng-us/features/6.0/data-tables.md, „Connections“): die eigene Datenbank der Installation, die Verbindungen, die Extensions… | – |
| `Exponential\View\Kernel\Datatable\Dashboard` | class extends ModuleView | `kernel/private/classes/views/datatable/dashboard.php:16` | Die Ansicht datatable/dashboard (doc/eng-us/features/6.0/data-tables.md): jede persistente Klasse der Installation mit ihrer Tabelle, Verbindung, Schlüsseln und… | – |
| `Exponential\View\Kernel\Datatable\Rows` | class extends ModuleView | `kernel/private/classes/views/datatable/rows.php:16` | Die Ansicht datatable/rows (doc/eng-us/features/6.0/data-tables.md, „Rows“): die Zeilen der Tabelle einer persistenten Klasse, 25 je Seite, nach einem Feld gefiltert… | – |
| `Exponential\View\Kernel\Datatable\Schema` | class extends ModuleView | `kernel/private/classes/views/datatable/schema.php:17` | Die Ansicht datatable/schema (doc/eng-us/features/6.0/data-tables.md, „Schema“): legt Datentabellen auf einer Verbindung an oder aktualisiert sie. | – |
| `Exponential\View\Kernel\Datatable\Sync` | class extends ModuleView | `kernel/private/classes/views/datatable/sync.php:15` | Die Ansicht datatable/sync (doc/eng-us/features/6.0/data-tables.md, „Synchronisation“): legt eine Synchronisation an (/new, /new/(class)//(content_class)/ aus… | – |
| `Exponential\View\Kernel\Datatable\Syncs` | class extends ModuleView | `kernel/private/classes/views/datatable/syncs.php:16` | Die Ansicht datatable/syncs (doc/eng-us/features/6.0/data-tables.md, „Synchronisation“): jede Synchronisation zwischen Inhalt und einer Datentabelle, mit ihrem… | – |
| `Exponential\View\Kernel\Datatable\Table` | class extends ModuleView | `kernel/private/classes/views/datatable/table.php:16` | Die Ansicht datatable/table (doc/eng-us/features/6.0/data-tables.md, „Describe“): eine persistente Klasse und ihre Tabelle: die Definition (Felder, Schlüssel, Sortier… | – |
| `Exponential\View\Kernel\Error\View` | class extends ModuleView | `kernel/private/classes/views/error/view.php:21` | Der Code von kernel/error/view.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Exphelp\Asset` | class extends ModuleView | `kernel/private/classes/views/exphelp/asset.php:15` | exphelp/asset/ (doc/eng-us/features/6.0/help-system.md): ein Bild unterhalb eines Hilfe-Wurzelverzeichnisses. | – |
| `Exponential\View\Kernel\Exphelp\Audience` | class extends ModuleView | `kernel/private/classes/views/exphelp/audience.php:20` | exphelp/audience/ (doc/eng-us/features/6.0/help-system.md, „Landing pages“): die Landingpage einer Zielgruppe, aufgebaut aus den Blöcken von exphelp.ini [… | – |
| `Exponential\View\Kernel\Exphelp\Contents` | class extends ModuleView | `kernel/private/classes/views/exphelp/contents.php:19` | exphelp/contents (doc/eng-us/features/6.0/help-system.md): jede Seite der Hilfe als ein Baum, seine Ordner geschlossen (nativ : Titel und Seitenanzahl, die… | – |
| `Exponential\View\Kernel\Exphelp\Context` | class extends ModuleView | `kernel/private/classes/views/exphelp/context.php:21` | exphelp/context/ und exphelp/context/manual/ (doc/eng-us/features/6.0/help-content.md, „Context help by help_id“): ein Hilfethema oder das Inhaltsverzeichnis eines Handbuchs… | – |
| `Exponential\View\Kernel\Exphelp\Dashboard` | class extends ModuleView | `kernel/private/classes/views/exphelp/dashboard.php:19` | exphelp/dashboard (doc/eng-us/features/6.0/help-system.md): die Startseite der Hilfe. | – |
| `Exponential\View\Kernel\Exphelp\Extensions` | class extends ModuleView | `kernel/private/classes/views/exphelp/extensions.php:27` | exphelp/extensions (doc/eng-us/features/6.0/help-system.md, „Documentation of the extensions“): die Dokumentation der aktiven Extensions in der Hil… | – |
| `Exponential\View\Kernel\Exphelp\Feedback` | class extends ModuleView | `kernel/private/classes/views/exphelp/feedback.php:21` | exphelp/feedback/ (doc/eng-us/features/6.0/help-content.md, „Was this helpful?“): speichert eine Antwort auf „War das hilfreich?“ zu einem freigegebenen Hilfethema (… | – |
| `Exponential\View\Kernel\Exphelp\Image` | class extends ModuleView | `kernel/private/classes/views/exphelp/image.php:18` | exphelp/image/(id)/ (doc/eng-us/features/6.0/help-system.md, „External images“): die lokale Kopie eines externen Bildes, das eine Hilfeseite zeigt (README.md… | – |
| `Exponential\View\Kernel\Exphelp\Manual` | class extends ModuleView | `kernel/private/classes/views/exphelp/manual.php:18` | exphelp/manual/ (doc/eng-us/features/6.0/help-content.md): ein als Inhalt gepflegtes Hilfehandbuch, seine Metadaten und sein Inhaltsverzeichnis (Kapitel und… | – |
| `Exponential\View\Kernel\Exphelp\Pdf` | class extends ModuleView | `kernel/private/classes/views/exphelp/pdf.php:18` | exphelp/pdf/ (doc/eng-us/features/6.0/help-system.md, "Print and PDF"): die Hilfeseite als PDF-Datei zum Herunterladen. | – |
| `Exponential\View\Kernel\Exphelp\Report` | class extends ModuleView | `kernel/private/classes/views/exphelp/report.php:18` | exphelp/report (doc/eng-us/features/6.0/help-system.md, „Reporting an error“): welcher Tracker, wie man eine gute Meldung schreibt, ein vorausgefülltes Issue, die sicher… | – |
| `Exponential\View\Kernel\Exphelp\Search` | class extends ModuleView | `kernel/private/classes/views/exphelp/search.php:32` | exphelp/search (doc/eng-us/features/6.0/help-system.md, "Search"): die Such- und Stöberseite der Hilfe. | – |
| `Exponential\View\Kernel\Exphelp\State` | class extends ModuleView | `kernel/private/classes/views/exphelp/state.php:21` | exphelp/state/ und exphelp/state/manual/ (doc/eng-us/features/6.0/help-content.md, „The review workflow“): ein Schritt von Entwurf -› Prüfung -› Freigegeben für… | – |
| `Exponential\View\Kernel\Exphelp\Topic` | class extends ModuleView | `kernel/private/classes/views/exphelp/topic.php:26` | exphelp/topic/ (doc/eng-us/features/6.0/help-content.md): ein als Inhalt gepflegtes Hilfethema, gerendert. | – |
| `Exponential\View\Kernel\Exphelp\Viewer` | class extends ModuleView | `kernel/private/classes/views/exphelp/viewer.php:24` | exphelp/viewer/ (doc/eng-us/features/6.0/help-system.md): eine Hilfeseite, gerendert. | – |
| `Exponential\View\Kernel\Ezinfo\About` | class extends ModuleView | `kernel/private/classes/views/ezinfo/about.php:267` | Der Code von kernel/ezinfo/about.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Ezinfo\Copyright` | class extends ModuleView | `kernel/private/classes/views/ezinfo/copyright.php:21` | Der Code von kernel/ezinfo/copyright.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Ezinfo\Isalive` | class extends ModuleView | `kernel/private/classes/views/ezinfo/isalive.php:21` | Der Code von kernel/ezinfo/isalive.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Infocollector\Collectionlist` | class extends ModuleView | `kernel/private/classes/views/infocollector/collectionlist.php:21` | Der Code von kernel/infocollector/collectionlist.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Infocollector\Export` | class extends ModuleView | `kernel/private/classes/views/infocollector/export.php:20` | Die Ansicht infocollector/export/: alles, was ein Objekt (ein Formular, eine Umfrage, eine Feedback-Seite) gesammelt hat, als CSV-Download: die ID der Sammlung, wann i… | – |
| `Exponential\View\Kernel\Infocollector\Overview` | class extends ModuleView | `kernel/private/classes/views/infocollector/overview.php:28` | Setup › Gesammelte Informationen (infocollector/overview): die Objekte (Formulare, Umfragen, Feedbackseiten), die Informationen gesammelt haben, mit Kennzahlen, ei… | – |
| `Exponential\View\Kernel\Infocollector\View` | class extends ModuleView | `kernel/private/classes/views/infocollector/view.php:21` | Der Code von kernel/infocollector/view.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Layout\Set` | class extends ModuleView | `kernel/private/classes/views/layout/set.php:21` | Der Code von kernel/layout/set.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Mailpreferences\Admin` | class extends Page | `kernel/private/classes/views/mailpreferences/admin.php:29` | Die Seiten des Administrators für die E-Mail-Einstellungen (Richtlinie mailpreferences/administrate): | – |
| `Exponential\View\Kernel\Mailpreferences\Confirm` | class extends Page | `kernel/private/classes/views/mailpreferences/confirm.php:20` | Der Link einer Double-Opt-in-Mail (eine Kategorie oder eine neue E-Mail-Adresse). | – |
| `Exponential\View\Kernel\Mailpreferences\Export` | class extends Page | `kernel/private/classes/views/mailpreferences/export.php:20` | „Meine E-Mail-Daten herunterladen“: alles, was über die E-Mail-Präferenzen einer Person gespeichert ist, als JSON oder CSV (die Kategorien und ihr Zustand, ausstehende Bestätigu… | – |
| `Exponential\View\Kernel\Mailpreferences\Manage` | class extends Page | `kernel/private/classes/views/mailpreferences/manage.php:19` | Die Einstellungsseite über einen persönlichen Link (der "Manage"-Link jeder optionalen E-Mail oder "send me a link"): dieselbe Seite wie mailpreferences/settings,… | – |
| `Exponential\View\Kernel\Mailpreferences\Page` | abstract class extends ModuleView | `kernel/private/classes/views/mailpreferences/page.php:19` | Was die Ansichten des Moduls mailpreferences gemeinsam haben: die Variablen der einbindenden Funktion, das Rendern einer Seite von design:mailpreferences/, die Antwort… | – |
| `Exponential\View\Kernel\Mailpreferences\Request` | class extends Page | `kernel/private/classes/views/mailpreferences/request.php:20` | „Link zusenden“: eine Person ohne Konto oder ohne Anmeldung gibt eine Adresse ein und erhält einen persönlichen Link zur Präferenzseite. | – |
| `Exponential\View\Kernel\Mailpreferences\Settings` | class extends Page | `kernel/private/classes/views/mailpreferences/settings.php:20` | Die E-Mail-Einstellungen des angemeldeten Benutzers: der Hauptschalter, die Kategorien und ihre Häufigkeiten, wesentliche Mails (nicht abschaltbar), der Einwilligungsverl… | – |
| `Exponential\View\Kernel\Mailpreferences\Unsubscribe` | class extends Page | `kernel/private/classes/views/mailpreferences/unsubscribe.php:25` | Der Abmeldelink einer optionalen E-Mail. | – |
| `Exponential\View\Kernel\Notification\Addtonotification` | class extends ModuleView | `kernel/private/classes/views/notification/addtonotification.php:21` | Der Code von kernel/notification/addtonotification.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Notification\Job` | class extends ModuleView | `kernel/private/classes/views/notification/job.php:17` | Das "Run now"-Backend der Statusseite (expNotificationJob), antwortet mit JSON: POST notification/job mit Action=start (und DryRun=1) -› { "id": "..." }… | – |
| `Exponential\View\Kernel\Notification\Runfilter` | class extends ModuleView | `kernel/private/classes/views/notification/runfilter.php:21` | Der Code von kernel/notification/runfilter.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Notification\Settings` | class extends ModuleView | `kernel/private/classes/views/notification/settings.php:21` | Der Code von kernel/notification/settings.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Notification\Status` | class extends ModuleView | `kernel/private/classes/views/notification/status.php:13` | – | – |
| `Exponential\View\Kernel\Package\Compare` | class extends ModuleView | `kernel/private/classes/views/package/compare.php:21` | Der Code von kernel/package/compare.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Package\Create` | class extends ModuleView | `kernel/private/classes/views/package/create.php:21` | Der Code von kernel/package/create.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Package\Export` | class extends ModuleView | `kernel/private/classes/views/package/export.php:27` | package/export/[/]: das Paket als .ezpkg-Archiv (gzip-komprimiertes tar), erstellt im eigenen Exportverzeichnis des Benutzers, in Teilen gesendet von eZPackageDown… | – |
| `Exponential\View\Kernel\Package\Install` | class extends ModuleView | `kernel/private/classes/views/package/install.php:21` | Der Code von kernel/package/install.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Package\ListView` | class extends ModuleView | `kernel/private/classes/views/package/list.php:32` | package/list[/][/(search)/..][/(type)/..][/(state)/..][/(sort)/..][/(dir)/..][/offset/N] | – |
| `Exponential\View\Kernel\Package\Uninstall` | class extends ModuleView | `kernel/private/classes/views/package/uninstall.php:21` | Der Code von kernel/package/uninstall.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Package\Upload` | class extends ModuleView | `kernel/private/classes/views/package/upload.php:31` | package/upload: importiert ein hochgeladenes Paketarchiv in sein Repository (das, das sein Anbieter nennt, "local" ohne Anbieter). | – |
| `Exponential\View\Kernel\Package\View` | class extends ModuleView | `kernel/private/classes/views/package/view.php:21` | Der Code von kernel/package/view.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Package\Viewfile` | class extends ModuleView | `kernel/private/classes/views/package/viewfile.php:21` | Der Code von kernel/package/viewfile.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Pdf\Edit` | class extends ModuleView | `kernel/private/classes/views/pdf/edit.php:55` | Der Code von kernel/pdf/edit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Pdf\ListView` | class extends ModuleView | `kernel/private/classes/views/pdf/list.php:25` | Der Code von kernel/pdf/list.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Role\Assign` | class extends ModuleView | `kernel/private/classes/views/role/assign.php:21` | Der Code von kernel/role/assign.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Role\Copy` | class extends ModuleView | `kernel/private/classes/views/role/copy.php:21` | Der Code von kernel/role/copy.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Role\Edit` | class extends ModuleView | `kernel/private/classes/views/role/edit.php:21` | Der Code von kernel/role/edit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Role\ListView` | class extends ModuleView | `kernel/private/classes/views/role/list.php:21` | Der Code von kernel/role/list.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Role\Policyedit` | class extends ModuleView | `kernel/private/classes/views/role/policyedit.php:70` | Wendet die per POST übermittelten Einschränkungen aus den Auswahllisten an | – |
| `Exponential\View\Kernel\Role\View` | class extends ModuleView | `kernel/private/classes/views/role/view.php:21` | Der Code von kernel/role/view.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Rss\EditExport` | class extends ModuleView | `kernel/private/classes/views/rss/edit_export.php:21` | Der Code von kernel/rss/edit_export.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Rss\EditImport` | class extends ModuleView | `kernel/private/classes/views/rss/edit_import.php:114` | Der Code von kernel/rss/edit_import.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Rss\Feed` | class extends ModuleView | `kernel/private/classes/views/rss/feed.php:21` | Der Code von kernel/rss/feed.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Rss\ListView` | class extends ModuleView | `kernel/private/classes/views/rss/list.php:21` | Der Code von kernel/rss/list.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Search\Stats` | class extends ModuleView | `kernel/private/classes/views/search/stats.php:22` | Der Code von kernel/search/stats.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Secrets\SecretList` | class extends ModuleView | `kernel/private/classes/views/secrets/list.php:17` | Die Ansicht secrets/list (doc/eng-us/features/6.0/secret-store.md): die Namen der Geheimnisse des Geheimnisspeichers des Kernels und von secrets.ini, mit dem… | – |
| `Exponential\View\Kernel\Section\Assign` | class extends ModuleView | `kernel/private/classes/views/section/assign.php:21` | Der Code von kernel/section/assign.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Section\Edit` | class extends ModuleView | `kernel/private/classes/views/section/edit.php:21` | Der Code von kernel/section/edit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Section\ListView` | class extends ModuleView | `kernel/private/classes/views/section/list.php:22` | Der Code von kernel/section/list.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Section\View` | class extends ModuleView | `kernel/private/classes/views/section/view.php:21` | Der Code von kernel/section/view.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Settings\Edit` | class extends ModuleView | `kernel/private/classes/views/settings/edit.php:57` | Der Code von kernel/settings/edit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Settings\View` | class extends ModuleView | `kernel/private/classes/views/settings/view.php:26` | Der Code von kernel/settings/view.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Cache` | class extends ModuleView | `kernel/private/classes/views/setup/cache.php:32` | Setup › Caches. | – |
| `Exponential\View\Kernel\Setup\Cachetoolbar` | class extends ModuleView | `kernel/private/classes/views/setup/cachetoolbar.php:21` | Der Code von kernel/setup/cachetoolbar.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Contentextension` | class extends ModuleView | `kernel/private/classes/views/setup/contentextension.php:10` | Der Code von kernel/setup/contentextension.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Cronjobs` | class extends ModuleView | `kernel/private/classes/views/setup/cronjobs.php:28` | Der Code von kernel/setup/cronjobs.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Cronjobsstream` | class extends ModuleView | `kernel/private/classes/views/setup/cronjobsstream.php:48` | Der Code von kernel/setup/cronjobsstream.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Datatype` | class extends ModuleView | `kernel/private/classes/views/setup/datatype.php:10` | Der Code von kernel/setup/datatype.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Designextension` | class extends ModuleView | `kernel/private/classes/views/setup/designextension.php:10` | Der Code von kernel/setup/designextension.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Extensions` | class extends ModuleView | `kernel/private/classes/views/setup/extensions.php:77` | Der Code von kernel/setup/extensions.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Ezsetup` | class extends ModuleView | `kernel/private/classes/views/setup/ezsetup.php:21` | Der Code von kernel/setup/ezsetup.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Handlerextension` | class extends ModuleView | `kernel/private/classes/views/setup/handlerextension.php:10` | Der Code von kernel/setup/handlerextension.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Info` | class extends ModuleView | `kernel/private/classes/views/setup/info.php:21` | Der Code von kernel/setup/info.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Kerneloverride` | class extends ModuleView | `kernel/private/classes/views/setup/kerneloverride.php:10` | Der Code von kernel/setup/kerneloverride.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Languages` | class extends ModuleView | `kernel/private/classes/views/setup/languages.php:17` | Setup › Oberflächensprachen (setup/languages): jede Locale mit der Übersetzung, in der die Administrationsoberfläche sie anzeigt, die Kette der Übersetzungen, auf die sie zurückf… | – |
| `Exponential\View\Kernel\Setup\Logs` | class extends ModuleView | `kernel/private/classes/views/setup/logs.php:22` | Setup › Logdateien (setup/logs; doc/eng-us/features/6.0/log-viewer.md): die Seite des Log-Viewers. | – |
| `Exponential\View\Kernel\Setup\Logsaction` | class extends ModuleView | `kernel/private/classes/views/setup/logsaction.php:21` | Die Aktionen des Log-Viewers (setup/logsaction; doc/eng-us/features/6.0/log-viewer.md, "Actions"). | – |
| `Exponential\View\Kernel\Setup\Logsdigest` | class extends ModuleView | `kernel/private/classes/views/setup/logsdigest.php:22` | Setup › Logdateien › Fehlerübersicht (setup/logsdigest; doc/eng-us/features/6.0/log-viewer.md, „Error digest“): die verschiedenen Fehler und Warnungen der Lo… | – |
| `Exponential\View\Kernel\Setup\Logsfeed` | class extends ModuleView | `kernel/private/classes/views/setup/logsfeed.php:28` | Der Feed des Log-Viewers (setup/logsfeed; doc/eng-us/features/6.0/log-viewer.md, „The feed“): JSON für das Skript der Seite, je eine kurze Anfrage, n… | – |
| `Exponential\View\Kernel\Setup\Logshealth` | class extends ModuleView | `kernel/private/classes/views/setup/logshealth.php:17` | Setup › Logdateien › Zustand (setup/logshealth; doc/eng-us/features/6.0/log-viewer.md, „Health“): der Speicherplatz, den die Logs und ihre Archive belegen, gegenüber [Hea… | – |
| `Exponential\View\Kernel\Setup\Logsmanage` | class extends ModuleView | `kernel/private/classes/views/setup/logsmanage.php:24` | Setup › Logdateien › Verwalten (setup/logsmanage; doc/eng-us/features/6.0/log-viewer.md, „Managing logs“): jedes Log mit Größe, geschätzten Zeilen, letzt… | – |
| `Exponential\View\Kernel\Setup\Maintenance` | class extends ModuleView | `kernel/private/classes/views/setup/maintenance.php:19` | Der Code von kernel/setup/maintenance.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Menuconfig` | class extends ModuleView | `kernel/private/classes/views/setup/menuconfig.php:21` | Der Code von kernel/setup/menuconfig.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Moduleextension` | class extends ModuleView | `kernel/private/classes/views/setup/moduleextension.php:10` | Der Code von kernel/setup/moduleextension.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Moduletable` | class extends ModuleView | `kernel/private/classes/views/setup/moduletable.php:18` | Die Ansicht setup/moduletable: der Modultabellen-Assistent (expModuleTableWizard; doc/eng-us/features/6.0/data-tables.md, „The module table wizard“). | – |
| `Exponential\View\Kernel\Setup\Modulewizard` | class extends ModuleView | `kernel/private/classes/views/setup/modulewizard.php:10` | Der Code von kernel/setup/modulewizard.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Preload` | class extends ModuleView | `kernel/private/classes/views/setup/preload.php:36` | Setup › Preload: was gerade läuft, das Formular, das einen Lauf startet oder zeigt, was er tun würde (der Probelauf), und die letzten Läufe. | – |
| `Exponential\View\Kernel\Setup\Preloadjob` | class extends ModuleView | `kernel/private/classes/views/setup/preloadjob.php:37` | Setup › Backend von Preload, antwortet mit JSON: POST Action=start, SiteAccess, MaxPages, MaxDepth[, Images] -› { "id": "..." }, 409, solange ein Lauf aktiv ist… | – |
| `Exponential\View\Kernel\Setup\Preloadstream` | class extends ModuleView | `kernel/private/classes/views/setup/preloadstream.php:37` | Der ältere Fortschrittsstrom von Setup › Preload, beibehalten für alles, was ihn öffnet. | – |
| `Exponential\View\Kernel\Setup\Rad` | class extends ModuleView | `kernel/private/classes/views/setup/rad.php:23` | Der Code von kernel/setup/rad.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Radsurvey` | class extends ModuleView | `kernel/private/classes/views/setup/radsurvey.php:10` | Der Code von kernel/setup/radsurvey.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Session` | class extends ModuleView | `kernel/private/classes/views/setup/session.php:58` | Setup › Sitzungen (setup/session). | – |
| `Exponential\View\Kernel\Setup\Settingsextension` | class extends ModuleView | `kernel/private/classes/views/setup/settingsextension.php:10` | Der Code von kernel/setup/settingsextension.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Settingstoolbar` | class extends ModuleView | `kernel/private/classes/views/setup/settingstoolbar.php:21` | Der Code von kernel/setup/settingstoolbar.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Setupmenu` | class extends ModuleView | `kernel/private/classes/views/setup/setupmenu.php:21` | Der Code von kernel/setup/setupmenu.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Sitedata` | class extends ModuleView | `kernel/private/classes/views/setup/sitedata.php:19` | Setup › Site-Daten (setup/sitedata): die Zeilen von ezsite_data (eZSiteData), etwa Exponential-version, nur lesend, nach Name, mit einem Filter (ein ganzer Nam… | – |
| `Exponential\View\Kernel\Setup\Staticcachestream` | class extends ModuleView | `kernel/private/classes/views/setup/staticcachestream.php:28` | Der Code von kernel/setup/staticcachestream.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Systemupgrade` | class extends ModuleView | `kernel/private/classes/views/setup/systemupgrade.php:32` | Die Upgrade-Prüfung (setup/systemupgrade): die Dateikonsistenzprüfung und die Datenbankkonsistenzprüfung. | – |
| `Exponential\View\Kernel\Setup\Templatecreate` | class extends ModuleView | `kernel/private/classes/views/setup/templatecreate.php:21` | Der Code von kernel/setup/templatecreate.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Templateedit` | class extends ModuleView | `kernel/private/classes/views/setup/templateedit.php:21` | Der Code von kernel/setup/templateedit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Templatelist` | class extends ModuleView | `kernel/private/classes/views/setup/templatelist.php:21` | Der Code von kernel/setup/templatelist.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Templateoperator` | class extends ModuleView | `kernel/private/classes/views/setup/templateoperator.php:10` | Der Code von kernel/setup/templateoperator.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Templateview` | class extends ModuleView | `kernel/private/classes/views/setup/templateview.php:21` | Der Code von kernel/setup/templateview.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Toolbar` | class extends ModuleView | `kernel/private/classes/views/setup/toolbar.php:21` | Der Code von kernel/setup/toolbar.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Toolbarlist` | class extends ModuleView | `kernel/private/classes/views/setup/toolbarlist.php:21` | Der Code von kernel/setup/toolbarlist.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Setup\Workflowevent` | class extends ModuleView | `kernel/private/classes/views/setup/workflowevent.php:10` | Der Code von kernel/setup/workflowevent.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Add` | class extends ModuleView | `kernel/private/classes/views/shop/add.php:21` | Der Code von kernel/shop/add.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Archivelist` | class extends ModuleView | `kernel/private/classes/views/shop/archivelist.php:21` | Der Code von kernel/shop/archivelist.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Archiveorder` | class extends ModuleView | `kernel/private/classes/views/shop/archiveorder.php:21` | Der Code von kernel/shop/archiveorder.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Basket` | class extends ModuleView | `kernel/private/classes/views/shop/basket.php:21` | Der Code von kernel/shop/basket.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Checkout` | class extends ModuleView | `kernel/private/classes/views/shop/checkout.php:21` | Der Code von kernel/shop/checkout.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Confirmorder` | class extends ModuleView | `kernel/private/classes/views/shop/confirmorder.php:21` | Der Code von kernel/shop/confirmorder.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Currencylist` | class extends ModuleView | `kernel/private/classes/views/shop/currencylist.php:21` | Der Code von kernel/shop/currencylist.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Customerlist` | class extends ModuleView | `kernel/private/classes/views/shop/customerlist.php:21` | Der Code von kernel/shop/customerlist.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Customerorderview` | class extends ModuleView | `kernel/private/classes/views/shop/customerorderview.php:22` | Der Code von kernel/shop/customerorderview.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Dashboard` | class extends ModuleView | `kernel/private/classes/views/shop/dashboard.php:19` | Der Code von kernel/shop/dashboard.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Discountgroup` | class extends ModuleView | `kernel/private/classes/views/shop/discountgroup.php:21` | Der Code von kernel/shop/discountgroup.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Discountgroupedit` | class extends ModuleView | `kernel/private/classes/views/shop/discountgroupedit.php:21` | Der Code von kernel/shop/discountgroupedit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Discountgroupmembershipview` | class extends ModuleView | `kernel/private/classes/views/shop/discountgroupmembershipview.php:21` | Der Code von kernel/shop/discountgroupmembershipview.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Discountruleedit` | class extends ModuleView | `kernel/private/classes/views/shop/discountruleedit.php:21` | Der Code von kernel/shop/discountruleedit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Editcurrency` | class extends ModuleView | `kernel/private/classes/views/shop/editcurrency.php:56` | Validiert Währungsdaten: - prüft, dass custom_rate_value & rate_factor angegeben und Gleitkommazahlen sind. | – |
| `Exponential\View\Kernel\Shop\Editvatrule` | class extends ModuleView | `kernel/private/classes/views/shop/editvatrule.php:122` | Eingegebene Daten prüfen. | – |
| `Exponential\View\Kernel\Shop\Orderlist` | class extends ModuleView | `kernel/private/classes/views/shop/orderlist.php:21` | Der Code von kernel/shop/orderlist.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Orderreceipt` | class extends ModuleView | `kernel/private/classes/views/shop/orderreceipt.php:26` | Der Code von kernel/shop/orderreceipt.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Orderreciept` | class extends ModuleView | `kernel/private/classes/views/shop/orderreciept.php:22` | Der Code von kernel/shop/orderreciept.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Orderstatistics` | class extends ModuleView | `kernel/private/classes/views/shop/orderstatistics.php:21` | Der Code von kernel/shop/orderstatistics.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Orderview` | class extends ModuleView | `kernel/private/classes/views/shop/orderview.php:21` | Der Code von kernel/shop/orderview.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Preferredcurrency` | class extends ModuleView | `kernel/private/classes/views/shop/preferredcurrency.php:21` | Der Code von kernel/shop/preferredcurrency.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Productcategories` | class extends ModuleView | `kernel/private/classes/views/shop/productcategories.php:90` | Einen eindeutigen Kategorienamen erzeugen. | – |
| `Exponential\View\Kernel\Shop\Productsoverview` | class extends ModuleView | `kernel/private/classes/views/shop/productsoverview.php:21` | Der Code von kernel/shop/productsoverview.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Register` | class extends ModuleView | `kernel/private/classes/views/shop/register.php:21` | Der Code von kernel/shop/register.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Removeorder` | class extends ModuleView | `kernel/private/classes/views/shop/removeorder.php:21` | Der Code von kernel/shop/removeorder.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Setpreferredcurrency` | class extends ModuleView | `kernel/private/classes/views/shop/setpreferredcurrency.php:21` | Der Code von kernel/shop/setpreferredcurrency.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Setstatus` | class extends ModuleView | `kernel/private/classes/views/shop/setstatus.php:21` | Der Code von kernel/shop/setstatus.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Setusercountry` | class extends ModuleView | `kernel/private/classes/views/shop/setusercountry.php:21` | Der Code von kernel/shop/setusercountry.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Status` | class extends ModuleView | `kernel/private/classes/views/shop/status.php:21` | Der Code von kernel/shop/status.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Unarchiveorder` | class extends ModuleView | `kernel/private/classes/views/shop/unarchiveorder.php:21` | Der Code von kernel/shop/unarchiveorder.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Updatebasket` | class extends ModuleView | `kernel/private/classes/views/shop/updatebasket.php:21` | Der Code von kernel/shop/updatebasket.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Userregister` | class extends ModuleView | `kernel/private/classes/views/shop/userregister.php:21` | Der Code von kernel/shop/userregister.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Shop\Vatrules` | class extends ModuleView | `kernel/private/classes/views/shop/vatrules.php:161` | Hilfsfunktion zum Sortieren von MwSt.-Regeln. | – |
| `Exponential\View\Kernel\Shop\Vattype` | class extends ModuleView | `kernel/private/classes/views/shop/vattype.php:142` | Abhängige MwSt.-Regeln und Produkte für die gegebenen MwSt.-Typen ermitteln. | – |
| `Exponential\View\Kernel\Shop\Wishlist` | class extends ModuleView | `kernel/private/classes/views/shop/wishlist.php:21` | Der Code von kernel/shop/wishlist.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\State\Assign` | class extends ModuleView | `kernel/private/classes/views/state/assign.php:21` | Der Code von kernel/state/assign.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\State\Edit` | class extends ModuleView | `kernel/private/classes/views/state/edit.php:21` | Der Code von kernel/state/edit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\State\Group` | class extends ModuleView | `kernel/private/classes/views/state/group.php:21` | Der Code von kernel/state/group.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\State\GroupEdit` | class extends ModuleView | `kernel/private/classes/views/state/group_edit.php:21` | Der Code von kernel/state/group_edit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\State\Groups` | class extends ModuleView | `kernel/private/classes/views/state/groups.php:21` | Der Code von kernel/state/groups.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\State\View` | class extends ModuleView | `kernel/private/classes/views/state/view.php:21` | Der Code von kernel/state/view.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Trigger\ListView` | class extends ModuleView | `kernel/private/classes/views/trigger/list.php:37` | Der Code von kernel/trigger/list.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Uiswitch\Set` | class extends ModuleView | `kernel/private/classes/views/uiswitch/set.php:20` | Die Ansicht uiswitch/set (doc/eng-us/features/6.0/ui-switch.md): ändert eine der Oberflächeneinstellungen des angemeldeten Benutzers in der aktuellen Siteaccess und geht… | – |
| `Exponential\View\Kernel\Uiswitch\State` | class extends ModuleView | `kernel/private/classes/views/uiswitch/state.php:14` | Die Ansicht uiswitch/state: JSON dessen, was der angemeldete Benutzer im aktuellen Siteaccess wählen darf, für Skripte (expUISwitch::toolbar()). | – |
| `Exponential\View\Kernel\Url\Edit` | class extends ModuleView | `kernel/private/classes/views/url/edit.php:21` | Der Code von kernel/url/edit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Url\ListView` | class extends ModuleView | `kernel/private/classes/views/url/list.php:22` | Der Code von kernel/url/list.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Url\View` | class extends ModuleView | `kernel/private/classes/views/url/view.php:21` | Der Code von kernel/url/view.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\User\Activate` | class extends ModuleView | `kernel/private/classes/views/user/activate.php:21` | Der Code von kernel/user/activate.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\User\Deactivate` | class extends ModuleView | `kernel/private/classes/views/user/deactivate.php:30` | user/deactivate: "Deaktivieren und verschieben" und "Reaktivieren und zurückverschieben" für die per POST gesendeten Konten (siehe expUserDeactivation): UserIDArray[] aus Che… | – |
| `Exponential\View\Kernel\User\Edit` | class extends ModuleView | `kernel/private/classes/views/user/edit.php:21` | Der Code von kernel/user/edit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\User\Forgotpassword` | class extends ModuleView | `kernel/private/classes/views/user/forgotpassword.php:21` | Der Code von kernel/user/forgotpassword.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\User\Hygiene` | class extends ModuleView | `kernel/private/classes/views/user/hygiene.php:34` | user/hygiene, "Benutzerkonten prüfen": die Benutzerkonten der Installation oder die unterhalb einer Benutzergruppe, eingegrenzt durch einen Filter und eine Suche (GET-Fil… | – |
| `Exponential\View\Kernel\User\Login` | class extends ModuleView | `kernel/private/classes/views/user/login.php:21` | Der Code von kernel/user/login.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\User\Logout` | class extends ModuleView | `kernel/private/classes/views/user/logout.php:21` | Der Code von kernel/user/logout.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\User\Megamenu` | class extends ModuleView | `kernel/private/classes/views/user/megamenu.php:40` | Die Gruppen und Links des Menüs eines Hauptreiters als HTML-Fragment, ohne das Seitenlayout, für das Mega-Menü von admin4 (menu.ini [TopAdminMenu] MegaMenuTabs[]… | – |
| `Exponential\View\Kernel\User\Password` | class extends ModuleView | `kernel/private/classes/views/user/password.php:24` | Der Code von kernel/user/password.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\User\Preferences` | class extends ModuleView | `kernel/private/classes/views/user/preferences.php:21` | Der Code von kernel/user/preferences.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\User\Register` | class extends ModuleView | `kernel/private/classes/views/user/register.php:195` | Der Code von kernel/user/register.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\User\Registration` | class extends ModuleView | `kernel/private/classes/views/user/registration.php:30` | user/registration/: die Seite, die die signierten Links der Mail „Neuer Benutzer registriert“ an den Administrator öffnen (expRegistrationAdminAction, doc/eng-us/features… | – |
| `Exponential\View\Kernel\User\Sessions` | class extends ModuleView | `kernel/private/classes/views/user/sessions.php:28` | user/sessions: „Ihre Geräte“. | – |
| `Exponential\View\Kernel\User\Setting` | class extends ModuleView | `kernel/private/classes/views/user/setting.php:21` | Der Code von kernel/user/setting.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\User\Success` | class extends ModuleView | `kernel/private/classes/views/user/success.php:21` | Der Code von kernel/user/success.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\User\Unactivated` | class extends ModuleView | `kernel/private/classes/views/user/unactivated.php:31` | user/unactivated: die Benutzer, die sich registriert und ihr Konto nie aktiviert haben (expUnactivatedUsers). | – |
| `Exponential\View\Kernel\Visual\Menuconfig` | class extends ModuleView | `kernel/private/classes/views/visual/menuconfig.php:33` | Design › Menüs (klassisch): die Einstellungen menu.ini [SelectedMenu] eines Siteaccess, die nur die Seitenlayouts der klassischen Designs lesen. | – |
| `Exponential\View\Kernel\Visual\Templatecreate` | class extends ModuleView | `kernel/private/classes/views/visual/templatecreate.php:337` | Der Code von kernel/visual/templatecreate.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Visual\Templateedit` | class extends ModuleView | `kernel/private/classes/views/visual/templateedit.php:21` | Der Code von kernel/visual/templateedit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Visual\Templatelist` | class extends ModuleView | `kernel/private/classes/views/visual/templatelist.php:21` | Der Code von kernel/visual/templatelist.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Visual\Templateview` | class extends ModuleView | `kernel/private/classes/views/visual/templateview.php:21` | Der Code von kernel/visual/templateview.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Visual\Toolbar` | class extends ModuleView | `kernel/private/classes/views/visual/toolbar.php:39` | Der Code von kernel/visual/toolbar.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Visual\Toolbarlist` | class extends ModuleView | `kernel/private/classes/views/visual/toolbarlist.php:21` | Der Code von kernel/visual/toolbarlist.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Workflow\Edit` | class extends ModuleView | `kernel/private/classes/views/workflow/edit.php:21` | Der Code von kernel/workflow/edit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Workflow\Groupedit` | class extends ModuleView | `kernel/private/classes/views/workflow/groupedit.php:21` | Der Code von kernel/workflow/groupedit.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Workflow\Grouplist` | class extends ModuleView | `kernel/private/classes/views/workflow/grouplist.php:73` | Der Code von kernel/workflow/grouplist.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Workflow\Process` | class extends ModuleView | `kernel/private/classes/views/workflow/process.php:21` | Der Code von kernel/workflow/process.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Workflow\Processlist` | class extends ModuleView | `kernel/private/classes/views/workflow/processlist.php:22` | Der Code von kernel/workflow/processlist.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Workflow\Run` | class extends ModuleView | `kernel/private/classes/views/workflow/run.php:21` | Der Code von kernel/workflow/run.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Workflow\View` | class extends ModuleView | `kernel/private/classes/views/workflow/view.php:21` | Der Code von kernel/workflow/view.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Kernel\Workflow\Workflowlist` | class extends ModuleView | `kernel/private/classes/views/workflow/workflowlist.php:56` | Entfernt einen Workflow und alles, was dazugehört. | – |

## Modulansichten (532)

### extension/AdminAid (6)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/aid/user_switch` | `user_switch.php` | user_switch | `extension/AdminAid/modules/aid/module.php:18` |
| `/aid/object_list` | `object_list.php` | object_view | `extension/AdminAid/modules/aid/module.php:19` |
| `/aid/object_view` | `object_view.php` | object_view | `extension/AdminAid/modules/aid/module.php:20` |
| `/aid/class_translation` | `class_translation.php` | class_translate | `extension/AdminAid/modules/aid/module.php:21` |
| `/aid/class_attribute_select` | `class_attribute_select.php` | class_translate | `extension/AdminAid/modules/aid/module.php:22` |
| `/aid/search` | `search.php` | search | `extension/AdminAid/modules/aid/module.php:23` |

### extension/bccie (5)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/bccie/overview` | `overview.php` | read | `extension/bccie/modules/bccie/module.php:27` |
| `/bccie/export` | `export.php` | read | `extension/bccie/modules/bccie/module.php:40` |
| `/bccie/doexport` | `doexport.php` | read | `extension/bccie/modules/bccie/module.php:49` |
| `/bccie/download` | `download.php` | read | `extension/bccie/modules/bccie/module.php:59` |
| `/bccie/job` | `job.php` | read | `extension/bccie/modules/bccie/module.php:69` |

### extension/bcgooglesitemaps (1)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/googlesitemapdynamic/sitemap` | `sitemap.php` | sitemap | `extension/bcgooglesitemaps/modules/googlesitemapdynamic/module.php:15` |

### extension/cjw_newsletter (57)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/newsletter/index` | `index.php` | index | `extension/cjw_newsletter/modules/newsletter/module.php:17` |
| `/newsletter/job` | `job.php` | index | `extension/cjw_newsletter/modules/newsletter/module.php:23` |
| `/newsletter/settings` | `settings.php` | settings | `extension/cjw_newsletter/modules/newsletter/module.php:29` |
| `/newsletter/mailbox_item_list` | `mailbox_item_list.php` | mailbox_item_list | `extension/cjw_newsletter/modules/newsletter/module.php:35` |
| `/newsletter/mailbox_list` | `mailbox_list.php` | mailbox_list | `extension/cjw_newsletter/modules/newsletter/module.php:42` |
| `/newsletter/mailbox_edit` | `mailbox_edit.php` | mailbox_edit | `extension/cjw_newsletter/modules/newsletter/module.php:49` |
| `/newsletter/mailbox_item_view` | `mailbox_item_view.php` | mailbox_item_view | `extension/cjw_newsletter/modules/newsletter/module.php:56` |
| `/newsletter/blacklist_item_list` | `blacklist_item_list.php` | blacklist_item | `extension/cjw_newsletter/modules/newsletter/module.php:63` |
| `/newsletter/blacklist_item_add` | `blacklist_item_add.php` | blacklist_item | `extension/cjw_newsletter/modules/newsletter/module.php:70` |
| `/newsletter/blacklist_item_remove` | `blacklist_item_remove.php` | blacklist_item | `extension/cjw_newsletter/modules/newsletter/module.php:77` |
| `/newsletter/import_list` | `import_list.php` | import_list | `extension/cjw_newsletter/modules/newsletter/module.php:84` |
| `/newsletter/import_view` | `import_view.php` | import_view | `extension/cjw_newsletter/modules/newsletter/module.php:91` |
| `/newsletter/user_list` | `user_list.php` | user_list | `extension/cjw_newsletter/modules/newsletter/module.php:98` |
| `/newsletter/user_view` | `user_view.php` | user_view | `extension/cjw_newsletter/modules/newsletter/module.php:105` |
| `/newsletter/user_remove` | `user_remove.php` | user_remove | `extension/cjw_newsletter/modules/newsletter/module.php:111` |
| `/newsletter/user_edit` | `user_edit.php` | user_edit | `extension/cjw_newsletter/modules/newsletter/module.php:122` |
| `/newsletter/user_create` | `user_create.php` | user_create, user_edit | `extension/cjw_newsletter/modules/newsletter/module.php:135` |
| `/newsletter/subscription_list` | `subscription_list.php` | subscription_list | `extension/cjw_newsletter/modules/newsletter/module.php:148` |
| `/newsletter/subscription_view` | `subscription_view.php` | subscription_view | `extension/cjw_newsletter/modules/newsletter/module.php:154` |
| `/newsletter/subscription_list_csvimport` | `subscription_list_csvimport.php` | subscription_list_csvimport | `extension/cjw_newsletter/modules/newsletter/module.php:160` |
| `/newsletter/subscription_list_csvexport` | `subscription_list_csvexport.php` | subscription_list_csvexport | `extension/cjw_newsletter/modules/newsletter/module.php:166` |
| `/newsletter/subscribe` | `subscribe.php` | subscribe | `extension/cjw_newsletter/modules/newsletter/module.php:173` |
| `/newsletter/subscribe_infomail` | `subscribe_infomail.php` | subscribe | `extension/cjw_newsletter/modules/newsletter/module.php:185` |
| `/newsletter/configure` | `configure.php` | configure | `extension/cjw_newsletter/modules/newsletter/module.php:199` |
| `/newsletter/unsubscribe` | `unsubscribe.php` | unsubscribe | `extension/cjw_newsletter/modules/newsletter/module.php:211` |
| `/newsletter/preview` | `preview.php` | preview | `extension/cjw_newsletter/modules/newsletter/module.php:224` |
| `/newsletter/preview_archive` | `preview_archive.php` | preview | `extension/cjw_newsletter/modules/newsletter/module.php:230` |
| `/newsletter/send` | `send.php` | send | `extension/cjw_newsletter/modules/newsletter/module.php:236` |
| `/newsletter/send_abort` | `send_abort.php` | send | `extension/cjw_newsletter/modules/newsletter/module.php:252` |
| `/newsletter/archive` | `archive.php` | archive | `extension/cjw_newsletter/modules/newsletter/module.php:258` |
| `/newsletter/test_group_list` | `test_group_list.php` | deliverability | `extension/cjw_newsletter/modules/newsletter/module.php:267` |
| `/newsletter/test_group_edit` | `test_group_edit.php` | deliverability | `extension/cjw_newsletter/modules/newsletter/module.php:274` |
| `/newsletter/mailin_address_list` | `mailin_address_list.php` | deliverability | `extension/cjw_newsletter/modules/newsletter/module.php:281` |
| `/newsletter/mailin_address_edit` | `mailin_address_edit.php` | deliverability | `extension/cjw_newsletter/modules/newsletter/module.php:288` |
| `/newsletter/throttle` | `throttle.php` | deliverability | `extension/cjw_newsletter/modules/newsletter/module.php:295` |
| `/newsletter/suppression_import` | `suppression_import.php` | deliverability | `extension/cjw_newsletter/modules/newsletter/module.php:302` |
| `/newsletter/schedule_list` | `schedule_list.php` | editorial | `extension/cjw_newsletter/modules/newsletter/module.php:313` |
| `/newsletter/schedule_edit` | `schedule_edit.php` | editorial | `extension/cjw_newsletter/modules/newsletter/module.php:320` |
| `/newsletter/article_pool_list` | `article_pool_list.php` | editorial | `extension/cjw_newsletter/modules/newsletter/module.php:327` |
| `/newsletter/article_pool_edit` | `article_pool_edit.php` | editorial | `extension/cjw_newsletter/modules/newsletter/module.php:334` |
| `/newsletter/article_pool` | `article_pool.php` | editorial | `extension/cjw_newsletter/modules/newsletter/module.php:341` |
| `/newsletter/approval` | `approval.php` | – | `extension/cjw_newsletter/modules/newsletter/module.php:348` |
| `/newsletter/preview_as` | `preview_as.php` | preview | `extension/cjw_newsletter/modules/newsletter/module.php:360` |
| `/newsletter/interest_list` | `interest_list.php` | rendering | `extension/cjw_newsletter/modules/newsletter/module.php:367` |
| `/newsletter/interest_edit` | `interest_edit.php` | rendering | `extension/cjw_newsletter/modules/newsletter/module.php:374` |
| `/newsletter/skin_preview` | `skin_preview.php` | rendering | `extension/cjw_newsletter/modules/newsletter/module.php:381` |
| `/newsletter/r` | `r.php` | track | `extension/cjw_newsletter/modules/newsletter/module.php:392` |
| `/newsletter/o` | `o.php` | track | `extension/cjw_newsletter/modules/newsletter/module.php:399` |
| `/newsletter/report` | `report.php` | statistics | `extension/cjw_newsletter/modules/newsletter/module.php:406` |
| `/newsletter/statistics_export` | `statistics_export.php` | statistics | `extension/cjw_newsletter/modules/newsletter/module.php:413` |
| `/newsletter/ab_test` | `ab_test.php` | statistics | `extension/cjw_newsletter/modules/newsletter/module.php:420` |
| `/newsletter/sms_send` | `sms_send.php` | sms | `extension/cjw_newsletter/modules/newsletter/module.php:431` |
| `/newsletter/sms_confirm` | `sms_confirm.php` | sms_public | `extension/cjw_newsletter/modules/newsletter/module.php:438` |
| `/newsletter/sms_inbound` | `sms_inbound.php` | sms_public | `extension/cjw_newsletter/modules/newsletter/module.php:445` |
| `/newsletter/import_mapping` | `import_mapping.php` | import_export | `extension/cjw_newsletter/modules/newsletter/module.php:456` |
| `/newsletter/subscriber_export` | `subscriber_export.php` | import_export | `extension/cjw_newsletter/modules/newsletter/module.php:463` |
| `/newsletter/migration_log` | `migration_log.php` | import_export | `extension/cjw_newsletter/modules/newsletter/module.php:470` |

### extension/cjw_themes_jumper (2)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/cjw_paging/page` | `page.php` | – | `extension/cjw_themes_jumper/modules/cjw_paging/module.php:16` |
| `/osm_proxy/tiles` | `tiles.php` | tiles | `extension/cjw_themes_jumper/modules/osm_proxy/module.php:16` |

### extension/expai (9)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/expai/log` | `log.php` | write | `extension/expai/modules/expai/module.php:41` |
| `/expai/budgets` | `budgets.php` | log | `extension/expai/modules/expai/module.php:52` |
| `/expai/dashboard` | `dashboard.php` | write | `extension/expai/modules/expai/module.php:60` |
| `/expai/models` | `models.php` | model_manage | `extension/expai/modules/expai/module.php:72` |
| `/expai/chain` | `chain.php` | chain | `extension/expai/modules/expai/module.php:83` |
| `/expai/vector` | `vector.php` | vector | `extension/expai/modules/expai/module.php:94` |
| `/expai/stream` | `stream.php` | vector | `extension/expai/modules/expai/module.php:102` |
| `/expai/summary` | `summary.php` | summary | `extension/expai/modules/expai/module.php:110` |
| `/expai/alttext` | `alttext.php` | write | `extension/expai/modules/expai/module.php:157` |

### extension/expchangeclass (3)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/changeclass/select_class` | `select_class.php` | convert | `extension/expchangeclass/modules/changeclass/module.php:34` |
| `/changeclass/map_attributes` | `map_attributes.php` | convert | `extension/expchangeclass/modules/changeclass/module.php:38` |
| `/changeclass/action` | `action.php` | convert | `extension/expchangeclass/modules/changeclass/module.php:42` |

### extension/expimageeditor (1)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/expimageeditor/save` | `save.php` | edit | `extension/expimageeditor/modules/expimageeditor/module.php:15` |

### extension/explayouts (9)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/explayouts/dashboard` | `dashboard.php` | read | `extension/explayouts/modules/explayouts/module.php:7` |
| `/explayouts/setup` | `setup.php` | edit | `extension/explayouts/modules/explayouts/module.php:14` |
| `/explayouts/template_editor` | `template_editor.php` | read, edit | `extension/explayouts/modules/explayouts/module.php:21` |
| `/explayouts/layout_list` | `layout_list.php` | read | `extension/explayouts/modules/explayouts/module.php:28` |
| `/explayouts/layout_edit` | `layout_edit.php` | edit | `extension/explayouts/modules/explayouts/module.php:35` |
| `/explayouts/layout_preview` | `preview.php` | read | `extension/explayouts/modules/explayouts/module.php:42` |
| `/explayouts/block_edit` | `block_edit.php` | edit | `extension/explayouts/modules/explayouts/module.php:49` |
| `/explayouts/rule_list` | `rule_list.php` | read | `extension/explayouts/modules/explayouts/module.php:56` |
| `/explayouts/rule_edit` | `rule_edit.php` | edit | `extension/explayouts/modules/explayouts/module.php:63` |

### extension/explayouts_content_browser_ui (1)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/explayouts_content_browser_ui/browser` | `browser.php` | read | `extension/explayouts_content_browser_ui/modules/explayouts_content_browser_ui/module.php:7` |

### extension/explayouts_ui (14)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/explayouts_ui/layout_list` | `layout_list.php` | read | `extension/explayouts_ui/modules/explayouts_ui/module.php:7` |
| `/explayouts_ui/layout_create` | `layout_create.php` | edit | `extension/explayouts_ui/modules/explayouts_ui/module.php:14` |
| `/explayouts_ui/layout_edit` | `layout_edit.php` | edit | `extension/explayouts_ui/modules/explayouts_ui/module.php:21` |
| `/explayouts_ui/rule_list` | `rule_list.php` | read | `extension/explayouts_ui/modules/explayouts_ui/module.php:28` |
| `/explayouts_ui/shared_layouts_list` | `shared_layouts_list.php` | read | `extension/explayouts_ui/modules/explayouts_ui/module.php:35` |
| `/explayouts_ui/rule_edit` | `rule_edit.php` | edit | `extension/explayouts_ui/modules/explayouts_ui/module.php:42` |
| `/explayouts_ui/block_edit` | `block_edit.php` | edit | `extension/explayouts_ui/modules/explayouts_ui/module.php:49` |
| `/explayouts_ui/dashboard` | `dashboard.php` | read | `extension/explayouts_ui/modules/explayouts_ui/module.php:56` |
| `/explayouts_ui/layout_preview` | `preview.php` | read | `extension/explayouts_ui/modules/explayouts_ui/module.php:63` |
| `/explayouts_ui/setup` | `setup.php` | edit | `extension/explayouts_ui/modules/explayouts_ui/module.php:70` |
| `/explayouts_ui/template_editor` | `template_editor.php` | read, edit | `extension/explayouts_ui/modules/explayouts_ui/module.php:77` |
| `/explayouts_ui/transfer_export` | `transfer_export.php` | transfer_export | `extension/explayouts_ui/modules/explayouts_ui/module.php:86` |
| `/explayouts_ui/transfer_import` | `transfer_import.php` | transfer_import | `extension/explayouts_ui/modules/explayouts_ui/module.php:93` |
| `/explayouts_ui/components` | `components.php` | read | `extension/explayouts_ui/modules/explayouts_ui/module.php:100` |

### extension/explayouts_ui_api (4)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/explayouts_ui_api/layouts` | `layouts.php` | read | `extension/explayouts_ui_api/modules/explayouts_ui_api/module.php:7` |
| `/explayouts_ui_api/rules` | `rules.php` | read | `extension/explayouts_ui_api/modules/explayouts_ui_api/module.php:14` |
| `/explayouts_ui_api/blocks` | `blocks.php` | read | `extension/explayouts_ui_api/modules/explayouts_ui_api/module.php:21` |
| `/explayouts_ui_api/app` | `app.php` | read | `extension/explayouts_ui_api/modules/explayouts_ui_api/module.php:28` |

### extension/expqueue (5)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/expqueue/dashboard` | `dashboard.php` | view | `extension/expqueue/modules/expqueue/module.php:14` |
| `/expqueue/list` | `list.php` | view | `extension/expqueue/modules/expqueue/module.php:20` |
| `/expqueue/job` | `job.php` | view | `extension/expqueue/modules/expqueue/module.php:28` |
| `/expqueue/group` | `group.php` | view | `extension/expqueue/modules/expqueue/module.php:34` |
| `/expqueue/action` | `action.php` | manage | `extension/expqueue/modules/expqueue/module.php:41` |

### extension/exp_oe_tiptap (1)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/exp_oe_tiptap/switch` | `switch.php` | switch | `extension/exp_oe_tiptap/modules/exp_oe_tiptap/module.php:16` |

### extension/ezauthorize (2)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/refund/refund` | `order.php` | – | `extension/ezauthorize/modules/refund/module.php:27` |
| `/refund/refund` | `order.php` | refund | `extension/ezauthorize/modules/refund/module.php:35` |

### extension/ezfind (3)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/ezfind/elevate` | `elevate.php` | elevate | `extension/ezfind/modules/ezfind/module.php:11` |
| `/ezfind/elevation_detail` | `elevation_detail.php` | elevate | `extension/ezfind/modules/ezfind/module.php:24` |
| `/ezfind/remove_elevation` | `remove_elevation.php` | elevate | `extension/ezfind/modules/ezfind/module.php:37` |

### extension/ezflow (8)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/ezflow/get` | `get.php` | edit | `extension/ezflow/modules/ezflow/module.php:32` |
| `/ezflow/timeline` | `timeline.php` | timeline | `extension/ezflow/modules/ezflow/module.php:35` |
| `/ezflow/preview` | `preview.php` | timeline | `extension/ezflow/modules/ezflow/module.php:39` |
| `/ezflow/zone` | `zone.php` | edit | `extension/ezflow/modules/ezflow/module.php:43` |
| `/ezflow/request` | `request.php` | edit | `extension/ezflow/modules/ezflow/module.php:47` |
| `/ezflow/push` | `push.php` | edit | `extension/ezflow/modules/ezflow/module.php:52` |
| `/ezflow/block` | `block.php` | call | `extension/ezflow/modules/ezflow/module.php:58` |
| `/flash/embed` | `embed.php` | – | `extension/ezflow/modules/flash/module.php:31` |

### extension/ezie (13)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/ezie/prepare` | `prepare.php` | – | `extension/ezie/modules/ezie/module.php:16` |
| `/ezie/filter_bw` | `filter_bw.php` | – | `extension/ezie/modules/ezie/module.php:21` |
| `/ezie/filter_sepia` | `filter_sepia.php` | – | `extension/ezie/modules/ezie/module.php:22` |
| `/ezie/filter_contrast` | `filter_contrast.php` | – | `extension/ezie/modules/ezie/module.php:23` |
| `/ezie/filter_brightness` | `filter_brightness.php` | – | `extension/ezie/modules/ezie/module.php:24` |
| `/ezie/tool_flip_hor` | `tool_flip_hor.php` | – | `extension/ezie/modules/ezie/module.php:27` |
| `/ezie/tool_flip_ver` | `tool_flip_ver.php` | – | `extension/ezie/modules/ezie/module.php:28` |
| `/ezie/tool_rotation` | `tool_rotation.php` | – | `extension/ezie/modules/ezie/module.php:29` |
| `/ezie/tool_pixelate` | `tool_pixelate.php` | – | `extension/ezie/modules/ezie/module.php:30` |
| `/ezie/tool_crop` | `tool_crop.php` | – | `extension/ezie/modules/ezie/module.php:31` |
| `/ezie/tool_watermark` | `tool_watermark.php` | – | `extension/ezie/modules/ezie/module.php:32` |
| `/ezie/no_save_and_quit` | `no_save_and_quit.php` | – | `extension/ezie/modules/ezie/module.php:35` |
| `/ezie/save_and_quit` | `save_and_quit.php` | – | `extension/ezie/modules/ezie/module.php:36` |

### extension/ezjscore (3)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/ezjscore/hello` | `hello.php` | – | `extension/ezjscore/modules/ezjscore/module.php:35` |
| `/ezjscore/call` | `call.php` | call | `extension/ezjscore/modules/ezjscore/module.php:40` |
| `/ezjscore/run` | `run.php` | run | `extension/ezjscore/modules/ezjscore/module.php:46` |

### extension/ezmbpaex (2)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/userpaex/password` | `password.php` | password | `extension/ezmbpaex/modules/userpaex/module.php:16` |
| `/userpaex/forgotpassword` | `forgotpassword.php` | password | `extension/ezmbpaex/modules/userpaex/module.php:23` |

### extension/ezmultiupload (2)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/ezmultiupload/upload` | `upload.php` | – | `extension/ezmultiupload/modules/ezmultiupload/module.php:15` |
| `/ezmultiupload/service` | `service.php` | – | `extension/ezmultiupload/modules/ezmultiupload/module.php:20` |

### extension/ezodf (11)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/ezodf/import` | `import.php` | import | `extension/ezodf/modules/ezodf/module.php:29` |
| `/ezodf/export` | `export.php` | export | `extension/ezodf/modules/ezodf/module.php:37` |
| `/ezodf/upload` | `upload.php` | import | `extension/ezodf/modules/ezodf/module.php:45` |
| `/ezodf/batch` | `batch.php` | import | `extension/ezodf/modules/ezodf/module.php:50` |
| `/ezodf/batchstatus` | `batchstatus.php` | import | `extension/ezodf/modules/ezodf/module.php:56` |
| `/ezodf/runs` | `runs.php` | import | `extension/ezodf/modules/ezodf/module.php:61` |
| `/ezodf/exportfile` | `exportfile.php` | export | `extension/ezodf/modules/ezodf/module.php:67` |
| `/ezodf/exportstatus` | `exportstatus.php` | export | `extension/ezodf/modules/ezodf/module.php:72` |
| `/ezodf/converter` | `converter.php` | converter | `extension/ezodf/modules/ezodf/module.php:77` |
| `/ezodf/converterstatus` | `converterstatus.php` | converter | `extension/ezodf/modules/ezodf/module.php:83` |
| `/ezodf/converterfile` | `converterfile.php` | converter | `extension/ezodf/modules/ezodf/module.php:88` |

### extension/ezoe (8)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/ezoe/relations` | `relations.php` | editor | `extension/ezoe/modules/ezoe/module.php:34` |
| `/ezoe/upload` | `upload.php` | editor | `extension/ezoe/modules/ezoe/module.php:41` |
| `/ezoe/tags` | `tags.php` | editor | `extension/ezoe/modules/ezoe/module.php:48` |
| `/ezoe/dialog` | `dialog.php` | editor | `extension/ezoe/modules/ezoe/module.php:55` |
| `/ezoe/embed_view` | `embed_view.php` | editor | `extension/ezoe/modules/ezoe/module.php:62` |
| `/ezoe/load` | `load.php` | editor | `extension/ezoe/modules/ezoe/module.php:68` |
| `/ezoe/spellcheck_rpc` | `spellcheck_rpc.php` | editor | `extension/ezoe/modules/ezoe/module.php:74` |
| `/ezoe/engine` | `engine.php` | editor | `extension/ezoe/modules/ezoe/module.php:146` |

### extension/ezownerchange (1)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/owner/change` | `change.php` | change | `extension/ezownerchange/modules/owner/module.php:22` |

### extension/ezpaypal (1)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/paypal/notify_url` | `notify_url.php` | – | `extension/ezpaypal/modules/paypal/module.php:33` |

### extension/ezpm (12)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/pm/message` | `view_message.php` | – | `extension/ezpm/modules/pm/module.php:7` |
| `/pm/list` | `list.php` | – | `extension/ezpm/modules/pm/module.php:11` |
| `/pm/list_inbox` | `list.php` | – | `extension/ezpm/modules/pm/module.php:15` |
| `/pm/list_sent` | `list_sent.php` | – | `extension/ezpm/modules/pm/module.php:19` |
| `/pm/list_drafts` | `list_drafts.php` | – | `extension/ezpm/modules/pm/module.php:23` |
| `/pm/create` | `create.php` | – | `extension/ezpm/modules/pm/module.php:27` |
| `/pm/reply` | `reply.php` | – | `extension/ezpm/modules/pm/module.php:31` |
| `/pm/edit` | `edit.php` | – | `extension/ezpm/modules/pm/module.php:35` |
| `/pm/add` | `add.php` | – | `extension/ezpm/modules/pm/module.php:39` |
| `/pm/action` | `action.php` | – | `extension/ezpm/modules/pm/module.php:46` |
| `/pm/blacklist` | `blacklist.php` | – | `extension/ezpm/modules/pm/module.php:59` |
| `/pm/contacts` | `contact_list.php` | – | `extension/ezpm/modules/pm/module.php:63` |

### extension/eztags (17)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/tags/treemenu` | `treemenu.php` | read | `extension/eztags/modules/tags/module.php:7` |
| `/tags/dashboard` | `dashboard.php` | dashboard | `extension/eztags/modules/tags/module.php:13` |
| `/tags/id` | `id.php` | id | `extension/eztags/modules/tags/module.php:20` |
| `/tags/list_objects` | `list_objects.php` | id | `extension/eztags/modules/tags/module.php:27` |
| `/tags/view` | `view.php` | view | `extension/eztags/modules/tags/module.php:34` |
| `/tags/add` | `add.php` | add | `extension/eztags/modules/tags/module.php:41` |
| `/tags/addsynonym` | `addsynonym.php` | addsynonym | `extension/eztags/modules/tags/module.php:47` |
| `/tags/edit` | `edit.php` | edit | `extension/eztags/modules/tags/module.php:53` |
| `/tags/movetags` | `movetags.php` | edit | `extension/eztags/modules/tags/module.php:59` |
| `/tags/translation` | `translation.php` | edit | `extension/eztags/modules/tags/module.php:65` |
| `/tags/editsynonym` | `editsynonym.php` | editsynonym | `extension/eztags/modules/tags/module.php:71` |
| `/tags/delete` | `delete.php` | delete | `extension/eztags/modules/tags/module.php:77` |
| `/tags/deletetags` | `deletetags.php` | delete | `extension/eztags/modules/tags/module.php:83` |
| `/tags/deletesynonym` | `deletesynonym.php` | deletesynonym | `extension/eztags/modules/tags/module.php:89` |
| `/tags/makesynonym` | `makesynonym.php` | makesynonym | `extension/eztags/modules/tags/module.php:95` |
| `/tags/merge` | `merge.php` | merge | `extension/eztags/modules/tags/module.php:101` |
| `/tags/search` | `search.php` | search | `extension/eztags/modules/tags/module.php:107` |

### extension/ezwt (1)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/websitetoolbar/sort` | `sort.php` | use | `extension/ezwt/modules/websitetoolbar/module.php:35` |

### extension/nxc_powercontent (40)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/content/edit` | `edit.php` | bearbeiten oder anlegen | `extension/nxc_powercontent/modules/content/module.php:14` |
| `/content/removenode` | `removenode.php` | edit | `extension/nxc_powercontent/modules/content/module.php:49` |
| `/content/removeassignment` | `removeassignment.php` | edit | `extension/nxc_powercontent/modules/content/module.php:58` |
| `/content/pdf` | `pdf.php` | pdf | `extension/nxc_powercontent/modules/content/module.php:68` |
| `/content/view` | `view.php` | read | `extension/nxc_powercontent/modules/content/module.php:80` |
| `/content/copy` | `copy.php` | read | `extension/nxc_powercontent/modules/content/module.php:92` |
| `/content/copysubtree` | `copysubtree.php` | create | `extension/nxc_powercontent/modules/content/module.php:103` |
| `/content/versionview` | `versionview.php` | versionread | `extension/nxc_powercontent/modules/content/module.php:117` |
| `/content/restore` | `restore.php` | restore | `extension/nxc_powercontent/modules/content/module.php:134` |
| `/content/search` | `search.php` | read | `extension/nxc_powercontent/modules/content/module.php:145` |
| `/content/urlalias` | `urlalias.php` | edit | `extension/nxc_powercontent/modules/content/module.php:152` |
| `/content/urltranslator` | `urlalias_global.php` | urltranslator | `extension/nxc_powercontent/modules/content/module.php:166` |
| `/content/urlwildcards` | `urlalias_wildcard.php` | urltranslator | `extension/nxc_powercontent/modules/content/module.php:186` |
| `/content/advancedsearch` | `advancedsearch.php` | read | `extension/nxc_powercontent/modules/content/module.php:203` |
| `/content/browse` | `browse.php` | read | `extension/nxc_powercontent/modules/content/module.php:210` |
| `/content/upload` | `upload.php` | create | `extension/nxc_powercontent/modules/content/module.php:218` |
| `/content/removeobject` | `removeobject.php` | read | `extension/nxc_powercontent/modules/content/module.php:228` |
| `/content/removeuserobject` | `removeobject.php` | read | `extension/nxc_powercontent/modules/content/module.php:234` |
| `/content/removemediaobject` | `removeobject.php` | read | `extension/nxc_powercontent/modules/content/module.php:240` |
| `/content/removeeditversion` | `removeeditversion.php` | read | `extension/nxc_powercontent/modules/content/module.php:246` |
| `/content/download` | `download.php` | read | `extension/nxc_powercontent/modules/content/module.php:253` |
| `/content/action` | `action.php` | read | `extension/nxc_powercontent/modules/content/module.php:260` |
| `/content/collectinformation` | `collectinformation.php` | read | `extension/nxc_powercontent/modules/content/module.php:328` |
| `/content/draft` | `draft.php` | edit | `extension/nxc_powercontent/modules/content/module.php:338` |
| `/content/history` | `history.php` | read, edit | `extension/nxc_powercontent/modules/content/module.php:345` |
| `/content/trash` | `trash.php` | restore | `extension/nxc_powercontent/modules/content/module.php:360` |
| `/content/translations` | `translations.php` | translations | `extension/nxc_powercontent/modules/content/module.php:367` |
| `/content/tipafriend` | `tipafriend.php` | tipafriend, read | `extension/nxc_powercontent/modules/content/module.php:383` |
| `/content/keyword` | `keyword.php` | read | `extension/nxc_powercontent/modules/content/module.php:389` |
| `/content/collectedinfo` | `collectedinfo.php` | read | `extension/nxc_powercontent/modules/content/module.php:396` |
| `/content/bookmark` | `bookmark.php` | bookmark | `extension/nxc_powercontent/modules/content/module.php:402` |
| `/content/pendinglist` | `pendinglist.php` | pendinglist | `extension/nxc_powercontent/modules/content/module.php:413` |
| `/content/new` | `newcontent.php` | read | `extension/nxc_powercontent/modules/content/module.php:420` |
| `/content/hide` | `hide.php` | hide | `extension/nxc_powercontent/modules/content/module.php:426` |
| `/content/move` | `move.php` | edit | `extension/nxc_powercontent/modules/content/module.php:432` |
| `/content/reverserelatedlist` | `reverserelatedlist.php` | reverserelatedlist | `extension/nxc_powercontent/modules/content/module.php:438` |
| `/content/translation` | `translation.php` | read | `extension/nxc_powercontent/modules/content/module.php:445` |
| `/content/treemenu` | `treemenu.php` | read | `extension/nxc_powercontent/modules/content/module.php:474` |
| `/content/dashboard` | `dashboard.php` | dashboard | `extension/nxc_powercontent/modules/content/module.php:481` |
| `/content/queued` | `queued.php` | edit | `extension/nxc_powercontent/modules/content/module.php:488` |

### extension/powercontent (1)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/powercontent/action` | `action.php` | – | `extension/powercontent/modules/powercontent/module.php:6` |

### extension/sevenx_authentication_2fa (4)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/user2fa/verify` | `verify.php` | verify | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:17` |
| `/user2fa/setup` | `setup.php` | setup | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:25` |
| `/user2fa/callback` | `callback.php` | callback | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:33` |
| `/user2fa/oauth` | `oauth.php` | oauth | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:41` |

### extension/sevenx_themes_media (2)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/info-collection/view-modal` | `viewmodal.php` | read | `extension/sevenx_themes_media/modules/info-collection/module.php:6` |
| `/info-collection/submit` | `submit.php` | read | `extension/sevenx_themes_media/modules/info-collection/module.php:13` |

### extension/sqliimport (20)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/sqliimport/list` | `list.php` | – | `extension/sqliimport/modules/sqliimport/module.php:36` |
| `/sqliimport/run` | `run.php` | – | `extension/sqliimport/modules/sqliimport/module.php:44` |
| `/sqliimport/mapping` | `mapping.php` | – | `extension/sqliimport/modules/sqliimport/module.php:52` |
| `/sqliimport/report` | `report.php` | – | `extension/sqliimport/modules/sqliimport/module.php:59` |
| `/sqliimport/statistics` | `statistics.php` | – | `extension/sqliimport/modules/sqliimport/module.php:67` |
| `/sqliimport/action` | `action.php` | – | `extension/sqliimport/modules/sqliimport/module.php:77` |
| `/sqliimport/edit` | `edit.php` | manage oder manageimports | `extension/sqliimport/modules/sqliimport/module.php:84` |
| `/sqliimport/schedules` | `schedules.php` | – | `extension/sqliimport/modules/sqliimport/module.php:92` |
| `/sqliimport/catalogue` | `catalogue.php` | – | `extension/sqliimport/modules/sqliimport/module.php:100` |
| `/sqliimport/settings` | `settings.php` | manage oder manageimports | `extension/sqliimport/modules/sqliimport/module.php:108` |
| `/sqliimport/transfer` | `transfer.php` | manage oder manageimports | `extension/sqliimport/modules/sqliimport/module.php:116` |
| `/sqliimport/templates` | `templates.php` | manage oder manageimports | `extension/sqliimport/modules/sqliimport/module.php:124` |
| `/sqliimport/trigger` | `trigger.php` | – | `extension/sqliimport/modules/sqliimport/module.php:135` |
| `/sqliimport/items` | `items.php` | – | `extension/sqliimport/modules/sqliimport/module.php:143` |
| `/sqliimport/scheduledlist` | `scheduledlist.php` | – | `extension/sqliimport/modules/sqliimport/module.php:151` |
| `/sqliimport/addimport` | `addimport.php` | – | `extension/sqliimport/modules/sqliimport/module.php:158` |
| `/sqliimport/addscheduled` | `addscheduled.php` | – | `extension/sqliimport/modules/sqliimport/module.php:167` |
| `/sqliimport/removescheduled` | `removescheduled.php` | – | `extension/sqliimport/modules/sqliimport/module.php:186` |
| `/sqliimport/alterimport` | `alterimport.php` | – | `extension/sqliimport/modules/sqliimport/module.php:193` |
| `/sqliimport/purgelist` | `purgelist.php` | – | `extension/sqliimport/modules/sqliimport/module.php:200` |

### extension/syndication (13)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/syndication/menu` | `menu.php` | menu | `extension/syndication/modules/syndication/module.php:43` |
| `/syndication/list` | `list.php` | view_export | `extension/syndication/modules/syndication/module.php:48` |
| `/syndication/import_list` | `import_list.php` | view_export | `extension/syndication/modules/syndication/module.php:54` |
| `/syndication/import_edit` | `import_edit.php` | edit_import | `extension/syndication/modules/syndication/module.php:60` |
| `/syndication/pending_edit` | `pending_edit.php` | import_object_status | `extension/syndication/modules/syndication/module.php:67` |
| `/syndication/edit` | `edit.php` | edit_export | `extension/syndication/modules/syndication/module.php:75` |
| `/syndication/add_feed_source` | `add_feed_source.php` | edit_export | `extension/syndication/modules/syndication/module.php:81` |
| `/syndication/list_source_filter` | `list_source_filter.php` | edit_export | `extension/syndication/modules/syndication/module.php:88` |
| `/syndication/edit_source_filter` | `edit_source_filter.php` | edit_export | `extension/syndication/modules/syndication/module.php:94` |
| `/syndication/edit_import_filter` | `edit_import_filter.php` | edit_import | `extension/syndication/modules/syndication/module.php:100` |
| `/syndication/import_info` | `import_info.php` | view_import | `extension/syndication/modules/syndication/module.php:108` |
| `/syndication/feed_info` | `feed_info.php` | view_export_info | `extension/syndication/modules/syndication/module.php:115` |
| `/syndication/job` | `job.php` | menu | `extension/syndication/modules/syndication/module.php:122` |

### extension/xrowextract (9)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/xrowextract/csv` | `csv.php` | csv | `extension/xrowextract/modules/xrowextract/module.php:6` |
| `/xrowextract/upload_chunk` | `upload_chunk.php` | import | `extension/xrowextract/modules/xrowextract/module.php:56` |
| `/xrowextract/package` | `package.php` | import | `extension/xrowextract/modules/xrowextract/module.php:65` |
| `/xrowextract/schedules` | `schedules.php` | schedule | `extension/xrowextract/modules/xrowextract/module.php:74` |
| `/xrowextract/destinations` | `destinations.php` | destinations | `extension/xrowextract/modules/xrowextract/module.php:80` |
| `/xrowextract/history` | `history.php` | history | `extension/xrowextract/modules/xrowextract/module.php:86` |
| `/xrowextract/browse` | `browse.php` | import | `extension/xrowextract/modules/xrowextract/module.php:96` |
| `/xrowextract/compare` | `compare.php` | import | `extension/xrowextract/modules/xrowextract/module.php:105` |
| `/xrowextract/browse_file` | `browse_file.php` | import | `extension/xrowextract/modules/xrowextract/module.php:111` |

### extension/xrowmetadata (2)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/sitemaps/index` | `index.php` | – | `extension/xrowmetadata/modules/sitemaps/module.php:9` |
| `/sitemaps/robots` | `robots.php` | – | `extension/xrowmetadata/modules/sitemaps/module.php:12` |

### kernel/modules (231)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/apikey/list` | `list.php` | – | `kernel/apikey/module.php:24` |
| `/audit/dashboard` | `dashboard.php` | read | `kernel/audit/module.php:27` |
| `/audit/console` | `console.php` | read | `kernel/audit/module.php:33` |
| `/audit/event` | `event.php` | read | `kernel/audit/module.php:40` |
| `/audit/charts` | `charts.php` | read | `kernel/audit/module.php:47` |
| `/audit/alerts` | `alerts.php` | read | `kernel/audit/module.php:54` |
| `/audit/export` | `export.php` | read | `kernel/audit/module.php:61` |
| `/audit/archives` | `archives.php` | manage | `kernel/audit/module.php:68` |
| `/audit/settings` | `settings.php` | manage | `kernel/audit/module.php:75` |
| `/audit/recent` | `recent.php` | read | `kernel/audit/module.php:81` |
| `/class/edit` | `edit.php` | – | `kernel/class/module.php:13` |
| `/class/view` | `view.php` | – | `kernel/class/module.php:20` |
| `/class/copy` | `copy.php` | – | `kernel/class/module.php:26` |
| `/class/down` | `edit.php` | – | `kernel/class/module.php:31` |
| `/class/up` | `edit.php` | – | `kernel/class/module.php:35` |
| `/class/removeclass` | `removeclass.php` | – | `kernel/class/module.php:39` |
| `/class/removegroup` | `removegroup.php` | – | `kernel/class/module.php:43` |
| `/class/classlist` | `classlist.php` | – | `kernel/class/module.php:47` |
| `/class/grouplist` | `grouplist.php` | – | `kernel/class/module.php:51` |
| `/class/groupedit` | `groupedit.php` | – | `kernel/class/module.php:55` |
| `/class/translation` | `translation.php` | – | `kernel/class/module.php:60` |
| `/collaboration/action` | `action.php` | – | `kernel/collaboration/module.php:13` |
| `/collaboration/view` | `view.php` | – | `kernel/collaboration/module.php:24` |
| `/collaboration/item` | `item.php` | – | `kernel/collaboration/module.php:34` |
| `/collaboration/group` | `group.php` | – | `kernel/collaboration/module.php:40` |
| `/content/multiedit` | `multiedit.php` | edit | `kernel/content/module.php:23` |
| `/content/edit` | `edit.php` | bearbeiten oder anlegen | `kernel/content/module.php:34` |
| `/content/removenode` | `removenode.php` | edit | `kernel/content/module.php:70` |
| `/content/removeassignment` | `removeassignment.php` | edit | `kernel/content/module.php:79` |
| `/content/pdf` | `pdf.php` | pdf | `kernel/content/module.php:89` |
| `/content/view` | `view.php` | read | `kernel/content/module.php:101` |
| `/content/copy` | `copy.php` | read | `kernel/content/module.php:113` |
| `/content/copysubtree` | `copysubtree.php` | create | `kernel/content/module.php:124` |
| `/content/versionview` | `versionview.php` | versionread | `kernel/content/module.php:138` |
| `/content/restore` | `restore.php` | restore | `kernel/content/module.php:158` |
| `/content/search` | `search.php` | read | `kernel/content/module.php:169` |
| `/content/urlalias` | `urlalias.php` | edit | `kernel/content/module.php:176` |
| `/content/urltranslator` | `urlalias_global.php` | urltranslator | `kernel/content/module.php:190` |
| `/content/urlwildcards` | `urlalias_wildcard.php` | urltranslator | `kernel/content/module.php:210` |
| `/content/advancedsearch` | `advancedsearch.php` | read | `kernel/content/module.php:227` |
| `/content/browse` | `browse.php` | read | `kernel/content/module.php:234` |
| `/content/upload` | `upload.php` | create | `kernel/content/module.php:242` |
| `/content/removeobject` | `removeobject.php` | read | `kernel/content/module.php:252` |
| `/content/removeuserobject` | `removeobject.php` | read | `kernel/content/module.php:258` |
| `/content/removemediaobject` | `removeobject.php` | read | `kernel/content/module.php:264` |
| `/content/removeeditversion` | `removeeditversion.php` | read | `kernel/content/module.php:270` |
| `/content/download` | `download.php` | read | `kernel/content/module.php:277` |
| `/content/action` | `action.php` | read | `kernel/content/module.php:284` |
| `/content/collectinformation` | `collectinformation.php` | read | `kernel/content/module.php:356` |
| `/content/draft` | `draft.php` | edit | `kernel/content/module.php:366` |
| `/content/history` | `history.php` | lesen oder bearbeiten | `kernel/content/module.php:373` |
| `/content/trash` | `trash.php` | restore | `kernel/content/module.php:389` |
| `/content/translations` | `translations.php` | translations | `kernel/content/module.php:396` |
| `/content/tipafriend` | `tipafriend.php` | tipafriend, read | `kernel/content/module.php:412` |
| `/content/keyword` | `keyword.php` | read | `kernel/content/module.php:418` |
| `/content/collectedinfo` | `collectedinfo.php` | read | `kernel/content/module.php:425` |
| `/content/bookmark` | `bookmark.php` | bookmark | `kernel/content/module.php:431` |
| `/content/pendinglist` | `pendinglist.php` | pendinglist | `kernel/content/module.php:442` |
| `/content/new` | `newcontent.php` | read | `kernel/content/module.php:449` |
| `/content/hide` | `hide.php` | hide | `kernel/content/module.php:455` |
| `/content/move` | `move.php` | edit | `kernel/content/module.php:463` |
| `/content/reverserelatedlist` | `reverserelatedlist.php` | reverserelatedlist | `kernel/content/module.php:469` |
| `/content/translation` | `translation.php` | read | `kernel/content/module.php:476` |
| `/content/treemenu` | `treemenu.php` | read | `kernel/content/module.php:505` |
| `/content/subitemsexport` | `subitemsexport.php` | read | `kernel/content/module.php:513` |
| `/content/dashboard` | `dashboard.php` | dashboard | `kernel/content/module.php:520` |
| `/content/queued` | `queued.php` | edit | `kernel/content/module.php:527` |
| `/content/job` | `job.php` | read | `kernel/content/module.php:536` |
| `/content/jobs` | `jobs.php` | read | `kernel/content/module.php:544` |
| `/contentcheck/unreferenced` | `unreferenced.php` | unreferenced | `kernel/contentcheck/module.php:28` |
| `/contentcheck/trash` | `trash.php` | unreferenced, trash | `kernel/contentcheck/module.php:37` |
| `/contentcheck/brokenrelations` | `brokenrelations.php` | brokenrelations | `kernel/contentcheck/module.php:46` |
| `/contentcheck/fixfiles` | `fixfiles.php` | brokenrelations, fixfiles | `kernel/contentcheck/module.php:56` |
| `/contentcheck/scope` | `scope.php` | – | `kernel/contentcheck/module.php:64` |
| `/contentcheck/storage` | `storage.php` | storage | `kernel/contentcheck/module.php:72` |
| `/contentcheck/storageaction` | `storageaction.php` | storage | `kernel/contentcheck/module.php:81` |
| `/contentfinder/search` | `search.php` | search | `kernel/contentfinder/module.php:25` |
| `/contentfinder/action` | `action.php` | search, delete | `kernel/contentfinder/module.php:33` |
| `/csp/report` | `report.php` | – | `kernel/csp/module.php:22` |
| `/datatable/dashboard` | `dashboard.php` | read | `kernel/datatable/module.php:21` |
| `/datatable/table` | `table.php` | read | `kernel/datatable/module.php:28` |
| `/datatable/rows` | `rows.php` | read | `kernel/datatable/module.php:34` |
| `/datatable/schema` | `schema.php` | manage | `kernel/datatable/module.php:41` |
| `/datatable/syncs` | `syncs.php` | sync | `kernel/datatable/module.php:48` |
| `/datatable/sync` | `sync.php` | sync | `kernel/datatable/module.php:54` |
| `/datatable/conflicts` | `conflicts.php` | sync | `kernel/datatable/module.php:61` |
| `/datatable/connections` | `connections.php` | connections | `kernel/datatable/module.php:68` |
| `/datatable/connection` | `connection.php` | connections | `kernel/datatable/module.php:74` |
| `/error/view` | `view.php` | – | `kernel/error/module.php:13` |
| `/exphelp/dashboard` | `dashboard.php` | read | `kernel/exphelp/module.php:41` |
| `/exphelp/audience` | `audience.php` | read | `kernel/exphelp/module.php:49` |
| `/exphelp/viewer` | `viewer.php` | read | `kernel/exphelp/module.php:59` |
| `/exphelp/contents` | `contents.php` | read | `kernel/exphelp/module.php:67` |
| `/exphelp/search` | `search.php` | read | `kernel/exphelp/module.php:77` |
| `/exphelp/extensions` | `extensions.php` | read | `kernel/exphelp/module.php:93` |
| `/exphelp/asset` | `asset.php` | read | `kernel/exphelp/module.php:100` |
| `/exphelp/image` | `image.php` | read | `kernel/exphelp/module.php:108` |
| `/exphelp/pdf` | `pdf.php` | read | `kernel/exphelp/module.php:116` |
| `/exphelp/report` | `report.php` | read | `kernel/exphelp/module.php:123` |
| `/exphelp/topic` | `topic.php` | read | `kernel/exphelp/module.php:132` |
| `/exphelp/state` | `state.php` | read | `kernel/exphelp/module.php:143` |
| `/exphelp/feedback` | `feedback.php` | – | `kernel/exphelp/module.php:151` |
| `/exphelp/manual` | `manual.php` | read | `kernel/exphelp/module.php:159` |
| `/exphelp/context` | `context.php` | context | `kernel/exphelp/module.php:168` |
| `/ezinfo/copyright` | `copyright.php` | read | `kernel/ezinfo/module.php:14` |
| `/ezinfo/about` | `about.php` | read | `kernel/ezinfo/module.php:21` |
| `/ezinfo/is_alive` | `isalive.php` | read | `kernel/ezinfo/module.php:27` |
| `/infocollector/overview` | `overview.php` | read | `kernel/infocollector/module.php:13` |
| `/infocollector/collectionlist` | `collectionlist.php` | read | `kernel/infocollector/module.php:23` |
| `/infocollector/view` | `view.php` | read | `kernel/infocollector/module.php:34` |
| `/infocollector/export` | `export.php` | read | `kernel/infocollector/module.php:43` |
| `/layout/set` | `set.php` | – | `kernel/layout/module.php:14` |
| `/mailpreferences/settings` | `settings.php` | – | `kernel/mailpreferences/module.php:22` |
| `/mailpreferences/manage` | `manage.php` | – | `kernel/mailpreferences/module.php:30` |
| `/mailpreferences/unsubscribe` | `unsubscribe.php` | – | `kernel/mailpreferences/module.php:37` |
| `/mailpreferences/confirm` | `confirm.php` | – | `kernel/mailpreferences/module.php:43` |
| `/mailpreferences/request` | `request.php` | – | `kernel/mailpreferences/module.php:49` |
| `/mailpreferences/export` | `export.php` | – | `kernel/mailpreferences/module.php:55` |
| `/mailpreferences/admin` | `admin.php` | administrate | `kernel/mailpreferences/module.php:62` |
| `/notification/settings` | `settings.php` | use | `kernel/notification/module.php:15` |
| `/notification/runfilter` | `runfilter.php` | administrate | `kernel/notification/module.php:23` |
| `/notification/status` | `status.php` | administrate | `kernel/notification/module.php:31` |
| `/notification/job` | `job.php` | administrate | `kernel/notification/module.php:39` |
| `/notification/addtonotification` | `addtonotification.php` | use | `kernel/notification/module.php:46` |
| `/package/list` | `list.php` | list | `kernel/package/module.php:13` |
| `/package/export` | `export.php` | export | `kernel/package/module.php:62` |
| `/package/view` | `view.php` | read | `kernel/package/module.php:71` |
| `/pdf/edit` | `edit.php` | edit | `kernel/pdf/module.php:13` |
| `/pdf/list` | `list.php` | edit | `kernel/pdf/module.php:48` |
| `/role/list` | `list.php` | – | `kernel/role/module.php:13` |
| `/role/edit` | `edit.php` | – | `kernel/role/module.php:19` |
| `/role/copy` | `copy.php` | – | `kernel/role/module.php:24` |
| `/role/policyedit` | `policyedit.php` | – | `kernel/role/module.php:29` |
| `/role/view` | `view.php` | – | `kernel/role/module.php:34` |
| `/role/assign` | `assign.php` | – | `kernel/role/module.php:39` |
| `/rss/list` | `list.php` | edit | `kernel/rss/module.php:13` |
| `/rss/edit_export` | `edit_export.php` | edit | `kernel/rss/module.php:28` |
| `/rss/edit_import` | `edit_import.php` | edit | `kernel/rss/module.php:41` |
| `/rss/feed` | `feed.php` | feed | `kernel/rss/module.php:55` |
| `/search/stats` | `stats.php` | – | `kernel/search/module.php:15` |
| `/secrets/list` | `list.php` | manage | `kernel/secrets/module.php:19` |
| `/section/list` | `list.php` | ansehen, bearbeiten oder zuweisen | `kernel/section/module.php:13` |
| `/section/view` | `view.php` | ansehen oder zuweisen | `kernel/section/module.php:20` |
| `/section/edit` | `edit.php` | edit | `kernel/section/module.php:28` |
| `/section/assign` | `assign.php` | assign | `kernel/section/module.php:35` |
| `/settings/view` | `view.php` | – | `kernel/settings/module.php:14` |
| `/settings/edit` | `edit.php` | – | `kernel/settings/module.php:21` |
| `/setup/init` | `ezsetup.php` | install | `kernel/setup/module.php:18` |
| `/setup/cache` | `cache.php` | managecache | `kernel/setup/module.php:25` |
| `/shop/add` | `add.php` | buy | `kernel/shop/module.php:14` |
| `/shop/orderview` | `orderview.php` | buy | `kernel/shop/module.php:20` |
| `/shop/orderreceipt` | `orderreceipt.php` | – | `kernel/shop/module.php:28` |
| `/shop/orderreciept` | `orderreciept.php` | – | `kernel/shop/module.php:36` |
| `/shop/updatebasket` | `updatebasket.php` | buy | `kernel/shop/module.php:42` |
| `/shop/basket` | `basket.php` | buy | `kernel/shop/module.php:48` |
| `/shop/register` | `register.php` | buy | `kernel/shop/module.php:70` |
| `/shop/userregister` | `userregister.php` | buy | `kernel/shop/module.php:80` |
| `/shop/wishlist` | `wishlist.php` | buy | `kernel/shop/module.php:90` |
| `/shop/dashboard` | `dashboard.php` | administrate | `kernel/shop/module.php:99` |
| `/shop/orderlist` | `orderlist.php` | administrate | `kernel/shop/module.php:105` |
| `/shop/archivelist` | `archivelist.php` | administrate | `kernel/shop/module.php:112` |
| `/shop/removeorder` | `removeorder.php` | administrate | `kernel/shop/module.php:119` |
| `/shop/archiveorder` | `archiveorder.php` | administrate | `kernel/shop/module.php:126` |
| `/shop/unarchiveorder` | `unarchiveorder.php` | administrate | `kernel/shop/module.php:133` |
| `/shop/customerlist` | `customerlist.php` | administrate | `kernel/shop/module.php:141` |
| `/shop/customerorderview` | `customerorderview.php` | administrate | `kernel/shop/module.php:148` |
| `/shop/statistics` | `orderstatistics.php` | administrate | `kernel/shop/module.php:154` |
| `/shop/confirmorder` | `confirmorder.php` | buy | `kernel/shop/module.php:160` |
| `/shop/checkout` | `checkout.php` | buy | `kernel/shop/module.php:166` |
| `/shop/vattype` | `vattype.php` | setup | `kernel/shop/module.php:172` |
| `/shop/vatrules` | `vatrules.php` | setup | `kernel/shop/module.php:185` |
| `/shop/editvatrule` | `editvatrule.php` | setup | `kernel/shop/module.php:191` |
| `/shop/productcategories` | `productcategories.php` | setup | `kernel/shop/module.php:209` |
| `/shop/discountgroup` | `discountgroup.php` | setup | `kernel/shop/module.php:222` |
| `/shop/discountgroupedit` | `discountgroupedit.php` | setup | `kernel/shop/module.php:228` |
| `/shop/discountruleedit` | `discountruleedit.php` | setup | `kernel/shop/module.php:235` |
| `/shop/discountgroupview` | `discountgroupmembershipview.php` | setup | `kernel/shop/module.php:243` |
| `/shop/status` | `status.php` | edit_status | `kernel/shop/module.php:250` |
| `/shop/setstatus` | `setstatus.php` | setstatus | `kernel/shop/module.php:256` |
| `/shop/currencylist` | `currencylist.php` | setup | `kernel/shop/module.php:262` |
| `/shop/editcurrency` | `editcurrency.php` | setup | `kernel/shop/module.php:279` |
| `/shop/preferredcurrency` | `preferredcurrency.php` | buy | `kernel/shop/module.php:292` |
| `/shop/productsoverview` | `productsoverview.php` | administrate | `kernel/shop/module.php:298` |
| `/shop/setpreferredcurrency` | `setpreferredcurrency.php` | buy | `kernel/shop/module.php:312` |
| `/shop/setusercountry` | `setusercountry.php` | buy | `kernel/shop/module.php:321` |
| `/state/assign` | `assign.php` | assign | `kernel/state/module.php:15` |
| `/state/groups` | `groups.php` | administrate | `kernel/state/module.php:26` |
| `/state/group` | `group.php` | administrate | `kernel/state/module.php:37` |
| `/state/group_edit` | `group_edit.php` | administrate | `kernel/state/module.php:50` |
| `/state/view` | `view.php` | administrate | `kernel/state/module.php:60` |
| `/state/edit` | `edit.php` | administrate | `kernel/state/module.php:68` |
| `/trigger/list` | `list.php` | – | `kernel/trigger/module.php:13` |
| `/uiswitch/set` | `set.php` | – | `kernel/uiswitch/module.php:24` |
| `/url/list` | `list.php` | – | `kernel/url/module.php:13` |
| `/url/view` | `view.php` | – | `kernel/url/module.php:22` |
| `/url/edit` | `edit.php` | – | `kernel/url/module.php:28` |
| `/user/logout` | `logout.php` | login | `kernel/user/module.php:14` |
| `/user/login` | `login.php` | login | `kernel/user/module.php:20` |
| `/user/setting` | `setting.php` | preferences | `kernel/user/module.php:34` |
| `/user/preferences` | `preferences.php` | login | `kernel/user/module.php:40` |
| `/user/megamenu` | `megamenu.php` | login | `kernel/user/module.php:52` |
| `/user/passkeys` | `passkeys.php` | login | `kernel/user/module.php:61` |
| `/user/password` | `password.php` | password | `kernel/user/module.php:67` |
| `/user/forgotpassword` | `forgotpassword.php` | password | `kernel/user/module.php:74` |
| `/user/edit` | `edit.php` | login | `kernel/user/module.php:85` |
| `/user/register` | `register.php` | register | `kernel/user/module.php:95` |
| `/user/activate` | `activate.php` | login | `kernel/user/module.php:105` |
| `/user/success` | `success.php` | register | `kernel/user/module.php:112` |
| `/user/unactivated` | `unactivated.php` | activation | `kernel/user/module.php:119` |
| `/user/hygiene` | `hygiene.php` | hygiene | `kernel/user/module.php:144` |
| `/user/deactivate` | `deactivate.php` | deactivate | `kernel/user/module.php:157` |
| `/user/registration` | `registration.php` | login | `kernel/user/module.php:170` |
| `/user/sessions` | `sessions.php` | login | `kernel/user/module.php:187` |
| `/visual/toolbarlist` | `toolbarlist.php` | – | `kernel/visual/module.php:15` |
| `/visual/toolbar` | `toolbar.php` | – | `kernel/visual/module.php:20` |
| `/visual/menuconfig` | `menuconfig.php` | – | `kernel/visual/module.php:33` |
| `/visual/templatelist` | `templatelist.php` | – | `kernel/visual/module.php:42` |
| `/visual/templateview` | `templateview.php` | – | `kernel/visual/module.php:48` |
| `/visual/templateedit` | `templateedit.php` | – | `kernel/visual/module.php:57` |
| `/visual/templatecreate` | `templatecreate.php` | – | `kernel/visual/module.php:66` |
| `/workflow/view` | `view.php` | – | `kernel/workflow/module.php:13` |
| `/workflow/edit` | `edit.php` | – | `kernel/workflow/module.php:17` |
| `/workflow/groupedit` | `groupedit.php` | – | `kernel/workflow/module.php:22` |
| `/workflow/down` | `edit.php` | – | `kernel/workflow/module.php:27` |
| `/workflow/up` | `edit.php` | – | `kernel/workflow/module.php:31` |
| `/workflow/workflowlist` | `workflowlist.php` | – | `kernel/workflow/module.php:35` |
| `/workflow/grouplist` | `grouplist.php` | – | `kernel/workflow/module.php:39` |
| `/workflow/process` | `process.php` | – | `kernel/workflow/module.php:43` |
| `/workflow/run` | `run.php` | – | `kernel/workflow/module.php:47` |
| `/workflow/event` | `event.php` | – | `kernel/workflow/module.php:51` |
| `/workflow/processlist` | `processlist.php` | – | `kernel/workflow/module.php:55` |

### kernel/private (8)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/oauth/authorize` | `authorize.php` | – | `kernel/private/modules/oauth/module.php:21` |
| `/oauthadmin/list` | `list.php` | – | `kernel/private/modules/oauthadmin/module.php:21` |
| `/oauthadmin/edit` | `edit.php` | – | `kernel/private/modules/oauthadmin/module.php:26` |
| `/oauthadmin/action` | `action.php` | – | `kernel/private/modules/oauthadmin/module.php:37` |
| `/oauthadmin/view` | `view.php` | – | `kernel/private/modules/oauthadmin/module.php:46` |
| `/oauthadmin/keys` | `keys.php` | – | `kernel/private/modules/oauthadmin/module.php:54` |
| `/oauthadmin/keyaction` | `keyaction.php` | – | `kernel/private/modules/oauthadmin/module.php:61` |
| `/switchlanguage/to` | `to.php` | – | `kernel/private/modules/switchlanguage/module.php:16` |
