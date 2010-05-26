{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h4>{'Newsletter states'|i18n( 'design/nvnewsletter' )}</h4>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">

<ul>
    <li><div><a href={'nvnewsletter/list_sent'|ezurl}>{'Sent'|i18n('design/nvnewsletter')}</a></div></li>
    <li><div><a href={'nvnewsletter/list_in_progress'|ezurl}>{'In progress'|i18n('design/nvnewsletter')}</a></div></li>
    <li><div><a href={'nvnewsletter/list_draft'|ezurl}>{'Drafts'|i18n('design/nvnewsletter')}</a></div></li>
    <li><div><a href={'nvnewsletter/list_failed'|ezurl}>{'Failed'|i18n('design/nvnewsletter')}</a></div></li>
</ul>

{* Left menu width control. *}
<div id="widthcontrol-links" class="widthcontrol">
<p>
{switch match=ezpreference( 'admin_left_menu_width' )}
{case match='medium'}
<a href={'/user/preferences/set/admin_left_menu_width/small'|ezurl} title="{'Change the left menu width to small size.'|i18n( 'design/admin/parts/content/menu' )}">{'Small'|i18n( 'design/admin/parts/content/menu' )}</a>
<span class="current">{'Medium'|i18n( 'design/admin/parts/content/menu' )}</span>
<a href={'/user/preferences/set/admin_left_menu_width/large'|ezurl} title="{'Change the left menu width to large size.'|i18n( 'design/admin/parts/content/menu' )}">{'Large'|i18n( 'design/admin/parts/content/menu' )}</a>
{/case}

{case match='large'}
<a href={'/user/preferences/set/admin_left_menu_width/small'|ezurl} title="{'Change the left menu width to small size.'|i18n( 'design/admin/parts/content/menu' )}">{'Small'|i18n( 'design/admin/parts/content/menu' )}</a>
<a href={'/user/preferences/set/admin_left_menu_width/medium'|ezurl} title="{'Change the left menu width to medium size.'|i18n( 'design/admin/parts/content/menu' )}">{'Medium'|i18n( 'design/admin/parts/content/menu' )}</a>
<span class="current">{'Large'|i18n( 'design/admin/parts/content/menu' )}</span>
{/case}

{case}
<span class="current">{'Small'|i18n( 'design/admin/parts/content/menu' )}</span>
<a href={'/user/preferences/set/admin_left_menu_width/medium'|ezurl} title="{'Change the left menu width to medium size.'|i18n( 'design/admin/parts/content/menu' )}">{'Medium'|i18n( 'design/admin/parts/content/menu' )}</a>
<a href={'/user/preferences/set/admin_left_menu_width/large'|ezurl} title="{'Change the left menu width to large size.'|i18n( 'design/admin/parts/content/menu' )}">{'Large'|i18n( 'design/admin/parts/content/menu' )}</a>
{/case}
{/switch}
</p>
</div>
<div class="" id="widthcontrol-handler">
<div class="widthcontrol-grippy"></div>
</div>
{* DESIGN: Content END *}</div></div></div></div></div></div>
