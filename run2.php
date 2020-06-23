<?php
/* 
	vBlog to vBulletin Forum Threads Converter
	Created by Harry Taheem - https://StealthBay.com
	June 2020 - v1
	https://github.com/stealthbay/vblogsconversion
	run2.php
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


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

	// Check connection
	if ($conn->connect_error) 
	{die("Connection failed: " . $conn->connect_error);}

	//connection is working
	echo "Database Connection Established!" . "<br><br><br>";
	
	//query the blog for posts
	$sqlcomments = "SELECT userid, title, pagetext, username, threadid FROM blog_text";
	$resultcomment = $conn->query($sqlcomments);

			//go through each result
			if ($resultcomment->num_rows > 0) 
			{	
				// output data of each row
				while($row = $resultcomment->fetch_assoc()) 
				{ 		
	
					//list out the atrributes needed from the blog post and sanitize the data
					$userid = $row["userid"];					
					$title = filter_var($row["title"], FILTER_SANITIZE_STRING);
					$pagetext = filter_var($row["pagetext"], FILTER_SANITIZE_STRING);
					$uname = filter_var($row["username"], FILTER_SANITIZE_STRING);
					$threadid = $row["threadid"];
					
						if(empty($title))
						{

	
						echo "<table border=2><tr>" . "<td> " . $row["userid"]. "</td>" .  "<td> " . $row["title"]. "</td>" . "<td> " . $row["pagetext"]. "</td>" . "<td> " . $row["username"]. "</td></td></table><br>";
						
						echo "Threadid is:" . $threadid . "<br><br>";

							//create the first post
							$postsqlcomments = "INSERT INTO post (threadid, username, userid, title, pagetext, proxyip, chargecontent) VALUES (".$threadid.",'".$uname."',".$userid.",'".$title."','".$pagetext."','".$proxyip."','".$chargecontent."')";
							$postcommentsresult = $conn->query($postsqlcomments);												
															
																		
						}							
				
				
				} 
				
				
			} 
			//numrows
			
			//delete the blog threadid column
			$droptable = "ALTER TABLE blog_text DROP threadid"; 			
			if($conn->query($droptable)){
											echo "Temp table dropped successfully <br><br>";	
										}
										else{
											echo "Could not delete temp table<br><br>";
				
											}	
			
echo "Conversion Completed <br><br> If you have any other questions visit us at https://community.stealthbay.com<br><br>";

//close the database connection
$conn->close();

?>
