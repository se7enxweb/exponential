{section show=$validation.processed}
    {section show=or( $validation.attributes, $validation.placement, $validation.custom_rules )}
        <div class="message-error a4-error-summary" id="a4-error-summary" role="alert" tabindex="-1" aria-labelledby="a4-error-summary-title">
            <h2 id="a4-error-summary-title"><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'The draft could not be stored.'|i18n( 'design/admin/content/edit' )}</h2>

            {section show=$validation.attributes}
                <p>{'Required data is either missing or is invalid'|i18n( 'design/admin/content/edit' )}:</p>
                <ul>
                    {section var=UnvalidatedAttributes loop=$validation.attributes}
                        <li>{if is_set( $UnvalidatedAttributes.item.identifier )}<a href="#ezcca-attr-{$UnvalidatedAttributes.item.id|wash}">{$UnvalidatedAttributes.item.name|wash}</a>{else}{$UnvalidatedAttributes.item.name|wash}{/if}: {$UnvalidatedAttributes.item.description|wash}</li>
                    {/section}
                </ul>
            {/section}

            {section show=$validation.placement}
                <p>{'The following locations are invalid'|i18n( 'design/admin/content/edit' )}:</p>
                <ul>
                    {section var=UnvalidatedPlacements loop=$validation.placement}
                        <li>{$UnvalidatedPlacements.item.text|wash}</li>
                    {/section}
                </ul>
            {/section}

            {section show=$validation.custom_rules}
                <p>{'The following data is invalid according to the custom validation rules'|i18n( 'design/admin/content/edit' )}:</p>
                <ul>
                    {section var=UnvalidatedCustomRules loop=$validation.custom_rules}
                        <li>{$UnvalidatedCustomRules.item.text|wash}</li>
                    {/section}
                </ul>
            {/section}
        </div>

    {section-else}

        {section show=$validation_log}
            <div class="message-warning" role="status">
                <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'The draft was only partially stored.'|i18n( 'design/admin/content/edit' )}</h2>
                {section var=ValidationLogs loop=$validation_log}
                    <p>{$ValidationLogs.item.name|wash}:</p>
                    <ul>
                        {section var=LogMessages loop=$ValidationLogs.item.description}
                            <li>{$LogMessages.item|wash}</li>
                        {/section}
                    </ul>
                {/section}
            </div>
            {section-else}
            <div class="message-feedback" role="status">
                <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'The draft was successfully stored.'|i18n( 'design/admin/content/edit' )}</h2>
            </div>
        {/section}
    {/section}
{/section}
