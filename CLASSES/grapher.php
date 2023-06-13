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

}



?>