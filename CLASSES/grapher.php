<?PHP
include_once("../CLASSES/headers.php");
use MathOperations as np; // simulating numpy from python 
use TestOperations as Test; // For testing 
class Grapher {
    private $colors;
    
    public function __construct() {
        $this->colors = [];
    }
    
    public function addColor($color) {
        $this->colors[] = $color;
    }
    
    public function createImage($x, $y, $filename, $width = 800, $height = 600) {
    $minX = min(array_column($x, 0));
    $minY = min(array_column($x, 1));

    $xRange = max(array_column($x, 0)) - $minX;
    $yRange = max(array_column($x, 1)) - $minY;

    $imageWidth = $width;
    $imageHeight = $height;

    // Calculate the scaling factors
    $xScale = $imageWidth / $xRange;
    $yScale = $imageHeight / $yRange;

    $image = imagecreatetruecolor($imageWidth, $imageHeight);

    $background = imagecolorallocate($image, 255, 255, 255); // Set white background
    imagefill($image, 0, 0, $background);

    $uniqueClasses = array_unique($y);

    $pixelSize = 10; // Adjust this value to increase or decrease the pixel size

    foreach ($x as $i => $xCoord) {
        $yCoord = $y[$i];

        $colorIndex = array_search($yCoord, $uniqueClasses);

        $yCoord = $y[$i];
           	
        $color = $this->colors[$yCoord];
            
        //$color = $this->colors[$colorIndex];

        $pointColor = imagecolorallocate($image, $color[0], $color[1], $color[2]);

        // Scale and shift the coordinates
        $scaledX = ($xCoord[0] - $minX) * $xScale;
        $scaledY = ($xCoord[1] - $minY) * $yScale;

        // Adjust the coordinates based on the pixel size
        $x1 = $scaledX - $pixelSize / 2;
        $y1 = $scaledY - $pixelSize / 2;
        $x2 = $x1 + $pixelSize;
        $y2 = $y1 + $pixelSize;

        imagefilledrectangle($image, $x1, $y1, $x2, $y2, $pointColor);
    }

    imagepng($image, $filename);
    imagedestroy($image);

    echo "Image created successfully: $filename";
} 


public function drawLineGraph($xValues, $yValues, $imageWidth, $imageHeight, $outputPath, $label = '') {
    // Create a blank image

    if ($xValues==null) {
        $this->drawLine($yValues, $imageWidth, $imageHeight, $outputPath, $label);
        return ;
    }

    $image = imagecreatetruecolor($imageWidth, $imageHeight);
    
    // Define colors
    $backgroundColor = imagecolorallocate($image, 255, 255, 255);
    $lineColor = imagecolorallocate($image, 0, 0, 0);
    $pointColor = imagecolorallocate($image, 255, 0, 0);
    $labelColor = imagecolorallocate($image, 0, 0, 255);
    
    // Fill the background
    imagefilledrectangle($image, 0, 0, $imageWidth, $imageHeight, $backgroundColor);
    
    // Determine the maximum and minimum values of x and y
    $minX = min($xValues);
    $maxX = max($xValues);
    $minY = min($yValues);
    $maxY = max($yValues);
    
    // Calculate the scaling factors for x and y coordinates
    $xScale = $imageWidth / ($maxX - $minX);
    $yScale = $imageHeight / ($maxY - $minY);
    
    // Draw the x and y axes
    imageline($image, 0, $imageHeight - 1, $imageWidth - 1, $imageHeight - 1, $lineColor);  // x-axis
    imageline($image, 0, 0, 0, $imageHeight - 1, $lineColor);  // y-axis
    
    // Draw the line graph
    $numPoints = count($xValues);
    for ($i = 1; $i < $numPoints; $i++) {
        $x1 = ($xValues[$i - 1] - $minX) * $xScale;
        $y1 = $imageHeight - (($yValues[$i - 1] - $minY) * $yScale);
        $x2 = ($xValues[$i] - $minX) * $xScale;
        $y2 = $imageHeight - (($yValues[$i] - $minY) * $yScale);
        
        imageline($image, $x1, $y1, $x2, $y2, $lineColor);
        imagefilledellipse($image, $x1, $y1, 4, 4, $pointColor);  // Optional: Draw points at each data point
    }
    
    // Add label if provided
    if (!empty($label)) {
        $labelX = 10;
        $labelY = 20;
        imagestring($image, 5, $labelX, $labelY, $label, $labelColor);
    }
    
    // Save the image to a file
    imagepng($image, $outputPath);
    
    // Free up memory by destroying the image resource
    imagedestroy($image);
}


public function drawLine($yValues, $imageWidth, $imageHeight, $outputPath, $label = '') {
    // Generate xValues as a sequence from 1 to the number of yValues
    $numPoints = count($yValues);
    $xValues = range(1, $numPoints);
    
    // Create a blank image
    $image = imagecreatetruecolor($imageWidth, $imageHeight);
    
    // Define colors
    $backgroundColor = imagecolorallocate($image, 255, 255, 255);
    $lineColor = imagecolorallocate($image, 0, 0, 0);
    $pointColor = imagecolorallocate($image, 255, 0, 0);
    $labelColor = imagecolorallocate($image, 0, 0, 255);
    
    // Fill the background
    imagefilledrectangle($image, 0, 0, $imageWidth, $imageHeight, $backgroundColor);
    
    // Determine the maximum and minimum values of x and y
    $minX = min($xValues);
    $maxX = max($xValues);
    $minY = min($yValues);
    $maxY = max($yValues);
    
    // Calculate the scaling factors for x and y coordinates
    $xScale = $imageWidth / ($maxX - $minX);
    $yScale = $imageHeight / ($maxY - $minY);
    
    // Draw the x and y axes
    imageline($image, 0, $imageHeight - 1, $imageWidth - 1, $imageHeight - 1, $lineColor);  // x-axis
    imageline($image, 0, 0, 0, $imageHeight - 1, $lineColor);  // y-axis
    
    // Draw the line graph
    for ($i = 1; $i < $numPoints; $i++) {
        $x1 = ($xValues[$i - 1] - $minX) * $xScale;
        $y1 = $imageHeight - (($yValues[$i - 1] - $minY) * $yScale);
        $x2 = ($xValues[$i] - $minX) * $xScale;
        $y2 = $imageHeight - (($yValues[$i] - $minY) * $yScale);
        
        imageline($image, $x1, $y1, $x2, $y2, $lineColor);
        imagefilledellipse($image, $x1, $y1, 4, 4, $pointColor);  // Optional: Draw points at each data point
    }
    
    // Add label if provided
    if (!empty($label)) {
        $labelX = 10;
        $labelY = 20;
        imagestring($image, 5, $labelX, $labelY, $label, $labelColor);
    }
    
    // Save the image to a file
    imagepng($image, $outputPath);
    
    // Free up memory by destroying the image resource
    imagedestroy($image);
}

public function combineGraphs($graphs, $outputPath) {
    // Determine the total width and maximum height of the graphs
    $totalWidth = 0;
    $maxHeight = 0;
    
    foreach ($graphs as $graph) {
        $graphWidth = imagesx($graph);
        $graphHeight = imagesy($graph);
        
        $totalWidth += $graphWidth;
        $maxHeight = max($maxHeight, $graphHeight);
    }
    
    // Create a blank combined image
    $combinedImage = imagecreatetruecolor($totalWidth, $maxHeight);
    
    // Set the background color
    $backgroundColor = imagecolorallocate($combinedImage, 255, 255, 255);
    imagefill($combinedImage, 0, 0, $backgroundColor);
    
    // Copy and merge the graphs into the combined image
    $offsetX = 0;
    
    foreach ($graphs as $graph) {
        $graphWidth = imagesx($graph);
        $graphHeight = imagesy($graph);
        
        imagecopy($combinedImage, $graph, $offsetX, 0, 0, 0, $graphWidth, $graphHeight);
        
        $offsetX += $graphWidth;
    }
    
    // Save the combined image to a file
    imagepng($combinedImage, $outputPath);
    
    // Free up memory by destroying the image resources
    foreach ($graphs as $graph) {
        imagedestroy($graph);
    }
    
    imagedestroy($combinedImage);
}


}



?>