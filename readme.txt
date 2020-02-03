=== MooWoodle ===
Contributors: downtown2020, trideep_das_modak
Tags: wordpress, moodle, wooCommerce, wordpress-moodle, wooCommerce-moodle
Donate link: https://wc-marketplace.com
Requires at least: 4.1.1
Tested up to: 7.1.1
Requires at least PHP: 5.6
Stable tag: 2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The MooWoodle plugin is an extention of WooCommerce that acts as a bridge between WordPress/Woocommerce and Moodle.

== Description ==
The MooWoodle plugin is an extention of WooCommerce that acts as a bridge between WordPress/Woocommerce and Moodle. It fetches all the courses from your Moodle instance and makes them available for sale, which may be bought by users through WooCommerce. It reduces your effort by synchronising your LMS site with your online store. And when someone purchases a course from the store he/she is automatically gets registered for the course in the LMS site. More over this plugin works with WooCommerce subscription plugin too.

* For details documentation: [Click Here](https://dualcube.com/installation-guide-for-moowoodle/)

= Compatibility =

* Compatible with the latest version of WordPress, WooCommerce, Moodle 
* WooCommerce upto 3.8.1
* WordPress upto 5.3.2
* Moodle upto 3.8.1
* Multilingual Support is included with the plugin and is fully compatible with WPML.

= Configurable =
WP site setup:

To set up the plugin: Synchronise > Settings >
1. Fill up `Access URL` (site url of your moodle site) and `Webservice Token` (generated from your moodle site; a more detailed guide is given below).

2. Check `Create Products From Courses` checkbox if you want to create product from courses during course sync; otherwise leave it unchecked. In that case the courses will be saved in wordpress site and you can manually add products for courses later.

3. Check `Update user info with order info` checkbox to update user info with new order info.

4. By default, Yes to `Update existing users` for Moodle will update the profile fields in Moodle for existing users.

5. Click on `Save Changes` button.

To Sync the plugin: Synchronise > Synchronise >
1. Click Yes to `Synchronise now` for Synchronise all moodle courses to woocommerce product.

2. Click on `Synchronise` button.

To Setup products: Product > Course Name >
1. Click on `Edit` button.

2. Click the `Moowoodle` button on Product Description.

* For paid product, please do this:
3. Change all feilds like `cohort` , `group` , `course` according to your moodle settings. 
Ex: [moowoodle cohort="cohortid" group="groupid" course="courseid" class="moowoodle" target="_self" authtext="" activity="0"][/moowoodle]

* For free product, please do this:
3. Change all feilds like `cohort` , `group` , `course` according to your moodle settings and also write something in the shortcode that appears on the `view product` page. 
Ex: [moowoodle cohort="cohortid" group="groupid" course="courseid" class="moowoodle" target="_self" authtext="" activity="0"]Click to enrolled and Visit Course[/moowoodle]

Note: If your product is a PAID product then never ever write anything in the shortcode. If you want to add some description about your product write all the stuff below this `[moowoodle][/moowoodle]` shortcode, otherwise this shortcode will not work.

*Select woocommerce email type
Woocommerce > Settings > Emails > New Moodle Enrollment > Email type > HTML

Moodle site set up:
Create and setup webservice:
1. Administration > Site administration > Advanced features > Enable webservice > Save changes.

2. Administration > Site administration > Plugins > Manage protocols > Enable `REST protocol`.

3. Administration > Site administration > Plugins > External services > Add > Give a `Name` and chack `Enabled` > Add service > Add functions > Add the following functions to your webservice:
	i. core_user_create_users: Create users
	ii. core_user_get_users: Search for users matching the parameters
	iii. core_user_update_users: Update users
	iv. core_course_get_courses: Return course details
	v. core_course_get_categories: Return category details
	vi. enrol_manual_enrol_users: Manual enrol users

4. Administration > Site administration > Plugins > Manage tokens > Add > Select user (Admin) from users\' list > Select your service form services\' list > Save changes.

Disable password policy:
The password policy settings needed to be disabled since the user password will be generated in WordPress end and will not match the password policy of Moodle. The steps to disable password policy are given below.

1. Administration > Site administration > Security > Site policies > Uncheck `Password policy` > Do not forget to save changes.

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
NOTE: Woo-Moodle bridge plugin is a extention of WooCommerce, so the WooCommerce plugin must be installed and activated in your WordPress site for this plugin to work properly.

1. Download and install MooWoodle plugin using the built-in WordPress plugin installer.
If you download MooWoodle plugin manually, make sure it is uploaded to `/wp-content/plugins/moowoodle/`. Or follow the steps below:
Plugins > Add new > Upload plugin > Upload moowoodle.zip > Install Now.

2. Activate the plugin through the \'Plugins\' menu in WordPress.

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
= 2.0 =

= 1.3.0 =

= 1.2.2 =

= 1.2.1 =

= 1.2 =

= 1.1.0 =

= 1.0.0 =

