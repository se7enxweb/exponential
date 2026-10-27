# Azerbaijani (aze-AZ): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard Azerbaijani (Azərbaycan dili) as written in the Republic of Azerbaijan, in the **Latin alphabet** of 1991,
in the neutral, polite register of Azerbaijani government portals, software localisations (Windows, Android, Firefox
az) and Azerbaijani Wikipedia. No other locale falls back to `aze-AZ`.

- Word order is subject - object - verb; the verb comes last. Suffixes follow vowel harmony (-lar/-lər,
  -da/-də, -dan/-dən, -ı/-i/-u/-ü).
- Technical words: the Azerbaijani word where one is established (fayl, istifadəçi, şifrə, parametrlər, axtarış,
  verilənlər bazası); an international loan in Azerbaijani spelling where Azerbaijani IT writing uses it (server,
  skript, modul, keş). Turkish (tur-TR) is a close relative and may help with vocabulary, but write Azerbaijani
  forms and spelling (ə, x, q), never Turkish ones (dosya -> fayl, kullanıcı -> istifadəçi, şifre -> şifrə,
  ayarlar -> parametrlər, sil -> sil, kaydet -> yadda saxla).
- Short, plain sentences.

## 2. Form of address

- The user is **siz** (polite plural) with the matching verb forms (edə bilərsiniz, istəyirsinizmi).
- **Buttons, menu items and links**: the short singular imperative, as Azerbaijani interfaces do: Yadda saxla (save),
  Ləğv et (cancel), Sil (delete), Çıxar (remove), Redaktə et (edit), Əlavə et (add), Yarat (create), Axtar (search),
  Seç (select), Göndər (send), Bax (view), Dərc et (publish), Yüklə (upload), Endir (download).
- **Titles, column headings and tabs**: nouns or noun phrases: Parametrlər, Tərcümələr, Əsas yer, Ad.
- **Instructions**: the polite imperative in -in/-ın/-un/-ün: "Elementi seçin və \"OK\" düyməsini klikləyin."
  "Please" is "Zəhmət olmasa" only where the source has it.
- **Confirmations and questions**: "... istədiyinizə əminsinizmi?": "Bu bölməni silmək istədiyinizə əminsinizmi?".
- **What the user can or may do**: "... edə bilərsiniz", "... edə bilməzsiniz"; "You do not have permission" is
  "... icazəniz yoxdur".
- **Messages about a result**: the passive past: "Qaralama yadda saxlanıldı.", "Fayl uğurla yükləndi.".

## 3. Script and typography

- **Latin** alphabet with ə, ç, ğ, ı, ö, ş, ü, x, q. Capital of i is İ, of ı is I (İstifadəçi, Iş -> İş).
- **Capitals**: only the first word of a button, title or sentence and proper names (Sentence case).
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free. A case suffix is
  never written onto a placeholder (%namedə is a different placeholder): use a noun that carries the suffix
  ("%name faylında", "%count element") or put the placeholder in quotation marks with the suffix after an apostrophe
  outside them ("%name"-də). The same applies to code, file names, INI settings and product names: Exponential-da.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Punctuation**: as in the source.
- **Numbers**: digits 0-9; a noun after a number stays singular: "%count element", "5 fayl".

## 4. Glossary

Binding for the kernel and every extension.

| English | Azerbaijani | Note |
|---|---|---|
| content | məzmun | content object: məzmun obyekti |
| object | obyekt | plural: obyektlər |
| node | düyün | main node: əsas düyün; parent node: ana düyün |
| location | yer | main location: əsas yer |
| item (list entry) | element | |
| class | sinif | the content class; class group: sinif qrupu |
| attribute | atribut | |
| datatype | verilən tipi | |
| version | versiya | version history: versiya tarixçəsi |
| draft | qaralama | store draft: Qaralamanı yadda saxla |
| publish | dərc et | published: dərc edilib; publishing: dərc |
| send for publishing | dərc üçün göndər | |
| discard | ləğv et | discard draft: Qaralamanı at |
| archived / pending / rejected | arxivləşdirilib / gözləmədə / rədd edilib | |
| siteaccess | sayt girişi | siteaccess as a setting name stays in code |
| site, website | sayt | |
| section | bölmə | the Exponential section; an INI section: INI bölməsi |
| role | rol | |
| policy | siyasət | one rule of a role |
| limitation | məhdudiyyət | |
| permission | icazə | access denied: giriş qadağandır |
| module | modul | |
| function | funksiya | |
| user / user group | istifadəçi / istifadəçi qrupu | |
| group | qrup | |
| anonymous | anonim | anonymous user: anonim istifadəçi |
| administrator | administrator | administration (interface): idarəetmə |
| username | istifadəçi adı | login (the name): giriş adı |
| password | şifrə | |
| log in / log out | daxil ol / çıxış et | the act: giriş / çıxış |
| e-mail | e-poçt | e-mail address: e-poçt ünvanı |
| cache | keş | clear cache: Keşi təmizlə |
| template | şablon | override template: əvəzedici şablon |
| override | əvəzetmə | |
| fallback | ehtiyat | fallback language: ehtiyat dil |
| design | dizayn | |
| workflow | iş axını | |
| trigger | tətikləyici | |
| event | hadisə | |
| trash | zibil qutusu | empty trash: Zibil qutusunu boşalt; move to trash: Zibil qutusuna at |
| bookmark | əlfəcin | |
| settings | parametrlər | configuration: konfiqurasiya |
| setup | quraşdırma | the Setup tab |
| layout | səhifə düzümü | Exponential Layouts stays |
| block / zone | blok / zona | |
| extension | genişlənmə | |
| package | paket | |
| search | axtarış | to search: axtar; search index: axtarış indeksi |
| log | jurnal | a log file: jurnal faylı; never "log in" |
| queue | növbə | job queue: tapşırıq növbəsi |
| job / task | tapşırıq | |
| cron | cron | |
| script | skript | |
| kernel, core | nüvə | |
| server | server | |
| database | verilənlər bazası | |
| folder / directory | qovluq | |
| file | fayl | |
| image | şəkil | thumbnail: kiçik şəkil |
| preview | önbaxış | |
| upload / download | yüklə / endir | |
| export / import | ixrac et / idxal et | |
| remove | çıxar | take out of a list or place |
| delete | sil | destroy; deleted: silindi |
| edit | redaktə et | |
| editor | redaktor | |
| save / store | yadda saxla | the verb "Store" of the forms |
| Store (shop tab) | mağaza | the shop, as "Shop" |
| cancel | ləğv et | |
| OK | OK | allowlisted |
| apply | tətbiq et | |
| back | geri | |
| next / previous | növbəti / əvvəlki | |
| enable / disable | aktivləşdir / deaktivləşdir | enabled / disabled: aktivdir / deaktivdir |
| active / inactive | aktiv / qeyri-aktiv | |
| run | işə sal | |
| start / stop | başlat / dayandır | |
| session | sessiya | |
| hidden / visible | gizli / görünən | hide / reveal: gizlət / göstər |
| archive | arxiv | |
| order (sort) | sıralama | sort order: sıralama qaydası |
| order (shop) | sifariş | |
| shop | mağaza | |
| basket | səbət | |
| product | məhsul | |
| customer | müştəri | |
| discount | endirim | |
| VAT | ƏDV | |
| price / currency | qiymət / valyuta | |
| tag | teq | an HTML tag: HTML teqi |
| translation | tərcümə | |
| language | dil | main language: əsas dil |
| locale | lokal | |
| state | vəziyyət | the object state |
| status | status | kept apart from vəziyyət |
| view (button) | bax | a module view (noun): görünüş |
| format | format | |
| sort | sırala | |
| filter | filtr | to filter: filtrlə |
| select | seç | select all: Hamısını seç; selected: seçilmiş |
| copy / move | kopyala / köçür | |
| add / create / new | əlavə et / yarat / yeni | |
| restore | bərpa et | |
| install / uninstall | quraşdır / sil | uninstall software: quraşdırmanı sil |
| related objects | əlaqəli obyektlər | relation: əlaqə |
| dashboard | idarə paneli | |
| identifier / ID | identifikator / ID | |
| priority | prioritet | |
| owner | sahib | |
| modified / created | dəyişdirilib / yaradılıb | |
| date / time | tarix / vaxt | |
| error / warning | xəta / xəbərdarlıq | |
| help | kömək | |
| notification | bildiriş | |
| account | hesab | |
| default | standart | |
| link | keçid | |
| none / all | heç biri / hamısı | |
| yes / no | bəli / xeyr | |
| name / title / description | ad / başlıq / təsvir | |
| value / type / list | dəyər / tip / siyahı | |
| page / path | səhifə / yol | |
| required / optional | məcburi / istəyə bağlı | |
| options | seçimlər | |
| alias (URL alias) | ləqəb | URL alias: URL ləqəbi |
| wildcard | joker simvol | URL wildcard: URL joker simvolu |
| collection (information collector) | kolleksiya | information collector: məlumat toplayıcı |
| sub items | alt elementlər | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is mağaza, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is yadda saxla.
- "Login" in the user, session and audit lists is the user's login name (giriş adı); the Login button of the error
  page is the act (Daxil ol).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID, OK ...) are listed with
  their reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
