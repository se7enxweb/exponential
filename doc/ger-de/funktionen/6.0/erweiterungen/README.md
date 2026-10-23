# Extensions, Themes und Pakete

Diese Seite führt jede Legacy-Extension, jedes Theme und jedes Paket von Exponential 6 auf, das eine eigene Seite hat.
Verwenden Sie sie, um herauszufinden, was eine Extension tut, bevor Sie sie aktivieren. Jede Seite sagt, wofür die
Extension da ist, wie Sie sie einrichten, welche Einstellungen sie mit welchen Standards hat und was sich in den
Releases von Exponential 6 geändert hat.

Um eine Extension zu aktivieren, fügen Sie sie `ActiveExtensions[]` in `settings/override/site.ini.append.php` hinzu,
erzeugen die Autoloads neu (`php bin/php/ezpgenerateautoloads.php -e`) und leeren die Caches. Die Seite jeder
Extension nennt jeden zusätzlichen Schritt (Tabellen, Richtlinien, Schlüssel).

## Inhalte und Datentypen

| Extension | Was sie ist |
|---|---|
| [birthday](birthday.md) | Datentyp für Geburtstage |
| [enhancedezbinaryfile](enhancedezbinaryfile.md) | Dateidatentyp für Formulare |
| [enhancedselection2](enhancedselection2.md) | Auswahl-Datentyp, der Bezeichner speichert |
| [exp_enhanced_link](exp_enhanced_link.md) | erweiterter Link-Datentyp |
| [ezgmaplocation](ezgmaplocation.md) | Datentyp für Kartenstandorte |
| [ezstarrating](ezstarrating.md) | Sternebewertungen |
| [eztags](eztags.md) | Tags |
| [hcaptcha](hcaptcha.md) | Datentyp für hCaptcha |
| [ngclasslist](ngclasslist.md) | Datentyp für Klassenlisten |
| [recaptcha](recaptcha.md) | Datentyp für reCAPTCHA |
| [xrowmetadata](xrowmetadata.md) | Metadaten, Open Graph und Sitemaps |

## Bearbeiten und Seitenaufbau

| Extension | Was sie ist |
|---|---|
| [expai](expai.md) | Exponential Intelligence, der Schreibassistent der Administrationsoberfläche (nur Entwürfe) |
| [explayouts-Suite](../../../bc/6.0/layouts.md) | Exponential Layouts: Layouts, Zonen, Blöcke, Regeln und gemeinsame Layouts (die Anleitung) |
| [explayouts_ui](explayouts_ui.md) | die Admin-Bildschirme für Layouts |
| [explayouts_ui_api](explayouts_ui_api.md) | der Layout-Editor |
| [ezautosave](ezautosave.md) | automatisches Speichern von Entwürfen |
| [ezflow](ezflow.md) | Seiten, Zonen und Blöcke |
| [expimageeditor](expimageeditor.md) | Bildeditor von admin4 (Raster und SVG), mit dem Kernel ausgeliefert |
| [ezie](ezie.md) | Bildeditor |
| [ezmultiupload](ezmultiupload.md) | Hochladen mehrerer Dateien |
| [ezodf](ezodf.md) | Import und Export von OpenDocument |
| [ezoe](ezoe.md) | Repository des Online-Editors |
| [ezwt](ezwt.md) | Website-Werkzeugleiste |

## Designs und Themes

| Extension | Was sie ist |
|---|---|
| [ezdemo](ezdemo.md) | Demo-Design |
| [ezwebin](ezwebin.md) | Design Website Interface |
| [ezwebin-ezpackage](ezwebin-ezpackage.md) | Installationspakete von ezwebin |
| [sevenx_themes_admin_classic](sevenx_themes_admin_classic.md) | Umschalter des Admin-Designs |
| [sevenx_themes_simple](sevenx_themes_simple.md) | das Simple-Theme |

## Shop, Newsletter und Besucher

| Extension | Was sie ist |
|---|---|
| [bccie](bccie.md) | Export gesammelter Informationen |
| [bcgooglesitemaps](bcgooglesitemaps.md) | Google-Sitemaps |
| [bcwebsitestatistics](bcwebsitestatistics.md) | Google Analytics |
| [cjw_newsletter](cjw_newsletter.md) | Newsletter |
| [ezpaypal](ezpaypal.md) | PayPal-Gateway |
| [ezpm](ezpm.md) | private Nachrichten |

## Datenaustausch und APIs

| Extension | Was sie ist |
|---|---|
| [ezprestapi](ezprestapi.md) | REST API v1 und v2 |
| [ezprestapiprovider](ezprestapiprovider.md) | veraltete Kompatibilitätsschicht, verwenden Sie ezprestapi |
| [ezxmlexport](ezxmlexport.md) | XML-Export |
| [nxc_powercontent](nxc_powercontent.md) | Inhalte aus Code und REST |
| [syndication](syndication.md) | Syndikation von Inhalten |
| [xrowextract](xrowextract.md) | Export und Import |

## Administration, Sicherheit und Betrieb

| Extension | Was sie ist |
|---|---|
| [AdminAid](AdminAid.md) | Hilfsmittel für Administratoren |
| [ezjscore](ezjscore.md) | JavaScript-Kern und Packer |
| [ezmbpaex](ezmbpaex.md) | Ablauf von Passwörtern |
| [expqueue](expqueue.md) | die Auftragswarteschlange für Hintergrundarbeit von Extensions |
| [ezupdate](ezupdate.md) | Aktualisierungen und Pakete |
| [git_manager](git_manager.md) | Git-Dashboard und Sicherungen |
| [sevenx_authentication_2fa](sevenx_authentication_2fa.md) | Zwei-Faktor- und Social-Login |
| [sevenx_dse](sevenx_dse.md) | Database Source Editor |
| [sevenx_valkey_cache](sevenx_valkey_cache.md) | Cache in Redis und Valkey |

## Template-Helfer

| Extension | Was sie ist |
|---|---|
| [owsimpleoperator](owsimpleoperator.md) | einfache Template-Operatoren |
| [swark](swark.md) | Template-Operatoren und Workflow-Ereignisse |

## Plattformpakete

| Paket | Was es ist |
|---|---|
| [cjw-exponential-media-site-data](cjw-exponential-media-site-data.md) | Daten der Media-Site |
| [ez-support-tools](ez-support-tools.md) | Plattform-Bundle (Chronik im Abschnitt Ökosystem) |
| [ngsymfonytools](ngsymfonytools.md) | Plattform-Bundle (Chronik im Abschnitt Ökosystem) |
| [sevenx-recipes](sevenx-recipes.md) | Symfony-Flex-Rezepte |

## Verwandte Seiten

- [Was eine neue Installation mitbringt: die Standard-Auslieferung der Extensions](../standard-extension-auslieferung.md)
- [Extension-Liste: jede Extension sortieren, untersuchen und herunterladen](../extension-liste-und-downloads.md)
- [Setup > Extensions: Ladereihenfolge und sicheres Speichern](../ladereihenfolge-der-extensions.md)
- [Extension-Metadaten: `ezinfo.php` und `extension.xml`](../../../spezifikationen/6.0/extension-metadaten.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Chronik: [Januar 2024, erste Hälfte](../../../geschichte/2024/2024-01a.md), [Januar 2024, zweite Hälfte](../../../geschichte/2024/2024-01b.md), [Februar 2024](../../../geschichte/2024/2024-02.md), [März 2024](../../../geschichte/2024/2024-03.md), [Juni 2024](../../../geschichte/2024/2024-06.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
