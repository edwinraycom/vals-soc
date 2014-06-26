vals_soc
========

VALS Drupal module

Install the following Drupal Module Dependencies...

[Admin Menus] (https://drupal.org/project/admin_menu)

[Date Module](https://drupal.org/project/date)

[SMTP Module](https://drupal.org/project/smtp)

[CKEditor Module](https://drupal.org/project/ckeditor)

Create a front page for the guests (node 1) and one for the logged in users (node 2).
Go to Structure->blocks and enable the Semester of Code block (move it to the spot 'content' in
the current theme).
Go to settings and enable it for node 2 ('node/2') only
Make this block available only for  authenticated users.

Copy the file initial.php to the root of the installation. Due to a bug in Drupal it needs the base url before it can 
do a bootstrap in ajax. The bootstrap from the index doesn't suffer from that. Probably because Drupal does not derive
the base url (based on assumptions about the position of index.php if it is already defined. Instead we derive it ourselves
from a location that is known before the bootstrap. All urls served pass either the (/vals)/index.php or
the (/vals)/sites/all/modules/vals_soc/actions