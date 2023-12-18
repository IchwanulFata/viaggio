<?php
if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $heapsize = isset($_GET['heapsize']) ? (int)$_GET['heapsize'] : 15;
    $method = isset($_GET['method']) ? $_GET['method'] : 'cRank';

    if ($method === 'cRank') {
        $command = "C:\\xampp\\htdocs\\search\\querydb.exe \"$query\" $heapsize 2>&1";
        exec($command, $output, $exitCode);
    } elseif ($method === 'swish-e') {
        $indexFile = "C:\\xampp\\htdocs\\search\\result.index";

        // Use exec to run swish-e search
        $command = "C:\SWISH-E\bin\swish-e.exe -f $indexFile -w \"$query\" -m $heapsize 2>&1";
        exec($command, $output, $exitCode);
        

    } else {
        // Handle other search methods as needed
        $command = ''; // Update this line accordingly
    }
    echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Search Engine</title>
            <link rel="stylesheet" type="text/css" href="css/result.css">
            <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
            <script>
                $(document).ready(function() {
                    $("#modify-form").submit(function(e) {
                        e.preventDefault();

                        var formData = $(this).serialize();
                        var url = $(this).attr("action");

                        // Reload the page with modified heap size
                        window.location.href = url + "?" + formData;
                    });
                });
            </script>
        </head>
        <body>
            <form id="modify-form" action="search.php" method="get">
            <div class="head">
                <img src="img/logo.png" alt="" class="logo">
                <div class="form">
                   <input class="query" type="text" id="query" name="query" value="' . $query . '" required>
                   <input class="heapsize" type="number" id="heapsize" name="heapsize" min="1" max="49" value="' . $heapsize . '">
                   <select class="method" id="method" name="method">
                       <option value="cRank" ' . ($method === 'cRank' ? 'selected' : '') . '>C</option>
                       <option value="swish-e" ' . ($method === 'swish-e' ? 'selected' : '') . '>Swish-e</option>
                   </select>
               </div>
               <div class="submit">
                   <input class="btn-submit" id="search-button" type="submit" value="Search">
               </div>
           </div>
       </form>';
    
   // Process swish-e results
    if ($method === 'swish-e') {
        $filePaths = [];

    // Extract additional information from swish-e output
    $searchInfo = [];
    foreach ($output as $line) {
        if (preg_match('/# Search words: (.+)/', $line, $matches)) {
            $searchInfo['Search words'] = $matches[1];
        } elseif (preg_match('/# Removed stopwords: (.+)/', $line, $matches)) {
            $searchInfo['Removed stopwords'] = $matches[1];
        } elseif (preg_match('/# Number of hits: (\d+)/', $line, $matches)) {
            $searchInfo['Number of hits'] = $matches[1];
        } elseif (preg_match('/# Search time: (.+)/', $line, $matches)) {
            $searchInfo['Search time'] = $matches[1];
        } elseif (preg_match('/# Run time: (.+)/', $line, $matches)) {
            $searchInfo['Run time'] = $matches[1];
        }
    }
    // Process swish-e results
    $filePaths = [];
    foreach ($output as $line) {
        if (preg_match('/^\d+\s+(.*?\.txt)\s+"(.*?)"/', $line, $matches)) {
            $filePaths[] = ['file' => $matches[1], 'title' => $matches[2]];
        }
    }
                
    // Display additional search information
    echo '<div class="search-info">';
    foreach ($searchInfo as $key => $value) {
        echo "<p>$key: $value</p>";
    }
    echo '</div>';

       // Display document information
    echo '<ul>';
        foreach ($filePaths as $fileInfo) {
            echo '<li>
                    <div class="title">
                        <a class="doc-title" href="view_file.php?file=' . urlencode($fileInfo['file']) . '">' . $fileInfo['title'] . '</a>
                    </div>
                    <hr>
                </li>';
        }
    echo '</ul>';
    } else {
        // Display the current format for other search methods
        echo '<pre style="margin: 0; border: 0; padding: 0; white-space: pre-wrap; word-wrap: break-word; background-color: #f9f9f9; padding: 5px; border-radius: 5px;">' . implode("\n", $output) . '</pre>';
    }

    echo '<script>
              function newSearch() {
                  window.location.href = "index.html";
              }
          </script>
      </body>
      </html>';
    exit;
}
?>
