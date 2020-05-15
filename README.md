# H5P-User-Score (by Uni Freiburg)

*PLEASE NOTE: H5P IS A REGISTERED TRADEMARK OF JOUBEL. THIS PLUGIN WAS NEITHER CREATED BY JOUBEL NOR IS IT ENDORSED BY THEM.*

*PLEASE NOTE: THIS PLUGIN IS THE RESULT OF A UNIVERSITY PROJECT WITH SPECIFIC REQUIREMENTS AND NOT IN ACTIVE DEVELOPMENT. WHILE OLIVER TACKE IS THE DEVELOPER, HE'S MERELY THE CONTRACTOR AND NOT SUPPOSED TO PROVIDE SUPPORT ACCEPT FEATURE REQUESTS OR PULL REQUESTS. PLEASE DON'T BOTHER HIM.*

## Features
Allows you to add user score information of H5P interactions to WordPress posts and pages.

## Install/Usage
[Download H5P-User-Score](https://github.com/otacke/h5p-user-score/archive/master.zip), install it on your Wordpress instance and activate it. Done.

You can then use WordPress shortcodes to include score information in posts and pages.

Please note that scores recorded before activating this plugin will not be taken into account. Recording the score for this plugin is done using the browser's local storage, so the score is bound to the device, but there's no need for users to be logged into WordPress. However, the score data may be deleted after 7 days on Safari browsers and iOS devices, see. https://webkit.org/blog/10218/full-third-party-cookie-blocking-and-more/

### Current user's score for a particular H5P interaction
Use the shortcode _[h5pScore value="score" id="xyz"]_ to include the current user's score for a particular H5P interaction where _xyz_ is the id of the H5P interaction of interest.

### Current user's score percentage for a particular H5P interaction
Use the shortcode _[h5pScore value="percentage" id="xyz"]_ to include the current user's score percentage for a particular H5P interaction where _xyz_ is the id of the H5P interaction of interest.

### Maximum possible score for a particular H5P interaction
Use the shortcode _[h5pScore value="maxScore" id="xyz"]_ to include the maximum possible score for a particular H5P interaction where _xyz_ is the id of the H5P interaction of interest.

## Example
Let's say you have an H5P multiple choice quiz with ID 17 available for your site's users. Assume the current user scored 5 points while the maximum possible score is 10 points.

On any page or post (not limited to those where the H5P interaction is available), you could e.g. write:

    In the multiple choice quiz, you scored [h5pScore value="score" id="17"] points out of [h5pScore value="max" id="17"]. That's [h5pScore value="percentage" id="17"] percent!

On the page or post, this would render as:

    In the multiple choice quiz, you scored 5 points out of 10. That's 50 percent!

## License
H5P-User-Score is licensed under the [MIT License](https://github.com/otacke/h5p-user-score/blob/master/LICENSE).

## Funding
The H5P User Score WordPress Plugin was developed by the University of Freiburg, funded by the Ministry of Science, Research and Arts Baden-Württemberg, Germany.
