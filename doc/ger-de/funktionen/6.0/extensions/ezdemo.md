# ezdemo: das Demo-Design

Diese Seite ist für alle, die eine vollständige Exponential-Website sehen oder Templates daraus kopieren möchten.
`ezdemo` („Exponential Demo Design LS“) ist die Demo-Website von Exponential: ein Design, Einstellungen und
Installationspakete, die zeigen, was [ezwebin](../../../../eng-us/features/6.0/extensions/ezwebin.md), [ezflow](../../../../eng-us/features/6.0/extensions/ezflow.md), [ezwt](ezwt.md) und [ezodf](ezodf.md)
zusammen leisten. Sie hat Galerien, einen Editor für Startseite und Landingpages, Flyouts, einen Kampagnenblock,
RSS-Seiten und einen Shop. Version 6.0.9 (2. Oktober 2026) ist aktuell.

Sie benötigt `ezjscore` und erweitert `ezflow`, `ezwt` und `ezodf`.

## Verwenden

1. Aktivieren Sie die Extension als Design-Extension: `design.ini [ExtensionSettings] DesignExtensions[]=ezdemo` (die
   Extension bringt diese Zeile mit).
2. Verwenden Sie das Design `ezdemo` (`design/ezdemo`; die Extension enthält außerdem einen Ordner `design/admin`).
3. Um ein Template wiederzuverwenden, kopieren Sie es aus `design/ezdemo/templates` in Ihre eigene Design-Extension.
   Bearbeiten Sie die Demo nicht an Ort und Stelle.

Die Skripte, die das Design auf öffentlichen Seiten lädt, sind die `[JavaScriptSettings] FrontendJavaScriptList[]`
seiner `design.ini`: `ezjsc::jquery`, `init_ua.js`, `handle_transition.js`, `toggle_class.js` und `eztransition.js`.
Die Skripte für Galerie, Flyout und Ajax-Suche (`ezgallery.js`, `ezgallerynavigator.js`, `ezflyout.js`,
`ezajaxsearch.js`) liegen in `design/ezdemo/javascript/`.

## Was sich in den Releases von Exponential 6 geändert hat

| Release | Änderung |
|---|---|
| 6.0.1 | Massenaktualisierung aus dem übergeordneten Paket-Repository `ezdemo-ezpackage`. |
| 6.0.2, 6.0.3 | Die Extension nennt sich Exponential Demo Design LS; sichtbare Texte nennen Exponential; Templates schreiben `<br>` wie in HTML5; `ezinfo.php` und die Lizenz vollständig. |
| 6.0.4 | Die Seite **Passwort vergessen** verrät nicht, ob zu einer Adresse ein Konto gehört, und maskiert, was sie ausgibt ([Einzelheiten](../../../../eng-us/bc/6.0/extensions-behaviour-changes.md#2-forgot-password-pages-no-longer-tell-whether-an-address-has-an-account)). |
| 6.0.5 | **Fehlerseiten** zeigen ihren eigenen Fehler im Seitentitel ([Einzelheiten](../../../../eng-us/bc/6.0/extensions-behaviour-changes.md#3-error-pages-show-their-own-error-in-the-page-title)). |
| 6.0.6 | Deutsch deckt jeden Oberflächentext der Templates ab: die Seiten für RSS-Export, -Import und -Liste, Kopfzeilen-Links und Suchfeld, Fußzeile, Schaltflächen des Inhaltseditors, Produkt-, Datei-, Video- und Blogansichten und den Startseiten-Editor. |
| 6.0.7 | Die aufklappbaren Attributgruppen der Bearbeitungsseite verwenden jQuery 4. |
| 6.0.8 | **YUI entfernt.** Siehe unten. |
| 6.0.9 | Die Copyright-Hinweise nennen zuerst „1998 - 2026 7x & Exponential Foundation“. |

Zu 6.0.8: Die Galerien (`ezgallery`, `ezgallerynavigator`, `ezsimplegallery`) und `ezflyout` sind jQuery-Module
(`$.eZ.*`) mit denselben Optionen und demselben Markup. Übergänge sind CSS (`eztransition.js`); `init_ua.js`,
`toggle_class.js` und `handle_transition.js` verwenden das DOM. `design.ini` lädt `ezjsc::jquery` statt `ezjsc::yui3`;
der Landingpage-Editor lädt jQuery; ein `ezajaxsearch.js` für jQuery wird mit dem Design ausgeliefert; die Stile der
ezpage-Reiter verwenden die Klassen `.ezpage-tabs*` von ezflow 6.1.4.

**Hinweis zur Aktualisierung:** Wenn Sie das Galerie- oder Flyout-Markup der Demo in Ihr eigenes Design kopiert haben,
ändert sich für Ihr Markup nichts: Optionen und Markup sind dieselben, nur das Skript dahinter ist jQuery. Siehe
[Entfernung von YUI](../../../../eng-us/bc/6.0/yui-removal.md).

## Verwandte Seiten

- [ezwebin](../../../../eng-us/features/6.0/extensions/ezwebin.md), [ezflow](../../../../eng-us/features/6.0/extensions/ezflow.md)
- [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Chronik](../../../../eng-us/history/extensions/ezdemo.md) und [Versionshinweise](../../../../changelogs/extensions/ezdemo.md)
- [Änderungsverzeichnis](../../../../eng-us/history/ledger/ezdemo.md)
- Monate: [2024-01](../../../../eng-us/history/extensions/months/2024-01.md), [2026-09](../../../../eng-us/history/extensions/months/2026-09.md), [2026-10](../../../../eng-us/history/extensions/months/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
