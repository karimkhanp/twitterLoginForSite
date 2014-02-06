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

	print "Name : ".$content->{'name'}; 
	echo "<br/>";
	$name = $content->{'name'}; 
	print "Screen name : ".$content->{'screen_name'};
	$screen_name = $content->{'screen_name'};
	echo "<br/>";
	print "User id : ".$content->{'id'}; 

	$img_link = $content->{'profile_image_url'};
	$id = $content->{'id'}; 

	echo "<br/>";
	print "Location : ".$content->{'location'}; 
	$location = $content->{'location'}; 
	echo "<br/>";
	$date = date("Ymd"); 

	$con = mysqli_connect('127.0.0.1', 'root', '', 'mysql');
	echo "<b>Latest 5 tweets:</b> <br/>";
	foreach ($tweets as $item)
	{
		echo $item->text;
		$tweet = $item->text;
		$insertQuery1 = "INSERT INTO user_tweets (`id`,`name`,`tweet`,`date`) VALUES ('".$id."','".$name."','".$tweet."','".$date."')";
		if (!mysqli_query($con,$insertQuery1))
		{
			//die('Error: ' . mysqli_error($con));
		}
	}
			

	if (mysqli_connect_errno())
	{
		//echo "Failed to connect to MySQL: " . mysqli_connect_error();
		return;
	}
	$insertQuery2 = "INSERT INTO user_record_twitter (`name`,`screen_name`,`id`,`location`) VALUES ('".$name."','".$screen_name."','".$id."','".$location."')";
	if (!mysqli_query($con,$insertQuery2))
	{
		//die('Error: ' . mysqli_error($con));
	}


?>
