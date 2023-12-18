<?php
if (isset($_GET['file'])) {
    $fileName = htmlspecialchars($_GET['file']);
    $filePath = "./" . $fileName; // Update the path accordingly

    // Display the content of the selected file
    if (file_exists($filePath)) {
        $fileContent = file_get_contents($filePath);

        // Display the title
        if (preg_match('/<title>(.*?)<\/title>/', $fileContent, $matches)) {
            $title = htmlspecialchars(trim($matches[1]));
            echo 
            "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>$title</title>
                <style>
                body {
                    font-family: 'Arial', sans-serif;
                    background-color: white;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                }
                
                h1 {
                    color: #5F8670;
                    text-align: center;
                }
                
                h2 {
                    color: black;
                    text-align: center;
                }

                p {
                    font-size: 16px;
                    text-align: justify;
                    line-height: 1.6;
                    max-width: 800px;
                    margin: 0 auto;
                }
                
                .file-content {
                    margin-top: 20px;
                    margin: 2rem;
                    padding: 2rem;
                    background-color: rgb(151, 196, 184, 0.3);
                    border-radius: 20px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
            
                .file-content p {
                    text-align: justify;
                    margin: 0; /* Remove default margin for <p> */
                }
                
                .back-button {
                    margin-top: 20px;
                    text-align: center;
                }
                
                .back-button a {
                    display: inline-block;
                    padding: 20px 40px;
                    background-color: rgb(151, 196, 184, 0.5);
                    color: #000;
                    text-decoration: none;
                    border-radius: 25px;
                }
                
                .back-button a:hover {
                    background-color: rgb(151, 196, 184, 0.8);
                }
                
                </style>
            </head>
            <body>
                
                <main>
                    <h1>$title</h1>
                    <div class='file-content'>
                        <p>$fileContent</p>
                    </div>
                    <div class='back-button'>
                        <a href='javascript:history.go(-1)'>Kembali</a>
                    </div>
                </main>
            </body>
            </html>
            ";
        } else {
            echo "<p class='error-message'>Title not found in the file content.</p>";
        }
    } else {
        echo "<p class='error-message'>File not found: $filePath</p>";
    }
} else {
    echo "<p class='error-message'>Invalid file parameter.</p>";
}
?>
