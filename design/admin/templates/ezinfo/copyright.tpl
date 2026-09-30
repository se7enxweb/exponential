{* The administration's Info > Copyright page.

   Set by kernel/ezinfo/copyright.php:
     copyright_info    hash( 'years', 'holder', 'original_years', 'original_holder', 'license',
                             'license_version', 'license_url', 'version' )
     copyright_notice  the same notice as HTML paragraphs in English; shown as it is under
                       "The notice in English", since a translation is only a reading aid

   The styles are in stylesheets/ezinfo.css. *}
{ezcss_require( 'ezinfo.css' )}
<div class="context-block ezinfo-page ezinfo-copyright">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Copyright Notice'|i18n( 'design/admin/ezinfo/about' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="ezinfo-intro">
    <ul class="ezinfo-summary">
        <li class="ezinfo-summary-version"><span>{'Version'|i18n( 'design/admin/ezinfo/about' )}</span> <strong>{$copyright_info.version|wash}</strong></li>
        <li class="ezinfo-summary-license">GNU GPL v{$copyright_info.license_version|wash}</li>
        <li><a href={'/ezinfo/about'|ezurl}>{'About Exponential'|i18n( 'design/admin/ezinfo/about' )}</a></li>
    </ul>
</div>

<section class="ezinfo-section">
    <h2>{'Copyright'|i18n( 'design/admin/ezinfo/about' )}</h2>
    <div class="ezinfo-prose">
        <p class="ezinfo-notice-holder">{'Copyright (C) %years %holder. All rights reserved.'|i18n( 'design/admin/ezinfo/about',, hash( '%years', $copyright_info.years, '%holder', $copyright_info.holder ) )|wash}</p>
    </div>
</section>

<section class="ezinfo-section">
    <h2>{'License'|i18n( 'design/admin/ezinfo/about' )}</h2>
    <div class="ezinfo-prose">
        <p>{'Exponential is free software: you may redistribute it and/or modify it under the terms of the "%license" version %license_version as published by the Free Software Foundation and appearing in the file LICENSE included in the packaging of this software.'|i18n( 'design/admin/ezinfo/about',, hash( '%license', $copyright_info.license, '%license_version', $copyright_info.license_version ) )|wash}</p>
        <p>{'The "%license" (GPL) is available at %link and in the file LICENSE included in the packaging of this software.'|i18n( 'design/admin/ezinfo/about',, hash( '%license', $copyright_info.license|wash, '%link', concat( '<a href="', $copyright_info.license_url|wash, '" rel="noopener">', $copyright_info.license_url|wash, '</a>' ) ) )}</p>
        <p><a href={'/ezinfo/about#ezinfo-license'|ezurl}>{'Read the license text'|i18n( 'design/admin/ezinfo/about' )}</a></p>
    </div>
</section>

<section class="ezinfo-section">
    <h2>{'No warranty'|i18n( 'design/admin/ezinfo/about' )}</h2>
    <p class="ezinfo-warranty">{'Exponential is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE.'|i18n( 'design/admin/ezinfo/about' )}</p>
</section>

<section class="ezinfo-section">
    <h2>{'Original authors and third-party software'|i18n( 'design/admin/ezinfo/about' )}</h2>
    <div class="ezinfo-prose">
        <p>{'Exponential was originally developed by %holder: Copyright (C) %years %holder. All rights reserved.'|i18n( 'design/admin/ezinfo/about',, hash( '%years', $copyright_info.original_years, '%holder', $copyright_info.original_holder ) )|wash}</p>
        <p>{'The copyright notices of the original authors and of the third-party software included with Exponential are kept in the source files and in the LICENSE file, as the license requires.'|i18n( 'design/admin/ezinfo/about' )}</p>
        <p><a href={'/ezinfo/about#ezinfo-third-party'|ezurl}>{'Third-Party Software'|i18n( 'design/admin/ezinfo/about' )}</a> &middot; <a href={'/ezinfo/about#ezinfo-extensions'|ezurl}>{'Extensions'|i18n( 'design/admin/ezinfo/about' )}</a></p>
    </div>
</section>

<section class="ezinfo-section">
    <details class="ezinfo-plain-notice">
        <summary>{'The notice in English, as it is distributed'|i18n( 'design/admin/ezinfo/about' )}</summary>
        <div class="ezinfo-plain-notice-text" lang="en">{$copyright_notice}</div>
    </details>
</section>

</div>
{* DESIGN: Content END *}</div></div></div>

</div>
