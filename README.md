# WebDevBot

A bot for the [Web Developer Italiani](https://www.facebook.com/groups/webdeveloperitaliani/) Facebook group.

## WTF is this?

Basically, this bot fetch the group feed every fifteen minutes to get the latest posts. If one or more posts doesn't contains hashtags at the start, this bot stores the post id and comments it with a warning message.

After X minutes (the value, in minutes, can be specified in TIME_SLOT in .env file), if the hashtags are still missing, the post is deleted.

## Install Notes

Before use, make sure you've created the `data.json` file with the following content:

    []

Thanks!

## How to Use It

Configure a cron job to execute the `./run` script every `env('TIME_SLOT')` minutes.
