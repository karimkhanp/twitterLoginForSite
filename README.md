twitterLoginForSite
===================

Add twitter login button on your site and get user profile data into db

There are couple of files to do this in nice flow

1. config.php - Add you twitter auth keys on this page. Later we can access those value just by including this file

2. login.php - You site page. We will keep login button here


3. redirect.php - This page will start session and load library function. It creates connection using your twitter keys and redirect to twitter authentication system. Here user authenticates your Twitter apps and enters his twitter credentials
  
4. callback.php - After user enters correct parameters he is redirect to this page. If the oauth_token is old redirect to the connect page. If the user has been verified then the access tokens can be saved for future use. it will do it automatically. Here you can now get user profile info. But for easiness we will do it in next page index.php
  
  
5. index.php - Get user profile info and store it in db

