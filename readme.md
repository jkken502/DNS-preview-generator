Getting started:
create the settings.php using the settings-sample.php as a template:
1.) Add the host, such as hosts.justinkkennedy.com
2.) Then add your database credentials
3.) Import the "initial database setup.sql"
4.) IMPORTANT you must setup a wildcard subdomain
5.) OPTIONAL add an SSL certificate for this wildcard subdomain to be able to display websites with an ssl certificate.
6.) Optional configure and add a cron task to remove-old-entries-cron.php to automatically remove old website previews.
6.) Get started hosting your own DNS previews

Who this tool is for?
This was originally created for a team of website migrators to send a preview to clients, but can also be used for website developers working on a website that is not live.

What does this tool do?
This tool will create a temporary website preview of a website. You no longer have to update your host file to preview a website that is not live. You can create as many preview links as you want.