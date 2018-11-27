 <!DOCTYPE html>
<html lang="en">
    <head>
        <title>Test</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.2/css/all.css" integrity="sha384-/rXc/GQVaYpyDdyxK+ecHPVYJSN9bmVFBvjA/9eOB+pb3F2w2N6fc5qB9Ew5yIns" crossorigin="anonymous">
        <link rel="stylesheet" href="css/main.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="js/repository.js"></script>
        <script src="js/validation.js"></script>
        
    </head> 
    <body> 
        <button class="btn btn-primary row" id="addreviewbtn" data-toggle="modal" data-target="#makereview"
                onclick="showReviewInput('1235486548456', 7)">
                        <i class="far fa-comment"></i><i class="fas fa-edit"></i>
        </button>
       <?php include "pages/makeReview.php"; ?>


            
    </body> 
</html>