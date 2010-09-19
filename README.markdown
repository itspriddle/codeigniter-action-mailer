CodeIgniter ActionMailer
========================
CodeIgniter's Email library is pretty good...
but it's no [ActionMailer](http://api.rubyonrails.org/classes/ActionMailer/Base.html).

Usage
-----
Drop `ActionMailer.php` in your `libraries/` directory. Create a new mailer and
extend ActionMailer. See `ApplicationMailer.php` for an example mailer.

To send an email, call `ApplicationMailer->deliver_account_notification()`

Defining Messages
-----------------
Each message in a mailer should be it's own method. This method should define
`$this->to`, `$this->from`, and `$this->subject`. If you
need attachments, assign them as an array to `$this->attachments`.

The message content will be autoloaded from `views/[mailer classname]/methodname`.

The `$this->body` array will be loaded as view data in your message file.

Messages are delivered by calling `ApplicationMailer->deliver_MESSAGE_METHOD_NAME()`
(replace *MESSAGE_METHOD_NAME* - See Sending Messages below).

Sending Messages
----------------
To deliver a message, call the message method from your Mailer with a
prefix of **deliver_**.

For example: assume you've loaded your mailer to `$this->mailer` and want to send
the message defined in `ApplicationMailer->my_message()`. From your application, simply
call `$this->mailer->deliver_my_message()`.

Explanation for non Rails People
--------------------------------
In Rails, each message that is emailed is defined as a method in a mailer. For example,
you may have `Mailer.account_created`.  This method should handle setting the to address,
from address, subject, and email body.

To actually send a message, you would call `Mailer.deliver_account_created` (notice
the **deliver** prefix).

This PHP version behaves similarly. You can do things like:

    $user = $this->users->get('josh');
    $this->mailer->deliver_account_created($user);

Requirements
------------
Sorry kids, PHP5 only. You should be ashamed of yourself if you're using PHP4 anyway.
