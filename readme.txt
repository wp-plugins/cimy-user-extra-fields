=== Cimy User Extra Fields ===
Contributors: Marco Cimmino
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=cimmino%2emarco%40gmail%2ecom&no_shipping=0&no_note=1&tax=0&currency_code=EUR&lc=IT&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: admin, registration, profile, extra fields
Requires at least: 2.5
Tested up to: 2.7
Stable tag: 1.3.0

Add some useful fields to registration and user's info

== Description ==

WordPress is becoming more than ever a tool to open blog/websites and CMSs in an easier way. Users are increasing day by day; one of the limits however is the restricted and predefined fields that are available in the registered users profile: it is difficult for example to ask for the acceptance of "Terms and Conditions of Use" or "Permission to use personal data".

= Features =

The plug-in adds two new menu voices in the admin for the administrator and two for users.

Two new menus are:

    * "Users-> A&U Extended" lets you show users lists with the new fields that are created
    * "Options-> Cimy User Extra Fields" lets administrators add as many new fields as are needed to the users' profile, giving the possibility to set some interesting rules.

Rules are:

    * min/exact/max length admitted
	[only for text, textarea, textarea-rich, password, picture, picture-url, avatar]

    * field can be empty
	[only for text, textarea, textarea-rich, password, picture, picture-url, dropdown, avatar]

    * check for e-mail address syntax
	[only for text, textarea, textarea-rich, password]

    * field can be modified after the registration
	[only for text, textarea, textarea-rich, password, picture, picture-url, checkbox, radio, dropdown, avatar]
	[for radio and checkbox 'edit_only_if_empty' has no effects and 'edit_only_by_admin_or_if_empty' has the same effect as edit_only_by_admin]

    * field equal to some value (for example accept terms and conditions)
	[all except avatar by default set to 512]

      * equal to can be or not case sensitive
	[only for text, textarea, textarea-rich, password, dropdown]

    * field can be hidden during registration
	[all]

    * field can be hidden in user's profile
	[all]

    * field can be hidden in A&U Extended page
	[all]

New fields will be visible in the profile and in the registration.
As for now the plug-in supports: text, textarea, textarea-rich, password, checkbox, radio, drop-down, picture, picture-url, registration-date and avatar fields, future versions can have more.

Following WordPress hidden fields can be enabled during registration: password, name, surname, nickname, website, Aim, Yahoo im, Jabber/Google Talk

== Frequently Asked Questions ==

= I have a lot of questions and I want support where can I go? =

http://www.marcocimmino.net/cimy-wordpress-plugins/cimy-user-extra-fields/

== Installation ==

Just copy the plug-in in your plug-in directory and activate it

== Screenshots ==

1. Registration form with extra fields
2. User's profile with extra fields
3. Main options page
4. Add a new field form
