{* The filter of the order lists by the siteaccess (store or site) the orders were placed in (expOrderSiteaccess,
   doc/eng-us/features/6.0/order-siteaccess.md).

   A form of its own, sent with GET, outside the list's form: the view answers with the list's address carrying
   (siteaccess)/<name>, so paging and sorting keep the filter. "Unknown" lists the orders placed before the
   siteaccess was recorded. The texts reuse messages every catalogue translates already.

   page_uri         the list's address, '/shop/orderlist' or '/shop/archivelist'
   siteaccess_filter what expOrderSiteaccess::templateFilter() gives the view *}
{default page_uri='/shop/orderlist'}
{if $siteaccess_filter.enabled}
<form class="a4-filterbar" method="get" action={$page_uri|ezurl}>
    <div class="a4-field">
        <label for="exp-order-siteaccess-filter">{'Siteaccess'|i18n( 'design/admin/content/job' )}</label>
        <select id="exp-order-siteaccess-filter" name="SiteaccessFilter">
            <option value=""{if eq( $siteaccess_filter.parameter, '' )} selected="selected"{/if}>{'All'|i18n( 'design/admin/content/job' )}</option>
            {foreach $siteaccess_filter.list as $sa_option}
            <option value="{$sa_option|wash}"{if eq( $sa_option, $siteaccess_filter.parameter )} selected="selected"{/if}>{$sa_option|wash}</option>
            {/foreach}
            <option value="{$siteaccess_filter.unknown|wash}"{if eq( $siteaccess_filter.parameter, $siteaccess_filter.unknown )} selected="selected"{/if}>{'Unknown'|i18n( 'design/admin/settings' )}</option>
        </select>
    </div>
    <div class="a4-field">
        <div class="a4-actions">
            <button class="a4-btn a4-btn-primary" type="submit">{'Filter'|i18n( 'design/admin/content/job' )}</button>
        </div>
    </div>
</form>
{/if}
{/default}
