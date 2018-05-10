Done
----

- Data model

- "Notes" page

- Styling (except "Author" page)

- Invalidate post node cache when adjacent post is saved

- Homepage route (redirects to latest post)

- Sidebar calendar


To do
-----

- Meta tags

  - Facebook/Twitter-specific tags

- Auto-publish on cron

  - Post to Facebook
    <br>https://developers.facebook.com/docs/pages/publishing
  - Config page with time of day to publish
    <br>https://drupal.stackexchange.com/a/245887/3904
    
- Author page

  - Photo
  - Bio
  - Design
  
- Content import

  - Prepare CSV (texts, image URLs)
  - Feed import (maybe just a very custom hook_update script to read from the CSV)
    <br>https://www.drupal.org/project/migrate_plus
  
- Search

  - Algolia setup
  - Frontend implementation

- JS-mode

  - Links to posts work using AJAX, calendar is updated by JS
  - Pre-load adjacent posts (?)

- 404 / 403 pages

- Sidebar calendar: cache output by month (invalidate on post node)

- Post body as alt text for the image (accessibility issue)