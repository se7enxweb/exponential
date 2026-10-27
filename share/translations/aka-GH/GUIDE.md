# Akan (aka-GH): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Akan as written in Ghana in its most widely read form, **Asante Twi**, in the Akan orthography of the Bureau of Ghana
Languages, in the neutral, polite register of Akan radio and television news, the Twi localisations of Google and
Microsoft and Twi Wikipedia (tw). No other locale falls back to `aka-GH`.

- Word order is subject - verb - object. Function words: ne (and, with), anaa (or), nanso (but), sɛ (if, that), yi
  (this), no (the, that), nyinaa (all), mu (in), so (on), fi (from), ma (for), nni (there is not), wɔ hɔ (there is),
  -n- / ɛn- (negation: ɛnyɛ, wonntumi).
- Technical words: an Akan word or phrase where one is in use (ɔdefoɔ, ahintasɛm, nhwehwɛmu), an
  English loan in Twi spelling where Twi speakers use it (fael, kɔmputa, sɛva, data). Never leave the English word
  unless it is a product name or code. Where Twi has no settled term, a short descriptive phrase is better than an
  English word.
- Short, plain sentences.

## 2. Form of address

- The user is **wo** (you), polite by word choice ("Mesrɛ wo" for please).
- **Buttons, menu items and links**: the short imperative, as Twi interfaces do: Sie (save), Gyae (cancel), Popa
  (delete), Yi fi mu (remove), Sesa (edit), Fa ka ho (add), Yɛ foforɔ (create), Hwehwɛ (search), Paw (select), Soma
  (send), Hwɛ (view), Tintim (publish), Fa to so (upload), Twe gu so (download).
- **Titles, column headings and tabs**: nouns or noun phrases: Nhyehyɛeɛ, Nkyerɛaseɛ, Baabi titiriw, Din.
- **Instructions**: the imperative, "Mesrɛ wo" where the source has please: "Paw adeɛ no na mia \"OK\" so.".
- **Confirmations and questions**: "Wo ani gye sɛ wobɛ ... anaa?": "Wo ani gye sɛ wopɛ sɛ wopopa ɔfa yi anaa?".
- **What the user can or may do**: "Wobɛtumi ...", "Worentumi ..."; "You do not have permission" is "Wonni ho
  kwan sɛ ...".
- **Messages about a result**: the completive: "Wɔasie draft no.", "Fael no akɔ so yiye.".

## 3. Script and typography

- **Latin** alphabet with the Akan letters **ɛ Ɛ** (U+025B/U+0190) and **ɔ Ɔ** (U+0254/U+0186); no tone marks.
  Never write e or o for them, and never the Greek ε or the digit-like look-alikes.
- **Capitals**: only the first word of a button, title or sentence and proper names.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Nothing is glued to a placeholder. The same for code, file names, INI
  settings and product names.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Punctuation**: as in the source.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Akan (Twi) | Note |
|---|---|---|
| content | emu nsɛm | content object: emu nsɛm adeɛ |
| object | adeɛ | plural: nneɛma |
| node | nkɔmmɔbea | main node: nkɔmmɔbea titiriw; parent node: nkɔmmɔbea agya |
| location | beaeɛ | main location: beaeɛ titiriw |
| item (list entry) | adeɛ | |
| tree | dua | tree menu: dua menu; content structure tree: emu nsɛm dua |
| class | kuo | the content class: nneɛma kuo; class group: akuo kuo |
| attribute | su | plural: nsu |
| datatype | data su | |
| version | nsakraeɛ | version history: nsakraeɛ abakɔsɛm |
| draft | nsɛm a ɛnwieeɛ | draft (short): draft; store draft: Sie draft no |
| publish | tintim | published: wɔatintim; publishing: ntintimiɛ |
| send for publishing | soma kɔ ntintimiɛ | |
| discard | to gu | discard draft: To draft no gu |
| archived / pending / rejected | wɔakora / ɛretwɛn / wɔapo | |
| siteaccess | wɛbsaet kwan | siteaccess as a setting name stays in code |
| site, website | wɛbsaet | |
| section | ɔfa | the Exponential section; an INI section: INI ɔfa |
| role | dwumadie | |
| policy | nhyehyɛeɛ mmara | |
| limitation | anohyetoɔ | |
| permission | kwan | access denied: wɔasi kwan |
| module | modul | |
| function | dwumadie | a PHP function: PHP function |
| user / user group | ɔdefoɔ / adefoɔ kuo | plural: adefoɔ |
| group | kuo | |
| anonymous | a wonnim no | anonymous user: ɔdefoɔ a wonnim no |
| administrator | ɔhwɛfoɔ | administration (interface): ahwɛsoɔ |
| username | ɔdefoɔ din | login (the name): din a wode bɛhyɛne |
| password | ahintasɛm | |
| log in / log out | hyɛ mu / fi mu | the act: kɔ mu / fi adi |
| e-mail | email | e-mail address: email address -> email adrɛse |
| cache | ntɛmkora | clear cache: Popa ntɛmkora |
| template | nhwɛsoɔ | override template: nhwɛsoɔ a ɛsi anan |
| override | si anan | |
| fallback | mmoa kwan | fallback language: kasa a ɛboa |
| design | nhyehyɛeɛ su | |
| workflow | adwuma kwan | |
| trigger | ɔkanyanfoɔ | |
| event | asɛm a esiiɛ | |
| trash | nwura kotoku | empty trash: Pepa nwura kotoku; move to trash: Fa kɔ nwura kotoku mu |
| bookmark | nsɛnkyerɛnne | |
| settings | nhyehyɛeɛ | configuration: nhyehyɛeɛ |
| setup | ahosiesie | the Setup tab |
| layout | krataafa nhyehyɛeɛ | Exponential Layouts stays |
| block / zone | block / mpɔtam | |
| extension | ntrɛmu | |
| package | boa | |
| search | nhwehwɛmu | to search: hwehwɛ; search index: nhwehwɛmu index |
| log | kyerɛwtohɔ | a log file: kyerɛwtohɔ fael; never "log in" |
| queue | santen | job queue: nnwuma santen |
| job / task | adwuma | |
| cron | cron | |
| script | script | |
| kernel, core | mfitiaseɛ | |
| server | sɛva | |
| database | data korabea | |
| folder / directory | fɔlda | |
| file | fael | plural: fael ahodoɔ |
| image | mfonini | thumbnail: mfonini ketewa |
| preview | hwɛ kane | |
| upload / download | fa to so / twe gu so | |
| export / import | yi kɔ abɔnten / fa ba mu | |
| remove | yi fi mu | take out of a list or place |
| delete | popa | destroy; deleted: wɔapopa |
| edit | sesa | |
| editor | ɔsesafoɔ / sesa adwinnadeɛ | person / software |
| save / store | sie | the verb "Store" of the forms |
| Store (shop tab) | sotɔɔ | the shop, as "Shop" |
| cancel | gyae | |
| OK | Yoo | |
| apply | de yɛ adwuma | |
| back | san kɔ | |
| next / previous | deɛ ɛdi so / deɛ atwam | |
| enable / disable | bue / to mu | enabled / disabled: ɛreyɛ adwuma / ɛnyɛ adwuma |
| active / inactive | ɛreyɛ adwuma / ɛnyɛ adwuma | |
| run | ma ɛnkɔ so | |
| start / stop | fi ase / gyae | |
| session | bere a wowɔ mu | session (short): sɛhyen |
| hidden / visible | ahintaw / ɛda adi | hide / reveal: de hinta / da no adi |
| archive | korabea | |
| order (sort) | nhyehyɛeɛ | sort order: nhyehyɛeɛ kwan |
| order (shop) | adetɔ ahyɛdeɛ | short: ahyɛdeɛ |
| shop | sotɔɔ | |
| basket | kɛntɛn | |
| product | adetɔnneɛ | |
| customer | adetɔfoɔ | |
| discount | ka so tew | |
| VAT | VAT | allowlisted where it stands alone |
| price / currency | boɔ / sika | |
| tag | nsɛnkyerɛnne | |
| translation | nkyerɛaseɛ | |
| language | kasa | main language: kasa titiriw |
| locale | kasa ne ɔman | |
| state | tebea | the object state |
| status | gyinabea | |
| view (button) | hwɛ | a module view (noun): nhwɛeɛ |
| format | nhyehyɛeɛ su | |
| sort | hyehyɛ | |
| filter | twa so | |
| select | paw | select all: Paw ne nyinaa; selected: wɔapaw |
| copy / move | yɛ bi / tu kɔ | |
| add / create / new | fa ka ho / yɛ / foforɔ | |
| restore | san de ba | |
| install / uninstall | de hyɛ mu / yi fi mu | |
| related objects | nneɛma a ɛfa ho | relation: abusuabɔ |
| dashboard | adwumayɛbea | |
| identifier / ID | nsɛnkyerɛnne / ID | |
| priority | deɛ edi kan | |
| owner | wura | |
| modified / created | wɔasesa / wɔayɛ | |
| date / time | da / bere | |
| error / warning | mfomsoɔ / kɔkɔbɔ | |
| help | mmoa | |
| notification | amanneɛbɔ | |
| account | akawnt | |
| default | deɛ ɛwɔ hɔ dada | |
| link | link | |
| none / all | biribiara nni hɔ / ne nyinaa | |
| yes / no | aane / daabi | |
| name / title / description | din / asɛmti / nkyerɛkyerɛmu | |
| value / type / list | bo / su / din krataa | |
| page / path | krataafa / kwan | |
| required / optional | ɛhia / wopɛ a | |
| options | nneɛma a wobɛpaw | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is sotɔɔ, the word the shop menu uses for "Shop"; the verb "Store" of the forms is
  sie.
- "Login" in the user, session and audit lists is the user's login name; the Login button of the error page is the act
  (Hyɛ mu).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID, VAT ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
