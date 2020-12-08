<?php
echo "Name : Giang Duong. Student ID: 014533857. Midterm 1 CS 174 - Fall 2020 <br> \n<br>";
echo <<<_END
<html><head><title>Upload a text file and return greatest product of four adjacent numbers in 4 posible direction.</title></head><body>
<form action='midterm1.php' method= "post"  enctype='multipart/form-data'>
 Select text file (.txt) need to upload: 
 <input type='file' name='filename' id='filename' size ='10'>
 <input type ='submit' value='Submit'>
</form>
_END;
echo"</body></html>";
if ($_FILES['filename']['size'][0] == 0) {
    $name = $_FILES['filename']['name'];
    #this will leave only character A-Z, a-z, 0-9 and period in the string $name ans strips out everything else
    $name = preg_replace("^A-Za-z0-9.", "", $name);
    # case-sensitive, this will changes all uppercase characters to lowercase
    $name = strtolower($name);
    # sanitizer the user input file name
    $name = santizerString($name);
    # this will check the file type that is text file txt or not
    switch ($_FILES['filename']['type']){
        case 'text/txt':$ext ='txt'; break;
        case 'text/plain':$ext='txt'; break;
        default:        $ext = '';break;
    }
    if($ext) { // if $ext is not empty
        $n = 'filetext.txt';
        move_uploaded_file($_FILES['filename']['tmp_name'], $n);
        echo "Upload text file on server $name as '$n': \n <br>";
        echo "<txt src='$n'>";
        $name2 = $_FILES['filename']['name'];
        echo ("Now open file : '$name2'. \n <br>");

        $filehandle = fopen("$n",'r+') or die ("File does not exist! \n<br>");
        $line ="";
        # read whole file, remove newline, and concatenate all into 1 line
        while(!feof($filehandle)){
            $line2 = fgetc($filehandle);
            $line2 = str_replace(array("\n","\r"," "), "", $line2);
            $line = $line.$line2;
        }
        # discard non-numberic characters
        $line= discard($line);
        # checkfile function to check amount of number characters
        $line = checkfile($line);
        fclose($filehandle);
        # convert the line into 20x20 2D array
        $matrix = format20x20($line);
        # return the greatest product of four adjacent numbers in 4 possible direction
        $numberinEachLine = 20;
        Solve($matrix, $numberinEachLine);
        printarray($matrix);
        tester();
    }
    else echo "'$name' is not an accepted text file.\n<br>";
}
else echo "No text file has been uploaded";
//echo"</body></html>";
# check if file contains only number or not
# aslo check the amount of numbers in the file correct 400 or not
function checkfile($line){
    # if string does not have anything
    if (strlen($line)== 0) die("File has nothing\n<br>");
    # if string does not have enough number
    elseif (strlen($line) < 400 ) die("File does not have enough numbers.\n <br>");
    # if string have more numbers than 400
    elseif(strlen($line) >400)
    {
        echo "File has more than 400 character but my program will only take 
        the first 400 numberic character.\n<br><br>";
        $line = substr($line, 0, 400);
    }
    else return $line;
}
# discard character except numberic and let user knows that file is not format correctly
function discard($line){
    $numcharacterBefore = strlen($line);
    $line = str_replace('/\D/', "", $line);
    $numcharacterAfter = strlen($line);
    if ($numcharacterBefore === $numcharacterAfter) return $line;
    else {
        die ("File is not format correctly. There non-numberic character.\n<br>");
    }
}

# return the format file 20x20
function format20x20($line){
    $matrix = array();
    $matrix2 = array();
    $matrix2 = array_pad($matrix2, 20, 0);
    $matrix = array_pad($matrix, 20, $matrix2);
    $count = 0;
    for($i = 0; $i < 20; $i++ ){
        for ($j = 0; $j <20; $j++){
            $matrix[$i][$j] = isNumber($line[$count]);
            $count++;
        }
    }
    return $matrix;
}
# check if the chracter is numberic or not
function isNumber($charcter){
    if(is_numeric($charcter)){
        return $charcter;
    }
    else die("There character(s) is/are not numberic.\n<br>");
}
# return the greatest product of four adjacent numbers in 4 possible direction
function Solve($matrix,$numberinEachLine){
    # amount of numbers in production
    $numbersInProduct = 4;
    $fournumber = array(); // stores the numbers will give the greatest product value
    $fournumber = array_pad($fournumber, 4, 0); // init the array
    $product = 0;   // init the product value
    for($col = 0; $col < $numberinEachLine; $col++) {
        for ($row = 0; $row < $numberinEachLine; $row++) {
            # check vertically
            if ($row < $numberinEachLine - $numbersInProduct) {
                $temp = $matrix[$col][$row];    // the first number
                # array temp for 4 numbers of greatest value
                $fournumbertemp = array(); // init the array temp
                $fournumbertemp = array_pad($fournumbertemp, 4, 0);
                # the first array temp value
                $fournumbertemp[0]= $matrix[$col][$row];
                for ($i = 1; $i < $numbersInProduct; $i++) {
                    $fournumbertemp[$i] = $matrix[$col][$row + $i];
                    $temp *= $matrix[$col][$row + $i];
                }
                if ($temp > $product){$fournumber= $fournumbertemp;}
                $product = max($product, $temp);

            }
            # check horizontally
            if ($col < $numberinEachLine - $numbersInProduct) {
                $temp = $matrix[$col][$row];
                # array temp for 4 numbers of greatest value
                $fournumbertemp = array(); // init the array temp
                $fournumbertemp = array_pad($fournumbertemp, 4, 0);
                # the first array temp value
                $fournumbertemp[0]= $matrix[$col][$row];
                for ($i = 1; $i < $numbersInProduct; $i++) {
                    $fournumbertemp[$i] = $matrix[$col+$i][$row];
                    $temp *= $matrix[$col+$i][$row];
                }
                if ($temp > $product){$fournumber= $fournumbertemp;}
                $product = max($product, $temp);
            }
            # check diagonally upwards/ forwards
            if(($col < $numberinEachLine - $numbersInProduct) && ($row >= $numbersInProduct)){
                $temp = $matrix[$col][$row];
                # array temp for 4 numbers of greatest value
                $fournumbertemp = array(); // init the array temp
                $fournumbertemp = array_pad($fournumbertemp, 4, 0);
                # the first array temp value
                $fournumbertemp[0]= $matrix[$col][$row];
                for ($i = 1; $i < $numbersInProduct; $i++) {
                    $fournumbertemp[$i] = $matrix[$col+$i][$row-$i];
                    $temp *= $matrix[$col+$i][$row-$i];
                }
                if ($temp > $product){$fournumber= $fournumbertemp;}
                $product = max($product, $temp);
            }
            # check diagonally downward/ forwards
            if(($col < $numberinEachLine - $numbersInProduct) && ($row < $numberinEachLine -$numbersInProduct)){
                $temp = $matrix[$col][$row];
                # array temp for 4 numbers of greatest value
                $fournumbertemp = array(); // init the array temp
                $fournumbertemp = array_pad($fournumbertemp, 4, 0);
                # the first array temp value
                $fournumbertemp[0]= $matrix[$col][$row];
                for ($i = 1; $i < $numbersInProduct; $i++) {
                    $fournumbertemp[$i] = $matrix[$col+$i][$row+$i];
                    $temp *= $matrix[$col+$i][$row+$i];
                }
                if ($temp > $product){$fournumber= $fournumbertemp;}
                $product = max($product, $temp);
            }
        }
    }
    echo (  "The greatest product is :'$product' which from 4 numbers : 
    '$fournumber[0]', '$fournumber[1]','$fournumber[2]','$fournumber[3]'.\n <br>");
    return $product;
}
# add a tester for function to check the behavior of PHP function
function tester(){
    echo ("<br> This is function to test 4 different possible greatest product. <br>");
    # init the matrix 20x20 with all zero numbers
    $matrixtester = array();
    $matrix2 = array();
    $matrix2 = array_pad($matrix2, 20, 0);
    $matrixtester = array_pad($matrixtester, 20, $matrix2);
    # have only 4 number in the row 12 has value 1, 2, 3,4 which has product = 24
    $matrixtester[12][1] = 1;
    $matrixtester[12][2] = 2;
    $matrixtester[12][3] = 3;
    $matrixtester[12][4] = 4;

    printarray($matrixtester);
    $numberinEachLine = 20;
    # check if function working correctly
    echo ("Test 1: Test horizontal: \n<br>");
    $product = Solve($matrixtester, $numberinEachLine);
    $resultsample = 24;
    if ($product === $resultsample){
        echo "Tester 1 is pass.\n<br><br>";
    }
    else echo "Tester 1 is fail.\n<br><br>";
    # test 2:
    // clear the first matrix
    $matrixtester[12][1] = 0;
    $matrixtester[12][2] = 0;
    $matrixtester[12][3] = 0;
    $matrixtester[12][4] = 0;
    // assign a new values check vertically
    echo ("Test 2: Test vertical: \n<br>");
    $matrixtester[1][1] = 4;
    $matrixtester[2][1] = 5;
    $matrixtester[3][1] = 6;
    $matrixtester[4][1] = 7;
    printarray($matrixtester);
    $product = Solve($matrixtester, $numberinEachLine);
    $resultsample = 840;
    if ($product === $resultsample){
        echo "Tester 2 is pass.\n<br><br>";
    }
    else echo "Tester 2 is fail.\n<br><br>";
    // assign a new values check diagonal
    echo ("Test 3: Test diagonal forwards: \n<br>");
    // clear the second matrix
    $matrixtester[1][1] = 0;
    $matrixtester[2][1] = 0;
    $matrixtester[3][1] = 0;
    $matrixtester[4][1] = 0;
    // assign a new values check vertically
    $matrixtester[1][1] = 2;
    $matrixtester[2][2] = 4;
    $matrixtester[3][3] = 6;
    $matrixtester[4][4] = 8;
    printarray($matrixtester);
    $product = Solve($matrixtester, $numberinEachLine);
    $resultsample = 384;
    if ($product == $resultsample){
        echo "Tester 3 is pass.\n <br><br>";
    }
    else echo "Tester 3 is fail.\n <br><br>";
    // clear the third matrix
    $matrixtester[1][1] = 0;
    $matrixtester[2][2] = 0;
    $matrixtester[3][3] = 0;
    $matrixtester[4][4] = 0;
    echo ("Test 4: Test diagonal backward: \n <br>");
    // assign a new values check diagonal
    $matrixtester[4][1] = 3;
    $matrixtester[3][2] = 5;
    $matrixtester[2][3] = 7;
    $matrixtester[1][4] = 9;
    printarray($matrixtester);
    $product = Solve($matrixtester, $numberinEachLine);
    $resultsample = 945;
    if ($product == $resultsample){
        echo "Tester 4 is pass.\n<br><br>";
    }
    else echo "Tester 4 is fail.\n<br><br>";
}
# print array
function printarray($matrix){
    echo ("The matrix 20x20: <br>");
    for ($i = 0; $i < 20; $i++){
        for($j = 0; $j <20; $j++){
            echo ($matrix[$i][$j]);
        }
        echo ("\n<br>");
    }
}

# santizer the output
function santizerString($string){
    return htmlentities(fix_string($string));
}
function fix_string($string){
    if (get_magic_quotes_gpc()) $string = stripslashes($string);
    return $string;
}
?>