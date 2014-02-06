twitterLoginForSite
===================

Add twitter login button on your site and get user profile data into db

There are couple of files to do this in nice flow

1. config.php - Add you twitter auth keys on this page. Later we can access those value just by including this file

  <?php
  define('CONSUMER_KEY', 'Nw5cni8ag');
  define('CONSUMER_SECRET', 'NeS730IqeyxNLKiZA0dk');
  define('OAUTH_CALLBACK', 'http://www.yoursite.com/test/twitter/callback.php');
  ?>

2. login.php - You site page. We will keep login button here

  <a href="./redirect.php"><input type = "button" id = "loginTwitter" class = "btn btn-primary"  value = "Login | Twitter "/></a>


3. redirect.php - This page will start session and load library function. It creates connection using your twitter keys and redirect to twitter authentication system. Here user authenticates your Twitter apps and enters his twitter credentials
  
  <?php
  
  /* Start session and load library. */
  session_start();
  require_once('twitteroauth/twitteroauth.php');
  require_once('config.php');
  
  /* Build TwitterOAuth object with client credentials. */
  $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
   
  /* Get temporary credentials. */
  $request_token = $connection->getRequestToken(OAUTH_CALLBACK);
  
  /* Save temporary credentials to session. */
  $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
  $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
   
  /* If last connection failed don't display authorization link. */
  switch ($connection->http_code) {
    case 200:
      /* Build authorize URL and redirect user to Twitter. */
      $url = $connection->getAuthorizeURL($token);
      header('Location: ' . $url); 
      break;
    default:
      /* Show notification if something went wrong. */
      echo 'Could not connect to Twitter. Refresh the page or try again later.';
  }

4. callback.php - After user enters correct parameters he is redirect to this page. If the oauth_token is old redirect to the connect page. If the user has been verified then the access tokens can be saved for future use. it will do it automatically. Here you can now get user profile info. But for easiness we will do it in next page index.php
  
  <?php
  /**
   * @file
   * Take the user when they return from Twitter. Get access tokens.
   * Verify credentials and redirect to based on response from Twitter.
   */
  
  /* Start session and load lib */
  session_start();
  require_once('twitteroauth/twitteroauth.php');
  require_once('config.php');
  
  /* If the oauth_token is old redirect to the connect page. */
  if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
    $_SESSION['oauth_status'] = 'oldtoken';
    header('Location: ./clearsessions.php');
  }
  
  /* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
  $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
  
  /* Request access tokens from twitter */
  $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
  
  /* Save the access tokens. Normally these would be saved in a database for future use. */
  $_SESSION['access_token'] = $access_token;
  
  /* Remove no longer needed request tokens */
  unset($_SESSION['oauth_token']);
  unset($_SESSION['oauth_token_secret']);
  
  /* If HTTP response is 200 continue otherwise send to connect page to retry */
  if (200 == $connection->http_code) {
    /* The user has been verified and the access tokens can be saved for future use */
    $_SESSION['status'] = 'verified';
    header('Location: ./index.php');
  } else {
    /* Save HTTP status for error dialog on connnect page.*/
    header('Location: ./clearsessions.php');
  }

5. index.php - Get user profile info and store it in db


<?php

	session_start();
	require_once('twitteroauth/twitteroauth.php');
	require_once('config.php');

	if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
	    header('Location: ./clearsessions.php');
	}
	$access_token = $_SESSION['access_token'];
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
	$content = $connection->get('account/verify_credentials');
	$twitteruser = $content->{'screen_name'};
	$notweets = 5;
	$tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$twitteruser."&count=".$notweets);

	foreach ($tweets as $item)
	{
//		echo $item->text;
		$tweet = $item->text;
		$insertQuery1 = "INSERT INTO user_tweets (`id`,`name`,`tweet`,`date`) VALUES ('".$id."','".$name."','".$tweet."','".$date."')";
		if (!mysqli_query($con,$insertQuery1))
		{
			//die('Error: ' . mysqli_error($con));
		}
	}
		$id = $content->{'id'}; 
		$name = $content->{'name'}; 
		?>
