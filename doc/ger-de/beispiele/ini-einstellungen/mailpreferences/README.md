---
title: mailpreferences.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von mailpreferences.ini.
keywords: Beispiele, Referenz, mailpreferences.ini, examples, reference
type: generated
generated: exp:help:examples
---
# mailpreferences.ini

Die ausgelieferten Einstellungen von `settings/mailpreferences.ini`: 19 Blöcke, 95 Einstellungen.

> Exponential-Konfigurationsdatei für das System der E-Mail-Einstellungen und Einwilligungen.  
>
> Jede Person kann E-Mail pro Kategorie auf einer Seite ein- und ausschalten  
> (mailpreferences/settings, mailpreferences/manage/<token> ohne Anmeldung).  
> Das Mail-Gate in eZMailTransport::send() prüft jede Mail, die eine  
> Kategorie angibt (eZMail::setCategory() oder der Header X-Exp-Mail-Category), gegen  
> den Hauptschalter, die Kategorie, die Sperrliste und ein ausstehendes Double-  
> Opt-in; optionale Mail erhält eine Fußzeile, einen Abmeldelink mit einem Klick und die  
> Header List-Unsubscribe und List-Unsubscribe-Post nach RFC 8058.  
>
> HINWEIS: Es wird nicht empfohlen, diese Datei direkt zu bearbeiten; legen Sie stattdessen eine Datei in  
>       override für die Werte an, die die Website braucht:  
>       settings/override/mailpreferences.ini.append.php  
>
> Das Website-Geheimnis für die Links und die Hashes der Adressen (\[SecretSettings\]) wird  
> bei der ersten Verwendung in settings/override/mailpreferences.ini.append.php erzeugt.  
> Diese Datei wird nie committet.  

| Block | Einstellungen |
|---|---|
| [GateSettings](GateSettings.md) | 5 |
| [CategorySettings](CategorySettings.md) | 2 |
| [Category_content](Category_content.md) | 7 |
| [Category_collaboration](Category_collaboration.md) | 7 |
| [Category_newsletter](Category_newsletter.md) | 7 |
| [Category_marketing](Category_marketing.md) | 7 |
| [Category_system](Category_system.md) | 7 |
| [Category_security](Category_security.md) | 6 |
| [Category_orders](Category_orders.md) | 6 |
| [Category_legal](Category_legal.md) | 6 |
| [Category_admin](Category_admin.md) | 6 |
| [FooterSettings](FooterSettings.md) | 4 |
| [LinkSettings](LinkSettings.md) | 1 |
| [TokenSettings](TokenSettings.md) | 3 |
| [ConsentSettings](ConsentSettings.md) | 2 |
| [SuppressionSettings](SuppressionSettings.md) | 3 |
| [EmailChangeSettings](EmailChangeSettings.md) | 2 |
| [BounceSettings](BounceSettings.md) | 13 |
| [SecretSettings](SecretSettings.md) | 1 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/mailpreferences.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
