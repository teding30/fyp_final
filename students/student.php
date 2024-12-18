<?php
session_start();
include("../include/config.php");
include("../include/header.php");


// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal</title>
    <link rel="stylesheet" href="styles.css">
    <script src="../script.js"></script>
    
</head>
<body>
    

<div class="foreground">
         <p>Prepare for Exit Exam</p>

        </div>
        <div class="boxes">
    <div class="box">
      <h2>Box 1</h2>
      <p>This is the description for Box 1.</p>
    </div>
    <div class="box">
      <h2>Box 2</h2>
      <p>This is the description for Box 2.</p>
    </div>
    <div class="box">
      <h2>Box 3</h2>
      <p>This is the description for Box 3.</p>
    </div>
    <div class="box">
      <h2>Box 4</h2>
      <p>This is the description for Box 4.</p>
    </div>
  </div>

  <div class="about">
         <p>About exit exam</p>
         <img class="img" src="../img/img 2.jpeg" alt="">
         <p class="aboutdesc"> 
Exit exams are significant for students. They help solidify understanding, improve problem-solving skills, and boost confidence. By preparing well, 
students can enhance their performance and set a strong foundation for future studies</p>  

<ul >
  <li>Item 1</li>
  <li>Item 2</li>
  <li>Item 3</li><br>
  

  <li>Item 1</li>
  <li>Item 2</li>
  <li>Item 3</li>

        </div>

        <div class="instructors">
        <p>Instructors</p>
        <div class="boxes">
    <div class="box">
      
    <img class="instruct" src="../img/img 1.jpeg" alt="">
      <p>Mr. Abebe Kebede</p>
    </div>
    <div class="box">
    <img class="instruct" src="../img/img 1.jpeg" alt="">
      <p>Mr. Abebe Kebede</p>
    </div>
    <div class="box">
    <img class="instruct" src="../img/img 1.jpeg" alt="">
      <p>Mr. Abebe Kebede</p>
    </div>
    <div class="box">
    <img class="instruct" src="../img/img 1.jpeg" alt="">
      <p>Mr. Abebe Kebede</p>
    </div>
  </div>
  </div>

  <div class="footer">
  <a href="" > <img class="logo" src="../img/logo.png" alt=""> </a> 
   <ul class="social-icons">
  <li><a href="https://www.facebook.com"><img src="../img/fb.png" alt="Facebook"></a></li>
  <li><a href="https://telegram.com"><img src="../img/tg.png" alt="Telegram"></a></li>
  <li><a href="https://www.instagram.com"><img src="../img/ig.png" alt="Instagram"></a></li>
  <li><a href="https://www.linkedin.com"><img src="../img/link.png" alt="LinkedIn"></a></li>
</ul><br>
 <p>@2024 | copy rights reseved</p>
  </div>
       
        
    
</body>
</html>

