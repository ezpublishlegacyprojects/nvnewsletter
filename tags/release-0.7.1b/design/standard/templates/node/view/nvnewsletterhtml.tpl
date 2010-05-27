{*?template charset=utf-8?*}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>{$node.name}</title>
	<style type="text/css">
    {literal}
    body, td, tr {
        font-family: Tahoma, Arial, Helvetica, sans-serif;
        font-size: 11px;
        font-weight: normal;
        color: #000000;
    }
    h1 {
        font-family: Tahoma, Arial, Helvetica, sans-serif;
        font-size: 18px;
        font-weight: bold;
        color: #595959;
        display: inline;
    }
    
    a:link 		{ color: #C53417; text-decoration:none; }
    a:active 	{ color: #C53417; text-decoration:none; }
    a:visited 	{ color: #C53417; text-decoration:none; }
    a:hover   	{ color: #C53417; text-decoration:none; }
    {/literal}
	</style>
</head>
<body bgcolor="#C7C6C6" bottommargin="0" topmargin="0" rightmargin="0" leftmargin="0" marginheight="0" marginwidth="0">

{* Required stuff *}
{def $view_link = nvnewslettergetviewlink( $node.contentobject_id, $node.contentobject_version )
     $links = nvnewslettergetsitelink( $node.contentobject_id, $node.contentobject_version )
     $nvn_site_url = $links[0] 
     $nvn_file_url = $links[1]
     $nvn_tracker_url = concat( $links[0], '/nvnewsletter/viewlink/', $node.contentobject_id, '/?lnk=' )}
     
{* Use in any template $#site_url or $#file_url *}
{set scope='global' $nvn_site_url=$nvn_site_url}
{set scope='global' $nvn_file_url=$nvn_file_url}
{set scope='global' $nvn_tracker_url=$nvn_tracker_url}

<table cellspacing="0" cellpadding="0" border="0" width="498" align="center" bgcolor="#ffffff">
<tr>
	<td rowspan="4" valign="top" background="{concat( $nvn_site_url, 'left_shade.gif'|ezimage(no) )}"><img src="{concat( $nvn_site_url, 'left_shade.gif'|ezimage(no) )}" width="5" height="25" alt="" border="0"></td>
	<td colspan="3"><a href="http://www.naviatech.fi/"><img src={concat( $nvn_site_url, 'logo.jpg'|ezimage(no) )} width="559" height="56" alt="" border="0"></a></td>
	<td rowspan="4" valign="top" background="{concat( $nvn_site_url, 'right_shade.gif'|ezimage(no) )}"><img src="{concat( $nvn_site_url, 'right_shade.gif'|ezimage(no) )}" width="5" height="25" alt="" border="0"></td>
</tr>
<tr>
	<td colspan="3">{attribute_view_gui attribute=$node.data_map.image image_class=reference}<br><img src="{concat( $nvn_site_url, '1x1.gif'|ezimage(no) )}" width="1" height="30" alt="" border="0"><br></td>
</tr>
<tr>
	<td rowspan="2" valign="top" background="{concat( $nvn_site_url, 'left_inner.gif'|ezimage(no) )}"><img src="{concat( $nvn_site_url, 'left_inner.gif'|ezimage(no) )}" width="38" height="20" alt="" border="0"></td>
	<td valign="top" width="483">
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top"><img src="{concat( $nvn_site_url, 'icon.gif'|ezimage(no) )}" width="33" height="33" alt="" border="0"></td>
		<td width="100%" valign="top" style="padding-left: 10px; padding-top: 4px;"><h1>{$node.name}</h1></td>
	</tr>
	</table>
    <br>
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td><img src="{concat( $nvn_site_url, '1x1.gif'|ezimage(no) )}" width="16" height="1" alt="" border="0"></td>
		<td width="100%">
        
        {attribute_view_gui attribute=$node.data_map.description}
        
        {def $children=fetch( content, list, hash( parent_node_id, $node.node_id,
                                                   sort_by, $node.sort_array ) )}

        {if $children}
            <h2>{'Articles'|i18n('design/nvnewsletter/example_template')}</h2>
            {foreach $children as $child}
                <p><strong>{$child.name}</strong></p>
                {attribute_view_gui attribute=$child.data_map.intro}
            {/foreach}
        {/if}
        
		</td>
		<td><img src="{concat( $nvn_site_url, '1x1.gif'|ezimage(no) )}" width="16" height="1" alt="" border="0"></td>
	</tr>
	</table>
	</td>
	<td rowspan="2" valign="top" background="{concat( $nvn_site_url, 'right_inner.gif'|ezimage(no) )}"><img src="{concat( $nvn_site_url, 'right_inner.gif'|ezimage(no) )}" width="38" height="20" alt="" border="0"></td>
</tr>
<tr>
	<td valign="top" align="center">
        <img src="{concat( $nvn_site_url, '1x1.gif'|ezimage(no) )}" width="1" height="40" alt="" border="0"><br>
        <p>{'nvNewsletter example template created by'|i18n('design/nvnewsletter/example_template')} <a href="http://www.naviatech.fi/">Naviatech Solutions Oy</a>
    </td>
</tr>
<tr>
	<td colspan="5"><img src="{concat( $nvn_site_url, 'bottom.gif'|ezimage(no) )}" width="569" height="52" alt="" border="0"></td>
</tr>
</table>

<div align="center">
    <p style="font-size:11px"><a href="{$view_link}/<NVN_USER_CODE>">{'View newsletter in browser'|i18n('design/nvnewsletter/example_template')}</a> | <a href="{$nvn_site_url}/nvnewsletter/unsubscribe/{$node.contentobject_id}/<NVN_USER_CODE>">{'Unsubscribe'|i18n('design/nvnewsletter/example_template')}</a></p>
</div>

<img alt="" height="1" width="1" src="{$nvn_site_url}/trk_<NVN_TRACKER_CODE>.gif" />
</body>
</html>