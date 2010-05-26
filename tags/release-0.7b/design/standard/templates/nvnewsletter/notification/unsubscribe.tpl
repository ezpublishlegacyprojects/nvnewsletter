{'Email address %email unsubscribed newsletter on %date.'|i18n( 'design/nvnewsletter/notification', '', hash( '%email', $receiver.email_address, '%date', currentdate()|l10n( 'shortdatetime' ) ) )}

-- 
{'Sent from'|i18n('design/nvnewsletter/notification')} {ezini( 'SiteSettings', 'SiteURL', 'site.ini' )}