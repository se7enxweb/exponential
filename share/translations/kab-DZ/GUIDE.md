# Kabyle (kab-DZ): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Kabyle (Taqbaylit) of Algeria, in the neutral, plain written register of the Kabyle localisations of Mozilla,
LibreOffice and Wikipedia, and of the Berber press. No other locale falls back to `kab-DZ`. Kabyle words and the
Kabyle computing terms of those localisations are used; French (`fre-FR`) gives a loan word only where Kabyle has none,
as everyday Kabyle does.

- Kabyle words are used where they exist: sekles, sefsex, kkes, ssuffeɣ, ẓreg, nadi, fren, rnu, snulfu-d, wali,
  aseqdac, awal uffir, tugna, afaylu, akaram, iḍumman.
- A loan word without a Kabyle one is written in Kabyle spelling, with a Kabyle article where the sentence needs it:
  amutur, asekript. Never a fresh French word in French spelling inside a Kabyle sentence.
- Nouns have a free state and an annexed state: "afaylu" -> "n ufaylu", "aseqdac" -> "n useqdac", "tugna" -> "n
  tugna". Plurals as in the glossary: ifuyla, iseqdacen, tiɣawsiwin.

## 2. Form of address

The interface addresses the user in the second person singular, masculine as Kabyle software does (**-k**, **ik**):
"awal-ik uffir" (your password).

- **Buttons, menu items and links**: the imperative singular: Sekles, Sefsex, Kkes, Ẓreg, Siẓreg, Azen i usiẓreg,
  Ssuffeɣ ayen yettwafernen, Azen ɣer iḍumman.
- **Titles, column headings and tabs**: nouns or noun phrases: Iɣewwaren, Tisuqilin, Adeg agejdan.
- **Instructions**: the imperative, softened with "Ma ulac aɣilif," where a request needs it: "Fren taɣawsa syin
  sit ɣef «IH».", "Ma ulac aɣilif, rǧu ...". A reference ("see ...") is "Wali ...".
- **Confirmations and questions**: "Tebɣiḍ s tidet ad tekkseḍ asmil-a?", "Tebɣiḍ ad ... ?".
- **What the user can or may do**: "Tzemreḍ ad ...", "Ur tezmireḍ ara ad ...", or impersonal: "Ur yezmir ara ad
  yettwabeddel ...". "You do not have permission" is "Ur tesɛiḍ ara tasiregt ...".
- Not allowed: slash forms.

## 3. Script and typography

- **Latin**, the Berber Latin alphabet of Kabyle (Mammeri tradition, INALCO conventions), the script of the shipped
  locale (`share/locale/kab-DZ.ini`, LanguageName Taqbaylit). The special letters are written with their own code
  points: č, ḍ, ǧ, ḥ, ɣ (U+0263), ṛ, ṣ, ṭ, ẓ, ɛ (U+025B), capitals Č, Ḍ, Ǧ, Ḥ, Ɣ, Ṛ, Ṣ, Ṭ, Ẓ, Ɛ; the
  precomposed letters (ḍ U+1E0D, ḥ U+1E25, ṛ U+1E5B, ṣ U+1E63, ṭ U+1E6D, ẓ U+1E93). The Tifinagh and Arabic scripts are
  not used here. A hyphen joins the affixed pronouns: awal-ik, sekles-it, ad t-tekkseḍ.
- **Latin text inside Kabyle**: product names, codes and acronyms stay as they are: "afaylu PHP", "URL", "tangalt
  HTML".
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Never a letter glued to a placeholder: "%count n tɣawsiwin" or
  "Tiɣawsiwin: %count", not "%countn" or "%name-ik".
- **Numbers**: the number comes before the noun, joined by "n": "%count n yifuyla". Digits 0-9.
- **Quotation marks**: the guillemets «...» where the source has its own quotation marks in text; where the source
  has `&quot;` or a straight " that is code or an entity, keep it exactly.
- **Punctuation**: as in the source, without a space before a colon, question or exclamation mark. A label keeps
  its colon.

## 4. Glossary

Binding for the kernel and every extension.

| English | Kabyle | Note |
|---|---|---|
| content | agbur | |
| content object | taɣawsa n ugbur | |
| object | taɣawsa | plural tiɣawsiwin |
| node | takerrist | main node: takerrist tagejdant; parent node: takerrist tamaraẓt; child: takerrist tarwiwt |
| location | adeg | main location: adeg agejdan |
| item (list entry) | aferdis | |
| class | asmil | the content class; class group: agraw n yismilen; a PHP class: asmil PHP |
| content class | asmil n ugbur | |
| attribute | ayla | |
| datatype | anaw n yisefka | |
| version | lqem | version history: amazray n yileqman |
| draft | arewway | store draft: Sekles arewway |
| publish | siẓreg | published: yettwasiẓreg |
| send for publishing | Azen i usiẓreg | |
| discard | ttu | discard draft: Ttu arewway |
| archived | yettwaḥrez | |
| pending | yettraǧu | |
| rejected | yettwagi | |
| siteaccess | anekcum ɣer usmel | the configuration unit |
| site, website | asmel, asmel web | |
| section | tigezmi | the Exponential section |
| role | tamlilt | |
| policy | tasertit | |
| limitation | talast | |
| permission | tasiregt | access rights: tisirag n unekcum |
| module | amudil | |
| function | tawuri | |
| user | aseqdac | plural iseqdacen |
| user group | agraw n iseqdacen | |
| group | agraw | |
| anonymous | udrig | anonymous user: aseqdac udrig |
| administrator | anedbal | |
| administration (interface) | tadbelt | |
| username | isem n useqdac | the column "Login" too |
| password | awal uffir | |
| log in / sign in | kcem | button: Kcem |
| log out / sign out | ffeɣ | button: Ffeɣ |
| e-mail | imayl | e-mail address: tansa imayl |
| cache | tazarkatut | clear cache: Sfeḍ tazarkatut |
| template | tamudemt | |
| override | amsenfel | override template: tamudemt n umsenfel |
| fallback | tuɣalin | fallback language: tutlayt n tuɣalin |
| design | aɣanib | |
| workflow | aɣawas n umahil | |
| trigger | amsebdu | |
| event | tadyant | |
| trash | iḍumman | empty trash: Sfeḍ iḍumman |
| bookmark | tacreḍt | |
| settings | iɣewwaren | |
| setup | asbadu | the Setup tab; installation: asebded |
| layout | asiskel | Exponential Layouts |
| block | iḥder | |
| zone | tamnaḍt | |
| extension | asiɣzef | |
| package | akemmus | |
| search | nadi | button: Nadi |
| log | aɣmis | log file: afaylu n uɣmis |
| queue | abrid n uṛaǧu | |
| job / task | amahil | |
| cron | cron | cron job: amahil cron |
| script | asekript | |
| worker | amahal | kept apart from aseqdac (user) |
| kernel, core | aẓar | |
| server | aqeddac | |
| database | taffa n yisefka | |
| folder | akaram | plural ikaramen |
| file | afaylu | plural ifuyla |
| image | tugna | |
| preview | taskant | |
| upload | sali | button: Sali |
| download | sider | button: Sider |
| export / import | sifeḍ / kter | |
| remove | ssuffeɣ | take out of a list or a place |
| delete | kkes | destroy |
| edit | ẓreg | |
| editor | amaẓrag | |
| save / store | sekles | |
| Store (shop tab) | Tahanut | the word the shop menu uses for "Shop" |
| cancel | sefsex | |
| OK | IH | |
| apply | snes | |
| back | uɣal | |
| next / previous | uḍfiṛ / uzwir | |
| enable / disable | rmed / sens | a setting or feature |
| enabled / disabled | yermed / yensa | |
| active / inactive | urmid / arurmid | |
| run | sedu | a script or job |
| start / stop | bdu / seḥbes | |
| session | tiɣimit | |
| hidden / visible | yeffer / yettban | hide / reveal: ffer / sken |
| archive | aḥraz | a file archive (.zip): aḥraz n yifuyla |
| order (sort) | tanila n usmizzwer | sort: smizzwer |
| order (shop) | taladna | |
| shop | tahanut | |
| basket | taqecwalt | |
| product | afaris | |
| customer | amsaɣ | |
| discount | asenqes | |
| VAT | TVA | the abbreviation used in Algeria |
| price | ssuma | |
| currency | tadrimt | |
| tag | tabzimt | keyword tag; an HTML tag: tabzimt HTML |
| translation | tasuqilt | |
| language | tutlayt | main language: tutlayt tagejdant |
| locale | tutlayt tadigant | |
| state | addad | the object state; state group: agraw n waddaden |
| status | liḥala | |
| view (button) | wali | a module view: timeẓri |
| format | amasal | |
| sort | smizzwer | |
| filter | imsizdeg | |
| select | fren | selected: yettwafren |
| browse | inig | |
| copy | nɣel | |
| move | smutti | |
| add | rnu | |
| create | snulfu-d | |
| new | amaynut | |
| restore | err-d | |
| install / uninstall | sebded / kkes asebded | |
| sub items | iferdisen n ddaw | |
| subtree | aseklu n ddaw | |
| related objects | tiɣawsiwin icudden | relation: assaɣ |
| URL alias | isem-nniḍen n URL | |
| redirect | awelleh | |
| dashboard | tafelwit n tadbelt | |
| content structure | taɣessa n ugbur | |
| media library | tamkarḍit n yimidyaten | |
| identifier / ID | asulay / ID | |
| priority | tazwart | |
| owner | bab | |
| modified / created | yettwabeddel / yettwasnulfa-d | |
| date / time | azemz / akud | |
| error / warning | tuccḍa / ɣur-k | |
| help | tallalt | |
| notification | alɣu | |
| wizard | amarag | |
| feed (RSS) | asuddem | RSS feed: asuddem RSS |
| tab | iccer | |
| audit, audit trail | asenqed, aɣmis n usenqed | |
| repository | akufi | |
| checksum | tamkadt n usenqed | |
| account | amiḍan | |
| default | amezwer | default value: azal amezwer |
| toolbar | afeggag n yifecka | |
| container | amagbar | |
| AI | IA | |
| pattern (name pattern) | taneɣruft | kept apart from tamudemt (template) |
| wildcard | ajuker | |
| remote ID | asulay n tama-nniḍen | |
| placeholder | amḍiq | |
| collection | tagrumma | information collector: amsegrew n yisallen |
| approve, approval | qbel, aqbal | |
| software | aseɣẓan | |
| creator / modifier | amesnulfu / ambeddel | |
| request (HTTP) | tuttra | |
| query | tasuturt | search query: tasuturt n unadi |
| handler / provider / listener | amsefrak / asaǧǧaw / amsefli | |
| engine | amutur | |
| release / upgrade | tazrewt / aleqqem | update: leqqem |
| backup | akles n tɣellist | |
| maintenance | aseggem | |
| directory | akaram | |
| header (HTTP) | aqerru | |
| page layout (pagelayout template) | tamudemt n usebter | kept apart from asiskel (Exponential Layouts) |
| passkey | tasarut n unekcum | |
| two-step sign-in | anekcum s snat n tikkal | |
| device / browser | ibenk / iminig | |
| link | aseɣwen | |
| registration, register | ajerred, jerred | |
| payment gateway | tawwurt n lexlaṣ | |
| form | tiferkit | |
| comment | awennit | |
| newsletter / subscription | tabrat n yisallen / amulteɣ | |
| field | urti | |
| value | azal | |
| list | tabdart | |
| page | asebter | |
| title / name | azwel / isem | the column "Name": Isem |
| description | aglam | |
| type | anaw | |
| path | abrid | |
| width / height | tehri / teɣzi | |
| size | tiddi | |
| none | ulac | |
| yes / no | ih / ala | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Tahanut, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is Sekles.
- "Delete" is Kkes (destroy) and "remove" is Ssuffeɣ (take out), so that the two buttons are never confused;
  "publish" is Siẓreg.
- "OK" is IH, as in the Kabyle localisations of Mozilla.
