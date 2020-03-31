=== H5P User Score (by Uni Freiburg) ===
Contributors: otacke
Tags: h5p, xapi, score
Requires at least: 4.0
Tested up to: 5.3
Stable tag: 0.1
License: MIT
License URI: https://github.com/otacke/h5p-user-score/blob/master/LICENSE

This WordPress plugin allows to display the score of H5P interactions to users.

== Description ==

*PLEASE NOTE: H5P IS A REGISTERED TRADEMARK OF JOUBEL. THIS PLUGIN WAS NEITHER CREATED BY JOUBEL NOR IS IT ENDORSED BY THEM.*

*PLEASE NOTE: THIS PLUGIN IS THE RESULT OF A UNIVERSITY PROJECT WITH SPECIFIC REQUIREMENTS AND NOT IN ACTIVE DEVELOPMENT. WHILE OLIVER TACKE IS THE DEVELOPER, HE'S MERELY THE CONTRACTOR AND NOT SUPPOSED TO DEAL WITH BUG REPORTS OR ACCEPT FEATURE REQUESTS OR PULL REQUESTS. PLEASE DON'T BOTHER HIM.*

Use WordPress shortcodes to be replaced by the item you're interested in:

=== Current user's score for a content type ===
`[h5pScore value="score" id="5"]`
Will be replaced by the current user's score for H5P content with id 5.

=== Maximum score possible for a content type ===
`[h5pScore value="maxScore" id="5"]`
Will be replaced by the maximum score possible for H5P content with id 5.

=== Current user's percentage for a content type ===
`[h5pScore value="percentage" id="5"]`
Will be replaced by the current user's percentage for H5P content with id 5.

== Installation ==

Install H5P-User-Score from the Wordpress Plugin directory or via your Wordpress instance and activate it. Done.

You can then use WordPress shortcodes to include score information in posts and pages.

Please note that scores recorded before activating this plugin will not be taken into account. Recording the score for this plugin is done using the browser's local storage, so the score is bound to the device, but there's no need for users to be logged into WordPress. However, the score data may be deleted after 7 days on Safari browsers and iOS devices, see. https://webkit.org/blog/10218/full-third-party-cookie-blocking-and-more/

== Changelog ==

= 0.1 =
Initial release.

== Upgrade Notice ==

= 0.1 =
Initial release.
