<?php
	
	$servername = "localhost";
	$username = "root";
	$password = "";

	// Create connection
	$conn = new mysqli($servername, $username, $password);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn->select_db("Crawler");
	
    $pageCrawlerResult = '';
	$pageCrawlerResult = [];
    $crawlerDepth = 2;

    function crawl_page($url, $depth = 5)
    {
        $hrefArray = [];
        static $seen = array();
        if (isset($seen[$url]) || $depth === 0) {
            return;
        }

        $seen[$url] = true;

        $dom = new DOMDocument('1.0');
        @$dom->loadHTMLFile($url);

        $anchors = $dom->getElementsByTagName('a');
		$content = $dom->documentElement;
        $hrefArray = [];

        foreach ($anchors as $element) {

			$finalLink = explode("#",$element->getAttribute('href'));
			$link = $finalLink[0];
            
			$protocol = "http://";
			$protocolS = "https://";
			
			$adres = substr($link,0,7);
			$adresS = substr($link,0,8);
			
			if ($adres != $protocol and $adresS != $protocolS) {
				$link = $url.$link;
			}
			$hrefArray[] = $link;
		}
        $hrefArray = array_unique($hrefArray);

        return $hrefArray;
    }
	
	if(!empty($_GET['url'])){
        $url = $_GET['url'];
    }


    if(isset($url)) {
        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            echo 'Not a valid html!!!';
        } 
		else {
            $pageCrawlerResult = crawl_page($url, $crawlerDepth);
            $pageContent = 'Page Content';
            $sql = "INSERT INTO SitesAwaiting (site) VALUES ('$url')";
            if (mysqli_query($conn, $sql)) 
            {
                echo "Query added successfully".'<br>';
            } 
            else 
            {
                echo "Error creating query " . mysqli_error($conn).'<br>';
            }
            foreach ($pageCrawlerResult as $link) 
            {
                $sql = "INSERT INTO SitesViewed (site, content) VALUES ('$url', '$link')";
                if (mysqli_query($conn, $sql)) 
                {
                    //echo "Query added successfully".'<br>';
                } 
                else 
                {
                    echo "Error creating query " . mysqli_error($conn).'<br>';
                }
            }
		}
    }

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Crawler</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <div class="header">Crawler</div>
  	<div class="search">
        <form action="" type="GET">
            <div class="search-container">
				<input type="text" class="search-input" name="url" value="<?php if(!empty($_GET['url'])){ echo $url; } ?>">
            </div>
            <div class="submit-container">
                <input class="submit" type="submit" value="Crawl!">
            </div>
        </form>
  	</div>
    <div class="result">
        <?php
            foreach ($pageCrawlerResult as $href) 
            {
              echo '<a href = "'.$href.'">'.$href.'</a>';
            }
        ?>
    </div>
  </body>
</html>