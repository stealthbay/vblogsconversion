<?php
/* 
	vBlog to vBulletin Forum Threads Converter
	Created by Harry Taheem - https://StealthBay.com
	June 2020 - v1
	https://github.com/stealthbay/vblogsconversion
	run.php
*/
 
 //connect to the forum database
$servername = "localhost";
$username = "admin";
$password = "mypassword";
$dbname = "forum";

//extra variables do not edit these
$proxyip = '0.0.0.0';
$chargecontent = '';
$threadid = 0;
$blogid = 0;

//set your forum ID here where your blog posts will be moved too
$forumid = 222;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

	// Check connection
	if ($conn->connect_error) 
	{die("Connection failed: " . $conn->connect_error);}

	//connection is working
	echo "Database Connection Established!" . "<br><br><br>";
	
	//create a new field in  the blog table
	echo "Now Creating a new field in blog for conversion<br><br>";
	
	//creat the new table
	$newtable = "ALTER TABLE blog_text ADD COLUMN threadid INT NOT NULL";
	$addtable = $conn->query($newtable);		

	//query the blog for posts
	$sql = "SELECT userid, title, pagetext, username, blogid FROM blog_text";
	$result = $conn->query($sql);

			//go through each result
			if ($result->num_rows > 0) 
			{	
				// output data of each row
				while($row = $result->fetch_assoc()) 
				{ 		
	
					//list out the atrributes needed from the blog post and sanitize the data
					$userid = $row["userid"];	
					$title = filter_var($row["title"], FILTER_SANITIZE_SPECIAL_CHARS);
					$pagetext = filter_var($row["pagetext"], FILTER_SANITIZE_SPECIAL_CHARS);
					$uname = filter_var($row["username"], FILTER_SANITIZE_SPECIAL_CHARS);			
					$blogid = $row["blogid"];
					
						if(!empty($title))
						{

	
						echo "<table border=2><tr>" . "<td> " . $userid. "</td>" .  "<td> " . $title. "</td>" . "<td> " . $pagetext. "</td>" . "<td> " . $uname. "</td></td></table><br>";


		
						//create a new thread
						$sqlthread = "INSERT INTO thread (title, forumid, postuserid, postusername, lastposter) VALUES ('".$title."',".$forumid.",".$userid.",'".$uname."','".$uname."')";
							if($conn->query($sqlthread))
							{
							echo "Thread converted sucessfully<br><br>";


							//find the last threadid that was posted and store it
							$threadidsql = "SELECT threadid FROM thread ORDER BY threadid DESC LIMIT 1";
							$thread = $conn->query($threadidsql);

									while($threadrow = $thread->fetch_assoc()) {

									$threadid = $threadrow["threadid"];
									
									echo "Here is the threadID:" . $threadid . "<br><br>";

									}

									//create the first post
									$postsql = "INSERT INTO post (threadid, username, userid, title, pagetext, proxyip, chargecontent) VALUES (".$threadid.",'".$uname."',".$userid.",'".$title."','".$pagetext."','".$proxyip."','".$chargecontent."')";
									$postresult = $conn->query($postsql);
																																			
									//insert postid in the blog postid table
									$sqlpost = "UPDATE blog_text SET threadid = ".$threadid." WHERE blogid = ".$blogid."";
										if($conn->query($sqlpost)){
										echo "Post ID added into blog sucessfully<br><br>";	
										}
										else{
											echo "postid insertion failed<br><br>";
				
											}	
					
							}
							//sqlthread check
							else
							{
								echo $conn->error;
							}	
				
						}
				//if empty
				
				} 
				//while loop
				
			} 
			//numrows
			
echo "Conversion Completed<br><br> Please run run2.php to convert over all the comments from the blog posts <br><br>";

//close the database connection
$conn->close();

?>
