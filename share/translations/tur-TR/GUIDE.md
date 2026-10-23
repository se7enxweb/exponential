# Turkish (tur-TR): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Turkish of Turkey, in the plain, factual style of the major operating systems and office software in
Turkish. Spelling follows the Turkish Language Association (TDK). No other locale reads this catalogue; a string
without a Turkish translation shows in English.

## 2. Form of address

The user is addressed with the polite **siz**, everywhere and without exception.

- **Buttons, menu items, links and short actions** use the bare imperative (the verb stem): Kaydet (Save),
  Sil (Delete), Kaldır (Remove), Düzenle (Edit), İptal (Cancel), Uygula (Apply), Yayımla (Publish), Yükle (Upload),
  Tümünü seç (Select all), Taslağı at (Discard draft), Yer işaretlerine ekle (Add to bookmarks).
- **Titles, column headings and tabs** are nouns or noun phrases: Kullanıcı ayarları, Önbellek yönetimi,
  Değiştirilme, Yayımlanma.
- **Sentences, hints, questions and error messages** use the siz forms: the imperative in -(y)In, the possessive
  -(I)nIz, verbs in the second person plural:
  - Bu öğeleri kaldırmak istediğinizden emin misiniz? (Are you sure you want to remove these items?)
  - Lütfen bir konum seçin (Please choose a location)
  - Sınıfları kaldırma izniniz yok. (You do not have permission to remove classes.)
  - Karşıya yüklemek istediğiniz dosyayı seçin. (Select the file that you want to upload.)
- A tooltip that is a sentence ends in the siz imperative: "Seçili öğeleri kaldırın."
- "Please" is "Lütfen", followed by the siz imperative.
- **Not allowed**: sen forms in sentences (sen, senin, sana, parolan, yapabilirsin, emin misin), and mixing forms
  in one text. The bare stem (Seç) is for buttons only.

## 3. Script and typography

- Turkish capitals and small letters: İ/i and I/ı (İptal, Işık). Labels capitalise only the first word, except
  names (Kullanıcı hesabı).
- A suffix after a name, abbreviation or code takes an apostrophe: Solr'a, URL'yi, PHP'nin, Uygula'ya.
- A placeholder takes no suffix, because its vowel harmony is unknown: write "%name adlı nesne", "Öğe sayısı:
  %count", "%count öğe".
- After a number Turkish uses the singular: "%count öğe", "%count nesne".
- Quotation marks "" or “ ” as in the source; the colon after a label is kept (Ad:).
- Numbers: digits 0–9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Turkish | Note |
|---|---|---|
| content | içerik | |
| content object / object | içerik nesnesi / nesne | |
| node | düğüm | |
| location | konum | main location = ana konum |
| content class / class | içerik sınıfı / sınıf | class group = sınıf grubu |
| attribute | öznitelik | |
| datatype | veri türü | |
| version | sürüm | |
| draft | taslak | My drafts = Taslaklarım |
| publish / published | yayımla / yayımlanmış | column "Published" = Yayımlanma; never yayınla |
| translation / language | çeviri / dil | main language = ana dil |
| siteaccess | site erişimi | plural site erişimleri |
| site | site | |
| design | tasarım | |
| section | bölüm | the Exponential section only |
| object state / state group | nesne durumu / durum grubu | |
| role / policy | rol / ilke | |
| permission / access | izin / erişim | Access denied = Erişim reddedildi |
| limitation | sınırlama | |
| user / user group | kullanıcı / kullanıcı grubu | anonymous user = anonim kullanıcı |
| user account / My account | kullanıcı hesabı / Hesabım | |
| log in / log out | oturum aç / oturumu kapat | |
| password / username | parola / kullanıcı adı | |
| e-mail | e-posta | |
| cache / clear cache | önbellek / önbelleği temizle | |
| template | şablon | |
| override | geçersiz kılma | override templates = geçersiz kılma şablonları |
| fallback (language, chain) | geri dönüş | fallback chain = geri dönüş zinciri |
| fallback (value, model) | yedek | fallback value = yedek değer |
| workflow / workflow event | iş akışı / iş akışı olayı | |
| trigger | tetikleyici | |
| trash / empty trash | çöp kutusu / çöp kutusunu boşalt | |
| bookmark | yer işareti | |
| settings | ayarlar | |
| Setup (admin tab) | Kurulum | |
| Dashboard | Pano | |
| Content structure / Media library | İçerik yapısı / Medya kitaplığı | |
| layout / block / zone | düzen / blok / bölge | Exponential Layouts stays as is |
| extension | uzantı | |
| plugin | eklenti | |
| package | paket | |
| module / view / function | modül / görünüm / işlev | View (button) = Görüntüle |
| search / search engine | arama, Ara / arama motoru | |
| search index / index / reindex | arama dizini / dizin, dizinle / yeniden dizinleme | |
| log / log file | günlük / günlük dosyası | |
| audit / audit trail | denetim / denetim izi | |
| cronjob | cron işi | |
| job / queue / worker | iş / kuyruk / çalışan | |
| preview | Önizle (button) / önizleme | |
| upload / download | karşıya yükle (button: Yükle) / indir | |
| export / import | dışa aktar / içe aktar | |
| remove | kaldır | take out of a list or place |
| delete | sil | destroy |
| edit / save / store | düzenle / kaydet / kaydet | "Store" is the save button; only the shop tab of the header is Mağaza |
| discard | vazgeç | Discard draft = Taslağı at |
| apply / cancel / OK | uygula / iptal / Tamam | |
| back / next / previous | Geri / Sonraki / Önceki | |
| yes / no | Evet / Hayır | |
| add / create / new | ekle / oluştur / yeni | |
| move / copy | taşı / kopyala | |
| close / refresh | kapat / yenile | |
| update / reset / restore | güncelle / sıfırla / geri yükle | |
| select / select all | seç / Tümünü seç | |
| enable / disable | etkinleştir / devre dışı bırak | enabled = etkin, disabled = devre dışı |
| visible / hidden | görünür / gizli | hide = gizle, reveal = göster |
| archive | arşiv | |
| order (shop) / order (sorting) | sipariş / sıra | |
| sort / ascending / descending | sırala / artan / azalan | |
| tag | etiket | an HTML or XML tag is also etiket; custom tag = özel etiket |
| keyword | anahtar sözcük | |
| owner / creator | sahip / oluşturan | |
| created / modified (column) | oluşturulma / değiştirilme | in a sentence: oluşturuldu / değiştirildi |
| relation / related objects | ilişki / ilişkili nesneler | reverse related = ters ilişkili |
| parent / sub-items | üst öğe / alt öğeler | item = öğe |
| subtree | alt ağaç | |
| ID / identifier / remote ID | ID, kimlik / tanımlayıcı / uzak kimlik | a bare "ID" column stays ID; node ID = Düğüm kimliği |
| URL alias / URL wildcard | URL takma adı / URL joker karakteri | |
| priority / default / required | öncelik / varsayılan / gerekli | |
| status, state | durum | |
| warning / error | uyarı / hata | |
| notification | bildirim | |
| collaboration | işbirliği | |
| information collection / collected information | bilgi toplama / toplanan bilgiler | |
| file / folder / directory | dosya / klasör / dizin | |
| image / media | resim / medya | |
| thumbnail | küçük resim | |
| path / browse | yol / gözat | |
| database | veritabanı | |
| statistics / system information | istatistikler / sistem bilgileri | |
| install / installation / uninstall | kur, yükle / kurulum / kaldır | installed = yüklü |
| shop / basket / customer / product | mağaza / sepet / müşteri / ürün | |
| VAT / discount / price / currency | KDV / indirim / fiyat / para birimi | |
| wish list | istek listesi | |
| newsletter / subscription | bülten / abonelik | |
| session | oturum | |
| field | alan | |
| format | biçim | |
| feed | akış | |
| tab | sekme | |
| theme | tema | |
| menu / toolbar | menü / araç çubuğu | |
| name / title / description / type / value / date | ad / başlık / açıklama / tür / değer / tarih | |
| AI / model / prompt / token | yapay zekâ / model / istem / belirteç | |
| embedding / chunk / vector | gömme / parça / vektör | |
| provider | sağlayıcı | |
| deploy, deployment | dağıtım | |
| mapping | eşleme | |
| editor (software) | düzenleyici | |
| interface language | arayüz dili | |
| admin interface | yönetim paneli | |

## 5. Decisions after the translation

| English | Turkish | Note |
|---|---|---|
| Next | Sonraki | not İleri; the wizard and pager buttons use Sonraki / Önceki |
| Approve / Approval | Onayla / onay | Confirm is Onayla too; validate = doğrula |
| Reject | Reddet | |
| Pending | Beklemede | |
| Container | kapsayıcı | |
| Locale | yerel ayar | |
| Charset | karakter kümesi | |
| Wildcard | joker | |
| Redirect | yönlendir | |
| License | lisans | |
| Embed / embedded | göm / gömülü öğe | |
| Placement | yerleşim | |
| Overview | genel bakış | |
| Rich text | zengin metin | |
| Preset | ön ayar | |
| Batch | toplu iş | |
| Scope / assign | kapsam / ata | |
| Thread (process) / thread (conversation) | iş parçacığı / ileti dizisi | |
| handler / alias / wizard | işleyici / diğer ad / sihirbaz | URL alias stays URL takma adı |
| checksum / backend | sağlama toplamı / arka uç | |
| method / listener / controller | yöntem / dinleyici / denetleyici | |
| allowlist / read-only / argument | izin listesi / salt okunur / bağımsız değişken | |
| dry run / self-test | deneme çalıştırması / kendi kendine test | |
| slot / collection / facets / elevate | yuva / koleksiyon / yönler / öne çıkar | |
| tick (a checkbox) | işaretle | |
