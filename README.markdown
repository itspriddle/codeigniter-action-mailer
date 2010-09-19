# CodeIgniter ActionMailer

CodeIgniter's Email library is pretty good... but it's no
[ActionMailer](http://api.rubyonrails.org/classes/ActionMailer/Base.html).


## Installation

First, copy `libraries/ActionMailer.php` to your app's libraries directory.
Copy `libraries/MY_Loader.php` to your app's libraries directory too. If you
already have one, copy the `mailer()` function.

Next, create the directory `APPPATH/mailers`, (parallel to `libraries/` and
`views/` in your app). This directory will be home to your mailer classes.


## Usage

ActionMailer aims to DRY sending email with CodeIgniter's email class. A class
method sets up the email. Mail contents are kept in views and autoloaded by
class/method name. Email is delivered with one simple method call.


### Example Mailer

You must use a lower case underscored filename, and a CamelCase class name:

    // APPROOT/mailers/user_mailer.php

    class UserMailer extends ActionMailer {
      function __construct()
      {
        parent::__construct();
        $this->from = 'noreply@example.com';
      }

      function account_created($user)
      {
        $this->to           = $user->email;
        $this->subject      = 'Account Created!';
        $this->body['url']  = "http://example.com";
        $this->body['name'] = $user->name;
      }

      // Send an email with attachments
      function new_photo($user, $path_to_image)
      {
        $this->to           = $user->email;
        $this->subject      = 'New Photo';
        $this->attachments  = array($path_to_image);
      }
    }


### Example Mailer View

View files are automatically rendered. They must be stored in a directory
corresponding to your mailer's filename (eg: `user_mailer.php` =>
`user_mailer/`). The view loaded for each message must correspond to that
mailer's method (eg: `account_created()` => `account_created.php`). Finally,
views are loaded with data from the mailer's `$body` (`url` and `name` above).

    // APPROOT/views/user_mailer/account_created.php

    Hello <?php echo $name; ?>,

    Thanks for signing up. You can login at:

    <?php echo $url; ?>


### Sending Email

Load `UserMailer` with `$this->load->mailer('user_mailer')`. To send an email,
prefix the mailer's method with `deliver_`. Using the mailer above, deliver
the account created message with (this obviously assumes you've already
set `$user` in your own code):

    $this->user_mailer->deliver_account_created($user);


## Requirements

Sorry kids, PHP5 only. You should be ashamed of yourself if you're using PHP4
anyway.


## License

MIT, see LICENSE
