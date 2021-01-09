=== MooWoodle ===
Contributors: downtown2020, trideep_das_modak
Tags: wordpress, moodle, wooCommerce, wordpress-moodle, wooCommerce-moodle
Donate link: https://wc-marketplace.com
Requires at least: 4.1.1
Tested up to: 5.4.2
Requires at least PHP: 5.6
Stable tag: 2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The MooWoodle plugin is an extention of WooCommerce that acts as a bridge between WordPress/Woocommerce and Moodle.


== This plugin now supports Bulk Purchase ==

== Description ==
The MooWoodle plugin is an extention of WooCommerce that acts as a bridge between WordPress/Woocommerce and Moodle. It fetches all the courses from your Moodle instance and makes them available for sale, which may be bought by users through WooCommerce. It reduces your effort by synchronising your LMS site with your online store. And when someone purchases a course from the store he/she is automatically gets registered for the course in the LMS site. More over this plugin works with WooCommerce subscription plugin too.

* For details documentation: [Click Here](https://dualcube.com/installation-guide-for-moowoodle/)

For a complete instruction on the MooWoodle set-up, [Click Here](https://dualcube.com/docs/moowoodle-set-up-guide/#3-toc-title)


= Compatibility =

* Compatible with the latest version of WordPress, WooCommerce, Moodle 
* WooCommerce upto 4.0.1
* WordPress upto 5.4.1
* Moodle upto 3.8.3
* Multilingual Support is included with the plugin and is fully compatible with WPML.

== Configurable ==
*For a complete instruction on the MooWoodle set-up, [Click Here](https://dualcube.com/docs/moowoodle-set-up-guide/#3-toc-title)

== Other Moodle Products From Dualcube ==
After years of not getting it just right, our excellent design and development team combined craft, care, love and experience to deliver two impeccable Moodle themes i.e "Nalanda" and "University" , blend with a smooth and rich UI.
It was mostly our clients who inspired us for this venture. Their constant feedback and appreciation led us to build this for the countless Moodle lovers.

* You can view the demo of Nalanda here [Click Here](http://nalanda.dualcube.com/)
** Much awaited Dark mode is coming with the new update.

* Student Access: 
* Login ID: student
* Password: 1Admin@23

* You can view the demo of University here [Click Here](https://gladguys.com/demo/moodle/universitydemo/)

* Student Access: 
* Login ID: student1
* Password: 1Admin@23


* We also have a premium version of the plugin on Dualcube with Single Sign-On feature.  
* This is to be installed in Moodle. 
* To get any of these themes: [Click Here](https://dualcube.com/shop/)

= Feedback =
All we want is love. We are extremely responsive about support requests - so if you face a problem or find any bugs, shoot us a mail or post it in the support forum, and we will respond within 24 hours(during business days). If you get the impulse to rate the plugin low because it is not working as it should, please do wait for our response because the root cause of the problem may be something else. 

== Installation ==
NOTE: MooWoodle plugin is a extention of WooCommerce, so the WooCommerce plugin must be installed and activated in your WordPress site for this plugin to work properly.

1. Download and install MooWoodle plugin using the built-in WordPress plugin installer.
If you download MooWoodle plugin manually, make sure it is uploaded to `/wp-content/plugins/moowoodle/`. Or follow the steps below:
Plugins > Add new > Upload plugin > Upload moowoodle.zip > Install Now.

2. Activate the plugin through the \'Plugins\' menu in WordPress.



== Workflow ==

After the users receive the order-complete email, they need to click the link of the course or courses that they bought. 
It will then create and enroll the users in their courses.
They can then access the courses using the username and password sent via email.

Alternatively, When someone buys a course, after the payment, he/she will be directed to a thank you page which normally contains the link of the my courses page.

On this page you will have all the courses listed that you have bought along with the links to the individual courses.

You will need to click on the links/buttons to get enrolled.
If you have bought a course and, the course is listed on that page but if the link is not displayed; then the payment is not verified yet. Once it is verified, the link will appear.

Note: You will need to click the purchased courses' links/buttons in the email body or on my course page in order to get enrolled.Then only you can use the ID and password to access your course on Moodle site.



== Frequently Asked Questions ==
= Does this plugin work with newest WP version and also older versions? =
Yes, this plugin works really fine with WordPress 4.9! It is also compatible for older WordPress versions upto 4.1.1.
= Does this plugin work with latest version of Moodle? =
Yes, this plugin works really fine with WordPress 4.9! It is also compatible with Moodle version 3.4+.
= Up to which version of WooCommerce this plugin compatible with? =
This plugin is compatible with the latest version of WooCommerce, that is, Version 3.2.6.
= Does this plugin work with WooCommerce Subscription? =
Yes, it works with WooCommerce Subscription.

== Screenshots ==
1. Fill up `Access URL`, `Webservice token` and other settings fields.
2. Select `Yes` and click on `Synchronise` to sync courses and course categories from your moodle site.
3. In advanced features check `Enable web service` check box and save changes to enable web service for your moodle site.
4. Enable REST protocol from manage protocol of your moodle site.
5. In `External services` click on `Add` to add your external service.
6. Give a `Name` to your external service and check `Enabled` then `Add service` to add your external service.
7. Click on `Add functions` to add functions to your service.
8. Add above mentioned functions mentioned to your service.
9. In `Manage tokens` click on `Add` to generate new webservice token for your service.
10. Select the admin user from the `User` list and your service form `Service` list the save changes.
11. The token generated for your service. (Copy this token and pest it in `Webservice token` field in our WordPress plugin settings.)
12. Uncheck `Password policy` then save changes to disable Moodle's default password policy checking.


== Changelog ==

= 2.4 =
* Fix - Firstname and lastname data not store

= 2.3 =
* Added - Moodle 3.8.3 compatibility
* Added - Moowoodle Enrolment box
* Added - Sync through course and categories
* Added - Display start and end date on shop page
* Fix - Enrollment through administrative data

= 2.2 =
* Added - WooCommerce 4.0.1 compatibility added
* Added - Moodle 3.8.2 compatibility added
* Added - Show purchased courses' details on My Course page
* Fix - Enrollment against course id
* Fix - Some CSS 

= 2.1 =
* Added - Bulk Purchase 

= 2.0 =
* Added - WooCommerce 3.8 compatibility added
* Added - Moodle 3.8 compatibility added
* Fix - Now ID and password to login to the Moodle instance will be emailed to the customer after verified purchase.
* Fix - More stable workflow and coding clean-up.

= 1.3.0 =
* Added - Compatible with the latest version of WordPress, WooCommerce and Moodle 
* Added - WooCommerce upto 3.5.0
* Added - WordPress upto 4.9.8
* Added - Moodle upto 3.5
* Fix - Stable structure implementation and minor fixation

= 1.2.2 =
* Fix - Typo in registration email.

= 1.2.1 =
* Compatible with Moodle 3.4+.

= 1.2 =
* Compatible with Moodle 3.2.

= 1.1.0 =
* Tweak - Now using REST Protocol instead of XML-RPC Protocol.
* Fix - All the courses\' name are now in the `New Enrollment` notification mail.
* Fix - Admin can publish products in which course is not assigned.

= 1.0.0 =
* Initial version

== Upgrade Notice ==
= 2.3 =

= 2.2 =

= 2.1 =

= 2.0 =

= 1.3.0 =

= 1.2.2 =

= 1.2.1 =

= 1.2 =

= 1.1.0 =

= 1.0.0 =

